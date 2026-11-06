<?php

namespace Tests\Feature;

use App\Models\Claim;
use App\Models\Client;
use App\Models\Company;
use Database\Seeders\ClaimSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class InsuranceSeederTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_seeder_creates_200_claims_with_company_prefixes_and_exclusive_clients(): void
    {
        $this->seed(ClaimSeeder::class);

        $this->assertDatabaseCount('claims', 200);
        $this->assertDatabaseCount('clients', 200);
        $this->assertDatabaseCount('companies', 5);
        $this->assertDatabaseCount('inspectors', 10);
        $this->assertDatabaseCount('workshops', 40);
        $this->assertDatabaseCount('producers', 8);
        foreach (Company::withCount('claims')->get() as $company) {
            $this->assertSame(40, $company->claims_count);
        }
        foreach (Claim::all() as $claim) {
            $this->assertMatchesRegularExpression('/^'.preg_quote($claim->company->prefix, '/').'[0-9]{8,16}$/', $claim->claim_number);
            $this->assertSame($claim->company_id, $claim->client->company_id);
            $this->assertSame($claim->id, $claim->client->claim->id);
            $this->assertModelExists($claim->inspector);
        }
        $this->assertSame(200, Claim::distinct()->count('claim_number'));
        $this->assertSame(200, Claim::distinct()->count('client_id'));
    }

    public function test_factory_uses_the_selected_company_prefix(): void
    {
        $company = Company::factory()->create(['name' => 'Mercantil Andina', 'prefix' => 'ML']);

        $claim = Claim::factory()->forCompany($company)->create();

        $this->assertMatchesRegularExpression('/^ML[0-9]{8,16}$/', $claim->claim_number);
        $this->assertSame($company->id, $claim->client->company_id);
        $this->assertNotEmpty($claim->client->phone);
        $this->assertNotEmpty($claim->client->address);
    }

    public function test_client_cannot_belong_to_two_claims(): void
    {
        $claim = Claim::factory()->create();

        $this->expectException(QueryException::class);
        Claim::factory()->for($claim->client)->create();
    }

    public function test_claim_cannot_reference_a_client_from_another_company(): void
    {
        $company = Company::factory()->create();
        $client = Client::factory()->create();

        $this->expectException(QueryException::class);
        Claim::factory()->create(['client_id' => $client->id, 'company_id' => $company->id]);
    }
    public function test_default_seeder_includes_claims_and_links_existing_inspector_users(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $this->assertDatabaseCount('claims', 200);
        $this->assertDatabaseHas('inspectors', ['email' => 'inspectores@example.com']);
        $inspector = \App\Models\Inspector::where('email', 'inspectores@example.com')->firstOrFail();
        $this->assertSame('inspectores@example.com', $inspector->user->email);
    }
    public function test_repeating_demo_seed_does_not_duplicate_records_or_change_assignments(): void
    {
        $this->seed(ClaimSeeder::class);
        $claim = Claim::firstOrFail();
        $producerId = $claim->producer_id;
        $clientId = $claim->client_id;

        $this->seed(ClaimSeeder::class);

        $this->assertDatabaseCount('workshops', 40);
        $this->assertDatabaseCount('producers', 8);
        $this->assertDatabaseCount('inspectors', 10);
        $this->assertDatabaseCount('clients', 200);
        $this->assertDatabaseCount('claims', 200);
        $this->assertSame($producerId, $claim->fresh()->producer_id);
        $this->assertSame($clientId, $claim->fresh()->client_id);
        foreach (\App\Models\Producer::withCount('claims')->get() as $producer) {
            $this->assertSame(25, $producer->claims_count);
        }
    }

    public function test_demo_seed_links_existing_claims_without_creating_another_200_clients(): void
    {
        $company = Company::factory()->create(['name' => 'Mercantil Andina', 'prefix' => 'ML']);
        $inspector = \App\Models\Inspector::factory()->create();
        $claims = Claim::factory()->count(200)->forCompany($company)->for($inspector)->create(['producer_id' => null]);

        $this->seed(ClaimSeeder::class);

        $this->assertDatabaseCount('claims', 200);
        $this->assertDatabaseCount('clients', 200);
        $this->assertDatabaseCount('inspectors', 10);
        $this->assertDatabaseCount('workshops', 40);
        $this->assertDatabaseCount('producers', 8);
        $this->assertSame(0, Claim::whereNull('producer_id')->count());
        $this->assertSame($claims->first()->client_id, $claims->first()->fresh()->client_id);
        $this->assertSame($claims->first()->id, $claims->first()->fresh()->producer->claims()->first()->id);
    }
}
