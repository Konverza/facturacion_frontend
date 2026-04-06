<?php

namespace App\Http\Controllers\Business;

use App\Services\OctopusService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class PaymentMethodController extends Controller
{
    public $dte;
    public $octopus_service;
    public $formas_pago;


    public function __construct()
    {
        $this->octopus_service = new OctopusService();
        $this->dte = session("dte", []);
        $this->ensureDteDefaults();
        $this->formas_pago = $this->octopus_service->getCatalog("CAT-017");
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

            $monto = $this->roundMoney((float) $request->monto);
            if ($monto <= 0) {
                return response()->json([
                    "success" => false,
                    "message" => "El monto debe ser mayor a 0"
                ]);
            }

            $paymentMethodId = $request->input('payment_method_id');
            if ($paymentMethodId !== null && $paymentMethodId !== '') {
                $paymentMethodId = (int) $paymentMethodId;
                $index = $this->findPaymentMethodIndex($paymentMethodId);
                if ($index === null) {
                    return response()->json([
                        "success" => false,
                        "message" => "La forma de pago a editar no existe"
                    ]);
                }

                if (!$this->canApplyAmount($monto, $paymentMethodId)) {
                    return response()->json([
                        "success" => false,
                        "message" => "El monto total de las formas de pago no puede ser mayor al total del DTE"
                    ]);
                }

                $this->dte["metodos_pago"][$index]["forma_pago"] = $request->forma_pago;
                $this->dte["metodos_pago"][$index]["monto"] = $monto;
                $this->dte["metodos_pago"][$index]["numero_documento"] = $request->numero_documento;
                $this->dte["metodos_pago"][$index]["plazo"] = $request->plazo;
                $this->dte["metodos_pago"][$index]["periodo"] = $request->periodo;

                $message = "Forma de pago actualizada correctamente";
            } else {
                if (!$this->canApplyAmount($monto)) {
                    return response()->json([
                        "success" => false,
                        "message" => "El monto total de las formas de pago no puede ser mayor al total del DTE"
                    ]);
                }

                $this->dte["metodos_pago"][] = [
                    "id" => random_int(1000, 999999),
                    "forma_pago" => $request->forma_pago,
                    "monto" => $monto,
                    "numero_documento" => $request->numero_documento,
                    "plazo" => $request->plazo,
                    "periodo" => $request->periodo,
                ];

                $message = "Forma de pago agregada correctamente";
            }

            $this->recalculatePaymentTotals();

            session(["dte" => $this->dte]);
            return response()->json([
                "success" => true,
                "message" => $message,
                "table_data" => view("layouts.partials.ajax.business.table-payment-methods-dte", [
                    "dte" => $this->dte,
                    "formas_pago" => $this->formas_pago,
                ])->render(),
                "monto_pendiente" => round($this->dte["monto_pendiente"], 2),
                "monto_abonado" => round($this->dte["monto_abonado"], 2),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function syncLastPaymentAmount()
    {
        try {
            if (!isset($this->dte["metodos_pago"]) || count($this->dte["metodos_pago"]) === 0) {
                return response()->json([
                    "success" => false,
                    "message" => "Debe agregar al menos una forma de pago para actualizar el monto"
                ]);
            }

            $this->recalculatePaymentTotals();
            $montoPendiente = $this->roundMoney((float) ($this->dte["monto_pendiente"] ?? 0));
            if ($montoPendiente <= 0) {
                return response()->json([
                    "success" => false,
                    "message" => "No hay monto pendiente por aplicar"
                ]);
            }

            $lastIndex = array_key_last($this->dte["metodos_pago"]);
            $lastPaymentMethod = $this->dte["metodos_pago"][$lastIndex];
            $newAmount = $this->roundMoney((float) ($lastPaymentMethod["monto"] ?? 0) + $montoPendiente);
            $paymentMethodId = (int) ($lastPaymentMethod["id"] ?? 0);

            if (!$this->canApplyAmount($newAmount, $paymentMethodId)) {
                return response()->json([
                    "success" => false,
                    "message" => "El monto total de las formas de pago no puede ser mayor al total del DTE"
                ]);
            }

            $this->dte["metodos_pago"][$lastIndex]["monto"] = $this->roundMoney($newAmount);
            $this->recalculatePaymentTotals();

            session(["dte" => $this->dte]);

            return response()->json([
                "success" => true,
                "message" => "Monto pendiente aplicado a la última forma de pago",
                "table_data" => view("layouts.partials.ajax.business.table-payment-methods-dte", [
                    "dte" => $this->dte
                ])->render(),
                "monto_pendiente" => round($this->dte["monto_pendiente"], 2),
                "monto_abonado" => round($this->dte["monto_abonado"], 2),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage(),
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
            $this->dte["metodos_pago"] = array_values($this->dte["metodos_pago"]);

            $this->recalculatePaymentTotals();

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

    private function ensureDteDefaults(): void
    {
        if (!is_array($this->dte)) {
            $this->dte = [];
        }

        if (!isset($this->dte["metodos_pago"]) || !is_array($this->dte["metodos_pago"])) {
            $this->dte["metodos_pago"] = [];
        }

        if (!isset($this->dte["total_pagar"])) {
            $this->dte["total_pagar"] = 0;
        }

        if (!isset($this->dte["monto_abonado"])) {
            $this->dte["monto_abonado"] = 0;
        }

        if (!isset($this->dte["monto_pendiente"])) {
            $this->dte["monto_pendiente"] = 0;
        }
    }

    private function round8(float $value): float
    {
        return round($value, 8);
    }

    private function recalculatePaymentTotals(): void
    {
        $montoAbonado = 0.0;
        foreach ($this->dte["metodos_pago"] as $metodo) {
            $montoAbonado += $this->roundMoney((float) ($metodo["monto"] ?? 0));
        }

        $totalPagar = $this->roundMoney((float) ($this->dte["total_pagar"] ?? 0));
        $this->dte["monto_abonado"] = $this->roundMoney($montoAbonado);
        $this->dte["monto_pendiente"] = $this->roundMoney($totalPagar - (float) $this->dte["monto_abonado"]);
    }

    private function canApplyAmount(float $amount, ?int $excludeId = null): bool
    {
        $total = 0.0;
        $amount = $this->roundMoney($amount);

        foreach ($this->dte["metodos_pago"] as $metodo) {
            $methodId = (int) ($metodo["id"] ?? 0);
            if ($excludeId !== null && $methodId === $excludeId) {
                continue;
            }
            $total += $this->roundMoney((float) ($metodo["monto"] ?? 0));
        }

        $total = $this->roundMoney($total + $amount);
        $limite = $this->roundMoney((float) ($this->dte["total_pagar"] ?? 0));

        return $total <= $limite;
    }

    private function roundMoney(float $value): float
    {
        return round($value, 2);
    }

    private function findPaymentMethodIndex(int $id): ?int
    {
        foreach ($this->dte["metodos_pago"] as $index => $metodo) {
            if ((int) ($metodo["id"] ?? 0) === $id) {
                return $index;
            }
        }

        return null;
    }
}
