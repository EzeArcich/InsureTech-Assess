<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateClaimSimulationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('simulate-claims') ?? false;
    }

    /** @return array<string,mixed> */
    public function rules(): array
    {
        return ['credential_id' => ['required', 'integer', Rule::exists('company_integration_credentials', 'id')->where('is_simulation', true)->where('active', true)->whereNull('revoked_at')],
            'reason' => ['required', Rule::in(config('claim_integrations.reasons'))],
            'quantity' => ['required', 'integer', 'min:1', 'max:'.config('claim_integrations.max_batch')],
            'seed' => ['nullable', 'integer', 'min:0', 'max:2147483647']];
    }
}
