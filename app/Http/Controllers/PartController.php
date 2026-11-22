<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\CatalogCollisionPart;
use App\Models\Part;
use App\Services\SyncClaimCatalogParts;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PartController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $data = $request->validate(['q' => ['nullable', 'string', 'max:120']]);
        $parts = Part::where('name', 'like', '%'.($data['q'] ?? '').'%')->orderBy('name')->limit(20)->get();

        return response()->json(['results' => $parts->map(fn (Part $part): array => ['id' => $part->name, 'text' => $part->name])]);
    }

    public function searchCatalog(Request $request, Claim $claim): JsonResponse
    {
        abort_unless($request->user()->isRestrictedInspector() || $request->user()->canAny(['editar-siniestro', 'ceo-siniestro', 'peritos-siniestro']), 403);
        $claim = Claim::visibleTo($request->user())->with('vehicle')->findOrFail($claim->id);
        $data = $request->validate(['q' => ['required', 'string', 'min:3', 'max:120'], 'page' => ['sometimes', 'integer', 'min:1', 'max:1000']]);
        $query = trim($data['q']);
        if (mb_strlen($query) < 3 || ! $claim->vehicle?->catalog_configuration_code) {
            return response()->json(['results' => [], 'has_more' => false]);
        }

        $parts = CatalogCollisionPart::where('vehicle_configuration_code', $claim->vehicle->catalog_configuration_code)
            ->where(function ($builder) use ($query): void {
                $builder->where('name', 'like', '%'.$query.'%')->orWhere('category', 'like', '%'.$query.'%');
            })->orderBy('name')->orderBy('code')->simplePaginate(20, ['code', 'name', 'category', 'equipment_dependent']);

        return response()->json(['results' => $parts->items(), 'has_more' => $parts->hasMorePages()]);
    }

    public function updateCatalog(Request $request, Claim $claim, SyncClaimCatalogParts $syncParts): JsonResponse
    {
        abort_unless($request->user()->isRestrictedInspector() || $request->user()->canAny(['editar-siniestro', 'ceo-siniestro', 'peritos-siniestro']), 403);
        $data = $request->validate([
            'catalog_part_codes' => ['present', 'array', 'max:100'],
            'catalog_part_codes.*' => ['required', 'string', 'distinct', 'exists:catalog_collision_parts,code'],
        ]);
        DB::transaction(function () use ($claim, $request, $data, $syncParts): void {
            $claim = Claim::visibleTo($request->user())->lockForUpdate()->findOrFail($claim->id);
            $syncParts->handle($claim, $data['catalog_part_codes']);
        });

        return response()->json(['message' => 'Repuestos guardados correctamente.']);
    }

    public function show(Claim $claim): View
    {
        $claim = Claim::visibleTo(auth()->user())->findOrFail($claim->id);
        $claim->load('parts', 'catalogParts', 'workshop', 'vehicle');

        return view('siniestros.parts', compact('claim'));
    }
}
