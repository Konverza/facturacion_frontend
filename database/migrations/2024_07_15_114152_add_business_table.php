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
        Schema::create('business', function (Blueprint $table) {
            $table->id();
            $table->string('nit');
            $table->string('nombre');
            // Foreign key with plans table
            $table->unsignedBigInteger('plan_id');
            $table->foreign('plan_id')->references('id')->on('plans');
            $table->string('dui');
            $table->string('telefono');
            $table->string('correo_responsable');
            $table->string('nombre_responsable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business');
    }
};
