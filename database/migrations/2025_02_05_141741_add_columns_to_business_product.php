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
            $table->integer("stockActual")->default(0);
            $table->enum("estado_stock", ["disponible", "agotado", "por_agotarse"])->default("disponible");
            $table->integer("stockMinimo")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_product', function (Blueprint $table) {
            $table->dropColumn("stockActual");
            $table->dropColumn("estado_stock");
            $table->dropColumn("stockMinimo");
        });
    }
};
