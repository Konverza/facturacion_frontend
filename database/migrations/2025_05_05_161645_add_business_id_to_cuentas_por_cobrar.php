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
        Schema::table('cuentas_por_cobrar', function (Blueprint $table) {
            $table->unsignedBigInteger('business_id')->nullable();
            $table->foreign(['business_id'], 'fk_cuentas_por_cobrar_business1')->references(['id'])->on('business')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cuentas_por_cobrar', function (Blueprint $table) {
            $table->dropForeign('fk_cuentas_por_cobrar_business1');
        });
    }
};
