<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_templates', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('email_template_versions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('email_template_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('number');
            $table->string('subject', 191);
            $table->text('body');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['email_template_id', 'number']);
        });
        Schema::create('email_deliveries', function (Blueprint $table): void {
            $table->id();
            $table->uuid('request_id')->unique();
            $table->foreignId('claim_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('email_template_version_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sent_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('recipient', 191);
            $table->json('cc');
            $table->string('subject', 191);
            $table->text('body');
            $table->string('status', 20);
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_deliveries');
        Schema::dropIfExists('email_template_versions');
        Schema::dropIfExists('email_templates');
    }
};
