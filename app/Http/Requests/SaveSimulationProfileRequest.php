<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SaveSimulationProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('simulate-claims') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['enabled' => $this->boolean('enabled')]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:191'],
            'credential_id' => ['required', 'integer', Rule::exists('company_integration_credentials', 'id')->where('is_simulation', true)->where('active', true)->whereNull('revoked_at')],
            'enabled' => ['required', 'boolean'],
            'configuration' => ['required', 'array:scenario,rate_per_hour,duration_minutes,max_per_hour,max_pending,max_concurrency,daily_limit,seed,reasons,distribution,peak_multiplier,peak_minutes,fault_percent,fault_type'],
            'configuration.scenario' => ['required', Rule::in(['normal', 'peak', 'weather', 'communication'])],
            'configuration.rate_per_hour' => ['required', 'numeric', 'min:0.1', 'max:120'],
            'configuration.duration_minutes' => ['nullable', 'integer', 'min:1', 'max:10080'],
            'configuration.max_per_hour' => ['required', 'integer', 'min:1', 'max:120', 'gte:configuration.rate_per_hour'],
            'configuration.max_pending' => ['required', 'integer', 'min:1', 'max:50'],
            'configuration.max_concurrency' => ['required', 'integer', 'min:1', 'max:3'],
            'configuration.daily_limit' => ['nullable', 'integer', 'min:1', 'max:2000'],
            'configuration.seed' => ['nullable', 'integer', 'min:0', 'max:2147483647'],
            'configuration.reasons' => ['required', 'array', 'min:1', 'max:7'],
            'configuration.reasons.*' => ['required', 'integer', 'min:0', 'max:100'],
            'configuration.distribution' => ['required', Rule::in(['fixed', 'exponential'])],
            'configuration.peak_multiplier' => ['required', 'numeric', 'min:1', 'max:10'],
            'configuration.peak_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'configuration.fault_percent' => ['required', 'integer', 'min:0', 'max:50'],
            'configuration.fault_type' => ['required', Rule::in(['timeout', 'transient', 'http_error', 'lost_response', 'duplicate'])],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }
            $reasons = $this->input('configuration.reasons');
            if (array_diff(array_keys($reasons), config('claim_integrations.reasons')) || array_sum($reasons) < 1) {
                $validator->errors()->add('configuration.reasons', 'Seleccioná motivos válidos con al menos una ponderación positiva.');
            }
            if ((int) $this->input('configuration.fault_percent') > 0 && (! config('claim_integrations.engine.faults_enabled') || $this->input('configuration.scenario') !== 'communication')) {
                $validator->errors()->add('configuration.fault_percent', 'Las fallas requieren el escenario de comunicación y habilitación explícita del laboratorio.');
            }
        }];
    }
}
