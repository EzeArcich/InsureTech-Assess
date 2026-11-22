<?php

namespace App\Services;

use App\Models\CatalogCollisionPart;
use App\Models\Claim;
use Illuminate\Validation\ValidationException;

class SyncClaimCatalogParts
{
    /** @param list<string> $codes */
    public function handle(Claim $claim, array $codes): void
    {
        $configuration = $claim->vehicle?->catalog_configuration_code;
        $validCount = $configuration ? CatalogCollisionPart::where('vehicle_configuration_code', $configuration)->whereIn('code', $codes)->count() : 0;
        if ($validCount !== count($codes)) {
            throw ValidationException::withMessages(['catalog_part_codes' => 'Los repuestos deben corresponder al vehículo asociado.']);
        }

        $claim->catalogParts()->sync($codes);
    }
}
