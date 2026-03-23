<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_product_cost_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_product_id')->constrained('business_product')->onDelete('cascade');
            $table->string('nombre_proveedor', 150);
            $table->decimal('costo_final', 19, 8);
            $table->foreignId('price_variant_id')->nullable()->constrained('business_price_variants')->nullOnDelete();
            $table->timestamps();

            $table->index('business_product_id', 'bp_cost_variants_product_idx');
            $table->index('price_variant_id', 'bp_cost_variants_price_variant_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_product_cost_variants');
    }
};
