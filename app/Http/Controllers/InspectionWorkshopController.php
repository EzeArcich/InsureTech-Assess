<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveWorkshopRequest;
use App\Models\Claim;
use App\Models\Workshop;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class InspectionWorkshopController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $data = $request->validate(['q' => ['nullable', 'string', 'max:191'], 'name' => ['nullable', 'string', 'max:191'], 'address' => ['nullable', 'string', 'max:191']]);
        $terms = array_filter($data, fn ($term): bool => mb_strlen(trim($term ?? '')) >= 2);
        if ($terms === []) {
            return response()->json(['results' => []]);
        }
        $workshops = Workshop::where(function ($query) use ($terms): void {
            foreach ($terms as $field => $term) {
                if ($field === 'q') {
                    $query->orWhere('name', 'like', '%'.trim($term).'%')->orWhere('address', 'like', '%'.trim($term).'%');
                } else {
                    $query->orWhere($field, 'like', '%'.trim($term).'%');
                }
            }
        })->orderBy('name')->limit(10)->get(['id', 'name', 'address', 'city', 'phone', 'opening_hours', 'is_approved']);

        return response()->json(['results' => $workshops]);
    }

    public function store(SaveWorkshopRequest $request, Claim $claim): JsonResponse
    {
        Claim::visibleTo($request->user())->findOrFail($claim->id);
        abort_unless($request->user()->isRestrictedInspector() || $request->user()->canAny(['editar-siniestro', 'ceo-siniestro', 'peritos-siniestro']), 403);
        try {
            $workshop = Workshop::create($request->workshopAttributes());
        } catch (UniqueConstraintViolationException $exception) {
            throw ValidationException::withMessages(['name' => 'Ya existe un taller con ese nombre o domicilio. Buscalo y seleccionalo.']);
        }

        return response()->json(['workshop' => $workshop->only(['id', 'name', 'address', 'city', 'phone', 'opening_hours', 'is_approved'])], 201);
    }
}
