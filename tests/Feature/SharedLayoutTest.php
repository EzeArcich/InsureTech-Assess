<?php

namespace Tests\Feature;

use App\Models\Claim;
use App\Models\Client;
use App\Models\Inspector;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class SharedLayoutTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_sidebar_is_shared_by_every_navigation_destination(): void
    {
        $this->withoutVite();
        $user = User::factory()->create(['email' => 'admin@gmail.com']);

        $user->assignRole(\Spatie\Permission\Models\Role::findOrCreate('CEO', 'web'));

        foreach (['home', 'workshops.index', 'users.index', 'blogs.index', 'roles.index', 'claims.all'] as $route) {
            $this->actingAs($user)->get(route($route))->assertOk()
                ->assertSee('class="claims-sidebar"', false)->assertSee('class="claims-workspace"', false);
        }
    }

    public function test_client_and_inspector_profiles_display_real_details_and_related_claims(): void
    {
        $this->withoutVite();
        $user = User::factory()->create();
        $claim = Claim::factory()->create();

        $this->actingAs($user)->get(route('clients.show', $claim->client_id))->assertOk()
            ->assertSee($claim->client->name)->assertSee($claim->client->phone)->assertSee($claim->claim_number);
        $this->get(route('inspectors.show', $claim->inspector_id))->assertOk()
            ->assertSee($claim->inspector->email)->assertSee($claim->claim_number)
            ->assertSee(route('clients.show', $claim->client_id));
    }

    public function test_filtered_claim_tables_link_to_client_and_inspector_profiles(): void
    {
        $this->withoutVite();
        $user = User::factory()->create(['email' => 'admin@gmail.com']);
        $inspector = Inspector::factory()->for($user)->create();
        $claim = Claim::factory()->for($inspector)->create(['status' => 'Pendiente', 'client_type' => 'Tercero']);

        foreach (['claims.index', 'claims.third_parties'] as $route) {
            $this->actingAs($user)->get(route($route))->assertOk()
                ->assertSee(route('clients.show', $claim->client_id))->assertSee(route('inspectors.show', $inspector->id));
        }
        $claim->update(['status' => 'Coordinado']);
        $this->get(route('claims.assignments'))->assertOk()->assertSee(route('clients.show', $claim->client_id));
        $claim->update(['status' => 'Peritando']);
        $this->get(route('claims.assigned'))->assertOk()->assertSee(route('inspectors.show', $inspector->id));
    }

    public function test_profile_routes_require_login_and_missing_records_return_404(): void
    {
        $this->get('/clients/1')->assertRedirect('/login');
        $this->get('/inspectors/1')->assertRedirect('/login');
        $user = User::factory()->create();
        $this->actingAs($user)->get('/clients/999999')->assertNotFound();
        $this->get('/inspectors/999999')->assertNotFound();
    }

    public function test_profile_escapes_user_supplied_names(): void
    {
        $this->withoutVite();
        $user = User::factory()->create();
        $client = Client::factory()->create(['name' => '<script>alert(1)</script>']);

        $this->actingAs($user)->get(route('clients.show', $client))->assertOk()
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
    }
}
