<?php

namespace Tests\Feature;

use App\Models\Claim;
use App\Models\ClaimSimulationRun;
use App\Models\Company;
use App\Models\User;
use App\Services\IssueCompanyIntegrationCredential;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Symfony\Component\Process\Process;
use Tests\TestCase;

class ClaimIntegrationHttpTest extends TestCase
{
    public function test_web_simulator_to_actual_http_api_creates_visible_claim_and_retries_idempotently(): void
    {
        if (getenv('INTEGRATION_HTTP_TESTS') !== 'true') {
            $this->markTestSkipped('Opt-in: INTEGRATION_HTTP_TESTS=true para iniciar servidor HTTP local y base SQLite temporal.');
        }
        $this->withoutVite();
        $database = tempnam(sys_get_temp_dir(), 'insuretech_http_');
        $oldDefault = config('database.default');
        $oldDatabase = config('database.connections.sqlite.database');
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => $database, 'claim_integrations.simulator_enabled' => true]);
        DB::purge('sqlite');
        $server = null;
        try {
            $this->artisan('migrate', ['--force' => true, '--no-interaction' => true])->assertSuccessful();
            $user = User::factory()->create();
            $user->assignRole(Role::findOrCreate('CEO', 'web'));
            $issued = app(IssueCompanyIntegrationCredential::class)->handle(Company::factory()->create(), 'HTTP laboratory', true);
            $socket = stream_socket_server('tcp://127.0.0.1:0');
            $address = stream_socket_get_name($socket, false);
            fclose($socket);
            $port = (int) substr(strrchr($address, ':'), 1);
            $origin = 'http://127.0.0.1:'.$port;
            config(['claim_integrations.simulator_origin' => $origin]);
            $server = new Process([PHP_BINARY, 'artisan', 'serve', '--host=127.0.0.1', '--port='.$port, '--no-reload', '--no-interaction'], base_path(), [
                'APP_ENV' => 'testing', 'APP_KEY' => config('app.key'), 'APP_DEBUG' => 'false',
                'DB_CONNECTION' => 'sqlite', 'DB_DATABASE' => $database,
                'DB_URL' => false, 'DATABASE_URL' => false,
                'CACHE_DRIVER' => 'array', 'SESSION_DRIVER' => 'array', 'QUEUE_CONNECTION' => 'sync', 'MAIL_MAILER' => 'array',
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
            $this->actingAs($user)->post('/claim-simulations', ['credential_id' => $issued['credential']->id, 'reason' => 'Todo riesgo', 'quantity' => 2, 'seed' => 42])->assertRedirect();
            $run = ClaimSimulationRun::firstOrFail();
            $this->post('/claim-simulations/'.$run->id.'/send', ['confirmation' => '1'])->assertRedirect();
            $this->assertSame(['created', 'created'], $run->fresh()->items->pluck('status')->all(), json_encode($run->fresh()->items->pluck('response')));
            $this->assertDatabaseCount('claims', 2);
            $this->assertDatabaseCount('clients', 2);
            $item = $run->fresh()->items->first();
            $claim = Claim::findOrFail(data_get($item->response, 'data.id'));
            $this->get('/claims/'.$claim->id.'/edit')->assertOk()->assertSee($claim->claim_number);
            $this->get('/claim-simulations/'.$run->id)->assertOk()->assertSee('Abrir expediente')->assertDontSee($issued['token']);
            $retry = Http::withToken($issued['token'])->acceptJson()->timeout(5)->post($origin.'/api/v1/integrations/claims', $item->payload);
            $this->assertSame(200, $retry->status());
            $this->assertTrue($retry->json('meta.replayed'));
            $this->assertSame($claim->id, $retry->json('data.id'));
            $this->assertDatabaseCount('claims', 2);
        } finally {
            $server?->stop(2);
            DB::purge('sqlite');
            config(['database.default' => $oldDefault, 'database.connections.sqlite.database' => $oldDatabase]);
            unlink($database);
        }
    }
}
