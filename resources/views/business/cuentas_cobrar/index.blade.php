@extends('layouts.auth-template')
@section('title', 'Negocios')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full justify-between">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Cuentas por cobrar
            </h1>
        </div>
        <div class="mt-4">
            <div class="mb-4 flex w-full flex-col gap-4 sm:flex-row">
                <div class="flex-[4]">
                    <x-input type="text" placeholder="Buscar usuario" class="w-full" icon="search" id="input-search-data" />
                </div>
                <div class="flex-1">
                    <x-button type="button" icon="plus" typeButton="primary" data-target="#add-account"
                        text="Nueva cuenta" class="show-modal w-full" />
                </div>
            </div>
            <x-table id="table-data">
                <x-slot name="thead">
                    <x-tr>
                        <x-th class="w-10">#</x-th>
                        <x-th>Número de factura</x-th>
                        <x-th>Cliente</x-th>
                        <x-th>Fecha vencimiento</x-th>
                        <x-th>Total</x-th>
                        <x-th>Monto pendiente</x-th>
                        <x-th>Estado</x-th>
                        <x-th :last="true">Accciones</x-th>
                    </x-tr>
                </x-slot>
                <x-slot name="tbody">
                    @foreach ($cuentas as $cuenta)
                        <x-tr :last="$loop->last">
                            <x-td>{{ $loop->iteration }}</x-td>
                            <x-td>{{ $cuenta->numero_factura ?? '' }}</x-td>
                            <x-td>{{ $cuenta->cliente }}</x-td>
                            <x-td>{{ $cuenta->created_at->format('d/m/Y H:i A') }}</x-td>
                            <x-td>${{ $cuenta->monto }}</x-td>
                            <x-td>${{ $cuenta->saldo }}</x-td>
                            <x-td>
                                @if ($cuenta->estado == 'pendiente')
                                    <span class="flex items-center gap-1 text-sm font-semibold text-yellow-500">
                                        <x-icon icon="clock" class="h-4 w-4" />
                                        Pendiente
                                    </span>
                                @elseif($cuenta->estado == 'parcial')
                                    <span class="flex items-center gap-1 text-sm font-semibold text-blue-500">
                                        <x-icon icon="currency-dollar" class="h-4 w-4" />
                                        Parcial
                                    </span>
                                @else
                                    <span class="flex items-center gap-1 text-sm font-semibold text-green-500">
                                        <x-icon icon="check" class="h-4 w-4" />
                                        Pagado
                                    </span>
                                @endif
                            </x-td>
                            <x-td :last="true">
                                <div class="relative">
                                    <x-button type="button" icon="arrow-down" typeButton="primary" text="Acciones"
                                        class="show-options" data-target="#options-dtes-1" size="small" />
                                    <div class="options absolute right-0 top-0 z-10 mt-1 hidden w-max rounded-lg border border-gray-300 bg-white p-1 dark:border-gray-800 dark:bg-gray-950"
                                        id="options-dtes-1">
                                        <ul class="flex flex-col text-xs">
                                            @if ($cuenta->invoice)
                                                <li>
                                                    <a href="{{ $cuenta->invoice['enlace_pdf'] }}" target="_blank"
                                                        class="flex w-full items-center gap-1 rounded-lg p-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                                        <x-icon icon="pdf" class="h-4 w-4" />
                                                        Ver factura
                                                    </a>
                                                </li>
                                            @endif
                                            @if ($cuenta->estado == 'pendiente' || $cuenta->estado == 'parcial')
                                                <li>
                                                    <button type="button" data-id="{{ $cuenta->id }}"
                                                        data-amount="{{ $cuenta->saldo }}" data-target="#add-pay"
                                                        class="show-modal btn-add-pay flex w-full items-center gap-1 rounded-lg p-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                                        <x-icon icon="currency-dollar" class="h-4 w-4" />
                                                        Registrar pago
                                                    </button>
                                                </li>
                                            @endif
                                            {{--  <li>
                                                <a href="#" target="_blank"
                                                    class="flex w-full items-center gap-1 text-nowrap rounded-lg p-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                                    <x-icon icon="email-forward" class="h-4 w-4" />
                                                    Enviar recordatorio
                                                </a>
                                            </li> --}}
                                            @if ($cuenta->movements->count() > 0)
                                                <li>
                                                    <button type="button"
                                                        class="btn-show-history flex w-full items-center gap-1 rounded-lg p-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900"
                                                        data-url="{{ Route('business.cuentas-por-cobrar.show', $cuenta->id) }}"
                                                        data-id="{{ $cuenta->id }}">
                                                        <x-icon icon="history" class="h-4 w-4" />
                                                        Ver historial
                                                    </button>
                                                </li>
                                            @endif
                                            <li>
                                                <form
                                                    action="{{ Route('business.cuentas-por-cobrar.destroy', $cuenta->id) }}"
                                                    method="POST" id="form-account-delete-{{ $cuenta->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        data-form="form-account-delete-{{ $cuenta->id }}"
                                                        data-modal-target="deleteModal" data-modal-toggle="deleteModal"
                                                        class="buttonDelete btn-anular-dte flex w-full items-center gap-1 rounded-lg p-2 text-red-500 hover:bg-red-100 dark:text-red-500 dark:hover:bg-red-950/30"
                                                        data-target="#anular-dte">
                                                        <x-icon icon="trash" class="h-4 w-4" />
                                                        Eliminar
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </x-td>
                        </x-tr>
                    @endforeach
                </x-slot>
            </x-table>
        </div>

        <x-delete-modal modalId="deleteModal" title="¿Estás seguro de eliminar la cuenta"
            message="No podrás recuperar este registro" />

        <div id="add-pay" tabindex="-1" aria-hidden="true"
            class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
            <div class="relative max-h-full w-full max-w-lg p-4">
                <!-- Modal content -->
                <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                    <div class="flex flex-col">
                        <!-- Modal header -->
                        <form action="{{ Route('business.cuentas-por-cobrar.movement') }}" method="POST">
                            @csrf
                            <div
                                class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                    Registrar pago
                                </h3>
                                <button type="button"
                                    class="hide-modal ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                                    data-target="#add-pay">
                                    <x-icon icon="x" class="h-5 w-5" />
                                </button>
                            </div>
                            <!-- Modal body -->
                            <div class="flex flex-col gap-4 p-4">
                                <input type="hidden" name="cuenta_id" id="cuenta-id" />
                                <div class="flex items-center gap-4">
                                    <div class="flex-1">
                                        <x-select name="tipo" id="tipo" :options="[
                                            'pago' => 'Pago',
                                            'ajuste' => 'Ajuste',
                                            'cargo_extra' => 'Cargo extra',
                                            'descuento' => 'Descuento',
                                        ]" required label="Tipo"
                                            :search="false" />
                                    </div>
                                    <div class="flex-1">
                                        <x-input type="number" icon="currency-dollar" label="Monto" placeholder="0.00"
                                            name="monto" id="monto" required label="Monto" step="0.01"
                                            min="0.01" />
                                    </div>
                                </div>
                                <x-input type="date" label="Fecha" icon="calendar" placeholder="Fecha de pago"
                                    name="fecha_pago" />
                                <div id="numero-factura-container">
                                    <x-select name="numero_factura" id="numero_factura_movimiento" :options="$dtes"
                                        required label="Factura" />
                                </div>
                                <x-input type="textarea" placeholder="Ingresar observaciones del pago"
                                    name="observaciones" label="Observaciones" />
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                                <x-button type="button" class="hide-modal" text="Cancelar" icon="x"
                                    typeButton="secondary" data-target="#add-pay" />
                                <x-button type="submit" text="Agregar pago" icon="save" typeButton="primary" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="add-account" tabindex="-1" aria-hidden="true"
            class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
            <div class="relative max-h-full w-full max-w-lg p-4">
                <!-- Modal content -->
                <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                    <div class="flex flex-col">
                        <!-- Modal header -->
                        <form action="{{ Route('business.cuentas-por-cobrar.store') }}" method="POST">
                            @csrf
                            <div
                                class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                    Registrar cuenta por cobrar
                                </h3>
                                <button type="button"
                                    class="hide-modal ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                                    data-target="#add-account">
                                    <x-icon icon="x" class="h-5 w-5" />
                                </button>
                            </div>
                            <!-- Modal body -->
                            <div class="flex flex-col gap-4 p-4">
                                <x-select name="numero_factura" id="numero_factura" :options="$dtes" required
                                    label="Factura" />
                                <x-select name="cliente" id="customer" :options="$business_customers->pluck('nombre', 'nombre')->toArray()" required label="Cliente" />
                                <x-input type="number" icon="currency-dollar" label="Monto" placeholder="0.00"
                                    name="monto" required label="Monto" step="0.01" min="0.01" />
                                <x-input type="date" label="Fecha vencimiento" icon="calendar"
                                    placeholder="Fecha de pago" name="fecha_vencimiento" />
                                <x-input type="textarea" placeholder="Ingresar observaciones del pago"
                                    name="observaciones" label="Observaciones" />
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                                <x-button type="button" class="hide-modal" text="Cancelar" icon="x"
                                    typeButton="secondary" data-target="#add-account" />
                                <x-button type="submit" text="Agregar cuenta" icon="save" typeButton="primary" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="drawer-history"
            class="fixed right-0 top-0 z-[300] h-screen w-full translate-x-full overflow-y-auto bg-white p-4 transition-transform dark:bg-gray-950 sm:w-96"
            tabindex="-1" aria-labelledby="drawer-left-label">
            <h5 id="drawer-label"
                class="mb-4 inline-flex items-center text-lg font-semibold text-gray-500 dark:text-gray-400">
                Historial de movimientos
            </h5>
            <button type="button" data-target="#drawer-history" aria-controls="drawer-history"
                class="hide-drawer-left absolute end-2.5 top-2.5 flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white">
                <x-icon icon="x" class="h-5 w-5" />
                <span class="sr-only">Close menu</span>
            </button>
            <div id="history">

            </div>
        </div>

    </section>
@endsection
