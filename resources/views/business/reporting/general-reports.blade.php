@extends('layouts.auth-template')
@section('title', 'Reportería General')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                    Reportería General (PDF/Excel)
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-300">
                    Selecciona el tipo de reporte y genera el archivo en PDF o Excel.
                </p>
            </div>
            <x-button type="a" typeButton="secondary" icon="arrow-left" href="{{ route('business.reporting.general') }}"
                text="Volver" class="w-full sm:w-auto" />
        </div>

        <div class="mt-4 pb-4">
            <div class="mb-4 rounded-lg border border-dashed border-yellow-400 bg-yellow-50 p-4 text-sm text-yellow-800 dark:border-yellow-700 dark:bg-yellow-950/30 dark:text-yellow-200">
                El tiempo de generación del reporte puede variar según los filtros aplicados. Si el volumen de datos es muy alto, el reporte podría no generarse. Intenta con un rango o filtros más acotados.
            </div>
            <form action="{{ route('business.reporting.general-reports.generate') }}" method="POST" target="_blank"
                class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                @csrf

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <x-select id="report_type" name="report_type" label="Tipo de reporte" :options="[
                        'ventas_globales' => 'Ventas globales (por día)',
                        'ventas_punto_venta' => 'Ventas por punto de venta (por día)',
                        'ventas_sucursal' => 'Ventas por sucursal (por día)',
                        'ventas_productos_periodo' => 'Ventas de productos por período',
                        'ventas_producto_especifico' => 'Ventas de producto específico (por día)',
                        'ventas_credito' => 'Ventas al crédito',
                        'ventas_contado' => 'Ventas al contado',
                    ]"
                        :search="false" required />

                    <x-select id="format" name="format" label="Formato" :options="[
                        'pdf' => 'PDF',
                        'excel' => 'Excel',
                    ]" :search="false" required />
                </div>

                <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <x-input type="date" name="start_date" label="Fecha inicio" required />
                    <x-input type="date" name="end_date" label="Fecha fin" required />
                </div>

                <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div id="container_sucursal">
                        <x-select id="codSucursal" name="codSucursal" label="Sucursal" :options="$sucursal_options" />
                    </div>
                    <div id="container_punto_venta">
                        <x-select id="codPuntoVenta" name="codPuntoVenta" label="Punto de venta" :options="$punto_venta_options" />
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="" id="container_productos">
                        <x-select id="producto" name="producto" label="Producto" :options="$product_options" />
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap justify-end gap-2">
                    <x-button type="submit" typeButton="success" icon="download" text="Generar reporte" />
                </div>
            </form>
        </div>
    </section>

    @push('scripts')
        <script>
            $(document).ready(function() {
                const $reportType = $('#report_type');
                const $codSucursal = $('#container_sucursal');
                const $codPuntoVenta = $('#container_punto_venta');
                const $producto = $('#container_productos');

                function toggleFields() {
                    console.log('Report type changed');
                    const value = $reportType.val();
                    $codSucursal.toggleClass('hidden', !['ventas_sucursal'].includes(value));
                    $codPuntoVenta.toggleClass('hidden', !['ventas_punto_venta'].includes(value));
                    $producto.toggleClass('hidden', !['ventas_producto_especifico', 'ventas_productos_periodo']
                        .includes(value));
                }

                $reportType.on('Changed', toggleFields);
                toggleFields();
            });
        </script>
    @endpush
@endsection
