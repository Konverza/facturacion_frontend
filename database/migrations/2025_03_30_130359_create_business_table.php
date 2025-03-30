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
            $table->bigIncrements('id');
            $table->string('nit');
            $table->string('nombre');
            $table->unsignedBigInteger('plan_id')->index('business_plan_id_foreign');
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
