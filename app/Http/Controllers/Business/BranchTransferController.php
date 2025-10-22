<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\BranchTransfer;
use App\Models\BusinessProduct;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BranchTransferController extends Controller
{
    /**
     * Mostrar listado de traslados
     */
    public function index(Request $request)
    {
        $businessId = session('business');
        
        $query = BranchTransfer::whereHas('businessProduct', function ($q) use ($businessId) {
            $q->where('business_id', $businessId);
        })->with(['businessProduct', 'sucursalOrigen', 'sucursalDestino', 'user']);

        // Filtros
        if ($request->filled('sucursal_origen')) {
            $query->where('sucursal_origen_id', $request->sucursal_origen);
        }
        if ($request->filled('sucursal_destino')) {
            $query->where('sucursal_destino_id', $request->sucursal_destino);
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

        $traslados = $query->orderBy('fecha_traslado', 'desc')
            ->paginate(20);

        $sucursales = Sucursal::where('business_id', $businessId)
            ->orderBy('nombre')
            ->get();

        return view('business.traslados.index', compact('traslados', 'sucursales'));
    }

    /**
     * Mostrar formulario de nuevo traslado
     */
    public function create()
    {
        $businessId = session('business');
        
        $sucursales = Sucursal::where('business_id', $businessId)
            ->orderBy('nombre')
            ->get();

        // Productos con control de stock (no globales)
        $productos = BusinessProduct::where('business_id', $businessId)
            ->where('has_stock', true)
            ->where('is_global', false)
            ->orderBy('descripcion')
            ->get();

        return view('business.traslados.create', compact('sucursales', 'productos'));
    }

    /**
     * Crear un nuevo traslado
     */
    public function store(Request $request)
    {
        $request->validate([
            'business_product_id' => 'required|exists:business_product,id',
            'sucursal_origen_id' => 'required|exists:sucursals,id|different:sucursal_destino_id',
            'sucursal_destino_id' => 'required|exists:sucursals,id|different:sucursal_origen_id',
            'cantidad' => 'required|numeric|min:0.01',
            'notas' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $producto = BusinessProduct::findOrFail($request->business_product_id);

            // Validar que el producto pertenece al negocio actual
            if ($producto->business_id != session('business')) {
                throw new \Exception('El producto no pertenece a este negocio.');
            }

            // Validar que las sucursales pertenecen al negocio
            $sucursalOrigen = Sucursal::where('id', $request->sucursal_origen_id)
                ->where('business_id', session('business'))
                ->firstOrFail();
            
            $sucursalDestino = Sucursal::where('id', $request->sucursal_destino_id)
                ->where('business_id', session('business'))
                ->firstOrFail();

            // Crear el traslado
            $traslado = BranchTransfer::create([
                'business_product_id' => $request->business_product_id,
                'sucursal_origen_id' => $request->sucursal_origen_id,
                'sucursal_destino_id' => $request->sucursal_destino_id,
                'cantidad' => $request->cantidad,
                'user_id' => auth()->id(),
                'notas' => $request->notas,
                'estado' => 'pendiente',
            ]);

            // Ejecutar el traslado
            $traslado->ejecutar();

            DB::commit();

            return redirect()
                ->route('business.traslados.index')
                ->with('success', 'Traslado realizado exitosamente')
                ->with('success_message', "Se trasladaron {$request->cantidad} unidades de {$producto->descripcion} desde {$sucursalOrigen->nombre} hacia {$sucursalDestino->nombre}.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en traslado de productos: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error')
                ->with('error_message', $e->getMessage());
        }
    }

    /**
     * Ver detalle de un traslado
     */
    public function show($id)
    {
        $traslado = BranchTransfer::with([
            'businessProduct',
            'sucursalOrigen',
            'sucursalDestino',
            'user'
        ])->findOrFail($id);

        // Verificar que pertenece al negocio actual
        if ($traslado->businessProduct->business_id != session('business')) {
            abort(403, 'No autorizado');
        }

        return view('business.traslados.show', compact('traslado'));
    }

    /**
     * Cancelar un traslado pendiente
     */
    public function cancel($id)
    {
        try {
            DB::beginTransaction();

            $traslado = BranchTransfer::findOrFail($id);

            // Verificar que pertenece al negocio actual
            if ($traslado->businessProduct->business_id != session('business')) {
                abort(403, 'No autorizado');
            }

            if ($traslado->estado !== 'pendiente') {
                throw new \Exception('Solo se pueden cancelar traslados pendientes.');
            }

            $traslado->estado = 'cancelado';
            $traslado->save();

            DB::commit();

            return redirect()
                ->route('business.traslados.index')
                ->with('success', 'Traslado cancelado')
                ->with('success_message', 'El traslado ha sido cancelado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al cancelar traslado: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->with('error', 'Error')
                ->with('error_message', $e->getMessage());
        }
    }

    /**
     * API: Obtener stock disponible de un producto en una sucursal
     */
    public function getProductStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:business_product,id',
            'sucursal_id' => 'required|exists:sucursals,id',
        ]);

        $producto = BusinessProduct::findOrFail($request->product_id);
        
        // Verificar que pertenece al negocio actual
        if ($producto->business_id != session('business')) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $stockDisponible = $producto->getAvailableStockForBranch($request->sucursal_id);

        return response()->json([
            'success' => true,
            'stock_disponible' => $stockDisponible,
            'producto' => [
                'id' => $producto->id,
                'codigo' => $producto->codigo,
                'descripcion' => $producto->descripcion,
                'is_global' => $producto->is_global,
                'has_stock' => $producto->has_stock,
            ],
        ]);
    }
}
