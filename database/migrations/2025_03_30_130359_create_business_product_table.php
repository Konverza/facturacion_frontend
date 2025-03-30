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
        Schema::create('business_product', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('business_id')->index('business_product_business_id_foreign');
            $table->integer('tipoItem');
            $table->string('codigo');
            $table->string('uniMedida');
            $table->string('descripcion');
            $table->decimal('precioUni', 10);
            $table->decimal('precioSinTributos', 10);
            $table->string('tributos');
            $table->timestamps();
            $table->integer('stockInicial')->default(0);
            $table->integer('stockActual')->default(0);
            $table->enum('estado_stock', ['disponible', 'agotado', 'por_agotarse'])->default('disponible');
            $table->integer('stockMinimo')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_product');
    }
};
