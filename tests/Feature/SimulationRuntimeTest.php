<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\SimulationRuntime;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Process\PendingProcess;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Symfony\Component\Process\Process as NativeProcess;
use Tests\TestCase;

class SimulationRuntimeTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('CEO', 'web'));
        config(['claim_integrations.simulator_enabled' => true, 'claim_integrations.simulator_origin' => 'http://127.0.0.1:65534']);

        return $user;
    }

    public function test_runtime_requires_authentication_admin_permission_and_local_laboratory(): void
    {
        $admin = $this->admin();
        Process::fake();
        $this->post('/claim-simulations/runtime')->assertRedirect('/login');
        $this->actingAs(User::factory()->create())->post('/claim-simulations/runtime')->assertForbidden();
        $this->getJson('/claim-simulations/runtime')->assertForbidden();
        config(['claim_integrations.simulator_enabled' => false]);
        $this->actingAs($admin)->post('/claim-simulations/runtime')->assertNotFound();
        config(['claim_integrations.simulator_enabled' => true]);
        $this->app->detectEnvironment(fn (): string => 'production');
        $this->withSession(['_token' => 'runtime-test'])->post('/claim-simulations/runtime', ['_token' => 'runtime-test'])->assertNotFound();
        Process::assertNothingRan();
    }

    public function test_start_uses_only_fixed_detached_commands_and_reports_status_without_running_commands_on_get(): void
    {
        $admin = $this->admin();
        $directory = sys_get_temp_dir().'/insuretech-runtime-'.Str::uuid();
        config(['claim_integrations.runtime.directory' => $directory]);
        Process::fake();
        try {
            $this->actingAs($admin)->getJson('/claim-simulations/runtime')->assertOk()->assertJsonPath('ready', true)->assertHeader('Cache-Control', 'no-store, private');
            Process::assertNothingRan();
            $this->from('/claim-simulations')->post('/claim-simulations/runtime', ['command' => 'touch /tmp/not-allowed', 'port' => 9999])->assertRedirect('/claim-simulations')->assertSessionHas('success');
            Process::assertRan(fn (PendingProcess $process): bool => str_contains($process->command, "'queue:work' 'database' '--queue=simulations'")
                && str_contains($process->command, "'/usr/bin/flock' '--nonblock'") && str_contains($process->command, '2>&1 < /dev/null &')
                && ! str_contains($process->command, 'not-allowed') && ! str_contains($process->command, '--max-time') && $process->path === base_path());
            Process::assertRan(fn (PendingProcess $process): bool => str_contains($process->command, "'schedule:work'"));
            Process::assertRan(fn (PendingProcess $process): bool => str_contains($process->command, "'serve' '--host=127.0.0.1' '--port=65534'"));
            $this->assertDatabaseCount('claims', 0);
        } finally {
            File::deleteDirectory($directory);
        }
    }

    public function test_missing_php_binary_prevents_launch_with_a_clear_error(): void
    {
        $admin = $this->admin();
        config(['claim_integrations.runtime.php_binary' => '/missing/cli/php']);
        Process::fake();
        $this->actingAs($admin)->post('/claim-simulations/runtime')->assertUnprocessable();
        Process::assertNothingRan();
    }

    public function test_detached_processes_survive_launch_and_repeated_start_does_not_duplicate_them(): void
    {
        if (PHP_OS_FAMILY !== 'Linux' || ! is_executable('/usr/bin/flock') || ! function_exists('posix_kill')) {
            $this->markTestSkipped('Necesita Linux, flock y POSIX.');
        }
        $this->admin();
        $originalBase = base_path();
        $directory = sys_get_temp_dir().'/insuretech-runtime-native-'.Str::uuid();
        File::ensureDirectoryExists($directory);
        file_put_contents($directory.'/artisan', <<<'STUB'
<?php
$name = ['queue:work' => 'queue', 'schedule:work' => 'scheduler', 'serve' => 'receiver'][$argv[1]];
file_put_contents(__DIR__.'/'.$name.'.started', getmypid()."\n", FILE_APPEND);
usleep(10000000);
STUB);
        config(['claim_integrations.runtime.directory' => $directory.'/runtime', 'claim_integrations.runtime.php_binary' => PHP_BINARY]);
        $this->app->setBasePath($directory);
        try {
            $runtime = app(SimulationRuntime::class);
            $this->assertSame(['receiver', 'queue', 'scheduler'], $runtime->start());
            $until = microtime(true) + 3;
            do {
                $ready = count(glob($directory.'/*.started')) === 3;
                if (! $ready) {
                    usleep(10000);
                }
            } while (! $ready && microtime(true) < $until);
            $this->assertTrue($ready, 'Los tres procesos independientes deben haber iniciado.');
            $this->assertTrue($runtime->status()['services']['queue']);
            $this->assertTrue($runtime->status()['services']['scheduler']);
            $this->assertSame([], $runtime->start());
            foreach (glob($directory.'/*.started') as $file) {
                $this->assertCount(1, file($file, FILE_IGNORE_NEW_LINES));
            }
        } finally {
            foreach (glob($directory.'/*.started') as $file) {
                foreach (file($file, FILE_IGNORE_NEW_LINES) as $pid) {
                    posix_kill((int) $pid, SIGTERM);
                }
            }
            $this->app->setBasePath($originalBase);
            File::deleteDirectory($directory);
        }
    }

    public function test_console_processes_with_relative_artisan_are_detected_from_the_web_public_directory(): void
    {
        $this->admin();
        $originalBase = base_path();
        $originalDirectory = getcwd();
        $directory = sys_get_temp_dir().'/insuretech-runtime-console-'.Str::uuid();
        File::ensureDirectoryExists($directory);
        file_put_contents($directory.'/artisan', '<?php usleep(10000000);');
        $scheduler = new NativeProcess([PHP_BINARY, 'artisan', 'schedule:work'], $directory);
        $worker = new NativeProcess([PHP_BINARY, 'artisan', 'queue:work', 'database', '--queue=simulations'], $directory);
        config(['claim_integrations.runtime.directory' => $directory.'/unused-locks']);
        try {
            $scheduler->start();
            $worker->start();
            chdir(public_path());
            $this->app->setBasePath($directory);
            $runtime = app(SimulationRuntime::class);
            $until = microtime(true) + 2;
            do {
                $services = $runtime->status()['services'];
                if ($services['queue'] && $services['scheduler']) {
                    break;
                }
                usleep(10000);
            } while (microtime(true) < $until);
            $this->assertTrue($services['scheduler'], 'Debe reconocer schedule:work iniciado con artisan relativo.');
            $this->assertTrue($services['queue'], 'Debe reconocer queue:work iniciado con artisan relativo.');
        } finally {
            $scheduler->stop(1);
            $worker->stop(1);
            chdir($originalDirectory);
            $this->app->setBasePath($originalBase);
            File::deleteDirectory($directory);
        }
    }
}
