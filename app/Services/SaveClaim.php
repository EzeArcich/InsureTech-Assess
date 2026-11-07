<?php

namespace App\Services;

use App\Models\Claim;
use App\Models\Client;
use App\Models\Producer;
use App\Models\Part;
use App\Models\Workshop;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class SaveClaim
{
    public function __construct(private CalculateClaimEstimate $estimates) {}

    /** @param array<string, mixed> $attributes */
    public function handle(array $attributes, ?Claim $claim = null): Claim
    {
        return DB::transaction(function () use ($attributes, $claim): Claim {
            $claim ??= new Claim;
            if ($claim->exists) {
                $claim = Claim::query()->when(auth()->user(), fn ($query, $user) => $query->visibleTo($user))->lockForUpdate()->findOrFail($claim->id);
            }
            if (($attributes['status'] ?? null) === 'Resuelto') {
                throw \Illuminate\Validation\ValidationException::withMessages(['status' => 'Usá el módulo de honorarios para confirmar el cierre del siniestro.']);
            }
            if ($claim->exists && $claim->inspectionPayment()->exists()) {
                foreach (['company_id', 'inspector_id', 'inspection_mode', 'status', 'closed_at'] as $field) {
                    if (array_key_exists($field, $attributes) && (string) $attributes[$field] !== (string) $claim->getRawOriginal($field)) {
                        throw \Illuminate\Validation\ValidationException::withMessages([$field => 'Este siniestro ya fue resuelto y tiene honorarios registrados.']);
                    }
                }
            }
            if ($claim->exists && isset($attributes['company_id']) && (int) $attributes['company_id'] !== (int) $claim->company_id
                && $claim->integrationReceipt()->exists()) {
                throw \Illuminate\Validation\ValidationException::withMessages(['company_id' => 'Un siniestro recibido por integración conserva su compañía emisora.']);
            }
            $locationChanged = false;
            foreach (['address', 'city', 'workshop_id'] as $field) {
                if (array_key_exists($field, $attributes) && trim((string) $attributes[$field]) !== trim((string) $claim->$field)) {
                    $locationChanged = true;
                }
            }
            $hasCoordinates = isset($attributes['inspection_latitude'], $attributes['inspection_longitude']);
            if ($locationChanged || array_key_exists('inspection_latitude', $attributes) || array_key_exists('inspection_longitude', $attributes)) {
                $claim->forceFill(['inspection_latitude' => null, 'inspection_longitude' => null, 'inspection_address' => null]);
            }
            if ($hasCoordinates) {
                $claim->forceFill(Arr::only($attributes, ['inspection_address', 'inspection_latitude', 'inspection_longitude']));
            }
            $effectiveClaim = clone $claim;
            $effectiveClaim->inspection_mode = $attributes['inspection_mode'] ?? $claim->inspection_mode;
            if ($effectiveClaim->inspectionModality() !== 'in_person') {
                $claim->forceFill(['inspection_latitude' => null, 'inspection_longitude' => null, 'inspection_address' => null]);
            }
            $isCoordinating = ($attributes['status'] ?? $claim->status) === 'Coordinado'
                && (array_key_exists('status', $attributes) || $locationChanged);
            if ($isCoordinating && $effectiveClaim->inspectionModality() === 'in_person'
                && ($claim->inspection_latitude === null || $claim->inspection_longitude === null)) {
                throw \Illuminate\Validation\ValidationException::withMessages(['inspection_latitude' => 'Buscá y confirmá la ubicación de la pericia presencial antes de marcarla como Coordinado.']);
            }
            $quote = null;
            if (array_intersect(array_keys($attributes), ['bodywork_unit', 'bodywork_units', 'mechanical_unit', 'mechanical_units', 'paint_panels']) !== []
                || (isset($attributes['company_id']) && (int) $attributes['company_id'] !== (int) $claim->company_id)) {
                $quote = $this->estimates->calculate($claim, $attributes);
            }
            if (array_key_exists('workshop_id', $attributes)) {
                $attributes['workshop_name'] = $attributes['workshop_id'] ? Workshop::findOrFail($attributes['workshop_id'])->name : null;
            }
            $companyId = $attributes['company_id'] ?? $claim->company_id;
            $client = $claim->client ?? new Client;
            $clientAttributes = Arr::only($attributes, ['phone', 'address', 'city']);
            if (array_key_exists('client_email', $attributes)) {
                $clientAttributes['email'] = $attributes['client_email'];
            }
            if (array_key_exists('client_name', $attributes)) {
                $clientAttributes['name'] = $attributes['client_name'];
            }
            // Both sides of the composite FK must change together when transferring a claim.
            if ($claim->exists && $claim->company_id !== (int) $companyId) {
                $claim->client_id = null;
                $claim->save();
            }
            $client->fill([...$clientAttributes, 'company_id' => $companyId])->save();
            if (! empty($attributes['producer_id'])) {
                $producer = Producer::findOrFail($attributes['producer_id']);
                $attributes['producer_name'] = $producer->name;
                $attributes['producer_email'] = $producer->email;
            }
            $claim->fill(Arr::except($attributes, ['client_name', 'client_email', 'phone', 'address', 'city', 'urls', 'image', 'parts_present', 'part_names', 'inspection_address', 'inspection_latitude', 'inspection_longitude']));
            $claim->company_id = $companyId;
            $claim->client_id = $client->id;
            if ($quote !== null) {
                $claim->forceFill($quote);
            }
            $claim->save();
            if (array_key_exists('parts_present', $attributes) || array_key_exists('part_names', $attributes)) {
                $partIds = [];
                foreach ($attributes['part_names'] ?? [] as $name) {
                    $name = preg_replace('/\s+/u', ' ', trim($name));
                    if ($name !== '') {
                        $part = Part::firstOrCreate(['normalized_name' => mb_strtolower($name)], ['name' => $name]);
                        $partIds[] = $part->id;
                    }
                }
                $claim->parts()->sync(array_unique($partIds));
            }

            return $claim->fresh();
        });
    }
}
