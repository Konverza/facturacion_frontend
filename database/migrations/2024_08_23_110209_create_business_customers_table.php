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
        Schema::create('business_customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->foreign('business_id')->references('id')->on('business');
            $table->string('tipoDocumento');
            $table->string('numDocumento');
            $table->string('nrc')->nullable();
            $table->string('nombre');
            $table->string('codActividad')->nullable();
            $table->string('nombreComercial')->nullable();
            $table->string('departamento');
            $table->string('municipio');
            $table->string('complemento');
            $table->string('telefono');
            $table->string('correo');
            $table->string('codPais')->nullable();
            $table->string('tipoPersona')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_customers');
    }
};
