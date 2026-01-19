<section class="my-4 px-4">
    <div class="flex w-full flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Reportería General
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-300">
                Resumen consolidado de documentos emitidos y comportamiento de ventas.
            </p>
        </div>
        <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row">
            <x-button type="a" typeButton="info" icon="file-text" href="{{ route('business.documents.index') }}"
                text="Ver documentos" class="w-full sm:w-auto" />
            <x-button type="a" typeButton="success" icon="download"
                href="{{ route('business.reporting.general-reports') }}" text="Reportes PDF/Excel"
                class="w-full sm:w-auto" />
        </div>
    </div>

    <div class="relative mt-4 pb-6">
        <div class="mb-4 rounded-lg border border-dashed border-yellow-400 bg-yellow-50 p-4 text-sm text-yellow-800 dark:border-yellow-700 dark:bg-yellow-950/30 dark:text-yellow-200">
            El tiempo de generación del reporte puede variar según los filtros aplicados. Si el volumen de datos es muy alto, el reporte podría no generarse. Intenta con un rango o filtros más acotados.
        </div>
        <div wire:loading.delay
            wire:target="fechaInicio,fechaFin,emisionInicio,emisionFin,codSucursal,codPuntoVenta,tipo_dte,estado,documento_receptor,q,condicion_operacion,minMonto,maxMonto,limit,periodo,clearFilters,saveFilter,loadFilter,deleteFilter"
            class="absolute inset-0 z-50 flex items-center justify-center bg-white/70 backdrop-blur-sm dark:bg-secondary-950/60">
            <div class="text-center">
                <x-icon icon="loader" class="mx-auto mb-2 h-8 w-8 text-blue-600" />
                <p class="text-sm text-gray-700 dark:text-gray-200">Aplicando filtros...</p>
            </div>
        </div>
        <div class="mb-4 rounded-lg border border-dashed border-blue-500 bg-blue-50 p-4 text-sm text-blue-700 dark:border-blue-800 dark:bg-blue-950/30 dark:text-blue-300">
            Se muestran {{ $total_mostrados }} de {{ $total_registros }} documentos según los filtros actuales.
            @if ($total_registros > $limit)
                <span class="font-semibold">Aumenta el límite si necesitas más detalle.</span>
            @endif
        </div>

        <div x-data="{ showFilters: false }"
            class="mb-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    <x-icon icon="filter" class="mr-1 inline" />
                    Filtros de reportería
                </h3>
                <button @click="showFilters = !showFilters"
                    class="text-sm text-blue-500 hover:text-blue-700 dark:text-blue-400">
                    <span x-text="showFilters ? 'Ocultar filtros' : 'Mostrar filtros'"></span>
                </button>
            </div>

            <div x-show="showFilters" x-transition>
                <div class="animate-fade-in space-y-4">
                    <div class="flex flex-col gap-4 sm:flex-row">
                        <div class="flex-1">
                            <x-select id="periodo" name="periodo" label="Periodo rápido" :options="[
                                '' => 'Personalizado',
                                'hoy' => 'Hoy',
                                'ayer' => 'Ayer',
                                'semana_actual' => 'Semana actual',
                                'mes_actual' => 'Mes actual',
                                'mes_anterior' => 'Mes anterior',
                            ]" :search="false" wire:model.live="periodo" />
                        </div>
                        <div class="flex-1">
                            <x-input type="number" name="limit" label="Límite de documentos" wire:model.live="limit"
                                placeholder="500" />
                        </div>
                    </div>

                    <div class="flex flex-col gap-4 sm:flex-row">
                        <div class="flex-1">
                            <x-input type="text" name="filter_name" label="Nombre del filtro"
                                placeholder="Ej: Ventas enero contado" wire:model.live="filter_name" />
                        </div>
                        <div class="flex-1">
                            <x-select id="selected_filter" name="selected_filter" label="Filtros guardados"
                                :options="$saved_filter_options" :search="false"
                                wire:model.live="selected_filter" />
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-center gap-2">
                        <x-button type="button" wire:click="saveFilter" typeButton="success" icon="save"
                            text="Guardar filtro" />
                        <x-button type="button" wire:click="loadFilter" typeButton="info" icon="refresh"
                            text="Cargar filtro" />
                        <x-button type="button" wire:click="deleteFilter" typeButton="warning" icon="trash"
                            text="Eliminar filtro" />
                    </div>

                    @if ($filter_error)
                        <div class="mt-2 flex items-center justify-center gap-2 text-sm text-red-500">
                            <x-icon icon="alert-circle" class="h-4 w-4" />
                            <span>{{ $filter_error }}</span>
                        </div>
                    @endif

                    <div class="flex flex-col gap-4 sm:flex-row">
                        <div class="flex-1">
                            <x-input type="date" name="emisionInicio" label="Fecha de emisión desde"
                                wire:model.live.debounce.500ms="emisionInicio" />
                        </div>
                        <div class="flex-1">
                            <x-input type="date" name="emisionFin" label="Fecha de emisión hasta"
                                wire:model.live.debounce.500ms="emisionFin" />
                        </div>
                    </div>

                    <div class="flex flex-col gap-4 sm:flex-row">
                        <div class="flex-1">
                            <x-input type="date" name="fechaInicio" label="Fecha de procesamiento desde"
                                wire:model.live.debounce.500ms="fechaInicio" />
                        </div>
                        <div class="flex-1">
                            <x-input type="date" name="fechaFin" label="Fecha de procesamiento hasta"
                                wire:model.live.debounce.500ms="fechaFin" />
                        </div>
                    </div>

                    <div class="flex flex-col gap-4 sm:flex-row">
                        <div class="flex-1">
                            <x-input type="text" name="q" label="Búsqueda rápida" icon="search"
                                placeholder="Código, receptor, documento" wire:model.live.debounce.500ms="q" />
                        </div>
                        <div class="flex-1">
                            <x-select id="documento_receptor" name="documento_receptor" label="Receptor"
                                :options="$receptores_unicos" :search="false"
                                wire:model.live.debounce.500ms="documento_receptor" />
                        </div>
                    </div>

                    <div class="flex flex-col gap-4 sm:flex-row">
                        @if (!$only_fcf)
                            <div class="flex-1">
                                <x-select id="tipo_dte" name="tipo_dte" label="Tipo de DTE" :options="$dte_options"
                                    :search="false" wire:model.live.debounce.500ms="tipo_dte" />
                            </div>
                        @endif
                        <div class="flex-1">
                            <x-select id="estado" name="estado" label="Estado" :options="[
                                '' => 'Todos',
                                'PROCESADO' => 'Procesado',
                                'RECHAZADO' => 'Rechazado',
                                'CONTINGENCIA' => 'Contingencia',
                                'ANULADO' => 'Anulado',
                            ]" :search="false" wire:model.live.debounce.500ms="estado" />
                        </div>
                        <div class="flex-1">
                            <x-select id="condicion_operacion" name="condicion_operacion"
                                label="Condición de operación" :options="[
                                    '' => 'Todas',
                                    '1' => 'Contado',
                                    '2' => 'Crédito',
                                    '3' => 'Otro',
                                ]" :search="false" wire:model.live.debounce.500ms="condicion_operacion" />
                        </div>
                    </div>

                    @if (!$only_default_pos && !Session::has('sucursal'))
                        <div class="flex flex-col gap-4 sm:flex-row">
                            <div class="flex-1">
                                <x-select id="codSucursal" name="codSucursal" label="Sucursal"
                                    :options="$sucursal_options" wire:model.live.debounce.500ms="codSucursal" />
                            </div>
                            <div class="flex-1">
                                <x-select id="codPuntoVenta" name="codPuntoVenta" label="Punto de venta"
                                    :options="$punto_venta_options" wire:model.live.debounce.500ms="codPuntoVenta" />
                            </div>
                        </div>
                    @endif

                    <div class="flex flex-col gap-4 sm:flex-row">
                        <div class="flex-1">
                            <x-input type="number" name="minMonto" label="Monto mínimo" placeholder="0.00"
                                wire:model.live.debounce.500ms="minMonto" />
                        </div>
                        <div class="flex-1">
                            <x-input type="number" name="maxMonto" label="Monto máximo" placeholder="0.00"
                                wire:model.live.debounce.500ms="maxMonto" />
                        </div>
                    </div>

                    <div class="flex justify-center">
                        <x-button type="button" wire:click="clearFilters" typeButton="info" text="Limpiar filtros" />
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="flex items-center gap-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <span class="rounded-full bg-blue-100 p-3 dark:bg-blue-950/30">
                    <x-icon icon="currency-dollar" class="size-6 text-blue-500" />
                </span>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-300">Ventas globales</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ $this->formatMoney($dashboard['stats']['total_ventas'] ?? 0) }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <span class="rounded-full bg-green-100 p-3 dark:bg-green-950/30">
                    <x-icon icon="trending-up" class="size-6 text-green-500" />
                </span>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-300">Venta Promedio</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ $this->formatMoney($dashboard['stats']['ticket_promedio'] ?? 0) }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <span class="rounded-full bg-purple-100 p-3 dark:bg-purple-950/30">
                    <x-icon icon="files" class="size-6 text-purple-500" />
                </span>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-300">Documentos procesados</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ $dashboard['stats']['total_documentos'] ?? 0 }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <span class="rounded-full bg-amber-100 p-3 dark:bg-amber-950/30">
                    <x-icon icon="users" class="size-6 text-amber-500" />
                </span>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-300">Clientes únicos</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ $dashboard['stats']['clientes_unicos'] ?? 0 }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <span class="rounded-full bg-rose-100 p-3 dark:bg-rose-950/30">
                    <x-icon icon="shopping-cart" class="size-6 text-rose-500" />
                </span>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-300">Compras a sujeto excluido</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ $this->formatMoney($dashboard['stats']['compras_sujeto_excluido'] ?? 0) }}
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h3 class="mb-3 flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-200">
                    <x-icon icon="cash-banknote" class="size-4" />
                    Composición de ventas
                </h3>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500 dark:text-gray-300">Gravadas</span>
                        <span class="font-semibold text-gray-900 dark:text-white">
                            {{ $this->formatMoney($dashboard['stats']['ventas_gravadas'] ?? 0) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500 dark:text-gray-300">Exentas</span>
                        <span class="font-semibold text-gray-900 dark:text-white">
                            {{ $this->formatMoney($dashboard['stats']['ventas_exentas'] ?? 0) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500 dark:text-gray-300">No sujetas</span>
                        <span class="font-semibold text-gray-900 dark:text-white">
                            {{ $this->formatMoney($dashboard['stats']['ventas_no_sujetas'] ?? 0) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h3 class="mb-3 flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-200">
                    <x-icon icon="cash-register" class="size-4" />
                    Condición de operación
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-300">Contado</span>
                        <span class="font-semibold text-gray-900 dark:text-white">
                            {{ $this->formatMoney($dashboard['stats']['total_contado'] ?? 0) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-300">Crédito</span>
                        <span class="font-semibold text-gray-900 dark:text-white">
                            {{ $this->formatMoney($dashboard['stats']['total_credito'] ?? 0) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-300">Otros</span>
                        <span class="font-semibold text-gray-900 dark:text-white">
                            {{ $this->formatMoney($dashboard['stats']['total_otro'] ?? 0) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h3 class="mb-3 flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-200">
                    <x-icon icon="alert-circle" class="size-4" />
                    Estados de documentos
                </h3>
                <div class="space-y-2">
                    @forelse ($dashboard['ventas_por_estado'] ?? [] as $estado => $monto)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-300">{{ $estado ?: 'Sin estado' }}</span>
                            <span class="font-semibold text-gray-900 dark:text-white">
                                {{ $this->formatMoney($monto) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">Sin datos disponibles.</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="mt-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h3 class="mb-3 flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-200">
                <x-icon icon="chartline" class="size-4" />
                Ventas por tipo de DTE
            </h3>
            <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($dashboard['ventas_por_tipo'] ?? [] as $row)
                    <div class="flex items-center justify-between rounded-lg border border-gray-200 p-3 text-sm dark:border-gray-700">
                        <span class="text-gray-500 dark:text-gray-300">{{ $row['nombre'] }}</span>
                        <span class="font-semibold text-gray-900 dark:text-white">
                            {{ $this->formatMoney($row['total']) }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">Sin datos disponibles.</p>
                @endforelse
            </div>
        </div>

        <div class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-2">
            <x-card title="Ventas por sucursal" icon="building-store" :collapsible="false">
                <x-table class="space-y-2">
                    <x-slot name="thead">
                        <x-tr>
                            <x-th>Sucursal</x-th>
                            <x-th>Documentos</x-th>
                            <x-th :last="true">Ventas</x-th>
                        </x-tr>
                    </x-slot>
                    <x-slot name="tbody">
                        @forelse ($dashboard['ventas_por_sucursal'] ?? [] as $row)
                            <x-tr>
                                <x-td>{{ $row['nombre'] }}</x-td>
                                <x-td>{{ $row['cantidad'] }}</x-td>
                                <x-td :last="true">{{ $this->formatMoney($row['total']) }}</x-td>
                            </x-tr>
                        @empty
                            <x-tr>
                                <x-td colspan="3">Sin datos disponibles.</x-td>
                            </x-tr>
                        @endforelse
                    </x-slot>
                </x-table>
            </x-card>

            <x-card title="Ventas por punto de venta" icon="device-pos" :collapsible="false">
                <x-table class="space-y-2">
                    <x-slot name="thead">
                        <x-tr>
                            <x-th>Punto de venta</x-th>
                            <x-th>Documentos</x-th>
                            <x-th :last="true">Ventas</x-th>
                        </x-tr>
                    </x-slot>
                    <x-slot name="tbody">
                        @forelse ($dashboard['ventas_por_punto'] ?? [] as $row)
                            <x-tr>
                                <x-td>{{ $row['nombre'] }}</x-td>
                                <x-td>{{ $row['cantidad'] }}</x-td>
                                <x-td :last="true">{{ $this->formatMoney($row['total']) }}</x-td>
                            </x-tr>
                        @empty
                            <x-tr>
                                <x-td colspan="3">Sin datos disponibles.</x-td>
                            </x-tr>
                        @endforelse
                    </x-slot>
                </x-table>
            </x-card>
        </div>

        <div class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-2">
            <x-card title="Top 10 productos/servicios" icon="package" :collapsible="false">
                <x-table class="space-y-2">
                    <x-slot name="thead">
                        <x-tr>
                            <x-th>Código</x-th>
                            <x-th>Descripción</x-th>
                            <x-th>Cantidad</x-th>
                            <x-th :last="true">Ventas</x-th>
                        </x-tr>
                    </x-slot>
                    <x-slot name="tbody">
                        @forelse ($dashboard['ventas_por_producto'] ?? [] as $row)
                            <x-tr>
                                <x-td>{{ $row['codigo'] }}</x-td>
                                <x-td>{{ $row['descripcion'] }}</x-td>
                                <x-td>{{ number_format($row['cantidad'], 2, '.', ',') }}</x-td>
                                <x-td :last="true">{{ $this->formatMoney($row['total']) }}</x-td>
                            </x-tr>
                        @empty
                            <x-tr>
                                <x-td colspan="4">Sin datos disponibles.</x-td>
                            </x-tr>
                        @endforelse
                    </x-slot>
                </x-table>
            </x-card>

            <x-card title="Top 10 clientes" icon="users-group" :collapsible="false">
                <x-table class="space-y-2">
                    <x-slot name="thead">
                        <x-tr>
                            <x-th>Documento</x-th>
                            <x-th>Cliente</x-th>
                            <x-th>Documentos</x-th>
                            <x-th :last="true">Ventas</x-th>
                        </x-tr>
                    </x-slot>
                    <x-slot name="tbody">
                        @forelse ($dashboard['ventas_por_cliente'] ?? [] as $row)
                            <x-tr>
                                <x-td>{{ $row['documento'] }}</x-td>
                                <x-td>{{ $row['nombre'] }}</x-td>
                                <x-td>{{ $row['cantidad'] }}</x-td>
                                <x-td :last="true">{{ $this->formatMoney($row['total']) }}</x-td>
                            </x-tr>
                        @empty
                            <x-tr>
                                <x-td colspan="4">Sin datos disponibles.</x-td>
                            </x-tr>
                        @endforelse
                    </x-slot>
                </x-table>
            </x-card>
        </div>
    </div>
</section>
