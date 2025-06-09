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
        Schema::table('business_product', function (Blueprint $table) {
            $table->decimal('precioUni', 19, 8)->change();
            $table->decimal('precioSinTributos', 19, 8)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_product', function (Blueprint $table) {
            $table->decimal('precioUni', 10)->change();
            $table->decimal('precioSinTributos', 10)->change();
        });
    }
};
