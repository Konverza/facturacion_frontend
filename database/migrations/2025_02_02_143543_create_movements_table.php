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
        Schema::create('movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("cuenta_id")->nullable();
            $table->foreign("cuenta_id")->references("id")
            ->on("cuentas_por_cobrar")->onDelete("cascade")->onUpdate("cascade")->nullable();
            $table->string("numero_factura")->nullable();
            $table->enum("tipo",["pago", "ajuste", "cargo_extra","descuento"]);
            $table->datetime("fecha");
            $table->decimal("monto", 8, 2);
            $table->text("observaciones")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movements');
    }
};
