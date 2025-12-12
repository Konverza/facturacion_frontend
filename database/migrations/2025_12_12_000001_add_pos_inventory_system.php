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
        // 1. Agregar campo a business para habilitar inventario por punto de venta
        Schema::table('business', function (Blueprint $table) {
            $table->boolean('pos_inventory_enabled')->default(false)->after('has_customer_branches')
                ->comment('Si es true, permite gestionar inventario independiente por punto de venta');
        });

        // 2. Agregar campos a punto_ventas para control de inventario
        Schema::table('punto_ventas', function (Blueprint $table) {
            $table->boolean('has_independent_inventory')->default(false)->after('codPuntoVenta')
                ->comment('Si es true, este punto de venta maneja su propio inventario');
        });

        // 3. Crear tabla de inventario por punto de venta
        Schema::create('pos_product_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_product_id')->constrained('business_product')->onDelete('cascade');
            $table->foreignId('punto_venta_id')->constrained('punto_ventas')->onDelete('cascade');
            $table->decimal('stockActual', 10, 2)->default(0);
            $table->decimal('stockMinimo', 10, 2)->default(0);
            $table->enum('estado_stock', ['disponible', 'por_agotarse', 'agotado'])->default('disponible');
            $table->timestamps();

            // Un producto solo puede estar una vez por punto de venta
            $table->unique(['business_product_id', 'punto_venta_id'], 'product_pos_unique');
        });

        // 4. Crear tabla de traslados de punto de venta
        Schema::create('pos_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_product_id')->constrained('business_product')->onDelete('cascade');
            
            // Origen puede ser sucursal o punto de venta
            $table->foreignId('sucursal_origen_id')->nullable()->constrained('sucursals')->onDelete('cascade');
            $table->foreignId('punto_venta_origen_id')->nullable()->constrained('punto_ventas')->onDelete('cascade');
            
            // Destino puede ser sucursal o punto de venta
            $table->foreignId('sucursal_destino_id')->nullable()->constrained('sucursals')->onDelete('cascade');
            $table->foreignId('punto_venta_destino_id')->nullable()->constrained('punto_ventas')->onDelete('cascade');
            
            $table->enum('tipo_traslado', ['pos_to_branch', 'branch_to_pos', 'pos_to_pos'])
                ->comment('Tipo de traslado: punto de venta a sucursal, sucursal a punto de venta, o entre puntos de venta');
            
            $table->decimal('cantidad', 10, 2);
            $table->foreignId('user_id')->constrained('users')->comment('Usuario que realizó el traslado');
            $table->text('notas')->nullable();
            $table->enum('estado', ['pendiente', 'completado', 'cancelado'])->default('completado');
            $table->timestamp('fecha_traslado')->useCurrent();
            $table->timestamps();

            $table->index(['sucursal_origen_id', 'punto_venta_origen_id']);
            $table->index(['sucursal_destino_id', 'punto_venta_destino_id']);
            $table->index('fecha_traslado');
            $table->index('tipo_traslado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_transfers');
        Schema::dropIfExists('pos_product_stock');
        
        Schema::table('punto_ventas', function (Blueprint $table) {
            $table->dropColumn('has_independent_inventory');
        });
        
        Schema::table('business', function (Blueprint $table) {
            $table->dropColumn('pos_inventory_enabled');
        });
    }
};
