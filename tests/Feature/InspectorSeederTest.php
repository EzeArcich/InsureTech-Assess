<?php

namespace Tests\Feature;

use App\Models\Inspector;
use App\Models\User;
use Database\Seeders\InspectorSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InspectorSeederTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_existing_inspectors_receive_accounts_without_duplicate_records(): void
    {
        $inspectors = Inspector::factory()->count(10)->create(['user_id' => null]);

        $this->seed(InspectorSeeder::class);

        $this->assertDatabaseCount('inspectors', 10);
        $this->assertDatabaseCount('users', 10);
        foreach ($inspectors as $inspector) {
            $user = $inspector->fresh()->user;
            $this->assertSame($inspector->email, $user->email);
            $this->assertTrue($user->hasRole('Inspectores'));
            $this->assertTrue(Hash::check('InsureTech2026!', $user->password));
        }
    }

    public function test_repeating_seed_preserves_accounts_and_passwords(): void
    {
        $this->seed(InspectorSeeder::class);
        $user = User::firstOrFail();
        $this->assertTrue(Hash::check('InsureTech2026!', $user->password));
        $user->update(['password' => Hash::make('changed-password')]);

        $this->seed(InspectorSeeder::class);

        $this->assertDatabaseCount('inspectors', 10);
        $this->assertDatabaseCount('users', 10);
        $this->assertTrue(Hash::check('changed-password', $user->fresh()->password));
    }

    public function test_existing_inspector_account_is_matched_by_email(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('Inspectores', 'web'));
        $inspector = Inspector::factory()->create(['user_id' => null, 'email' => $user->email]);

        $this->seed(InspectorSeeder::class);

        $this->assertDatabaseCount('inspectors', 10);
        $this->assertDatabaseCount('users', 10);
        $this->assertSame($user->id, $inspector->fresh()->user_id);
    }

    public function test_factory_creates_a_linked_inspector_user_with_role(): void
    {
        $inspector = Inspector::factory()->create();

        $this->assertSame($inspector->email, $inspector->user->email);
        $this->assertSame($inspector->name, $inspector->user->name);
        $this->assertTrue($inspector->user->hasRole('Inspectores'));
    }
}
