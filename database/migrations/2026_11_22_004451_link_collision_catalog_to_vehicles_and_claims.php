<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table): void {
            $table->string('catalog_configuration_code', 100)->nullable()->unique();
            $table->foreign('catalog_configuration_code')->references('code')->on('catalog_vehicle_configurations')->restrictOnDelete();
        });
        Schema::create('claim_catalog_part', function (Blueprint $table): void {
            $table->foreignId('claim_id')->constrained()->cascadeOnDelete();
            $table->string('part_code', 120);
            $table->foreign('part_code')->references('code')->on('catalog_collision_parts')->restrictOnDelete();
            $table->primary(['claim_id', 'part_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claim_catalog_part');
        Schema::table('vehicles', function (Blueprint $table): void {
            $table->dropForeign(['catalog_configuration_code']);
            $table->dropUnique(['catalog_configuration_code']);
            $table->dropColumn('catalog_configuration_code');
        });
    }
};
