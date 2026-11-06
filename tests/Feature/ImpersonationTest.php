<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ImpersonationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_local_ceo_can_replace_session_with_target_user(): void
    {
        $this->app['env'] = 'local';
        $this->withSession(['_token' => 'impersonation-test-token']);
        $admin = User::factory()->create();
        $admin->assignRole(Role::create(['name' => 'CEO', 'guard_name' => 'web']));
        $target = User::factory()->create();

        $response = $this->actingAs($admin)->withSession(['admin_only_data' => 'secret'])
            ->post(route('users.impersonate', $target), ['_token' => 'impersonation-test-token']);

        $response->assertRedirect(route('home'))->assertSessionMissing('admin_only_data');
        $this->assertAuthenticatedAs($target, 'web');
        $this->assertFalse($target->fresh()->can('view-all-claims'));
    }

    public function test_regular_user_can_switch_accounts_in_local_environment(): void
    {
        $this->app['env'] = 'local';
        $this->withSession(['_token' => 'impersonation-test-token']);
        $user = User::factory()->create();
        $target = User::factory()->create();

        $this->actingAs($user)->post(route('users.impersonate', $target), ['_token' => 'impersonation-test-token'])->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($target);
    }

    public function test_switching_is_disabled_in_production_even_for_superadmin(): void
    {
        $this->app['env'] = 'production';
        $this->withSession(['_token' => 'impersonation-test-token']);
        $admin = User::factory()->create(['email' => 'admin@gmail.com']);
        $target = User::factory()->create();

        $this->actingAs($admin)->post(route('users.impersonate', $target), ['_token' => 'impersonation-test-token'])->assertNotFound();
        $this->assertAuthenticatedAs($admin);
    }

    public function test_guest_must_log_in_before_switching(): void
    {
        $this->app['env'] = 'local';
        $this->withSession(['_token' => 'impersonation-test-token']);
        $target = User::factory()->create();

        $this->post(route('users.impersonate', $target), ['_token' => 'impersonation-test-token'])->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_button_is_available_to_all_users_locally_but_not_for_their_own_account(): void
    {
        $this->withoutVite();
        $this->app['env'] = 'local';
        $this->withSession(['_token' => 'impersonation-test-token']);
        $admin = User::factory()->create(['email' => 'admin@gmail.com']);
        $target = User::factory()->create();

        $this->actingAs($admin)->get('/users')->assertOk()
            ->assertSee(route('users.impersonate', $target))->assertDontSee(route('users.impersonate', $admin));
        $this->app['env'] = 'production';
        $this->withSession(['_token' => 'impersonation-test-token']);
        $this->get('/users')->assertOk()->assertDontSee('Ingresar como');
        $this->app['env'] = 'local';
        $this->withSession(['_token' => 'impersonation-test-token']);
        $this->actingAs($target)->get('/users')->assertOk()->assertSee('Ingresar como');
    }

    public function test_missing_target_preserves_current_session(): void
    {
        $this->app['env'] = 'local';
        $this->withSession(['_token' => 'impersonation-test-token']);
        $admin = User::factory()->create(['email' => 'admin@gmail.com']);

        $this->actingAs($admin)->post('/users/999999/impersonate', ['_token' => 'impersonation-test-token'])->assertNotFound();
        $this->assertAuthenticatedAs($admin);
    }

    public function test_switching_to_current_user_is_rejected(): void
    {
        $this->app['env'] = 'local';
        $this->withSession(['_token' => 'impersonation-test-token']);
        $admin = User::factory()->create(['email' => 'admin@gmail.com']);

        $this->actingAs($admin)->post(route('users.impersonate', $admin), ['_token' => 'impersonation-test-token'])->assertUnprocessable();
        $this->assertAuthenticatedAs($admin);
    }
    public function test_inspector_can_open_users_and_switch_accounts_locally_without_editing_users(): void
    {
        $this->withoutVite();
        $this->app['env'] = 'local';
        $this->withSession(['_token' => 'impersonation-test-token']);
        $inspector = \App\Models\Inspector::factory()->create();
        $admin = User::factory()->create(['email' => 'admin@gmail.com']);

        $this->actingAs($inspector->user)->get('/users')->assertOk()->assertSee('Ingresar como')
            ->assertDontSee(route('users.edit', $admin))->assertDontSee('Nuevo usuario');
        $this->get('/users/'.$admin->id.'/edit')->assertForbidden();
        $this->post(route('users.impersonate', $admin), ['_token' => 'impersonation-test-token'])->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($admin);
    }
}
