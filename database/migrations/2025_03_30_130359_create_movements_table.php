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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cuenta_id')->nullable()->index('movements_cuenta_id_foreign');
            $table->string('numero_factura')->nullable();
            $table->enum('tipo', ['pago', 'ajuste', 'cargo_extra', 'descuento']);
            $table->dateTime('fecha');
            $table->decimal('monto');
            $table->text('observaciones')->nullable();
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
