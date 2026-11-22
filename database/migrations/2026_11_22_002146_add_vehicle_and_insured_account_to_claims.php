<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->foreignId('vehicle_id')->nullable()->constrained()->restrictOnDelete();
            $table->text('insured_account')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropConstrainedForeignId('vehicle_id');
            $table->dropColumn('insured_account');
        });
    }
};
