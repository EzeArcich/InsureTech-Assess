<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmailTemplateVersion>
 */
class EmailTemplateVersionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email_template_id' => \App\Models\EmailTemplate::factory(), 'number' => 1,
            'subject' => 'Siniestro {{ claim_number }}', 'body' => '<p>Hola {{ producer_name }}, {{ company_name }}.</p>',
        ];
    }
}
