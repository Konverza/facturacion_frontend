@extends('layouts.auth-template')
@section('title', 'Sucursales')
@section('content')
    <section class="my-4 px-4 sm:px-6">
        <div class="flex w-full items-center justify-between">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Sucursales
            </h1>
            <a href="{{ Route('admin.business.index') }}"
                class="flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400">
                <x-icon icon="arrow-back" class="size-5" />
                Regresar
            </a>
        </div>
        <div class="mt-1">
            <p class="text-sm text-gray-600 dark:text-gray-400 sm:text-base">
                Consulte aquí las sucursales y puntos de venta asociados al negocio. Puede crear, editar o eliminar
                sucursales según sea necesario.
            </p>
            <div class="flex flex-col gap-6 xl:flex-row mt-4">
                <div class="flex-1 xl:border-r xl:border-gray-300 dark:xl:border-gray-700">
                    <div class="mt-4 me-2">
                        <div class="mb-4 flex w-full flex-col gap-4 sm:flex-row">
                            <div class="flex-[4]">
                                <span class="text-gray-400 dark:text-gray-600">
                                    <h2 class="text-lg font-semibold text-primary-500 dark:text-primary-300">
                                        Sucursales Registradas
                                    </h2>
                                </span>
                            </div>
                            <div class="flex-1">
                                <x-button type="button" icon="plus" typeButton="primary" text="Nueva sucursal"
                                    class="w-full" data-modal-target="new-sucursal" data-modal-toggle="new-sucursal" />
                            </div>
                        </div>
                        <div class="mt-2 flex flex-col gap-4">
                            <x-table id="table-data">
                                <x-slot name="thead">
                                    <x-tr>
                                        <x-th class="w-10">#</x-th>
                                        <x-th>Código</x-th>
                                        <x-th>Nombre</x-th>
                                        <x-th>Direccion</x-th>
                                        <x-th>Puntos de Venta</x-th>
                                        <x-th :last="true">Accciones</x-th>
                                    </x-tr>
                                </x-slot>
                                <x-slot name="tbody">
                                    @foreach ($sucursales as $sucursal)
                                        <x-tr>
                                            <x-td class="text-center">{{ $loop->iteration }}</x-td>
                                            <x-td>{{ $sucursal->codSucursal }}</x-td>
                                            <x-td>{{ $sucursal->nombre }}</x-td>
                                            <x-td>{{ $sucursal->complemento }}</x-td>
                                            <x-td class="text-center">
                                                {{ $sucursal->puntosVentas->count() }}
                                            </x-td>
                                            <x-td :last="true">
                                                <div class="relative">
                                                    <x-button type="button" icon="arrow-down" typeButton="primary"
                                                        text="Acciones" class="show-options"
                                                        data-target="#options-users-{{ $sucursal->id }}" size="small" />
                                                    <div class="options absolute right-0 top-0 z-10 mt-1 hidden w-max rounded-lg border border-gray-300 bg-white p-2 dark:border-gray-800 dark:bg-gray-950"
                                                        id="options-users-{{ $sucursal->id }}">
                                                        <ul class="flex flex-col text-xs">
                                                            <li>
                                                                <a href="{{ Route('admin.puntos-venta.index', [$business->id, $sucursal->id]) }}"
                                                                    class="flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                                                    <x-icon icon="eye" class="h-4 w-4" />
                                                                    Ver Puntos de Venta
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <button type="button"
                                                                    data-url="{{ Route('admin.sucursales.edit', [$business->id, $sucursal->id]) }}"
                                                                    data-action="{{ Route('admin.sucursales.update_sucursal', [$business->id, $sucursal->id]) }}"
                                                                    data-type="sucursales"
                                                                    class="btn-edit flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                                                    <x-icon icon="pencil" class="h-4 w-4" />
                                                                    Editar
                                                                </button>
                                                            </li>
                                                            <li>
                                                                <form
                                                                    action="{{ Route('admin.sucursales.delete_sucursal', [$business->id, $sucursal->id]) }}"
                                                                    method="POST"
                                                                    id="form-delete-user-{{ $sucursal->id }}">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="button"
                                                                        data-form="form-delete-user-{{ $sucursal->id }}"
                                                                        data-modal-target="deleteModal"
                                                                        data-modal-toggle="deleteModal"
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
                    </div>
                </div>
            </div>
        </div>
        <div id="new-sucursal" tabindex="-1" aria-hidden="true"
            class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
            <div class="relative max-h-full w-full max-w-xl p-4">
                <form action="{{ route('admin.sucursales.store_sucursal', $business->id) }}" method="POST">
                    @csrf
                    <!-- Modal content -->
                    <div
                        class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800 md:p-5">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Nueva Sucursal
                            </h3>
                            <button type="button"
                                class="ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                                data-modal-hide="new-sucursal">
                                <x-icon icon="x" class="h-5 w-5" />
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="p-4">
                            <div class="flex-1 mb-3">
                                <x-input type="text" label="Nombre de la Sucursal" name="nombre"
                                    placeholder="Nombre de la sucursal" class="w-full" required
                                    value="{{ old('nombre') }}" />
                            </div>
                            <div class="flex-1 mb-3">
                                <x-input type="text" label="Código" placeholder="S001" name="codSucursal"
                                    value="{{ old('codSucursal') }}" maxlength="4" />
                            </div>
                            <div class="flex flex-col gap-4 sm:flex-row mb-3">
                                <div class="flex-1">
                                    <x-select name="departamento" label="Departamento" id="departamento" required
                                        :options="$departamentos" value="{{ old('departamento') }}"
                                        selected="{{ old('departamento') }}" value="{{ old('departamento') }}"
                                        data-action="{{ Route('admin.get-municipios') }}" />
                                </div>
                                <div class="flex-1" id="select-municipio">
                                    <x-select name="municipio" label="Municipio" id="municipality" required
                                        :options="[
                                            'Selecciona un municipio' => 'Seleccione un municipio',
                                        ]" />
                                </div>
                            </div>
                            <div class="mb-3">
                                <x-input type="text" label="Dirección" name="complemento"
                                    value="{{ old('complemento') }}" />
                            </div>
                            <div class="flex flex-col gap-4 sm:flex-row mb-3">
                                <div class="flex-[2]">
                                    <x-input type="email" label="Correo electrónico" icon="email" name="correo"
                                        placeholder="example@exam.com" value="{{ old('correo') }}" />
                                </div>
                                <div class="flex-1">
                                    <x-input type="text" placeholder="XXXX - XXXX" label="Teléfono" icon="phone"
                                        name="telefono" value="{{ old('telefono') }}" />
                                </div>
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800 md:p-5">
                            <x-button type="button" text="Cancelar" icon="x" typeButton="secondary"
                                data-modal-hide="new-sucursal" />
                            <x-button type="submit" text="Guardar" icon="device-floppy" typeButton="primary" />
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div id="edit-sucursal" tabindex="-1" aria-hidden="true"
            class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
            <div class="relative max-h-full w-full max-w-xl p-4">
                <form action="{{ Route('admin.users.store') }}" method="POST" id="form-edit-sucursal">
                    @csrf
                    @method('PUT')
                    <!-- Modal content -->
                    <div
                        class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800 md:p-5">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Editar sucursal
                            </h3>
                            <button type="button"
                                class="hide-modal ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                                data-target="#edit-sucursal">
                                <x-icon icon="x" class="h-5 w-5" />
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="flex flex-col gap-4 p-4">
                            <div class="flex-1 mb-3">
                                <x-input type="text" label="Nombre de la Sucursal" name="nombre"
                                    placeholder="Nombre de la sucursal" class="w-full" required
                                    id="nombre" />
                            </div>
                            <div class="flex-1 mb-3">
                                <x-input type="text" label="Código" placeholder="S001" name="codSucursal"
                                    id="codSucursal" maxlength="4" />
                            </div>
                            <div class="flex flex-col gap-4 sm:flex-row mb-3">
                                <div class="flex-1">
                                    <x-select name="departamento" label="Departamento" id="departamento" required
                                        :options="$departamentos" value="{{ old('departamento') }}"
                                        data-action="{{ Route('admin.get-municipios') }}" />
                                </div>
                                <div class="flex-1" id="edit-municipio">
                                    <x-select name="municipio" label="Municipio" id="municipio" required
                                        :options="[
                                            'Selecciona un municipio' => 'Seleccione un municipio',
                                        ]" />
                                </div>
                            </div>
                            <div class="mb-3">
                                <x-input type="text" label="Dirección" name="complemento" id="complemento"/>
                            </div>
                            <div class="flex flex-col gap-4 sm:flex-row mb-3">
                                <div class="flex-[2]">
                                    <x-input type="email" label="Correo electrónico" icon="email" name="correo" id="correo"
                                        placeholder="example@exam.com"/>
                                </div>
                                <div class="flex-1">
                                    <x-input type="text" placeholder="XXXX - XXXX" label="Teléfono" icon="phone"
                                        name="telefono" id="telefono" />
                                </div>
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800 md:p-5">
                            <x-button type="button" text="Cancelar" icon="x" typeButton="secondary"
                                data-target="#edit-sucursal" class="hide-modal" />
                            <x-button type="submit" text="Editar" icon="pencil" typeButton="primary" />
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <x-delete-modal modalId="deleteModal" title="¿Estás seguro de eliminar esta sucursal?"
            message="No podrás recuperar este registro" />
    </section>
@endsection
