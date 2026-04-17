<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessProduct;
use App\Models\BusinessProductCostVariant;
use App\Models\BusinessPriceVariant;
use App\Models\Project;
use App\Services\OctopusService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct(private OctopusService $octopusService)
    {
    }

    public function index()
    {
        $business = $this->resolveBusinessOrFail();

        $projects = Project::where('business_id', $business->id)
            ->orderByDesc('updated_at')
            ->paginate(10);

        return view('business.projects.index', [
            'projects' => $projects,
            'business' => $business,
        ]);
    }

    public function create()
    {
        $this->resolveBusinessOrFail();

        return view('business.projects.create');
    }

    public function store(Request $request)
    {
        $business = $this->resolveBusinessOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $project = Project::create([
            'business_id' => $business->id,
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'status' => 'draft',
            'content' => [
                'items' => [],
            ],
            'comparison_meta' => [
                'total_best_cost' => 0,
            ],
            'costing_meta' => [
                'profit_margin_percent' => 0,
                'labor_concept' => 'Mano de obra',
                'labor_cost' => 0,
                'advance_percent' => 0,
            ],
        ]);

        return redirect()->route('business.projects.edit', $project->id)
            ->with('success', 'Exito')
            ->with('success_message', 'Proyecto creado correctamente.');
    }

    public function show(string $id)
    {
        return redirect()->route('business.projects.edit', $id);
    }

    public function edit(string $id)
    {
        $business = $this->resolveBusinessOrFail();
        $project = $this->findProject($id);

        $comparison = $this->computeComparisonData($project);
        $costing = $this->computeCostingData($project, $comparison);

        $unidades = $this->octopusService->getCatalog('CAT-014');

        return view('business.projects.edit', [
            'project' => $project,
            'business' => $business,
            'number' => '01',
            'dte' => ['documentos_relacionados' => []],
            'unidades_medidas' => $unidades,
            'comparison' => $comparison,
            'costing' => $costing,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $this->resolveBusinessOrFail();
        $project = $this->findProject($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $project->update([
            'name' => $validated['name'],
        ]);

        return redirect()->route('business.projects.edit', $project->id)
            ->with('success', 'Exito')
            ->with('success_message', 'Nombre del proyecto actualizado.');
    }

    public function destroy(string $id)
    {
        $this->resolveBusinessOrFail();
        $project = $this->findProject($id);
        $project->delete();

        return redirect()->route('business.projects.index')
            ->with('success', 'Exito')
            ->with('success_message', 'Proyecto eliminado correctamente.');
    }

    public function selectProduct(Request $request, string $projectId)
    {
        $business = $this->resolveBusinessOrFail();
        $this->findProject($projectId);

        $product = BusinessProduct::where('business_id', $business->id)
            ->find($request->input('product_id'));

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado',
            ]);
        }

        $priceVariantsEnabled = (bool) ($business->price_variants_enabled ?? false);
        $productCostsEnabled = (bool) ($business->enable_product_costs ?? false);

        $priceVariants = [];
        if ($priceVariantsEnabled) {
            $priceVariants = BusinessPriceVariant::where('business_id', $business->id)
                ->orderBy('name')
                ->get()
                ->map(function ($variant) use ($product) {
                    $resolved = $product->resolvePriceForVariant($variant->id);

                    return [
                        'id' => $variant->id,
                        'name' => $variant->name,
                        'effective_price_without_iva' => (float) $resolved['without_iva'],
                        'effective_price_with_iva' => (float) $resolved['with_iva'],
                    ];
                })
                ->values();
        }

        $productCostVariants = [];
        if ($productCostsEnabled) {
            $productCostVariants = $product->costVariants()
                ->with('priceVariant')
                ->orderBy('nombre_proveedor')
                ->get()
                ->map(function ($costVariant) use ($product) {
                    $resolved = $product->resolvePriceForCostVariant($costVariant->id);

                    return [
                        'id' => $costVariant->id,
                        'nombre_proveedor' => $costVariant->nombre_proveedor,
                        'costo_final' => (float) $costVariant->costo_final,
                        'price_variant_id' => $costVariant->price_variant_id,
                        'price_variant_name' => $costVariant->priceVariant?->name,
                        'effective_price_without_iva' => (float) $resolved['without_iva'],
                        'effective_price_with_iva' => (float) $resolved['with_iva'],
                    ];
                })
                ->values();
        }

        $productData = $product->toArray();
        $productData['stockDisponible'] = null;
        $productData['estadoStock'] = null;
        $productData['inventory_source'] = 'none';

        return response()->json([
            'success' => true,
            'product' => $productData,
            'dte' => '01',
            'customer' => null,
            'price_variants_enabled' => $priceVariantsEnabled,
            'price_variants' => $priceVariants,
            'customer_price_variant_id' => null,
            'enable_product_costs' => $productCostsEnabled,
            'product_cost_variants' => $productCostVariants,
        ]);
    }

    public function storeExistingItem(Request $request, string $projectId)
    {
        $business = $this->resolveBusinessOrFail();
        $project = $this->findProject($projectId);

        $validated = $request->validate([
            'product_id' => 'required|integer',
            'cantidad' => 'required|numeric|min:0.00000001',
        ]);

        $product = BusinessProduct::where('business_id', $business->id)
            ->find($validated['product_id']);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado',
            ], 422);
        }

        $optimalPair = $this->resolveOptimalCatalogPair($product);

        $items = $this->projectItems($project);

        $items[] = [
            'id' => $this->newItemId(),
            'source' => 'catalog',
            'product_id' => (int) $product->id,
            'codigo' => (string) ($product->codigo ?? ''),
            'descripcion' => (string) ($product->descripcion ?? ''),
            'unidad_medida' => (string) ($product->uniMedida ?? ''),
            'cantidad' => (float) $validated['cantidad'],
            'discount_percent' => 0,
            'suppliers' => [],
            'lock_catalog_pair' => true,
            'apply_margin' => false,
            'fixed_cost_individual' => $this->round8((float) $optimalPair['cost']),
            'fixed_price_without_iva' => $this->round8((float) $optimalPair['price_without_iva']),
            'fixed_price_with_iva' => $this->round8((float) $optimalPair['price_with_iva']),
            'fixed_pair_label' => (string) ($optimalPair['label'] ?? ''),
            'fixed_pair_source' => (string) ($optimalPair['source'] ?? 'base_price_only'),
        ];

        $this->saveProjectItems($project, $items);
        $comparison = $this->computeComparisonData($project, $items);

        return response()->json([
            'success' => true,
            'reload' => true,
            'table_products' => view('business.projects.partials.stage1-items-table', [
                'project' => $project,
                'comparison' => $comparison,
            ])->render(),
        ]);
    }

    public function storeManualItem(Request $request, string $projectId)
    {
        $this->resolveBusinessOrFail();
        $project = $this->findProject($projectId);

        $validated = $request->validate([
            'codigo' => 'nullable|string|max:100',
            'unidad_medida' => 'required|string|max:50',
            'descripcion' => 'required|string|max:255',
            'cantidad' => 'required|numeric|min:0.00000001',
        ]);

        $items = $this->projectItems($project);
        $items[] = [
            'id' => $this->newItemId(),
            'source' => 'manual',
            'product_id' => null,
            'codigo' => (string) ($validated['codigo'] ?? ''),
            'descripcion' => (string) $validated['descripcion'],
            'unidad_medida' => (string) $validated['unidad_medida'],
            'cantidad' => (float) $validated['cantidad'],
            'discount_percent' => 0,
            'suppliers' => [],
        ];

        $this->saveProjectItems($project, $items);
        $comparison = $this->computeComparisonData($project, $items);

        return response()->json([
            'success' => true,
            'reload' => true,
            'message' => 'Detalle agregado correctamente.',
            'table_data' => view('business.projects.partials.stage1-items-table', [
                'project' => $project,
                'comparison' => $comparison,
            ])->render(),
            'table' => 'products-dte',
            'drawer' => 'drawer-new-product',
        ]);
    }

    public function addSupplierCost(Request $request, string $projectId, string $itemId)
    {
        $this->resolveBusinessOrFail();
        $project = $this->findProject($projectId);

        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'unit_cost' => 'required|numeric|min:0',
        ]);

        $items = $this->projectItems($project);
        foreach ($items as &$item) {
            if (($item['id'] ?? null) !== $itemId) {
                continue;
            }

            if ((string) ($item['source'] ?? '') === 'catalog' && (bool) ($item['lock_catalog_pair'] ?? false)) {
                return redirect()->route('business.projects.edit', $project->id)
                    ->with('error', 'Error')
                    ->with('error_message', 'El costo/precio de este producto de catálogo es fijo y no es editable.');
            }

            $suppliers = isset($item['suppliers']) && is_array($item['suppliers'])
                ? $item['suppliers']
                : [];

            $suppliers[] = [
                'name' => trim((string) $validated['supplier_name']),
                'unit_cost' => (float) $validated['unit_cost'],
            ];

            $item['suppliers'] = $this->normalizeSuppliers($suppliers);
            break;
        }

        unset($item);

        $this->saveProjectItems($project, $items);

        return redirect()->route('business.projects.edit', $project->id)
            ->with('success', 'Exito')
            ->with('success_message', 'Costo por proveedor agregado.');
    }

    public function deleteItem(string $projectId, string $itemId)
    {
        $this->resolveBusinessOrFail();
        $project = $this->findProject($projectId);

        $items = array_values(array_filter(
            $this->projectItems($project),
            fn(array $item) => ($item['id'] ?? null) !== $itemId
        ));

        $this->saveProjectItems($project, $items);

        return redirect()->route('business.projects.edit', $project->id)
            ->with('success', 'Exito')
            ->with('success_message', 'Item eliminado correctamente.');
    }

    public function updateCosting(Request $request, string $projectId)
    {
        $this->resolveBusinessOrFail();
        $project = $this->findProject($projectId);

        $validated = $request->validate([
            'profit_margin_percent' => 'required|numeric|min:0|max:99.99',
            'labor_concept' => 'nullable|string|max:255',
            'labor_cost' => 'required|numeric|min:0',
            'advance_percent' => 'required|numeric|min:0|max:100',
            'discounts' => 'nullable|array',
            'discounts.*' => 'nullable|numeric|min:0|max:100',
        ]);

        $items = $this->projectItems($project);
        $discounts = $validated['discounts'] ?? [];

        foreach ($items as &$item) {
            $itemId = (string) ($item['id'] ?? '');
            if ($itemId === '') {
                continue;
            }

            $discount = isset($discounts[$itemId]) ? (float) $discounts[$itemId] : (float) ($item['discount_percent'] ?? 0);
            $item['discount_percent'] = $this->clamp($discount, 0, 100);
        }

        unset($item);

        $project->costing_meta = [
            'profit_margin_percent' => (float) $validated['profit_margin_percent'],
            'labor_concept' => trim((string) ($validated['labor_concept'] ?? '')) !== ''
                ? trim((string) $validated['labor_concept'])
                : 'Mano de obra',
            'labor_cost' => (float) $validated['labor_cost'],
            'advance_percent' => (float) $validated['advance_percent'],
        ];

        $this->saveProjectItems($project, $items);

        return redirect()->route('business.projects.edit', $project->id)
            ->with('success', 'Exito')
            ->with('success_message', 'Costeo actualizado correctamente.');
    }

    public function comparisonPdf(string $projectId)
    {
        $this->resolveBusinessOrFail();
        $project = $this->findProject($projectId);

        $comparison = $this->computeComparisonData($project);

        $pdf = Pdf::loadView('business.projects.pdf-stage1', [
            'project' => $project,
            'comparison' => $comparison,
        ])->setPaper('letter', 'landscape');

        return $pdf->stream('proyecto-comparacion-' . $project->id . '.pdf');
    }

    public function costingPdf(string $projectId)
    {
        $this->resolveBusinessOrFail();
        $project = $this->findProject($projectId);

        $comparison = $this->computeComparisonData($project);
        $costing = $this->computeCostingData($project, $comparison);

        $pdf = Pdf::loadView('business.projects.pdf-stage2', [
            'project' => $project,
            'comparison' => $comparison,
            'costing' => $costing,
        ])->setPaper('letter', 'landscape');

        return $pdf->stream('proyecto-costeo-' . $project->id . '.pdf');
    }

    public function prepareQuotation(Request $request, string $projectId)
    {
        $this->resolveBusinessOrFail();
        $project = $this->findProject($projectId);

        $validated = $request->validate([
            'dte_type' => 'required|in:01,03',
        ]);

        $comparison = $this->computeComparisonData($project);
        if (empty($comparison['items'])) {
            return redirect()->route('business.projects.edit', $project->id)
                ->with('error', 'Error')
                ->with('error_message', 'Debes agregar al menos un item en la Etapa 1.');
        }

        $costing = $this->computeCostingData($project, $comparison);
        $dte = $this->buildDteFromCosting($project, $costing, $validated['dte_type']);

        session([
            'dte' => $dte,
            'project_source_id' => $project->id,
        ]);

        return redirect()
            ->route('business.quotations.create', [
                'from_project' => 1,
                'dte_type' => $validated['dte_type'],
            ])
            ->withInput([
                'name' => 'Cotizacion del proyecto: ' . $project->name,
                'dte_type' => $validated['dte_type'],
            ])
            ->with('success', 'Exito')
            ->with('success_message', 'Proyecto preparado para convertirse en cotizacion.');
    }

    private function resolveBusinessOrFail(): Business
    {
        $business = Business::findOrFail((int) session('business'));

        if (!(bool) ($business->quotation_enabled ?? false) || !(bool) ($business->has_projects_enabled ?? false)) {
            abort(403, 'El modulo de proyectos no esta habilitado para este negocio.');
        }

        return $business;
    }

    private function findProject(string $id): Project
    {
        return Project::where('business_id', session('business'))->findOrFail($id);
    }

    private function projectItems(Project $project): array
    {
        $content = is_array($project->content) ? $project->content : [];
        $items = $content['items'] ?? [];

        return is_array($items) ? $items : [];
    }

    private function saveProjectItems(Project $project, array $items): void
    {
        $content = is_array($project->content) ? $project->content : [];
        $content['items'] = array_values($items);
        $project->content = $content;

        if ($project->status === 'draft' && count($items) > 0) {
            $project->status = 'comparison';
        }

        $project->save();
    }

    private function computeComparisonData(Project $project, ?array $items = null): array
    {
        $items = $items ?? $this->projectItems($project);

        $providerNames = [];
        $rows = [];
        $totalBestCost = 0;

        foreach ($items as $item) {
            $isCatalogPairLocked = (string) ($item['source'] ?? '') === 'catalog'
                && (bool) ($item['lock_catalog_pair'] ?? false);

            $suppliers = [];
            $supplierMap = [];
            $bestUnitCost = null;
            $bestSupplier = null;

            if ($isCatalogPairLocked) {
                $bestUnitCost = max(0, (float) ($item['fixed_cost_individual'] ?? 0));
                $bestSupplier = trim((string) ($item['fixed_pair_label'] ?? '')) !== ''
                    ? trim((string) $item['fixed_pair_label'])
                    : 'Par fijo';
            } else {
                $suppliers = $this->normalizeSuppliers($item['suppliers'] ?? []);

                foreach ($suppliers as $supplier) {
                    $name = trim((string) ($supplier['name'] ?? ''));
                    if ($name === '') {
                        continue;
                    }

                    $unitCost = (float) ($supplier['unit_cost'] ?? 0);
                    $supplierMap[$name] = $unitCost;
                    $providerNames[$name] = $name;

                    if ($bestUnitCost === null || $unitCost < $bestUnitCost) {
                        $bestUnitCost = $unitCost;
                        $bestSupplier = $name;
                    }
                }
            }

            $qty = (float) ($item['cantidad'] ?? 0);
            $bestUnitCost = $bestUnitCost ?? 0;
            $bestTotalCost = $bestUnitCost * $qty;
            $totalBestCost += $bestTotalCost;

            $rows[] = [
                'id' => (string) ($item['id'] ?? ''),
                'cantidad' => $qty,
                'unidad_medida' => (string) ($item['unidad_medida'] ?? ''),
                'descripcion' => (string) ($item['descripcion'] ?? ''),
                'codigo' => (string) ($item['codigo'] ?? ''),
                'product_id' => $item['product_id'] ?? null,
                'suppliers' => $suppliers,
                'supplier_map' => $supplierMap,
                'best_unit_cost' => $bestUnitCost,
                'best_total_cost' => $bestTotalCost,
                'best_supplier' => $bestSupplier,
                'discount_percent' => $this->clamp((float) ($item['discount_percent'] ?? 0), 0, 100),
                'source' => (string) ($item['source'] ?? 'manual'),
                'lock_catalog_pair' => $isCatalogPairLocked,
                'apply_margin' => isset($item['apply_margin'])
                    ? (bool) $item['apply_margin']
                    : !$isCatalogPairLocked,
                'fixed_price_without_iva' => $this->round8((float) ($item['fixed_price_without_iva'] ?? 0)),
            ];
        }

        ksort($providerNames);

        $comparison = [
            'provider_names' => array_values($providerNames),
            'items' => $rows,
            'total_best_cost' => $this->round8($totalBestCost),
        ];

        $meta = is_array($project->comparison_meta) ? $project->comparison_meta : [];
        $meta['total_best_cost'] = $comparison['total_best_cost'];
        $project->comparison_meta = $meta;

        if (count($rows) > 0 && in_array($project->status, ['draft', 'comparison'], true)) {
            $project->status = 'costing';
        }

        $project->save();

        return $comparison;
    }

    private function computeCostingData(Project $project, ?array $comparison = null): array
    {
        $comparison = $comparison ?? $this->computeComparisonData($project);
        $meta = is_array($project->costing_meta) ? $project->costing_meta : [];

        $profitMarginPercent = (float) ($meta['profit_margin_percent'] ?? 0);
        $profitMarginPercent = $this->clamp($profitMarginPercent, 0, 99.99);

        $laborConcept = trim((string) ($meta['labor_concept'] ?? ''));
        if ($laborConcept === '') {
            $laborConcept = 'Mano de obra';
        }

        $laborCost = max(0, (float) ($meta['labor_cost'] ?? 0));
        $advancePercent = $this->clamp((float) ($meta['advance_percent'] ?? 0), 0, 100);

        $divisor = 1 - ($profitMarginPercent / 100);

        $rows = [];
        $materialsCostWithoutIva = 0;
        $materialsSaleWithoutIva = 0;
        $gainWithoutIva = 0;

        foreach ($comparison['items'] as $item) {
            $qty = (float) ($item['cantidad'] ?? 0);
            $costIndividual = (float) ($item['best_unit_cost'] ?? 0);
            $costTotal = $costIndividual * $qty;
            $applyMargin = isset($item['apply_margin']) ? (bool) $item['apply_margin'] : true;
            $fixedPriceWithoutIva = max(0, (float) ($item['fixed_price_without_iva'] ?? 0));
            $priceUnit = $applyMargin
                ? ($divisor > 0 ? $costIndividual / $divisor : 0)
                : $fixedPriceWithoutIva;
            $discountPercent = $this->clamp((float) ($item['discount_percent'] ?? 0), 0, 100);
            $priceUnitWithDiscount = $priceUnit - ($priceUnit * ($discountPercent / 100));
            $priceTotal = $priceUnitWithDiscount * $qty;
            $gain = $priceTotal - $costTotal;

            $materialsCostWithoutIva += $costTotal;
            $materialsSaleWithoutIva += $priceTotal;
            $gainWithoutIva += $gain;

            $rows[] = [
                'id' => $item['id'],
                'product_id' => $item['product_id'] ?? null,
                'cantidad' => $qty,
                'unidad_medida' => $item['unidad_medida'],
                'descripcion' => $item['descripcion'],
                'codigo' => $item['codigo'],
                'cost_individual' => $this->round8($costIndividual),
                'cost_total' => $this->round8($costTotal),
                'price_unit' => $this->round8($priceUnit),
                'discount_percent' => $this->round8($discountPercent),
                'price_unit_with_discount' => $this->round8($priceUnitWithDiscount),
                'price_total' => $this->round8($priceTotal),
                'gain' => $this->round8($gain),
                'provider' => $item['best_supplier'] ?? null,
                'apply_margin' => $applyMargin,
                'fixed_price_without_iva' => $this->round8($fixedPriceWithoutIva),
            ];
        }

        $summary = [
            'materials_cost_without_iva' => $this->round8($materialsCostWithoutIva),
            'materials_cost_with_iva' => $this->round8($materialsCostWithoutIva * 1.13),
            'materials_sale_without_iva' => $this->round8($materialsSaleWithoutIva),
            'materials_sale_with_iva' => $this->round8($materialsSaleWithoutIva * 1.13),
            'gain_without_iva' => $this->round8($gainWithoutIva),
            'advance_percent' => $this->round8($advancePercent),
            'advance_amount' => $this->round8(($materialsSaleWithoutIva * 1.13) * ($advancePercent / 100)),
        ];

        return [
            'profit_margin_percent' => $profitMarginPercent,
            'labor_concept' => $laborConcept,
            'labor_cost' => $this->round8($laborCost),
            'advance_percent' => $advancePercent,
            'items' => $rows,
            'summary' => $summary,
        ];
    }

    private function buildDteFromCosting(Project $project, array $costing, string $dteType): array
    {
        $products = [];

        foreach ($costing['items'] as $item) {
            $qty = (float) $item['cantidad'];
            $priceWithoutIva = (float) $item['price_unit_with_discount'];
            $priceWithIva = $priceWithoutIva * 1.13;
            $lineWithoutIva = $priceWithoutIva * $qty;
            $lineWithIva = $priceWithIva * $qty;

            $ventasGravadas = $dteType === '01' ? $lineWithIva : $lineWithoutIva;
            $iva = $dteType === '01'
                ? (($lineWithIva / 1.13) * 0.13)
                : ($lineWithoutIva * 0.13);

            $products[] = [
                'id' => $this->nextSequentialId($products, 2000),
                'product' => null,
                'product_id' => $item['product_id'] ?? null,
                'codigo' => $item['codigo'],
                'unidad_medida' => $item['unidad_medida'],
                'descripcion' => $item['descripcion'],
                'cantidad' => $qty,
                'tipo' => 'Gravada',
                'precio' => $this->round8($dteType === '01' ? $priceWithIva : $priceWithoutIva),
                'precio_sin_tributos' => $this->round8($priceWithoutIva),
                'descuento' => 0,
                'ventas_gravadas' => $this->round8($ventasGravadas),
                'ventas_exentas' => 0,
                'ventas_no_sujetas' => 0,
                'total' => $this->round8($ventasGravadas),
                'tipo_item' => 1,
                'iva' => $this->round8($iva),
                'documento_relacionado' => null,
                'project_cost_without_iva' => $this->round8((float) $item['cost_individual']),
                'project_cost_with_iva' => $this->round8((float) $item['cost_individual'] * 1.13),
                'project_best_supplier' => $item['provider'],
                'project_discount_percent' => $this->round8((float) $item['discount_percent']),
            ];
        }

        $laborCost = (float) ($costing['labor_cost'] ?? 0);
        if ($laborCost > 0) {
            $laborDescription = trim((string) ($costing['labor_concept'] ?? 'Mano de obra'));
            $priceWithoutIva = $laborCost;
            $priceWithIva = $laborCost * 1.13;
            $ventasGravadas = $dteType === '01' ? $priceWithIva : $priceWithoutIva;
            $iva = $dteType === '01'
                ? (($priceWithIva / 1.13) * 0.13)
                : ($priceWithoutIva * 0.13);

            $products[] = [
                'id' => $this->nextSequentialId($products, 2000),
                'product' => null,
                'product_id' => null,
                'codigo' => 'MANO-OBRA',
                'unidad_medida' => '99',
                'descripcion' => $laborDescription,
                'cantidad' => 1,
                'tipo' => 'Gravada',
                'precio' => $this->round8($dteType === '01' ? $priceWithIva : $priceWithoutIva),
                'precio_sin_tributos' => $this->round8($priceWithoutIva),
                'descuento' => 0,
                'ventas_gravadas' => $this->round8($ventasGravadas),
                'ventas_exentas' => 0,
                'ventas_no_sujetas' => 0,
                'total' => $this->round8($ventasGravadas),
                'tipo_item' => 2,
                'iva' => $this->round8($iva),
                'documento_relacionado' => null,
                'project_cost_without_iva' => $this->round8($laborCost),
                'project_cost_with_iva' => $this->round8($laborCost * 1.13),
                'project_best_supplier' => null,
                'project_discount_percent' => 0,
            ];
        }

        $totalVentasGravadas = array_sum(array_map(
            fn(array $product) => (float) ($product['ventas_gravadas'] ?? 0),
            $products
        ));

        $ivaTotal = $dteType === '01'
            ? (($totalVentasGravadas / 1.13) * 0.13)
            : ($totalVentasGravadas * 0.13);

        $name = trim((string) $project->name);

        return [
            'type' => $dteType,
            'name' => 'Proyecto: ' . ($name !== '' ? $name : ('#' . $project->id)),
            'products' => $products,
            'subtotal' => $this->round8($totalVentasGravadas),
            'total' => $this->round8($totalVentasGravadas),
            'total_pagar' => $this->round8($totalVentasGravadas),
            'total_descuentos' => 0,
            'total_ventas_gravadas' => $this->round8($totalVentasGravadas),
            'total_ventas_exentas' => 0,
            'total_ventas_no_sujetas' => 0,
            'iva' => $this->round8($ivaTotal),
            'retener_iva' => 'inactive',
            'retener_renta' => 'inactive',
            'percibir_iva' => 'inactive',
            'total_iva_retenido' => 0,
            'isr' => 0,
            'remove_discounts' => false,
            'project_context' => [
                'project_id' => $project->id,
                'project_name' => $project->name,
                'summary' => $costing['summary'] ?? [],
                'profit_margin_percent' => $costing['profit_margin_percent'] ?? 0,
            ],
        ];
    }

    private function supplierListFromProduct(BusinessProduct $product): array
    {
        $suppliers = $product->costVariants()
            ->orderBy('nombre_proveedor')
            ->get()
            ->map(fn(BusinessProductCostVariant $variant) => [
                'name' => trim((string) $variant->nombre_proveedor),
                'unit_cost' => (float) $variant->costo_final,
            ])
            ->toArray();

        if (empty($suppliers)) {
            $baseCost = (float) ($product->cost ?? 0);
            if ($baseCost > 0) {
                $suppliers[] = [
                    'name' => 'Costo base',
                    'unit_cost' => $baseCost,
                ];
            }
        }

        return $this->normalizeSuppliers($suppliers);
    }

    /**
     * Obtiene el par costo/precio más conveniente para productos de catálogo.
     * Prioriza el costo más bajo y, en empate, el precio más bajo.
     */
    private function resolveOptimalCatalogPair(BusinessProduct $product): array
    {
        $basePriceWithoutIva = (float) ($product->precioSinTributos ?? 0);
        $basePriceWithIva = (float) ($product->precioUni ?? 0);

        $candidates = $product->costVariants()
            ->with('priceVariant')
            ->orderBy('costo_final')
            ->orderBy('nombre_proveedor')
            ->get()
            ->map(function (BusinessProductCostVariant $variant) use ($product) {
                $resolvedPrice = $product->resolvePriceForCostVariant((int) $variant->id);

                return [
                    'cost' => max(0, (float) ($variant->costo_final ?? 0)),
                    'price_without_iva' => max(0, (float) ($resolvedPrice['without_iva'] ?? 0)),
                    'price_with_iva' => max(0, (float) ($resolvedPrice['with_iva'] ?? 0)),
                    'supplier_name' => trim((string) ($variant->nombre_proveedor ?? '')),
                    'price_variant_name' => trim((string) ($variant->priceVariant?->name ?? '')),
                ];
            })
            ->values();

        if ($candidates->isEmpty()) {
            return [
                'has_pair' => false,
                'cost' => 0,
                'price_without_iva' => $basePriceWithoutIva,
                'price_with_iva' => $basePriceWithIva,
                'label' => 'Precio base del producto',
                'source' => 'base_price_only',
            ];
        }

        $best = $candidates
            ->sortBy([
                ['cost', 'asc'],
                ['price_without_iva', 'asc'],
            ])
            ->first();

        $labelParts = [];
        if (trim((string) ($best['supplier_name'] ?? '')) !== '') {
            $labelParts[] = (string) $best['supplier_name'];
        }
        if (trim((string) ($best['price_variant_name'] ?? '')) !== '') {
            $labelParts[] = (string) $best['price_variant_name'];
        }

        return [
            'has_pair' => true,
            'cost' => (float) ($best['cost'] ?? 0),
            'price_without_iva' => (float) ($best['price_without_iva'] ?? $basePriceWithoutIva),
            'price_with_iva' => (float) ($best['price_with_iva'] ?? $basePriceWithIva),
            'label' => !empty($labelParts) ? implode(' - ', $labelParts) : 'Par óptimo',
            'source' => 'cost_variant_pair',
        ];
    }

    private function normalizeSuppliers(array $suppliers): array
    {
        $normalized = [];

        foreach ($suppliers as $supplier) {
            $name = trim((string) ($supplier['name'] ?? ''));
            if ($name === '') {
                continue;
            }

            $unitCost = max(0, (float) ($supplier['unit_cost'] ?? 0));
            $key = mb_strtolower($name);

            if (!isset($normalized[$key]) || $unitCost < $normalized[$key]['unit_cost']) {
                $normalized[$key] = [
                    'name' => $name,
                    'unit_cost' => $unitCost,
                ];
            }
        }

        usort($normalized, fn(array $a, array $b) => strcmp($a['name'], $b['name']));

        return array_values($normalized);
    }

    private function newItemId(): string
    {
        return 'item_' . uniqid();
    }

    private function round8(float $value): float
    {
        return round($value, 8);
    }

    private function clamp(float $value, float $min, float $max): float
    {
        return max($min, min($max, $value));
    }

    private function nextSequentialId(array $items, int $max = 2000): int
    {
        $usedIds = collect($items)
            ->pluck('id')
            ->filter(fn($id) => is_numeric($id) && (int) $id >= 1 && (int) $id <= $max)
            ->map(fn($id) => (int) $id)
            ->unique()
            ->all();

        for ($id = 1; $id <= $max; $id++) {
            if (!in_array($id, $usedIds, true)) {
                return $id;
            }
        }

        throw new \RuntimeException("Se alcanzó el límite máximo de {$max} productos para el DTE del proyecto.");
    }
}
