@php
    $business_id = Session::get('business') ?? null;
    $business = \App\Models\Business::find($business_id);
@endphp
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
                <!-- Precio Especial y Costo -->
                @if($business->show_special_prices && !$business->price_variants_enabled)
                    <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                        <div class="flex-1">
                            <x-input type="number" required icon="currency-dollar" placeholder="0.00" label="Costo"
                                name="cost" step="0.00000001" value="{{ old('cost') }}" id="cost" />
                        </div>
                        <div class="flex-1">
                            <x-input type="number" required icon="percentage" placeholder="0.00" label="Margen de ganancia"
                                name="margin" step="0.00000001" value="{{ old('margin') }}" id="margin" />
                        </div>
                        <div class="flex-1">
                            <x-input type="number" required icon="percentage" placeholder="0.00"
                                label="Porcentaje de descuento (Sobre precio sin IVA)" name="discount" id="discount"
                                value="{{ old('discount') }}" step="0.01" />
                        </div>
                    </div>
                    <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                        <div class="flex-1">
                            <x-input type="number" required icon="currency-dollar" placeholder="0.00"
                                label="Precio especial (sin IVA)" name="special_price" id="special_price"
                                value="{{ old('special_price') }}" step="0.00000001" />
                        </div>
                        <div class="flex-1">
                            <x-input type="number" required icon="currency-dollar" placeholder="0.00"
                                label="Precio especial (con IVA)" name="special_price_with_iva" id="special_price_with_iva"
                                value="{{ old('special_price_with_iva') }}" step="0.00000001" />
                        </div>
                    </div>
                @endif
                <!-- End Precio Especial y Costo -->
                <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                    <div class="flex-1">
                        <x-input type="number" required icon="currency-dollar" placeholder="0.00"
                            :label="($business->show_special_prices && !$business->price_variants_enabled) ? 'Precio normal (sin IVA)' : 'Precio (sin IVA)'" name="precio_sin_iva" id="price-not-iva"
                            value="{{ old('precio_sin_iva') }}" step="0.00000001" />
                    </div>
                    <div class="flex-1">
                        <x-input type="number" required icon="currency-dollar" placeholder="0.00"
                            :label="($business->show_special_prices && !$business->price_variants_enabled) ? 'Precio normal (con IVA)' : 'Precio (con IVA)'" name="precio" step="0.00000001" value="{{ old('precio') }}"
                            id="price-with-iva" />
                    </div>
                </div>
                @if($business->price_variants_enabled)
                    <div class="mt-6 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                        <div class="mb-4 flex items-center justify-between gap-3">
                            <h2 class="text-lg font-semibold text-primary-600 dark:text-primary-300">Variantes de precio</h2>
                            <x-button type="button" typeButton="secondary" size="small" icon="plus" text="Nueva variante"
                                id="btn-add-new-price-variant" />
                        </div>
                        <p class="mb-4 text-xs text-gray-500 dark:text-gray-400">
                            Deja los campos vacíos para usar el precio base en esa variante.
                        </p>
                        <div class="flex flex-col gap-4">
                            @foreach ($priceVariants as $variant)
                                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                                    <div class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                        {{ $variant->name }}
                                    </div>
                                    <div class="flex flex-col gap-4 sm:flex-row">
                                        <div class="flex-1">
                                            <x-input type="number" icon="currency-dollar" placeholder="Usar precio base"
                                                label="Precio sin IVA" name="price_variants[{{ $variant->id }}][price_without_iva]"
                                                value="{{ old('price_variants.' . $variant->id . '.price_without_iva') }}" step="0.00000001" />
                                        </div>
                                        <div class="flex-1">
                                            <x-input type="number" icon="currency-dollar" placeholder="Usar precio base"
                                                label="Precio con IVA" name="price_variants[{{ $variant->id }}][price_with_iva]"
                                                value="{{ old('price_variants.' . $variant->id . '.price_with_iva') }}" step="0.00000001" />
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @php
                            $oldNewPriceVariants = old('new_price_variants', []);
                        @endphp
                        <div class="mt-5 border-t border-dashed border-gray-300 pt-4 dark:border-gray-700">
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Crear variantes nuevas</h3>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Puedes crear variantes aquí sin salir del formulario.
                            </p>

                            <div class="mt-3 space-y-3" id="new-price-variants-wrapper" data-next-index="{{ count($oldNewPriceVariants) }}">
                                @foreach ($oldNewPriceVariants as $index => $newVariant)
                                    <div class="new-price-variant-row rounded-lg border border-gray-200 p-3 dark:border-gray-700" data-new-index="{{ $index }}">
                                        <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
                                            <div class="md:col-span-2">
                                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre de variante</label>
                                                <input type="text" name="new_price_variants[{{ $index }}][name]"
                                                    value="{{ $newVariant['name'] ?? '' }}" placeholder="Ej: Precio proveedor A"
                                                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-600 focus:ring-primary-600 dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
                                            </div>
                                            <div>
                                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Precio sin IVA</label>
                                                <input type="number" step="0.00000001" min="0"
                                                    name="new_price_variants[{{ $index }}][price_without_iva]"
                                                    value="{{ $newVariant['price_without_iva'] ?? '' }}"
                                                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-600 focus:ring-primary-600 dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
                                            </div>
                                            <div>
                                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Precio con IVA</label>
                                                <div class="flex items-center gap-2">
                                                    <input type="number" step="0.00000001" min="0"
                                                        name="new_price_variants[{{ $index }}][price_with_iva]"
                                                        value="{{ $newVariant['price_with_iva'] ?? '' }}"
                                                        class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-600 focus:ring-primary-600 dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
                                                    <button type="button" class="remove-new-price-variant rounded-md px-2 py-1 text-xs text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">Quitar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <template id="new-price-variant-template">
                                <div class="new-price-variant-row rounded-lg border border-gray-200 p-3 dark:border-gray-700" data-new-index="__INDEX__">
                                    <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
                                        <div class="md:col-span-2">
                                            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre de variante</label>
                                            <input type="text" name="new_price_variants[__INDEX__][name]"
                                                value="" placeholder="Ej: Precio proveedor A"
                                                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-600 focus:ring-primary-600 dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
                                        </div>
                                        <div>
                                            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Precio sin IVA</label>
                                            <input type="number" step="0.00000001" min="0"
                                                name="new_price_variants[__INDEX__][price_without_iva]"
                                                value=""
                                                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-600 focus:ring-primary-600 dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
                                        </div>
                                        <div>
                                            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Precio con IVA</label>
                                            <div class="flex items-center gap-2">
                                                <input type="number" step="0.00000001" min="0"
                                                    name="new_price_variants[__INDEX__][price_with_iva]"
                                                    value=""
                                                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-600 focus:ring-primary-600 dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
                                                <button type="button" class="remove-new-price-variant rounded-md px-2 py-1 text-xs text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">Quitar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                @endif

                @if($business->enable_product_costs)
                    @php
                        $oldCostVariants = old('cost_variants', []);
                        if (!is_array($oldCostVariants) || count($oldCostVariants) === 0) {
                            $oldCostVariants = [[
                                'nombre_proveedor' => '',
                                'costo_final' => '',
                                'price_variant_id' => '',
                            ]];
                        }
                    @endphp
                    <div class="mt-6 rounded-lg border border-gray-200 p-4 dark:border-gray-700" id="product-cost-variants-card">
                        <div class="flex items-center justify-between gap-3">
                            <h2 class="text-lg font-semibold text-primary-600 dark:text-primary-300">Costos por proveedor</h2>
                            <x-button type="button" typeButton="secondary" size="small" icon="plus" text="Agregar proveedor"
                                id="btn-add-cost-variant" />
                        </div>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            Agrega uno o más proveedores. Puedes vincular una variante de precio para calcular la ganancia con ese precio.
                        </p>

                        <div class="mt-4 space-y-3" id="product-cost-variants-wrapper" data-next-index="{{ count($oldCostVariants) }}">
                            @foreach ($oldCostVariants as $index => $costVariant)
                                <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700 product-cost-variant-row" data-row-index="{{ $index }}">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start">
                                        <div class="flex-1">
                                            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Proveedor</label>
                                            <input type="text" placeholder="Ej: Proveedor Norte"
                                                name="cost_variants[{{ $index }}][nombre_proveedor]"
                                                value="{{ $costVariant['nombre_proveedor'] ?? '' }}"
                                                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-600 focus:ring-primary-600 dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
                                        </div>
                                        <div class="flex-1">
                                            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Costo final</label>
                                            <input type="number" placeholder="0.00" step="0.00000001" min="0"
                                                name="cost_variants[{{ $index }}][costo_final]"
                                                value="{{ $costVariant['costo_final'] ?? '' }}"
                                                class="provider-cost-input block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-600 focus:ring-primary-600 dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
                                        </div>
                                        <div class="flex-1">
                                            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Variante de precio (opcional)</label>
                                            <select name="cost_variants[{{ $index }}][price_variant_id]"
                                                class="provider-price-variant-select block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-600 focus:ring-primary-600 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                                <option value="">Precio base</option>
                                                @foreach ($priceVariants as $variant)
                                                    <option value="{{ $variant->id }}" {{ (string) ($costVariant['price_variant_id'] ?? '') === (string) $variant->id ? 'selected' : '' }}>
                                                        {{ $variant->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="sm:pt-7">
                                            <x-button type="button" typeButton="danger" size="small" icon="trash" text="Quitar"
                                                class="remove-cost-variant" />
                                        </div>
                                    </div>
                                    <p class="mt-2 text-xs text-gray-600 dark:text-gray-300 provider-gain-label" data-role="gain-text">
                                        0.00% de ganancia con este proveedor
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <template id="product-cost-variant-row-template">
                        <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700 product-cost-variant-row" data-row-index="__INDEX__">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start">
                                <div class="flex-1">
                                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Proveedor</label>
                                    <input type="text" placeholder="Ej: Proveedor Norte"
                                        name="cost_variants[__INDEX__][nombre_proveedor]" value=""
                                        class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-600 focus:ring-primary-600 dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
                                </div>
                                <div class="flex-1">
                                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Costo final</label>
                                    <input type="number" placeholder="0.00" step="0.00000001" min="0"
                                        name="cost_variants[__INDEX__][costo_final]" value=""
                                        class="provider-cost-input block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-600 focus:ring-primary-600 dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
                                </div>
                                <div class="flex-1">
                                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Variante de precio (opcional)</label>
                                    <select name="cost_variants[__INDEX__][price_variant_id]"
                                        class="provider-price-variant-select block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-600 focus:ring-primary-600 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                        <option value="">Precio base</option>
                                        @foreach ($priceVariants as $variant)
                                            <option value="{{ $variant->id }}">{{ $variant->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="sm:pt-7">
                                    <x-button type="button" typeButton="danger" size="small" icon="trash" text="Quitar"
                                        class="remove-cost-variant" />
                                </div>
                            </div>
                            <p class="mt-2 text-xs text-gray-600 dark:text-gray-300 provider-gain-label" data-role="gain-text">
                                0.00% de ganancia con este proveedor
                            </p>
                        </div>
                    </template>
                @endif
                <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                    <x-input type="toggle" label="Guardar inventario para este producto" name="has_stock"
                        id="has_stock" value="1" checked />
                </div>
                <div class="mt-4 flex flex-col gap-4 sm:flex-row" id="stocks">
                    <div class="flex-1">
                        <x-input type="number" required label="Stock inicial" name="stock_inicial" id="stock_inicial"
                            placeholder="0" value="{{ old('stock_inicial', 0) }}" min="1" />
                    </div>
                    <div class="flex-1">
                        <x-input type="number" required label="Stock mínimo" name="stock_minimo" id="stock_minimo"
                            placeholder="0" value="{{ old('stock_minimo', 0) }}" min="1" />
                    </div>
                </div>
                
                <!-- Selector de Disponibilidad por Sucursales -->
                <h2 class="mt-4 flex items-center gap-1 text-xl font-semibold text-primary-500 dark:text-primary-300">
                    <x-icon icon="building-store" class="w-5 h-5" />
                    Disponibilidad en Sucursales
                </h2>
                <div class="mt-4 flex flex-col gap-4">
                    @if ($canSelectBranch)
                        <x-input type="toggle" label="Producto disponible globalmente (en todas las sucursales)" 
                            name="is_global" id="is_global" value="1" checked />
                        
                        <div class="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800">
                            <p class="text-sm text-amber-700 dark:text-amber-300">
                                <x-icon icon="alert-triangle" class="inline w-4 h-4" />
                                <strong>Importante:</strong> Los productos globales no pueden tener control de inventario por sucursal. 
                                Si necesita manejar stock específico por sucursal, desactive esta opción y seleccione las sucursales correspondientes.
                            </p>
                        </div>
                        
                        <div id="sucursales-selector" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Seleccione las sucursales donde estará disponible este producto
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 p-4 border border-gray-300 dark:border-gray-600 rounded-lg">
                                @foreach ($sucursales as $sucursal)
                                    <label class="flex items-center gap-2 p-2 hover:bg-gray-50 dark:hover:bg-gray-800 rounded cursor-pointer">
                                        <input type="checkbox" name="sucursales[]" value="{{ $sucursal->id }}" 
                                            class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $sucursal->nombre }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                <x-icon icon="info-circle" class="inline w-4 h-4" />
                                Debe seleccionar al menos una sucursal si el producto no es global
                            </p>
                        </div>
                    @else
                        <!-- Usuario sin branch_selector: auto-asignar sucursal por defecto -->
                        <input type="hidden" name="is_global" value="0">
                        @if ($defaultSucursalId)
                            <input type="hidden" name="sucursales[]" value="{{ $defaultSucursalId }}">
                            <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                <p class="text-sm text-blue-700 dark:text-blue-300">
                                    <x-icon icon="map-pin" class="inline w-4 h-4" />
                                    Este producto se creará para la sucursal: 
                                    <strong>{{ $sucursales->firstWhere('id', $defaultSucursalId)?->nombre ?? 'Sucursal por defecto' }}</strong>
                                </p>
                            </div>
                        @else
                            <div class="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800">
                                <p class="text-sm text-amber-700 dark:text-amber-300">
                                    <x-icon icon="alert-triangle" class="inline w-4 h-4" />
                                    No se ha configurado una sucursal por defecto. Contacte al administrador.
                                </p>
                            </div>
                        @endif
                    @endif
                </div>
                @if ($business->posmode)
                    <div class="mt-4">
                        <div class="flex-1">
                            <x-select id="category_id" name="category_id" :options="$categories"
                                value="{{ old('category_id') }}" selected="{{ old('category_id') }}"
                                label="Categoría (opcional)" />
                        </div>
                    </div>
                @endif
                @if ($business->posmode || $business->quotation_enabled)
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
                // Toggle stock fields
                $("#has_stock").on("change", function() {
                    if ($(this).is(":checked")) {
                        $("#stocks").removeClass("hidden");
                        $("#stock_inicial, #stock_minimo").prop("disabled", false);
                    } else {
                        $("#stocks").addClass("hidden");
                        $("#stock_inicial, #stock_minimo").prop("disabled", true).val(0);
                    }
                });
                
                // Toggle sucursales selector
                $("#is_global").on("change", function() {
                    if ($(this).is(":checked")) {
                        $("#sucursales-selector").addClass("hidden");
                        // Desmarcar todos los checkboxes de sucursales
                        $("input[name='sucursales[]']").prop("checked", false);
                    } else {
                        $("#sucursales-selector").removeClass("hidden");
                    }
                });

                const parseNumber = (value) => {
                    const n = parseFloat(value);
                    return Number.isFinite(n) ? n : 0;
                };

                const getPriceWithoutIvaFromVariantSelect = (selectedVariantId, fallback) => {
                    if (!selectedVariantId) {
                        return fallback;
                    }

                    if (String(selectedVariantId).startsWith("new:")) {
                        const newIndex = String(selectedVariantId).replace("new:", "");
                        const dynamicInput = $("input[name='new_price_variants[" + newIndex + "][price_without_iva]']");
                        if (dynamicInput.length === 0) {
                            return fallback;
                        }

                        const dynamicValue = parseFloat(dynamicInput.val());
                        return Number.isFinite(dynamicValue) ? dynamicValue : fallback;
                    }

                    const variantInput = $("input[name='price_variants[" + selectedVariantId + "][price_without_iva]']");
                    if (variantInput.length === 0) {
                        return fallback;
                    }

                    const variantValue = parseFloat(variantInput.val());
                    return Number.isFinite(variantValue) ? variantValue : fallback;
                };

                const getSalePriceWithoutIva = (row) => {
                    const basePrice = parseNumber($("#price-not-iva").val());
                    const selectedVariantId = row.find(".provider-price-variant-select").val();

                    return getPriceWithoutIvaFromVariantSelect(selectedVariantId, basePrice);
                };

                const syncProviderVariantOptionsFromNewRows = () => {
                    const providerSelects = $(".provider-price-variant-select");
                    if (providerSelects.length === 0) {
                        return;
                    }

                    const dynamicOptions = [];
                    $(".new-price-variant-row").each(function() {
                        const row = $(this);
                        const index = row.data("new-index");
                        const name = (row.find("input[name$='[name]']").val() || "").trim();

                        if (!name || index === undefined || index === null) {
                            return;
                        }

                        dynamicOptions.push({
                            value: "new:" + index,
                            label: name + " (nueva)",
                        });
                    });

                    providerSelects.each(function() {
                        const select = $(this);
                        const prev = select.val();

                        select.find("option[data-dynamic-new='1']").remove();

                        dynamicOptions.forEach((opt) => {
                            if (select.find("option[value='" + opt.value + "']").length === 0) {
                                const option = new Option(opt.label, opt.value);
                                $(option).attr("data-dynamic-new", "1");
                                select.append(option);
                            }
                        });

                        if (prev && select.find("option[value='" + prev + "']").length > 0) {
                            select.val(prev);
                        }
                    });
                };

                const updateGainLabelForRow = (row) => {
                    const cost = parseNumber(row.find(".provider-cost-input").val());
                    const salePrice = getSalePriceWithoutIva(row);
                    const label = row.find("[data-role='gain-text']");

                    if (cost <= 0) {
                        label.text("0.00% de ganancia con este proveedor");
                        return;
                    }

                    const gain = ((salePrice - cost) / cost) * 100;
                    label.text(gain.toFixed(2) + "% de ganancia con este proveedor");
                };

                const updateAllGainLabels = () => {
                    $(".product-cost-variant-row").each(function() {
                        updateGainLabelForRow($(this));
                    });
                };

                $(document).on("input", "#price-not-iva, .provider-cost-input, input[name^='price_variants'][name$='[price_without_iva]'], input[name^='new_price_variants'][name$='[price_without_iva]']", function() {
                    updateAllGainLabels();
                });

                $(document).on("change", ".provider-price-variant-select", function() {
                    updateGainLabelForRow($(this).closest(".product-cost-variant-row"));
                });

                $(document).on("click", ".remove-cost-variant", function() {
                    const rows = $(".product-cost-variant-row");
                    if (rows.length <= 1) {
                        $(this).closest(".product-cost-variant-row").find("input, select").val("");
                        updateAllGainLabels();
                        return;
                    }
                    $(this).closest(".product-cost-variant-row").remove();
                    updateAllGainLabels();
                });

                $("#btn-add-cost-variant").on("click", function() {
                    const wrapper = $("#product-cost-variants-wrapper");
                    const template = $("#product-cost-variant-row-template").html();
                    const nextIndex = parseInt(wrapper.attr("data-next-index"), 10) || 0;

                    wrapper.append(template.replaceAll("__INDEX__", String(nextIndex)));
                    wrapper.attr("data-next-index", String(nextIndex + 1));
                    syncProviderVariantOptionsFromNewRows();
                    updateAllGainLabels();
                });

                const addNewPriceVariantRow = () => {
                    const wrapper = $("#new-price-variants-wrapper");
                    if (wrapper.length === 0) {
                        return;
                    }

                    const template = $("#new-price-variant-template").html();
                    const nextIndex = parseInt(wrapper.attr("data-next-index"), 10) || 0;
                    wrapper.append(template.replaceAll("__INDEX__", String(nextIndex)));
                    wrapper.attr("data-next-index", String(nextIndex + 1));
                    syncProviderVariantOptionsFromNewRows();
                };

                $("#btn-add-new-price-variant").on("click", function() {
                    addNewPriceVariantRow();
                });

                $(document).on("click", ".remove-new-price-variant", function() {
                    $(this).closest(".new-price-variant-row").remove();
                    syncProviderVariantOptionsFromNewRows();
                    updateAllGainLabels();
                });

                $(document).on("input", "input[name^='new_price_variants'][name$='[name]']", function() {
                    syncProviderVariantOptionsFromNewRows();
                });

                $(document).on("input", "input[name^='new_price_variants']", function() {
                    const input = $(this);
                    const name = input.attr("name") || "";
                    const match = name.match(/new_price_variants\[(\d+)\]\[(price_without_iva|price_with_iva)\]/);
                    if (!match) {
                        return;
                    }

                    const index = match[1];
                    const field = match[2];
                    const value = parseFloat(input.val()) || 0;

                    if (field === "price_without_iva") {
                        $("input[name='new_price_variants[" + index + "][price_with_iva]']").val((value * 1.13).toFixed(8));
                    } else {
                        $("input[name='new_price_variants[" + index + "][price_without_iva]']").val((value / 1.13).toFixed(8));
                    }

                    updateAllGainLabels();
                });

                if ($("#new-price-variants-wrapper").length > 0 && $("#new-price-variants-wrapper .new-price-variant-row").length === 0) {
                    addNewPriceVariantRow();
                }

                syncProviderVariantOptionsFromNewRows();

                updateAllGainLabels();
            });
        </script>
    @endpush
@endsection
