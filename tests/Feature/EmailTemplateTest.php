<?php

namespace Tests\Feature;

use App\Models\EmailTemplate;
use App\Models\EmailTemplateVersion;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EmailTemplateTest extends TestCase
{
    use LazilyRefreshDatabase;

    #[TestWith(['CEO'])]
    #[TestWith(['Administrativo'])]
    #[TestWith(['Coordinaciones'])]
    public function test_authorized_roles_manage_templates_with_immutable_versions_and_restore_history(string $role): void
    {
        $this->withoutVite();
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate($role, 'web'));
        $payload = ['name' => 'Coordinación', 'subject' => 'Siniestro {{ claim_number }}', 'body' => '<p>Hola <strong>{{ producer_name }}</strong></p>', 'is_active' => 1];
        $this->actingAs($user)->get('/email-templates')->assertOk();
        $this->get('/email-templates/create')->assertOk()->assertSee('template-editor');
        $this->post('/email-templates', $payload)->assertRedirect()->assertSessionHasNoErrors();
        $template = EmailTemplate::firstOrFail();
        $first = $template->latestVersion;
        $this->get('/email-templates/'.$template->id.'/edit')->assertOk()->assertSee('Guardar nueva versión');
        $this->put('/email-templates/'.$template->id, [...$payload, 'base_version' => 1, 'subject' => 'Nueva versión'])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame('Siniestro {{ claim_number }}', $first->fresh()->subject);
        $this->assertEquals(2, $template->fresh()->latestVersion->number);
        $this->putJson('/email-templates/'.$template->id, [...$payload, 'base_version' => 1])->assertUnprocessable()->assertJsonValidationErrors('base_version');
        $this->post('/email-templates/'.$template->id.'/versions/'.$first->id.'/restore')->assertRedirect();
        $this->assertEquals(3, $template->fresh()->latestVersion->number);
        $this->get('/email-templates/'.$template->id)->assertOk()->assertSee('Versión 3');
        $this->delete('/email-templates/'.$template->id)->assertRedirect();
        $this->assertFalse($template->fresh()->is_active);
        $this->assertDatabaseCount('email_template_versions', 3);
    }

    public function test_template_content_is_sanitized_and_unknown_variables_are_rejected(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('Coordinaciones', 'web'));
        $data = ['name' => 'Segura', 'is_active' => 1, 'subject' => 'Hola', 'body' => '<p onclick="bad()">Hola<strong>texto</strong><script>alert(1)</script><img src="x" onerror="bad()"><a href="javascript:bad()">Enlace</a></p>'];
        $this->actingAs($user)->post('/email-templates', $data)->assertRedirect()->assertSessionHasNoErrors();
        $body = EmailTemplateVersion::firstOrFail()->body;
        $this->assertStringContainsString('<strong>texto</strong>', $body);
        foreach (['script', 'onclick', '<img', 'javascript:'] as $danger) {
            $this->assertStringNotContainsString($danger, $body);
        }
        $this->postJson('/email-templates', [...$data, 'name' => 'Otra', 'body' => '{{ unknown_variable }}'])->assertUnprocessable()->assertJsonValidationErrors('body');
        $this->assertDatabaseCount('email_templates', 1);
    }

    public function test_other_roles_cannot_read_or_write_templates(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('Inspectores', 'web'));
        $this->actingAs($user)->get('/email-templates')->assertForbidden();
        $this->post('/email-templates', [])->assertForbidden();
        $this->actingAs(User::factory()->create())->get('/email-templates')->assertForbidden();
    }
}
