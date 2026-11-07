<?php

namespace Tests\Feature;

use App\Models\ClaimSimulationRun;
use App\Models\Company;
use App\Models\CompanyIntegrationCredential;
use App\Models\User;
use App\Services\GenerateSimulatedClaims;
use App\Services\IssueCompanyIntegrationCredential;
use App\Services\SendSimulatedClaims;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ClaimSimulationTest extends TestCase
{
    use LazilyRefreshDatabase;

    /** @return array{user:User,credential:CompanyIntegrationCredential,token:string} */
    private function laboratory(): array
    {
        config(['claim_integrations.simulator_enabled' => true, 'claim_integrations.simulator_origin' => 'http://127.0.0.1:8001']);
        $this->withoutVite();
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('CEO', 'web'));
        $issued = app(IssueCompanyIntegrationCredential::class)->handle(Company::factory()->create(), 'simulator', true);

        return ['user' => $user, ...$issued];
    }

    public function test_simulator_requires_administrator_and_is_disabled_by_default(): void
    {
        $lab = $this->laboratory();
        $this->get('/claim-simulations')->assertRedirect('/login');
        $this->actingAs(User::factory()->create())->get('/claim-simulations')->assertForbidden();
        config(['claim_integrations.simulator_enabled' => false]);
        $this->actingAs($lab['user'])->get('/claim-simulations')->assertNotFound();
        config(['claim_integrations.simulator_enabled' => true]);
        $this->app->detectEnvironment(fn (): string => 'production');
        $this->get('/claim-simulations')->assertNotFound();
    }

    public function test_preview_creates_no_claims_and_never_exposes_plaintext_credential(): void
    {
        $lab = $this->laboratory();
        $this->actingAs($lab['user'])->get('/claim-simulations')->assertOk()->assertSee('Laboratorio de aseguradoras')->assertDontSee($lab['token']);
        $this->post('/claim-simulations', ['credential_id' => $lab['credential']->id, 'reason' => 'Todo riesgo', 'quantity' => 2, 'seed' => 42])->assertRedirect();
        $run = ClaimSimulationRun::with('items')->firstOrFail();
        $this->get('/claim-simulations/'.$run->id)->assertOk()->assertSee('Payload de entrada')->assertDontSee($lab['token']);
        $this->assertCount(2, $run->items);
        $this->assertDatabaseCount('claims', 0);
        $stored = DB::table('company_integration_credentials')->where('id', $lab['credential']->id)->value('simulator_token');
        $this->assertNotSame($stored, $lab['token']);
        $this->assertArrayNotHasKey('simulator_token', $lab['credential']->toArray());
    }

    public function test_manual_batch_sends_real_http_contract_and_keeps_partial_results(): void
    {
        $lab = $this->laboratory();
        $run = app(GenerateSimulatedClaims::class)->handle($lab['user'], ['credential_id' => $lab['credential']->id, 'reason' => 'Todo riesgo', 'quantity' => 3, 'seed' => 1]);
        $items = $run->items;
        Http::preventStrayRequests();
        Http::fake(['http://127.0.0.1:8001/api/v1/integrations/claims' => Http::sequence()
            ->push(['data' => ['id' => 11, 'company_id' => $lab['credential']->company_id, 'external_id' => $items[0]->payload['external_id']], 'meta' => ['replayed' => false]], 201)
            ->push(['message' => 'Rechazado', 'errors' => ['client_name' => ['Inválido']]], 422)
            ->push(['data' => ['id' => 12, 'company_id' => $lab['credential']->company_id, 'external_id' => $items[2]->payload['external_id']], 'meta' => ['replayed' => true]], 200)]);
        $this->actingAs($lab['user'])->post('/claim-simulations/'.$run->id.'/send', ['confirmation' => '1'])->assertRedirect();
        $this->assertSame(['created', 'rejected', 'duplicate'], $run->fresh()->items->pluck('status')->all());
        $this->assertSame('finished', $run->fresh()->status);
        Http::assertSentCount(3);
        Http::assertSent(fn ($request): bool => $request->hasHeader('Authorization', 'Bearer '.$lab['token']) && $request['external_id'] === $items[0]->payload['external_id']);
        $this->assertDatabaseCount('claims', 0); // Only the remote API may create claims; this test fakes that boundary.
        $this->get('/claim-simulations/'.$run->id)->assertOk()->assertSee('Abrir expediente')->assertDontSee($lab['token']);
        $this->post('/claim-simulations/'.$run->id.'/send', ['confirmation' => '1'])->assertConflict();
    }

    public function test_transport_failure_is_visible_and_does_not_abort_other_items(): void
    {
        $lab = $this->laboratory();
        $run = app(GenerateSimulatedClaims::class)->handle($lab['user'], ['credential_id' => $lab['credential']->id, 'reason' => 'robo', 'quantity' => 1]);
        Http::preventStrayRequests();
        Http::fake(['http://127.0.0.1:8001/api/v1/integrations/claims' => Http::failedConnection()]);
        app(SendSimulatedClaims::class)->handle($run);
        $this->assertSame('transport_error', $run->fresh()->items->first()->status);
        Http::assertSentCount(1);
    }

    public function test_production_destination_and_real_credentials_cannot_be_used(): void
    {
        $lab = $this->laboratory();
        $real = app(IssueCompanyIntegrationCredential::class)->handle(Company::factory()->create(), 'real');
        $this->actingAs($lab['user'])->post('/claim-simulations', ['credential_id' => $real['credential']->id, 'reason' => 'Todo riesgo', 'quantity' => 1])->assertSessionHasErrors('credential_id');
        $run = app(GenerateSimulatedClaims::class)->handle($lab['user'], ['credential_id' => $lab['credential']->id, 'reason' => 'robo', 'quantity' => 1]);
        config(['claim_integrations.simulator_origin' => 'https://production.example.com']);
        Http::preventStrayRequests();
        $this->post('/claim-simulations/'.$run->id.'/send', ['confirmation' => '1'])->assertUnprocessable();
        $this->assertSame('preview', $run->fresh()->status);
        Http::assertNothingSent();
    }

    public function test_generator_is_seeded_and_payloads_pass_api_validation(): void
    {
        $lab = $this->laboratory();
        $generator = app(GenerateSimulatedClaims::class);
        $payloads = $generator->payloads('Todo riesgo', 2, 42, 'test');
        $this->assertSame($payloads, $generator->payloads('Todo riesgo', 2, 42, 'test'));
        foreach ($payloads as $payload) {
            $this->withToken($lab['token'])->postJson('/api/v1/integrations/claims', $payload)->assertCreated();
        }
        $this->assertDatabaseCount('claims', 2);
    }

    public function test_demo_seeder_is_local_only_additive_and_repeat_safe(): void
    {
        $lab = $this->laboratory();
        $this->artisan('integrations:seed-demo')->assertSuccessful();
        $this->assertDatabaseHas('companies', ['prefix' => 'SIM-NORTE', 'name' => 'DEMO Seguro Norte']);
        $this->assertDatabaseCount('claims', 0);
        $this->artisan('integrations:seed-demo')->assertFailed();
        $this->assertDatabaseCount('companies', 2);
        config(['claim_integrations.simulator_enabled' => false]);
        $this->artisan('integrations:seed-demo')->assertFailed();
    }
}
