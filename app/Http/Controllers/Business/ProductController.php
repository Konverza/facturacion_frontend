<?php

namespace App\Http\Controllers\Business;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\BusinessProduct;
use App\Models\BusinessProductMovement;
use App\Models\Tributes;
use App\Services\OctopusService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

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
            $business_products = BusinessProduct::where("business_id", session("business"))->orderBy("id", "desc")->get();
            $tributes = Tributes::all();
            $business_products->each(function ($product) use ($tributes) {
                $tributos_producto = json_decode($product->tributos);
                $product->texto_tributos = collect($tributos_producto)->map(function ($tributo) use ($tributes) {
                    $tributo_encontrado = $tributes->where('codigo', $tributo)->first();
                    return $tributo_encontrado ? $tributo_encontrado->descripcion : null;
                })->filter()->values();
            });

            return view(
                'business.products.index',
                ['business_products' => $business_products]
            );
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
            return view('business.products.create', [
                'unidades_medidas' => $this->unidades_medidas,
                'tributes' => $tributes
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
            $product->precioSinTributos = $validated['precio_sin_iva'];
            $product->tributos = json_encode($validated["tributos"]);
            $product->stockInicial = $validated['stock_inicial'];
            $product->stockActual = $validated['stock_inicial'];
            $product->stockMinimo = $validated['stock_minimo'];
            $product->save();
            DB::commit();
            return redirect()->route('business.products.index')
                ->with('success', 'Producto guardado')
                ->with("success_message", "El producto ha sido guardado correctamente");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error')->with("error_message", "Ha ocurrido un error al guardar el producto");
        }
    }

    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            $product = BusinessProduct::find($id);
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
            'tributes' => $tributes
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
            $product->precioSinTributos = $validated['precio_sin_iva'];
            $product->tributos = json_encode($validated["tributos"]);
            $product->stockInicial = $validated['stock_inicial'];
            $product->stockMinimo = $validated['stock_minimo'];

            if ($product->stockActual == 0) {
                $product->stockActual = $validated['stock_inicial'];
            }

            $product->save();
            DB::commit();
            return redirect()->route('business.products.index')
                ->with('success', 'Producto actualizado')
                ->with("success_message", "El producto ha sido actualizado correctamente");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error')->with("error_message", "Ha ocurrido un error al actualizar el producto");
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
}
