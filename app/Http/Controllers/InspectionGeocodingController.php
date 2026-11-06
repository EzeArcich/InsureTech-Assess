<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Services\GeocodeInspectionAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class InspectionGeocodingController extends Controller
{
    public function __invoke(Request $request, Claim $claim, GeocodeInspectionAddress $geocoder): JsonResponse
    {
        $isCoordination = $request->routeIs('claims.location.search');
        abort_unless($isCoordination ? $request->user()->canCoordinateInspections()
            : ($request->user()->canPlanInspectionRoutes() || $request->user()->isRestrictedInspector()), 403);
        $claim = Claim::visibleTo($request->user())->findOrFail($claim->id);
        if ($isCoordination) {
            $mode = $request->validate(['inspection_mode' => ['required', 'string', 'max:191']]);
            $claim->inspection_mode = $mode['inspection_mode'];
        }
        if ($claim->inspectionModality() !== 'in_person') {
            throw ValidationException::withMessages(['address' => 'Las inspecciones remotas no llevan ubicación en el mapa.']);
        }
        $data = $request->validate(['address' => ['required', 'string', 'min:5', 'max:255']]);

        return response()->json(['results' => $geocoder->search($data['address'])]);
    }
}
