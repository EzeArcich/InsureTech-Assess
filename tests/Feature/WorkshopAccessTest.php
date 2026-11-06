<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WorkshopAccessTest extends TestCase
{
    use LazilyRefreshDatabase;

    #[TestWith(['Coordinaciones'])]
    #[TestWith(['Inspectores'])]
    #[TestWith(['Hojas de ruta'])]
    #[TestWith(['Inspecciones'])]
    #[TestWith(['Supervisor técnico'])]
    #[TestWith(['Terceros'])]
    public function test_all_operational_roles_can_view_workshops_but_cannot_manage_them(string $role): void
    {
        $this->withoutVite();
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate($role, 'web'));
        $workshop = Workshop::factory()->create();
        $this->actingAs($user)->get('/workshops')->assertOk()->assertSee($workshop->name)
            ->assertSee('href="'.route('workshops.index').'"', false)
            ->assertDontSee('Nuevo taller')->assertDontSee('href="'.route('workshops.edit', $workshop).'"', false);
        $this->get('/workshops/create')->assertForbidden();
        $this->post('/workshops', ['name' => 'Nuevo taller', 'address' => 'Nueva 123'])->assertForbidden();
        $this->get('/workshops/'.$workshop->id.'/edit')->assertForbidden();
        $this->put('/workshops/'.$workshop->id, ['name' => 'Otro nombre', 'address' => $workshop->address])->assertForbidden();
        $this->delete('/workshops/'.$workshop->id)->assertForbidden();
        $this->assertDatabaseCount('workshops', 1);
    }

    #[TestWith(['CEO'])]
    #[TestWith(['Administrativo'])]
    public function test_ceo_and_administrative_can_create_workshops_from_the_listing(string $role): void
    {
        $this->withoutVite();
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate($role, 'web'));
        $this->actingAs($user)->get('/workshops')->assertOk()->assertSee('Nuevo taller');
        $this->get('/workshops/create')->assertOk()->assertSee('Horarios de atención');
        $this->post('/workshops', ['name' => 'Taller de prueba', 'address' => 'Mitre 123', 'opening_hours' => '9 a 18'])
            ->assertRedirect('/workshops')->assertSessionHasNoErrors();
        $this->assertDatabaseHas('workshops', ['name' => 'Taller de prueba', 'opening_hours' => '9 a 18']);
    }

    public function test_guest_cannot_view_workshops_and_legacy_admin_email_does_not_grant_creation(): void
    {
        $this->get('/workshops')->assertRedirect('/login');
        $this->actingAs(User::factory()->create(['email' => 'admin@gmail.com']))->get('/workshops/create')->assertForbidden();
    }
}
