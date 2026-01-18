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
        // Crear tabla para items de transferencia
        Schema::create('pos_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pos_transfer_id')->constrained('pos_transfers')->onDelete('cascade');
            $table->foreignId('business_product_id')->constrained('business_product')->onDelete('cascade');
            $table->decimal('cantidad_solicitada', 12, 2);
            $table->decimal('cantidad_real', 12, 2)->nullable(); // Para liquidaciones
            $table->decimal('diferencia', 12, 2)->nullable(); // Diferencia entre solicitada y real
            $table->text('nota_item')->nullable();
            $table->timestamps();
        });

        // Agregar campos a pos_transfers
        Schema::table('pos_transfers', function (Blueprint $table) {
            $table->boolean('es_devolucion')->default(false)->after('tipo_traslado');
            $table->boolean('requiere_liquidacion')->default(false)->after('es_devolucion');
            $table->boolean('liquidacion_completada')->default(false)->after('requiere_liquidacion');
            $table->text('observaciones_liquidacion')->nullable()->after('liquidacion_completada');
            $table->string('numero_transferencia')->nullable()->after('id');
            
            // Hacer nullable el campo business_product_id ya que ahora los items están en otra tabla
            $table->unsignedBigInteger('business_product_id')->nullable()->change();
            $table->decimal('cantidad', 12, 2)->nullable()->change();
        });

        // Crear índice para número de transferencia
        Schema::table('pos_transfers', function (Blueprint $table) {
            $table->index('numero_transferencia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_transfers', function (Blueprint $table) {
            $table->dropIndex(['numero_transferencia']);
            $table->dropColumn([
                'es_devolucion',
                'requiere_liquidacion',
                'liquidacion_completada',
                'observaciones_liquidacion',
                'numero_transferencia'
            ]);
        });

        Schema::dropIfExists('pos_transfer_items');
    }
};
