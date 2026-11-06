<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveClaimRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->user()?->isRestrictedInspector()) {
            if (! $this->isMethod('put') && ! $this->isMethod('patch')) {
                return false;
            }
            $id = $this->route('claim') ?? $this->route('id');
            \App\Models\Claim::visibleTo($this->user())->findOrFail($id);

            return true;
        }
        if (! $this->isMethod('post') && $this->user()?->canCoordinateInspections()) {
            return true;
        }
        $permission = $this->isMethod('post') ? 'crear-siniestro' : 'editar-siniestro';

        return $this->user() !== null && ($this->user()->can($permission) || $this->user()->can('ceo-siniestro')
            || (! $this->isMethod('post') && $this->user()->canAny(['derivar-siniestro', 'peritos-siniestro', 'terceros-siniestro', 'cotizar-siniestro'])));
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        if ($this->user()?->isRestrictedInspector()) {
            $rules = [
                'inspection_address' => ['prohibited'],
                'inspection_latitude' => ['prohibited'],
                'inspection_longitude' => ['prohibited'],
                'status' => ['sometimes', 'required', 'in:Peritando,IPgestion'],
                'inspector_notes' => ['sometimes', 'nullable', 'string', 'max:10000'],
                ...$this->repairRules(),
                'urls' => ['sometimes', 'array'],
                'urls.*' => ['file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
            ];
            foreach (array_diff((new \App\Models\Claim)->getFillable(), ['status', 'inspector_notes', 'workshop_id', 'bodywork_unit', 'bodywork_units', 'mechanical_unit', 'mechanical_units', 'paint_panels']) as $field) {
                $rules[$field] = ['prohibited'];
            }

            return $rules;
        }
        $required = $this->isMethod('post') ? 'required' : 'sometimes';
        $rules = [
            ...$this->repairRules(),
            'inspection_address' => ['nullable', 'required_with:inspection_latitude,inspection_longitude', 'string', 'max:191'],
            'inspection_latitude' => ['nullable', 'required_with:inspection_longitude', 'numeric', 'between:-90,90'],
            'inspection_longitude' => ['nullable', 'required_with:inspection_latitude', 'numeric', 'between:-180,180'],
            'claim_number' => [$required, 'string', 'max:191'],
            'company_id' => [$required, 'integer', 'exists:companies,id'],
            'client_name' => [$required, 'string', 'max:191'],
            'client_email' => ['sometimes', 'nullable', 'email', 'max:191'],
            'inspector_id' => ['sometimes', 'nullable', 'integer', 'exists:inspectors,id'],
            'producer_id' => ['sometimes', 'nullable', 'integer', 'exists:producers,id'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:191'],
            'address' => ['sometimes', 'nullable', 'string', 'max:191'],
            'city' => ['sometimes', 'nullable', 'string', 'max:191'],
            'inspection_date' => ['sometimes', 'nullable', 'date'],
            'closed_at' => ['sometimes', 'nullable', 'date'],
            'image' => ['sometimes', 'nullable', 'image', 'max:5120'],
            'urls' => ['sometimes', 'array'],
            'urls.*' => ['file', 'mimes:pdf,jpg,jpeg,png,webp,xlsx,xls,doc,docx', 'max:10240'],
        ];
        foreach (['license_plate', 'status', 'client_type', 'inspection_mode', 'notes', 'contact',
            'inspection_code', 'link', 'short_number', 'reason', 'workshop_name', 'coordinator',
            'send_order', 'inspection_time', 'inspector_notes', 'location', 'third_party_name',
            'analyst', 'closure', 'quoted', 'negotiation', 'first_offer', 'second_offer',
            'template', 'estimate', 'inspector_email', 'inspector_phone', 'claim_url', 'producer_name', 'producer_email', 'email', 'cc', 'cc2'] as $field) {
            $rules[$field] = ['sometimes', 'nullable', 'string', 'max:10000'];
        }
        foreach (['license_plate', 'status', 'client_type', 'inspection_mode', 'contact', 'inspection_code'] as $field) {
            $rules[$field] = ['sometimes', 'nullable', 'string', 'max:191'];
        }

        if ($this->user()?->isRestrictedCoordinator()) {
            $rules['status'] = ['sometimes', 'required', 'in:Pendiente,Ausente,Coordinado'];
            $rules['inspector_id'] = ['prohibited'];
        }

        return $rules;
    }
    /** @return array<string, mixed> */
    private function repairRules(): array
    {
        return [
            'bodywork_unit' => ['sometimes', 'required', 'in:day,hour'],
            'mechanical_unit' => ['sometimes', 'required', 'in:day,hour'],
            'bodywork_units' => ['sometimes', 'required', 'numeric', 'decimal:0,2', 'min:0', 'max:9999.99'],
            'mechanical_units' => ['sometimes', 'required', 'numeric', 'decimal:0,2', 'min:0', 'max:9999.99'],
            'paint_panels' => ['sometimes', 'required', 'numeric', 'decimal:0,2', 'min:0', 'max:9999.99'],
            'workshop_id' => ['sometimes', 'nullable', 'integer', 'exists:workshops,id'],
            'parts_present' => ['sometimes', 'boolean'],
            'part_names' => ['sometimes', 'array', 'max:100'],
            'part_names.*' => ['required', 'string', 'max:120'],
        ];
    }
}
