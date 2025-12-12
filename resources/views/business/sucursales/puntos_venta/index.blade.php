@extends('layouts.auth-template')
@section('title', 'Puntos de Venta')
@section('content')
    <section class="my-4 px-4 sm:px-6">
        <div class="flex w-full items-center justify-between">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Puntos de Venta
            </h1>
            <a href="{{ Route('business.sucursales.index', $business_id) }}"
                class="flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400">
                <x-icon icon="arrow-back" class="size-5" />
                Regresar
            </a>
        </div>
        <div class="mt-1">
            <p class="text-sm text-gray-600 dark:text-gray-400 sm:text-base">
                Consulte aquí los puntos de venta asociados al negocio. Puede crear, editar o eliminar
                puntos de venta según sea necesario.
            </p>
            <div class="flex flex-col gap-6 xl:flex-row mt-4">
                <div class="flex-1 xl:border-r xl:border-gray-300 dark:xl:border-gray-700">
                    <div class="mt-4 me-2">
                        <div class="mb-4 flex w-full flex-col gap-4 sm:flex-row">
                            <div class="flex-[4]">
                                <span class="text-gray-400 dark:text-gray-600">
                                    <h2 class="text-lg font-semibold text-primary-500 dark:text-primary-300">
                                        Puntos de Venta Registrados
                                    </h2>
                                </span>
                            </div>
                            <div class="flex-1">
                                <x-button type="button" icon="plus" typeButton="primary" text="Nuevo Punto de Venta"
                                    class="w-full" data-modal-target="new-punto-venta" data-modal-toggle="new-punto-venta" />
                            </div>
                        </div>
                        <div class="mt-2 flex flex-col gap-4">
                            <x-table id="table-data">
                                <x-slot name="thead">
                                    <x-tr>
                                        <x-th class="w-10">#</x-th>
                                        <x-th>Código</x-th>
                                        <x-th>Nombre</x-th>
                                        @if ($business->pos_inventory_enabled)
                                            <x-th>Inventario</x-th>
                                        @endif
                                        <x-th :last="true">Accciones</x-th>
                                    </x-tr>
                                </x-slot>
                                <x-slot name="tbody">
                                    @foreach ($puntos_venta as $punto_venta)
                                        <x-tr>
                                            <x-td class="text-center">{{ $loop->iteration }}</x-td>
                                            <x-td>{{ $punto_venta->codPuntoVenta }}</x-td>
                                            <x-td>{{ $punto_venta->nombre }}</x-td>
                                            @if ($business->pos_inventory_enabled)
                                                <x-td>
                                                    @if ($punto_venta->has_independent_inventory)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                                            <x-icon icon="check" class="w-3 h-3 mr-1" />
                                                            Independiente
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400">
                                                            <x-icon icon="building" class="w-3 h-3 mr-1" />
                                                            Sucursal
                                                        </span>
                                                    @endif
                                                </x-td>
                                            @endif
                                            <x-td :last="true">
                                                <div class="relative">
                                                    <x-button type="button" icon="arrow-down" typeButton="primary"
                                                        text="Acciones" class="show-options"
                                                        data-target="#options-users-{{ $punto_venta->id }}" size="small" />
                                                    <div class="options absolute right-0 top-0 z-10 mt-1 hidden w-max rounded-lg border border-gray-300 bg-white p-2 dark:border-gray-800 dark:bg-gray-950"
                                                        id="options-users-{{ $punto_venta->id }}">
                                                        <ul class="flex flex-col text-xs">
                                                            <li>
                                                                <button type="button"
                                                                    data-url="{{ Route('business.puntos-venta.edit', [$business_id, $punto_venta->sucursal->id, $punto_venta->id]) }}"
                                                                    data-action="{{ Route('business.puntos-venta.update_punto_venta', [$business_id, $punto_venta->sucursal->id, $punto_venta->id]) }}"
                                                                    data-type="puntos_venta"
                                                                    class="btn-edit flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                                                    <x-icon icon="pencil" class="h-4 w-4" />
                                                                    Editar
                                                                </button>
                                                            </li>
                                                            <li>
                                                                <form
                                                                    action="{{ Route('business.puntos-venta.delete_punto_venta', [$business_id, $punto_venta->sucursal->id, $punto_venta->id]) }}"
                                                                    method="POST"
                                                                    id="form-delete-user-{{ $punto_venta->id }}">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="button"
                                                                        data-form="form-delete-user-{{ $punto_venta->id }}"
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
        <div id="new-punto-venta" tabindex="-1" aria-hidden="true"
            class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
            <div class="relative max-h-full w-full max-w-xl p-4">
                <form action="{{ route('business.puntos-venta.store_punto_venta', [$business_id, $sucursal->id]) }}" method="POST">
                    @csrf
                    <!-- Modal content -->
                    <div
                        class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800 md:p-5">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Nuevo Punto de Venta
                            </h3>
                            <button type="button"
                                class="ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                                data-modal-hide="new-punto-venta">
                                <x-icon icon="x" class="h-5 w-5" />
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="p-4">
                            <div class="flex-1 mb-3">
                                <x-input type="text" label="Nombre del Punto de Venta" name="nombre"
                                    placeholder="Nombre del Punto de Venta" class="w-full" required
                                    value="{{ old('nombre') }}" />
                            </div>
                            <div class="flex-1 mb-3">
                                <x-input type="text" label="Código" placeholder="P001" name="codPuntoVenta"
                                    value="{{ old('codPuntoVenta') }}" maxlength="4" />
                            </div>
                            @if ($business->pos_inventory_enabled)
                                <div class="flex-1 mb-3">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" name="has_independent_inventory" value="1" 
                                            class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                            {{ old('has_independent_inventory') ? 'checked' : '' }}>
                                        <span class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                            Habilitar inventario independiente
                                        </span>
                                    </label>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Al habilitar esta opción, este punto de venta podrá manejar su propio inventario de productos.
                                    </p>
                                </div>
                            @endif
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800 md:p-5">
                            <x-button type="button" text="Cancelar" icon="x" typeButton="secondary"
                                data-modal-hide="new-punto-venta" />
                            <x-button type="submit" text="Guardar" icon="device-floppy" typeButton="primary" />
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div id="edit-punto-venta" tabindex="-1" aria-hidden="true"
            class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
            <div class="relative max-h-full w-full max-w-xl p-4">
                <form action="{{ Route('business.index') }}" method="POST" id="form-edit-punto-venta">
                    @csrf
                    @method('PUT')
                    <!-- Modal content -->
                    <div
                        class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800 md:p-5">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Editar Punto de Venta
                            </h3>
                            <button type="button"
                                class="hide-modal ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                                data-target="#edit-punto-venta">
                                <x-icon icon="x" class="h-5 w-5" />
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="flex flex-col gap-4 p-4">
                            <div class="flex-1 mb-3">
                                <x-input type="text" label="Nombre del Punto de Venta" name="nombre"
                                    placeholder="Nombre del Punto de Venta" class="w-full" required
                                    id="nombre" />
                            </div>
                            <div class="flex-1 mb-3">
                                <x-input type="text" label="Código" placeholder="P001" name="codPuntoVenta"
                                    id="codPuntoVenta" maxlength="4" />
                            </div>
                            @if ($business->pos_inventory_enabled)
                                <div class="flex-1 mb-3">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" name="has_independent_inventory" value="1" 
                                            id="has_independent_inventory"
                                            class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <span class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                            Habilitar inventario independiente
                                        </span>
                                    </label>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Al habilitar esta opción, este punto de venta podrá manejar su propio inventario de productos.
                                    </p>
                                </div>
                            @endif
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800 md:p-5">
                            <x-button type="button" text="Cancelar" icon="x" typeButton="secondary"
                                data-target="#edit-punto-venta" class="hide-modal" />
                            <x-button type="submit" text="Editar" icon="pencil" typeButton="primary" />
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <x-delete-modal modalId="deleteModal" title="¿Estás seguro de eliminar este punto de venta?"
            message="No podrás recuperar este registro" />
    </section>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Manejar clic en botón de editar
            $(document).on('click', '.btn-edit', function() {
                const url = $(this).data('url');
                const action = $(this).data('action');
                
                console.log('URL:', url);
                console.log('Action:', action);
                
                // Hacer petición AJAX para obtener los datos del punto de venta
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        console.log('Respuesta del servidor:', response);
                        
                        // Llenar los campos del formulario
                        $('#nombre').val(response.nombre);
                        $('#codPuntoVenta').val(response.codPuntoVenta);
                        
                        // Manejar el checkbox de inventario independiente
                        if ($('#has_independent_inventory').length) {
                            $('#has_independent_inventory').prop('checked', response.has_independent_inventory == 1);
                        }
                        
                        // Actualizar la acción del formulario
                        $('#form-edit-punto-venta').attr('action', action);
                        
                        // Mostrar el modal usando display flex para mantener el centrado
                        $('#edit-punto-venta').removeClass('hidden').css('display', 'flex');
                    },
                    error: function(xhr) {
                        console.error('Error al cargar los datos:', xhr);
                        alert('Error al cargar los datos del punto de venta');
                    }
                });
            });

            // Cerrar modal con botón de cerrar
            $(document).on('click', '.hide-modal', function() {
                const target = $(this).data('target');
                $(target).addClass('hidden').css('display', 'none');
            });
        });
    </script>
@endsection
