<?php

namespace Tests\Feature;

use App\Models\Claim;
use App\Models\Company;
use App\Models\InspectionPayment;
use App\Models\InspectionRate;
use App\Models\Inspector;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InspectionPaymentTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_ceo_sets_per_company_inspection_rates_and_administrative_cannot_change_them(): void
    {
        $this->withoutVite();
        $company = Company::factory()->create();
        $ceo = User::factory()->create();
        $ceo->assignRole(Role::findOrCreate('CEO', 'web'));
        $values = ['in_person' => '15000.50', 'photos' => '7000', 'video_call' => '9000'];
        $url = '/settings/companies/'.$company->id.'/inspection-rates';
        $this->actingAs($ceo)->put($url, $values)->assertRedirect('/settings/rates');
        $this->assertDatabaseHas('inspection_rates', ['company_id' => $company->id, 'in_person' => 15000.50]);
        $this->get('/settings/inspection-rates')->assertOk()->assertSee('Pericia por videollamada');
        $this->putJson($url, [...$values, 'photos' => '-1'])->assertUnprocessable()->assertJsonValidationErrors('photos');
        $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('Administrativo', 'web'));
        $this->actingAs($admin)->put($url, $values)->assertForbidden();
        $this->get('/inspection-payments')->assertOk();
    }

    public function test_resolution_snapshots_fee_and_payment_is_idempotent_and_inspector_can_only_see_own_earnings(): void
    {
        $this->withoutVite();
        $this->freezeTime();
        $ceo = User::factory()->create();
        $ceo->assignRole(Role::findOrCreate('CEO', 'web'));
        $inspector = Inspector::factory()->create();
        $claim = Claim::factory()->for($inspector)->create(['status' => 'IPgestion', 'inspection_mode' => 'videollamada']);
        $rates = InspectionRate::factory()->create(['company_id' => $claim->company_id, 'video_call' => '9000.25']);
        $resolveUrl = '/inspection-payments/claims/'.$claim->id.'/resolve';
        $this->actingAs($ceo)->post($resolveUrl)->assertRedirect();
        $payment = InspectionPayment::where('claim_id', $claim->id)->firstOrFail();
        $this->assertSame('Resuelto', $claim->fresh()->status);
        $this->assertSame('9000.25', $payment->amount);
        $this->assertNull($payment->paid_at);
        $rates->update(['video_call' => '10000']);
        $this->post($resolveUrl)->assertRedirect();
        $this->assertDatabaseCount('inspection_payments', 1);
        $this->assertSame('9000.25', $payment->fresh()->amount);
        $foreign = InspectionPayment::factory()->create();
        $this->actingAs($inspector->user)->get('/inspection-payments?inspector_id='.$foreign->inspector_id)->assertOk()
            ->assertSee($claim->claim_number)->assertDontSee($foreign->claim->claim_number)->assertViewHas('totals', fn ($totals): bool => (int)$totals->total === 1 && $totals->pending_amount == 9000.25);
        $this->get('/inspection-payments/'.$payment->id)->assertOk();
        $this->get('/inspection-payments/'.$foreign->id)->assertNotFound();
        $this->post('/inspection-payments/'.$payment->id.'/pay')->assertForbidden();
        $this->post($resolveUrl)->assertForbidden();
        $this->actingAs($ceo)->post('/inspection-payments/'.$payment->id.'/pay')->assertRedirect();
        $paidAt = $payment->fresh()->paid_at->toISOString();
        $this->travel(1)->days();
        $this->post('/inspection-payments/'.$payment->id.'/pay')->assertRedirect();
        $this->assertSame($paidAt, $payment->fresh()->paid_at->toISOString());
        $this->assertEquals($ceo->id, $payment->fresh()->paid_by);
        $this->actingAs($inspector->user)->get('/inspection-payments?payment_status=pending')->assertViewHas('totals', fn ($totals): bool => (int)$totals->total === 0);
    }

    public function test_unconfigured_or_unperformed_inspections_cannot_be_resolved_and_generic_updates_cannot_bypass_closure(): void
    {
        $ceo = User::factory()->create();
        $ceo->assignRole(Role::findOrCreate('CEO', 'web'));
        $claim = Claim::factory()->create(['status' => 'IPgestion', 'inspection_mode' => 'presencial']);
        $this->actingAs($ceo)->postJson('/inspection-payments/claims/'.$claim->id.'/resolve')->assertUnprocessable()->assertJsonValidationErrors('claim');
        $this->assertSame('IPgestion', $claim->fresh()->status);
        $this->assertDatabaseCount('inspection_payments', 0);
        InspectionRate::factory()->create(['company_id' => $claim->company_id]);
        $claim->update(['status' => 'Peritando']);
        $this->postJson('/inspection-payments/claims/'.$claim->id.'/resolve')->assertUnprocessable();
        $this->putJson('/claims/'.$claim->id, ['status' => 'Resuelto'])->assertUnprocessable()->assertJsonValidationErrors('status');
        $claim->update(['status' => 'IPgestion']);
        $this->post('/inspection-payments/claims/'.$claim->id.'/resolve')->assertRedirect();
        $this->putJson('/claims/'.$claim->id, ['inspector_id' => Inspector::factory()->create()->id])->assertUnprocessable()->assertJsonValidationErrors('inspector_id');
        $this->deleteJson('/claims/'.$claim->id)->assertUnprocessable()->assertJsonValidationErrors('claim');
        $this->assertDatabaseHas('claims', ['id' => $claim->id, 'status' => 'Resuelto']);
    }

    public function test_administrative_summary_totals_and_company_filter_match_resolved_records(): void
    {
        $this->withoutVite();
        $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('Administrativo', 'web'));
        $one = InspectionPayment::factory()->create(['amount' => '100']);
        InspectionPayment::factory()->create(['amount' => '200', 'paid_at' => now()]);
        $this->actingAs($admin)->get('/inspection-payments')->assertOk()
            ->assertViewHas('totals', fn ($totals): bool => (int)$totals->total === 2 && $totals->amount == 300 && $totals->pending_amount == 100)
            ->assertViewHas('byCompany', fn ($rows): bool => $rows->count() === 2)
            ->assertViewHas('byInspector', fn ($rows): bool => $rows->count() === 2);
        $this->get('/inspection-payments?company_id='.$one->company_id)->assertOk()
            ->assertViewHas('totals', fn ($totals): bool => (int)$totals->total === 1 && $totals->amount == 100);
        $this->actingAs(User::factory()->create())->get('/inspection-payments')->assertForbidden();
    }
}
