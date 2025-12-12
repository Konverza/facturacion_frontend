<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\BusinessProduct;
use App\Models\PosProductStock;
use App\Models\PuntoVenta;
use App\Models\Sucursal;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PosInventoryController extends Controller
{
    /**
     * Mostrar dashboard de inventario por punto de venta
     */
    public function index()
    {
        $business_id = Session::get('business') ?? null;
        $business = Business::find($business_id);

        if (!$business) {
            return redirect()->route('business.select')
                ->with('error', 'Por favor seleccione un negocio.');
        }

        // Verificar si el negocio tiene habilitado el inventario por POS
        if (!$business->pos_inventory_enabled) {
            return redirect()->route('business.dashboard')
                ->with('error', 'El inventario por punto de venta no está habilitado para este negocio.');
        }

        // Obtener sucursales del negocio
        $sucursales = Sucursal::where('business_id', $business->id)->get();

        // Obtener puntos de venta con inventario independiente
        $puntosVenta = PuntoVenta::whereIn('sucursal_id', $sucursales->pluck('id'))
            ->where('has_independent_inventory', true)
            ->with('sucursal')
            ->get();

        return view('business.inventory.pos-inventory.index', compact('sucursales', 'puntosVenta'));
    }

    /**
     * Ver stock de un punto de venta específico
     */
    public function show($puntoVentaId)
    {
        $business_id = Session::get('business') ?? null;
        $business = Business::find($business_id);

        if (!$business) {
            abort(403, 'No autorizado');
        }
        
        $puntoVenta = PuntoVenta::with('sucursal')->findOrFail($puntoVentaId);

        // Verificar que el punto de venta pertenece al negocio
        if ($puntoVenta->sucursal->business_id !== $business->id) {
            abort(403, 'No autorizado');
        }

        // Obtener productos con stock en este punto de venta
        $stocks = PosProductStock::where('punto_venta_id', $puntoVentaId)
            ->with('businessProduct.category')
            ->get();

        return view('business.inventory.pos-inventory.show', compact('puntoVenta', 'stocks'));
    }

    /**
     * Obtener stock en tiempo real (AJAX)
     */
    public function getStock(Request $request)
    {
        $business_id = Session::get('business') ?? null;
        $business = Business::find($business_id);

        if (!$business) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        $type = $request->input('type'); // 'branch' o 'pos'
        $id = $request->input('id');

        if ($type === 'branch') {
            $sucursal = Sucursal::findOrFail($id);
            
            if ($sucursal->business_id !== $business->id) {
                return response()->json(['error' => 'No autorizado'], 403);
            }

            $stocks = DB::table('business_product_stock')
                ->join('business_product', 'business_product_stock.business_product_id', '=', 'business_product.id')
                ->where('business_product_stock.sucursal_id', $id)
                ->where('business_product.business_id', $business->id)
                ->select(
                    'business_product.id',
                    'business_product.codigo',
                    'business_product.descripcion',
                    'business_product_stock.stockActual',
                    'business_product_stock.stockMinimo',
                    'business_product_stock.estado_stock'
                )
                ->orderBy('business_product.descripcion')
                ->get();

            $html = view('layouts.partials.ajax.business.inventory.stock-table', [
                'stocks' => $stocks,
                'type' => 'branch'
            ])->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'total_productos' => $stocks->count(),
                'total_stock' => $stocks->sum('stockActual')
            ]);
        }

        if ($type === 'pos') {
            $puntoVenta = PuntoVenta::with('sucursal')->findOrFail($id);
            
            if ($puntoVenta->sucursal->business_id !== $business->id) {
                return response()->json(['error' => 'No autorizado'], 403);
            }

            $stocks = DB::table('pos_product_stock')
                ->join('business_product', 'pos_product_stock.business_product_id', '=', 'business_product.id')
                ->where('pos_product_stock.punto_venta_id', $id)
                ->where('business_product.business_id', $business->id)
                ->select(
                    'business_product.id',
                    'business_product.codigo',
                    'business_product.descripcion',
                    'pos_product_stock.stockActual',
                    'pos_product_stock.stockMinimo',
                    'pos_product_stock.estado_stock'
                )
                ->orderBy('business_product.descripcion')
                ->get();

            $html = view('layouts.partials.ajax.business.inventory.stock-table', [
                'stocks' => $stocks,
                'type' => 'pos'
            ])->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'total_productos' => $stocks->count(),
                'total_stock' => $stocks->sum('stockActual')
            ]);
        }

        return response()->json(['error' => 'Tipo no válido'], 400);
    }

    /**
     * Formulario para asignar productos a punto de venta
     */
    public function assignForm($puntoVentaId)
    {
        $business_id = Session::get('business') ?? null;
        $business = Business::find($business_id);

        if (!$business) {
            abort(403, 'No autorizado');
        }
        
        $puntoVenta = PuntoVenta::with('sucursal')->findOrFail($puntoVentaId);

        // Verificar que el punto de venta pertenece al negocio
        if ($puntoVenta->sucursal->business_id !== $business->id) {
            abort(403, 'No autorizado');
        }

        // Obtener productos disponibles en la sucursal
        $productosDisponibles = BusinessProduct::where('business_id', $business->id)
            ->where('has_stock', true)
            ->where('is_global', false)
            ->whereHas('branchStocks', function ($query) use ($puntoVenta) {
                $query->where('sucursal_id', $puntoVenta->sucursal_id)
                    ->whereIn('estado_stock', ['disponible', 'por_agotarse'])
                    ->where('stockActual', '>', 0);
            })
            ->with(['branchStocks' => function ($query) use ($puntoVenta) {
                $query->where('sucursal_id', $puntoVenta->sucursal_id);
            }])
            ->get()
            ->map(function ($producto) use ($puntoVenta) {
                $branchStock = $producto->branchStocks->first();
                $producto->stock_sucursal = $branchStock ? $branchStock->stockActual : 0;
                return $producto;
            });

        // Obtener stocks actuales en el punto de venta
        $stocksActuales = PosProductStock::where('punto_venta_id', $puntoVentaId)
            ->get();

        return view('business.inventory.pos-inventory.assign-form', compact('puntoVenta', 'productosDisponibles', 'stocksActuales'));
    }

    /**
     * Habilitar/deshabilitar inventario independiente en un punto de venta
     */
    public function toggleInventory(Request $request, $puntoVentaId)
    {
        $business_id = Session::get('business') ?? null;
        $business = Business::find($business_id);

        if (!$business) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }

        if (!$business->pos_inventory_enabled) {
            return response()->json([
                'success' => false,
                'message' => 'El inventario por punto de venta no está habilitado para este negocio.'
            ], 400);
        }

        $puntoVenta = PuntoVenta::with('sucursal')->findOrFail($puntoVentaId);

        if ($puntoVenta->sucursal->business_id !== $business->id) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }

        $puntoVenta->has_independent_inventory = !$puntoVenta->has_independent_inventory;
        $puntoVenta->save();

        return response()->json([
            'success' => true,
            'message' => $puntoVenta->has_independent_inventory 
                ? 'Inventario independiente habilitado' 
                : 'Inventario independiente deshabilitado',
            'has_independent_inventory' => $puntoVenta->has_independent_inventory
        ]);
    }

    /**
     * Obtener comparativa de stock (sucursal vs puntos de venta)
     */
    public function compareStock($sucursalId)
    {
        $business_id = Session::get('business') ?? null;
        $business = Business::find($business_id);

        if (!$business) {
            abort(403, 'No autorizado');
        }
        
        $sucursal = Sucursal::findOrFail($sucursalId);

        if ($sucursal->business_id !== $business->id) {
            abort(403, 'No autorizado');
        }

        // Stock en sucursal
        $stockSucursal = DB::table('business_product_stock')
            ->join('business_product', 'business_product_stock.business_product_id', '=', 'business_product.id')
            ->where('business_product_stock.sucursal_id', $sucursalId)
            ->where('business_product.business_id', $business->id)
            ->select(
                'business_product.id',
                'business_product.codigo',
                'business_product.descripcion',
                'business_product_stock.stockActual as stock_sucursal'
            )
            ->get()
            ->keyBy('id');

        // Stock en puntos de venta de esta sucursal
        $puntosVenta = PuntoVenta::where('sucursal_id', $sucursalId)
            ->where('has_independent_inventory', true)
            ->get();

        $stockPOS = [];
        foreach ($puntosVenta as $pos) {
            $stocks = DB::table('pos_product_stock')
                ->where('punto_venta_id', $pos->id)
                ->select('business_product_id', 'stockActual')
                ->get();
            
            foreach ($stocks as $stock) {
                if (!isset($stockPOS[$stock->business_product_id])) {
                    $stockPOS[$stock->business_product_id] = [];
                }
                $stockPOS[$stock->business_product_id][$pos->id] = $stock->stockActual;
            }
        }

        return view('business.inventory.pos-inventory.compare', compact('sucursal', 'stockSucursal', 'stockPOS', 'puntosVenta'));
    }
}
