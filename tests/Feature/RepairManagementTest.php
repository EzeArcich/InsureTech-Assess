<?php

namespace Tests\Feature;

use App\Models\Claim;
use App\Models\CompanyRate;
use App\Models\Inspector;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RepairManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_only_ceo_can_read_and_update_company_rates(): void
    {
        $this->withoutVite();
        $rates = CompanyRate::factory()->create();
        $user = User::factory()->create();
        $values = $rates->only(['bodywork_day', 'bodywork_hour', 'mechanical_day', 'mechanical_hour', 'paint_panel']);
        $this->actingAs($user)->get('/settings/rates')->assertForbidden();
        $this->put('/settings/companies/'.$rates->company_id.'/rates', $values)->assertForbidden();
        $user->assignRole(Role::findOrCreate('CEO', 'web'));
        $this->get('/settings/repair-rates')->assertOk()->assertSee($rates->company->name);
        $this->put('/settings/companies/'.$rates->company_id.'/rates', [...$values, 'paint_panel' => '123.45'])->assertRedirect('/settings/rates');
        $this->assertDatabaseHas('company_rates', ['company_id' => $rates->company_id, 'paint_panel' => 123.45]);
        $this->put('/settings/companies/'.$rates->company_id.'/rates', [...$values, 'paint_panel' => '-1'])->assertSessionHasErrors('paint_panel');
    }

    public function test_decimal_calculation_preserves_agreed_rates_and_uses_selected_units(): void
    {
        $inspector = Inspector::factory()->create();
        $claim = Claim::factory()->for($inspector)->create(['status' => 'Peritando']);
        $rates = CompanyRate::factory()->create(['company_id' => $claim->company_id]);
        $this->actingAs($inspector->user)->put('/claims/'.$claim->id, [
            'bodywork_unit' => 'day', 'bodywork_units' => '0.5', 'mechanical_unit' => 'hour', 'mechanical_units' => '1.25', 'paint_panels' => '0.5',
        ])->assertRedirect();
        $this->assertDatabaseHas('claims', ['id' => $claim->id, 'bodywork_total' => 40000, 'mechanical_total' => 15000, 'paint_total' => 25000, 'repair_total' => 80000]);
        $rates->update(['bodywork_day' => 100000, 'bodywork_hour' => 20000]);
        $this->put('/claims/'.$claim->id, ['bodywork_units' => '1'])->assertRedirect();
        $this->assertSame('80000.00', $claim->fresh()->bodywork_total);
        $this->put('/claims/'.$claim->id, ['bodywork_unit' => 'hour', 'bodywork_units' => '0.5'])->assertRedirect();
        $this->assertSame('10000.00', $claim->fresh()->bodywork_total);
        $this->putJson('/claims/'.$claim->id, ['bodywork_units' => '0.001'])->assertUnprocessable()->assertJsonValidationErrors('bodywork_units');
    }

    public function test_rounding_and_unused_work_do_not_freeze_future_tariffs(): void
    {
        $inspector = Inspector::factory()->create();
        $claim = Claim::factory()->for($inspector)->create(['status' => 'Peritando']);
        $rates = CompanyRate::factory()->create(['company_id' => $claim->company_id, 'paint_panel' => '0.01']);
        $this->actingAs($inspector->user)->put('/claims/'.$claim->id, ['paint_panels' => '0.5', 'bodywork_units' => '0'])->assertRedirect();
        $this->assertSame('0.01', $claim->fresh()->paint_total);
        $this->assertNull($claim->fresh()->bodywork_rate);
        $rates->update(['bodywork_hour' => '20000']);
        $this->put('/claims/'.$claim->id, ['bodywork_units' => '1'])->assertRedirect();
        $this->assertSame('20000.00', $claim->fresh()->bodywork_total);
    }

    public function test_staff_can_edit_workshop_details_without_duplicate_self_rejection(): void
    {
        $this->withoutVite();
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('CEO', 'web'));
        $workshop = Workshop::factory()->create();
        $this->actingAs($user)->get('/workshops/'.$workshop->id.'/edit')->assertOk()->assertSee('Horarios');
        $this->put('/workshops/'.$workshop->id, ['name' => $workshop->name, 'address' => $workshop->address, 'is_approved' => false, 'opening_hours' => '9 a 17'])->assertRedirect('/workshops');
        $this->assertDatabaseHas('workshops', ['id' => $workshop->id, 'is_approved' => false, 'opening_hours' => '9 a 17']);
        $this->get('/workshops')->assertOk()->assertSee('No homologado');
    }

    public function test_missing_tariffs_reject_positive_quantities_without_partial_changes(): void
    {
        $inspector = Inspector::factory()->create();
        $claim = Claim::factory()->for($inspector)->create(['status' => 'Peritando']);
        $this->actingAs($inspector->user)->putJson('/claims/'.$claim->id, ['paint_panels' => '0.5', 'status' => 'IPgestion'])
            ->assertUnprocessable()->assertJsonValidationErrors('paint_panels');
        $this->assertSame('Peritando', $claim->fresh()->status);
    }

    public function test_inspector_creates_unapproved_workshop_and_duplicates_are_rejected(): void
    {
        $inspector = Inspector::factory()->create();
        $claim = Claim::factory()->for($inspector)->create(['status' => 'Peritando']);
        $url = '/claims/'.$claim->id.'/workshops';
        $this->actingAs($inspector->user)->postJson($url, ['name' => 'Taller Central', 'address' => 'Mitre 123', 'phone' => '12345', 'opening_hours' => '9 a 18'])
            ->assertCreated()->assertJsonPath('workshop.is_approved', false);
        $this->postJson($url, ['name' => ' TALLER   CENTRAL ', 'address' => 'Otra dirección'])->assertUnprocessable()->assertJsonValidationErrors('name');
        $this->postJson($url, ['name' => 'Otro taller', 'address' => 'mitre 123'])->assertUnprocessable()->assertJsonValidationErrors('address');
        $this->postJson($url, ['name' => 'Nuevo', 'address' => 'San Juan 2', 'is_approved' => true])->assertUnprocessable();
        $this->getJson('/workshops/search?q=Mitre')->assertOk()->assertJsonPath('results.0.name', 'Taller Central');
        $this->getJson('/workshops/search')->assertExactJson(['results' => []]);
        $this->assertDatabaseCount('workshops', 1);
    }

    public function test_parts_and_workshop_assignment_are_saved_and_foreign_claims_are_protected(): void
    {
        $this->withoutVite();
        $inspector = Inspector::factory()->create();
        $claim = Claim::factory()->for($inspector)->create(['status' => 'Peritando']);
        $foreign = Claim::factory()->create(['status' => 'Peritando']);
        $workshop = Workshop::factory()->create();
        $this->actingAs($inspector->user)->get('/claims/'.$claim->id.'/inspection')->assertOk()->assertSee('Repuestos necesarios');
        $this->put('/claims/'.$claim->id, ['workshop_id' => $workshop->id, 'parts_present' => 1, 'part_names' => ['Paragolpes', 'paragolpes', 'Faro']])->assertRedirect();
        $this->assertDatabaseCount('parts', 2);
        $this->assertCount(2, $claim->fresh()->parts);
        $this->assertEquals($workshop->id, $claim->fresh()->workshop_id);
        $this->get('/claims/'.$claim->id.'/parts')->assertOk()->assertSee('Paragolpes')->assertSee($workshop->address);
        $this->get('/claims/'.$foreign->id.'/parts')->assertNotFound();
        $this->get('/claims/'.$foreign->id.'/inspection')->assertNotFound();
        $this->postJson('/claims/'.$foreign->id.'/workshops', ['name' => 'Prohibido', 'address' => 'Otra 12'])->assertNotFound();
        $this->put('/claims/'.$claim->id, ['parts_present' => 1])->assertRedirect();
        $this->assertCount(0, $claim->fresh()->parts);
    }
}
