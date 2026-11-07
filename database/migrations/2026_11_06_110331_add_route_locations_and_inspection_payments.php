<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('claims', function (Blueprint $table): void {
            $table->string('inspection_address')->nullable();
            $table->decimal('inspection_latitude', 10, 7)->nullable();
            $table->decimal('inspection_longitude', 10, 7)->nullable();
            $table->index(['inspector_id', 'inspection_date', 'status'], 'claims_daily_route_index');
        });
        Schema::create('inspection_rates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('in_person', 12, 2);
            $table->decimal('photos', 12, 2);
            $table->decimal('video_call', 12, 2);
            $table->timestamps();
        });
        Schema::create('inspection_payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('claim_id')->unique()->constrained()->restrictOnDelete();
            $table->foreignId('inspector_id')->constrained()->restrictOnDelete();
            $table->foreignId('company_id')->constrained()->restrictOnDelete();
            $table->string('modality', 20);
            $table->decimal('amount', 12, 2);
            $table->timestamp('completed_at');
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_payments');
        Schema::dropIfExists('inspection_rates');
        // MySQL may replace the inspector FK index with the covering route index.
        if (Schema::getConnection()->getDriverName() === 'mysql' && ! Schema::hasIndex('claims', ['inspector_id'])) {
            Schema::table('claims', function (Blueprint $table): void {
                $table->index('inspector_id', 'claims_inspector_rollback_index');
            });
        }
        Schema::table('claims', function (Blueprint $table): void {
            $table->dropIndex('claims_daily_route_index');
            $table->dropColumn(['inspection_address', 'inspection_latitude', 'inspection_longitude']);
        });
    }
};
