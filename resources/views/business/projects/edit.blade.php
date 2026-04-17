@extends('layouts.auth-template')
@section('title', 'Proyecto')

@section('content')
    <section class="my-4 px-4 pb-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl">
                Proyecto: {{ $project->name }}
            </h1>
            <div class="flex flex-wrap gap-2">
                <x-button type="a" href="{{ Route('business.projects.index') }}" text="Volver" icon="arrow-left"
                    typeButton="secondary" class="w-full sm:w-auto" />
                <x-button type="a" href="{{ Route('business.projects.comparison-pdf', $project->id) }}" text="PDF Etapa 1"
                    icon="file-report" typeButton="info" class="w-full sm:w-auto" target="_blank" />
                <x-button type="a" href="{{ Route('business.projects.costing-pdf', $project->id) }}" text="PDF Etapa 2"
                    icon="file-report" typeButton="info" class="w-full sm:w-auto" target="_blank" />
            </div>
        </div>

        <form action="{{ Route('business.projects.update', $project->id) }}" method="POST"
            class="mt-4 rounded-lg border border-gray-300 p-4 dark:border-gray-800">
            @csrf
            @method('PUT')
            <div class="grid gap-4 md:grid-cols-[1fr_auto] md:items-end">
                <x-input type="text" name="name" id="project_name" label="Nombre del proyecto"
                    value="{{ old('name', $project->name) }}" required />
                <x-button type="submit" text="Actualizar" icon="save" typeButton="secondary" class="w-full sm:w-auto" />
            </div>
        </form>

        <div class="mt-6 rounded-lg border border-gray-300 p-4 dark:border-gray-800">
            <h2 class="text-lg font-semibold text-primary-500 dark:text-primary-300">ETAPA 1 - Comparación</h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Agrega items del catálogo o manuales, incorpora costos por proveedor y el sistema calculará automáticamente el costo más bajo por item.
            </p>

            <div class="mt-4 flex flex-col items-center justify-start gap-4 sm:flex-row">
                <div class="relative w-full sm:w-auto">
                    <x-button type="button" icon="arrow-down" typeButton="info" text="Agregar detalle"
                        class="show-options w-full sm:w-auto" data-target="#options-add-project-item" size="normal"
                        data-align="right" />
                    <div class="options absolute right-0 top-0 z-10 mt-1 hidden w-max rounded-lg border border-gray-300 bg-white p-2 dark:border-gray-800 dark:bg-gray-950"
                        id="options-add-project-item">
                        <ul class="flex flex-col text-sm">
                            <li>
                                <button type="button"
                                    class="flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900"
                                    data-drawer-target="drawer-new-product" data-drawer-show="drawer-new-product"
                                    aria-controls="drawer-new-product">
                                    <x-icon icon="pencil" class="h-4 w-4" />
                                    Item manual
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

                <x-button type="button" text="Seleccionar producto existente" icon="arrow-double-next" typeButton="success"
                    data-modal-target="selected-product" data-modal-toggle="selected-product" class="w-full sm:w-auto" />
            </div>

            <div id="table-products-dte" class="mt-4">
                @include('business.projects.partials.stage1-items-table', [
                    'project' => $project,
                    'comparison' => $comparison,
                ])
            </div>
        </div>

        <form action="{{ Route('business.projects.costing.update', $project->id) }}" method="POST"
            class="mt-6 rounded-lg border border-gray-300 p-4 dark:border-gray-800">
            @csrf
            <h2 class="text-lg font-semibold text-primary-500 dark:text-primary-300">ETAPA 2 - Costeo</h2>

            <div class="mt-4 grid gap-4 md:grid-cols-4">
                <x-input type="number" name="profit_margin_percent" id="profit_margin_percent"
                    label="% ganancia" step="0.01" min="0" max="99.99"
                    value="{{ old('profit_margin_percent', $costing['profit_margin_percent'] ?? 0) }}" required />
                <x-input type="text" name="labor_concept" id="labor_concept" label="Concepto mano de obra"
                    value="{{ old('labor_concept', $costing['labor_concept'] ?? 'Mano de obra') }}" />
                <x-input type="number" name="labor_cost" id="labor_cost" label="Costo mano de obra"
                    step="0.01" min="0" value="{{ old('labor_cost', $costing['labor_cost'] ?? 0) }}" required />
                <x-input type="number" name="advance_percent" id="advance_percent" label="% anticipo"
                    step="0.01" min="0" max="100"
                    value="{{ old('advance_percent', $costing['advance_percent'] ?? 0) }}" required />
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full border border-gray-300 text-sm dark:border-gray-700">
                    <thead class="bg-gray-100 dark:bg-gray-900">
                        <tr>
                            <th class="border p-2">#</th>
                            <th class="border p-2">Cantidad</th>
                            <th class="border p-2">Unidad</th>
                            <th class="border p-2">Descripción</th>
                            <th class="border p-2">Código</th>
                            <th class="border p-2">Costo Individual</th>
                            <th class="border p-2">Costo Total</th>
                            <th class="border p-2">Precio Unitario</th>
                            <th class="border p-2">% Descuento</th>
                            <th class="border p-2">Precio Unit. c/ desc.</th>
                            <th class="border p-2">Precio Total</th>
                            <th class="border p-2">Ganancia</th>
                            <th class="border p-2">Proveedor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($costing['items'] ?? [] as $row)
                            <tr class="js-cost-row" data-row-id="{{ $row['id'] }}" data-qty="{{ (float) $row['cantidad'] }}"
                                data-cost-individual="{{ (float) $row['cost_individual'] }}">
                                <td class="border p-2">{{ $loop->iteration }}</td>
                                <td class="border p-2">{{ number_format((float) $row['cantidad'], 2) }}</td>
                                <td class="border p-2">{{ $row['unidad_medida'] ?: '-' }}</td>
                                <td class="border p-2">{{ $row['descripcion'] }}</td>
                                <td class="border p-2">{{ $row['codigo'] ?: '-' }}</td>
                                <td class="border p-2 js-cost-individual">${{ number_format((float) $row['cost_individual'], 4) }}</td>
                                <td class="border p-2 js-cost-total" id="cost-total-{{ $row['id'] }}">${{ number_format((float) $row['cost_total'], 4) }}</td>
                                <td class="border p-2 js-price-unit" id="price-unit-{{ $row['id'] }}">${{ number_format((float) $row['price_unit'], 4) }}</td>
                                <td class="border p-2">
                                    <input type="number" name="discounts[{{ $row['id'] }}]"
                                        value="{{ old('discounts.' . $row['id'], $row['discount_percent']) }}"
                                        min="0" max="100" step="0.01"
                                        class="js-discount-input w-24 rounded border border-gray-300 p-1 dark:border-gray-700 dark:bg-gray-900"
                                        data-row-id="{{ $row['id'] }}">
                                </td>
                                <td class="border p-2 js-price-unit-discount" id="price-unit-discount-{{ $row['id'] }}">${{ number_format((float) $row['price_unit_with_discount'], 4) }}</td>
                                <td class="border p-2 js-price-total" id="price-total-{{ $row['id'] }}">${{ number_format((float) $row['price_total'], 4) }}</td>
                                <td class="border p-2 js-gain" id="gain-{{ $row['id'] }}">${{ number_format((float) $row['gain'], 4) }}</td>
                                <td class="border p-2">{{ $row['provider'] ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="border p-4 text-center text-gray-500">No hay datos para costeo.</td>
                            </tr>
                        @endforelse

                        <tr class="bg-gray-50 dark:bg-gray-900/50">
                            <td class="border p-2 font-semibold">{{ count($costing['items'] ?? []) + 1 }}</td>
                            <td class="border p-2">1.00</td>
                            <td class="border p-2">-</td>
                            <td class="border p-2" id="labor-concept-cell">{{ $costing['labor_concept'] ?? 'Mano de obra' }}</td>
                            <td class="border p-2">MANO-OBRA</td>
                            <td class="border p-2" id="labor-cost-cell">${{ number_format((float) ($costing['labor_cost'] ?? 0), 4) }}</td>
                            <td class="border p-2" id="labor-total-cell">${{ number_format((float) ($costing['labor_cost'] ?? 0), 4) }}</td>
                            <td class="border p-2" id="labor-price-unit-cell">${{ number_format((float) ($costing['labor_cost'] ?? 0), 4) }}</td>
                            <td class="border p-2">0.00%</td>
                            <td class="border p-2" id="labor-price-unit-discount-cell">${{ number_format((float) ($costing['labor_cost'] ?? 0), 4) }}</td>
                            <td class="border p-2" id="labor-price-total-cell">${{ number_format((float) ($costing['labor_cost'] ?? 0), 4) }}</td>
                            <td class="border p-2">$0.0000</td>
                            <td class="border p-2">-</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 rounded-lg border border-dashed border-blue-500 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-950/30">
                <h3 class="font-semibold text-blue-700 dark:text-blue-300">Resumen</h3>
                <div class="mt-3 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <tbody>
                            <tr>
                                <td class="py-1 pr-4 font-medium">Inversión de materiales (Sin IVA)</td>
                                <td class="py-1" id="summary-materials-cost-without-iva">${{ number_format((float) ($costing['summary']['materials_cost_without_iva'] ?? 0), 4) }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 pr-4 font-medium">Inversión de materiales (Con IVA)</td>
                                <td class="py-1" id="summary-materials-cost-with-iva">${{ number_format((float) ($costing['summary']['materials_cost_with_iva'] ?? 0), 4) }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 pr-4 font-medium">Venta de materiales (Sin IVA)</td>
                                <td class="py-1" id="summary-materials-sale-without-iva">${{ number_format((float) ($costing['summary']['materials_sale_without_iva'] ?? 0), 4) }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 pr-4 font-medium">Venta de materiales (Con IVA)</td>
                                <td class="py-1" id="summary-materials-sale-with-iva">${{ number_format((float) ($costing['summary']['materials_sale_with_iva'] ?? 0), 4) }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 pr-4 font-medium">Ganancia</td>
                                <td class="py-1" id="summary-gain-without-iva">${{ number_format((float) ($costing['summary']['gain_without_iva'] ?? 0), 4) }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 pr-4 font-bold" id="summary-advance-label">Anticipo ({{ number_format((float) ($costing['summary']['advance_percent'] ?? 0), 2) }}%)</td>
                                <td class="py-1 font-bold" id="summary-advance-amount">${{ number_format((float) ($costing['summary']['advance_amount'] ?? 0), 4) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 flex justify-end">
                <x-button type="submit" text="Recalcular costeo" icon="calculator" typeButton="primary"
                    class="w-full sm:w-auto" />
            </div>
        </form>

        <form action="{{ Route('business.projects.prepare-quotation', $project->id) }}" method="POST"
            class="mt-6 rounded-lg border border-gray-300 p-4 dark:border-gray-800">
            @csrf
            <h2 class="text-lg font-semibold text-primary-500 dark:text-primary-300">ETAPA 3 - Cotización</h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Al completar comparación y costeo, puedes convertir este proyecto a cotización y continuar con el flujo normal.
            </p>

            <div class="mt-4 grid gap-4 md:grid-cols-[1fr_auto] md:items-end">
                <x-select label="Tipo de DTE objetivo" name="dte_type" id="project_dte_type"
                    :options="['01' => 'Factura', '03' => 'Credito fiscal']"
                    value="{{ old('dte_type', '01') }}"
                    selected="{{ old('dte_type', '01') }}"
                    :search="false" />
                <x-button type="submit" text="Convertir a cotización" icon="arrow-right" typeButton="success"
                    class="w-full sm:w-auto" />
            </div>

            @if ($project->quotation_id)
                <div class="mt-3 rounded-lg border border-green-300 bg-green-50 p-3 text-sm text-green-700 dark:border-green-800 dark:bg-green-950/30 dark:text-green-300">
                    Cotización vinculada: #{{ $project->quotation_id }}.
                    <a href="{{ Route('business.quotations.show', $project->quotation_id) }}" class="font-semibold underline">Ver cotización</a>
                </div>
            @endif
        </form>
    </section>

    @include('layouts.partials.business.dte.modals.modal-select-product', [
        'selectProductAction' => Route('business.projects.items.store-existing', $project->id),
        'productSelectorComponent' => 'business.tables.dte-product',
        'productSelectLookupRouteName' => 'business.projects.products.select',
        'productSelectLookupRouteParams' => ['project' => $project->id],
        'priceInputMode' => 'without_iva',
    ])

    @include('layouts.partials.business.dte.drawer-new-product', [
        'newProductAction' => Route('business.projects.items.store-manual', $project->id),
        'projectMode' => true,
        'priceInputMode' => 'without_iva',
    ])
@endsection

@push('scripts')
    @vite('resources/js/dte.js')
    <script>
        (function() {
            function toNumber(value) {
                const parsed = parseFloat(value);
                return Number.isNaN(parsed) ? 0 : parsed;
            }

            function clamp(value, min, max) {
                return Math.max(min, Math.min(max, value));
            }

            function money(value) {
                return '$' + toNumber(value).toFixed(4);
            }

            function recalculateProjectCostingPreview() {
                const marginPercent = clamp(toNumber($('#profit_margin_percent').val()), 0, 99.99);
                const divisor = 1 - (marginPercent / 100);
                const advancePercent = clamp(toNumber($('#advance_percent').val()), 0, 100);

                let materialsCostWithoutIva = 0;
                let materialsSaleWithoutIva = 0;
                let gainWithoutIva = 0;

                $('.js-cost-row').each(function() {
                    const row = $(this);
                    const rowId = row.data('rowId');
                    const qty = toNumber(row.data('qty'));
                    const costIndividual = toNumber(row.data('costIndividual'));
                    const discountInput = $('.js-discount-input[data-row-id="' + rowId + '"]');

                    let discountPercent = clamp(toNumber(discountInput.val()), 0, 100);
                    if (toNumber(discountInput.val()) !== discountPercent) {
                        discountInput.val(discountPercent.toFixed(2));
                    }

                    const costTotal = costIndividual * qty;
                    const priceUnit = divisor > 0 ? (costIndividual / divisor) : 0;
                    const priceUnitWithDiscount = priceUnit - (priceUnit * (discountPercent / 100));
                    const priceTotal = priceUnitWithDiscount * qty;
                    const gain = priceTotal - costTotal;

                    materialsCostWithoutIva += costTotal;
                    materialsSaleWithoutIva += priceTotal;
                    gainWithoutIva += gain;

                    $('#cost-total-' + rowId).text(money(costTotal));
                    $('#price-unit-' + rowId).text(money(priceUnit));
                    $('#price-unit-discount-' + rowId).text(money(priceUnitWithDiscount));
                    $('#price-total-' + rowId).text(money(priceTotal));
                    $('#gain-' + rowId).text(money(gain));
                });

                const laborConcept = ($('#labor_concept').val() || '').trim() || 'Mano de obra';
                const laborCost = Math.max(0, toNumber($('#labor_cost').val()));
                $('#labor-concept-cell').text(laborConcept);
                $('#labor-cost-cell').text(money(laborCost));
                $('#labor-total-cell').text(money(laborCost));
                $('#labor-price-unit-cell').text(money(laborCost));
                $('#labor-price-unit-discount-cell').text(money(laborCost));
                $('#labor-price-total-cell').text(money(laborCost));

                const materialsCostWithIva = materialsCostWithoutIva * 1.13;
                const materialsSaleWithIva = materialsSaleWithoutIva * 1.13;
                const advanceAmount = materialsSaleWithIva * (advancePercent / 100);

                $('#summary-materials-cost-without-iva').text(money(materialsCostWithoutIva));
                $('#summary-materials-cost-with-iva').text(money(materialsCostWithIva));
                $('#summary-materials-sale-without-iva').text(money(materialsSaleWithoutIva));
                $('#summary-materials-sale-with-iva').text(money(materialsSaleWithIva));
                $('#summary-gain-without-iva').text(money(gainWithoutIva));
                $('#summary-advance-label').text('Anticipo (' + advancePercent.toFixed(2) + '%)');
                $('#summary-advance-amount').text(money(advanceAmount));
            }

            $(document).on('input change', '#profit_margin_percent, #labor_concept, #labor_cost, #advance_percent, .js-discount-input', function() {
                recalculateProjectCostingPreview();
            });

            recalculateProjectCostingPreview();
        })();
    </script>
@endpush
