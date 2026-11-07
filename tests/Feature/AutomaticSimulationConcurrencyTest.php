<?php

namespace Tests\Feature;

use App\Models\ClaimSimulationRun;
use App\Models\Company;
use App\Models\SimulationProfile;
use App\Models\User;
use App\Services\IssueCompanyIntegrationCredential;
use App\Services\SimulationEngine;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AutomaticSimulationConcurrencyTest extends TestCase
{
    public function test_two_mysql_planners_block_on_the_same_run_and_enqueue_one_event(): void
    {
        if (getenv('INTEGRATION_CONCURRENCY_TESTS') !== 'true' || DB::getDriverName() !== 'mysql') {
            $this->markTestSkipped('Opt-in: INTEGRATION_CONCURRENCY_TESTS=true; MySQL con CREATE DATABASE y pcntl.');
        }
        $original = config('database.connections.mysql.database');
        $database = 'insuretech_auto_'.bin2hex(random_bytes(6)).'_testing';
        $directory = sys_get_temp_dir().'/'.$database;
        mkdir($directory, 0700);
        DB::statement('CREATE DATABASE '.$database);
        config(['database.connections.mysql.database' => $database, 'claim_integrations.simulator_enabled' => true]);
        DB::purge('mysql');
        $pids = [];
        try {
            $this->artisan('migrate', ['--force' => true, '--no-interaction' => true])->assertSuccessful();
            $issued = app(IssueCompanyIntegrationCredential::class)->handle(Company::factory()->create(), 'concurrent laboratory', true);
            $profile = SimulationProfile::factory()->create(['credential_id' => $issued['credential']->id]);
            $run = app(SimulationEngine::class)->start($profile, User::factory()->create());
            DB::disconnect();
            for ($i = 0; $i < 2; $i++) {
                $pid = pcntl_fork();
                $this->assertNotSame(-1, $pid);
                if ($pid === 0) {
                    DB::purge();
                    try {
                        $count = DB::transaction(function () use ($i, $run, $directory): int {
                            if ($i === 0) {
                                ClaimSimulationRun::whereKey($run->id)->lockForUpdate()->firstOrFail();
                                file_put_contents($directory.'/locked', '1');
                                $until = microtime(true) + 10;
                                while (! file_exists($directory.'/release') && microtime(true) < $until) {
                                    usleep(10000);
                                }
                            }

                            return app(SimulationEngine::class)->tick();
                        });
                        file_put_contents($directory.'/result-'.$i, (string) $count);
                    } catch (\Throwable $exception) {
                        file_put_contents($directory.'/result-'.$i, get_class($exception));
                    }
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
            $pids = [];
            $this->assertTrue($blocked, 'Se debe observar espera real por el bloqueo de la ejecución.');
            $this->assertSame('1', file_get_contents($directory.'/result-0'));
            $this->assertSame('0', file_get_contents($directory.'/result-1'));
            $this->assertDatabaseCount('claim_simulation_items', 1);
            $this->assertDatabaseCount('jobs', 1);
            $this->assertDatabaseCount('claims', 0);
        } finally {
            file_put_contents($directory.'/release', '1');
            foreach ($pids as $pid) {
                pcntl_waitpid($pid, $status);
            }
            DB::purge('mysql');
            config(['database.connections.mysql.database' => $original]);
            DB::statement('DROP DATABASE '.$database);
            foreach (glob($directory.'/*') as $file) {
                unlink($file);
            }
            rmdir($directory);
        }
    }
}
