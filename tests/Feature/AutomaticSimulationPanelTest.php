<?php

namespace Tests\Feature;

use App\Models\ClaimSimulationRun;
use App\Models\Company;
use App\Models\SimulationProfile;
use App\Models\User;
use App\Services\IssueCompanyIntegrationCredential;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AutomaticSimulationPanelTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function profile(): SimulationProfile
    {
        config(['claim_integrations.simulator_enabled' => true]);
        $this->withoutVite();
        $issued = app(IssueCompanyIntegrationCredential::class)->handle(Company::factory()->create(), 'lab', true);

        return SimulationProfile::factory()->create(['credential_id' => $issued['credential']->id]);
    }

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('CEO', 'web'));

        return $user;
    }

    public function test_administration_requires_auth_permission_and_local_feature_flag(): void
    {
        $profile = $this->profile();
        $this->get('/claim-simulations/automatic')->assertRedirect('/login');
        $this->actingAs(User::factory()->create())->post('/claim-simulations/automatic/profiles/'.$profile->id.'/start', ['confirmation' => 1])->assertForbidden();
        $this->get('/claim-simulations/automatic/metrics')->assertForbidden();
        config(['claim_integrations.simulator_enabled' => false]);
        $this->actingAs($this->admin())->get('/claim-simulations/automatic')->assertNotFound();
        config(['claim_integrations.simulator_enabled' => true]);
        $this->app->detectEnvironment(fn () => 'production');
        $this->get('/claim-simulations/automatic')->assertNotFound();
        $this->assertDatabaseCount('claim_simulation_runs', 0);
    }

    public function test_profiles_can_be_created_edited_duplicated_disabled_and_started_with_confirmation(): void
    {
        $profile = $this->profile();
        $this->actingAs($this->admin());
        $this->get('/claim-simulations/automatic')->assertOk()->assertSee('Simulaciones automáticas');
        $this->get('/claim-simulations/automatic/profiles/create')->assertOk();
        $this->get('/claim-simulations/automatic/profiles/'.$profile->id.'/edit')->assertOk();
        $data = $profile->only('name', 'credential_id', 'configuration', 'enabled');
        $data['name'] = 'Segundo perfil';
        $this->post('/claim-simulations/automatic/profiles', $data)->assertRedirect();
        $this->assertDatabaseHas('simulation_profiles', ['name' => 'Segundo perfil']);
        $this->post('/claim-simulations/automatic/profiles/'.$profile->id.'/duplicate')->assertRedirect();
        $this->assertDatabaseHas('simulation_profiles', ['name' => $profile->name.' (copia)', 'enabled' => false]);
        $this->post('/claim-simulations/automatic/profiles/'.$profile->id.'/start')->assertSessionHasErrors('confirmation');
        $this->post('/claim-simulations/automatic/profiles/'.$profile->id.'/start', ['confirmation' => 1])->assertRedirect();
        $run = ClaimSimulationRun::firstOrFail();
        $this->get('/claim-simulations/automatic/runs/'.$run->id)->assertOk()->assertSee('Snapshot de configuración')->assertDontSee($profile->credential->simulator_token);
        $this->post('/claim-simulations/automatic/runs/'.$run->id.'/transition', ['action' => 'pause'])->assertRedirect();
        $this->assertSame('paused', $run->fresh()->status);
        $this->post('/claim-simulations/automatic/runs/'.$run->id.'/transition', ['action' => 'resume'])->assertRedirect();
        $this->post('/claim-simulations/automatic/runs/'.$run->id.'/transition', ['action' => 'stop'])->assertRedirect();
        $this->post('/claim-simulations/automatic/runs/'.$run->id.'/transition', ['action' => 'resume'])->assertConflict();
        $this->getJson('/claim-simulations/automatic/metrics')->assertOk()->assertJsonPath('active', 0)->assertDontSee($profile->credential->simulator_token);
        $data['enabled'] = false;
        $this->put('/claim-simulations/automatic/profiles/'.$profile->id, $data)->assertRedirect();
        $this->post('/claim-simulations/automatic/profiles/'.$profile->id.'/start', ['confirmation' => 1])->assertUnprocessable();
    }

    public function test_profiles_reject_real_credentials_unbounded_load_and_unknown_domain_types(): void
    {
        $profile = $this->profile();
        $this->actingAs($this->admin());
        $data = $profile->only('name', 'credential_id', 'configuration', 'enabled');
        $real = app(IssueCompanyIntegrationCredential::class)->handle(Company::factory()->create(), 'real');
        $data['credential_id'] = $real['credential']->id;
        $data['configuration']['max_per_hour'] = 999999;
        $data['configuration']['reasons'] = ['granizo' => 100];
        $this->post('/claim-simulations/automatic/profiles', $data)->assertSessionHasErrors(['credential_id', 'configuration.max_per_hour']);
        $data['credential_id'] = $profile->credential_id;
        $data['configuration']['max_per_hour'] = 30;
        $this->post('/claim-simulations/automatic/profiles', $data)->assertSessionHasErrors('configuration.reasons');
        $data['configuration']['reasons'] = ['Todo riesgo' => 100];
        $data['configuration']['fault_percent'] = 10;
        $this->post('/claim-simulations/automatic/profiles', $data)->assertSessionHasErrors('configuration.fault_percent');
        $this->assertDatabaseCount('simulation_profiles', 1);
    }

    public function test_additional_insurers_get_lab_credentials_only_when_explicitly_enabled(): void
    {
        $profile = $this->profile();
        $company = Company::factory()->create();
        config(['claim_integrations.simulator_enabled' => false]);
        $this->artisan('integrations:issue-token', ['company' => $company->id, '--simulation' => true])->assertFailed();
        $this->assertDatabaseCount('company_integration_credentials', 1);
        config(['claim_integrations.simulator_enabled' => true]);
        $this->artisan('integrations:issue-token', ['company' => $company->id, '--simulation' => true])->assertSuccessful();
        $this->assertDatabaseHas('company_integration_credentials', ['company_id' => $company->id, 'is_simulation' => true]);
        $this->actingAs($this->admin())->get('/claim-simulations/automatic')->assertOk()->assertSee($company->name)->assertDontSee($profile->credential->simulator_token);
    }
}
