<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendProducerEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageEmailTemplates() ?? false;
    }

    public function rules(): array
    {
        return ['producer_id' => ['required', 'integer', 'exists:producers,id'],
            'template_version_id' => ['required', 'integer', 'exists:email_template_versions,id'],
            'cc' => ['sometimes', 'array', 'max:10'], 'cc.*' => ['required', 'email:rfc', 'max:191', 'distinct:ignore_case'],
            'request_id' => [$this->routeIs('claims.producer-email.send') ? 'required' : 'sometimes', 'uuid']];
    }
}
