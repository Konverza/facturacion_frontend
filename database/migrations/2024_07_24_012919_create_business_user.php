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
        Schema::create('business_user', function (Blueprint $table) {
            $table->id();
            # Foreign key with business id
            $table->unsignedBigInteger('business_id');
            $table->foreign('business_id')->references('id')->on('business');
            # Foreign key with user id
            $table->foreignId('user_id')->constrained();
            $table->string('role');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_user');
    }
};
