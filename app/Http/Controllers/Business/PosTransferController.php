<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\BusinessProduct;
use App\Models\PosTransfer;
use App\Models\PosTransferItem;
use App\Models\PuntoVenta;
use App\Models\Sucursal;
use App\Models\BranchProductStock;
use App\Models\PosProductStock;
use App\Models\Business;
use App\Services\OctopusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class PosTransferController extends Controller
{
    public $unidades_medidas;
    public $octopus_service;

    public function __construct()
    {
        $this->octopus_service = new OctopusService();
        $this->unidades_medidas = $this->octopus_service->getCatalog("CAT-014");
    }
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
            'items.businessProduct',
            'sucursalOrigen',
            'puntoVentaOrigen',
            'sucursalDestino',
            'puntoVentaDestino',
            'user'
        ])
            ->whereHas('businessProduct', function ($q) use ($business) {
                $q->where('business_id', $business->id);
            })
            ->orWhereHas('items.businessProduct', function ($q) use ($business) {
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

        // Obtener estadísticas para el dashboard
        $estadisticas = $this->obtenerEstadisticas($business->id);

        return view('business.inventory.pos-transfers.index', compact('traslados', 'estadisticas'));
    }

    /**
     * Obtener estadísticas de transferencias
     */
    private function obtenerEstadisticas($businessId)
    {
        $mesActual = now()->startOfMonth();

        $query = PosTransfer::where(function ($q) use ($businessId) {
            $q->whereHas('businessProduct', function ($sub) use ($businessId) {
                $sub->where('business_id', $businessId);
            })->orWhereHas('items.businessProduct', function ($sub) use ($businessId) {
                $sub->where('business_id', $businessId);
            });
        });

        return [
            // Total transferencias del mes
            'total_mes' => (clone $query)->where('created_at', '>=', $mesActual)->count(),

            // Total transferencias completadas
            'completadas' => (clone $query)->where('estado', 'completado')->count(),

            // Transferencias pendientes
            'pendientes' => (clone $query)->where('estado', 'pendiente')->count(),

            // Devoluciones del mes
            'devoluciones_mes' => (clone $query)
                ->where('es_devolucion', true)
                ->where('created_at', '>=', $mesActual)
                ->count(),

            // Liquidaciones pendientes
            'liquidaciones_pendientes' => (clone $query)
                ->where('requiere_liquidacion', true)
                ->where('liquidacion_completada', false)
                ->count(),

            // Productos más transferidos (top 5)
            'productos_top' => DB::table('pos_transfer_items')
                ->join('pos_transfers', 'pos_transfer_items.pos_transfer_id', '=', 'pos_transfers.id')
                ->join('business_product', 'pos_transfer_items.business_product_id', '=', 'business_product.id')
                ->where('business_product.business_id', $businessId)
                ->where('pos_transfers.estado', 'completado')
                ->select(
                    'business_product.codigo',
                    'business_product.descripcion',
                    DB::raw('COUNT(pos_transfer_items.id) as total_transferencias'),
                    DB::raw('SUM(pos_transfer_items.cantidad_solicitada) as cantidad_total')
                )
                ->groupBy('business_product.id', 'business_product.codigo', 'business_product.descripcion')
                ->orderBy('total_transferencias', 'desc')
                ->limit(5)
                ->get(),

            // Discrepancias en liquidaciones
            'discrepancias' => DB::table('pos_transfer_items')
                ->join('pos_transfers', 'pos_transfer_items.pos_transfer_id', '=', 'pos_transfers.id')
                ->join('business_product', 'pos_transfer_items.business_product_id', '=', 'business_product.id')
                ->where('business_product.business_id', $businessId)
                ->where('pos_transfers.liquidacion_completada', true)
                ->whereNotNull('pos_transfer_items.diferencia')
                ->where('pos_transfer_items.diferencia', '!=', 0)
                ->count(),
        ];
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
     * Mostrar formulario para crear traslado múltiple
     */
    public function createMultiple()
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

        // Obtener sucursales
        $sucursales = Sucursal::where('business_id', $business->id)->get();

        // Obtener puntos de venta con inventario independiente
        $puntosVenta = PuntoVenta::whereIn('sucursal_id', $sucursales->pluck('id'))
            ->where('has_independent_inventory', true)
            ->with('sucursal')
            ->get();

        return view('business.inventory.pos-transfers.create-multiple', compact('sucursales', 'puntosVenta'));
    }

    /**
     * Crear traslado (simple o múltiple)
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
            'notas' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.business_product_id' => 'required|exists:business_product,id',
            'items.*.cantidad' => 'required|numeric|min:0.01',
            'items.*.nota' => 'nullable|string|max:200',
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

            // Verificar que todos los productos pertenecen al negocio
            foreach ($request->items as $itemData) {
                $producto = BusinessProduct::findOrFail($itemData['business_product_id']);
                if ($producto->business_id !== $business->id) {
                    throw new \Exception('Uno o más productos no pertenecen a este negocio');
                }
            }

            // Crear el traslado
            $traslado = PosTransfer::create([
                'sucursal_origen_id' => $request->sucursal_origen_id,
                'punto_venta_origen_id' => $request->punto_venta_origen_id,
                'sucursal_destino_id' => $request->sucursal_destino_id,
                'punto_venta_destino_id' => $request->punto_venta_destino_id,
                'tipo_traslado' => $request->tipo_traslado,
                'user_id' => Auth::id(),
                'notas' => $request->notas,
                'estado' => 'pendiente',
            ]);

            // Crear los items
            foreach ($request->items as $itemData) {
                PosTransferItem::create([
                    'pos_transfer_id' => $traslado->id,
                    'business_product_id' => $itemData['business_product_id'],
                    'cantidad_solicitada' => $itemData['cantidad'],
                    'nota_item' => $itemData['nota'] ?? null,
                ]);
            }

            // Ejecutar el traslado
            $traslado->load('items.businessProduct');
            $traslado->ejecutar();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Traslado realizado exitosamente',
                'traslado_id' => $traslado->id,
                'numero_transferencia' => $traslado->numero_transferencia
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
                    ->with([
                        'branchStocks' => function ($query) use ($origenId) {
                            $query->where('sucursal_id', $origenId);
                        }
                    ])
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
                    ->with([
                        'posStocks' => function ($query) use ($origenId) {
                            $query->where('punto_venta_id', $origenId);
                        }
                    ])
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
            'items.businessProduct',
            'sucursalOrigen',
            'puntoVentaOrigen',
            'sucursalDestino',
            'puntoVentaDestino',
            'user'
        ])
            ->where(function ($query) use ($business) {
                $query->whereHas('sucursalOrigen', function ($q) use ($business) {
                    $q->where('business_id', $business->id);
                })
                    ->orWhereHas('sucursalDestino', function ($q) use ($business) {
                        $q->where('business_id', $business->id);
                    });
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
            $traslado = PosTransfer::with(['sucursalOrigen', 'sucursalDestino', 'puntoVentaOrigen', 'puntoVentaDestino'])
                ->where(function($query) use ($business) {
                    $query->whereHas('sucursalOrigen', function($q) use ($business) {
                        $q->where('business_id', $business->id);
                    })
                    ->orWhereHas('sucursalDestino', function($q) use ($business) {
                        $q->where('business_id', $business->id);
                    });
                })
                ->findOrFail($id);

            if ($traslado->estado !== 'pendiente') {
                throw new \Exception('Solo se pueden cancelar traslados pendientes');
            }

            $traslado->estado = 'cancelado';
            $traslado->requiere_liquidacion = false;
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

    /**
     * Crear devolución masiva de POS a sucursal
     */
    public function createDevolucion(Request $request, $puntoVentaId)
    {
        $business_id = Session::get('business') ?? null;
        $business = Business::find($business_id);

        if (!$business) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }

        try {
            DB::beginTransaction();

            $puntoVenta = PuntoVenta::with('sucursal')->findOrFail($puntoVentaId);

            if ($puntoVenta->sucursal->business_id !== $business->id) {
                throw new \Exception('No autorizado');
            }

            // Verificar si ya existe una devolución pendiente de liquidación
            $devolucionPendiente = PosTransfer::where('punto_venta_origen_id', $puntoVentaId)
                ->where('es_devolucion', true)
                ->where('requiere_liquidacion', true)
                ->where('liquidacion_completada', false)
                ->where('estado', 'pendiente')
                ->exists();

            if ($devolucionPendiente) {
                throw new \Exception('Ya existe una devolución pendiente de liquidación para este punto de venta. Complete la liquidación antes de crear una nueva devolución.');
            }

            // Obtener todos los productos con stock en el POS
            $stocksPOS = PosProductStock::where('punto_venta_id', $puntoVentaId)
                ->where('stockActual', '>', 0)
                ->get();

            if ($stocksPOS->isEmpty()) {
                throw new \Exception('No hay productos con stock en este punto de venta');
            }

            // Crear la transferencia de devolución
            $traslado = PosTransfer::create([
                'punto_venta_origen_id' => $puntoVentaId,
                'sucursal_destino_id' => $puntoVenta->sucursal_id,
                'tipo_traslado' => 'pos_to_branch',
                'user_id' => Auth::id(),
                'notas' => 'Devolución masiva de inventario',
                'estado' => 'pendiente',
                'es_devolucion' => true,
                'requiere_liquidacion' => true,
            ]);

            // Crear items para cada producto
            foreach ($stocksPOS as $stock) {
                PosTransferItem::create([
                    'pos_transfer_id' => $traslado->id,
                    'business_product_id' => $stock->business_product_id,
                    'cantidad_solicitada' => $stock->stockActual,
                ]);
            }

            // NO ejecutar la transferencia todavía - se hará en la liquidación con las cantidades reales
            // El traslado queda en estado 'pendiente'
            $traslado->load('items.businessProduct');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Devolución creada exitosamente. Debe completar la liquidación.',
                'traslado_id' => $traslado->id,
                'numero_transferencia' => $traslado->numero_transferencia ?? '#' . $traslado->id,
                'total_productos' => $traslado->items->count(),
                'requiere_liquidacion' => true
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al crear devolución: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Formulario de liquidación
     */
    public function liquidacionForm($id)
    {
        $business_id = Session::get('business') ?? null;
        $business = Business::find($business_id);

        if (!$business) {
            abort(403, 'No autorizado');
        }

        $traslado = PosTransfer::with([
            'items.businessProduct',
            'puntoVentaOrigen',
            'sucursalDestino'
        ])->findOrFail($id);

        if (!$traslado->requiere_liquidacion) {
            return redirect()->route('business.pos-transfers.show', $id)
                ->with('error', 'Esta transferencia no requiere liquidación');
        }

        if ($traslado->liquidacion_completada) {
            return redirect()->route('business.pos-transfers.show', $id)
                ->with('info', 'La liquidación ya fue completada');
        }

        return view('business.inventory.pos-transfers.liquidacion', compact('traslado'));
    }

    /**
     * Procesar liquidación
     */
    public function procesarLiquidacion(Request $request, $id)
    {
        $business_id = Session::get('business') ?? null;
        $business = Business::find($business_id);

        if (!$business) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }

        try {
            DB::beginTransaction();

            $traslado = PosTransfer::with('items')->findOrFail($id);

            if (!$traslado->requiere_liquidacion) {
                throw new \Exception('Esta transferencia no requiere liquidación');
            }

            if ($traslado->liquidacion_completada) {
                throw new \Exception('La liquidación ya fue completada');
            }

            $validator = Validator::make($request->all(), [
                'items' => 'required|array',
                'items.*.item_id' => 'required|exists:pos_transfer_items,id',
                'items.*.cantidad_real' => 'required|numeric|min:0',
                'observaciones' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Actualizar cantidades reales y calcular diferencias
            $tieneDiscrepancias = false;
            foreach ($request->items as $itemData) {
                $item = PosTransferItem::findOrFail($itemData['item_id']);
                $item->cantidad_real = $itemData['cantidad_real'];
                $item->calcularDiferencia();
                
                if ($item->diferencia != 0) {
                    $tieneDiscrepancias = true;
                }
            }

            // Refrescar la relación de items para obtener las cantidades reales actualizadas
            $traslado->load('items');

            // Ahora SÍ ejecutar la transferencia con las cantidades REALES
            // Para devoluciones (pos_to_branch):
            // 1. El POS devuelve TODO su stock actual (queda en 0)
            // 2. La sucursal recibe solo lo que físicamente llegó (cantidad_real)
            // 3. La diferencia es pérdida/merma/daño
            if ($traslado->tipo_traslado === 'pos_to_branch') {
                foreach ($traslado->items as $item) {
                    $cantidadReal = $item->cantidad_real ?? $item->cantidad_solicitada;
                    
                    // Descontar TODO el stock actual del POS (debe quedar en 0)
                    $stockPOS = PosProductStock::where('punto_venta_id', $traslado->punto_venta_origen_id)
                        ->where('business_product_id', $item->business_product_id)
                        ->first();
                    
                    if ($stockPOS && $stockPOS->stockActual > 0) {
                        $stockPOS->stockActual = 0; // Devuelve TODO
                        $stockPOS->updateStockEstado(); // Actualizar estado (agotado)
                    }
                    
                    // Sumar a Sucursal SOLO las cantidades realmente recibidas
                    $stockSucursal = BranchProductStock::where('sucursal_id', $traslado->sucursal_destino_id)
                        ->where('business_product_id', $item->business_product_id)
                        ->first();
                    
                    if ($stockSucursal) {
                        $stockSucursal->stockActual += $cantidadReal;
                        $stockSucursal->updateStockEstado(); // Recalcular estado según nuevo stock
                    }
                }
            }

            // Marcar liquidación como completada y traslado como completado
            $traslado->liquidacion_completada = true;
            $traslado->observaciones_liquidacion = $request->observaciones;
            $traslado->estado = 'completado';
            $traslado->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Liquidación procesada exitosamente. El inventario ha sido ajustado según las cantidades reales recibidas.',
                'tiene_discrepancias' => $tieneDiscrepancias
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Generar reporte PDF de transferencia
     */
    public function generarReportePDF($id)
    {
        $business_id = Session::get('business') ?? null;
        $business = Business::find($business_id);
        $business_data = app(OctopusService::class)->getDatosEmpresa($business->nit);

        if (!$business) {
            abort(403, 'No autorizado');
        }

        $traslado = PosTransfer::with([
            'items.businessProduct',
            'sucursalOrigen',
            'puntoVentaOrigen',
            'sucursalDestino',
            'puntoVentaDestino',
            'user'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('business.inventory.pos-transfers.pdf.reporte', [
            'traslado' => $traslado,
            'business' => $business,
            'business_data' => $business_data,
            'unidades_medidas' => $this->unidades_medidas,
        ]);

        return $pdf->stream("transferencia_{$traslado->numero_transferencia}.pdf");
    }

    /**
     * Generar reporte PDF de devolución
     */
    public function generarReporteDevolucionPDF($id)
    {
        $business_id = Session::get('business') ?? null;
        $business = Business::find($business_id);
        $business_data = app(OctopusService::class)->getDatosEmpresa($business->nit);

        if (!$business) {
            abort(403, 'No autorizado');
        }

        $traslado = PosTransfer::with([
            'items.businessProduct',
            'puntoVentaOrigen',
            'sucursalDestino',
            'user'
        ])->findOrFail($id);

        if (!$traslado->es_devolucion) {
            abort(400, 'Esta transferencia no es una devolución');
        }

        $pdf = Pdf::loadView('business.inventory.pos-transfers.pdf.devolucion', [
            'traslado' => $traslado,
            'business' => $business,
            'business_data' => $business_data,
            'unidades_medidas' => $this->unidades_medidas,
        ]);

        return $pdf->stream("devolucion_{$traslado->numero_transferencia}.pdf");
    }

    /**
     * Generar reporte PDF de liquidación
     */
    public function generarReporteLiquidacionPDF($id)
    {
        $business_id = Session::get('business') ?? null;
        $business = Business::find($business_id);
        $business_data = app(OctopusService::class)->getDatosEmpresa($business->nit);

        if (!$business) {
            abort(403, 'No autorizado');
        }

        $traslado = PosTransfer::with([
            'items.businessProduct',
            'puntoVentaOrigen',
            'sucursalDestino',
            'user'
        ])->findOrFail($id);

        if (!$traslado->requiere_liquidacion) {
            abort(400, 'Esta transferencia no requiere liquidación');
        }

        if (!$traslado->liquidacion_completada) {
            abort(400, 'La liquidación no ha sido completada');
        }

        $pdf = Pdf::loadView('business.inventory.pos-transfers.pdf.liquidacion', [
            'traslado' => $traslado,
            'business' => $business,
            'business_data' => $business_data,
            'unidades_medidas' => $this->unidades_medidas,
        ]);

        return $pdf->stream("liquidacion_{$traslado->numero_transferencia}.pdf");
    }
}
