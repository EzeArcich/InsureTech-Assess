<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use JsonException;
use RuntimeException;

final class DemoCollisionCatalogSeeder extends Seeder
{
    public function run(): void
    {
        // Usar solo en desarrollo: datos sinteticos, no compatibilidades homologadas.
        if (app()->environment('production')) {
            throw new RuntimeException('Este seeder de demostracion no puede ejecutarse en production.');
        }

        $dir = database_path('seeders/data');
        $vehicles = $this->loadJson($dir . '/vehiculos.json');
        $catalog = $this->loadJson($dir . '/repuestos_colision.json');

        DB::transaction(function () use ($vehicles, $catalog): void {
            $this->upsert('catalog_vehicle_brands', $vehicles['brands'],
                ['code', 'name']);
            $this->upsert('catalog_vehicle_models', $vehicles['models'],
                ['code', 'brand_code', 'name', 'body_style', 'door_count', 'market']);
            $this->upsert('catalog_vehicle_configurations', $vehicles['vehicle_configurations'],
                ['code', 'model_code', 'trim', 'year', 'body_style', 'door_count', 'engine',
                 'transmission', 'display_name', 'market', 'configuration_status']);
            $this->upsert('catalog_collision_part_types', $catalog['part_types'],
                ['code', 'slug', 'name', 'category', 'side', 'position',
                 'equipment_dependent', 'fitment_required']);
            $this->upsert('catalog_collision_parts', $catalog['parts'],
                ['code', 'vehicle_configuration_code', 'part_type_code', 'name',
                 'category', 'side', 'position', 'equipment_dependent', 'oem_code',
                 'manufacturer_part_number', 'price_ars', 'stock',
                 'compatibility_status', 'data_origin']);

            $brands = collect($vehicles['brands'])->keyBy('code');
            $models = collect($vehicles['models'])->keyBy('code');
            foreach ($vehicles['vehicle_configurations'] as $configuration) {
                $model = $models[$configuration['model_code']];
                DB::table('vehicles')->updateOrInsert(
                    ['catalog_configuration_code' => $configuration['code']],
                    ['brand' => $brands[$model['brand_code']]['name'], 'model' => $model['name'],
                        'year' => $configuration['year'], 'version' => $configuration['trim'],
                        'doors' => $configuration['door_count'], 'updated_at' => now(),
                        'created_at' => DB::table('vehicles')->where('catalog_configuration_code', $configuration['code'])->value('created_at') ?? now()]
                );
            }
        });

        $this->command?->info('Catalogo demo cargado: 4 marcas, 8 modelos, 48 configuraciones, 32 tipos y 1536 piezas.');
    }

    /** @return array<string,mixed> */
    private function loadJson(string $filename): array
    {
        $contents = file_get_contents($filename);
        if ($contents === false) {
            throw new RuntimeException('No se pudo leer ' . $filename);
        }

        try {
            return json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('JSON invalido: ' . $filename, previous: $exception);
        }
    }

    /** @param array<int,array<string,mixed>> $records @param string[] $fields */
    private function upsert(string $table, array $records, array $fields): void
    {
        // Idempotente, con lotes razonables para MySQL.
        foreach (array_chunk($records, 200) as $batch) {
            $rows = array_map(
                static fn (array $record): array => array_intersect_key($record, array_flip($fields)),
                $batch
            );
            DB::table($table)->upsert($rows, ['code'], array_values(array_diff($fields, ['code'])));
        }
    }
}
