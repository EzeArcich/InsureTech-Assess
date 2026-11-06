<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignInspectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canPlanInspectionRoutes() ?? false;
    }

    public function rules(): array
    {
        return [
            'inspector_id' => ['required', 'integer', 'exists:inspectors,id'],
            'inspection_date' => ['required', 'date_format:Y-m-d'],
            'inspection_time' => ['nullable', 'date_format:H:i'],
        ];
    }
}
