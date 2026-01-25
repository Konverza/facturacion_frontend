<section class="my-4 px-4">
    <div class="flex w-full justify-between">
        <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
            Documentos Recibidos
        </h1>
    </div>
    <div class="mt-4 pb-8">
        <!-- Indicador de última importación -->
        @if ($lastImport)
            <div class="mb-4 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
                <div class="flex items-center gap-3">
                    <span class="bg-blue-100 dark:bg-blue-900/50 p-2 rounded-full">
                        <x-icon icon="clock" class="size-5 text-blue-600 dark:text-blue-400" />
                    </span>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-blue-900 dark:text-blue-100">
                            Última importación de DTEs desde Hacienda
                        </p>
                        <p class="text-xs text-blue-700 dark:text-blue-300">
                            {{ $lastImport->updated_at->diffForHumans() }}
                            <span
                                class="text-blue-600 dark:text-blue-400">({{ $lastImport->updated_at->format('d/m/Y h:i A') }})</span>
                        </p>
                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                            @if ($lastImport->total_dtes > 0)
                                <strong>{{ $lastImport->total_dtes }}</strong> documentos descargados,
                                <strong>{{ $lastImport->processed_dtes }}</strong> procesados exitosamente
                                @if ($lastImport->failed_dtes > 0)
                                    <span class="text-red-600 dark:text-red-400">
                                        , <strong>{{ $lastImport->failed_dtes }}</strong> fallidos
                                    </span>
                                @endif
                            @else
                                <span class="text-blue-700 dark:text-blue-300">
                                    Sincronización completada. No se encontraron documentos recibidos en Hacienda.
                                </span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @else
            <div
                class="mb-4 rounded-lg border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-800 dark:bg-yellow-900/20">
                <div class="flex items-center gap-3">
                    <span class="bg-yellow-100 dark:bg-yellow-900/50 p-2 rounded-full">
                        <x-icon icon="warning" class="size-5 text-yellow-600 dark:text-yellow-400" />
                    </span>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-yellow-900 dark:text-yellow-100">
                            No se han descargado DTEs desde Hacienda
                        </p>
                        <p class="text-xs text-yellow-700 dark:text-yellow-300">
                            Haz clic en el botón "Obtener DTEs de Hacienda" para sincronizar tus documentos recibidos.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="mb-4 flex w-full flex-col gap-4 sm:flex-row">
            <div class="flex-[6]">
                <x-input type="text" placeholder="Búsqueda Rápida" class="w-full" icon="search"
                    wire:model.live.debounce.500ms="q" />
                <span class="text-xs">
                    Puede buscar rápidamente por codígo de generación, sello de recibido, documento o nombre del
                    emisor.
                </span>
            </div>
            <div class="flex-1">
                <x-button type="a" typeButton="info" icon="download"
                    href="{{ Route('business.received-documents.import.index') }}" text="Obtener DTEs de Hacienda" />
            </div>
            <div class="flex-1">
                <x-button type="a" typeButton="success" icon="download"
                    href="{{ Route('business.received-documents.zip') }}" text="Descargar DTEs" />
            </div>
        </div>
        <div x-data="{ showFilters: false }"
            class="mb-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">

            <!-- Título y botón Alpine -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    <x-icon icon="filter" class="inline mr-1" />
                    Filtros de búsqueda avanzada
                </h3>
                <button @click="showFilters = !showFilters"
                    class="text-sm text-blue-500 hover:text-blue-700 dark:text-blue-400">
                    <span x-text="showFilters ? 'Ocultar filtros' : 'Mostrar filtros'"></span>
                </button>
            </div>

            <!-- Contenido de filtros controlado por Alpine -->
            <div x-show="showFilters" x-transition>
                <div class="animate-fade-in space-y-4">
                    <div class="flex sm:flex-row flex-col gap-4">
                        <div class="flex-1">
                            <x-input type="date" wire:model.live.debounce.500ms="fechaInicio"
                                label="Fecha emisión desde" />
                        </div>
                        <div class="flex-1">
                            <x-input type="date" wire:model.live.debounce.500ms="fechaFin"
                                label="Fecha emisión hasta" />
                        </div>
                    </div>
                    <div class="flex sm:flex-row flex-col gap-4">
                        <div class="flex-1">
                            <x-select id="tipo_dte" :options="$dte_options" name="tipo_dte"
                                placeholder="Seleccione un tipo de DTE" wire:model.live.debounce.500ms="tipo_dte"
                                :value="$tipo_dte" :selected="$tipo_dte" :search="false" label="Buscar por tipo de DTE" />
                        </div>
                        <div class="flex-1">
                            <x-select id="documento_emisor" :options="$emisores_unicos" name="documento_emisor"
                                placeholder="Seleccione un emisor" label="Buscar por emisor" :search="false"
                                wire:model.live.debounce.500ms="documento_emisor" :value="$documento_emisor" :selected="$documento_emisor" />
                        </div>
                    </div>
                    <!-- Botón Limpiar Filtros (centrado) -->
                    <div class="flex justify-center">
                        <x-button type="button" wire:click="clearFilters" typeButton="info" text="Limpiar filtros" />
                    </div>
                </div>
            </div>
        </div>
        <div class="flex flex-wrap gap-4 my-4 w-full">
            <div
                class="flex-1 flex justify-center items-center gap-4 bg-gradient-to-br from-blue-400 dark:from-blue-600 to-blue-800 dark:to-blue-950 p-4 rounded-lg">
                <span class="bg-blue-100 dark:bg-secondary-950 p-2 rounded-full">
                    <x-icon icon="files" class="size-8 text-blue-500" />
                </span>
                <div class="flex flex-col justify-center items-center sm:items-start gap-1">
                    <p class="font-bold text-white text-2xl">
                        {{ $statistics['total'] }}
                    </p>
                    <h1 class="text-white text-sm">
                        Recibidos
                    </h1>
                </div>
            </div>
            <div
                class="flex-1 flex justify-center items-center gap-4 bg-gradient-to-br from-green-400 dark:from-green-600 to-green-800 dark:to-green-950 p-4 rounded-lg">
                <span class="bg-green-100 dark:bg-secondary-950 p-2 rounded-full">
                    <x-icon icon="circle-check" class="size-8 text-green-500" />
                </span>
                <div class="flex flex-col justify-center items-center sm:items-start gap-1">
                    <p class="font-bold text-white text-2xl">
                        {{ $statistics['approved'] }}
                    </p>
                    <h1 class="text-white text-sm">
                        Procesados
                    </h1>
                </div>
            </div>
            <div
                class="flex-1 flex justify-center items-center gap-4 bg-gradient-to-br from-yellow-400 dark:from-yellow-600 to-yellow-800 dark:to-yellow-950 p-4 rounded-lg">
                <span class="bg-white dark:bg-secondary-950 p-2 rounded-full">
                    <x-icon icon="circle-minus" class="size-8 text-yellow-500" />
                </span>
                <div class="flex flex-col justify-center items-center sm:items-start gap-1">
                    <p class="font-bold text-white text-2xl">
                        {{ $statistics['anulado'] }}
                    </p>
                    <h1 class="text-white text-sm">
                        Anulados
                    </h1>
                </div>
            </div>
        </div>
        <div class="flex-1">
            <div class="flex justify-end items-center">
                <x-button wire:click="exportAsExcel" typeButton="success" icon="excel"
                    text="Exportar esta tabla a Excel" class="my-3" wire:loading.attr="disabled" />
            </div>
            <div wire:loading wire:target="exportAsExcel" class="mt-2 text-sm text-gray-500">
                Generando archivo Excel, por favor espere...
            </div>
        </div>
        <div class="relative">
            <!-- Overlay mientras carga -->
            <div wire:loading.delay
                wire:target="fechaInicio,fechaFin,tipo_dte,documento_emisor,estado,codSucursal,codPuntoVenta,page,perPage,q"
                class="absolute inset-0 mt-8 bg-white bg-opacity-70 z-50 flex items-center justify-center">
                <div class="text-center">
                    <svg class="animate-spin h-8 w-8 text-blue-600 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                    </svg>
                    <p class="text-gray-700 text-sm">Cargando datos...</p>
                </div>
            </div>
            <x-table class="space-y-2">
                <x-slot name="thead">
                    <x-tr>
                        <x-th>Tipo de documento</x-th>
                        <x-th>Respuesta de Hacienda</x-th>
                        <x-th>Emisor</x-th>
                        <x-th>Fecha de Emisión</x-th>
                        <x-th>Monto de Operación</x-th>
                        <x-th :last="true">Accciones</x-th>
                    </x-tr>
                </x-slot>
                <x-slot name="tbody">
                    @forelse ($dtes as $invoice)
                        @php
                            $status = $invoice['estado'];
                            $style = '';
                            if ($status == 'RECHAZADO') {
                                $style = 'bg-red-100 dark:bg-red-950/30';
                            }

                            if ($status == 'CONTINGENCIA' || $status == 'ANULADO') {
                                $style = 'bg-yellow-100 dark:bg-yellow-950/30';
                            }
                        @endphp

                        <x-tr class="{{ $style }}" :last="$loop->last">
                            <x-td>
                                {{ $types[$invoice['tipo_dte']] }}
                                @if ($invoice['estado'] === 'PROCESADO' || $invoice['estado'] === 'VALIDADO' || $invoice['estado'] === 'OBSERVADO')
                                    <span
                                        class="flex items-center gap-1 bg-green-200 dark:bg-green-900/50 px-2 py-1 rounded-lg w-max font-bold text-green-800 dark:text-green-300 text-xs uppercase text-nowrap">
                                        <x-icon icon="circle-check" class="size-4" />
                                        Procesado
                                    </span>
                                @elseif($invoice['estado'] === 'RECHAZADO')
                                    <span
                                        class="flex items-center gap-1 bg-red-200 dark:bg-red-900/50 px-2 py-1 rounded-lg w-max font-bold text-red-800 dark:text-red-300 text-xs uppercase text-nowrap">
                                        <x-icon icon="circle-x" class="size-4" />
                                        Rechazado
                                    </span>
                                @elseif($invoice['estado'] === 'CONTINGENCIA')
                                    <span
                                        class="flex items-center gap-1 bg-yellow-200 dark:bg-yellow-900/50 px-2 py-1 rounded-lg w-max font-bold text-yellow-800 dark:text-yellow-300 text-xs uppercase text-nowrap">
                                        <x-icon icon="warning" class="size-4" />
                                        Contingencia
                                    </span>
                                @elseif($invoice['estado'] === 'ANULADO')
                                    <span
                                        class="flex items-center gap-1 bg-yellow-200 dark:bg-yellow-900/50 px-2 py-1 rounded-lg w-max font-bold text-yellow-800 dark:text-yellow-300 text-xs uppercase text-nowrap">
                                        <x-icon icon="circle-minus" class="size-4" />
                                        Anulado
                                    </span>
                                @endif
                            </x-td>
                            <x-td>
                                <div class="flex flex-col gap-1 text-xs">
                                    <span class="font-semibold">Código generación:</span>
                                    <span>{{ $invoice['codGeneracion'] }}</span>
                                    <span class="font-semibold">Número de control:</span>
                                    <span>{{ $invoice['documento']->identificacion->numeroControl }}</span>
                                    <span class="font-semibold">Sello de recibido:</span>
                                    <span>{{ $invoice['selloRecibido'] }}</span>
                                </div>
                            </x-td>
                            <x-td>
                                @if ($invoice['nombre_emisor'])
                                    <div class="flex flex-col gap-1 text-xs">
                                        <span class="font-semibold">Nombre:</span>
                                        <span>{{ $invoice['nombre_emisor'] }}</span>
                                    </div>
                                @endif

                                @if ($invoice['documento_emisor'])
                                    <div class="flex flex-col gap-1 text-xs">
                                        <span class="font-semibold">Identificación:</span>
                                        <span>{{ $invoice['documento_emisor'] }}</span>
                                    </div>
                                @endif
                            </x-td>
                            <x-td>
                                <span class="text-xs">
                                    <strong>Fecha de emisión:</strong><br>
                                    {{ \Carbon\Carbon::parse($invoice['fhEmision'])->format('d/m/Y h:i:s A') }}
                                </span>
                            </x-td>
                            <x-td class="text-xs">
                                @if (property_exists($invoice['documento'], 'resumen'))
                                    @switch($invoice['tipo_dte'])
                                        @case('01')
                                            <strong>Total:
                                            </strong>${{ number_format($invoice['documento']->resumen->totalPagar ?? 0, 2, '.', ',') }}
                                        @break

                                        @case('03')
                                            <strong>Neto:
                                            </strong>${{ number_format($invoice['documento']->resumen->subTotalVentas ?? 0, 2, '.', ',') }}<br>
                                            <strong>IVA:
                                            </strong>${{ number_format(($invoice['documento']->resumen->totalPagar ?? 0) - ($invoice['documento']->resumen->subTotalVentas ?? 0), 2, '.', ',') }}<br>
                                            <strong>Total:
                                            </strong>${{ number_format($invoice['documento']->resumen->totalPagar ?? 0, 2, '.', ',') }}
                                        @break

                                        @case('05')
                                            <strong>Neto:
                                            </strong>${{ number_format($invoice['documento']->resumen->subTotalVentas ?? 0, 2, '.', ',') }}<br>
                                            <strong>IVA:
                                            </strong>${{ number_format(($invoice['documento']->resumen->montoTotalOperacion ?? 0) - ($invoice['documento']->resumen->subTotalVentas ?? 0), 2, '.', ',') }}<br>
                                            <strong>Total:
                                            </strong>${{ number_format($invoice['documento']->resumen->montoTotalOperacion ?? 0, 2, '.', ',') }}
                                        @break

                                        @case('07')
                                            <strong>Sujeto a Retención:
                                            </strong>${{ number_format($invoice['documento']->resumen->totalSujetoRetencion ?? 0, 2, '.', ',') }}<br>
                                            <strong>IVA Retenido:
                                            </strong>${{ number_format($invoice['documento']->resumen->totalIVAretenido ?? 0, 2, '.', ',') }}
                                        @break

                                        @case('11')
                                            <strong>Total:
                                            </strong>${{ number_format($invoice['documento']->resumen->totalPagar ?? 0, 2, '.', ',') }}
                                        @break

                                        @case('14')
                                            <strong>Total:
                                            </strong>${{ number_format($invoice['documento']->resumen->totalCompra ?? 0, 2, '.', ',') }}
                                        @break

                                        @case('15')
                                            <strong>Total:
                                            </strong>${{ number_format($invoice['documento']->resumen->valorTotal ?? 0, 2, '.', ',') }}
                                        @break

                                        @default
                                            <strong>Total: $0.00</strong>
                                        @break
                                    @endswitch
                                    <hr class="my-3">
                                    <strong>Forma(s) de Pago</strong><br>
                                    @if (property_exists($invoice['documento']->resumen, 'pagos'))
                                        @forelse ($invoice['documento']->resumen->pagos ?? [] as $pago)
                                            <span class="text-xs">
                                                {{ $formas_pago[$pago->codigo] ?? 'Desconocido' }}:
                                                ${{ number_format($pago->montoPago ?? 0, 2, '.', ',') }}
                                            </span><br>
                                        @empty
                                            <span class="text-xs">Desconocido</span><br>
                                        @endforelse
                                    @else
                                        <span class="text-xs">Desconocido</span>
                                    @endif
                                @else
                                    @if ($invoice['tipo_dte'] == '09')
                                        <strong>Líquido a Pagar:
                                        </strong>${{ number_format($invoice['documento']->cuerpoDocumento->liquidoApagar ?? 0, 2, '.', ',') }}
                                    @else
                                        <span class="text-xs text-gray-500">Sin información de montos</span>
                                    @endif
                                @endif
                            </x-td>
                            <x-td th :last="true">
                                <x-button type="a" typeButton="primary" icon="eye"
                                    href="{{ Route('business.received-documents.show', ['codGeneracion' => $invoice['codGeneracion']]) }}"
                                    text="Ver detalles" />
                            </x-td>
                        </x-tr>
                    @empty
                        <x-tr>
                            <x-td colspan="8" class="p-3 text-center text-gray-500">No se encontraron
                                resultados.</x-td>
                        </x-tr>
                    @endforelse
                </x-slot>
            </x-table>
        </div>
        <!-- Paginación y controles -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mt-2">
            <div>
                <label class="text-sm">
                    Mostrar
                    <select wire:model.live="perPage"
                        class="ml-1 rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-200 text-sm">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    registros por página
                </label>
            </div>

            <div class="flex items-center gap-2 text-sm">
                Página {{ $page }} de {{ $total_pages }}

                <!-- Botón Anterior -->
                <button wire:click="$set('page', {{ max(1, $page - 1) }})"
                    class="px-3 py-1 border rounded text-sm hover:bg-gray-100 {{ $page <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                    @if ($page <= 1) disabled @endif>
                    Anterior
                </button>

                <!-- Botón Siguiente -->
                <button wire:click="$set('page', {{ min($total_pages, $page + 1) }})"
                    class="px-3 py-1 border rounded text-sm hover:bg-gray-100 {{ $page >= $total_pages ? 'opacity-50 cursor-not-allowed' : '' }}"
                    @if ($page >= $total_pages) disabled @endif>
                    Siguiente
                </button>

            </div>
        </div>
    </div>
</section>
@push('scripts')
    <script>
        $("#documento_emisor").on("Changed", function() {
            let selectedValue = $(this).val();
            @this.set('documento_emisor', selectedValue);
        });
        $("#tipo_dte").on("Changed", function() {
            let selectedValue = $(this).val();
            @this.set('tipo_dte', selectedValue);
        });
        $("#estado").on("Changed", function() {
            let selectedValue = $(this).val();
            @this.set('estado', selectedValue);
        });
    </script>
@endpush
