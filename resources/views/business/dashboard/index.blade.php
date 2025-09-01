@extends('layouts.auth-template')
@section('title', 'Dashboard')
@section('content')
    @php
        $types = [
            '01' => 'Factura Consumidor Final',
            '03' => 'Comprobante de crédito fiscal',
            '04' => 'Nota de Remisión',
            '05' => 'Nota de crédito',
            '06' => 'Nota de débito',
            '07' => 'Comprobante de retención',
            '11' => 'Factura de exportación',
            '14' => 'Factura de sujeto excluido',
            '15' => 'Comprobante de Donación',
        ];
    @endphp
    <section class="my-4 px-4 pb-4">
        <div class="flex flex-wrap justify-between gap-4">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Bienvenido, {{ Auth::user()->name }}
            </h1>
            <div class="mt-4 flex w-full flex-col items-center gap-2 sm:mt-0 sm:w-auto sm:flex-row">
                @include('layouts.partials.business.button-new-dte')
                <x-button type="a" href="{{ Route('business.customers.create') }}" typeButton="secondary"
                    text="Nuevo cliente" icon="user-plus" class="w-full sm:w-auto" />
                <x-button type="a" href="{{ Route('business.products.create') }}" typeButton="warning"
                    text="Nuevo producto" icon="cube-plus" class="w-full sm:w-auto" />
            </div>
        </div>
        @php
            // Asegura que $statistics_by_dte['statistics'] siempre sea un array
            $statistics_by_dte = $statistics_by_dte ?? [];
            $statistics_by_dte['statistics'] = $statistics_by_dte['statistics'] ?? [];
        @endphp
        <div class="flex flex-col flex-wrap gap-4 md:flex-row">
            <div class="flex flex-1 flex-col">
                <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <div
                        class="flex w-full flex-1 items-center justify-center gap-4 rounded-lg border border-gray-300 p-4 dark:border-gray-800 dark:bg-gray-950">
                        <span class="rounded-full bg-blue-100 p-4 dark:bg-blue-950/30">
                            <x-icon icon="files" class="size-10 text-blue-500 sm:size-12" />
                        </span>
                        <div class="flex flex-col items-center justify-center gap-1 sm:items-start">
                            <p class="text-2xl font-bold text-blue-500">
                                {{ $statistics['approved'] }} de {{ $business_plan->plan->limite }}
                            </p>
                            <h1 class="text-sm text-gray-500 dark:text-gray-300">
                                Documentos emitidos
                            </h1>
                            <h1 class="text-sm text-gray-500 dark:text-gray-300">
                                ({{ \Carbon\Carbon::parse($inicio_mes)->format('d/m/Y') }} -
                                {{ \Carbon\Carbon::parse($fin_mes)->format('d/m/Y') }})
                            </h1>
                            <x-button type="a" href="{{ Route('business.documents.index') }}" typeButton="info"
                                text="Ver documentos" size="normal" />
                        </div>
                    </div>
                    <div
                        class="flex w-full flex-1 items-center justify-center gap-4 rounded-lg border border-gray-300 p-4 dark:border-gray-800 dark:bg-gray-950">
                        <!-- TODO: Actualizar estado según API -->
                        <span class="rounded-full bg-green-100 p-4 dark:bg-green-950/30">
                            <x-icon icon="circle-check" class="size-10 text-green-500 sm:size-12" />
                        </span>
                        <div class="flex flex-col items-center justify-center gap-1 sm:items-start">
                            <p class="text-2xl font-bold text-green-500">
                                Operativo
                            </p>
                            <h1 class="text-sm text-gray-500 dark:text-gray-300">
                                Estado hacienda
                            </h1>
                        </div>
                    </div>
                    <div
                        class="flex w-full flex-1 items-center justify-center gap-4 rounded-lg border border-gray-300 p-4 dark:border-gray-800 dark:bg-gray-950">
                        <span class="rounded-full bg-gray-100 p-4 dark:bg-gray-900">
                            <x-icon icon="users" class="size-10 text-gray-500 sm:size-12" />
                        </span>
                        <div class="flex flex-col items-center justify-center gap-1 sm:items-start">
                            <p class="text-4xl font-bold text-gray-500">
                                {{ $customers }}
                            </p>
                            <h1 class="text-sm text-gray-500 dark:text-gray-300">
                                Clientes registrados
                            </h1>
                            <x-button type="a" href="{{ Route('business.customers.index') }}" typeButton="secondary"
                                text="Ver clientes" size="normal" />
                        </div>
                    </div>
                    <div
                        class="flex w-full flex-1 items-center justify-center gap-4 rounded-lg border border-gray-300 p-4 dark:border-gray-800 dark:bg-gray-950">
                        <span class="rounded-full bg-yellow-100 p-4 dark:bg-yellow-950/30">
                            <x-icon icon="box" class="size-10 text-yellow-500 sm:size-12" />
                        </span>
                        <div class="flex flex-col items-center justify-center gap-1 sm:items-start">
                            <p class="text-4xl font-bold text-yellow-500">
                                {{ $products }}
                            </p>
                            <h1 class="text-sm text-gray-500 dark:text-gray-300">
                                Productos registrados
                            </h1>
                            <x-button type="a" href="{{ Route('business.products.index') }}" typeButton="warning"
                                text="Ver productos" size="normal" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if (session('ambiente') == '2')
            <div class="my-4 flex flex-col gap-4 xl:flex-row">
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-gray-600 dark:text-white">
                        Progreso de pruebas
                    </h2>
                    <div class="mt-2">
                        <x-table id="table-progress-tests" :datatable="false">
                            <x-slot name="thead">
                                <x-tr>
                                    <x-th>
                                        Tipo de Documento
                                    </x-th>
                                    <x-th :last="true">
                                        Progreso
                                    </x-th>
                                </x-tr>
                            </x-slot>
                            <x-slot name="tbody">
                                @php
                                    $aprobados_total = 0;
                                    $dtes_habilitados = json_decode($business_plan['dtes']) ?? [];
                                @endphp
                                @foreach ($dtes_habilitados as $item)
                                    @php
                                        $statistics_item = array_search(
                                            $item,
                                            array_column($statistics_by_dte['statistics'], 'tipo_dte'),
                                        );
                                        if ($statistics_item !== false) {
                                            $dte_actual = $statistics_by_dte['statistics'][$statistics_item];
                                            $aprobados_total += $dte_actual['aprobados'] >= 5 ? 1 : 0;
                                        } else {
                                            $dte_actual = null;
                                        }
                                    @endphp
                                    <x-tr :last="$loop->last">
                                        <x-td class="w-1/3">
                                            <span class="text-zinc-800 dark:text-zinc-100">
                                                {{ $types[$item] }} ({{ $dte_actual ? $dte_actual['aprobados'] : 0 }}/5)
                                            </span>
                                        </x-td>
                                        <x-td :last="true">
                                            @php
                                                $progress_percentage =
                                                    (($dte_actual ? $dte_actual['aprobados'] : 0) / 5) * 100;
                                                $progress_percentage = min($progress_percentage, 100); // No puede ser mayor al 100%
                                                // Definir colores según el porcentaje
                                                if ($progress_percentage < 50) {
                                                    $bar_color = 'bg-red-500';
                                                    $icon_color = 'text-red-500';
                                                } elseif ($progress_percentage < 100) {
                                                    $bar_color = 'bg-yellow-500';
                                                    $icon_color = 'text-yellow-500';
                                                } else {
                                                    $bar_color = 'bg-green-500';
                                                    $icon_color = 'text-green-500';
                                                }
                                            @endphp
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="flex-1 h-3 w-full rounded-full bg-secondary-200 dark:bg-secondary-900">
                                                    <div class="h-full rounded-full {{ $bar_color }}"
                                                        style="width: {{ $progress_percentage }}%;"></div>
                                                </div>
                                                <span class="text-xs text-zinc-500 dark:text-zinc-400 min-w-max">
                                                    {{ number_format($progress_percentage, 1) }}%
                                                </span>
                                                @if ($progress_percentage >= 100)
                                                    <span
                                                        class="flex items-center gap-1 text-nowrap rounded-lg border border-green-300 bg-green-200 px-2 py-1 text-xs font-light uppercase text-green-800 dark:border-green-900 dark:bg-green-900/50 dark:text-green-300">
                                                        <x-icon icon="circle-check" class="size-4" />
                                                        Completado - Listo para producción
                                                    </span>
                                                @else
                                                    <span
                                                        class="flex items-center gap-1 text-nowrap rounded-lg border border-yellow-300 bg-yellow-200 px-2 py-1 text-xs font-light uppercase text-yellow-800 dark:border-yellow-900 dark:bg-yellow-900/50 dark:text-yellow-300">
                                                        <x-icon icon="clock" class="size-4" />
                                                        En progreso - Mínimo 5 aprobados
                                                    </span>
                                                @endif
                                            </div>
                                        </x-td>
                                    </x-tr>
                                @endforeach
                            </x-slot>
                        </x-table>
                        {{-- Centered button that says "Solicitar Acceso a Producción" --}}
                        <div class="flex mt-4 justify-center">
                            @if ($aprobados_total >= count($dtes_habilitados) && count($dtes_habilitados) > 0)
                                <x-button type="a" href="{{ Route('business.customers.create') }}"
                                    typeButton="success" text="Solicitar Acceso a Producción" icon="arrow-up"
                                    class="w-auto" />
                            @else
                                <div
                                    class="my-4 rounded-lg border border-dashed border-yellow-500 bg-yellow-100 p-4 text-yellow-500 dark:bg-yellow-950/30">
                                    Aún no se ha completado todas las pruebas necesarias para solicitar el acceso a
                                    producción.<br>
                                    Tipos de DTE faltantes: {{ count($dtes_habilitados) - $aprobados_total }}.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="my-4 flex flex-col gap-4 xl:flex-row">
            <div class="flex-[2]">
                <h2 class="text-2xl font-bold text-gray-600 dark:text-white">
                    Últimos documentos emitidos
                </h2>
                <div class="mt-2">
                    <x-table id="table-dtes-dashboard" :datatable="false">
                        <x-slot name="thead">
                            <x-tr>
                                <x-th class="w-10">
                                    #
                                </x-th>
                                <x-th>
                                    Tipo de documento
                                </x-th>
                                <x-th>
                                    Fecha de emisión
                                </x-th>
                                <x-th>
                                    Código de generación
                                </x-th>
                                <x-th :last="true">
                                    Estado
                                </x-th>
                            </x-tr>
                        </x-slot>
                        <x-slot name="tbody">
                            @if (count($dtes['items']) > 0)
                                {{-- Grab first 5 $dte --}}
                                @foreach ($dtes['items'] as $item)
                                    <x-tr :last="$loop->last">
                                        <x-td>
                                            {{ $loop->iteration }}
                                        </x-td>
                                        <x-td>
                                            {{ $types[$item['tipo_dte']] }}
                                        </x-td>
                                        <x-td>
                                            {{ \Carbon\Carbon::parse($item['fhProcesamiento'])->format('d/m/Y h:i:s a') }}
                                        </x-td>
                                        <x-td>
                                            {{ $item['codGeneracion'] }}
                                        </x-td>
                                        <x-td :last="true">
                                            @if ($item['estado'] == 'PROCESADO')
                                                <span
                                                    class="flex w-max items-center gap-1 rounded-full px-2 py-1 text-sm font-semibold text-green-500">
                                                    <x-icon icon="check" class="size-5 text-green-500" />
                                                    {{ $item['estado'] }}
                                                </span>
                                            @elseif($item['estado'] == 'RECHAZADO')
                                                <span
                                                    class="flex w-max items-center gap-1 rounded-full px-2 py-1 text-sm font-semibold text-red-500">
                                                    <x-icon icon="x" class="size-5 text-red-500" />
                                                    {{ $item['estado'] }}
                                                </span>
                                            @elseif($item['estado'] == 'CONTINGENCIA')
                                                <span
                                                    class="flex w-max items-center gap-1 rounded-full px-2 py-1 text-sm font-semibold text-yellow-500">
                                                    <x-icon icon="clock" class="size-5 text-yellow-500" />
                                                    {{ $item['estado'] }}
                                                </span>
                                            @else
                                                <span
                                                    class="flex w-max items-center gap-1 rounded-full px-2 py-1 text-sm font-semibold text-gray-500">
                                                    <x-icon icon="file-x" class="size-5 text-gray-500" />
                                                    {{ $item['estado'] }}
                                                </span>
                                            @endif
                                        </x-td>
                                    </x-tr>
                                @endforeach
                            @else
                                <x-tr>
                                    <x-td colspan="5" class="text-center py-8">
                                        <div class="flex flex-col items-center gap-3">
                                            <x-icon icon="info-circle" class="size-12 text-gray-400 dark:text-gray-600" />
                                            <div class="text-gray-500 dark:text-gray-400">
                                                <p class="text-lg font-medium">No hay DTEs</p>
                                                <p class="text-sm">Aún no ha emitido DTEs</p>
                                            </div>
                                        </div>
                                    </x-td>
                                </x-tr>
                            @endif
                        </x-slot>
                    </x-table>
                </div>
            </div>
            <div class="h-max flex-1">
                <h2 class="text-2xl font-bold text-gray-600 dark:text-white">
                    Datos de la empresa:
                </h2>
                <div class="mt-2 rounded-lg border border-gray-300 p-4 dark:border-gray-800 dark:bg-gray-950">
                    <div class="flex items-start gap-1 text-left text-sm">
                        <p class="text-gray-500 dark:text-gray-400">
                            <b> NIT:</b>
                            {{ $datos_empresa['nit'] }}
                        </p>
                    </div>
                    <div class="flex items-start gap-1 text-sm">
                        <p class="text-gray-500 dark:text-gray-400">
                            <b>NRC:</b>
                            {{ $datos_empresa['nrc'] }}
                        </p>
                    </div>
                    <div class="flex flex-wrap items-start text-left text-sm">
                        <p class="text-gray-500 dark:text-gray-400">
                            <b>Nombre comercial:</b>
                            {{ $datos_empresa['nombreComercial'] }}
                        </p>
                    </div>
                    <div class="flex items-start gap-1 text-wrap text-sm">
                        <p class="text-gray-500 dark:text-gray-400">
                            <b>Giro:</b>
                            {{ $datos_empresa['descActividad'] }}
                        </p>
                    </div>
                    <div class="flex items-start gap-1 text-wrap text-sm">
                        <p class="text-gray-500 dark:text-gray-400">
                            <b>Dirección:</b>
                            {{ $datos_empresa['complemento'] }}
                        </p>
                    </div>
                    <div class="flex items-start gap-1 text-sm">
                        <p class="text-gray-500 dark:text-gray-400">
                            <b>Teléfono:</b>
                            {{ $datos_empresa['telefono'] }}
                        </p>
                    </div>
                    <div class="flex items-start gap-1 text-sm">
                        <p class="text-gray-500 dark:text-gray-400">
                            <b>Correo electrónico:</b>
                            {{ $datos_empresa['correo'] }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @if ($dtes_pending->count() > 0)
            <div class="w-full">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-gray-500 dark:text-gray-400">
                        Tienes {{ $dtes_pending->count() }} DTE{{ $dtes_pending->count() > 1 ? '(s)' : '' }}
                        pendiente
                        de
                        generar
                    </h2>
                    <form action="{{ Route('business.delete-all-dte') }}" method="POST" id="form-delete-all">
                        @csrf
                        @method('DELETE')
                        <x-button type="button" text="Eliminar todos" icon="trash" typeButton="danger"
                            class="buttonDelete" data-modal-toggle="deleteAllModal" data-modal-target="deleteAllModal"
                            data-form="form-delete-all" />
                    </form>
                </div>
                <div class="mt-4 flex flex-wrap gap-4">
                    @if ($dtes_pending->count() > 0)
                        @foreach ($dtes_pending as $dte)
                            @php
                                $style = '';
                                if ($dte->status === 'pending') {
                                    $style =
                                        'bg-yellow-100 dark:bg-yellow-950/30 border border-dashed border-yellow-500 dark:border-yellow-700';
                                } else {
                                    $style =
                                        'bg-red-100 dark:bg-red-950/30 border border-dashed border-red-500 dark:border-red-700';
                                }
                            @endphp
                            <div
                                class="{{ $style }} flex flex-col items-start justify-between gap-8 rounded-lg p-4 sm:flex-row sm:items-center">
                                <div class="flex items-start gap-2">
                                    @if ($dte->status === 'pending')
                                        <span class="rounded-full bg-yellow-500 p-2">
                                            <x-icon icon="file-report" class="size-6 text-white" />
                                        </span>
                                    @else
                                        <span class="rounded-full bg-red-500 p-2">
                                            <x-icon icon="file-x" class="size-6 text-white" />
                                        </span>
                                    @endif
                                    <div class="flex flex-col">
                                        @if ($dte->status === 'pending')
                                            <p class="text-lg font-semibold text-yellow-500">
                                                {{ $types[$dte->type] }}
                                            </p>
                                        @else
                                            <p class="text-lg font-semibold text-red-500">
                                                {{ $types[$dte->type] }}
                                            </p>
                                        @endif
                                        @if ($dte->status === 'pending')
                                            <p class="text-sm text-yellow-500">
                                                Pendiente
                                            </p>
                                        @else
                                            <p class="text-sm text-red-500">
                                                Rechazado
                                            </p>
                                        @endif
                                        <p
                                            class="flex items-start gap-1 text-sm font-semibold text-gray-500 dark:text-gray-400">
                                            <x-icon icon="info-circle"
                                                class="text-seFcondary-500 mt-1 size-4 min-w-4 max-w-4 dark:text-secondary-300" />
                                            @if ($dte->error_message)
                                                @php
                                                    $errors = json_decode($dte->error_message);
                                                @endphp
                                                @if (is_array($errors))
                                                    @foreach ($errors as $error)
                                                        {{ $error }}
                                                    @endforeach
                                                @else
                                                    {{ $dte->error_message }}
                                                @endif
                                            @endif
                                        </p>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            Fecha de emisión:
                                            {{ \Carbon\Carbon::parse($dte->created_at)->format('d/m/Y h:i:s A') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex flex-row gap-2 sm:flex-col">
                                    <form action="{{ Route('business.delete-dte', $dte->id) }}" method="POST"
                                        id="form-delete-dte-{{ $dte->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <x-button type="button" icon="trash" typeButton="danger" class="buttonDelete"
                                            data-modal-toggle="deleteModal" data-modal-target="deleteModal"
                                            data-form="form-delete-dte-{{ $dte->id }}" onlyIcon />
                                    </form>
                                    <x-button type="a" href="{!! Route('business.dte.create', ['document_type' => $dte->type]) . '&id=' . $dte->id !!}" icon="arrow-right"
                                        typeButton="secondary" onlyIcon />
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        @endif

        <div class="my-8 flex flex-col gap-4">
            <x-ad-carousel :ads="$ads" />
        </div>

        <x-delete-modal modalId="deleteModal" title="¿Estás seguro de eliminar el DTE?"
            message="No podrás recuperar este registro" />
        <x-delete-modal modalId="deleteAllModal" title="¿Estás seguro de eliminar todos los DTEs pendientes?"
            message="No podrás recuperar estos registros" />
    </section>
@endsection
