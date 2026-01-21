<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Imports\BusinessProductImport;
use App\Models\Business;
use App\Models\BusinessProduct;
use App\Models\BusinessProductMovement;
use App\Models\BusinessPriceVariant;
use App\Models\BusinessProductPriceVariant;
use App\Models\ProductCategory;
use App\Models\Tributes;
use App\Services\OctopusService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public $unidades_medidas;
    public $octopus_service;

    public function __construct()
    {
        $this->octopus_service = new OctopusService();
        $this->unidades_medidas = $this->octopus_service->getCatalog("CAT-014");
    }

    public function index()
    {
        try {
            $business = Business::find(session("business"));
            return view('business.products.index', ['business' => $business]);
        } catch (\Exception $e) {
            return back()->with([
                'error' => 'Error',
                'error_message' => 'Ha ocurrido un error al cargar los productos'
            ]);
        }
    }

    public function create()
    {
        try {
            $business = Business::find(session("business"));
            $tributes = Tributes::all();
            $categories = ProductCategory::where('business_id', $business->id)->get()->map(function ($category) {
                $category->full_path = $category->getFullPath();
                return $category;
            })->pluck('full_path', 'id')->toArray();
            $categories = ["0" => "Sin categoria"] + $categories;

            $priceVariants = BusinessPriceVariant::where('business_id', $business->id)
                ->orderBy('name')
                ->get();
            
            // Obtener sucursales del negocio
            $sucursales = \App\Models\Sucursal::where('business_id', session("business"))
                ->orderBy('nombre')
                ->get();
            
            // Verificar si el usuario tiene branch_selector y obtener sucursal por defecto
            $businessUser = \App\Models\BusinessUser::where('business_id', session('business'))
                ->where('user_id', auth()->id())
                ->first();
            
            $canSelectBranch = $businessUser ? (bool) $businessUser->branch_selector : false;
            $defaultSucursalId = null;
            
            if (!$canSelectBranch && $businessUser && $businessUser->default_pos_id) {
                $pos = $businessUser->defaultPos;
                if ($pos && $pos->sucursal_id) {
                    $defaultSucursalId = $pos->sucursal_id;
                }
            }
            
            return view('business.products.create', [
                'unidades_medidas' => $this->unidades_medidas,
                'tributes' => $tributes,
                'categories' => $categories,
                'sucursales' => $sucursales,
                'canSelectBranch' => $canSelectBranch,
                'defaultSucursalId' => $defaultSucursalId,
                'priceVariants' => $priceVariants,
            ]);
        } catch (\Exception $e) {
            return back()->with([
                'error' => 'Error',
                'error_message' => 'Ha ocurrido un error al cargar la vista. Intente nuevamente.'
            ]);
        }
    }

    public function store(ProductRequest $request)
    {
        try {
            DB::beginTransaction();
            $business = Business::find(session("business"));
            $validated = $request->validated();
            $product = new BusinessProduct();
            $product->business_id = session("business");
            $product->tipoItem = $validated['tipo_producto'];
            $product->codigo = $validated['codigo'];
            $product->uniMedida = $validated['unidad_medida'];
            $product->descripcion = $validated['descripcion'];
            $product->precioUni = $validated['precio'];
            $product->special_price = $business->price_variants_enabled ? 0 : ($validated['special_price'] ?? 0); // Default to 0 if not provided
            $product->cost = $validated['cost'] ?? 0; // Default to 0 if not provided
            $product->special_price_with_iva = $business->price_variants_enabled ? 0 : ($validated['special_price_with_iva'] ?? 0); // Default to 0 if not provided
            $product->margin = $validated['margin'] ?? 0; // Default to 0 if not provided
            $product->discount = $business->price_variants_enabled ? 0 : ($validated['discount'] ?? 0); // Default to 0 if not provided
            $product->precioSinTributos = $validated['precio_sin_iva'];
            $product->tributos = json_encode($validated["tributos"]);
            if(Arr::exists($validated, "has_stock")){
                $product->stockInicial = $validated['stock_inicial'];
                $product->stockActual = $validated['stock_inicial'];
                $product->stockMinimo = $validated['stock_minimo'];
                $product->has_stock = true;
            } else {
                $product->stockInicial = 0;
                $product->stockActual = 0;
                $product->stockMinimo = 0;
                $product->has_stock = false;
            }

            if (Arr::exists($validated, 'category_id') && $validated['category_id'] != 0) {
                $product->category_id = $validated['category_id'];
            }

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('products', 'public');
                $product->image_url = "/storage/$path";
            }

            $product->save();

            // Sincronizar precios por variante (si aplica)
            if ($business->price_variants_enabled) {
                $this->syncProductPriceVariants($product, $request->input('price_variants', []));
            }
            
            // Manejar disponibilidad por sucursales
            $isGlobal = $request->input('is_global', '0') === '1';
            $product->is_global = $isGlobal;
            $product->save();
            
            if (!$isGlobal) {
                // Si no es global, sincronizar sucursales
                $sucursalesSeleccionadas = $request->input('sucursales', []);
                
                if ($product->has_stock) {
                    // Para productos con stock: crear/actualizar BranchProductStock
                    foreach ($sucursalesSeleccionadas as $sucursalId) {
                        \App\Models\BranchProductStock::firstOrCreate(
                            [
                                'business_product_id' => $product->id,
                                'sucursal_id' => $sucursalId,
                            ],
                            [
                                'stockActual' => $validated['stock_inicial'] ?? 0,
                                'stockMinimo' => $validated['stock_minimo'] ?? 0,
                                'estado_stock' => 'disponible',
                            ]
                        );
                    }
                } else {
                    // Para productos sin stock: solo crear mapeo de disponibilidad
                    foreach ($sucursalesSeleccionadas as $sucursalId) {
                        \App\Models\BranchProductStock::firstOrCreate(
                            [
                                'business_product_id' => $product->id,
                                'sucursal_id' => $sucursalId,
                            ],
                            [
                                'stockActual' => 0,
                                'stockMinimo' => 0,
                                'estado_stock' => 'disponible',
                            ]
                        );
                    }
                }
            }
            
            DB::commit();
            return redirect()->route('business.products.index')
                ->with('success', 'Producto guardado')
                ->with("success_message", "El producto ha sido guardado correctamente");
        } catch (\Exception $e) {
            Log::error('Error al guardar el producto: ' . $e->getMessage());
            DB::rollBack();
            return back()->with('error', 'Error')->with("error_message", "Ha ocurrido un error al guardar el producto")->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            $product = BusinessProduct::find($id);

            // Unlink the old image if it exists
            if ($product->image_url) {
                $oldImagePath = public_path($product->image_url);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $product->delete();
            DB::commit();
            return redirect()->route('business.products.index')
                ->with('success', 'Producto eliminado')
                ->with("success_message", "El producto ha sido eliminado correctamente");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error')->with("error_message", "Ha ocurrido un error al eliminar el producto");
        }
    }

    public function edit(string $id)
    {
        $product = BusinessProduct::find($id);
        $categories = ProductCategory::all()->map(function ($category) {
            $category->full_path = $category->getFullPath();
            return $category;
        })->pluck('full_path', 'id')->toArray();
        $categories = ["0" => "Sin categoria"] + $categories;
        if (!$product) {
            return back()->with([
                'error' => 'Error',
                'error_message' => 'El producto no existe'
            ]);
        }

        // Obtener sucursales del negocio
        $sucursales = \App\Models\Sucursal::where('business_id', session("business"))
            ->orderBy('nombre')
            ->get();
        
        // Obtener sucursales donde está disponible este producto
        $sucursalesAsignadas = $product->branchStocks()->pluck('sucursal_id')->toArray();

        // Verificar si el usuario tiene branch_selector y obtener sucursal por defecto
        $businessUser = \App\Models\BusinessUser::where('business_id', session('business'))
            ->where('user_id', auth()->id())
            ->first();
        
        $canSelectBranch = $businessUser ? (bool) $businessUser->branch_selector : false;
        $defaultSucursalId = null;
        
        if (!$canSelectBranch && $businessUser && $businessUser->default_pos_id) {
            $pos = $businessUser->defaultPos;
            if ($pos && $pos->sucursal_id) {
                $defaultSucursalId = $pos->sucursal_id;
            }
        }

        $tributes = Tributes::all();
        $priceVariants = BusinessPriceVariant::where('business_id', session('business'))
            ->orderBy('name')
            ->get();
        $productVariantPrices = $product->priceVariantOverrides()
            ->get()
            ->keyBy('price_variant_id');
        return view('business.products.edit', [
            'product' => $product,
            'unidades_medidas' => $this->unidades_medidas,
            'tributes' => $tributes,
            'categories' => $categories,
            'sucursales' => $sucursales,
            'sucursalesAsignadas' => $sucursalesAsignadas,
            'canSelectBranch' => $canSelectBranch,
            'defaultSucursalId' => $defaultSucursalId,
            'priceVariants' => $priceVariants,
            'productVariantPrices' => $productVariantPrices,
        ]);
    }

    public function update(ProductRequest $request, string $id)
    {
        try {
            DB::beginTransaction();
            $business = Business::find(session("business"));
            $validated = $request->validated();
            $product = BusinessProduct::find($id);
            $product->tipoItem = $validated['tipo_producto'];
            $product->codigo = $validated['codigo'];
            $product->uniMedida = $validated['unidad_medida'];
            $product->descripcion = $validated['descripcion'];
            $product->precioUni = $validated['precio'];
            $product->special_price = $business->price_variants_enabled ? 0 : ($validated['special_price'] ?? 0); // Default to 0 if not provided
            $product->special_price_with_iva = $business->price_variants_enabled ? 0 : ($validated['special_price_with_iva'] ?? 0); // Default to 0 if not provided
            $product->margin = $validated['margin'] ?? 0; // Default to
            $product->cost = $validated['cost'] ?? 0; // Default to 0 if not provided
            $product->precioSinTributos = $validated['precio_sin_iva'];
            $product->tributos = json_encode($validated["tributos"]);
            $product->discount = $business->price_variants_enabled ? 0 : ($validated['discount'] ?? 0); // Default to 0 if not provided

            // Nota: NO actualizamos stock_inicial en edición, solo stockMinimo
            if(Arr::exists($validated, "has_stock")){
                $product->stockMinimo = $validated['stock_minimo'];
                $product->has_stock = true;
            } else {
                $product->stockInicial = 0;
                $product->stockActual = 0;
                $product->stockMinimo = 0;
                $product->has_stock = false;
            }

            if (Arr::exists($validated, "category_id") && $validated['category_id'] != 0) {
                $product->category_id = $validated['category_id'];
            } else {
                $product->category_id = null;
            }

            if ($request->hasFile('image')) {
                // Unlink the old image if it exists
                if ($product->image_url) {
                    $oldImagePath = public_path($product->image_url);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                // Store the new image
                $path = $request->file('image')->store('products', 'public');
                $product->image_url = "/storage/$path";
            }

            $product->save();

            // Sincronizar precios por variante (si aplica)
            if ($business->price_variants_enabled) {
                $this->syncProductPriceVariants($product, $request->input('price_variants', []));
            }
            
            // Manejar disponibilidad por sucursales
            $isGlobal = $request->input('is_global', '0') === '1';
            $product->is_global = $isGlobal;
            $product->save();
            
            if (!$isGlobal) {
                // Si no es global, sincronizar sucursales
                $sucursalesSeleccionadas = $request->input('sucursales', []);
                $sucursalesExistentes = $product->branchStocks()->pluck('sucursal_id')->toArray();
                
                // Agregar nuevas sucursales
                foreach ($sucursalesSeleccionadas as $sucursalId) {
                    if (!in_array($sucursalId, $sucursalesExistentes)) {
                        \App\Models\BranchProductStock::create([
                            'business_product_id' => $product->id,
                            'sucursal_id' => $sucursalId,
                            'stockActual' => 0,
                            'stockMinimo' => $product->stockMinimo ?? 0,
                            'estado_stock' => 'agotado',
                        ]);
                    }
                }
                
                // Eliminar sucursales desmarcadas
                $sucursalesAEliminar = array_diff($sucursalesExistentes, $sucursalesSeleccionadas);
                if (!empty($sucursalesAEliminar)) {
                    \App\Models\BranchProductStock::where('business_product_id', $product->id)
                        ->whereIn('sucursal_id', $sucursalesAEliminar)
                        ->delete();
                }
            } else {
                // Si se marcó como global, eliminar todos los mapeos por sucursal
                \App\Models\BranchProductStock::where('business_product_id', $product->id)->delete();
            }
            
            DB::commit();
            return redirect()->route('business.products.index')
                ->with('success', 'Producto actualizado')
                ->with("success_message", "El producto ha sido actualizado correctamente");
        } catch (\Exception $e) {
            Log::error('Error al actualizar el producto: ' . $e->getMessage());
            DB::rollBack();
            return back()->with('error', 'Error')->with("error_message", "Ha ocurrido un error al actualizar el producto")->withInput();
        }
    }

    public function add_stock(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:business_product,id',
            'cantidad' => 'required|numeric|min:1',
            'descripcion' => 'required|string',
            'sucursal_id' => 'required|exists:sucursals,id'
        ]);

        try {
            DB::beginTransaction();
            $product = BusinessProduct::find($request->id);

            // Verificar que el producto no sea global
            if ($product->is_global) {
                DB::rollBack();
                return back()->with('error', 'Error')
                    ->with("error_message", "No se puede modificar el stock de productos globales");
            }

            // Verificar que el producto tenga control de stock
            if (!$product->has_stock) {
                DB::rollBack();
                return back()->with('error', 'Error')
                    ->with("error_message", "Este producto no tiene control de stock habilitado");
            }

            // Aumentar stock en la sucursal específica
            $product->increaseStockInBranch(
                $request->sucursal_id,
                $request->cantidad,
                'ENTRADA-' . now()->format('YmdHis'),
                $request->descripcion
            );

            DB::commit();
            return redirect()->route('business.products.index')
                ->with('success', 'Stock actualizado')
                ->with("success_message", "El stock ha sido actualizado correctamente en la sucursal");
        } catch (\Exception $e) {
            Log::error('Error al aumentar stock: ' . $e->getMessage());
            DB::rollBack();
            return back()->with('error', 'Error')
                ->with("error_message", "Ha ocurrido un error al actualizar el stock: " . $e->getMessage());
        }
    }

    public function remove_stock(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:business_product,id',
            'cantidad' => 'required|numeric|min:1',
            'descripcion' => 'required|string',
            'sucursal_id' => 'required|exists:sucursals,id'
        ]);

        try {
            DB::beginTransaction();
            $product = BusinessProduct::find($request->id);

            // Verificar que el producto no sea global
            if ($product->is_global) {
                DB::rollBack();
                return back()->with('error', 'Error')
                    ->with("error_message", "No se puede modificar el stock de productos globales");
            }

            // Verificar que el producto tenga control de stock
            if (!$product->has_stock) {
                DB::rollBack();
                return back()->with('error', 'Error')
                    ->with("error_message", "Este producto no tiene control de stock habilitado");
            }

            // Verificar que haya suficiente stock
            if (!$product->hasEnoughStockInBranch($request->sucursal_id, $request->cantidad)) {
                DB::rollBack();
                $stockDisponible = $product->getAvailableStockForBranch($request->sucursal_id);
                return back()->with('error', 'Error')
                    ->with("error_message", "Stock insuficiente. Disponible: {$stockDisponible}");
            }

            // Reducir stock en la sucursal específica
            $product->reduceStockInBranch(
                $request->sucursal_id,
                $request->cantidad,
                'SALIDA-' . now()->format('YmdHis'),
                $request->descripcion
            );

            DB::commit();
            return redirect()->route('business.products.index')
                ->with('success', 'Stock actualizado')
                ->with("success_message", "El stock ha sido actualizado correctamente en la sucursal");
        } catch (\Exception $e) {
            Log::error('Error al disminuir stock: ' . $e->getMessage());
            DB::rollBack();
            return back()->with('error', 'Error')
                ->with("error_message", "Ha ocurrido un error al actualizar el stock: " . $e->getMessage());
        }
    }

    private function syncProductPriceVariants(BusinessProduct $product, array $priceVariantsInput): void
    {
        $businessId = session('business');
        $variants = BusinessPriceVariant::where('business_id', $businessId)->get();

        foreach ($variants as $variant) {
            $input = $priceVariantsInput[$variant->id] ?? [];

            $priceWithout = $input['price_without_iva'] ?? null;
            $priceWith = $input['price_with_iva'] ?? null;

            if ($priceWithout === '') {
                $priceWithout = null;
            }
            if ($priceWith === '') {
                $priceWith = null;
            }

            if ($priceWithout === null && $priceWith === null) {
                BusinessProductPriceVariant::where('business_product_id', $product->id)
                    ->where('price_variant_id', $variant->id)
                    ->delete();
                continue;
            }

            BusinessProductPriceVariant::updateOrCreate(
                [
                    'business_product_id' => $product->id,
                    'price_variant_id' => $variant->id,
                ],
                [
                    'price_without_iva' => $priceWithout,
                    'price_with_iva' => $priceWith,
                ]
            );
        }
    }

    public function import(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv',
                'sucursal_id' => 'required|exists:sucursals,id'
            ]);

            $business_id = session("business");
            $sucursal_id = $request->input('sucursal_id');
            $unidades_medidas = $this->unidades_medidas;
            
            Excel::import(new BusinessProductImport($business_id, $unidades_medidas, $sucursal_id), $request->file('file'));

            return redirect()->route('business.products.index')
                ->with('success', 'Productos importados')
                ->with("success_message", "Los productos han sido importados correctamente a la sucursal seleccionada");
        } catch (\Exception $e) {
            Log::error('Error al importar productos: ' . $e->getMessage());
            return back()->with('error', 'Error')->with("error_message", "Ha ocurrido un error al importar los productos: " . $e->getMessage());
        }
    }

    /**
     * Obtener información de sucursales para el producto
     */
    public function getBranchInfo($id, Request $request)
    {
        try {
            $product = BusinessProduct::findOrFail($id);
            $sucursalId = $request->get('sucursal_id');

            // Obtener stock en la sucursal de origen
            $stockOrigen = $product->getAvailableStockForBranch($sucursalId);

            // Obtener todas las sucursales del business
            $sucursales = \App\Models\Sucursal::where('business_id', session('business'))
                ->orderBy('nombre')
                ->get(['id', 'nombre']);

            return response()->json([
                'success' => true,
                'stock_origen' => $stockOrigen,
                'sucursales_disponibles' => $sucursales,
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener información de sucursales: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener información del producto'
            ], 500);
        }
    }

    /**
     * Obtener stock de un producto en una sucursal específica
     */
    public function getBranchStock($id, Request $request)
    {
        try {
            $product = BusinessProduct::findOrFail($id);
            $sucursalId = $request->get('sucursal_id');

            $stock = $product->getAvailableStockForBranch($sucursalId);

            return response()->json([
                'success' => true,
                'stock' => $stock,
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener stock de sucursal: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener stock'
            ], 500);
        }
    }

    /**
     * Trasladar stock entre sucursales
     */
    public function transferStock(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:business_product,id',
            'sucursal_origen_id' => 'required|exists:sucursals,id',
            'sucursal_destino_id' => 'required|exists:sucursals,id|different:sucursal_origen_id',
            'cantidad' => 'required|numeric|min:1',
            'notas' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $product = BusinessProduct::findOrFail($request->id);

            // Verificar que el producto no sea global
            if ($product->is_global) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'No se pueden trasladar productos globales'
                ], 400);
            }

            // Verificar que el producto tenga control de stock
            if (!$product->has_stock) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Este producto no tiene control de stock habilitado'
                ], 400);
            }

            // Crear el registro de traslado
            $transfer = \App\Models\BranchTransfer::create([
                'business_product_id' => $product->id,
                'sucursal_origen_id' => $request->sucursal_origen_id,
                'sucursal_destino_id' => $request->sucursal_destino_id,
                'cantidad' => $request->cantidad,
                'user_id' => auth()->id(),
                'notas' => $request->notas,
                'estado' => 'pendiente',
                'fecha_traslado' => now(),
            ]);

            // Ejecutar el traslado (reduce en origen y aumenta en destino)
            $transfer->ejecutar();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Traslado realizado exitosamente',
                'transfer_id' => $transfer->id
            ]);
        } catch (\Exception $e) {
            Log::error('Error al trasladar stock: ' . $e->getMessage());
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al realizar el traslado: ' . $e->getMessage()
            ], 500);
        }
    }
}
