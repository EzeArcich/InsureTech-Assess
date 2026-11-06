<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_rates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->unique()->constrained()->cascadeOnDelete();
            foreach (['bodywork_day', 'bodywork_hour', 'mechanical_day', 'mechanical_hour', 'paint_panel'] as $column) {
                $table->decimal($column, 12, 2);
            }
            $table->timestamps();
        });
        Schema::table('workshops', function (Blueprint $table): void {
            $table->boolean('is_approved')->default(false);
            $table->text('opening_hours')->nullable();
            $table->string('name_key', 64)->nullable()->unique();
            $table->string('address_key', 64)->nullable()->unique();
        });
        DB::table('workshops')->update(['is_approved' => true]);
        DB::table('workshops')->orderBy('id')->chunkById(100, function ($workshops): void {
            foreach ($workshops as $workshop) {
                foreach (['name', 'address'] as $field) {
                    $normalized = mb_strtolower(preg_replace('/\s+/u', ' ', trim($workshop->{$field})));
                    if ($normalized !== '') {
                        $key = hash('sha256', $normalized);
                        if (! DB::table('workshops')->where($field.'_key', $key)->exists()) {
                            DB::table('workshops')->where('id', $workshop->id)->update([$field.'_key' => $key]);
                        }
                    }
                }
            }
        });
        Schema::table('claims', function (Blueprint $table): void {
            $table->foreignId('workshop_id')->nullable()->constrained()->nullOnDelete();
            foreach (['bodywork', 'mechanical'] as $kind) {
                $table->string($kind.'_unit', 4)->default('hour');
                $table->decimal($kind.'_units', 8, 2)->default(0);
                $table->decimal($kind.'_rate', 12, 2)->nullable();
                $table->decimal($kind.'_total', 16, 2)->default(0);
            }
            $table->decimal('paint_panels', 8, 2)->default(0);
            $table->decimal('paint_rate', 12, 2)->nullable();
            $table->decimal('paint_total', 16, 2)->default(0);
            $table->decimal('repair_total', 16, 2)->default(0);
        });
        Schema::create('parts', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120);
            $table->string('normalized_name', 120)->unique();
            $table->timestamps();
        });
        Schema::create('claim_part', function (Blueprint $table): void {
            $table->foreignId('claim_id')->constrained()->cascadeOnDelete();
            $table->foreignId('part_id')->constrained()->restrictOnDelete();
            $table->primary(['claim_id', 'part_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claim_part');
        Schema::dropIfExists('parts');
        Schema::table('claims', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('workshop_id');
            $table->dropColumn(['bodywork_unit', 'bodywork_units', 'bodywork_rate', 'bodywork_total', 'mechanical_unit', 'mechanical_units', 'mechanical_rate', 'mechanical_total', 'paint_panels', 'paint_rate', 'paint_total', 'repair_total']);
        });
        Schema::table('workshops', function (Blueprint $table): void {
            $table->dropUnique(['name_key']);
            $table->dropUnique(['address_key']);
            $table->dropColumn(['is_approved', 'opening_hours', 'name_key', 'address_key']);
        });
        Schema::dropIfExists('company_rates');
    }
};
