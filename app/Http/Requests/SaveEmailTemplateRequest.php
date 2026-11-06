<?php

namespace App\Http\Requests;

use App\Services\RenderEmailTemplate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SaveEmailTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageEmailTemplates() ?? false;
    }

    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:120', Rule::unique('email_templates')->ignore($this->route('email_template'))],
            'subject' => ['required', 'string', 'max:191', 'not_regex:/[\r\n]/'],
            'body' => ['required', 'string', 'max:50000'], 'is_active' => ['required', 'boolean'],
            'base_version' => ['nullable', 'integer', 'min:1']];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->any()) {
                return;
            }
            foreach (['subject', 'body'] as $field) {
                preg_match_all('/{{(.*?)}}/s', $this->input($field), $matches);
                foreach ($matches[1] as $variable) {
                    if (! array_key_exists(trim($variable), RenderEmailTemplate::VARIABLES)) {
                        $validator->errors()->add($field, 'Variable desconocida: '.trim($variable));
                    }
                }
            }
            if (trim(strip_tags(app(RenderEmailTemplate::class)->sanitize($this->input('body')))) === '') {
                $validator->errors()->add('body', 'La plantilla debe tener contenido de texto.');
            }
        }];
    }
}
