<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_product_price_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_product_id')->constrained('business_product')->onDelete('cascade');
            $table->foreignId('price_variant_id')->constrained('business_price_variants')->onDelete('cascade');
            $table->decimal('price_without_iva', 19, 8)->nullable();
            $table->decimal('price_with_iva', 19, 8)->nullable();
            $table->timestamps();

            $table->unique(['business_product_id', 'price_variant_id'], 'bp_price_variant_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_product_price_variants');
    }
};
