<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ReceiveIntegrationClaimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->attributes->has('integration_credential');
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('external_id'))) {
            $this->merge(['external_id' => strtolower(trim($this->input('external_id')))]);
        }
    }

    /** @return array<string,mixed> */
    public function rules(): array
    {
        return [
            'external_id' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9][a-z0-9._:-]*$/D'],
            'claim_number' => ['required', 'string', 'max:191'],
            'client_name' => ['required', 'string', 'max:191'],
            'client_email' => ['nullable', 'email', 'max:191'],
            'phone' => ['nullable', 'string', 'max:191'], 'address' => ['nullable', 'string', 'max:191'], 'city' => ['nullable', 'string', 'max:191'],
            'license_plate' => ['nullable', 'string', 'max:191'],
            'client_type' => ['nullable', 'in:Asegurado,Tercero,Cotizacion'],
            'reason' => ['nullable', 'string', 'max:10000'],
            'inspection_mode' => ['nullable', 'in:presencial,videollamada,foto y presupuesto,foto'],
            'notes' => ['nullable', 'string', 'max:10000'],
        ];
    }

    /** @return array<callable> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            foreach (array_diff(array_keys($this->all()), array_keys($this->rules())) as $field) {
                $validator->errors()->add($field, 'Campo no admitido por el contrato de integración.');
            }
        }];
    }
}
