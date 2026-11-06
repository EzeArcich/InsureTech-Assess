<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveInspectionLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return ($this->user()?->canPlanInspectionRoutes() || $this->user()?->isRestrictedInspector());
    }

    public function rules(): array
    {
        return [
            'inspection_address' => ['required', 'string', 'max:255'],
            'inspection_latitude' => ['required', 'numeric', 'between:-90,90'],
            'inspection_longitude' => ['required', 'numeric', 'between:-180,180'],
        ];
    }
}
