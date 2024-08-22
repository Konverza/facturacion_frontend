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
        Schema::create('business_plan', function (Blueprint $table) {
            $table->id();
            $table->string('nit')->notNullable();
            $table->unsignedBigInteger('plan_id');
            $table->foreign('plan_id')->references('id')->on('plans');
            $table->string('dtes')->notNullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_plan');
    }
};
