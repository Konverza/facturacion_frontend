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
            $table->unsignedBigInteger('user_id')->nullable()->after('business_id');
            $table->index(['business_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dtes', function (Blueprint $table) {
            $table->dropIndex(['business_id', 'user_id']);
            $table->dropColumn('user_id');
        });
    }
};
