<?php

namespace Tests\Feature;

use App\Models\Claim;
use App\Models\Inspector;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ClaimEnhancementsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_inspector_can_upload_photos_but_cannot_change_the_insured_account(): void
    {
        $this->withoutVite();
        Storage::fake('local');
        $inspector = Inspector::factory()->create();
        $claim = Claim::factory()->for($inspector)->create(['status' => 'Peritando', 'insured_account' => 'Relato original']);
        $this->actingAs($inspector->user)->putJson('/claims/'.$claim->id, ['insured_account' => 'Otro relato'])->assertUnprocessable();
        $this->assertSame('Relato original', $claim->fresh()->insured_account);

        $this->put('/claims/'.$claim->id, ['inspection_photos' => [UploadedFile::fake()->image('frente.jpg')]])->assertRedirect();
        $photo = $claim->inspectionPhotos()->firstOrFail();
        Storage::disk('local')->assertExists($photo->path);
        $this->get('/claims/'.$claim->id.'/edit')->assertOk()->assertSee('Relato original')->assertSee('Fotos de la pericia');
        $this->get('/claims/'.$claim->id.'/photos/'.$photo->id)->assertOk();
        $other = Claim::factory()->create(['status' => 'Peritando']);
        $this->get('/claims/'.$other->id.'/photos/'.$photo->id)->assertNotFound();
    }

    public function test_vehicle_catalog_can_be_reused_and_assigned_to_claims(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::findOrCreate('editar-siniestro', 'web'));
        $data = ['brand' => 'Chevrolet', 'model' => 'Corsa', 'year' => 2010, 'version' => 'Classic', 'doors' => 5];
        $response = $this->actingAs($user)->postJson('/vehicles', $data)->assertCreated();
        $vehicle = Vehicle::findOrFail($response->json('id'));
        $claim = Claim::factory()->create(['vehicle_id' => $vehicle->id]);
        $otherVehicle = Vehicle::factory()->create();
        $this->putJson('/claims/'.$claim->id, ['vehicle_id' => $otherVehicle->id])->assertUnprocessable()->assertJsonValidationErrors('vehicle_id');
        $this->assertSame($vehicle->id, $claim->fresh()->vehicle_id);
        $this->deleteJson('/vehicles/'.$vehicle->id)->assertStatus(409);
        $this->putJson('/claims/'.$claim->id, ['insured_account' => 'Alterado'])->assertUnprocessable();
        $this->postJson('/vehicles', [...$data, 'year' => 100])->assertUnprocessable();
    }
}
