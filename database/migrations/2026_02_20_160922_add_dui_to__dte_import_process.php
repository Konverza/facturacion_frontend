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
        Schema::table('dte_import_processes', function (Blueprint $table) {
            $table->string('dui')->nullable()->after('nit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dte_import_processes', function (Blueprint $table) {
            $table->dropColumn(['dui']);
        });
    }
};
