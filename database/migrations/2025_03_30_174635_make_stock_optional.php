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
        Schema::table('business_product', function (Blueprint $table) {
            $table->integer('stockInicial')->default(0)->nullable()->change();
            $table->integer('stockActual')->default(0)->nullable()->change();
            $table->enum('estado_stock', ['disponible', 'agotado', 'por_agotarse', 'n/a'])->default('disponible')->change();
            $table->integer('stockMinimo')->default(0)->nullable()->change();
            $table->boolean('has_stock')->default(true)->after('stockMinimo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_product', function (Blueprint $table) {
            $table->integer('stockInicial')->default(0)->change();
            $table->integer('stockActual')->default(0)->change();
            $table->enum('estado_stock', ['disponible', 'agotado', 'por_agotarse'])->default('disponible')->change();
            $table->integer('stockMinimo')->default(0)->change();
            $table->dropColumn('has_stock');
        });
    }
};
