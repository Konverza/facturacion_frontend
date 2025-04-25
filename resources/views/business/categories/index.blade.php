@extends('layouts.auth-template')
@section('title', 'Categorías de productos')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full justify-between">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Categorías de productos
            </h1>
        </div>
        <div class="mt-4 pb-4">
            <div class="mb-4 flex w-full flex-col gap-4 sm:flex-row">
                <div class="flex-[6]">
                    <x-input type="text" placeholder="Buscar" class="w-full" icon="search" id="input-search-data" />
                </div>
                <div class="flex-1">
                    <x-button type="a" href="{{ Route('business.categories.create') }}" typeButton="primary"
                        text="Nueva Categoría" icon="cube-plus" class="w-full" />
                </div>
            </div>
            <x-table id="table-data">
                <x-slot name="thead">
                    <x-tr>
                        <x-th class="w-10">#</x-th>
                        <x-th>Nombre</x-th>
                        <x-th>Cantidad de Productos</x-th>
                        <x-th>Imagen</x-th>
                        <x-th :last="true">Accciones</x-th>
                    </x-tr>
                </x-slot>
                <x-slot name="tbody">
                    @foreach ($categories as $category)
                        <x-tr :last="$loop->last">
                            <x-td>{{ $loop->iteration }}</x-td>
                            <x-td>{{ $category->name }}</x-td>
                            <x-td>{{ $category->products->count() }}</x-td>
                            <x-td>
                                @if ($category->image_url)
                                    <img src="{{ asset($category->image_url) }}" alt="Imagen de {{ $category->name }}"
                                        class="h-10 w-10 rounded-lg object-cover" />
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">Sin imagen</span>
                                @endif
                            </x-td>
                            <x-td :last="true">
                                <div class="relative">
                                    <x-button type="button" icon="arrow-down" typeButton="primary" text="Acciones"
                                        class="show-options" data-target="#options-products-{{ $category->id }}"
                                        size="small" />
                                    <div class="options absolute right-0 top-0 z-10 mt-1 hidden w-max rounded-lg border border-gray-300 bg-white p-1 dark:border-gray-800 dark:bg-gray-950"
                                        id="options-products-{{ $category->id }}">
                                        <ul class="flex flex-col text-xs">
                                            <li>
                                                <a href="{{ Route('business.products.edit', $category->id) }}"
                                                    class="flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                                    <x-icon icon="pencil" class="h-4 w-4" />
                                                    Editar
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ Route('business.products.destroy', $category->id) }}"
                                                    method="POST" id="form-delete-product-{{ $category->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        data-form="form-delete-product-{{ $category->id }}"
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

        <x-delete-modal modalId="deleteModal" title="¿Estás seguro de eliminar el producto?"
            message="No podrás recuperar este registro" />

        <div id="modal-add-stock" tabindex="-1" aria-hidden="true"
            class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
            <div class="relative max-h-full w-full max-w-lg p-4">
                <!-- Modal content -->
                <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                    <div class="flex flex-col">
                        <!-- Modal header -->
                        <form action="{{ Route('business.products.add-stock') }}" method="POST" id="form-add-stock">
                            @csrf
                            <div
                                class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                    Agregar stock
                                </h3>
                                <button type="button"
                                    class="hide-modal ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                                    data-target="#modal-add-stock">
                                    <x-icon icon="x" class="h-5 w-5" />
                                </button>
                            </div>
                            <!-- Modal body -->
                            <div class="flex flex-col gap-4 p-4">
                                <input type="hidden" name="id" id="product-id">
                                <x-input type="number" icon="box" placeholder="Ingresar cantidad" name="cantidad"
                                    required label="Cantidad" min="1" />
                                <x-input type="textarea" placeholder="Ingresa la descripción" name="descripcion"
                                    label="Descripción" required />
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                                <x-button type="button"  class="hide-modal" text="Cancelar" icon="x"
                                    typeButton="secondary" data-target="#modal-add-stock" />
                                <x-button type="submit" text="Agregar" icon="plus" typeButton="primary" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="modal-remove-stock" tabindex="-1" aria-hidden="true"
            class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
            <div class="relative max-h-full w-full max-w-lg p-4">
                <!-- Modal content -->
                <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                    <div class="flex flex-col">
                        <!-- Modal header -->
                        <form action="{{ Route('business.products.remove-stock') }}" method="POST" id="form-remove-stock">
                            @csrf
                            <div
                                class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                    Disminuir stock
                                </h3>
                                <button type="button"
                                    class="hide-modal ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                                    data-target="#modal-remove-stock">
                                    <x-icon icon="x" class="h-5 w-5" />
                                </button>
                            </div>
                            <!-- Modal body -->
                            <div class="flex flex-col gap-4 p-4">
                                <input type="hidden" name="id" id="product-remove-id">
                                <x-input type="number" icon="box" placeholder="Ingresar cantidad" name="cantidad"
                                    required label="Cantidad" min="1" />
                                <x-input type="textarea" placeholder="Ingresa la descripción" name="descripcion"
                                    label="Descripción" required />
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                                <x-button type="button"  class="hide-modal" text="Cancelar" icon="x"
                                    typeButton="secondary" data-target="#modal-remove-stock" />
                                <x-button type="submit" text="Disminuir" icon="minus" typeButton="primary" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection
