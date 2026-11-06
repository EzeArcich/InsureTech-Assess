<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\InspectionPayment;
use App\Models\InspectionRate;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SettingsModuleTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_ceo_has_two_separate_settings_modules_and_other_roles_cannot_apply_values(): void
    {
        $this->withoutVite();
        $ceo = User::factory()->create();
        $ceo->assignRole(Role::findOrCreate('CEO', 'web'));
        $this->actingAs($ceo)->get('/settings/rates')->assertOk()->assertSee('Configurar reparación')->assertSee('Configurar inspecciones');
        $this->get('/settings/repair-rates')->assertOk()->assertSee('Chapa / día')->assertDontSee('Pericia por fotos');
        $this->get('/settings/inspection-rates')->assertOk()->assertSee('Pericia por fotos')->assertDontSee('Chapa / día');
        $this->actingAs(User::factory()->create());
        $this->get('/settings/repair-rates')->assertForbidden();
        $this->put('/settings/repair-rates', [])->assertForbidden();
        $this->put('/settings/inspection-rates', [])->assertForbidden();
    }

    public function test_repair_values_apply_to_all_or_only_selected_company(): void
    {
        $ceo = User::factory()->create();
        $ceo->assignRole(Role::findOrCreate('CEO', 'web'));
        $companies = Company::factory()->count(2)->create();
        $rates = ['bodywork_day' => 100, 'bodywork_hour' => 20, 'mechanical_day' => 200, 'mechanical_hour' => 30, 'paint_panel' => 400];
        $this->actingAs($ceo)->put('/settings/repair-rates', ['scope' => 'all', ...$rates])->assertRedirect('/settings/repair-rates');
        foreach ($companies as $company) {
            $this->assertDatabaseHas('company_rates', ['company_id' => $company->id, ...$rates]);
        }
        $this->put('/settings/repair-rates', ['scope' => 'company', 'company_id' => $companies[0]->id, ...$rates, 'paint_panel' => 500])->assertRedirect();
        $this->assertSame('500.00', $companies[0]->fresh()->rates->paint_panel);
        $this->assertSame('400.00', $companies[1]->fresh()->rates->paint_panel);
        $this->putJson('/settings/repair-rates', ['scope' => 'company', ...$rates])->assertUnprocessable()->assertJsonValidationErrors('company_id');
        $this->putJson('/settings/repair-rates', ['scope' => 'invalid', ...$rates])->assertUnprocessable()->assertJsonValidationErrors('scope');
        $this->assertDatabaseCount('company_rates', 2);
    }

    public function test_inspection_values_can_be_applied_globally_and_overridden_without_changing_recorded_fees(): void
    {
        $ceo = User::factory()->create();
        $ceo->assignRole(Role::findOrCreate('CEO', 'web'));
        $payment = InspectionPayment::factory()->create(['amount' => '75.50']);
        $another = Company::factory()->create();
        $rates = ['in_person' => 100, 'photos' => 40, 'video_call' => 60];
        $this->actingAs($ceo)->put('/settings/inspection-rates', ['scope' => 'all', ...$rates])->assertRedirect('/settings/inspection-rates');
        $this->assertDatabaseHas('inspection_rates', ['company_id' => $payment->company_id, ...$rates]);
        $this->assertDatabaseHas('inspection_rates', ['company_id' => $another->id, ...$rates]);
        $this->put('/settings/inspection-rates', ['scope' => 'company', 'company_id' => $another->id, ...$rates, 'in_person' => 200])->assertRedirect();
        $this->assertSame('200.00', $another->fresh()->inspectionRates->in_person);
        $this->assertSame('100.00', InspectionRate::where('company_id', $payment->company_id)->firstOrFail()->in_person);
        $this->assertSame('75.50', $payment->fresh()->amount);
    }
}
