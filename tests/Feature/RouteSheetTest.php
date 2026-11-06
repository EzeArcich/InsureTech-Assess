<?php

namespace Tests\Feature;

use App\Models\Claim;
use App\Models\Inspector;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RouteSheetTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_route_planner_has_only_coordinated_and_inspecting_claims_and_special_coordinated_view(): void
    {
        $this->withoutVite();
        $planner = User::factory()->create();
        $planner->assignRole(Role::findOrCreate('Hojas de ruta', 'web'));
        $active = Claim::factory()->create(['status' => 'Peritando']);
        $pending = Claim::factory()->create(['status' => 'Pendiente']);
        $coordinated = Claim::factory()->create(['status' => 'Coordinado']);
        $this->actingAs($planner)->get('/claims')->assertOk()->assertSee($active->claim_number)->assertDontSee($pending->claim_number);
        $this->get('/claims?status=Pendiente')->assertForbidden();
        $this->get('/claims/'.$pending->id)->assertNotFound();
        $this->get('/clients/'.$pending->client_id)->assertNotFound();
        $this->get('/claims/coordinated')->assertOk()->assertViewIs('route-sheets.index')->assertSee($coordinated->claim_number);
        $this->get('/claims?status=Coordinado')->assertOk()->assertViewIs('route-sheets.index');
        foreach (['/roles', '/blogs', '/workshops', '/settings/rates', '/inspection-payments', '/claims/'.$active->id.'/edit'] as $url) {
            $this->get($url)->assertForbidden();
        }
        $this->put('/claims/'.$active->id, ['status' => 'IPgestion'])->assertForbidden();
    }

    public function test_map_filters_date_inspector_and_modality_and_does_not_expose_foreign_assignments(): void
    {
        $this->withoutVite();
        $inspector = Inspector::factory()->create();
        $own = Claim::factory()->for($inspector)->create(['status' => 'Peritando', 'inspection_mode' => 'Presencial', 'inspection_date' => '2026-11-09']);
        $own->forceFill(['inspection_latitude' => -34.60, 'inspection_longitude' => -58.38, 'inspection_address' => 'Mitre 123'])->save();
        $remote = Claim::factory()->for($inspector)->create(['status' => 'Peritando', 'inspection_mode' => 'foto', 'inspection_date' => '2026-11-09']);
        $remote->forceFill(['inspection_latitude' => -34.61, 'inspection_longitude' => -58.39])->save();
        $foreign = Claim::factory()->create(['status' => 'Peritando', 'inspection_mode' => 'presencial', 'inspection_date' => '2026-11-09']);
        $foreign->forceFill(['inspection_latitude' => -34.62, 'inspection_longitude' => -58.40])->save();
        $anotherDay = Claim::factory()->for($inspector)->create(['status' => 'Peritando', 'inspection_mode' => 'presencial', 'inspection_date' => '2026-11-10']);
        $this->actingAs($inspector->user)->get('/route-sheets?date=2026-11-09&inspector_id='.$foreign->inspector_id)
            ->assertOk()->assertViewHas('points', fn ($points): bool => $points->pluck('id')->all() === [$own->id])
            ->assertSee($remote->claim_number)->assertDontSee($foreign->claim_number)->assertDontSee($anotherDay->claim_number);
        $this->put('/route-sheets/'.$foreign->id.'/location', ['inspection_address' => 'Otra', 'inspection_latitude' => -34, 'inspection_longitude' => -58])->assertNotFound();
        $this->put('/route-sheets/'.$remote->id.'/location', ['inspection_address' => 'Otra', 'inspection_latitude' => -34, 'inspection_longitude' => -58])->assertSessionHasErrors('inspection_address');
    }

    public function test_planner_assigns_and_reassigns_with_a_valid_day_and_confirmed_map_location(): void
    {
        $planner = User::factory()->create();
        $planner->assignRole(Role::findOrCreate('Hojas de ruta', 'web'));
        $inspector = Inspector::factory()->create();
        $claim = Claim::factory()->create(['status' => 'Coordinado', 'inspection_mode' => 'presencial']);
        $this->actingAs($planner)->put('/route-sheets/'.$claim->id.'/assignment', ['inspector_id' => $inspector->id, 'inspection_date' => '2026-11-09', 'inspection_time' => '10:30'])->assertRedirect();
        $this->assertDatabaseHas('claims', ['id' => $claim->id, 'status' => 'Peritando', 'inspector_id' => $inspector->id, 'inspection_date' => '2026-11-09', 'inspection_time' => '10:30']);
        $this->put('/route-sheets/'.$claim->id.'/location', ['inspection_address' => 'Mitre 123', 'inspection_latitude' => '-34.6037000', 'inspection_longitude' => '-58.3816000'])->assertRedirect();
        $this->assertSame('-34.6037000', $claim->fresh()->inspection_latitude);
        $this->putJson('/route-sheets/'.$claim->id.'/location', ['inspection_address' => 'Mitre 123', 'inspection_latitude' => 100, 'inspection_longitude' => 0])->assertUnprocessable()->assertJsonValidationErrors('inspection_latitude');
        $this->putJson('/route-sheets/'.$claim->id.'/assignment', ['inspector_id' => 99999, 'inspection_date' => 'no-fecha'])->assertUnprocessable()->assertJsonValidationErrors(['inspector_id', 'inspection_date']);
        $this->actingAs($inspector->user)->put('/route-sheets/'.$claim->id.'/assignment', ['inspector_id' => $inspector->id, 'inspection_date' => '2026-11-10'])->assertForbidden();
    }

    public function test_unrelated_roles_cannot_read_map_or_assign_and_guests_must_log_in(): void
    {
        $claim = Claim::factory()->create(['status' => 'Coordinado']);
        $this->get('/route-sheets')->assertRedirect('/login');
        $this->actingAs(User::factory()->create())->get('/route-sheets')->assertForbidden();
        $this->put('/route-sheets/'.$claim->id.'/assignment', [])->assertForbidden();
    }
}
