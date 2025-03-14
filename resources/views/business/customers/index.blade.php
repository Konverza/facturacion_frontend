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
                                {{ $customer->nombre }}
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
        </div>

        <x-delete-modal modalId="deleteModal" title="¿Estás seguro de eliminar el cliente"
            message="No podrás recuperar este registro" />

    </section>
@endsection
