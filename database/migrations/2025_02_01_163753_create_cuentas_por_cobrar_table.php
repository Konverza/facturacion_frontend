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
        Schema::create('cuentas_por_cobrar', function (Blueprint $table) {
            $table->id();
            $table->string("numero_factura");
            $table->string("cliente");
            $table->decimal("monto", 8, 2);
            $table->decimal("saldo", 8, 2);
            $table->enum("estado",["pendiente", "parcial", "pagado", "vencido"]);
            $table->datetime("fecha_vencimiento")->nullable();
            $table->text("observaciones")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuentas_por_cobrar');
    }
};
