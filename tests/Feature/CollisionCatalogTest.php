<?php

namespace Tests\Feature;

use App\Models\Claim;
use App\Models\Inspector;
use App\Models\Vehicle;
use Database\Seeders\DemoCollisionCatalogSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CollisionCatalogTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_catalog_seed_is_repeatable_and_links_all_configurations(): void
    {
        $this->seed(DemoCollisionCatalogSeeder::class);
        $this->seed(DemoCollisionCatalogSeeder::class);

        $this->assertDatabaseCount('catalog_vehicle_configurations', 48);
        $this->assertDatabaseCount('catalog_collision_parts', 1536);
        $this->assertDatabaseCount('vehicles', 48);
    }

    public function test_inspector_can_select_only_parts_for_the_claim_vehicle(): void
    {
        $this->withoutVite();
        $this->seed(DemoCollisionCatalogSeeder::class);
        $vehicle = Vehicle::whereNotNull('catalog_configuration_code')->firstOrFail();
        $inspector = Inspector::factory()->create();
        $claim = Claim::factory()->for($inspector)->create(['status' => 'Peritando', 'vehicle_id' => $vehicle->id]);
        $part = DB::table('catalog_collision_parts')->where('vehicle_configuration_code', $vehicle->catalog_configuration_code)->first();
        $foreignPart = DB::table('catalog_collision_parts')->where('vehicle_configuration_code', '!=', $vehicle->catalog_configuration_code)->first();

        $this->actingAs($inspector->user)->get('/claims/'.$claim->id.'/inspection')->assertOk()->assertDontSee($part->name)->assertSee('compatibilidad y equipamiento sin verificar');
        $this->put('/claims/'.$claim->id, ['catalog_parts_present' => 1, 'catalog_part_codes' => [$part->code]])->assertRedirect();
        $this->assertDatabaseHas('claim_catalog_part', ['claim_id' => $claim->id, 'part_code' => $part->code]);
        $this->putJson('/claims/'.$claim->id, ['catalog_parts_present' => 1, 'catalog_part_codes' => [$foreignPart->code]])->assertUnprocessable()->assertJsonValidationErrors('catalog_part_codes');
        $this->assertDatabaseHas('claim_catalog_part', ['claim_id' => $claim->id, 'part_code' => $part->code]);
    }
    public function test_catalog_search_requires_three_characters_and_only_returns_the_claim_vehicle_parts(): void
    {
        $this->seed(DemoCollisionCatalogSeeder::class);
        $vehicle = Vehicle::whereNotNull('catalog_configuration_code')->firstOrFail();
        $inspector = Inspector::factory()->create();
        $claim = Claim::factory()->for($inspector)->create(['status' => 'Peritando', 'vehicle_id' => $vehicle->id]);
        $other = Claim::factory()->create(['status' => 'Peritando', 'vehicle_id' => $vehicle->id]);
        $url = '/claims/'.$claim->id.'/parts/catalog-search';
        $this->getJson($url.'?q=par')->assertUnauthorized();
        $this->actingAs($inspector->user)->getJson($url.'?q=pa')->assertUnprocessable();
        $this->getJson('/claims/'.$other->id.'/parts/catalog-search?q=par')->assertNotFound();

        $response = $this->getJson($url.'?q=par')->assertOk();
        $codes = collect($response->json('results'))->pluck('code');
        $this->assertNotEmpty($codes);
        $this->assertSame($codes->count(), DB::table('catalog_collision_parts')->where('vehicle_configuration_code', $vehicle->catalog_configuration_code)->whereIn('code', $codes)->count());
        $this->getJson($url.'?q=zzzzzz')->assertOk()->assertJsonCount(0, 'results');
    }

    public function test_catalog_parts_save_independently_and_reject_invalid_or_inaccessible_claims(): void
    {
        $this->seed(DemoCollisionCatalogSeeder::class);
        $vehicle = Vehicle::whereNotNull('catalog_configuration_code')->firstOrFail();
        $inspector = Inspector::factory()->create();
        $claim = Claim::factory()->for($inspector)->create(['status' => 'Peritando', 'vehicle_id' => $vehicle->id]);
        $foreign = Claim::factory()->create(['status' => 'Peritando', 'vehicle_id' => $vehicle->id]);
        $part = DB::table('catalog_collision_parts')->where('vehicle_configuration_code', $vehicle->catalog_configuration_code)->first();
        $otherPart = DB::table('catalog_collision_parts')->where('vehicle_configuration_code', '!=', $vehicle->catalog_configuration_code)->first();
        $url = '/claims/'.$claim->id.'/parts/catalog';

        $this->putJson($url, ['catalog_part_codes' => [$part->code]])->assertUnauthorized();
        $this->actingAs($inspector->user)->putJson('/claims/'.$foreign->id.'/parts/catalog', ['catalog_part_codes' => [$part->code]])->assertNotFound();
        $this->putJson($url, ['catalog_part_codes' => [$part->code]])->assertOk();
        $this->assertDatabaseHas('claim_catalog_part', ['claim_id' => $claim->id, 'part_code' => $part->code]);
        $this->assertSame('Peritando', $claim->fresh()->status);
        $this->putJson($url, ['catalog_part_codes' => [$otherPart->code]])->assertUnprocessable()->assertJsonValidationErrors('catalog_part_codes');
        $this->assertDatabaseHas('claim_catalog_part', ['claim_id' => $claim->id, 'part_code' => $part->code]);
        $this->putJson($url, ['catalog_part_codes' => []])->assertOk();
        $this->assertDatabaseMissing('claim_catalog_part', ['claim_id' => $claim->id, 'part_code' => $part->code]);
    }

}
