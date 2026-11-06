<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveCompanyRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('CEO') ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $rules = [];
        foreach (['bodywork_day', 'bodywork_hour', 'mechanical_day', 'mechanical_hour', 'paint_panel'] as $field) {
            $rules[$field] = ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:9999999.99'];
        }

        return [...$rules, ...$this->scopeRules()];
    }

    /** @return array<string, mixed> */
    private function scopeRules(): array
    {
        if (! $this->routeIs('settings.repair-rates.apply')) {
            return [];
        }

        return ['scope' => ['required', 'in:all,company'], 'company_id' => ['exclude_unless:scope,company', 'required', 'integer', 'exists:companies,id']];
    }
}
