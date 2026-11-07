<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simulation_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('credential_id')->constrained('company_integration_credentials')->restrictOnDelete();
            $table->string('name');
            $table->json('configuration');
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });
        Schema::table('claim_simulation_runs', function (Blueprint $table): void {
            $table->foreignId('profile_id')->nullable()->constrained('simulation_profiles')->restrictOnDelete();
            $table->json('configuration')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('next_dispatch_at')->nullable();
            $table->unsignedInteger('planned_count')->default(0);
            $table->string('last_error')->nullable();
            $table->index(['status', 'next_dispatch_at'], 'simulation_runs_due');
        });
        Schema::table('claim_simulation_items', function (Blueprint $table): void {
            $table->string('external_id', 100)->nullable()->unique();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('available_at')->nullable();
            $table->timestamp('reserved_until')->nullable();
            $table->uuid('reservation')->nullable();
            $table->unsignedBigInteger('queue_job_id')->nullable();
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->unsignedBigInteger('claim_id')->nullable();
            $table->string('last_error')->nullable();
            $table->index(['run_id', 'status', 'available_at'], 'simulation_items_due');
        });
        Schema::create('simulation_event_attempts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('item_id')->constrained('claim_simulation_items')->restrictOnDelete();
            $table->unsignedSmallInteger('number');
            $table->string('outcome');
            $table->unsignedSmallInteger('http_status')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('finished_at')->nullable();
            $table->unique(['item_id', 'number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simulation_event_attempts');
        Schema::table('claim_simulation_items', function (Blueprint $table): void {
            $table->dropIndex('simulation_items_due');
            $table->dropUnique(['external_id']);
            $table->dropColumn(['external_id', 'scheduled_at', 'available_at', 'reserved_until', 'reservation', 'queue_job_id', 'attempts', 'claim_id', 'last_error']);
        });
        Schema::table('claim_simulation_runs', function (Blueprint $table): void {
            $table->dropIndex('simulation_runs_due');
            $table->dropConstrainedForeignId('profile_id');
            $table->dropColumn(['configuration', 'ends_at', 'next_dispatch_at', 'planned_count', 'last_error']);
        });
        Schema::dropIfExists('simulation_profiles');
    }
};
