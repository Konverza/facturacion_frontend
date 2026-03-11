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
            $table->boolean('see_others_dtes')
                ->default(false)
                ->after('branch_selector')
                ->comment('Permite ver borradores/errores de DTE de otros usuarios del mismo negocio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_user', function (Blueprint $table) {
            $table->dropColumn('see_others_dtes');
        });
    }
};
