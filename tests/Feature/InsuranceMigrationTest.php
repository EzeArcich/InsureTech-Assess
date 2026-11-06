<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class InsuranceMigrationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_normalization_and_rollback_preserve_legacy_contact_details(): void
    {
        DB::table('claims')->count();
        $emailMigration = require database_path('migrations/2026_11_06_124053_create_email_templates_and_deliveries_tables.php');
        $emailMigration->down();
        $routeMigration = require database_path('migrations/2026_11_06_110331_add_route_locations_and_inspection_payments.php');
        $routeMigration->down();
        $repairMigration = require database_path('migrations/2026_11_06_104252_add_repair_estimates_and_workshop_details.php');
        $repairMigration->down();
        $producerMigration = require database_path('migrations/2026_11_06_084856_add_producer_id_to_claims_table.php');
        $producerMigration->down();
        $migration = require database_path('migrations/2026_11_06_081202_normalize_insurance_schema.php');
        $migration->down();
        DB::table('siniestros')->insert([
            'id' => 1, 'siniestro' => 'ML12345678', 'cliente' => 'Tercero',
            'compania' => 'Mercantil Andina', 'inspector' => 'Inspector histórico',
            'telefono' => '1144556677', 'direccion' => 'San Martín 123', 'localidad' => 'Buenos Aires',
        ]);
        DB::table('blogs')->insert(['titulo' => 'Título', 'contenido' => 'Contenido']);

        $migration->up();

        $this->assertDatabaseHas('claims', ['id' => 1, 'claim_number' => 'ML12345678', 'client_type' => 'Tercero']);
        $this->assertDatabaseHas('clients', ['phone' => '1144556677', 'address' => 'San Martín 123', 'city' => 'Buenos Aires']);
        $this->assertDatabaseHas('companies', ['name' => 'Mercantil Andina']);
        $this->assertDatabaseHas('inspectors', ['name' => 'Inspector histórico']);
        $this->assertDatabaseHas('blogs', ['title' => 'Título', 'content' => 'Contenido']);

        $migration->down();

        $this->assertDatabaseHas('siniestros', ['id' => 1, 'siniestro' => 'ML12345678', 'compania' => 'Mercantil Andina',
            'inspector' => 'Inspector histórico', 'telefono' => '1144556677', 'direccion' => 'San Martín 123', 'localidad' => 'Buenos Aires']);
        $this->assertDatabaseHas('blogs', ['titulo' => 'Título', 'contenido' => 'Contenido']);
        $migration->up();
        $producerMigration->up();
        $repairMigration->up();
        $routeMigration->up();
        $emailMigration->up();
    }
}
