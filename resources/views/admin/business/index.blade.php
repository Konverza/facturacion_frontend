@extends('layouts.auth-template')
@section('title', 'Negocios')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full justify-between">
            <h1 class="text-4xl font-bold text-primary-500 dark:text-primary-300">
                Negocios
            </h1>
        </div>
        <div class="mt-4 pb-4">
            <div class="mb-4 flex w-full flex-col gap-4 sm:flex-row">
                <div class="flex-[4]">
                    <x-input type="text" placeholder="Buscar negocio" class="w-full" icon="search"
                        id="input-search-business" />
                </div>
                <div class="flex-1">
                    <x-button type="a" href="{{ route('admin.business.create') }}" icon="plus" typeButton="primary"
                        text="Nuevo negocio" />
                </div>
            </div>
            <x-table id="table-data">
                <x-slot name="thead">
                    <x-tr>
                        <x-th class="w-10">#</x-th>
                        <x-th>Negocio</x-th>
                        <x-th>Plan contratado</x-th>
                        <x-th>Uso de DTEs</x-th>
                        <x-th>Contacto</x-th>
                        <x-th>Pago</x-th>
                        <x-th :last="true">Accciones</x-th>
                    </x-tr>
                </x-slot>
                <x-slot name="tbody">
                    @foreach ($business as $busines)
                        <x-tr>
                            <x-td>{{ $loop->iteration }}</x-td>
                            <x-td>{{ $busines->nombre }}</x-td>
                            <x-td>
                                <span
                                    class="flex w-max items-center gap-1 rounded-full px-2 py-1 text-sm font-bold text-primary-500 dark:text-primary-300">
                                    <x-icon icon="star" class="size-5 text-current" />
                                    {{ $busines->plan->nombre }}
                                </span>
                            </x-td>
                            <x-td>
                                <div class="mb-1 flex justify-between">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-400">
                                        0 emitidas de 200
                                    </span>
                                    <span class="text-xs font-medium text-gray-700 dark:text-gray-400">0% usado</span>
                                </div>
                                <div class="h-2.5 w-full rounded-full bg-gray-200 dark:bg-gray-900">
                                    <div class="h-2.5 rounded-full bg-blue-600" style="width: 0%"></div>
                                </div>
                            </x-td>
                            <x-td>
                                <div class="flex flex-col justify-start">
                                    <span class="flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400">
                                        <x-icon icon="user" class="h-4 w-4" />
                                        {{ $busines->nombre_responsable }}
                                    </span>
                                    <span class="flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400">
                                        <x-icon icon="phone" class="h-4 w-4" />
                                        Tel.: {{ $busines->telefono }}
                                    </span>
                                    <span class="flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400">
                                        <x-icon icon="email" class="h-4 w-4" />
                                        {{ $busines->correo_responsable }}
                                    </span>
                                </div>
                            </x-td>
                            <x-td>
                                <div class="flex flex-col justify-start">
                                    <span
                                        class="flex w-max items-center gap-1 rounded-full bg-green-100 px-1 py-0.5 text-xs text-green-500 dark:bg-green-950/30">
                                        <x-icon icon="circle-check" class="h-4 w-4" />
                                        Pagado
                                    </span>
                                    <span class="flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400">
                                        21/09/2021
                                    </span>
                                    <span class="flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400">
                                        $10.00
                                    </span>
                                </div>
                            </x-td>
                            <x-td th :last="true">
                                <div class="relative">
                                    <x-button type="button" icon="arrow-down" typeButton="primary" text="Acciones"
                                        class="show-options" data-target="#options-business-{{ $busines->id }}"
                                        size="small" />
                                    <div class="options absolute right-0 top-0 z-10 mt-1 hidden w-max rounded-lg border border-gray-300 bg-white p-2 dark:border-gray-800 dark:bg-gray-950"
                                        id="options-business-{{ $busines->id }}">
                                        <ul class="flex flex-col text-xs">
                                            <li>
                                                <button type="button"
                                                    class="flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                                    <x-icon icon="currency-dollar" class="h-4 w-4" />
                                                    Registrar pago
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button"
                                                    class="flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                                    <x-icon icon="user-edit" class="h-4 w-4" />
                                                    Ver usuario
                                                </button>
                                            </li>
                                            <li>
                                                <a href="{{ Route('admin.business.edit', 1) }}"
                                                    class="flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                                    <x-icon icon="pencil" class="h-4 w-4" />
                                                    Editar
                                                </a>
                                            </li>
                                            <li>
                                                <button type="button"
                                                    class="flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                                    <x-icon icon="cloud-upload" class="h-4 w-4" />
                                                    Mejorar plan
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button"
                                                    class="flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                                    <x-icon icon="user-off" class="h-4 w-4" />
                                                    Desactivar
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button"
                                                    class="flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                                    <x-icon icon="address-book" class="h-4 w-4" />
                                                    Contactar
                                                </button>
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
    </section>
@endsection
