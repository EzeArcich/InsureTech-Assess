<?php

namespace Tests\Feature;

use App\Models\Claim;
use App\Models\Client;
use App\Models\Inspector;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CoordinatorAccessTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_roles_are_only_visible_and_accessible_to_ceo_even_for_legacy_admin_email(): void
    {
        $this->withoutVite();
        $user = User::factory()->create(['email' => 'admin@gmail.com']);
        $this->actingAs($user)->get('/home')->assertOk()->assertDontSee('href="'.route('roles.index').'"', false);
        $this->get('/roles')->assertForbidden();
        $this->post('/roles', ['name' => 'Nuevo'])->assertForbidden();
        $this->put('/roles/1', ['name' => 'Nuevo'])->assertForbidden();
        $this->delete('/roles/1')->assertForbidden();
        $user->assignRole(Role::findOrCreate('CEO', 'web'));
        $this->get('/roles')->assertOk();
        $this->get('/home')->assertSee('href="'.route('roles.index').'"', false);
    }

    public function test_coordinator_sees_pending_and_absent_only_across_lists_dashboard_profiles_and_direct_urls(): void
    {
        $this->withoutVite();
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('Coordinaciones', 'web'));
        $pending = Claim::factory()->create(['status' => 'Pendiente']);
        $absent = Claim::factory()->create(['status' => 'Ausente']);
        $assigned = Claim::factory()->create(['status' => 'Peritando']);
        $this->actingAs($user)->get('/claims')->assertOk()->assertSee($pending->claim_number)->assertSee($absent->claim_number)->assertDontSee($assigned->claim_number);
        $this->get('/claims/pending')->assertOk()->assertSee($pending->claim_number);
        $this->get('/claims/absent')->assertOk()->assertSee($absent->claim_number);
        $this->get('/home')->assertOk()->assertViewHas('totalClaims', 2)
            ->assertViewHas('statusCounts', fn ($counts): bool => $counts->keys()->all() === ['Pendiente', 'Ausente']);
        $this->get('/claims?status=Peritando')->assertForbidden();
        $this->get('/claims/'.$assigned->id)->assertNotFound();
        $this->get('/claims/'.$assigned->id.'/edit')->assertNotFound();
        $this->get('/clients/'.$assigned->client_id)->assertNotFound();
        $this->putJson('/claims/'.$assigned->id, ['notes' => 'Cambio'])->assertNotFound();
        $this->putJson('/claims/'.$pending->id, ['status' => 'Peritando'])->assertUnprocessable()->assertJsonValidationErrors('status');
        foreach (['/roles', '/claims/coordinated', '/claims/all', '/claims/assigned', '/blogs', '/settings/rates', '/route-sheets'] as $url) {
            $this->get($url)->assertForbidden();
        }
    }

    public function test_locality_suggestions_show_upcoming_presential_dates_and_inspectors_without_opening_other_claims(): void
    {
        $this->travelTo(now()->setDate(2026, 11, 6)->startOfDay());
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('Coordinaciones', 'web'));
        $claim = Claim::factory()->create(['status' => 'Pendiente']);
        $near = Claim::factory()->for(Client::factory()->state(['city' => 'Belén de Escobar']))->create([
            'status' => 'Peritando', 'inspection_date' => '2026-11-10', 'inspection_mode' => 'presencial',
        ]);
        $remote = Claim::factory()->for(Client::factory()->state(['city' => 'Escobar']))->create([
            'status' => 'Coordinado', 'inspection_date' => '2026-11-10', 'inspection_mode' => 'foto',
        ]);
        $past = Claim::factory()->for(Client::factory()->state(['city' => 'Escobar']))->create([
            'status' => 'Peritando', 'inspection_date' => '2026-11-01', 'inspection_mode' => 'presencial',
        ]);
        $this->actingAs($user)->getJson('/claims/'.$claim->id.'/nearby?city=Escobar')->assertOk()
            ->assertJsonPath('total', 1)->assertJsonPath('results.0.number', $near->claim_number)
            ->assertJsonPath('results.0.inspector', $near->inspector->name)->assertJsonPath('results.0.date', '2026-11-10');
        $this->get('/claims/'.$near->id)->assertNotFound();
    }

    public function test_coordinate_suggestions_include_nearby_other_locality_and_exclude_far_away_same_city(): void
    {
        $this->travelTo(now()->setDate(2026, 11, 6)->startOfDay());
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('Coordinaciones', 'web'));
        $claim = Claim::factory()->create(['status' => 'Pendiente']);
        $near = Claim::factory()->for(Client::factory()->state(['city' => 'Otra localidad']))->create([
            'status' => 'Coordinado', 'inspection_mode' => 'presencial', 'inspection_date' => '2026-11-10',
        ]);
        $near->forceFill(['inspection_latitude' => -34.26, 'inspection_longitude' => -58.80])->save();
        $far = Claim::factory()->for(Client::factory()->state(['city' => 'Escobar']))->create([
            'status' => 'Peritando', 'inspection_mode' => 'presencial', 'inspection_date' => '2026-11-10',
        ]);
        $far->forceFill(['inspection_latitude' => -35, 'inspection_longitude' => -59])->save();
        $this->actingAs($user)->getJson('/claims/'.$claim->id.'/nearby?city=Escobar&latitude=-34.25&longitude=-58.8')
            ->assertOk()->assertJsonPath('total', 1)->assertJsonPath('results.0.number', $near->claim_number)
            ->assertJsonPath('results.0.distance_km', 1.1);
        $this->actingAs($near->inspector->user)->getJson('/claims/'.$near->id.'/nearby?city=Escobar')->assertForbidden();
    }
}
