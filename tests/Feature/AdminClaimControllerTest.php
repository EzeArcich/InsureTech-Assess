<?php

namespace Tests\Feature;

use App\Models\Claim;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminClaimControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guest_is_redirected_to_login_for_page_and_data(): void
    {
        $this->get('/claims/all')->assertRedirect('/login');
        $this->get('/claims/all/data')->assertRedirect('/login');
    }

    public function test_regular_user_gets_403_for_page_and_data(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/claims/all')->assertForbidden();
        $this->getJson('/claims/all/data')->assertForbidden();
    }

    public function test_ceo_can_view_the_page_and_navigation_link(): void
    {
        $this->withoutVite();
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => 'CEO', 'guard_name' => 'web']));

        $this->actingAs($user)->get('/claims/all')->assertOk()
            ->assertViewIs('siniestros.all')->assertSee('Todos los siniestros')->assertSee('id="all-claims"', false);
    }

    public function test_superadministrator_can_view_the_page_without_ceo_role(): void
    {
        $this->withoutVite();
        $user = User::factory()->create(['email' => 'admin@gmail.com']);

        $this->actingAs($user)->get('/claims/all')->assertOk();
        $this->getJson('/claims/all/data')->assertOk();
    }

    public function test_data_includes_every_status_and_returns_only_requested_page(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => 'CEO', 'guard_name' => 'web']));
        Claim::factory()->count(15)->sequence(
            ['status' => 'Pendiente'], ['status' => 'Ausente'], ['status' => 'Coordinado'],
            ['status' => 'Peritando'], ['status' => 'IPgestion'],
        )->create();

        $response = $this->actingAs($user)->getJson('/claims/all/data?draw=2&start=10&length=10');

        $response->assertOk()->assertJsonPath('draw', 2)->assertJsonPath('recordsTotal', 15)
            ->assertJsonPath('recordsFiltered', 15)->assertJsonCount(5, 'data');
    }

    public function test_search_filters_across_companies_and_escapes_untrusted_names(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => 'CEO', 'guard_name' => 'web']));
        $company = Company::factory()->create(['name' => '<script>alert(1)</script>']);
        Claim::factory()->forCompany($company)->create(['status' => 'IPgestion']);
        Claim::factory()->create();
        $query = http_build_query([
            'draw' => 1, 'start' => 0, 'length' => 10,
            'search' => ['value' => 'alert', 'regex' => false],
            'columns' => [['data' => 'company_name', 'name' => 'companies.name', 'searchable' => 'true', 'orderable' => 'true']],
        ]);

        $response = $this->actingAs($user)->getJson('/claims/all/data?'.$query);

        $response->assertOk()->assertJsonPath('recordsTotal', 2)->assertJsonPath('recordsFiltered', 1)
            ->assertJsonPath('data.0.status', 'IPgestion')
            ->assertJsonPath('data.0.company_name', '&lt;script&gt;alert(1)&lt;/script&gt;');
    }
}
