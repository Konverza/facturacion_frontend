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
        Schema::create('business_product_movements', function (Blueprint $table) {
            $table->id();
            $table->integer("business_product_id");
            $table->string("numero_factura")->nullable();
            $table->enum("tipo",["entrada","salida"]);
            $table->integer("cantidad");
            $table->decimal("precio_unitario", 10, 2)->nullable();
            $table->text("descripcion")->nullable();
            $table->foreign("business_product_id")->references("id")->on("business_product");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_product_movements');
    }
};
