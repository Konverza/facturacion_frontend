<?php

namespace App\Http\Controllers\Business;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessProduct;
use App\Models\BusinessProductMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class MovementController extends Controller
{
    private function getBusinessFromSession(): ?Business
    {
        $businessId = Session::get('business');

        if (!$businessId) {
            return null;
        }

        return Business::find($businessId);
    }

    private function findDteByCodGeneracion(string $nit, string $codGeneracion): ?array
    {
        $page = 1;
        $limit = 25;
        $maxPages = 50;
        $totalPages = 1;

        do {
            $response = Http::timeout(30)->get(env("OCTOPUS_API_URL") . '/dtes/', [
                'nit' => $nit,
                'q' => $codGeneracion,
                'page' => $page,
                'limit' => $limit,
                'sort' => 'desc',
            ]);

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json() ?? [];
            $items = $data['items'] ?? [];

            foreach ($items as $item) {
                if (($item['codGeneracion'] ?? null) === $codGeneracion) {
                    return $item;
                }
            }

            $totalPages = (int) ($data['total_pages'] ?? 1);
            $page++;
        } while ($page <= $totalPages && $page <= $maxPages);

        return null;
    }

    public function index()
    {
        try {
            $business = $this->getBusinessFromSession();
            
            if (!$business) {
                return redirect()->route('business.dashboard')->with([
                    'error' => 'Error',
                    'error_message' => 'Business no encontrado'
                ]);
            }
            
            $movements = BusinessProductMovement::with(['businessProduct', 'sucursal', 'puntoVenta'])
                ->whereHas('businessProduct', function ($query) use ($business) {
                    $query->where('business_id', $business->id);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(50); // 50 registros por página

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

    public function getInvoiceLink(Request $request, string $id)
    {
        try {
            $business = $this->getBusinessFromSession();

            if (!$business || !$business->nit) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró el negocio seleccionado',
                ], 404);
            }

            $movement = BusinessProductMovement::with('businessProduct')->find($id);

            if (
                !$movement ||
                !$movement->numero_factura ||
                !$movement->businessProduct ||
                (int) $movement->businessProduct->business_id !== (int) $business->id
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró la factura para este movimiento',
                ], 404);
            }

            $dte = $this->findDteByCodGeneracion($business->nit, $movement->numero_factura);

            if (!$dte || empty($dte['enlace_pdf'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo obtener el enlace de la factura',
                ], 404);
            }

            if ($request->boolean('open')) {
                return redirect()->away($dte['enlace_pdf']);
            }

            return response()->json([
                'success' => true,
                'url' => $dte['enlace_pdf'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al consultar la factura',
            ], 500);
        }
    }
}