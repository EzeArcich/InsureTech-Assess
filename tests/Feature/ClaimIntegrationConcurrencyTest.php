<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Services\IssueCompanyIntegrationCredential;
use App\Services\ReceiveIntegrationClaim;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Tests\TestCase;

class ClaimIntegrationConcurrencyTest extends TestCase
{
    private function race(bool $conflict): array
    {
        if (getenv('INTEGRATION_CONCURRENCY_TESTS') !== 'true' || DB::getDriverName() !== 'mysql') {
            $this->markTestSkipped('Opt-in: INTEGRATION_CONCURRENCY_TESTS=true, MySQL de pruebas y permiso CREATE DATABASE.');
        }
        $this->assertTrue(function_exists('pcntl_fork'));
        $original = config('database.connections.mysql.database');
        $database = 'insuretech_'.bin2hex(random_bytes(6)).'_testing';
        $directory = sys_get_temp_dir().'/'.$database;
        mkdir($directory, 0700);
        DB::statement('CREATE DATABASE '.$database);
        config(['database.connections.mysql.database' => $database]);
        DB::purge('mysql');
        try {
            $this->artisan('migrate', ['--force' => true, '--no-interaction' => true])->assertSuccessful();
            $company = Company::factory()->create();
            $credentials = [app(IssueCompanyIntegrationCredential::class)->handle($company, 'first')['credential'], app(IssueCompanyIntegrationCredential::class)->handle($company, 'second')['credential']];
            $payload = ['external_id' => 'concurrent-1', 'claim_number' => 'SIM-CONCURRENT', 'client_name' => 'SIMULADO Asegurado'];
            DB::disconnect();
            $pids = [];
            for ($i = 0; $i < 2; $i++) {
                $pid = pcntl_fork();
                $this->assertNotSame(-1, $pid);
                if ($pid === 0) {
                    DB::purge();
                    DB::statement('SET SESSION innodb_lock_wait_timeout=10');
                    try {
                        $result = DB::transaction(function () use ($i, $company, $credentials, $payload, $directory, $conflict): array {
                            if ($i === 0) {
                                Company::whereKey($company->id)->lockForUpdate()->firstOrFail();
                                file_put_contents($directory.'/locked', '1');
                                $until = microtime(true) + 10;
                                while (! file_exists($directory.'/release') && microtime(true) < $until) {
                                    usleep(10000);
                                }
                            }
                            $input = $payload;
                            if ($conflict && $i === 1) {
                                $input['claim_number'] = 'SIM-DIFFERENT';
                            }

                            return app(ReceiveIntegrationClaim::class)->handle($credentials[$i], $input);
                        });
                        $output = ['status' => $result['replayed'] ? 200 : 201, 'id' => $result['data']['id']];
                    } catch (\Throwable $e) {
                        $output = ['status' => $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500, 'error' => get_class($e)];
                    }
                    file_put_contents($directory.'/result-'.$i, json_encode($output));
                    DB::disconnect();
                    exit(0);
                }
                $pids[] = $pid;
                if ($i === 0) {
                    $until = microtime(true) + 5;
                    while (! file_exists($directory.'/locked') && microtime(true) < $until) {
                        usleep(10000);
                    }
                    $this->assertFileExists($directory.'/locked');
                }
            }
            $blocked = false;
            $until = microtime(true) + 5;
            do {
                $blocked = DB::selectOne('SELECT COUNT(*) AS total FROM performance_schema.data_lock_waits')->total > 0;
                if (! $blocked) {
                    usleep(10000);
                }
            } while (! $blocked && microtime(true) < $until);
            file_put_contents($directory.'/release', '1');
            foreach ($pids as $pid) {
                pcntl_waitpid($pid, $status);
                $this->assertSame(0, pcntl_wexitstatus($status));
            }
            $this->assertTrue($blocked, 'The second MySQL connection must actually wait for the company lock.');
            $results = array_map(fn (int $i): array => json_decode(file_get_contents($directory.'/result-'.$i), true), [0, 1]);
            $this->assertDatabaseCount('claims', 1);
            $this->assertDatabaseCount('clients', 1);
            $this->assertDatabaseCount('integrated_claims', 1);

            return $results;
        } finally {
            DB::purge('mysql');
            config(['database.connections.mysql.database' => $original]);
            DB::statement('DROP DATABASE '.$database);
            foreach (glob($directory.'/*') as $file) {
                unlink($file);
            }
            rmdir($directory);
        }
    }

    public function test_concurrent_equivalent_requests_create_one_claim_across_two_credentials(): void
    {
        $results = $this->race(false);
        $this->assertSame([201, 200], array_column($results, 'status'), json_encode($results));
        $this->assertSame($results[0]['id'], $results[1]['id']);
    }

    public function test_concurrent_incompatible_payload_returns_conflict_without_extra_client(): void
    {
        $results = $this->race(true);
        $this->assertSame([201, 409], array_column($results, 'status'), json_encode($results));
    }
}
