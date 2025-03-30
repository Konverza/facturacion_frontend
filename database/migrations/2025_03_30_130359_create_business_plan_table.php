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
            $table->bigIncrements('id');
            $table->string('nit');
            $table->unsignedBigInteger('plan_id')->index('business_plan_plan_id_foreign');
            $table->string('dtes');
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
