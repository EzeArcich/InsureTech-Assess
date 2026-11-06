<?php

namespace Tests\Feature;

use App\Models\Claim;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_ceo_can_access_blogs_without_a_special_email_or_individual_permissions(): void
    {
        $this->withoutVite();
        $user = User::factory()->create(['email' => 'director@example.com']);
        $user->assignRole(Role::create(['name' => 'CEO', 'guard_name' => 'web']));

        $this->actingAs($user)->get('/blogs')->assertOk()->assertSee('Nueva noticia');
        $this->get('/blogs/create')->assertOk();
    }

    public function test_regular_user_still_requires_blog_permission(): void
    {
        $this->withoutVite();
        $user = User::factory()->create();
        $permission = Permission::create(['name' => 'ver-blog', 'guard_name' => 'web']);

        $this->actingAs($user)->get('/blogs')->assertForbidden();
        $user->givePermissionTo($permission);
        $this->get('/blogs')->assertOk();
    }

    public function test_dashboard_displays_status_counts_and_companies_ranked_by_claim_count(): void
    {
        $this->withoutVite();
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => 'CEO', 'guard_name' => 'web']));
        $small = Company::factory()->create(['name' => 'Compañía menor']);
        $large = Company::factory()->create(['name' => 'Compañía mayor']);
        Claim::factory()->count(2)->forCompany($small)->create(['status' => 'IPgestion']);
        Claim::factory()->count(3)->forCompany($large)->create(['status' => 'Pendiente']);

        $response = $this->actingAs($user)->get('/home');

        $response->assertOk()->assertViewHas('totalClaims', 5)
            ->assertViewHas('statusCounts', fn ($counts): bool => $counts['Pendiente'] === 3 && $counts['IPgestion'] === 2 && $counts['Ausente'] === 0)
            ->assertViewHas('companies', fn ($companies): bool => $companies->pluck('id')->all() === [$large->id, $small->id])
            ->assertSee(route('claims.all', ['status' => 'Pendiente']))
            ->assertSee(route('claims.all', ['company_id' => $large->id]));
    }

    public function test_status_and_company_filters_are_applied_to_datatable_results(): void
    {
        $user = User::factory()->create(['email' => 'admin@gmail.com']);
        $company = Company::factory()->create();
        Claim::factory()->forCompany($company)->create(['status' => 'Pendiente']);
        Claim::factory()->forCompany($company)->create(['status' => 'Ausente']);
        Claim::factory()->create(['status' => 'Pendiente']);

        $response = $this->actingAs($user)->getJson('/claims/all/data?'.http_build_query([
            'company_id' => $company->id, 'status' => 'Pendiente', 'length' => 25,
        ]));

        $response->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.status', 'Pendiente')
            ->assertJsonPath('data.0.company_name', $company->name);
    }

    public function test_filtered_list_preserves_filters_in_ajax_source(): void
    {
        $this->withoutVite();
        $user = User::factory()->create(['email' => 'admin@gmail.com']);

        $this->actingAs($user)->get('/claims/all?status=IPgestion')->assertOk()
            ->assertSee(route('claims.all.data', ['status' => 'IPgestion']))->assertSee('Quitar filtros');
    }

    public function test_empty_dashboard_has_zero_counts_and_a_meaningful_empty_state(): void
    {
        $this->withoutVite();
        $user = User::factory()->create();

        $this->actingAs($user)->get('/home')->assertOk()->assertViewHas('totalClaims', 0)
            ->assertSee('Todavía no hay siniestros cargados.')->assertSee('Todavía no hay compañías cargadas.');
    }

    public function test_dashboard_requires_authentication(): void
    {
        $this->get('/home')->assertRedirect('/login');
    }
}
