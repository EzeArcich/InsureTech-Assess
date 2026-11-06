<?php

namespace App\Services;

use App\Models\Claim;
use App\Models\CompanyRate;
use Illuminate\Validation\ValidationException;

class CalculateClaimEstimate
{
    /** @param array<string, mixed> $attributes
     *  @return array<string, string|null>
     */
    public function calculate(Claim $claim, array $attributes): array
    {
        $companyId = $attributes['company_id'] ?? $claim->company_id;
        $rates = CompanyRate::where('company_id', $companyId)->first();
        $result = [];
        $total = 0;
        foreach (['bodywork', 'mechanical', 'paint'] as $kind) {
            $quantityField = $kind === 'paint' ? 'paint_panels' : $kind.'_units';
            $quantity = $attributes[$quantityField] ?? $claim->{$quantityField} ?? '0';
            $unit = $kind === 'paint' ? 'panel' : ($attributes[$kind.'_unit'] ?? $claim->{$kind.'_unit'} ?? 'hour');
            $sameAgreement = (int) $companyId === (int) $claim->company_id
                && ($kind === 'paint' || $unit === ($claim->{$kind.'_unit'} ?? 'hour'));
            $rate = $sameAgreement ? $claim->{$kind.'_rate'} : null;
            if ($this->scaled($quantity) > 0) {
                $rate ??= $rates?->{$kind.'_'.$unit};
            }
            if ($this->scaled($quantity) > 0 && $rate === null) {
                throw ValidationException::withMessages([$quantityField => 'La compañía no tiene configurada la tarifa de este trabajo.']);
            }
            $cents = intdiv($this->scaled($quantity) * $this->scaled($rate ?? '0') + 50, 100);
            $result[$kind.'_rate'] = $rate;
            $result[$kind.'_total'] = $this->money($cents);
            $total += $cents;
        }
        $result['repair_total'] = $this->money($total);

        return $result;
    }

    private function scaled(string|int|float $value): int
    {
        [$whole, $fraction] = array_pad(explode('.', (string) $value, 2), 2, '');

        return ((int) $whole * 100) + (int) str_pad($fraction, 2, '0');
    }

    private function money(int $cents): string
    {
        return intdiv($cents, 100).'.'.str_pad((string) ($cents % 100), 2, '0', STR_PAD_LEFT);
    }
}
