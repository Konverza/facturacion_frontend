<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\BusinessProduct;
use App\Models\BusinessProductMovement;
use App\Models\Sucursal;
use App\Models\PuntoVenta;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class KardexController extends Controller
{
    /**
     * Mostrar formulario de filtros para el reporte Kardex
     */
    public function index()
    {
        $business_id = Session::get('business');
        $business = Business::find($business_id);

        if (!$business) {
            return redirect()->route('business.dashboard')->with([
                'error' => 'Error',
                'error_message' => 'Business no encontrado'
            ]);
        }

        $sucursales = Sucursal::where('business_id', $business_id)->get();
        $puntosVenta = PuntoVenta::whereHas('sucursal', function($q) use ($business_id) {
            $q->where('business_id', $business_id);
        })->with('sucursal')->get();
        
        $productos = BusinessProduct::where('business_id', $business_id)
            ->orderBy('descripcion')
            ->get();

        return view('business.reports.kardex.index', compact(
            'business',
            'sucursales',
            'puntosVenta',
            'productos'
        ));
    }

    /**
     * Generar reporte Kardex en PDF
     */
    public function generatePDF(Request $request)
    {
        $business_id = Session::get('business');
        $business = Business::find($business_id);

        if (!$business) {
            return redirect()->route('business.dashboard')->with([
                'error' => 'Error',
                'error_message' => 'Business no encontrado'
            ]);
        }

        // Validar parámetros
        $request->validate([
            'product_id' => 'required|exists:business_product,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $producto = BusinessProduct::findOrFail($request->product_id);

        // Validar que el producto pertenece al business
        if ($producto->business_id != $business_id) {
            abort(403, 'No autorizado');
        }

        // Construir query de movimientos
        $query = BusinessProductMovement::where('business_product_id', $producto->id)
            ->whereBetween('created_at', [
                Carbon::parse($request->fecha_inicio)->startOfDay(),
                Carbon::parse($request->fecha_fin)->endOfDay()
            ])
            ->with(['sucursal', 'puntoVenta'])
            ->orderBy('created_at', 'asc');

        // Filtrar por sucursal si se especifica
        if ($request->filled('sucursal_id')) {
            $query->where('sucursal_id', $request->sucursal_id);
        }

        // Filtrar por punto de venta si se especifica
        if ($request->filled('punto_venta_id')) {
            $query->where('punto_venta_id', $request->punto_venta_id);
        }

        $movimientos = $query->get();

        // Calcular saldo inicial (movimientos anteriores a la fecha de inicio)
        $saldoInicial = $this->calcularSaldoInicial(
            $producto->id,
            $request->fecha_inicio,
            $request->sucursal_id ?? null,
            $request->punto_venta_id ?? null
        );

        // Calcular kardex con saldos acumulados
        $kardex = $this->calcularKardex($movimientos, $saldoInicial, $producto->precioUni);

        // Obtener nombres para el reporte
        $sucursal = $request->filled('sucursal_id') 
            ? Sucursal::find($request->sucursal_id) 
            : null;
        
        $puntoVenta = $request->filled('punto_venta_id') 
            ? PuntoVenta::find($request->punto_venta_id) 
            : null;

        $pdf = Pdf::loadView('business.reports.kardex.pdf', [
            'business' => $business,
            'producto' => $producto,
            'kardex' => $kardex,
            'saldoInicial' => $saldoInicial,
            'fechaInicio' => Carbon::parse($request->fecha_inicio),
            'fechaFin' => Carbon::parse($request->fecha_fin),
            'sucursal' => $sucursal,
            'puntoVenta' => $puntoVenta,
        ]);

        return $pdf->stream("kardex_{$producto->codigo}_{$request->fecha_inicio}_{$request->fecha_fin}.pdf");
    }

    /**
     * Calcular saldo inicial antes de la fecha de inicio
     */
    private function calcularSaldoInicial($productoId, $fechaInicio, $sucursalId = null, $puntoVentaId = null)
    {
        $query = BusinessProductMovement::where('business_product_id', $productoId)
            ->where('created_at', '<', Carbon::parse($fechaInicio)->startOfDay());

        if ($sucursalId) {
            $query->where('sucursal_id', $sucursalId);
        }

        if ($puntoVentaId) {
            $query->where('punto_venta_id', $puntoVentaId);
        }

        $movimientos = $query->get();

        $saldo = 0;
        foreach ($movimientos as $mov) {
            if ($mov->tipo === 'entrada') {
                $saldo += $mov->cantidad;
            } else {
                $saldo -= $mov->cantidad;
            }
        }

        return $saldo;
    }

    /**
     * Calcular kardex con saldos y valores acumulados
     */
    private function calcularKardex($movimientos, $saldoInicial, $precioUnitario)
    {
        $kardex = [];
        $saldoActual = $saldoInicial;

        foreach ($movimientos as $movimiento) {
            $entrada = $movimiento->tipo === 'entrada' ? $movimiento->cantidad : 0;
            $salida = $movimiento->tipo === 'salida' ? $movimiento->cantidad : 0;
            
            $saldoActual += $entrada - $salida;

            $kardex[] = [
                'fecha' => $movimiento->created_at,
                'documento' => $movimiento->numero_factura ?? 'N/A',
                'descripcion' => $movimiento->descripcion,
                'sucursal' => $movimiento->sucursal ? $movimiento->sucursal->nombre : 'N/A',
                'punto_venta' => $movimiento->puntoVenta ? $movimiento->puntoVenta->nombre : 'N/A',
                'entrada' => $entrada,
                'salida' => $salida,
                'saldo' => $saldoActual,
                'precio_unitario' => $movimiento->precio_unitario,
                'valor_entrada' => $entrada * $movimiento->precio_unitario,
                'valor_salida' => $salida * $movimiento->precio_unitario,
                'valor_saldo' => $saldoActual * $movimiento->precio_unitario,
            ];
        }

        return $kardex;
    }
}
