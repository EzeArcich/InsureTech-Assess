<?php

namespace App\Http\Requests;

use App\Models\Claim;
use App\Models\Workshop;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SaveWorkshopRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->user()?->isRestrictedInspector()) {
            $claim = $this->route('claim');
            Claim::visibleTo($this->user())->findOrFail($claim instanceof Claim ? $claim->id : $claim);
        }

        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $rules = ['name' => ['required', 'string', 'max:191'], 'address' => ['required', 'string', 'max:191'],
            'is_approved' => $this->user()?->isRestrictedInspector() ? ['prohibited'] : ['sometimes', 'boolean'],
            'opening_hours' => ['sometimes', 'nullable', 'string', 'max:1000']];
        foreach (['city', 'neighborhood', 'phone', 'area', 'business_name', 'tax_id', 'holidays', 'deductible_discount', 'hail', 'repair_availability', 'towing', 'transport'] as $field) {
            $rules[$field] = ['sometimes', 'nullable', 'string', 'max:191'];
        }
        $rules['email'] = ['sometimes', 'nullable', 'email', 'max:191'];

        return $rules;
    }

    /** @return array<callable> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->any()) {
                return;
            }
            $workshop = $this->route('workshop');
            $ignoreId = $workshop instanceof Workshop ? $workshop->id : $workshop;
            foreach (['name', 'address'] as $field) {
                if (Workshop::where($field.'_key', Workshop::identityKey($this->input($field)))
                    ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))->exists()) {
                    $validator->errors()->add($field, 'Ya existe un taller con ese '.($field === 'name' ? 'nombre' : 'domicilio').'. Seleccionalo del listado.');
                }
            }
        }];
    }

    /** @return array<string, mixed> */
    public function workshopAttributes(): array
    {
        $values = $this->validated();
        foreach (['city', 'neighborhood', 'phone', 'email', 'area', 'business_name', 'tax_id', 'holidays', 'deductible_discount', 'hail', 'repair_availability', 'towing', 'transport'] as $field) {
            $values[$field] = $values[$field] ?? '';
        }
        $values['is_approved'] = $this->boolean('is_approved');

        return $values;
    }
}
