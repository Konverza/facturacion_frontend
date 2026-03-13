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
        Schema::table('dtes', function (Blueprint $table) {
            $table->boolean('is_quotation')->default(false)->after('status');
            $table->json('quotation_meta')->nullable()->after('is_quotation');
            $table->string('linked_dte_code', 64)->nullable()->after('quotation_meta');
            $table->index(['business_id', 'is_quotation']);
            $table->index('linked_dte_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dtes', function (Blueprint $table) {
            $table->dropIndex(['business_id', 'is_quotation']);
            $table->dropIndex(['linked_dte_code']);
            $table->dropColumn(['is_quotation', 'quotation_meta', 'linked_dte_code']);
        });
    }
};
