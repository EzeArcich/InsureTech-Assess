<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateClaimSimulationRequest;
use App\Models\ClaimSimulationRun;
use App\Models\CompanyIntegrationCredential;
use App\Services\GenerateSimulatedClaims;
use App\Services\SendSimulatedClaims;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClaimSimulationController extends Controller
{
    public function index(): View
    {
        $credentials = CompanyIntegrationCredential::where('is_simulation', true)->where('active', true)->whereNull('revoked_at')->with('company')->get();
        $runs = ClaimSimulationRun::whereNull('profile_id')->with('credential.company')->withCount('items')->orderByDesc('id')->paginate(15);

        return view('integrations.simulator', compact('credentials', 'runs'));
    }

    public function store(CreateClaimSimulationRequest $request, GenerateSimulatedClaims $generate): RedirectResponse
    {
        $run = $generate->handle($request->user(), $request->validated());

        return redirect()->route('claim-simulations.show', $run);
    }

    public function show(ClaimSimulationRun $claimSimulation): View
    {
        $run = $claimSimulation->load(['items', 'credential.company']);

        return view('integrations.simulation', compact('run'));
    }

    public function send(Request $request, ClaimSimulationRun $claimSimulation, SendSimulatedClaims $send): RedirectResponse
    {
        $request->validate(['confirmation' => 'accepted']);
        $send->handle($claimSimulation);

        return redirect()->route('claim-simulations.show', $claimSimulation)->with('success', 'Envío finalizado. Revisá el resultado de cada solicitud.');
    }
}
