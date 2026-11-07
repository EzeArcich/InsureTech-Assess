<?php

namespace Tests\Feature;

use App\Models\ClaimSimulationRun;
use App\Models\Company;
use App\Models\SimulationProfile;
use App\Models\User;
use App\Services\GenerateSimulatedClaims;
use App\Services\IssueCompanyIntegrationCredential;
use App\Services\SimulationBehavior;
use App\Services\SimulationEngine;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class SimulationBehaviorTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function simulation(array $configuration): ClaimSimulationRun
    {
        config(['claim_integrations.simulator_enabled' => true]);
        $issued = app(IssueCompanyIntegrationCredential::class)->handle(Company::factory()->create(), 'lab', true);
        $profile = SimulationProfile::factory()->create(['credential_id' => $issued['credential']->id]);
        $profile->update(['configuration' => array_replace($profile->configuration, $configuration)]);

        return app(SimulationEngine::class)->start($profile, User::factory()->create());
    }

    public function test_exponential_intervals_are_variable_reproducible_and_close_to_configured_mean(): void
    {
        $this->freezeTime();
        $run = $this->simulation(['distribution' => 'exponential']);
        $behavior = app(SimulationBehavior::class);
        $intervals = [];
        for ($position = 1; $position <= 10000; $position++) {
            $intervals[] = $behavior->interval($run, $position);
        }
        $mean = array_sum($intervals) / count($intervals);
        $this->assertGreaterThan(285, $mean);
        $this->assertLessThan(315, $mean);
        $this->assertGreaterThan(100, count(array_unique($intervals)));
        $this->assertSame($intervals[29], $behavior->interval($run, 30));
        $this->assertGreaterThanOrEqual(1, min($intervals));
    }

    public function test_peak_and_weather_accelerate_only_during_the_configured_initial_window(): void
    {
        $this->freezeTime();
        $run = $this->simulation(['scenario' => 'peak', 'peak_multiplier' => 2, 'peak_minutes' => 15]);
        $behavior = app(SimulationBehavior::class);
        $this->assertSame(150, $behavior->interval($run, 1));
        $this->travel(16)->minutes();
        $this->assertSame(300, $behavior->interval($run, 1));
        $weather = $this->simulation(['scenario' => 'weather', 'reasons' => ['robo' => 100]]);
        $payload = $behavior->payload($weather, 1, app(GenerateSimulatedClaims::class));
        $this->assertSame('Todo riesgo', $payload['reason']);
        $this->assertStringContainsString('granizo', $payload['notes']);
        $this->withToken($weather->credential->simulator_token)->postJson('/api/v1/integrations/claims', $payload)->assertCreated();
    }

    public function test_weighted_reasons_and_independent_event_payloads_are_reproducible_and_domain_valid(): void
    {
        $this->freezeTime();
        $run = $this->simulation(['reasons' => ['Todo riesgo' => 75, 'robo' => 25, 'incendio' => 0]]);
        $behavior = app(SimulationBehavior::class);
        $generator = app(GenerateSimulatedClaims::class);
        $counts = ['Todo riesgo' => 0, 'robo' => 0];
        for ($position = 1; $position <= 1000; $position++) {
            $payload = $behavior->payload($run, $position, $generator);
            $counts[$payload['reason']]++;
        }
        $this->assertGreaterThan(700, $counts['Todo riesgo']);
        $this->assertLessThan(800, $counts['Todo riesgo']);
        $first = $behavior->payload($run, 10, $generator);
        $this->assertSame($first, $behavior->payload($run, 10, $generator));
        $this->assertNotSame($first['external_id'], $behavior->payload($run, 11, $generator)['external_id']);
        $this->withToken($run->credential->simulator_token)->postJson('/api/v1/integrations/claims', $first)->assertCreated();
    }

    #[TestWith(['timeout'])]
    #[TestWith(['transient'])]
    #[TestWith(['http_error'])]
    public function test_lab_faults_are_disabled_by_default_and_injected_once_without_changing_the_api(string $fault): void
    {
        $this->freezeTime();
        // Seed 42 deterministically selects event 1 at the allowed 50% fault rate.
        $run = $this->simulation(['scenario' => 'communication', 'fault_percent' => 50, 'fault_type' => $fault]);
        $behavior = app(SimulationBehavior::class);
        $this->assertNull($behavior->fault($run, 1, 1));
        config(['claim_integrations.engine.faults_enabled' => true]);
        $position = collect(range(1, 20))->first(fn ($position) => $behavior->fault($run, $position, 1) !== null);
        $run->update(['planned_count' => $position - 1]);
        $engine = app(SimulationEngine::class);
        $engine->tick();
        $item = $run->items()->first();
        Http::preventStrayRequests();
        $engine->send($item->id, $item->reservation);
        Http::assertNothingSent();
        $this->assertSame('retry', $item->fresh()->status);
        $this->assertNull($behavior->fault($run, $position, 2));
        $this->assertDatabaseCount('claims', 0);
        $this->assertDatabaseHas('simulation_event_attempts', ['outcome' => 'simulated_'.$fault]);
    }
}
