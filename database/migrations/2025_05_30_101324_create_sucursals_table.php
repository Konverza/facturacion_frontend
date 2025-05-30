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
        Schema::create('sucursals', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('departamento');
            $table->string('municipio');
            $table->string('complemento');
            $table->string('telefono');
            $table->string('correo');
            $table->string('codSucursal');
            $table->unsignedBigInteger('business_id')->index('business_sucursals_business_id_foreign');
            $table->foreign(['business_id'])->references(['id'])->on('business')->onUpdate('no action')->onDelete('no action');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sucursals');
    }
};
