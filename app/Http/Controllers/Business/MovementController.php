<?php

namespace App\Http\Controllers\Business;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessProduct;
use App\Models\BusinessProductMovement;
use App\Models\Movements;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class MovementController extends Controller
{
    public function index()
    {
        try {
            $business_id = Session::get('business') ?? null;
            $business = Business::find($business_id);
            
            if (!$business) {
                return redirect()->route('business.dashboard')->with([
                    'error' => 'Error',
                    'error_message' => 'Business no encontrado'
                ]);
            }
            
            $movements = BusinessProductMovement::with(['businessProduct', 'sucursal', 'puntoVenta'])
                ->whereHas('businessProduct', function ($query) use ($business_id) {
                    $query->where('business_id', $business_id);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(50); // 50 registros por página
            
            $dtes = Http::get(env("OCTOPUS_API_URL") . '/dtes/?nit=' . $business->nit)->json();
            $dtes = $dtes["items"] ?? [];

            $dteByCodGeneracion = [];
            foreach ($dtes as $dte) {
                if (isset($dte["codGeneracion"])) {
                    $dteByCodGeneracion[$dte["codGeneracion"]] = $dte;
                }
            }

            // Solo asociar la información del DTE sin guardar cambios
            foreach ($movements as $movement) {
                if (isset($dteByCodGeneracion[$movement->numero_factura])) {
                    $movement->invoice = $dteByCodGeneracion[$movement->numero_factura];
                } else {
                    $movement->invoice = null;
                }
            }

            return view("business.movements.index", [
                "movimientos" => $movements
            ]);
        } catch (\Exception $e) {
            // Registrar el error en logs en lugar de redirect
            \Log::error('Error en movements.index: ' . $e->getMessage());
            
            return view("business.movements.index", [
                "movimientos" => collect([])
            ])->with([
                'error' => 'Error',
                'error_message' => 'Error al cargar los movimientos: ' . $e->getMessage()
            ]);
        }
    }

    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            $movement = BusinessProductMovement::find($id);

            if (!$movement) {
                return redirect()->route('business.movements.index')->with('error', 'Error')->with("error_message", "Movimiento no encontrado");
            }

            $product = BusinessProduct::where('id', $movement->business_product_id)->first();

            if (!$product) {
                return redirect()->route('business.movements.index')->with('error', 'Error')->with("error_message", "Producto no encontrado");
            }

            // Ajustar stock según el tipo de movimiento
            if ($movement->tipo === "salida") {
                $product->stockActual += $movement->cantidad;
            } else {
                // Verifica si existen movimientos de salida para este producto
                $hasSalida = BusinessProductMovement::where('business_product_id', $product->id)
                    ->where('tipo', 'salida')
                    ->exists();

                if ($hasSalida) {
                    return redirect()->route('business.movements.index')->with('error', 'Error')->with("error_message", "No se puede eliminar el movimiento porque el producto ya tiene salidas registradas.");
                }

                $product->stockActual -= $movement->cantidad;
            }

            // Actualizar estado del stock
            if ($product->stockActual <= $product->stockMinimo) {
                $product->estado_stock = "agotado";
            } elseif (($product->stockActual - $product->stockMinimo) <= 2) {
                $product->estado_stock = "por_agotarse";
            } else {
                $product->estado_stock = "disponible";
            }

            $product->save();
            $movement->delete();

            DB::commit();
            return redirect()->route('business.movements.index')->with('success', 'Exito')->with("success_message", "Movimiento eliminado correctamente");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('business.movements.index')->with('error', 'Error')->with("error_message", "Error al eliminar el movimiento: " . $e->getMessage());
        }
    }
}