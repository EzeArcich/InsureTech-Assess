<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('catalog_vehicle_brands', function (Blueprint $table): void {
            $table->string('code', 80)->primary();
            $table->string('name', 100);
        });

        Schema::create('catalog_vehicle_models', function (Blueprint $table): void {
            $table->string('code', 80)->primary();
            $table->string('brand_code', 80);
            $table->string('name', 100);
            $table->string('body_style', 20);
            $table->unsignedTinyInteger('door_count');
            $table->string('market', 2);
            $table->foreign('brand_code')->references('code')->on('catalog_vehicle_brands');
        });

        Schema::create('catalog_vehicle_configurations', function (Blueprint $table): void {
            $table->string('code', 100)->primary();
            $table->string('model_code', 80);
            $table->string('trim', 80);
            $table->unsignedSmallInteger('year');
            $table->string('body_style', 20);
            $table->unsignedTinyInteger('door_count');
            $table->string('engine', 100)->nullable();
            $table->string('transmission', 100)->nullable();
            $table->string('display_name', 200);
            $table->string('market', 2);
            $table->string('configuration_status', 40);
            $table->foreign('model_code')->references('code')->on('catalog_vehicle_models');
            $table->unique(['model_code','trim','year'], 'cv_configs_model_trim_year_unique');
        });

        Schema::create('catalog_collision_part_types', function (Blueprint $table): void {
            $table->string('code', 20)->primary();
            $table->string('slug', 100)->unique();
            $table->string('name', 160);
            $table->string('category', 40);
            $table->string('side', 10)->nullable();
            $table->string('position', 15)->nullable();
            $table->boolean('equipment_dependent');
            $table->boolean('fitment_required');
        });

        Schema::create('catalog_collision_parts', function (Blueprint $table): void {
            $table->string('code', 120)->primary();
            $table->string('vehicle_configuration_code', 100);
            $table->string('part_type_code', 20);
            $table->string('name', 160);
            $table->string('category', 40);
            $table->string('side', 10)->nullable();
            $table->string('position', 15)->nullable();
            $table->boolean('equipment_dependent');
            $table->string('oem_code', 100)->nullable();
            $table->string('manufacturer_part_number', 100)->nullable();
            $table->decimal('price_ars', 15, 2)->nullable();
            $table->unsignedInteger('stock')->nullable();
            $table->string('compatibility_status', 32);
            $table->string('data_origin', 32);
            $table->foreign('vehicle_configuration_code', 'cp_vehicle_configuration_fk')
                ->references('code')->on('catalog_vehicle_configurations');
            $table->foreign('part_type_code', 'cp_part_type_fk')
                ->references('code')->on('catalog_collision_part_types');
            $table->unique(['vehicle_configuration_code','part_type_code'], 'cp_vehicle_part_unique');
            $table->index(['vehicle_configuration_code','category'], 'cp_vehicle_category_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalog_collision_parts');
        Schema::dropIfExists('catalog_collision_part_types');
        Schema::dropIfExists('catalog_vehicle_configurations');
        Schema::dropIfExists('catalog_vehicle_models');
        Schema::dropIfExists('catalog_vehicle_brands');
    }
};
