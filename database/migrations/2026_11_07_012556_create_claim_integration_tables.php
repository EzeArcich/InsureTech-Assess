<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_integration_credentials', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('token_hash', 64)->unique();
            $table->json('abilities');
            $table->boolean('active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->boolean('is_simulation')->default(false);
            $table->text('simulator_token')->nullable();
            $table->timestamps();
        });
        Schema::create('integrated_claims', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained()->restrictOnDelete();
            $table->foreignId('credential_id')->constrained('company_integration_credentials')->restrictOnDelete();
            $table->string('external_id', 100);
            $table->string('payload_hash', 64);
            $table->foreignId('claim_id')->unique()->constrained()->restrictOnDelete();
            $table->json('response_snapshot');
            $table->timestamps();
            $table->unique(['company_id', 'external_id']);
        });
        Schema::create('integration_requests', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignId('credential_id')->nullable()->constrained('company_integration_credentials')->nullOnDelete();
            $table->string('payload_hash', 64)->nullable();
            $table->unsignedSmallInteger('http_status');
            $table->string('outcome', 40);
            $table->unsignedInteger('duration_ms');
            $table->timestamps();
        });
        Schema::create('claim_simulation_runs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('credential_id')->constrained('company_integration_credentials')->restrictOnDelete();
            $table->string('reason');
            $table->unsignedInteger('seed');
            $table->string('status')->default('preview');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
        Schema::create('claim_simulation_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('run_id')->constrained('claim_simulation_runs')->cascadeOnDelete();
            $table->unsignedInteger('position');
            $table->json('payload');
            $table->string('status')->default('preview');
            $table->unsignedSmallInteger('http_status')->nullable();
            $table->json('response')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            $table->unique(['run_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claim_simulation_items');
        Schema::dropIfExists('claim_simulation_runs');
        Schema::dropIfExists('integration_requests');
        Schema::dropIfExists('integrated_claims');
        Schema::dropIfExists('company_integration_credentials');
    }
};
