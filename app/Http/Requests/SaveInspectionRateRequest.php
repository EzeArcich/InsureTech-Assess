<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveInspectionRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('CEO') ?? false;
    }

    public function rules(): array
    {
        return [...array_fill_keys(['in_person', 'photos', 'video_call'], ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:9999999.99']), ...$this->scopeRules()];
    }

    /** @return array<string, mixed> */
    private function scopeRules(): array
    {
        if (! $this->routeIs('settings.inspection-rates.apply')) {
            return [];
        }

        return ['scope' => ['required', 'in:all,company'], 'company_id' => ['exclude_unless:scope,company', 'required', 'integer', 'exists:companies,id']];
    }
}
