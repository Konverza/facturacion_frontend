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
            $table->string('image_url')->nullable();
            $table->unsignedBigInteger('category_id')->index('business_product_category_id_foreign')->after('image_url')->nullable()->default(null);
            $table->foreign('category_id', 'business_product_category_id_foreign')->references('id')->on('product_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_product', function (Blueprint $table) {
            $table->dropForeign('business_product_category_id_foreign');
            $table->dropColumn('category_id');
            $table->dropColumn('image_url');
        });
    }
};
