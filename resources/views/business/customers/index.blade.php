@extends('layouts.auth-template')
@section('title', 'Negocios')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full justify-between">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Clientes
            </h1>
        </div>
        <div class="mt-4">
            <livewire:business.tables.clients />
        </div>
        {{-- <div class="mt-4">
            <div class="mb-4 flex w-full flex-col gap-4 sm:flex-row">
                <div class="flex-[6]">
                    <x-input type="text" placeholder="Buscar usuario" class="w-full" icon="search" id="input-search-data" />
                </div>
                <div class="flex-1">
                    <x-button type="a" href="{{ Route('business.customers.create') }}" icon="plus"
                        typeButton="primary" text="Nuevo cliente" class="w-full" />
                </div>
            </div>
            <x-table id="table-data">
                <x-slot name="thead">
                    <x-tr>
                        <x-th class="w-10">#</x-th>
                        <x-th>Indetificación</x-th>
                        <x-th>NRC</x-th>
                        <x-th>Nombre</x-th>
                        <x-th :last="true">Accciones</x-th>
                    </x-tr>
                </x-slot>
                <x-slot name="tbody">
                    @foreach ($business_customers as $customer)
                        <x-tr :last="$loop->last">
                            <x-td>{{$loop->iteration}}</x-td>
                            <x-td>
                                {{ $customer->numDocumento }}
                            </x-td>
                            <x-td>
                                {{ $customer->nrc }}
                            </x-td>
                            <x-td>
                                {{ $customer->nombre }} <br>
                                {{ $customer->nombreComercial ? ' (' . $customer->nombreComercial . ')' : '' }}
                                @if ($customer->special_price)
                                <span
                                    class=" mt-1 flex w-max items-center gap-1 text-nowrap rounded-lg bg-green-200 px-2 py-1 text-xs font-bold text-green-800 dark:bg-green-900/50 dark:text-green-300">
                                    <x-icon icon="circle-check" class="size-4" />
                                    Precio especial
                                </span>
                                @endif
                            </x-td>
                            <x-td th :last="true">
                                <div class="relative">
                                    <x-button type="button" icon="arrow-down" typeButton="primary" text="Acciones"
                                        class="show-options" data-target="#options-customers-{{ $customer->id }}" size="small" />
                                    <div class="options absolute right-0 top-0 z-10 mt-1 hidden w-max rounded-lg border border-gray-300 bg-white p-1 dark:border-gray-800 dark:bg-gray-950"
                                        id="options-customers-{{ $customer->id }}">
                                        <ul class="flex flex-col text-xs">
                                            <li>
                                                <a href="{{ Route('business.customers.edit', $customer->id) }}"
                                                    class="flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                                    <x-icon icon="pencil" class="h-4 w-4" />
                                                    Editar
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ Route('business.customers.destroy', $customer->id) }}"
                                                    method="POST" id="form-delete-customer-{{ $customer->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" data-modal-target="deleteModal"
                                                        data-modal-toggle="deleteModal"
                                                        data-form="form-delete-customer-{{ $customer->id }}"
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
        </div> --}}

        <x-delete-modal modalId="deleteModal" title="¿Estás seguro de eliminar el cliente"
            message="No podrás recuperar este registro" />

        <div id="modal-import" tabindex="-1" aria-hidden="true"
            class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
            <div class="relative max-h-full w-full max-w-lg p-4">
                <!-- Modal content -->
                <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                    <div class="flex flex-col">
                        <!-- Modal header -->
                        <form action="{{ Route('business.customers.import') }}" method="POST" id="form-import"
                            enctype="multipart/form-data">
                            @csrf
                            <div
                                class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                    Importar Clientes
                                </h3>
                                <button type="button"
                                    class="hide-modal ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                                    data-target="#modal-import">
                                    <x-icon icon="x" class="h-5 w-5" />
                                </button>
                            </div>
                            <!-- Modal body -->
                            <div class="flex flex-col gap-4 p-4">
                                <div
                                    class="my-2 rounded-lg border border-dashed border-blue-500 bg-blue-100 p-4 text-blue-500 dark:bg-blue-950/30">
                                    <b>Nota: </b> Debe utilizar un archivo en formato <b>.xlsx</b> disponible en
                                    <a href="{{ url('templates/importacion_productos.xlsx') }}" target="_blank"
                                        class="text-blue-600 underline dark:text-blue-400">este enlace</a> para
                                    importar clientes.
                                </div>
                                <div
                                    class="mb-2 rounded-lg border border-dashed border-yellow-500 bg-yellow-100 p-4 text-yellow-500 dark:bg-yellow-950/30">
                                    <b>Advertencia: </b> Tome en cuenta que al importar clientes, si coincide el Número de Documento y
                                    el nombre de un cliente ya existente, se actualizará el cliente con los nuevos
                                    datos. Si no existe, se creará un nuevo cliente.
                                </div>
                                <x-input type="file" label="Archivo de Clientes" name="file" id="file"
                                    accept=".xlsx" maxSize="3072" />
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                                <x-button type="button" class="hide-modal" text="Cancelar" icon="x"
                                    typeButton="secondary" data-target="#modal-import" />
                                <x-button type="submit" text="Importar" icon="cloud-upload" typeButton="primary" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection
