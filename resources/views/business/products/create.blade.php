@extends('layouts.auth-template')
@section('title', 'Nuevo producto')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full items-center justify-between">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Nuevo producto
            </h1>
            <a href="{{ Route('business.products.index') }}"
                class="flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400">
                <x-icon icon="arrow-back" class="size-5" />
                Regresar
            </a>
        </div>
        <div class="mt-4 rounded-lg pb-4">

            @if ($errors->any())
                <div class="border-l-4 border-red-500 bg-red-100 p-4 text-red-700" role="alert">
                    <p class="font-bold">Error</p>
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ Route('business.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="flex flex-col gap-4 sm:flex-row">
                    <div class="flex-1">
                        <x-select label="Tipo de producto" value="{{ old('tipo_producto') }}" required :search="false"
                            selected="{{ old('tipo_producto') }}" name="tipo_producto" id="product_type"
                            :options="[
                                '1' => 'Bien',
                                '2' => 'Servicio',
                                '3' => 'Bien y servicio',
                            ]" />
                    </div>
                    <div class="flex-[2]">
                        <x-input type="text" icon="barcode" required label="Código" name="codigo"
                            value="{{ old('codigo') }}" />
                    </div>
                    <div class="flex-1">
                        <x-select id="unidad_medida" required name="unidad_medida" :options="$unidades_medidas"
                            value="{{ old('unidad_medida') }}" selected="{{ old('unidad_medida') }}"
                            label="Unidad de medida" />
                    </div>
                </div>
                <div class="mt-4">
                    <x-input type="textarea" required name="descripcion" value="{{ old('descripcion') }}"
                        label="Descripción" />
                </div>
                <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                    <div class="flex-1">
                        <x-input type="number" required icon="currency-dollar" placeholder="0.00" label="Precio sin IVA"
                            name="precio_sin_iva" id="price-not-iva" value="{{ old('precio_sin_iva') }}"  step="0.01" />
                    </div>
                    <div class="flex-1">
                        <x-input type="number" required icon="currency-dollar" placeholder="0.00" label="Precio con IVA"
                            name="precio" step="0.01" value="{{ old('precio') }}" id="price-with-iva" />
                    </div>
                </div>
                <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                    <x-input type="toggle" label="Guardar inventario para este producto"
                        name="has_stock" id="has_stock" value="1" checked />
                </div>
                <div class="mt-4 flex flex-col gap-4 sm:flex-row" id="stocks">
                    <div class="flex-1">
                        <x-input type="number" required label="Stock inicial" name="stock_inicial" id="stock_inicial" placeholder="0"
                            value="{{ old('stock_inicial', 0) }}" min="1" />
                    </div>
                    <div class="flex-1">
                        <x-input type="number" required label="Stock mínimo" name="stock_minimo" id="stock_minimo" placeholder="0"
                            value="{{ old('stock_minimo', 0) }}" min="1" />
                    </div>
                </div>
                @php
                    $business_id = Session::get('business') ?? null;
                    $business = \App\Models\Business::find($business_id);
                @endphp
                @if($business->posmode)
                <div class="mt-4">
                    <div class="flex-1">
                        <x-select id="category_id" name="category_id" :options="$categories"
                            value="{{ old('category_id') }}" selected="{{ old('category_id') }}"
                            label="Categoría (opcional)" />
                    </div>
                </div>
                <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                    <div class="flex-1">
                        <x-input type="file" label="Imagen de Producto" name="image" id="image"
                                    accept=".png, .jpg, .jpeg, .webp" maxSize="3072" />
                    </div>
                </div>
                @endif
                <h2 class="mt-2 flex items-center gap-1 text-xl font-semibold text-primary-500 dark:text-primary-300">
                    Tributos
                </h2>
                <div class="mt-4 flex flex-col gap-2">
                    @foreach ($tributes as $tribute)
                        <x-input type="toggle" value="{{ $tribute->codigo }}" label="{{ $tribute->descripcion }}"
                            name="tributos[]" id="{{ $tribute->codigo }}" :disabled="$tribute->codigo === '20'" :checked="$tribute->codigo === '20'" />
                        @if ($tribute->codigo === '20')
                            <input type="hidden" name="tributos[]" value="{{ $tribute->codigo }}">
                        @endif
                    @endforeach
                </div>
                <div class="mt-4 flex items-center justify-center">
                    <x-button type="submit" typeButton="primary" class="w-full sm:w-auto" text="Guardar producto"
                        icon="save" />
                </div>
            </form>
        </div>
    </section>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $("#has_stock").on("change", function(){
                    if ($(this).is(":checked")) {
                        $("#stock_inicial").prop("disabled", false);
                        $("#stock_minimo").prop("disabled", false);
                        $("#stock_inicial").val(0);
                        $("#stock_minimo").val(0);
                        $("#stocks").removeClass("hidden");
                    } else {
                        $("#stock_inicial").prop("disabled", true);
                        $("#stock_minimo").prop("disabled", true);
                        $("#stock_inicial").val(0);
                        $("#stock_minimo").val(0);
                        $("#stocks").addClass("hidden");
                    }
                });
            });
        </script>
    @endpush
@endsection
