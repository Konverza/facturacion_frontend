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
            $table->foreign(['business_id'])->references(['id'])->on('business')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_product', function (Blueprint $table) {
            $table->dropForeign('business_product_business_id_foreign');
        });
    }
};
