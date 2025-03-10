@extends('layouts.auth-template')
@section('title', 'Negocios')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full justify-between">
            <h1 class="text-4xl font-bold text-primary-500 dark:text-primary-300">
                Planes
            </h1>
        </div>
        <div class="mt-4">
            <div class="mb-4 flex w-full flex-col gap-4 sm:flex-row">
                <div class="flex-[4]">
                    <x-input type="text" placeholder="Buscar plan" class="w-full" icon="search" id="input-search-plans" />
                </div>
                <div class="flex-1">
                    <x-button type="button" icon="plus" typeButton="primary" text="Nuevo plan" class="show-modal w-full"
                        data-target="#new-plan" />
                </div>
            </div>
            <x-table id="table-data">
                <x-slot name="thead">
                    <x-tr>
                        <x-th class="w-10">#</x-th>
                        <x-th>Nombre</x-th>
                        <x-th>Límite</x-th>
                        <x-th>Precio</x-th>
                        <x-th>Precio adicional</x-th>
                        <x-th :last="true">Accciones</x-th>
                    </x-tr>
                </x-slot>
                <x-slot name="tbody">
                    @foreach ($plans as $plan)
                        <x-tr>
                            <x-td>1</x-td>
                            <x-td>
                                {{ $plan->nombre }}
                            </x-td>
                            <x-td>
                                {{ $plan->limite }} al mes
                            </x-td>
                            <x-td>
                                ${{ $plan->precio }}
                            </x-td>
                            <x-td>
                                ${{ $plan->precio_adicional }}
                            </x-td>
                            <x-td th :last="true">
                                <div class="relative">
                                    <x-button type="button" icon="arrow-down" typeButton="primary" text="Acciones"
                                        class="show-options" data-target="#options-plans-{{ $plan->id }}"
                                        size="small" />
                                    <div class="options absolute right-0 top-0 z-10 mt-1 hidden w-max rounded-lg border border-gray-300 bg-white p-2 dark:border-gray-800 dark:bg-gray-950"
                                        id="options-plans-{{ $plan->id }}">
                                        <ul class="flex flex-col text-xs">
                                            <li>
                                                <button type="button"
                                                    class="btn-edit flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900" data-type="plans"
                                                    data-url="{{ Route('admin.plans.edit', $plan->id) }}"
                                                    data-action="{{ Route('admin.plans.update', $plan->id) }}">
                                                    <x-icon icon="pencil" class="h-4 w-4" />
                                                    Editar
                                                </button>
                                            </li>
                                            <li>
                                                <form action="{{ Route('admin.plans.destroy', $plan->id) }}" method="POST"
                                                    id="form-delete-plan-{{ $plan->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" data-form="form-delete-plan-{{ $plan->id }}"
                                                        data-modal-target="deleteModal" data-modal-toggle="deleteModal"
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

        <div id="new-plan" tabindex="-1" aria-hidden="true"
            class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
            <div class="relative max-h-full w-full max-w-lg p-4">
                <!-- Modal content -->
                <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                    <form action="{{ Route('admin.plans.store') }}" method="POST">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Nuevo plan
                            </h3>
                            <button type="button"
                                class="hide-modal ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                                data-target="#new-plan">
                                <x-icon icon="x" class="h-5 w-5" />
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="p-4">
                            <div class="flex flex-col gap-4">
                                @csrf
                                <x-input type="text" label="Nombre" placeholder="Nombre del plan" name="name" />
                                <x-input type="number" label="Límite de DTEs al mes" placeholder="Nombre del plan"
                                    name="limit" />
                                <div class="flex gap-4">
                                    <div class="flex-1">
                                        <x-input type="number" label="Precio" step="0.01" placeholder="$0.00"
                                            icon="currency-dollar" name="price" />
                                    </div>
                                    <div class="flex-1">
                                        <x-input type="number" name="price_aditional" label="Precio DTE adicional"
                                            step="0.01" placeholder="$0.00" icon="currency-dollar" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                            <x-button type="button" class="hide-modal" text="Cancelar" icon="x"
                                typeButton="secondary" data-target="#new-plan" />
                            <x-button type="submit" text="Guardar" icon="device-floppy" typeButton="primary" />
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="edit-plan" tabindex="-1" aria-hidden="true"
            class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
            <div class="relative max-h-full w-full max-w-lg p-4">
                <!-- Modal content -->
                <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                    <form action="{{ Route('admin.plans.store') }}" method="POST" id="form-edit-plan">
                        @method('PUT')
                        @csrf
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800 md:p-5">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Editar plan
                            </h3>
                            <button type="button"
                                class="hide-modal ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                                data-target="#edit-plan">
                                <x-icon icon="x" class="h-5 w-5" />
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="p-4">
                            <div class="flex flex-col gap-4">
                                <x-input type="text" label="Nombre" id="name" placeholder="Nombre del plan" name="name" />
                                <x-input type="number" label="Límite de DTEs al mes" placeholder="Nombre del plan"
                                    name="limit" id="limit" />
                                <div class="flex gap-4">
                                    <div class="flex-1">
                                        <x-input type="number" id="price" label="Precio" step="0.01" placeholder="$0.00"
                                            icon="currency-dollar" name="price" />
                                    </div>
                                    <div class="flex-1">
                                        <x-input type="number" id="price_aditional" name="price_aditional" label="Precio DTE adicional"
                                            step="0.01" placeholder="$0.00" icon="currency-dollar" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800 md:p-5">
                            <x-button type="button" text="Cancelar" icon="x" typeButton="secondary"
                                data-target="#edit-plan" class="hide-modal" />
                            <x-button type="submit" text="Guardar" icon="device-floppy" typeButton="primary" />
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <x-delete-modal modalId="deleteModal" title="¿Estás seguro de eliminar el plan"
            message="No podrás recuperar este registro" />

    </section>
@endsection
