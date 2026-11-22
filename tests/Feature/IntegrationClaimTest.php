<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class IntegrationClaimTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_rejects_claim_without_credentials(): void
    {
        $response = $this->postJson('/api/v1/integrations/claims', []);

        $response->assertUnauthorized()
            ->assertJsonPath('message', 'Credencial de aseguradora inválida.')
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertHeader('X-Request-ID');
    }

    public function test_rejects_claim_with_invalid_token(): void
    {
        $response = $this->withToken('invalid_token')->postJson('/api/v1/integrations/claims', []);

        $response->assertUnauthorized()
            ->assertJsonPath('message', 'Credencial de aseguradora inválida.')
            ->assertHeader('X-Request-ID');
    }
}
