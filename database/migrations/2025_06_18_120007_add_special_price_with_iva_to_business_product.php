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
            $table->decimal('special_price_with_iva', 19, 8)->default(0)->after('cost');
            $table->decimal('margin', 19, 8)->default(0)->after('special_price_with_iva');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_product', function (Blueprint $table) {
            $table->dropColumn('special_price_with_iva');
            $table->dropColumn('margin');
        });
    }
};
