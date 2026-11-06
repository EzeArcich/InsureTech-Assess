<?php

namespace Tests\Feature;

use App\Models\Claim;
use App\Models\Company;
use App\Models\Inspector;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ClaimPersistenceTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_authorized_user_creates_claim_with_insured_contact_details(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::create(['name' => 'crear-siniestro', 'guard_name' => 'web']));
        $company = Company::factory()->create(['prefix' => 'ML']);
        $inspector = Inspector::factory()->create();

        $response = $this->actingAs($user)->post('/claims', [
            'company_id' => $company->id, 'claim_number' => 'ML54897894656166',
            'client_name' => 'Ana Asegurada', 'phone' => '1144556677',
            'address' => 'San Martín 123', 'city' => 'Buenos Aires',
            'inspector_id' => $inspector->id, 'client_type' => 'Asegurado',
        ]);

        $response->assertRedirect('/claims');
        $this->assertDatabaseHas('clients', ['name' => 'Ana Asegurada', 'phone' => '1144556677', 'address' => 'San Martín 123', 'company_id' => $company->id]);
        $this->assertDatabaseHas('claims', ['claim_number' => 'ML54897894656166', 'inspector_id' => $inspector->id, 'created_by' => $user->id]);
    }

    public function test_updating_company_keeps_client_and_claim_consistent(): void
    {
        $claim = Claim::factory()->create();
        $company = Company::factory()->create();
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::create(['name' => 'editar-siniestro', 'guard_name' => 'web']));

        $response = $this->actingAs($user)->put('/claims/'.$claim->id, [
            'company_id' => $company->id, 'phone' => '1122334455',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('claims', ['id' => $claim->id, 'company_id' => $company->id, 'client_id' => $claim->client_id]);
        $this->assertDatabaseHas('clients', ['id' => $claim->client_id, 'company_id' => $company->id, 'phone' => '1122334455']);
    }

    public function test_invalid_company_is_rejected_without_creating_a_client(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::create(['name' => 'crear-siniestro', 'guard_name' => 'web']));

        $response = $this->actingAs($user)->post('/claims', [
            'company_id' => 999999, 'claim_number' => 'ML12345678', 'client_name' => 'Ana',
        ]);

        $response->assertSessionHasErrors('company_id');
        $this->assertDatabaseCount('claims', 0);
        $this->assertDatabaseCount('clients', 0);
    }

    public function test_guest_cannot_write_claims_through_ajax_endpoint(): void
    {
        $this->put('/claims/1/data', ['inspector_id' => 1])->assertRedirect('/login');
    }

    public function test_user_without_permission_cannot_create_claim(): void
    {
        $user = User::factory()->create();
        Permission::create(['name' => 'crear-siniestro', 'guard_name' => 'web']);
        Permission::create(['name' => 'ceo-siniestro', 'guard_name' => 'web']);

        $this->actingAs($user)->post('/claims', [])->assertForbidden();
        $this->assertDatabaseCount('claims', 0);
    }
    public function test_forms_and_claim_lists_render_with_normalized_relationships(): void
    {
        $this->withoutVite();
        $user = User::factory()->create();
        foreach (['ver-siniestro', 'crear-siniestro', 'editar-siniestro'] as $permission) {
            $user->givePermissionTo(Permission::create(['name' => $permission, 'guard_name' => 'web']));
        }
        $claim = Claim::factory()->create(['status' => 'Pendiente']);

        $this->actingAs($user)->get('/claims')->assertOk()->assertSee($claim->claim_number);
        $this->get('/claims/create')->assertOk()->assertSee('client_name')->assertSee($claim->company->name);
        $this->get('/claims/'.$claim->id.'/edit')->assertOk()->assertSee($claim->client->name);
        $this->get('/claims/'.$claim->id.'/refer')->assertOk();
        $this->get('/claims/coordinated')->assertOk();
        $this->get('/claims/assignments')->assertOk();
        $this->get('/claims/'.$claim->id.'/data')->assertOk()->assertJsonPath('client_name', $claim->client->name)
            ->assertJsonPath('inspector.id', $claim->inspector_id);
    }

    public function test_uploads_are_attached_using_the_normalized_claim_foreign_key(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::create(['name' => 'editar-siniestro', 'guard_name' => 'web']));
        $claim = Claim::factory()->create();

        $response = $this->actingAs($user)->put('/claims/'.$claim->id, [
            'urls' => [\Illuminate\Http\UploadedFile::fake()->create('inspection.pdf', 10, 'application/pdf')],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseCount('files', 1);
        $attachment = $claim->fresh()->files->sole();
        $this->assertSame($claim->id, $attachment->claim_id);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($attachment->url);
    }
    public function test_selected_producer_is_linked_and_its_details_are_saved(): void
    {
        $user = User::factory()->create(['email' => 'admin@gmail.com']);
        $claim = Claim::factory()->create();
        $producer = \App\Models\Producer::factory()->create();

        $response = $this->actingAs($user)->put('/claims/'.$claim->id, [
            'producer_id' => $producer->id,
            'producer_name' => 'A different submitted name',
        ]);

        $response->assertRedirect();
        $this->assertSame($producer->id, $claim->fresh()->producer->id);
        $this->assertDatabaseHas('claims', [
            'id' => $claim->id, 'producer_id' => $producer->id,
            'producer_name' => $producer->name, 'producer_email' => $producer->email,
        ]);
    }
}
