<?php

namespace Tests\Feature;

use App\Models\Claim;
use App\Models\Company;
use App\Models\IntegratedClaim;
use App\Models\User;
use App\Services\IssueCompanyIntegrationCredential;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ClaimIntegrationTest extends TestCase
{
    /** @return array<string,mixed> */
    private function payload(): array
    {
        return ['external_id' => 'sn-001', 'claim_number' => 'SN-001', 'client_name' => 'SIMULADO Ana', 'client_email' => 'ana@example.test', 'license_plate' => 'AA123AA', 'reason' => 'Todo riesgo', 'phone' => '0000000000', 'address' => 'Calle Ficticia 123', 'city' => 'Ciudad Simulada'];
    }

    use LazilyRefreshDatabase;

    public function test_valid_claim_uses_shared_domain_flow_and_credential_company(): void
    {
        $company = Company::factory()->create();
        $issued = app(IssueCompanyIntegrationCredential::class)->handle($company, 'Aseguradora');
        $response = $this->withToken($issued['token'])->postJson('/api/v1/integrations/claims', $this->payload())
            ->assertCreated()->assertJsonPath('data.company_id', $company->id)->assertJsonPath('data.status', 'Pendiente')->assertJsonPath('meta.replayed', false)->assertHeader('X-Request-ID');
        $claim = Claim::findOrFail($response->json('data.id'));
        $this->assertSame('SIMULADO Ana', $claim->client->name);
        $this->assertSame($company->id, $claim->client->company_id);
        $this->assertSame('AA123AA', $claim->license_plate);
        $this->assertSame('0.00', $claim->repair_total);
        $this->assertDatabaseHas('integration_requests', ['id' => $response->headers->get('X-Request-ID'), 'http_status' => 201, 'outcome' => 'created']);
        $this->assertDatabaseCount('integrated_claims', 1);
        $this->assertDatabaseCount('clients', 1);
        $this->assertNull($issued['credential']->fresh()->simulator_token);
        $this->assertNotSame($issued['token'], $issued['credential']->token_hash);
    }

    public function test_absent_invalid_revoked_expired_and_unauthorized_credentials(): void
    {
        $this->postJson('/api/v1/integrations/claims', $this->payload())->assertUnauthorized();
        $this->withToken('invalid')->postJson('/api/v1/integrations/claims', $this->payload())->assertUnauthorized();
        $issued = app(IssueCompanyIntegrationCredential::class)->handle(Company::factory()->create(), 'test');
        $this->withToken($issued['token']);
        $issued['credential']->update(['revoked_at' => now()]);
        $this->postJson('/api/v1/integrations/claims', $this->payload())->assertUnauthorized();
        $issued['credential']->update(['revoked_at' => null, 'expires_at' => now()->subMinute()]);
        $this->postJson('/api/v1/integrations/claims', $this->payload())->assertUnauthorized();
        $issued['credential']->update(['expires_at' => null, 'abilities' => []]);
        $this->postJson('/api/v1/integrations/claims', $this->payload())->assertForbidden();
        $this->assertDatabaseCount('claims', 0);
        $this->assertDatabaseCount('clients', 0);
    }

    public function test_payload_validation_and_protected_fields_are_strict(): void
    {
        $issued = app(IssueCompanyIntegrationCredential::class)->handle(Company::factory()->create(), 'test');
        $this->withToken($issued['token'])->postJson('/api/v1/integrations/claims', [])->assertUnprocessable()->assertJsonValidationErrors(['external_id', 'claim_number', 'client_name']);
        foreach (['company_id' => 999, 'client_id' => 1, 'status' => 'Resuelto', 'severity' => 'medium', 'inspection_mode' => 'unknown', 'client_email' => 'invalid', 'external_id' => 'bad/id'] as $field => $value) {
            $this->postJson('/api/v1/integrations/claims', [...$this->payload(), $field => $value])->assertUnprocessable()->assertJsonValidationErrors($field);
        }
        $this->assertDatabaseCount('claims', 0);
        $this->assertDatabaseCount('clients', 0);
    }

    public function test_equivalent_retry_is_idempotent_and_changed_payload_conflicts(): void
    {
        $company = Company::factory()->create();
        $first = app(IssueCompanyIntegrationCredential::class)->handle($company, 'first');
        $second = app(IssueCompanyIntegrationCredential::class)->handle($company, 'second');
        $response = $this->withToken($first['token'])->postJson('/api/v1/integrations/claims', $this->payload())->assertCreated();
        $id = $response->json('data.id');
        Claim::findOrFail($id)->update(['status' => 'Ausente']);
        $reordered = array_reverse([...$this->payload(), 'notes' => null], true);
        $reordered['external_id'] = 'SN-001';
        $this->withToken($second['token'])->postJson('/api/v1/integrations/claims', $reordered)->assertOk()->assertJsonPath('data.id', $id)->assertJsonPath('meta.replayed', true)->assertJsonPath('data.status', 'Pendiente');
        $this->postJson('/api/v1/integrations/claims', [...$this->payload(), 'client_name' => 'Different'])->assertConflict();
        $this->assertDatabaseCount('claims', 1);
        $this->assertDatabaseCount('clients', 1);
        $this->assertDatabaseCount('integrated_claims', 1);
    }

    public function test_other_company_may_use_same_external_id_and_cannot_select_emitter(): void
    {
        $first = app(IssueCompanyIntegrationCredential::class)->handle(Company::factory()->create(), 'first');
        $second = app(IssueCompanyIntegrationCredential::class)->handle(Company::factory()->create(), 'second');
        $this->withToken($first['token'])->postJson('/api/v1/integrations/claims', $this->payload())->assertCreated();
        $this->withToken($second['token'])->postJson('/api/v1/integrations/claims', $this->payload())->assertCreated()->assertJsonPath('data.company_id', $second['credential']->company_id);
        $this->postJson('/api/v1/integrations/claims', [...$this->payload(), 'company_id' => $first['credential']->company_id])->assertUnprocessable();
        $this->assertDatabaseCount('claims', 2);
    }

    public function test_failed_receipt_persistence_rolls_back_claim_and_client(): void
    {
        $issued = app(IssueCompanyIntegrationCredential::class)->handle(Company::factory()->create(), 'test');
        IntegratedClaim::creating(fn () => throw new \RuntimeException('Persistence failure'));
        $this->withToken($issued['token'])->postJson('/api/v1/integrations/claims', $this->payload())->assertInternalServerError()->assertJsonPath('message', 'No se pudo registrar el siniestro.');
        $this->assertDatabaseCount('claims', 0);
        $this->assertDatabaseCount('clients', 0);
        $this->assertDatabaseCount('integrated_claims', 0);
        $this->assertDatabaseHas('integration_requests', ['http_status' => 500]);
    }

    public function test_integrated_claim_cannot_be_deleted_or_transferred_by_internal_forms(): void
    {
        $issued = app(IssueCompanyIntegrationCredential::class)->handle(Company::factory()->create(), 'test');
        $id = $this->withToken($issued['token'])->postJson('/api/v1/integrations/claims', $this->payload())->assertCreated()->json('data.id');
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('CEO', 'web'));
        $other = Company::factory()->create();
        $this->withoutHeader('Authorization')->actingAs($user)->put('/claims/'.$id, ['company_id' => $other->id])->assertSessionHasErrors('company_id');
        $this->delete('/claims/'.$id)->assertSessionHasErrors('claim');
        $this->assertDatabaseHas('claims', ['id' => $id, 'company_id' => $issued['credential']->company_id]);
        $this->assertDatabaseCount('clients', 1);
        $this->assertDatabaseCount('integrated_claims', 1);
    }
}
