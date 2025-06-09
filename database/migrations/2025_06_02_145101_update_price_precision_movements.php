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
        Schema::table('business_product_movements', function (Blueprint $table) {
            $table->decimal('precio_unitario', 19, 8)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_product_movements', function (Blueprint $table) {
            $table->decimal('precio_unitario', 10)->change();
        });
    }
};
