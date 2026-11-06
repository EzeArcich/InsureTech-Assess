<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GeocodeInspectionAddress
{
    /** @return array<int, array{label: string, latitude: float, longitude: float, precision: string}> */
    public function search(string $address): array
    {
        $address = preg_replace('/\s+/u', ' ', trim(str_replace('·', ',', $address)));
        $endpoint = config('services.inspection_map.geocoding_url');
        $country = config('services.inspection_map.country_code', 'ar');
        $key = 'inspection-geocode:'.hash('sha256', $endpoint.'|'.$country.'|'.mb_strtolower($address));
        if (($cached = Cache::get($key)) !== null) {
            return $cached;
        }
        $lock = Cache::lock('inspection-geocode:request-lock', 15);
        if (! $lock->get()) {
            throw new HttpException(429, 'Hay otra búsqueda de domicilio en curso. Intentá nuevamente en unos segundos.');
        }
        try {
            if (($cached = Cache::get($key)) !== null) {
                return $cached;
            }
            $lastRequest = Cache::get('inspection-geocode:last-request', 0);
            if (microtime(true) - $lastRequest < 1.1) {
                throw new HttpException(429, 'Esperá un segundo antes de buscar otro domicilio.');
            }
            Cache::put('inspection-geocode:last-request', microtime(true), 60);
            try {
                $response = Http::acceptJson()->withUserAgent('InsureTechAssess/1.0 (+'.config('app.url').')')
                    ->connectTimeout(3)->timeout(8)->withoutRedirecting()
                    ->get($endpoint, ['q' => $address, 'format' => 'jsonv2', 'addressdetails' => 1,
                        'countrycodes' => $country, 'limit' => 5, 'accept-language' => 'es']);
            } catch (ConnectionException $exception) {
                throw new HttpException(503, 'No se pudo conectar con el buscador de domicilios. Podés ubicar el punto manualmente.');
            }
            if (! $response->successful()) {
                throw new HttpException(503, 'El buscador de domicilios no está disponible. Intentá más tarde o ubicá el punto manualmente.');
            }
            $payload = $response->json();
            if (! is_array($payload) || ! array_is_list($payload)) {
                throw new HttpException(503, 'El buscador devolvió una respuesta inválida. Intentá más tarde.');
            }
            $results = [];
            foreach ($payload as $item) {
                if (! is_array($item) || ! is_numeric($item['lat'] ?? null) || ! is_numeric($item['lon'] ?? null)
                    || abs((float) $item['lat']) > 90 || abs((float) $item['lon']) > 180
                    || ! is_string($item['display_name'] ?? null)) {
                    continue;
                }
                $results[] = ['label' => $item['display_name'], 'latitude' => (float) $item['lat'], 'longitude' => (float) $item['lon'],
                    'precision' => isset($item['address']['house_number']) ? 'Domicilio con numeración' : 'Ubicación aproximada: revisá el punto'];
            }
            Cache::put($key, $results, $results === [] ? now()->addHours(6) : now()->addDays(30));

            return $results;
        } finally {
            $lock->release();
        }
    }
}
