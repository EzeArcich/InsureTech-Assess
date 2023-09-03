<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('_t_h', function (Blueprint $table) {
            $table->id();
            $table->string('vacaciones');
            $table->string('descfranq');
            $table->string('granizo');
            $table->string('ponerep');
            $table->string('taller');
            $table->string('direccion');
            $table->string('barrio');
            $table->string('localidad');
            $table->string('telefonos');
            $table->string('email');
            $table->string('zona');
            $table->string('razon');
            $table->string('cuit');
            $table->string('grua');
            $table->string('traslado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('_t_h');
    }
};
