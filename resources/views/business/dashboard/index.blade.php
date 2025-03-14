@extends('layouts.auth-template')
@section('title', 'Dashboard')
@section('content')
    @php
        $plan_dtes = json_decode($business_plan->dtes);
        $dte_options = [];
        foreach ($plan_dtes as $dte) {
            $dte_options[$dte] = $types[$dte];
        }
    @endphp

    <section class="my-4 px-4 pb-4">
        <div class="flex flex-wrap justify-between gap-4">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Bienvenido, {{ Auth::user()->name }}
            </h1>
            <div class="mt-4 flex w-full flex-col items-center gap-2 sm:mt-0 sm:w-auto sm:flex-row">
                <x-button type="button" typeButton="info" text="Generar DTE" icon="file-plus" class="w-full sm:w-auto"
                    data-modal-target="generate-new-dte" data-modal-toggle="generate-new-dte" />
                <x-button type="a" href="{{ Route('business.customers.create') }}" typeButton="secondary"
                    text="Nuevo cliente" icon="user-plus" class="w-full sm:w-auto" />
                <x-button type="a" href="{{ Route('business.products.create') }}" typeButton="warning"
                    text="Nuevo producto" icon="cube-plus" class="w-full sm:w-auto" />
            </div>
        </div>
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
                                {{ $statistics['total'] }} de N/A
                            </p>
                            <h1 class="text-sm text-gray-500 dark:text-gray-300">
                                Documentos emitidos
                            </h1>
                            <x-button type="a" href="{{ Route('business.documents.index') }}" typeButton="info"
                                text="Ver documentos" size="normal" />
                        </div>
                    </div>
                    <div
                        class="flex w-full flex-1 items-center justify-center gap-4 rounded-lg border border-gray-300 p-4 dark:border-gray-800 dark:bg-gray-950">
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
                            {{-- Grab first 5 $dte --}}
                            @foreach (array_reverse(array_slice($dtes, -5)) as $item)
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
                        </x-slot>
                    </x-table>
                </div>
            </div>
            <div class="h-max flex-1 rounded-lg border border-gray-300 p-4 dark:border-gray-800 dark:bg-gray-950">
                <h2 class="text-2xl font-bold text-gray-600 dark:text-white">
                    Resumen
                </h2>
                <div class="mt-2">
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
            <div>
                <h2 class="text-2xl font-bold text-gray-500 dark:text-gray-300">
                    Tienes {{ $dtes_pending->count() }} DTE{{ $dtes_pending->count() > 1 ? '(s)' : '' }} pendiente de generar
                </h2>
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
                        class="{{ $style }} mt-4 flex flex-col items-start justify-between gap-4 rounded-lg p-4 sm:flex-row sm:items-center">
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
                                <p class="flex items-start gap-1 text-sm font-semibold text-gray-500 dark:text-gray-300">
                                    <x-icon icon="info-circle" class="size-4 max-w-4 min-w-4 text-gray-500 dark:text-gray-300 mt-1" />
                                    {{ $dte->error_message ?? '' }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-300">
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
                                <x-button type="button" text="Eliminar" icon="trash" typeButton="danger"
                                    class="buttonDelete" data-modal-toggle="deleteModal" data-modal-target="deleteModal"
                                    data-form="form-delete-dte-{{ $dte->id }}" />
                            </form>
                            <x-button type="a" href="{!! Route('business.dte.create', ['document_type' => $dte->type]) . '&id=' . $dte->id !!}" icon="file-arrow-right"
                                typeButton="primary" text="Generar" />
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div id="generate-new-dte" tabindex="-1" aria-hidden="true"
            class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
            <div class="relative max-h-full w-full max-w-lg p-4">
                <!-- Modal content -->
                <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                    <form action="{{ Route('business.dte.create') }}" method="GET" class="flex flex-col">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Generar nuevo DTE
                            </h3>
                            <button type="button"
                                class="ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                                data-modal-hide="generate-new-dte">
                                <x-icon icon="x" class="h-5 w-5" />
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="p-4">
                            <x-select :options="$dte_options" name="document_type" id="document_type"
                                label="¿Qué tipo de documento desea emitir?" :search="false" />
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                            <x-button type="button" text="Cancelar" icon="x" typeButton="secondary"
                                data-modal-hide="generate-new-dte" />
                            <x-button type="submit" text="Generar" icon="file-arrow-right" typeButton="primary" />
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <x-delete-modal modalId="deleteModal" title="¿Estás seguro de eliminar el DTE?"
            message="No podrás recuperar este registro" />
    </section>
@endsection
