<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_customers', function (Blueprint $table) {
            $table->foreignId('price_variant_id')
                ->nullable()
                ->after('special_price')
                ->constrained('business_price_variants')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('business_customers', function (Blueprint $table) {
            $table->dropForeign(['price_variant_id']);
            $table->dropColumn('price_variant_id');
        });
    }
};
