<?php

namespace App\Http\Controllers;

use App\Services\SimulationRuntime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class SimulationRuntimeController extends Controller
{
    public function show(SimulationRuntime $runtime): JsonResponse
    {
        Gate::authorize('simulate-claims');

        return response()->json($runtime->status())->header('Cache-Control', 'no-store');
    }

    public function store(SimulationRuntime $runtime): RedirectResponse
    {
        Gate::authorize('simulate-claims');
        $requested = $runtime->start();

        return redirect()->back()->with('success', $requested === [] ? 'El motor ya está encendido.' : 'Inicio solicitado. El estado de los procesos se actualiza en unos segundos.');
    }
}
