<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveSimulationProfileRequest;
use App\Models\ClaimSimulationItem;
use App\Models\ClaimSimulationRun;
use App\Models\CompanyIntegrationCredential;
use App\Models\SimulationProfile;
use App\Services\SimulationEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AutomaticSimulationController extends Controller
{
    public function index(Request $request): View
    {
        $companyId = $request->integer('company_id');
        $credentials = $this->credentials();
        $profiles = SimulationProfile::with('credential.company')->when($companyId, fn ($query) => $query->whereHas('credential', fn ($credential) => $credential->where('company_id', $companyId)))->orderByDesc('id')->paginate(10, ['*'], 'profiles_page')->withQueryString();
        $runs = $this->runs($companyId)->with(['credential.company', 'profile'])->withCount('items')->orderByDesc('id')->paginate(15, ['*'], 'runs_page')->withQueryString();
        $metrics = $this->metricsData($companyId);

        return view('integrations.automatic', compact('credentials', 'profiles', 'runs', 'metrics', 'companyId'));
    }

    public function create(): View
    {
        $profile = new SimulationProfile(['enabled' => true, 'configuration' => ['scenario' => 'normal', 'rate_per_hour' => 12, 'duration_minutes' => 60,
            'max_per_hour' => 30, 'max_pending' => 10, 'max_concurrency' => 1, 'daily_limit' => 100, 'seed' => 42,
            'reasons' => ['Todo riesgo' => 100], 'distribution' => 'exponential', 'peak_multiplier' => 2, 'peak_minutes' => 15, 'fault_percent' => 0, 'fault_type' => 'lost_response']]);

        return view('integrations.profile', ['profile' => $profile, 'credentials' => $this->credentials()]);
    }

    public function store(SaveSimulationProfileRequest $request): RedirectResponse
    {
        SimulationProfile::create($request->validated());

        return redirect()->route('claim-simulations.automatic.index')->with('success', 'Perfil creado. Todavía no se envían siniestros.');
    }

    public function edit(SimulationProfile $profile): View
    {
        return view('integrations.profile', ['profile' => $profile, 'credentials' => $this->credentials()]);
    }

    public function update(SaveSimulationProfileRequest $request, SimulationProfile $profile): RedirectResponse
    {
        DB::transaction(function () use ($profile, $request): void {
            SimulationProfile::whereKey($profile->id)->lockForUpdate()->firstOrFail()->update($request->validated());
        }, 5);

        return redirect()->route('claim-simulations.automatic.index')->with('success', 'Perfil actualizado. Las ejecuciones anteriores conservan su configuración.');
    }

    public function duplicate(SimulationProfile $profile): RedirectResponse
    {
        SimulationProfile::create(['credential_id' => $profile->credential_id, 'name' => mb_substr($profile->name, 0, 180).' (copia)', 'configuration' => $profile->configuration, 'enabled' => false]);

        return redirect()->route('claim-simulations.automatic.index')->with('success', 'Copia creada deshabilitada.');
    }

    public function start(Request $request, SimulationProfile $profile, SimulationEngine $engine): RedirectResponse
    {
        $request->validate(['confirmation' => ['accepted']]);
        $run = $engine->start($profile, $request->user());

        return redirect()->route('claim-simulations.automatic.show', $run);
    }

    public function transition(Request $request, ClaimSimulationRun $run, SimulationEngine $engine): RedirectResponse
    {
        $data = $request->validate(['action' => ['required', 'in:pause,resume,stop']]);
        $engine->transition($run, $data['action']);

        return redirect()->route('claim-simulations.automatic.show', $run)->with('success', 'Estado de ejecución actualizado.');
    }

    public function show(ClaimSimulationRun $run): View
    {
        abort_unless($run->profile_id, 404);
        $run->load('credential.company', 'profile');
        $items = $run->items()->reorder()->orderByDesc('position')->paginate(25);
        $metrics = $this->metricsData(0, $run->id);

        $attempts = DB::table('simulation_event_attempts')->whereIn('item_id', $items->getCollection()->pluck('id'))->orderBy('number')->get()->groupBy('item_id');

        return view('integrations.automatic-run', compact('run', 'items', 'metrics', 'attempts'));
    }

    public function metrics(Request $request): JsonResponse
    {
        $companyId = $request->integer('company_id');
        $runId = $request->integer('run_id');
        $runs = $this->runs($companyId)->when($runId, fn ($query) => $query->whereKey($runId))
            ->orderByDesc('id')->limit(15)->get(['id', 'status', 'next_dispatch_at']);
        $states = $runs->map(fn (ClaimSimulationRun $run): array => ['id' => $run->id, 'status' => $run->status,
            'next_dispatch_at' => $run->next_dispatch_at?->format('d/m/Y H:i:s') ?? '—'])->all();

        return response()->json([...$this->metricsData($companyId, $runId ?: null), 'runs' => $states])->header('Cache-Control', 'no-store');
    }

    private function credentials(): \Illuminate\Database\Eloquent\Collection
    {
        return CompanyIntegrationCredential::where('is_simulation', true)->where('active', true)->whereNull('revoked_at')->with('company')->orderBy('id')->get();
    }

    private function runs(int $companyId): \Illuminate\Database\Eloquent\Builder
    {
        return ClaimSimulationRun::whereNotNull('profile_id')->when($companyId, fn ($query) => $query->whereHas('credential', fn ($credential) => $credential->where('company_id', $companyId)));
    }

    /** @return array<string,int|float> */
    private function metricsData(int $companyId, ?int $runId = null): array
    {
        $runs = $this->runs($companyId)->when($runId, fn ($query) => $query->whereKey($runId));
        $items = ClaimSimulationItem::whereIn('run_id', (clone $runs)->select('id'));
        $counts = (clone $items)->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');
        $attempts = DB::table('simulation_event_attempts')->whereIn('item_id', (clone $items)->select('id'));
        $sent = (clone $attempts)->count();
        $success = (int) ($counts['created'] ?? 0) + (int) ($counts['duplicate'] ?? 0);
        $failed = (int) ($counts['failed'] ?? 0) + (int) ($counts['rejected'] ?? 0);
        $resolved = $success + $failed;
        $first = (clone $attempts)->min('started_at');
        $hours = $first ? max(1 / 60, abs(now()->diffInSeconds(\Illuminate\Support\Carbon::parse($first))) / 3600) : 0;

        return ['active' => (clone $runs)->whereIn('status', ['pending', 'running'])->count(), 'sent' => $sent, 'success' => $success, 'errors' => $failed,
            'retries' => (clone $attempts)->where('number', '>', 1)->count(), 'pending' => (int) $items->whereIn('status', ['pending', 'queued', 'processing', 'retry'])->count(),
            'success_rate' => $resolved ? round(100 * $success / $resolved, 1) : 0, 'effective_rate' => $hours ? round($sent / $hours, 1) : 0];
    }
}
