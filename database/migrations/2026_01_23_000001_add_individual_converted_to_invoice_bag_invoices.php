<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_bag_invoices', function (Blueprint $table) {
            $table->boolean('individual_converted')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('invoice_bag_invoices', function (Blueprint $table) {
            $table->dropColumn('individual_converted');
        });
    }
};
