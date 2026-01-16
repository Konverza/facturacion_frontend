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
            $table->date('emision_inicio')->nullable()->after('fecha_fin');
            $table->date('emision_fin')->nullable()->after('emision_inicio');
            $table->string('cod_sucursal')->nullable()->after('emision_fin');
            $table->string('cod_punto_venta')->nullable()->after('cod_sucursal');
            $table->string('tipo_dte')->nullable()->after('cod_punto_venta');
            $table->string('estado')->nullable()->after('tipo_dte');
            $table->string('documento_receptor')->nullable()->after('estado');
            $table->string('busqueda')->nullable()->after('documento_receptor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zip_download_jobs', function (Blueprint $table) {
            $table->dropColumn([
                'emision_inicio',
                'emision_fin',
                'cod_sucursal',
                'cod_punto_venta',
                'tipo_dte',
                'estado',
                'documento_receptor',
                'busqueda'
            ]);
        });
    }
};
