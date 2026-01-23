<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business', function (Blueprint $table) {
            $table->boolean('invoice_bag_enabled')->default(false)->after('pos_inventory_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('business', function (Blueprint $table) {
            $table->dropColumn('invoice_bag_enabled');
        });
    }
};
