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
        Schema::create('punto_ventas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codPuntoVenta');
            $table->unsignedBigInteger('sucursal_id')->index('punto_ventas_sucursal_id_foreign');
            $table->foreign(['sucursal_id'])->references(['id'])->on('sucursals')->onUpdate('no action')->onDelete('no action');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('punto_ventas');
    }
};
