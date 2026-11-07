<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Schema;

class SimulationRuntime
{
    public function __construct(private SimulationTransport $transport) {}

    /** @return array{supported:bool,ready:bool,services:array<string,bool>} */
    public function status(): array
    {
        $this->transport->ensureEnabled();
        $external = $this->externalProcesses();

        return [
            'supported' => $this->supported(),
            'ready' => Schema::hasTable('jobs') && Schema::hasTable('simulation_profiles'),
            'services' => [
                'queue' => $this->locked('queue') || $external['queue'],
                'scheduler' => $this->locked('scheduler') || $external['scheduler'],
                'receiver' => $this->receiverListening(),
            ],
        ];
    }

    /** @return list<string> */
    public function start(): array
    {
        $state = $this->status();
        abort_unless($state['supported'], 422, 'El inicio desde el panel requiere Linux, PHP CLI, proc_open, nohup y flock disponibles.');
        abort_unless($state['ready'], 422, 'Primero deben aplicarse las migraciones pendientes del motor y la cola.');
        File::ensureDirectoryExists($this->directory(), 0775);
        abort_unless(is_writable($this->directory()), 422, 'PHP necesita permiso de escritura en storage/app/simulation-runtime.');
        $requested = [];
        foreach ($this->commands() as $name => $arguments) {
            if ($state['services'][$name] || $this->locked($name)) {
                continue;
            }
            $log = $this->directory().'/'.$name.'.log';
            $handle = @fopen($log, 'ab');
            abort_unless($handle !== false, 422, 'No se pudo abrir el log del proceso del laboratorio.');
            fclose($handle);
            $command = implode(' ', array_map('escapeshellarg', ['/usr/bin/nohup', '/usr/bin/flock', '--nonblock', $this->lockPath($name),
                (string) config('claim_integrations.runtime.php_binary'), base_path('artisan'), ...$arguments]));
            // Detach all three standard streams so the process outlives the HTTP request.
            $result = Process::path(base_path())->timeout(5)->run($command.' >> '.escapeshellarg($log).' 2>&1 < /dev/null &');
            abort_unless($result->successful(), 422, 'No se pudo solicitar el inicio del proceso; revisá su log en storage/app/simulation-runtime.');
            $requested[] = $name;
        }

        return $requested;
    }

    private function supported(): bool
    {
        return PHP_OS_FAMILY === 'Linux' && function_exists('proc_open')
            && is_executable('/usr/bin/nohup') && is_executable('/usr/bin/flock')
            && is_executable((string) config('claim_integrations.runtime.php_binary'));
    }

    private function directory(): string
    {
        return (string) config('claim_integrations.runtime.directory');
    }

    private function lockPath(string $name): string
    {
        return $this->directory().'/'.$name.'.lock';
    }

    private function locked(string $name): bool
    {
        if (! is_file($this->lockPath($name))) {
            return false;
        }
        $handle = @fopen($this->lockPath($name), 'r');
        if ($handle === false) {
            return false;
        }
        $available = flock($handle, LOCK_EX | LOCK_NB);
        if ($available) {
            flock($handle, LOCK_UN);
        }
        fclose($handle);

        return ! $available;
    }

    /** @return array{queue:bool,scheduler:bool} */
    private function externalProcesses(): array
    {
        $result = ['queue' => false, 'scheduler' => false];
        foreach (glob('/proc/[0-9]*', GLOB_ONLYDIR) ?: [] as $process) {
            // Only inspect arguments of processes running in this project, never other apps.
            $workingDirectory = @readlink($process.'/cwd');
            if ($workingDirectory !== realpath(base_path())) {
                continue;
            }
            $arguments = explode("\0", @file_get_contents($process.'/cmdline') ?: '');
            if (! preg_match('/^php(?:[0-9.]+)?$/', basename($arguments[0] ?? ''))) {
                continue;
            }
            $script = $arguments[1] ?? '';
            $scriptPath = str_starts_with($script, '/') ? $script : $workingDirectory.'/'.$script;
            if (realpath($scriptPath) !== realpath(base_path('artisan'))) {
                continue;
            }
            $command = $arguments[2] ?? '';
            if ($command === 'schedule:work') {
                $result['scheduler'] = true;
            }
            if ($command === 'queue:work') {
                $connection = isset($arguments[3]) && ! str_starts_with($arguments[3], '-') ? $arguments[3] : config('queue.default');
                if ($connection !== 'database') {
                    continue;
                }
                foreach ($arguments as $index => $argument) {
                    $queues = str_starts_with($argument, '--queue=') ? substr($argument, 8) : ($argument === '--queue' ? ($arguments[$index + 1] ?? '') : '');
                    if (in_array('simulations', explode(',', $queues), true)) {
                        $result['queue'] = true;
                    }
                }
            }
        }

        return $result;
    }

    private function receiverListening(): bool
    {
        $url = parse_url($this->transport->endpoint());
        $socket = @stream_socket_client('tcp://'.$url['host'].':'.($url['port'] ?? 80), $error, $message, 0.15);
        if ($socket === false) {
            return false;
        }
        fclose($socket);

        return true;
    }

    /** @return array<string,list<string>> */
    private function commands(): array
    {
        $url = parse_url($this->transport->endpoint());

        return [
            'receiver' => ['serve', '--host='.trim($url['host'], '[]'), '--port='.($url['port'] ?? 80), '--no-reload', '--no-interaction'],
            'queue' => ['queue:work', 'database', '--queue=simulations', '--sleep=1', '--tries=1', '--timeout=30', '--no-interaction'],
            'scheduler' => ['schedule:work', '--no-interaction'],
        ];
    }
}
