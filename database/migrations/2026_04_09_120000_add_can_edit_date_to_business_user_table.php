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
            $table->boolean('can_edit_date')
                ->default(true)
                ->after('see_others_dtes')
                ->comment('Permite editar la fecha de emision del DTE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_user', function (Blueprint $table) {
            $table->dropColumn('can_edit_date');
        });
    }
};
