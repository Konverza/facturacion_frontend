@extends('layouts.auth-template')
@section('title', 'Reporte Kardex')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full justify-between items-center mb-4">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                <x-icon icon="clipboard-list" class="inline w-8 h-8" />
                Reporte Kardex
            </h1>
        </div>

        <div class="bg-white dark:bg-gray-950 rounded-lg shadow border border-gray-200 dark:border-gray-800 p-6">
            <form action="{{ route('business.reports.kardex.pdf') }}" method="GET" target="_blank" id="kardex-form">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Producto -->
                    <div class="md:col-span-2">
                        <label for="product_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Producto <span class="text-red-500">*</span>
                        </label>
                        <select name="product_id" id="product_id" required
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                            <option value="">Seleccione un producto</option>
                            @foreach($productos as $producto)
                                <option value="{{ $producto->id }}">
                                    {{ $producto->codigo }} - {{ $producto->descripcion }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Fecha Inicio -->
                    <div>
                        <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Fecha Inicio <span class="text-red-500">*</span>
                        </label>
                        <x-input type="date" name="fecha_inicio" id="fecha_inicio" required 
                            value="{{ date('Y-m-01') }}" />
                    </div>

                    <!-- Fecha Fin -->
                    <div>
                        <label for="fecha_fin" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Fecha Fin <span class="text-red-500">*</span>
                        </label>
                        <x-input type="date" name="fecha_fin" id="fecha_fin" required 
                            value="{{ date('Y-m-d') }}" />
                    </div>

                    <!-- Sucursal (Opcional) -->
                    <div>
                        <label for="sucursal_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Sucursal (Opcional)
                        </label>
                        <select name="sucursal_id" id="sucursal_id"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                            <option value="">Todas las sucursales</option>
                            @foreach($sucursales as $sucursal)
                                <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Punto de Venta (Opcional) -->
                    <div>
                        <label for="punto_venta_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Punto de Venta (Opcional)
                        </label>
                        <select name="punto_venta_id" id="punto_venta_id"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                            <option value="">Todos los puntos de venta</option>
                            @foreach($puntosVenta as $pos)
                                <option value="{{ $pos->id }}">
                                    {{ $pos->nombre }} ({{ $pos->sucursal->nombre }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <x-button type="submit" text="Generar Reporte PDF" icon="pdf" typeButton="primary" />
                    <x-button type="button" text="Limpiar" icon="rotate-ccw" typeButton="secondary" 
                        onclick="document.getElementById('kardex-form').reset()" />
                </div>
            </form>
        </div>

        <div class="mt-6 bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <x-icon icon="info-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" />
                <div class="text-sm text-blue-800 dark:text-blue-200">
                    <p class="font-semibold mb-2">Acerca del Reporte Kardex:</p>
                    <ul class="list-disc list-inside space-y-1">
                        <li>Muestra el historial completo de movimientos de inventario para el producto seleccionado</li>
                        <li>Incluye entradas, salidas y saldos acumulados con sus valores monetarios</li>
                        <li>Puede filtrar por sucursal y/o punto de venta específico</li>
                        <li>El saldo inicial se calcula automáticamente según los movimientos anteriores</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
@endsection
