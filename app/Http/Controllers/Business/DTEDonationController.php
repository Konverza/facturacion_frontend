<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Business;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use NumberFormatter;


class DTEDonationController extends Controller
{
    public $dte;
    public $business_id;
    public $business;

    public function __construct()
    {
        $this->dte = session("dte");
        $this->business_id = session("business");
        $this->business = Business::find($this->business_id);
    }

    public function store(Request $request)
    {
        try {
            $depreciacion = $request->depreciacion;
            $valor_donado = $request->valor_donado;

            if (in_array($request->tipo_donacion, [1, 3])) {
                $depreciacion = 0;
                $valor_donado = $request->cantidad * $request->valor_unitario;
            }

            $this->dte["products"][] = [
                "id" => rand(1, 1000),
                "tipo_donacion" => $request->tipo_donacion,
                "unidad_medida" => $request->unidad_medida,
                "cantidad" => $request->cantidad,
                "descripcion" => $request->descripcion,
                "depreciacion" => $depreciacion,
                "valor_unitario" => $request->valor_unitario,
                "valor_donado" => $valor_donado
            ];

            $this->totals();
            session(["dte" => $this->dte]);
            return response()->json([
                "success" => true,
                "message" => "Donación agregada correctamente",
                "table_data" => view("layouts.partials.ajax.business.table-comprobante-donacion", [
                    "dte" => $this->dte
                ])->render(),
                "table" => "comprobante-donacion",
                "drawer" => "drawer-new-product",
                "monto_pendiente" => $this->dte["monto_pendiente"],
            ]);
        } catch (\Exception $e) {
            Log::error("Error al agregar donación: " . $e->getMessage());
            return response()->json([
                "success" => false,
                "message" => "Error al agregar donación: " . $e->getMessage()
            ]);
        }
    }

    public function delete(string $id)
    {
        try {
            $this->dte["products"] = array_filter(
                $this->dte["products"],
                function ($documento) use ($id) {
                    return $documento["id"] != $id;
                }
            );
            $this->totals();
            session(["dte" => $this->dte]);
            return response()->json([
                "success" => true,
                "message" => "Donación eliminada correctamente",
                "table_data" => view("layouts.partials.ajax.business.table-comprobante-donacion", [
                    "dte" => $this->dte
                ])->render(),
                "table" => "comprobante-donacion",
                "monto_pendiente" => $this->dte["monto_pendiente"],
            ]);
        } catch (\Exception $e) {
            Log::error("Error al eliminar donación: " . $e->getMessage());
            return response()->json([
                "success" => false,
                "message" => "Error al eliminar donación: " . $e->getMessage()
            ]);
        }
    }

    private function totals()
    {
        $this->dte["total"] = array_sum(array_column($this->dte["products"], "valor_donado"));
        $total_pagar = array_sum(array_column(
            array_filter($this->dte["products"], function ($product) {
                return $product["tipo_donacion"] == 1;
            }),
            "valor_donado"
        ));
        $this->dte["total_pagar"] = $total_pagar;
        $this->dte["monto_pendiente"] = $total_pagar - ($this->dte["monto_abonado"] ?? 0);
        session(["dte" => $this->dte]);
    }

}
