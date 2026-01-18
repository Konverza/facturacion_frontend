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
            $table->foreignId('sucursal_id')->nullable()->after('business_product_id')->constrained('sucursals')->onDelete('set null');
            $table->foreignId('punto_venta_id')->nullable()->after('sucursal_id')->constrained('punto_ventas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_product_movements', function (Blueprint $table) {
            $table->dropForeign(['sucursal_id']);
            $table->dropForeign(['punto_venta_id']);
            $table->dropColumn(['sucursal_id', 'punto_venta_id']);
        });
    }
};
