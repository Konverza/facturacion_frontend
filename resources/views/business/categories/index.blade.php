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
                                                <a href="{{ Route('business.categories.edit', $category->id) }}"
                                                    class="flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                                    <x-icon icon="pencil" class="h-4 w-4" />
                                                    Editar
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ Route('business.categories.destroy', $category->id) }}"
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

        <x-delete-modal modalId="deleteModal" title="¿Estás seguro de eliminar la categoría?"
            message="No podrás recuperar este registro, los productos no se eliminarán." />

    </section>
@endsection
