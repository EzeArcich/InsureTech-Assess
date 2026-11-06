<?php

namespace Tests\Feature;

use App\Models\Claim;
use App\Models\Inspector;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CoordinationLocationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_coordinator_can_search_before_the_claim_is_coordinated_and_save_the_location_with_the_status(): void
    {
        $this->withoutVite();
        $coordinator = User::factory()->create();
        $coordinator->assignRole(Role::findOrCreate('Coordinaciones', 'web'));
        $inspector = Inspector::factory()->create();
        $claim = Claim::factory()->for($inspector)->create(['status' => 'Pendiente', 'inspection_mode' => null]);
        Http::preventStrayRequests();
        Http::fake(['nominatim.openstreetmap.org/search*' => Http::response([
            ['lat' => '-34.6037', 'lon' => '-58.3816', 'display_name' => 'Corrientes 1000, Buenos Aires', 'address' => ['house_number' => '1000']],
        ])]);
        $this->actingAs($coordinator)->get('/claims/'.$claim->id.'/edit')->assertOk()->assertSee('coordination-location-search')->assertSee('Confirmar punto');
        $this->postJson('/claims/'.$claim->id.'/location-search', ['address' => 'Corrientes 1000, Buenos Aires', 'inspection_mode' => 'presencial'])
            ->assertOk()->assertJsonPath('results.0.latitude', -34.6037);
        Http::assertSentCount(1);
        $this->assertNull($claim->fresh()->inspection_latitude);
        $this->put('/claims/'.$claim->id, [
            'status' => 'Coordinado', 'inspection_mode' => 'presencial', 'address' => 'Corrientes 1000', 'city' => 'Buenos Aires',
            'inspection_date' => '2026-11-09', 'inspection_address' => 'Corrientes 1000, Buenos Aires',
            'inspection_latitude' => '-34.6037000', 'inspection_longitude' => '-58.3816000',
        ])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertDatabaseHas('claims', ['id' => $claim->id, 'status' => 'Coordinado', 'inspection_address' => 'Corrientes 1000, Buenos Aires']);
        $this->assertSame('-34.6037000', $claim->fresh()->inspection_latitude);
        $planner = User::factory()->create();
        $planner->assignRole(Role::findOrCreate('Hojas de ruta', 'web'));
        $this->actingAs($planner)->put('/route-sheets/'.$claim->id.'/assignment', ['inspector_id' => $inspector->id, 'inspection_date' => '2026-11-09'])->assertRedirect()->assertSessionHasNoErrors();
        $this->actingAs($inspector->user)->get('/route-sheets?date=2026-11-09')->assertOk()
            ->assertViewHas('points', fn ($points): bool => $points->pluck('id')->all() === [$claim->id]);
    }

    public function test_presential_coordination_requires_coordinates_and_invalid_coordinates_do_not_partially_save(): void
    {
        $coordinator = User::factory()->create();
        $coordinator->assignRole(Role::findOrCreate('Coordinaciones', 'web'));
        $claim = Claim::factory()->create(['status' => 'Pendiente', 'inspection_mode' => 'presencial']);
        $this->actingAs($coordinator)->putJson('/claims/'.$claim->id, ['status' => 'Coordinado', 'address' => 'Nueva 123'])
            ->assertUnprocessable()->assertJsonValidationErrors('inspection_latitude');
        $this->assertSame('Pendiente', $claim->fresh()->status);
        $this->putJson('/claims/'.$claim->id, ['inspection_latitude' => '91', 'inspection_longitude' => '-58', 'inspection_address' => 'Nueva 123'])
            ->assertUnprocessable()->assertJsonValidationErrors('inspection_latitude');
        $this->putJson('/claims/'.$claim->id, ['inspection_latitude' => '-34', 'inspection_address' => 'Nueva 123'])
            ->assertUnprocessable()->assertJsonValidationErrors('inspection_longitude');
        $this->assertNull($claim->fresh()->inspection_latitude);
    }

    public function test_changing_address_invalidates_old_coordinates_and_requires_a_new_point_for_coordinated_claims(): void
    {
        $coordinator = User::factory()->create();
        $coordinator->assignRole(Role::findOrCreate('CEO', 'web'));
        $claim = Claim::factory()->create(['status' => 'Coordinado', 'inspection_mode' => 'presencial']);
        $claim->forceFill(['inspection_address' => 'Original 123', 'inspection_latitude' => -34, 'inspection_longitude' => -58])->save();
        $this->actingAs($coordinator)->putJson('/claims/'.$claim->id, ['city' => 'Otra localidad'])->assertUnprocessable()->assertJsonValidationErrors('inspection_latitude');
        $this->assertSame('-34.0000000', $claim->fresh()->inspection_latitude);
        $this->put('/claims/'.$claim->id, ['city' => 'Otra localidad', 'inspection_address' => 'Nuevo 123, Otra localidad', 'inspection_latitude' => '-35', 'inspection_longitude' => '-59'])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame('-35.0000000', $claim->fresh()->inspection_latitude);
        $this->put('/claims/'.$claim->id, ['status' => 'Pendiente', 'address' => 'Tercer domicilio'])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertNull($claim->fresh()->inspection_latitude);
    }

    public function test_remote_coordination_clears_existing_points_and_does_not_require_a_map(): void
    {
        $coordinator = User::factory()->create();
        $coordinator->assignRole(Role::findOrCreate('Coordinaciones', 'web'));
        $claim = Claim::factory()->create(['status' => 'Pendiente', 'inspection_mode' => 'presencial']);
        $claim->forceFill(['inspection_address' => 'Original 123', 'inspection_latitude' => -34, 'inspection_longitude' => -58])->save();
        $this->actingAs($coordinator)->put('/claims/'.$claim->id, ['status' => 'Coordinado', 'inspection_mode' => 'videollamada'])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame('Coordinado', $claim->fresh()->status);
        $this->assertNull($claim->fresh()->inspection_latitude);
        $this->assertNull($claim->fresh()->inspection_longitude);
    }

    public function test_inspectors_and_unprivileged_users_cannot_use_coordination_search_or_overwrite_locations_in_claim_updates(): void
    {
        Http::preventStrayRequests();
        Http::fake(['nominatim.openstreetmap.org/search*' => Http::response([])]);
        $inspector = Inspector::factory()->create();
        $claim = Claim::factory()->for($inspector)->create(['status' => 'Peritando', 'inspection_mode' => 'presencial']);
        $data = ['address' => 'Corrientes 1000', 'inspection_mode' => 'presencial'];
        $this->actingAs($inspector->user)->postJson('/claims/'.$claim->id.'/location-search', $data)->assertForbidden();
        $this->putJson('/claims/'.$claim->id, ['inspection_latitude' => '-34', 'inspection_longitude' => '-58', 'inspection_address' => 'Nueva 123'])->assertUnprocessable();
        $this->actingAs(User::factory()->create())->postJson('/claims/'.$claim->id.'/location-search', $data)->assertForbidden();
        Http::assertNothingSent();
    }
}
