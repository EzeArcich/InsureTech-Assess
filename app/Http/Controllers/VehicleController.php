<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:crear-siniestro|editar-siniestro|ceo-siniestro');
    }

    public function index(): JsonResponse
    {
        return response()->json(Vehicle::orderBy('brand')->orderBy('model')->paginate(50));
    }

    public function store(Request $request): JsonResponse
    {
        return response()->json(Vehicle::create($this->validateVehicle($request)), 201);
    }

    public function show(Vehicle $vehicle): JsonResponse
    {
        return response()->json($vehicle);
    }

    public function update(Request $request, Vehicle $vehicle): JsonResponse
    {
        if ($vehicle->catalog_configuration_code) {
            return response()->json(['message' => 'Las configuraciones del catálogo no se pueden modificar.'], 409);
        }
        $vehicle->update($this->validateVehicle($request));

        return response()->json($vehicle);
    }

    public function destroy(Vehicle $vehicle): JsonResponse
    {
        if ($vehicle->claims()->exists()) {
            return response()->json(['message' => 'El vehículo está asociado a siniestros.'], 409);
        }
        $vehicle->delete();

        return response()->json(null, 204);
    }

    /** @return array<string, mixed> */
    private function validateVehicle(Request $request): array
    {
        return $request->validate([
            'brand' => ['required', 'string', 'max:191'],
            'model' => ['required', 'string', 'max:191'],
            'year' => ['required', 'integer', 'min:1886', 'max:'.(now()->year + 1)],
            'version' => ['nullable', 'string', 'max:191'],
            'doors' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);
    }
}
