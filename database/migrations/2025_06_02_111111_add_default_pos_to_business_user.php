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
        Schema::table('business_user', function (Blueprint $table) {
            $table->unsignedBigInteger('default_pos_id')->nullable()->after('role')->index('business_user_default_pos_id_foreign');
            $table->foreign('default_pos_id', 'business_user_default_pos_id_foreign')
                ->references('id')
                ->on('punto_ventas')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_user', function (Blueprint $table) {
            $table->dropForeign('business_user_default_pos_id_foreign');
            $table->dropColumn('default_pos_id');
        });
    }
};
