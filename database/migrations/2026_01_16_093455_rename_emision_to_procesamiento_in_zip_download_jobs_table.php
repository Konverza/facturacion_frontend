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
        Schema::table('zip_download_jobs', function (Blueprint $table) {
            $table->renameColumn('emision_inicio', 'procesamiento_inicio');
            $table->renameColumn('emision_fin', 'procesamiento_fin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zip_download_jobs', function (Blueprint $table) {
            $table->renameColumn('procesamiento_inicio', 'emision_inicio');
            $table->renameColumn('procesamiento_fin', 'emision_fin');
        });
    }
};
