<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiniestrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('siniestros', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBiginter(column: 'created_by')->nullable();
            // $table->foreign('created_by')->references(columns: 'id')->on(table:'users');
            // $table->unsignedBiginter(column: 'updated_by')->nullable();
            // $table->foreign('updated_by')->references(columns: 'id')->on(table:'users');
            // $table->unsignedBiginter(column: 'deleted_by')->nullable();
            // $table->foreign('deleted_by')->references(columns: 'id')->on(table:'users');
            $table->string('siniestro')->nullable();
            $table->string('patente')->nullable();
            $table->date('fechaip')->nullable();
            $table->string('estado')->nullable();
            $table->string('cliente')->nullable();
            $table->string('modalidad')->nullable();
            $table->text('observaciones')->nullable();
            $table->date('fechacierre')->nullable();
            $table->string('compania')->nullable();
            $table->string('contacto')->nullable();
            $table->string('codigoinspeccion')->nullable();
            $table->string('inspector')->nullable();
            $table->string('direccion')->nullable();
            $table->string('localidad')->nullable();
            $table->string('telefono')->nullable();            
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('siniestros');
    }
};
