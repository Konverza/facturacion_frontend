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
        // 1. Agregar campo is_global a business_product
        Schema::table('business_product', function (Blueprint $table) {
            $table->boolean('is_global')->default(false)->after('has_stock')
                ->comment('Si es true, el producto está disponible para todas las sucursales sin control de stock');
        });

        // 2. Crear tabla de inventario por sucursal
        Schema::create('business_product_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_product_id')->constrained('business_product')->onDelete('cascade');
            $table->foreignId('sucursal_id')->constrained('sucursals')->onDelete('cascade');
            $table->decimal('stockActual', 10, 2)->default(0);
            $table->decimal('stockMinimo', 10, 2)->default(0);
            $table->enum('estado_stock', ['disponible', 'por_agotarse', 'agotado'])->default('disponible');
            $table->timestamps();

            // Un producto solo puede estar una vez por sucursal
            $table->unique(['business_product_id', 'sucursal_id'], 'product_sucursal_unique');
        });

        // 3. Crear tabla de traslados entre sucursales
        Schema::create('branch_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_product_id')->constrained('business_product')->onDelete('cascade');
            $table->foreignId('sucursal_origen_id')->constrained('sucursals')->onDelete('cascade');
            $table->foreignId('sucursal_destino_id')->constrained('sucursals')->onDelete('cascade');
            $table->decimal('cantidad', 10, 2);
            $table->foreignId('user_id')->constrained('users')->comment('Usuario que realizó el traslado');
            $table->text('notas')->nullable();
            $table->enum('estado', ['pendiente', 'completado', 'cancelado'])->default('completado');
            $table->timestamp('fecha_traslado')->useCurrent();
            $table->timestamps();

            $table->index(['sucursal_origen_id', 'sucursal_destino_id']);
            $table->index('fecha_traslado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_transfers');
        Schema::dropIfExists('business_product_stock');
        
        Schema::table('business_product', function (Blueprint $table) {
            $table->dropColumn('is_global');
        });
    }
};
