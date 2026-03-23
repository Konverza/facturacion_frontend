<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_product_movements', function (Blueprint $table) {
            $table->foreignId('product_cost_variant_id')
                ->nullable()
                ->after('price_variant_name')
                ->constrained('business_product_cost_variants')
                ->nullOnDelete();
            $table->string('supplier_name', 150)->nullable()->after('product_cost_variant_id');
            $table->decimal('supplier_cost', 19, 8)->nullable()->after('supplier_name');
        });
    }

    public function down(): void
    {
        Schema::table('business_product_movements', function (Blueprint $table) {
            $table->dropForeign(['product_cost_variant_id']);
            $table->dropColumn(['product_cost_variant_id', 'supplier_name', 'supplier_cost']);
        });
    }
};
