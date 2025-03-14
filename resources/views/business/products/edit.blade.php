@extends('layouts.auth-template')
@section('title', 'Nuevo producto')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full items-center justify-between">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Editar producto
            </h1>
            <a href="{{ Route('business.products.index') }}"
                class="flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400">
                <x-icon icon="arrow-back" class="size-5" />
                Regresar
            </a>
        </div>
        <div class="mt-4 rounded-lg pb-4">
            <form action="{{ Route('business.products.update', $product->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                    <div class="flex-1">
                        <x-select label="Tipo de producto" value="{{ old('tipo_producto', $product->tipoItem) }}" required
                            selected="{{ old('tipo_producto', $product->tipoItem) }}" name="tipo_producto" id="product_type"
                            :options="[
                                '1' => 'Bien',
                                '2' => 'Servicio',
                                '3' => 'Bien y servicio',
                            ]" />
                    </div>
                    <div class="flex-[2]">
                        <x-input type="text" icon="barcode" required label="Código" name="codigo"
                            value="{{ old('codigo', $product->codigo) }}" />
                    </div>
                    <div class="flex-1">
                        <x-select id="unidad_medida" required name="unidad_medida" :options="$unidades_medidas"
                            value="{{ old('unidad_medida', $product->uniMedida) }}"
                            selected="{{ old('unidad_medida', $product->uniMedida) }}" label="Unidad de medida" />
                    </div>
                </div>
                <div class="mt-4">
                    <x-input type="textarea" required name="descripcion"
                        value="{{ old('descripcion', $product->descripcion) }}" label="Descripción" />
                </div>
                <div class="mt-4 flex gap-4">
                    <div class="flex-1">
                        <x-input type="number" required icon="currency-dollar" placeholder="0.00" label="Precio sin IVA"
                            name="precio_sin_iva" step="0.01" id="price-not-iva"
                            value="{{ old('precio_sin_iva', $product->precioSinTributos) }}" />
                    </div>
                    <div class="flex-1">
                        <x-input type="number" required icon="currency-dollar" placeholder="0.00" label="Precio con IVA"
                            name="precio" step="0.01" value="{{ old('precio', $product->precioUni) }}"
                            id="price-with-iva" />
                    </div>
                    <div class="flex-1">
                        <x-input type="number" required label="Stock inicial" name="stock_inicial" placeholder="0"
                            min="0" value="{{ $product->stockInicial }}" />
                    </div>
                    <div class="flex-1">
                        <x-input type="number" required label="Stock mínimo" name="stock_minimo" placeholder="0"
                            value="{{ old('stock_minimo', $product->stockMinimo) }}" min="1" />
                    </div>
                </div>
                <div class="mt-4 flex flex-col gap-2">
                    @foreach ($tributes as $tribute)
                        <x-input type="toggle" value="{{ $tribute->codigo }}" label="{{ $tribute->descripcion }}"
                            name="tributos[]" :disabled="$tribute->codigo === '20'" :checked="$tribute->codigo === '20' || (isset($product->tributos) && in_array($tribute->codigo, json_decode($product->tributos, true) ?? []))" />

                        @if ($tribute->codigo === '20')
                            <input type="hidden" name="tributos[]" value="{{ $tribute->codigo }}">
                        @endif
                    @endforeach
                </div>
                <div class="mt-4 flex items-center justify-center">
                    <x-button type="submit" typeButton="primary" class="w-full sm:w-auto" text="Editar producto"
                        icon="pencil" />
                </div>
            </form>
        </div>
    </section>
@endsection
