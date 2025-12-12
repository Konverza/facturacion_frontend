<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\BusinessProduct;
use App\Models\PosTransfer;
use App\Models\PuntoVenta;
use App\Models\Sucursal;
use App\Models\BranchProductStock;
use App\Models\PosProductStock;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PosTransferController extends Controller
{
    /**
     * Listar traslados
     */
    public function index(Request $request)
    {
        $business_id = Session::get('business') ?? null;
        $business = Business::find($business_id);

        if (!$business) {
            return redirect()->route('business.select')
                ->with('error', 'Por favor seleccione un negocio.');
        }

        if (!$business->pos_inventory_enabled) {
            return redirect()->route('business.dashboard')
                ->with('error', 'El inventario por punto de venta no está habilitado.');
        }

        $query = PosTransfer::with([
            'businessProduct',
            'sucursalOrigen',
            'puntoVentaOrigen',
            'sucursalDestino',
            'puntoVentaDestino',
            'user'
        ])
        ->whereHas('businessProduct', function ($q) use ($business) {
            $q->where('business_id', $business->id);
        })
        ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('tipo_traslado')) {
            $query->where('tipo_traslado', $request->tipo_traslado);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_traslado', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_traslado', '<=', $request->fecha_hasta);
        }

        $traslados = $query->paginate(20);

        return view('business.inventory.pos-transfers.index', compact('traslados'));
    }

    /**
     * Formulario para crear traslado
     */
    public function create(Request $request)
    {
        $business_id = Session::get('business') ?? null;
        $business = Business::find($business_id);

        if (!$business) {
            return redirect()->route('business.select')
                ->with('error', 'Por favor seleccione un negocio.');
        }

        $tipo = $request->input('tipo', 'branch_to_pos'); // Por defecto: sucursal a POS

        // Obtener sucursales
        $sucursales = Sucursal::where('business_id', $business->id)->get();

        // Obtener puntos de venta con inventario independiente
        $puntosVenta = PuntoVenta::whereIn('sucursal_id', $sucursales->pluck('id'))
            ->where('has_independent_inventory', true)
            ->with('sucursal')
            ->get();

        return view('business.inventory.pos-transfers.create', compact('tipo', 'sucursales', 'puntosVenta'));
    }

    /**
     * Crear traslado
     */
    public function store(Request $request)
    {
        $business_id = Session::get('business') ?? null;
        $business = Business::find($business_id);

        if (!$business) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'tipo_traslado' => 'required|in:branch_to_pos,pos_to_branch,pos_to_pos',
            'business_product_id' => 'required|exists:business_product,id',
            'cantidad' => 'required|numeric|min:0.01',
            'notas' => 'nullable|string|max:500',
        ]);

        // Validación condicional según tipo de traslado
        if ($request->tipo_traslado === 'branch_to_pos') {
            $validator->addRules([
                'sucursal_origen_id' => 'required|exists:sucursals,id',
                'punto_venta_destino_id' => 'required|exists:punto_ventas,id',
            ]);
        } elseif ($request->tipo_traslado === 'pos_to_branch') {
            $validator->addRules([
                'punto_venta_origen_id' => 'required|exists:punto_ventas,id',
                'sucursal_destino_id' => 'required|exists:sucursals,id',
            ]);
        } elseif ($request->tipo_traslado === 'pos_to_pos') {
            $validator->addRules([
                'punto_venta_origen_id' => 'required|exists:punto_ventas,id',
                'punto_venta_destino_id' => 'required|exists:punto_ventas,id|different:punto_venta_origen_id',
            ]);
        }

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Verificar que el producto pertenece al negocio
            $producto = BusinessProduct::findOrFail($request->business_product_id);
            if ($producto->business_id !== $business->id) {
                throw new \Exception('Producto no pertenece a este negocio');
            }

            // Crear el traslado
            $traslado = PosTransfer::create([
                'business_product_id' => $request->business_product_id,
                'sucursal_origen_id' => $request->sucursal_origen_id,
                'punto_venta_origen_id' => $request->punto_venta_origen_id,
                'sucursal_destino_id' => $request->sucursal_destino_id,
                'punto_venta_destino_id' => $request->punto_venta_destino_id,
                'tipo_traslado' => $request->tipo_traslado,
                'cantidad' => $request->cantidad,
                'user_id' => Auth::id(),
                'notas' => $request->notas,
                'estado' => 'pendiente',
            ]);

            // Ejecutar el traslado
            $traslado->ejecutar();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Traslado realizado exitosamente',
                'traslado_id' => $traslado->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al realizar el traslado: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Obtener productos disponibles para traslado (AJAX)
     */
    public function getAvailableProducts(Request $request)
    {
        $business_id = Session::get('business') ?? null;
        $business = Business::find($business_id);

        if (!$business) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        $tipo = $request->input('tipo_traslado');
        $origenId = $request->input('origen_id');

        if (!$tipo || !$origenId) {
            return response()->json(['error' => 'Parámetros faltantes'], 400);
        }

        try {
            if ($tipo === 'branch_to_pos') {
                // Productos disponibles en la sucursal origen
                $productos = BusinessProduct::where('business_id', $business->id)
                    ->where('has_stock', true)
                    ->where('is_global', false)
                    ->whereHas('branchStocks', function ($query) use ($origenId) {
                        $query->where('sucursal_id', $origenId)
                            ->whereIn('estado_stock', ['disponible', 'por_agotarse'])
                            ->where('stockActual', '>', 0);
                    })
                    ->with(['branchStocks' => function ($query) use ($origenId) {
                        $query->where('sucursal_id', $origenId);
                    }])
                    ->get()
                    ->map(function ($producto) {
                        $stock = $producto->branchStocks->first();
                        return [
                            'id' => $producto->id,
                            'codigo' => $producto->codigo,
                            'descripcion' => $producto->descripcion,
                            'stock_disponible' => $stock ? $stock->stockActual : 0
                        ];
                    });

            } elseif ($tipo === 'pos_to_branch' || $tipo === 'pos_to_pos') {
                // Productos disponibles en el punto de venta origen
                $productos = BusinessProduct::where('business_id', $business->id)
                    ->where('has_stock', true)
                    ->where('is_global', false)
                    ->whereHas('posStocks', function ($query) use ($origenId) {
                        $query->where('punto_venta_id', $origenId)
                            ->whereIn('estado_stock', ['disponible', 'por_agotarse'])
                            ->where('stockActual', '>', 0);
                    })
                    ->with(['posStocks' => function ($query) use ($origenId) {
                        $query->where('punto_venta_id', $origenId);
                    }])
                    ->get()
                    ->map(function ($producto) {
                        $stock = $producto->posStocks->first();
                        return [
                            'id' => $producto->id,
                            'codigo' => $producto->codigo,
                            'descripcion' => $producto->descripcion,
                            'stock_disponible' => $stock ? $stock->stockActual : 0
                        ];
                    });
            } else {
                return response()->json(['error' => 'Tipo de traslado no válido'], 400);
            }

            return response()->json([
                'success' => true,
                'productos' => $productos
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Obtener stock disponible de un producto (AJAX)
     */
    public function getProductStock(Request $request)
    {
        $business_id = Session::get('business') ?? null;
        $business = Business::find($business_id);

        if (!$business) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        $productoId = $request->input('producto_id');
        $tipo = $request->input('tipo');
        $origenId = $request->input('origen_id');

        try {
            $producto = BusinessProduct::findOrFail($productoId);

            if ($producto->business_id !== $business->id) {
                throw new \Exception('Producto no pertenece a este negocio');
            }

            $stockDisponible = 0;

            if ($tipo === 'branch') {
                $stock = BranchProductStock::where('business_product_id', $productoId)
                    ->where('sucursal_id', $origenId)
                    ->first();
                $stockDisponible = $stock ? $stock->stockActual : 0;
            } elseif ($tipo === 'pos') {
                $stock = PosProductStock::where('business_product_id', $productoId)
                    ->where('punto_venta_id', $origenId)
                    ->first();
                $stockDisponible = $stock ? $stock->stockActual : 0;
            }

            return response()->json([
                'success' => true,
                'stock_disponible' => $stockDisponible,
                'producto' => [
                    'codigo' => $producto->codigo,
                    'descripcion' => $producto->descripcion,
                    'uniMedida' => $producto->uniMedida
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Ver detalles de un traslado
     */
    public function show($id)
    {
        $business_id = Session::get('business') ?? null;
        $business = Business::find($business_id);

        if (!$business) {
            abort(403, 'No autorizado');
        }

        $traslado = PosTransfer::with([
            'businessProduct',
            'sucursalOrigen',
            'puntoVentaOrigen',
            'sucursalDestino',
            'puntoVentaDestino',
            'user'
        ])
        ->whereHas('businessProduct', function ($q) use ($business) {
            $q->where('business_id', $business->id);
        })
        ->findOrFail($id);

        return view('business.inventory.pos-transfers.show', compact('traslado'));
    }

    /**
     * Cancelar un traslado pendiente
     */
    public function cancel($id)
    {
        $business_id = Session::get('business') ?? null;
        $business = Business::find($business_id);

        if (!$business) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }

        try {
            $traslado = PosTransfer::whereHas('businessProduct', function ($q) use ($business) {
                $q->where('business_id', $business->id);
            })->findOrFail($id);

            if ($traslado->estado !== 'pendiente') {
                throw new \Exception('Solo se pueden cancelar traslados pendientes');
            }

            $traslado->estado = 'cancelado';
            $traslado->save();

            return response()->json([
                'success' => true,
                'message' => 'Traslado cancelado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
