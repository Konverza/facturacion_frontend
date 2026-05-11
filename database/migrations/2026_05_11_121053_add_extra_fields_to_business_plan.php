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
        Schema::table('business_plan', function (Blueprint $table) {
            $table->enum('billing_type', ['monthly', 'yearly'])->default('monthly')->after('dtes');
            $table->integer('extra_dtes')->default(0)->after('billing_type');
            $table->date('extra_dtes_expiration')->nullable()->after('extra_dtes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_plan', function (Blueprint $table) {
            $table->dropColumn('billing_type');
            $table->dropColumn('extra_dtes');
            $table->dropColumn('extra_dtes_expiration');
        });
    }
};
