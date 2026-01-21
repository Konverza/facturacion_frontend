@extends('layouts.auth-template')
@section('title', 'Variantes de precio')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full items-center justify-between">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Variantes de precio
            </h1>
            <a href="{{ Route('business.products.index') }}"
                class="flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400">
                <x-icon icon="arrow-back" class="size-5" />
                Regresar
            </a>
        </div>

        @if (session('success'))
            <div class="mt-4 rounded-lg border-l-4 border-green-500 bg-green-100 p-4 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-4 rounded-lg border-l-4 border-red-500 bg-red-100 p-4 text-red-700">
                <p class="font-bold">Error</p>
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="mt-6 rounded-lg border border-gray-200 dark:border-gray-800 p-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Configuración</h2>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                Al activar variantes de precio, los precios especiales se deshabilitan automáticamente.
            </p>
            <form action="{{ Route('business.price-variants.settings') }}" method="POST" class="mt-4">
                @csrf
                <x-input type="toggle" name="price_variants_enabled" label="Habilitar variantes de precio"
                    value="1" id="price_variants_enabled" :checked="$business?->price_variants_enabled" />
                <div class="mt-4">
                    <x-button type="submit" typeButton="primary" text="Guardar configuración" icon="save" />
                </div>
            </form>
        </div>

        <div class="mt-6 rounded-lg border border-gray-200 dark:border-gray-800 p-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Crear variante</h2>
            <form action="{{ Route('business.price-variants.store') }}" method="POST" class="mt-4">
                @csrf
                <div class="flex flex-col gap-4 sm:flex-row">
                    <div class="flex-1">
                        <x-input type="text" label="Nombre" name="name" required placeholder="Ej: Mayorista" />
                    </div>
                </div>
                <div class="mt-4">
                    <x-button type="submit" typeButton="primary" text="Crear variante" icon="plus" />
                </div>
            </form>
        </div>

        <div class="mt-6 rounded-lg border border-gray-200 dark:border-gray-800 p-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Variantes existentes</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                    <thead class="border-b border-gray-200 dark:border-gray-700 text-xs uppercase">
                        <tr>
                            <th class="px-3 py-2">Nombre</th>
                            <th class="px-3 py-2 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($variants as $variant)
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="px-3 py-2">
                                    <input type="text" name="name" value="{{ $variant->name }}"
                                        form="price-variant-form-{{ $variant->id }}"
                                        class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white" required>
                                </td>
                                <td class="px-3 py-2 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <form action="{{ Route('business.price-variants.update', $variant->id) }}" method="POST" id="price-variant-form-{{ $variant->id }}">
                                                @csrf
                                                @method('PUT')
                                                <x-button type="submit" typeButton="secondary" size="small" text="Guardar" icon="save" />
                                            </form>
                                            <form action="{{ Route('business.price-variants.destroy', $variant->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <x-button type="submit" typeButton="danger" size="small" text="Eliminar" icon="trash" />
                                            </form>
                                        </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-3 py-4 text-center" colspan="4">No hay variantes creadas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
