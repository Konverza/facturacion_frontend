@extends('layouts.auth-template')
@section('title', 'Movimientos')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full justify-between">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Movimientos
            </h1>
        </div>
        <div class="mt-4 pb-4">
            <div class="mb-4 flex w-full flex-col gap-4 sm:flex-row">
                <div class="flex-[6]">
                    <x-input type="text" placeholder="Buscar" class="w-full" icon="search" id="input-search-data" />
                </div>
            </div>
            <x-table id="table-data">
                <x-slot name="thead">
                    <x-tr>
                        <x-th class="w-10">#</x-th>
                        <x-th>Factura</x-th>
                        <x-th>Tipo</x-th>
                        <x-th>Cantidad</x-th>
                        <x-th>Precio unitario</x-th>
                        <x-th>Producto</x-th>
                        <x-th>Descripción</x-th>
                        <x-th :last="true">Accciones</x-th>
                    </x-tr>
                </x-slot>
                <x-slot name="tbody">
                    @foreach ($movimientos as $movimiento)
                        <x-tr :last="$loop->last">
                            <x-td>{{ $loop->iteration }}</x-td>
                            <x-td>{{ $movimiento->numero_factura ?? "Ingreso de stock" }}</x-td>
                            <x-td>
                                @if ($movimiento->tipo === 'salida')
                                    <span
                                        class="flex items-center gap-1 font-bold uppercase text-red-500 dark:text-red-400">
                                        <x-icon icon="line-down" class="h-4 w-4" />
                                        {{ $movimiento->tipo }}
                                    </span>
                                @else
                                    <span
                                        class="flex items-center gap-1 font-semibold uppercase text-green-500 dark:text-green-400">
                                        <x-icon icon="line-up" class="h-4 w-4" />
                                        {{ $movimiento->tipo }}
                                    </span>
                                @endif
                            </x-td>
                            <x-td>{{ $movimiento->cantidad }}</x-td>
                            <x-td>${{ number_format($movimiento->precio_unitario, 2) }}</x-td>
                            <x-td>{{ $movimiento->producto }}</x-td>
                            <x-td>{{ $movimiento->descripcion }}</x-td>
                            <x-td :last="true">
                                <div class="relative">
                                    <x-button type="button" icon="arrow-down" typeButton="primary" text="Acciones"
                                        class="show-options" data-target="#options-movimientos-{{ $movimiento->id }}"
                                        size="small" />
                                    <div class="options absolute right-0 top-0 z-10 mt-1 hidden w-max rounded-lg border border-gray-300 bg-white p-1 dark:border-gray-800 dark:bg-gray-950"
                                        id="options-movimientos-{{ $movimiento->id }}">
                                        <ul class="flex flex-col text-xs">
                                            @if ($movimiento->numero_factura && $movimiento->invoice)
                                                <li>
                                                    <a href="{{ $movimiento->invoice['enlace_pdf'] }}" target="_blank"
                                                        class="flex w-full items-center gap-1 rounded-lg p-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                                        <x-icon icon="pdf" class="h-4 w-4" />
                                                        Ver factura
                                                    </a>
                                                </li>
                                            @endif
                                            <li>
                                                <form action="{{ Route('business.movements.destroy', $movimiento->id) }}"
                                                    method="POST" id="form-delete-movimiento-{{ $movimiento->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        data-form="form-delete-movimiento-{{ $movimiento->id }}"
                                                        data-modal-toggle="deleteModal" data-modal-target="deleteModal"
                                                        class="buttonDelete flex w-full items-center gap-1 rounded-lg px-2 py-2 text-red-500 hover:bg-red-100 dark:text-red-500 dark:hover:bg-red-950/30">
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

        <x-delete-modal modalId="deleteModal" title="¿Estás seguro de eliminar el movimiento?"
            message="No podrás recuperar este registro" />

    </section>
@endsection
