<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_product_movements', function (Blueprint $table) {
            $table->foreignId('price_variant_id')
                ->nullable()
                ->after('precio_unitario')
                ->constrained('business_price_variants')
                ->nullOnDelete();
            $table->string('price_variant_name')->nullable()->after('price_variant_id');
        });
    }

    public function down(): void
    {
        Schema::table('business_product_movements', function (Blueprint $table) {
            $table->dropForeign(['price_variant_id']);
            $table->dropColumn(['price_variant_id', 'price_variant_name']);
        });
    }
};
