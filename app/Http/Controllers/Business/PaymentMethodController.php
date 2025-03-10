<?php

namespace App\Http\Controllers\Business;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class PaymentMethodController extends Controller
{
    public $dte;

    public function __construct()
    {
        $this->dte = session("dte");
    }

    public function store(Request $request)
    {
        try {

            if (!isset($this->dte["products"]) || count($this->dte["products"]) == 0) {
                return response()->json([
                    "success" => false,
                    "message" => "Ingresa al menos un producto para poder agregar una forma de pago"
                ]);
            }

            if (isset($this->dte["metodos_pago"])) {
                foreach ($this->dte["metodos_pago"] as $metodo) {
                    $monto = array_sum(array_column($this->dte["metodos_pago"], "monto"));
                    if ($monto + $request->monto > $this->dte["total_pagar"]) {
                        return response()->json([
                            "success" => false,
                            "message" => "El monto total de las formas de pago no puede ser mayor al total del DTE"
                        ]);
                    }
                }
            }

            $this->dte["metodos_pago"][] = [
                "id" => rand(1, 1000),
                "forma_pago" => $request->forma_pago,
                "monto" => $request->monto,
                "numero_documento" => $request->numero_documento,
                "plazo" => $request->plazo,
                "periodo" => $request->periodo,
            ];

            $this->dte["monto_abonado"] = array_sum(array_column($this->dte["metodos_pago"], "monto"));
            $this->dte["monto_pendiente"] = $this->dte["total_pagar"] - $this->dte["monto_abonado"];

            session(["dte" => $this->dte]);
            return response()->json([
                "success" => true,
                "table_data" => view("layouts.partials.ajax.business.table-payment-methods-dte", [
                    "dte" => $this->dte
                ])->render(),
                "monto_pendiente" => round($this->dte["monto_pendiente"], 2),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function delete($id)
    {
        try {
            $this->dte["metodos_pago"] = array_filter(
                $this->dte["metodos_pago"],
                function ($metodo) use ($id) {
                    return $metodo["id"] != $id;
                }
            );

            $this->dte["monto_abonado"] = array_sum(array_column($this->dte["metodos_pago"], "monto"));
            $this->dte["monto_pendiente"] = $this->dte["total_pagar"] - $this->dte["monto_abonado"];

            session(["dte" => $this->dte]);
            return response()->json([
                "success" => true,
                "message" => "Forma de pago eliminada",
                "table_data" => view("layouts.partials.ajax.business.table-payment-methods-dte", [
                    "dte" => $this->dte
                ])->render(),
                "table" => "formas-pago",
                "monto_pendiente" => round($this->dte["monto_pendiente"], 2),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }
}
