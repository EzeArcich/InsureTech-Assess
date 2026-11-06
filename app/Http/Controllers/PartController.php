<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Part;
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

    public function show(Claim $claim): View
    {
        $claim = Claim::visibleTo(auth()->user())->findOrFail($claim->id);
        $claim->load('parts', 'workshop');

        return view('siniestros.parts', compact('claim'));
    }
}
