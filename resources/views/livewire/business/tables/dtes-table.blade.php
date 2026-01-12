<section class="my-4 px-4">
    <div class="flex w-full justify-between">
        <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
            Documentos emitidos
        </h1>
    </div>
    <div class="mt-4 pb-8">
        <div class="mb-4 flex w-full flex-col gap-4 sm:flex-row">
            <div class="flex-[6]">
                <x-input type="text" placeholder="Búsqueda Rápida" class="w-full" icon="search"
                    wire:model.live.debounce.500ms="q" />
                <span class="text-xs">
                    Puede buscar rápidamente por codígo de generación, sello de recibido, documento o nombre del
                    receptor.
                </span>
            </div>
            @if (!$only_fcf)
                <div class="flex-1">
                    <button type="button"
                        class="show-modal bg-green-500 text-white hover:bg-green-600 dark:bg-green-500 dark:text-white dark:hover:bg-green-600 font-medium rounded-lg flex items-center justify-center gap-1 transition-colors duration-300 text-nowrap  px-4 py-2.5 w-full"
                        data-target="#download-dtes">
                        <x-icon icon="file" class="h-4 w-4" />
                        <span class="text-sm">Descargar DTEs</span>
                    </button>
                </div>
                <div class="flex-1">
                    {{-- <button type="button"
                        class="show-modal bg-blue-500 text-white hover:bg-blue-600 dark:bg-blue-500 dark:text-white dark:hover:bg-blue-600 font-medium rounded-lg flex items-center justify-center gap-1 transition-colors duration-300 text-nowrap  px-4 py-2.5 w-full"
                        data-target="#download-anexos">
                        <x-icon icon="download" class="h-4 w-4" />
                        <span class="text-sm">Descargar Anexos</span>
                    </button> --}}
                    <x-button type="a" typeButton="info" icon="download"
                        href="{{ Route('business.reporting.index') }}" text="Descargar Anexos" />
                </div>
            @endif
        </div>
        @if (Session::has('sucursal'))
            <div
                class="my-4 rounded-lg border border-dashed border-yellow-500 bg-yellow-100 p-4 text-yellow-500 dark:bg-yellow-950/30">
                Actualmente está viendo solo los DTEs de la sucursal seleccionada: {{ $codSucursal }}
                {{ $sucursal_options[$codSucursal] }}
            </div>
        @endif
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
                            <x-input type="date" wire:model.live.debounce.500ms="emisionInicio"
                                label="Fecha emitido desde" />
                        </div>
                        <div class="flex-1">
                            <x-input type="date" wire:model.live.debounce.500ms="emisionFin"
                                label="Fecha emitido hasta" />
                        </div>
                    </div>
                    <div class="flex sm:flex-row flex-col gap-4">
                        <div class="flex-1">
                            <x-input type="date" wire:model.live.debounce.500ms="fechaInicio"
                                label="Fecha de procesamiento desde" />
                        </div>
                        <div class="flex-1">
                            <x-input type="date" wire:model.live.debounce.500ms="fechaFin"
                                label="Fecha de procesamiento hasta" />
                        </div>
                    </div>
                    <div class="flex sm:flex-row flex-col gap-4">
                        @if (!$only_fcf)
                            <div class="flex-1">
                                <x-select id="tipo_dte" :options="$dte_options" name="tipo_dte"
                                    placeholder="Seleccione un tipo de DTE" wire:model.live.debounce.500ms="tipo_dte"
                                    :value="$tipo_dte" :selected="$tipo_dte" :search="false"
                                    label="Buscar por tipo de DTE" />
                            </div>
                        @endif
                        <div class="flex-1">
                            <x-select id="documento_receptor" :options="$receptores_unicos" name="documento_receptor"
                                placeholder="Seleccione un receptor" label="Buscar por receptor" :search="false"
                                wire:model.live.debounce.500ms="documento_receptor" :value="$documento_receptor"
                                :selected="$documento_receptor" />
                        </div>
                        <div class="flex-1">
                            <x-select id="estado" :options="[
                                '' => 'Todos',
                                'PROCESADO' => 'Procesado',
                                'RECHAZADO' => 'Rechazado',
                                'CONTINGENCIA' => 'Contingencia',
                                'ANULADO' => 'Anulado',
                            ]" name="estado" placeholder="Seleccione un estado"
                                label="Buscar por estado" wire:model.live.debounce.500ms="estado" :value="$estado"
                                :selected="$estado" />
                        </div>
                    </div>

                    @if (!$only_default_pos && !Session::has('sucursal'))
                        <div class="flex sm:flex-row flex-col gap-4">
                            <div class="flex-1">
                                <x-select id="codSucursal" :options="$sucursal_options" name="codSucursal"
                                    placeholder="Seleccione una sucursal" wire:model.live.debounce.500ms="codSucursal"
                                    :value="$codSucursal" :selected="$codSucursal" label="Buscar por sucursal" />
                            </div>
                            <div class="flex-1">
                                <x-select id="codPuntoVenta" :options="$punto_venta_options" name="codPuntoVenta"
                                    placeholder="Seleccione un punto de venta" label="Buscar por punto de venta"
                                    wire:model.live.debounce.500ms="codPuntoVenta" :value="$codPuntoVenta"
                                    :selected="$codPuntoVenta" />
                            </div>
                        </div>
                    @endif
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
                        Emitidos
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
            <div
                class="flex-1 flex justify-center items-center gap-4 bg-gradient-to-br from-red-400 dark:from-red-600 to-red-800 dark:to-red-950 p-4 rounded-lg">
                <span class="bg-white dark:bg-secondary-950 p-2 rounded-full">
                    <x-icon icon="alert-circle" class="size-8 text-red-500" />
                </span>
                <div class="flex flex-col justify-center items-center sm:items-start gap-1">
                    <p class="font-bold text-white text-2xl">
                        {{ $statistics['rejected'] }}
                    </p>
                    <h1 class="text-white text-sm">
                        Rechazados
                    </h1>
                </div>
            </div>
        </div>
        <div class="flex-1">
            <div class="flex justify-between items-center">
                <div
                    class="my-4 rounded-lg border border-dashed border-blue-500 bg-blue-100 p-4 text-blue-500 dark:bg-blue-950/30 text-sm">
                    <b>Fecha de Procesamiento: </b>Fecha en la que el DTE recibe el sello de Hacienda. <br>
                    <b>Fecha de Emisión: </b>Fecha en la que el DTE fue generado en el sistema.
                </div>
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
                wire:target="fechaInicio,fechaFin,tipo_dte,documento_receptor,estado,codSucursal,codPuntoVenta,page,perPage"
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
                        <x-th>Receptor</x-th>
                        <x-th>Fecha</x-th>
                        <x-th>Monto de Operación</x-th>
                        <x-th>Observaciones</x-th>
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
                                @if ($invoice['estado'] === 'PROCESADO')
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
                                @if ($invoice['nombre_receptor'])
                                    <div class="flex flex-col gap-1 text-xs">
                                        <span class="font-semibold">Nombre:</span>
                                        <span>{{ $invoice['nombre_receptor'] }}</span>
                                    </div>
                                @endif

                                @if ($invoice['documento_receptor'])
                                    <div class="flex flex-col gap-1 text-xs">
                                        <span class="font-semibold">Identificación:</span>
                                        <span>{{ $invoice['documento_receptor'] }}</span>
                                    </div>
                                @endif
                            </x-td>
                            <x-td>
                                <span class="text-xs">
                                    <strong>Fecha de procesamiento:</strong><br>
                                    {{ \Carbon\Carbon::parse($invoice['fhProcesamiento'])->format('d/m/Y h:i:s A') }}
                                </span><br>
                                <span class="text-xs">
                                    <strong>Fecha de emisión:</strong><br>
                                    {{ \Carbon\Carbon::parse($invoice['fhEmision'])->format('d/m/Y h:i:s A') }}
                                </span>
                            </x-td>
                            <x-td class="text-xs">
                                @switch($invoice['tipo_dte'])
                                    @case('01')
                                        <strong>Total:
                                        </strong>${{ number_format($invoice['documento']->resumen->totalPagar, 2, '.', ',') }}
                                    @break

                                    @case('03')
                                        <strong>Neto:
                                        </strong>${{ number_format($invoice['documento']->resumen->subTotalVentas, 2, '.', ',') }}<br>
                                        <strong>IVA:
                                        </strong>${{ number_format($invoice['documento']->resumen->totalPagar - $invoice['documento']->resumen->subTotalVentas, 2, '.', ',') }}<br>
                                        <strong>Total:
                                        </strong>${{ number_format($invoice['documento']->resumen->totalPagar, 2, '.', ',') }}
                                    @break

                                    @case('05')
                                        <strong>Neto:
                                        </strong>${{ number_format($invoice['documento']->resumen->subTotalVentas, 2, '.', ',') }}<br>
                                        <strong>IVA:
                                        </strong>${{ number_format($invoice['documento']->resumen->montoTotalOperacion - $invoice['documento']->resumen->subTotalVentas, 2, '.', ',') }}<br>
                                        <strong>Total:
                                        </strong>${{ number_format($invoice['documento']->resumen->montoTotalOperacion, 2, '.', ',') }}
                                    @break

                                    @case('07')
                                        <strong>Sujeto a Retención:
                                        </strong>${{ number_format($invoice['documento']->resumen->totalSujetoRetencion, 2, '.', ',') }}<br>
                                        <strong>IVA Retenido:
                                        </strong>${{ number_format($invoice['documento']->resumen->totalIVAretenido, 2, '.', ',') }}
                                    @break

                                    @case('11')
                                        <strong>Total:
                                        </strong>${{ number_format($invoice['documento']->resumen->totalPagar, 2, '.', ',') }}
                                    @break

                                    @case('14')
                                        <strong>Total:
                                        </strong>${{ number_format($invoice['documento']->resumen->totalCompra, 2, '.', ',') }}
                                    @break

                                    @case('15')
                                        <strong>Total:
                                        </strong>${{ number_format($invoice['documento']->resumen->valorTotal, 2, '.', ',') }}
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
                                            {{ $formas_pago[$pago->codigo] }}:
                                            ${{ number_format($pago->montoPago, 2, '.', ',') }}
                                        </span><br>
                                    @empty
                                        <span class="text-xs">Desconocido</span><br>
                                    @endforelse
                                @else
                                    <span class="text-xs">Desconocido</span>
                                @endif
                            </x-td>
                            <x-td>
                                <small class="text-[10px] text-gray-500 dark:text-gray-300">
                                    @if ($invoice['observaciones'] != '[]')
                                        {{ $invoice['observaciones'] }}
                                    @endif
                                </small>
                            </x-td>
                            <x-td th :last="true">
                                @if ($invoice['estado'] !== 'RECHAZADO')
                                    <div class="relative">
                                        <x-button type="button" icon="arrow-down" typeButton="primary"
                                            text="Acciones" class="show-options"
                                            data-target="#options-dtes-{{ $loop->iteration }}" size="small" />
                                        <div class="options absolute right-0 top-0 z-10 mt-1 hidden w-max rounded-lg border border-gray-300 bg-white p-1 dark:border-gray-800 dark:bg-gray-950"
                                            id="options-dtes-{{ $loop->iteration }}">
                                            <ul class="flex flex-col text-xs">
                                                <li>
                                                    <a href="{{ $invoice['enlace_pdf'] }}" target="_blank"
                                                        class="flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                                        <x-icon icon="pdf" class="h-4 w-4" />
                                                        Ver PDF
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ $invoice['enlace_json'] }}" target="_blank"
                                                        class="flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                                        <x-icon icon="file-code" class="h-4 w-4" />
                                                        Ver JSON
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ $invoice['enlace_rtf'] }}" target="_blank"
                                                        class="flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                                        <x-icon icon="file-barcode" class="h-4 w-4" />
                                                        Ver tiquete
                                                    </a>
                                                </li>
                                                @if ($invoice['estado'] !== 'ANULADO')
                                                    <li>
                                                        <button type="button" data-target="#send-email"
                                                            class="show-modal btn-send-email flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900"
                                                            data-id="{{ $invoice['codGeneracion'] }}">
                                                            <x-icon icon="email-forward"
                                                                class="h-4 max-h-4 min-h-4 w-4 min-w-4 max-w-4" />
                                                            Reenviar correo
                                                        </button>
                                                    </li>

                                                    {{-- <li>
                                <button type="button" data-target="#send-whatsapp"
                                    class="show-modal btn-send-whatsapp flex w-full items-center gap-1 text-nowrap rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900"
                                    data-id="{{ $invoice['codGeneracion'] }}">
                                    <x-icon icon="whatsapp" class="h-4 max-h-4 min-h-4 w-4 min-w-4 max-w-4" />
                                    Enviar por WhatsApp
                                </button>
                            </li> --}}
                                                    <li>
                                                        <button type="button"
                                                            class="show-modal btn-anular-dte flex w-full items-center gap-1 rounded-lg px-2 py-2 text-red-500 hover:bg-red-100 dark:text-red-500 dark:hover:bg-red-950/30"
                                                            data-target="#anular-dte"
                                                            data-id="{{ $invoice['codGeneracion'] }}">
                                                            <x-icon icon="x" class="h-4 w-4" />
                                                            Anular
                                                        </button>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                @endif
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

    <div id="anular-dte" tabindex="-1" aria-hidden="true"
        class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
        <div class="relative max-h-full w-full max-w-md p-4">
            <!-- Modal content -->
            <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                <div class="flex flex-col">
                    <!-- Modal header -->
                    <form action="{{ Route('business.dte.anular') }}" method="POST">
                        @csrf
                        <div
                            class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Anular DTE
                            </h3>
                            <button type="button"
                                class="hide-modal ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                                data-target="#anular-dte">
                                <x-icon icon="x" class="h-5 w-5" />
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="flex flex-col gap-4 p-4">
                            <input type="hidden" name="codGeneracion" id="cod-generacion-anular">
                            <x-input type="textarea" placeholder="Ingresa el motivo de la anulación del dte"
                                name="motivo" label="Motivo de anulación" required />
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                            <x-button type="button" class="hide-modal" text="Cancelar" icon="x"
                                typeButton="secondary" data-target="#anular-dte" />
                            <x-button type="submit" text="Anular dte" icon="circle-off" typeButton="danger" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="send-email" tabindex="-1" aria-hidden="true"
        class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
        <div class="relative max-h-full w-full max-w-md p-4">
            <!-- Modal content -->
            <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                <div class="flex flex-col">
                    <!-- Modal header -->
                    <form action="{{ Route('business.dte.send-email') }}" method="POST">
                        @csrf
                        <div
                            class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Reenviar correo
                            </h3>
                            <button type="button"
                                class="hide-modal ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                                data-target="#send-email">
                                <x-icon icon="x" class="h-5 w-5" />
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="flex flex-col gap-4 p-4">
                            <input type="hidden" name="codGeneracion" id="cod-generacion-email">
                            <x-input type="email" icon="email"
                                placeholder="Ingresa el correo electrónico del destinatario" name="email"
                                label="Correo electrónico" required />
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                            <x-button type="button" class="hide-modal" text="Cancelar" icon="x"
                                typeButton="secondary" data-target="#send-email" />
                            <x-button type="submit" text="Reenviar correo" icon="email-forward"
                                typeButton="primary" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="send-whatsapp" tabindex="-1" aria-hidden="true"
        class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
        <div class="relative max-h-full w-full max-w-md p-4">
            <!-- Modal content -->
            <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                <div class="flex flex-col">
                    <!-- Modal header -->
                    <form action="{{ Route('business.dte.send-whatsapp') }}" method="POST">
                        @csrf
                        <div
                            class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Reenviar correo
                            </h3>
                            <button type="button"
                                class="hide-modal ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                                data-target="#send-whatsapp">
                                <x-icon icon="x" class="h-5 w-5" />
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="flex flex-col gap-4 p-4">
                            <input type="hidden" name="codGeneracion" id="cod-generacion-whatsapp">
                            <x-input type="text" icon="phone" placeholder="503XXXXXXXX" name="phone"
                                label="Telefono del destinatario" required />
                            <small class="text-xs text-gray-500 dark:text-gray-300">
                                El número de teléfono debe incluir el código de país y el número de teléfono sin
                                espacios ni guiones.
                            </small>
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                            <x-button type="button" class="hide-modal" text="Cancelar" icon="x"
                                typeButton="secondary" data-target="#send-whatsapp" />
                            <x-button type="submit" text="Enviar mensaje" icon="send" typeButton="primary" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="download-anexos" tabindex="-1" aria-hidden="true"
        class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
        <div class="relative max-h-full w-full max-w-md p-4">
            <!-- Modal content -->
            <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                <div class="flex flex-col">
                    <!-- Modal header -->
                    <form action="{{ Route('business.dte.anexos') }}" method="POST">
                        @csrf
                        <div
                            class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Descargar Anexos
                            </h3>
                            <button type="button"
                                class="hide-modal ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                                data-target="#download-anexos">
                                <x-icon icon="x" class="h-5 w-5" />
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="flex flex-col gap-4 p-4">
                            <x-select id="tipo" :options="[
                                '1' => 'F07 - Detalle de Ventas al Contribuyente',
                                '2' => 'F07 - Detalle de Ventas al Consumidor Final',
                            ]" label="Tipo de Anexo" name="tipo"
                                required />
                            <x-input type="date" icon="calendar" name="desde" label="Desde" required />
                            <x-input type="date" icon="calendar" name="hasta" label="Hasta" required />
                            <x-select id="tipo_operacion" :options="[
                                '1' => 'Gravada',
                                '2' => 'No Gravada o Exento',
                                '3' => 'Excluido o no Constituye Renta',
                                '4' => 'Mixta',
                                '12' => 'Ingresos que ya fueron sujetos de retención informados',
                                '13' => 'Sujetos pasivos Excluidos (art. 6 LISR)',
                            ]" label="Tipo de Operación"
                                name="tipo_operacion" required />
                            <x-select id="tipo_ingreso" :options="[
                                '1' => 'Profesiones, Artes y Oficios',
                                '2' => 'Actividades de Servicios',
                                '3' => 'Actividades Comerciales',
                                '4' => 'Actividades Industriales',
                                '5' => 'Actividades Agropecuarias',
                                '6' => 'Utilidades y Dividendos',
                                '7' => 'Exportaciones de bienes',
                                '8' => 'Servicios Realizados en el Exterior y Utilizados en El Salvador',
                                '9' => 'Exportaciones de servicios',
                                '10' => 'Otras Rentas Gravables',
                                '12' => 'Ingresos que ya fueron sujetos de retención informados',
                                '13' => 'Sujetos pasivos Excluidos (art. 6 LISR)',
                            ]" label="Tipo de Ingreso"
                                name="tipo_ingreso" required />
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                            <x-button type="button" class="hide-modal" text="Cancelar" icon="x"
                                typeButton="secondary" data-target="#download-anexos" />
                            <x-button type="button" text="Descargar" icon="download" typeButton="primary"
                                id="downloadAnexos" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="download-dtes" tabindex="-1" aria-hidden="true"
        class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
        <div class="relative max-h-full w-full max-w-md p-4">
            <!-- Modal content -->
            <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                <div class="flex flex-col">
                    <!-- Modal header -->
                    <form action="{{ Route('business.dte.download-dtes') }}" method="POST">
                        @csrf
                        <div
                            class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Descargar DTEs
                            </h3>
                            <button type="button"
                                class="hide-modal ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                                data-target="#download-dtes">
                                <x-icon icon="x" class="h-5 w-5" />
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="flex flex-col gap-4 p-4">
                            <x-input type="date" icon="calendar" name="desde" label="Desde" required />
                            <x-input type="date" icon="calendar" name="hasta" label="Hasta" required />
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                            <x-button type="button" class="hide-modal" text="Cancelar" icon="x"
                                typeButton="secondary" data-target="#download-dtes" />
                            <x-button type="button" text="Descargar" icon="download" typeButton="primary"
                                id="downloadFiles" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@push('scripts')
    <script>
        document.getElementById("downloadAnexos").addEventListener("click", async function(event) {
            event.preventDefault();
            console.log("Descarga iniciada");
            document.getElementById("loader").classList.remove("hidden");

            const form = document.querySelector("#download-anexos form");
            const formData = new FormData(form);
            const filename = (formData.get("tipo") == "1" ? "f07-contribuyente" : "f07-consumidor-final") +
                "_" + formData.get("desde") + "_" + formData.get("hasta") + ".csv";

            try {
                const response = await fetch(form.action, {
                    method: "POST",
                    body: formData
                });

                if (response.headers.get("X-Download-Started")) {
                    document.getElementById("loader").classList.add("hidden");
                }

                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement("a");
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
            } catch (error) {
                console.error("Error en la descarga:", error);
                document.getElementById("loader").classList.add("hidden");
            }
        });

        document.getElementById("downloadFiles").addEventListener("click", async function(event) {
            event.preventDefault();
            console.log("Descarga iniciada");
            document.getElementById("loader").classList.remove("hidden");

            const form = document.querySelector("#download-dtes form");
            const formData = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: "POST",
                    body: formData
                });

                if (response.headers.get("X-Download-Started")) {
                    document.getElementById("loader").classList.add("hidden");
                }

                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement("a");
                a.href = url;
                a.download = response.headers.get("X-File-Name");
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
            } catch (error) {
                console.error("Error en la descarga:", error);
                document.getElementById("loader").classList.add("hidden");
            }
        });

        $("#documento_receptor").on("Changed", function() {
            let selectedValue = $(this).val();
            @this.set('documento_receptor', selectedValue);
        });
        $("#tipo_dte").on("Changed", function() {
            let selectedValue = $(this).val();
            @this.set('tipo_dte', selectedValue);
        });
        $("#estado").on("Changed", function() {
            let selectedValue = $(this).val();
            @this.set('estado', selectedValue);
        });
        $("#codSucursal").on("Changed", function() {
            let selectedValue = $(this).val();
            @this.set('codSucursal', selectedValue);
        });
        $("#codPuntoVenta").on("Changed", function() {
            let selectedValue = $(this).val();
            @this.set('codPuntoVenta', selectedValue);
        });
    </script>
@endpush
