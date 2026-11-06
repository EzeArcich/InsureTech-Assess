<?php

namespace Tests\Feature;

use App\Models\Claim;
use App\Models\Inspector;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class EnglishRoutesTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_claim_filters_are_not_captured_by_resource_model_binding(): void
    {
        $this->withoutVite();
        $user = User::factory()->create(['email' => 'admin@gmail.com']);

        foreach (['pending', 'absent', 'coordinated', 'assigned', 'in-progress', 'quotes', 'third-parties', 'referrals', 'assignments'] as $filter) {
            $this->actingAs($user)->get('/claims/'.$filter)->assertOk();
        }
    }

    public function test_english_resource_parameter_resolves_claim_and_missing_claim_returns_404(): void
    {
        $this->withoutVite();
        $user = User::factory()->create(['email' => 'admin@gmail.com']);
        $claim = Claim::factory()->create();

        $this->actingAs($user)->get(route('claims.edit', ['claim' => $claim->id]))
            ->assertOk()->assertSee($claim->claim_number);
        $this->get('/claims/999999/edit')->assertNotFound();
        $this->get(route('users.edit', ['user' => $user->id]))->assertOk()->assertSee($user->email);
    }

    public function test_inspector_lookup_uses_the_new_domain_url(): void
    {
        $user = User::factory()->create();
        $inspector = Inspector::factory()->create();

        $this->actingAs($user)->getJson('/inspectors/'.$inspector->id.'/data')
            ->assertOk()->assertJsonPath('id', $inspector->id);
    }

    public function test_domain_lookup_requires_authentication(): void
    {
        $this->getJson('/inspectors/1/data')->assertUnauthorized();
        $this->get('/claims/pending')->assertRedirect('/login');
    }
}
