<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class NearbyInspectionController extends Controller
{
    public function __invoke(Request $request, Claim $claim): JsonResponse
    {
        abort_unless($request->user()->canCoordinateInspections(), 403);
        Claim::visibleTo($request->user())->findOrFail($claim->id);
        $data = $request->validate([
            'city' => ['required', 'string', 'min:3', 'max:191'],
            'latitude' => ['nullable', 'required_with:longitude', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'required_with:latitude', 'numeric', 'between:-180,180'],
            'date' => ['nullable', 'date_format:Y-m-d'],
        ]);
        $from = now()->startOfDay();
        $to = $from->copy()->addDays(60);
        if (! empty($data['date'])) {
            $date = Carbon::parse($data['date']);
            $from = $date->copy()->subDays(14)->max($from);
            $to = $date->copy()->addDays(14);
        }
        $city = $this->normalizeCity($data['city']);
        $hasCoordinates = isset($data['latitude'], $data['longitude']);
        $query = Claim::without(['company', 'client'])->with('client:id,city')
            ->whereKeyNot($claim->id)->whereIn('status', ['Coordinado', 'Peritando'])
            ->whereRaw('LOWER(TRIM(inspection_mode)) IN (?, ?)', ['presencial', 'in_person'])
            ->whereBetween('inspection_date', [$from->toDateString(), $to->toDateString()]);
        $matches = [];
        $query->chunkById(200, function ($claims) use (&$matches, $city, $hasCoordinates, $data): void {
            foreach ($claims as $candidate) {
                $distance = null;
                if ($hasCoordinates && $candidate->inspection_latitude !== null && $candidate->inspection_longitude !== null) {
                    $distance = $this->distance((float) $data['latitude'], (float) $data['longitude'], (float) $candidate->inspection_latitude, (float) $candidate->inspection_longitude);
                    if ($distance > 10) {
                        continue;
                    }
                } else {
                    $otherCity = $this->normalizeCity($candidate->city ?? '');
                    if ($otherCity === '' || (! str_contains(' '.$city.' ', ' '.$otherCity.' ') && ! str_contains(' '.$otherCity.' ', ' '.$city.' '))) {
                        continue;
                    }
                }
                $matches[] = ['number' => $candidate->claim_number, 'date' => $candidate->inspection_date->toDateString(),
                    'inspector' => $candidate->inspector?->name, 'city' => $candidate->city,
                    'distance_km' => $distance === null ? null : round($distance, 1)];
            }
        });
        $matches = collect($matches)->sortBy([['date', 'asc'], ['distance_km', 'asc']])->values();

        return response()->json(['results' => $matches->take(10)->values(), 'total' => $matches->count()]);
    }

    private function normalizeCity(string $city): string
    {
        return trim(preg_replace('/[^a-z0-9]+/', ' ', strtolower(Str::ascii($city))));
    }

    private function distance(float $latitude, float $longitude, float $otherLatitude, float $otherLongitude): float
    {
        $value = sin(deg2rad($otherLatitude - $latitude) / 2) ** 2
            + cos(deg2rad($latitude)) * cos(deg2rad($otherLatitude)) * sin(deg2rad($otherLongitude - $longitude) / 2) ** 2;

        return 6371 * 2 * asin(sqrt(min(1, max(0, $value))));
    }
}
