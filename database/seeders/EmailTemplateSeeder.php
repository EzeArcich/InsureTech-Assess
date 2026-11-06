<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $template = EmailTemplate::firstOrCreate(['name' => 'Solicitud de coordinación al productor'], ['is_active' => true]);
            if (! $template->versions()->exists()) {
                $template->versions()->create(['number' => 1, 'subject' => 'Coordinación de pericia · {{ claim_number }} · {{ license_plate }}',
                    'body' => '<p>Hola {{ producer_name }},</p><p>Nos comunicamos desde InsureTech Assess, por cuenta de <strong>{{ company_name }}</strong>, para coordinar la inspección del siniestro <strong>{{ claim_number }}</strong>, patente {{ license_plate }}, correspondiente a {{ client_name }}.</p><p>Por favor, informanos los datos de contacto y la disponibilidad para realizar la pericia.</p><p>Saludos cordiales,<br>{{ coordinator_name }}<br>InsureTech Assess</p>']);
            }
        });
    }
}
