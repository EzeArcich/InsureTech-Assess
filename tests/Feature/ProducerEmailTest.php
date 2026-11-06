<?php

namespace Tests\Feature;

use App\Mail\ProducerMessage;
use App\Models\Claim;
use App\Models\EmailDelivery;
use App\Models\EmailTemplateVersion;
use App\Models\Producer;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProducerEmailTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_modal_replaces_legacy_fields_and_preview_does_not_send_mail(): void
    {
        $this->withoutVite();
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('Coordinaciones', 'web'));
        $claim = Claim::factory()->create(['status' => 'Pendiente']);
        $version = EmailTemplateVersion::factory()->create();
        $producer = Producer::factory()->create(['name' => '<img src=x>Nombre', 'email' => 'producer@example.com']);
        Mail::fake();
        $this->actingAs($user)->get('/claims/'.$claim->id.'/edit')->assertOk()->assertSee('producer-email-modal')
            ->assertDontSee('Link 2.0')->assertDontSee('Email del PAS')->assertDontSee('Segundo en copia')->assertDontSee('Nro. corto')
            ->assertDontSee('name="cc"', false)->assertDontSee('onclick="Correo(event)"', false)
            ->assertSee('Continuar a Coordinación')->assertSee('Anterior');
        $response = $this->postJson('/claims/'.$claim->id.'/producer-email/preview', ['producer_id' => $producer->id, 'template_version_id' => $version->id]);
        $response->assertOk()->assertJsonPath('recipient', 'producer@example.com')->assertJsonPath('subject', 'Siniestro '.$claim->claim_number);
        $this->assertStringContainsString('&lt;img src=x&gt;Nombre', $response->json('body'));
        Mail::assertNothingSent();
    }

    public function test_sending_uses_saved_recipient_validated_copies_and_snapshot_version_without_double_sending(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('Coordinaciones', 'web'));
        $claim = Claim::factory()->create(['status' => 'Pendiente']);
        $version = EmailTemplateVersion::factory()->create();
        $producer = Producer::factory()->create(['email' => 'producer@example.com']);
        $data = ['producer_id' => $producer->id, 'template_version_id' => $version->id, 'cc' => ['copy@example.com'], 'request_id' => (string) Str::uuid(), 'recipient' => 'tampered@example.com'];
        Mail::fake();
        $url = '/claims/'.$claim->id.'/producer-email';
        $this->actingAs($user)->postJson($url, $data)->assertOk();
        $this->postJson($url, $data)->assertOk();
        Mail::assertSent(ProducerMessage::class, fn (ProducerMessage $mail): bool => $mail->hasTo('producer@example.com') && $mail->hasCc('copy@example.com') && $mail->messageSubject === 'Siniestro '.$claim->claim_number);
        Mail::assertSentCount(1);
        $this->assertDatabaseHas('email_deliveries', ['claim_id' => $claim->id, 'email_template_version_id' => $version->id, 'recipient' => 'producer@example.com', 'status' => 'sent']);
        $this->assertSame(['copy@example.com'], EmailDelivery::firstOrFail()->cc);
        $this->postJson($url, [...$data, 'request_id' => (string) Str::uuid(), 'cc' => ['invalid-email']])->assertUnprocessable()->assertJsonValidationErrors('cc.0');
    }

    public function test_restricted_claims_and_inactive_templates_cannot_be_used_for_mail(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('Coordinaciones', 'web'));
        $claim = Claim::factory()->create(['status' => 'Peritando']);
        $version = EmailTemplateVersion::factory()->create();
        $producer = Producer::factory()->create();
        Mail::fake();
        $data = ['producer_id' => $producer->id, 'template_version_id' => $version->id];
        $this->actingAs($user)->postJson('/claims/'.$claim->id.'/producer-email/preview', $data)->assertNotFound();
        $pending = Claim::factory()->create(['status' => 'Pendiente']);
        $version->template->update(['is_active' => false]);
        $this->postJson('/claims/'.$pending->id.'/producer-email/preview', $data)->assertUnprocessable();
        Mail::assertNothingSent();
    }

    public function test_mailable_renders_html_and_plain_text_from_the_snapshot(): void
    {
        $mail = new ProducerMessage('Asunto', '<p>Hola <strong>Productor</strong></p>');
        $mail->assertSeeInHtml('<strong>Productor</strong>', false);
        $mail->assertSeeInText('Hola Productor');
    }
    public function test_delivery_failure_is_recorded_without_claiming_success(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('Coordinaciones', 'web'));
        $claim = Claim::factory()->create(['status' => 'Pendiente']);
        $version = EmailTemplateVersion::factory()->create();
        $producer = Producer::factory()->create();
        Mail::shouldReceive('to')->once()->with($producer->email)->andThrow(new \RuntimeException('Transport unavailable'));
        $this->actingAs($user)->postJson('/claims/'.$claim->id.'/producer-email', [
            'producer_id' => $producer->id, 'template_version_id' => $version->id, 'request_id' => (string) Str::uuid(),
        ])->assertStatus(503)->assertJsonPath('message', 'No se pudo enviar el correo. Revisá la configuración de correo e intentá un nuevo envío.');
        $this->assertDatabaseHas('email_deliveries', ['claim_id' => $claim->id, 'status' => 'failed', 'sent_at' => null]);
    }

}
