<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Imports\BusinessProductImport;
use App\Models\Business;
use App\Models\BusinessProduct;
use App\Models\BusinessProductMovement;
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
            $tributes = Tributes::all();
            $categories = ProductCategory::all()->map(function ($category) {
                $category->full_path = $category->getFullPath();
                return $category;
            })->pluck('full_path', 'id')->toArray();
            $categories = ["0" => "Sin categoria"] + $categories;
            return view('business.products.create', [
                'unidades_medidas' => $this->unidades_medidas,
                'tributes' => $tributes,
                'categories' => $categories
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
            $validated = $request->validated();
            $product = new BusinessProduct();
            $product->business_id = session("business");
            $product->tipoItem = $validated['tipo_producto'];
            $product->codigo = $validated['codigo'];
            $product->uniMedida = $validated['unidad_medida'];
            $product->descripcion = $validated['descripcion'];
            $product->precioUni = $validated['precio'];
            $product->special_price = $validated['special_price'] ?? 0; // Default to 0 if not provided
            $product->cost = $validated['cost'] ?? 0; // Default to 0 if not provided
            $product->special_price_with_iva = $validated['special_price_with_iva'] ?? 0; // Default to 0 if not provided
            $product->margin = $validated['margin'] ?? 0; // Default to 0 if not provided
            $product->discount = $validated['discount'] ?? 0; // Default to 0 if not provided
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

        $tributes = Tributes::all();
        return view('business.products.edit', [
            'product' => $product,
            'unidades_medidas' => $this->unidades_medidas,
            'tributes' => $tributes,
            'categories' => $categories
        ]);
    }

    public function update(ProductRequest $request, string $id)
    {
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            $product = BusinessProduct::find($id);
            $product->tipoItem = $validated['tipo_producto'];
            $product->codigo = $validated['codigo'];
            $product->uniMedida = $validated['unidad_medida'];
            $product->descripcion = $validated['descripcion'];
            $product->precioUni = $validated['precio'];
            $product->special_price = $validated['special_price'] ?? 0; // Default to 0 if not provided
            $product->special_price_with_iva = $validated['special_price_with_iva'] ?? 0; // Default to 0 if not provided
            $product->margin = $validated['margin'] ?? 0; // Default to
            $product->cost = $validated['cost'] ?? 0; // Default to 0 if not provided
            $product->precioSinTributos = $validated['precio_sin_iva'];
            $product->tributos = json_encode($validated["tributos"]);
            $product->discount = $validated['discount'] ?? 0; // Default to 0 if not provided


            if(Arr::exists($validated, "has_stock")){
                $product->stockInicial = $validated['stock_inicial'];
                if($product->stockActual == 0){
                    $product->stockActual = $validated['stock_inicial'];
                }
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
            'descripcion' => 'required|string'
        ]);

        try {
            DB::beginTransaction();
            $product = BusinessProduct::find($request->id);
            $product->stockActual += $request->cantidad;
            BusinessProductMovement::create([
                'business_product_id' => $product->id,
                'numero_factura' => null,
                'tipo' => "entrada",
                'cantidad' => $request->cantidad,
                'precio_unitario' => $product->precioUni,
                'producto' => $product->descripcion,
                'descripcion' => $request->descripcion
            ]);

            if ($product->stockActual <= $product->stockMinimo) {
                $product->estado_stock = "agotado";
            } elseif (($product->stockActual - $product->stockMinimo) <= 2) {
                $product->estado_stock = "por_agotarse";
            } else {
                $product->estado_stock = "disponible";
            }

            $product->save();
            DB::commit();
            return redirect()->route('business.products.index')
                ->with('success', 'Stock actualizado')
                ->with("success_message", "El stock ha sido actualizado correctamente");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error')->with("error_message", "Ha ocurrido un error al actualizar el stock");
        }
    }

    public function remove_stock(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:business_product,id',
            'cantidad' => 'required|numeric|min:1',
            'descripcion' => 'required|string'
        ]);

        try {
            DB::beginTransaction();
            $product = BusinessProduct::find($request->id);
            $product->stockActual -= $request->cantidad;
            BusinessProductMovement::create([
                'business_product_id' => $product->id,
                'numero_factura' => "Salida de Stock",
                'tipo' => "salida",
                'cantidad' => $request->cantidad,
                'precio_unitario' => $product->precioUni,
                'producto' => $product->descripcion,
                'descripcion' => $request->descripcion
            ]);

            if ($product->stockActual <= $product->stockMinimo) {
                $product->estado_stock = "agotado";
            } elseif (($product->stockActual - $product->stockMinimo) <= 2) {
                $product->estado_stock = "por_agotarse";
            } else {
                $product->estado_stock = "disponible";
            }

            $product->save();
            DB::commit();
            return redirect()->route('business.products.index')
                ->with('success', 'Stock actualizado')
                ->with("success_message", "El stock ha sido actualizado correctamente");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error')->with("error_message", "Ha ocurrido un error al actualizar el stock");
        }
    }

    public function import(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv'
            ]);

            $business_id = session("business");
            $unidades_medidas = $this->unidades_medidas;
            Excel::import(new BusinessProductImport($business_id, $unidades_medidas), $request->file('file'));

            return redirect()->route('business.products.index')
                ->with('success', 'Productos importados')
                ->with("success_message", "Los productos han sido importados correctamente");
        } catch (\Exception $e) {
            Log::error('Error al importar productos: ' . $e->getMessage());
            return back()->with('error', 'Error')->with("error_message", "Ha ocurrido un error al importar los productos");
        }
    }
}
