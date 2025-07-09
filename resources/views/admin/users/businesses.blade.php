@extends('layouts.auth-template')
@section('title', 'Negocios Asociados')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full justify-between">
            <h1 class="text-4xl font-bold text-primary-500 dark:text-primary-300">
                Negocios Asociados
            </h1>
        </div>
        <div class="mt-4">
            <div class="mb-4 flex w-full flex-col gap-4 sm:flex-row">
                <div class="flex-[4]">
                    <x-input type="text" placeholder="Buscar negocio" class="w-full" icon="search" id="input-search-data" />
                </div>
                <div class="flex-1">
                    <x-button type="button" icon="plus" typeButton="primary" text="Asociar negocio" class="w-full"
                        data-modal-target="new-association" data-modal-toggle="new-association" />
                </div>
            </div>
            <x-table id="table-data">
                <x-slot name="thead">
                    <x-tr>
                        <x-th class="w-10">#</x-th>
                        <x-th>Negocio</x-th>
                        <x-th>Punto de Venta predeterminado</x-th>
                        <x-th>¿Solo ver DTEs de ese punto de venta?</x-th>
                        <x-th>Acciones</x-th>
                    </x-tr>
                </x-slot>
                <x-slot name="tbody">
                    @forelse ($user->businesses as $business)
                        <x-tr>
                            <x-td>{{ $loop->iteration }}</x-td>
                            <x-td>
                                {{ $business->business->nombre }}
                            </x-td>
                            <x-td>{{ $business->default_pos->nombre ?? 'N/A' }}</x-td>
                            <x-td>
                                @if ($business->only_default_pos)
                                    <span
                                        class="inline-block px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded">Sí</span>
                                @else
                                    <span
                                        class="inline-block px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded">No</span>
                                @endif
                            </x-td>
                            <x-td>
                                <x-button type="button" icon="arrow-down" typeButton="primary" text="Acciones"
                                    class="show-options" data-target="#options-businesses-{{ $business->id }}"
                                    size="small" />
                                <div class="options absolute right-0 top-0 z-10 mt-1 hidden w-max rounded-lg border border-gray-300 bg-white p-2 dark:border-gray-800 dark:bg-gray-950"
                                    id="options-businesses-{{ $business->id }}">
                                    <ul class="flex flex-col text-xs">
                                        <li>
                                            <button type="button" data-url="{{ Route('admin.users.edit', $user->id) }}"
                                                data-action="{{ Route('admin.users.update', $user->id) }}" data-type="users"
                                                class="btn-edit flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                                <x-icon icon="pencil" class="h-4 w-4" />
                                                Editar Asociación
                                            </button>
                                        </li>
                                        <li>
                                            <form
                                                action="{{ Route('admin.users.delete_business', [$user->id, $business->id]) }}"
                                                method="POST" id="form-delete-business-user-{{ $business->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    data-form="form-delete-business-user-{{ $business->id }}"
                                                    data-modal-target="deleteModal" data-modal-toggle="deleteModal"
                                                    class="buttonDelete flex w-full items-center gap-1 rounded-lg px-2 py-2 text-red-500 hover:bg-red-100 dark:text-red-500 dark:hover:bg-red-950/30">
                                                    <x-icon icon="trash" class="h-4 w-4" />
                                                    Eliminar
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </x-td>
                        </x-tr>
                    @empty
                        <x-tr>
                            <x-td colspan="5" class="text-center">No hay negocios asociados.</x-td>
                        </x-tr>
                    @endforelse
                </x-slot>
            </x-table>
        </div>
    </section>

    {{-- Modal new association --}}
    <div id="new-association" tabindex="-1" aria-hidden="true"
        class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
        <div class="relative max-h-full w-full max-w-xl p-4">
            <form action="{{ Route('admin.users.store_business', $user->id) }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                <!-- Modal content -->
                <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                    <!-- Modal header -->
                    <div
                        class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800 md:p-5">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Asociar Negocio
                        </h3>
                        <button type="button"
                            class="ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                            data-modal-hide="new-association">
                            <x-icon icon="x" class="h-5 w-5" />
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-4">
                        <div class="flex flex-col gap-4 mb-4">
                            <x-select label="Negocio" name="business_id" id="business_id" placeholder="Seleccionar negocio"
                                :options="$businesses->pluck('nombre', 'id')" 
                                data-action="{{Route('admin.sucursales.json')}}"/>
                        </div>
                        <div class="flex flex-col gap-4 mb-4" id="select-sucursal">
                            <x-select name="sucursal" label="Sucursal" id="sucursal" required :options="[
                                'Seleccione una Sucursal' => 'Seleccione una Sucursal',
                            ]" />
                        </div>
                        <div class="flex flex-col gap-4 mb-4" id="select-punto-venta">
                            <x-select name="pos" label="Punto de Venta" id="pos" required :options="[
                                'Seleccione un Punto de Venta' => 'Seleccione un Punto de Venta',
                            ]" />
                        </div>
                        <div class="mt-4">
                            <x-input type="toggle" name="only_default_pos" label="¿Solo ver DTEs de ese punto de venta?"
                                value="1" id="only_default_pos" />
                        </div>
                    </div>
                    <!-- Modal footer -->
                    <div
                        class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800 md:p-5">
                        <x-button type="button" text="Cancelar" icon="x" typeButton="secondary"
                            data-modal-hide="new-association" />
                        <x-button type="submit" text="Guardar" icon="device-floppy" typeButton="primary" />
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal delete association --}}
    <x-delete-modal modalId="deleteModal" title="¿Eliminar Asociación?" message="No podrás recuperar este registro" />
@endsection
