<?php

namespace Tests\Feature;

use App\Models\Claim;
use App\Models\Inspector;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class InspectorAccessTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_inspector_sees_only_own_active_assignments_and_can_filter_by_date(): void
    {
        $this->withoutVite();
        $inspector = Inspector::factory()->create();
        $today = Claim::factory()->for($inspector)->create(['status' => 'Peritando', 'inspection_date' => '2026-11-06']);
        $later = Claim::factory()->for($inspector)->create(['status' => 'Peritando', 'inspection_date' => '2026-11-07']);
        $foreign = Claim::factory()->create(['status' => 'Peritando']);
        $pending = Claim::factory()->for($inspector)->create(['status' => 'Pendiente']);

        $response = $this->actingAs($inspector->user)->get('/claims?status=Peritando&date=2026-11-06');

        $response->assertOk()->assertSee($today->claim_number)->assertDontSee($later->claim_number)
            ->assertDontSee($foreign->claim_number)->assertDontSee($pending->claim_number);
        $this->get('/claims?status=Pendiente')->assertForbidden();
    }

    public function test_inspector_dashboard_counts_only_own_allowed_states(): void
    {
        $this->withoutVite();
        $inspector = Inspector::factory()->create();
        Claim::factory()->for($inspector)->create(['status' => 'Peritando']);
        Claim::factory()->for($inspector)->create(['status' => 'IPgestion']);
        Claim::factory()->for($inspector)->create(['status' => 'Coordinado']);
        Claim::factory()->create(['status' => 'Peritando']);

        $this->actingAs($inspector->user)->get('/home')->assertOk()->assertViewHas('totalClaims', 2)
            ->assertViewHas('statusCounts', fn ($counts): bool => $counts->keys()->all() === ['Peritando', 'IPgestion']);
    }

    public function test_foreign_claims_and_profiles_return_404_including_json_and_updates(): void
    {
        $this->withoutVite();
        $inspector = Inspector::factory()->create();
        $foreign = Claim::factory()->create(['status' => 'Peritando']);

        $this->actingAs($inspector->user);
        foreach (['/claims/'.$foreign->id, '/claims/'.$foreign->id.'/edit', '/claims/'.$foreign->id.'/data',
            '/clients/'.$foreign->client_id, '/inspectors/'.$foreign->inspector_id] as $url) {
            $this->get($url)->assertNotFound();
        }
        $this->put('/claims/'.$foreign->id, ['status' => 'IPgestion'])->assertNotFound();
        $this->put('/claims/'.$foreign->id.'/data', ['status' => 'IPgestion'])->assertNotFound();
        $this->assertDatabaseHas('claims', ['id' => $foreign->id, 'status' => 'Peritando']);
    }

    public function test_inspector_can_manage_own_claim_but_cannot_reassign_it_or_choose_other_states(): void
    {
        $this->withoutVite();
        $inspector = Inspector::factory()->create();
        $claim = Claim::factory()->for($inspector)->create(['status' => 'Peritando']);
        $other = Inspector::factory()->create();

        $this->actingAs($inspector->user)->get('/claims/'.$claim->id.'/edit')->assertOk()->assertSee('Guardar gestión');
        $this->put('/claims/'.$claim->id, ['status' => 'IPgestion', 'inspector_notes' => 'Inspección realizada'])
            ->assertRedirect();
        $this->assertDatabaseHas('claims', ['id' => $claim->id, 'status' => 'IPgestion', 'inspector_notes' => 'Inspección realizada']);
        $this->put('/claims/'.$claim->id, ['inspector_id' => $other->id])->assertSessionHasErrors('inspector_id');
        $this->put('/claims/'.$claim->id, ['status' => 'Pendiente'])->assertSessionHasErrors('status');
        $this->assertDatabaseHas('claims', ['id' => $claim->id, 'inspector_id' => $inspector->id, 'status' => 'IPgestion']);
    }

    public function test_inspector_cannot_bypass_restrictions_through_other_lists_or_administration(): void
    {
        $inspector = Inspector::factory()->create();

        $this->actingAs($inspector->user);
        foreach (['/claims/all', '/claims/all/data', '/claims/datatable', '/claims/assignments/data',
            '/claims/pending', '/claims/assignments', '/users', '/roles', '/blogs'] as $url) {
            $this->get($url)->assertForbidden();
        }
    }
}
