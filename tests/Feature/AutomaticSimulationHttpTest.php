<?php

namespace Tests\Feature;

use App\Models\Claim;
use App\Models\Company;
use App\Models\SimulationProfile;
use App\Models\User;
use App\Services\IssueCompanyIntegrationCredential;
use App\Services\SimulationBehavior;
use App\Services\SimulationEngine;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Process\Process;
use Tests\TestCase;

class AutomaticSimulationHttpTest extends TestCase
{
    public function test_planner_database_worker_and_real_http_receiver_recover_a_lost_response_without_duplicate_claims(): void
    {
        if (getenv('INTEGRATION_HTTP_TESTS') !== 'true') {
            $this->markTestSkipped('Opt-in: INTEGRATION_HTTP_TESTS=true; servidor HTTP y SQLite temporal.');
        }
        $database = tempnam(sys_get_temp_dir(), 'insuretech_auto_http_');
        $original = config('database.default');
        $originalDatabase = config('database.connections.sqlite.database');
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => $database,
            'claim_integrations.simulator_enabled' => true, 'claim_integrations.engine.faults_enabled' => true,
            'claim_integrations.engine.backoff' => [1]]);
        DB::purge('sqlite');
        $server = null;
        try {
            $this->artisan('migrate', ['--force' => true, '--no-interaction' => true])->assertSuccessful();
            $issued = app(IssueCompanyIntegrationCredential::class)->handle(Company::factory()->create(), 'automatic HTTP lab', true);
            $profile = SimulationProfile::factory()->create(['credential_id' => $issued['credential']->id]);
            $profile->update(['configuration' => array_replace($profile->configuration, ['scenario' => 'communication', 'fault_percent' => 50, 'fault_type' => 'lost_response'])]);
            $engine = app(SimulationEngine::class);
            $run = $engine->start($profile, User::factory()->create());
            $behavior = app(SimulationBehavior::class);
            $position = collect(range(1, 100))->first(fn ($position) => $behavior->fault($run, $position, 1) === 'lost_response');
            $run->update(['planned_count' => $position - 1]);
            $socket = stream_socket_server('tcp://127.0.0.1:0');
            $address = stream_socket_get_name($socket, false);
            fclose($socket);
            $port = (int) substr(strrchr($address, ':'), 1);
            $origin = 'http://127.0.0.1:'.$port;
            config(['claim_integrations.simulator_origin' => $origin]);
            $server = new Process([PHP_BINARY, 'artisan', 'serve', '--host=127.0.0.1', '--port='.$port, '--no-reload', '--no-interaction'], base_path(), [
                'APP_ENV' => 'testing', 'APP_KEY' => config('app.key'), 'APP_DEBUG' => 'false',
                'DB_CONNECTION' => 'sqlite', 'DB_DATABASE' => $database, 'DB_URL' => false, 'DATABASE_URL' => false,
                'CACHE_DRIVER' => 'array', 'SESSION_DRIVER' => 'array', 'QUEUE_CONNECTION' => 'database', 'MAIL_MAILER' => 'array',
            ]);
            $server->start();
            $until = microtime(true) + 10;
            $ready = false;
            do {
                $connection = @stream_socket_client('tcp://127.0.0.1:'.$port, $error, $message, 0.1);
                if ($connection) {
                    fclose($connection);
                    $ready = true;
                    break;
                }
                usleep(10000);
            } while ($server->isRunning() && microtime(true) < $until);
            $this->assertTrue($ready, $server->getErrorOutput().$server->getOutput());
            Http::preventStrayRequests();
            Http::allowStrayRequests([$origin.'/api/v1/integrations/claims']);
            $this->assertSame(1, $engine->tick());
            $this->assertDatabaseCount('claims', 0);
            $this->artisan('queue:work', ['connection' => 'database', '--queue' => 'simulations', '--once' => true, '--no-interaction' => true])->assertSuccessful();
            $item = $run->items()->first();
            $payload = $item->payload;
            $this->assertSame('retry', $item->status);
            $this->assertDatabaseCount('claims', 1);
            // Recreate the service as after a worker restart; persisted state drives the retry.
            $this->travel(2)->seconds();
            $this->app->forgetInstance(SimulationEngine::class);
            app(SimulationEngine::class)->tick();
            $this->artisan('queue:work', ['connection' => 'database', '--queue' => 'simulations', '--once' => true, '--no-interaction' => true])->assertSuccessful();
            $item->refresh();
            $this->assertSame('duplicate', $item->status);
            $this->assertSame($payload, $item->payload);
            $this->assertSame(2, $item->attempts);
            $this->assertDatabaseCount('claims', 1);
            $this->assertDatabaseCount('clients', 1);
            $this->assertDatabaseCount('integrated_claims', 1);
            $this->assertDatabaseCount('jobs', 0);
            $this->assertSame('Pendiente', Claim::findOrFail($item->claim_id)->status);
            $this->assertSame(['simulated_lost_response', 'duplicate'], DB::table('simulation_event_attempts')->orderBy('number')->pluck('outcome')->all());
        } finally {
            $server?->stop(2);
            $this->travelBack();
            DB::purge('sqlite');
            config(['database.default' => $original, 'database.connections.sqlite.database' => $originalDatabase]);
            unlink($database);
        }
    }
}
