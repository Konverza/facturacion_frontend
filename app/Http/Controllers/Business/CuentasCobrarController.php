<?php

namespace App\Http\Controllers\Business;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessCustomer;
use App\Models\CuentasCobrar;
use App\Models\Movements;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class CuentasCobrarController extends Controller
{
    public function index()
    {
        try {
            
            $business_id = Session::get('business') ?? null;
            $cuentas = CuentasCobrar::where('business_id', $business_id)->get();
            $business = Business::find($business_id ?? $business_id);
            $business_customers = BusinessCustomer::where('business_id', $business_id)->get();
            $dtes = Http::get(env("OCTOPUS_API_URL") . '/dtes/?nit=' . $business->nit)->json();

            $dteByCodGeneracion = [];
            foreach ($dtes as $dte) {
                if (isset($dte["codGeneracion"])) {
                    $dteByCodGeneracion[$dte["codGeneracion"]] = $dte;
                }
            }

            $cuentas = $cuentas->map(function ($cuenta) use ($dteByCodGeneracion) {
                if (isset($dteByCodGeneracion[$cuenta->numero_factura])) {
                    $cuenta->invoice = $dteByCodGeneracion[$cuenta->numero_factura];
                } else {
                    $cuenta->invoice = null;
                }
                return $cuenta;
            });

            $dtes = collect($dtes)
                ->filter(function ($dte) {
                    return $dte["estado"] === "PROCESADO";
                })
                ->mapWithKeys(function ($dte) {
                    return [$dte["codGeneracion"] => $dte["codGeneracion"]];
                });


            return view('business.cuentas_cobrar.index', [
                'cuentas' => $cuentas,
                'dtes' => $dtes,
                'business_customers' => $business_customers
            ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with([
                    'error' => 'Error',
                    'error_message' => 'Ha ocurrido un error al cargar las cuentas por cobrar'
                ]);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'numero_factura' => 'required|string',
                'cliente' => 'required|string',
                'monto' => 'required|numeric|min:0',
                'fecha_vencimiento' => 'required|string',
                'observaciones' => 'nullable|string'
            ]);
            $business_id = Session::get('business') ?? null;
            DB::beginTransaction();
            $cuenta = new CuentasCobrar();
            $cuenta->numero_factura = $request->numero_factura;
            $cuenta->cliente = $request->cliente;
            $cuenta->monto = $request->monto;
            $cuenta->saldo = $request->monto;
            $cuenta->estado = "pendiente";
            $cuenta->fecha_vencimiento = $request->fecha_vencimiento;
            $cuenta->observaciones = $request->observaciones;
            $cuenta->business_id = $business_id;
            $cuenta->save();
            DB::commit();
            return redirect()->back()
                ->with('success', 'Exito')->with("success_message", "Cuenta por cobrar guardada correctamente");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error')->with("success_message", "Ha ocurrido un error al guardar la cuenta por cobrar");
        }
    }

    public function movement(Request $request)
    {
        try {
            $request->validate([
                'cuenta_id' => 'required|numeric',
                'numero_factura' => 'nullable|string',
                'monto' => 'required|numeric|min:0',
                'fecha_pago' => 'required|string',
                'tipo' => 'required|string',
                'observaciones' => 'nullable|string'
            ]);
            DB::beginTransaction();
            $cuenta = CuentasCobrar::find($request->cuenta_id);
            if ($cuenta) {
                Movements::create([
                    "cuenta_id" => $cuenta->id,
                    "numero_factura" => $request->tipo === "Ajuste" ? null : $request->numero_factura,
                    "tipo" => $request->tipo,
                    "fecha" => $request->fecha_pago,
                    "monto" => $request->monto,
                    "observaciones" => $request->observaciones
                ]);

                if ($request->tipo === "pago") {
                    $cuenta->saldo -= $request->monto;
                } elseif ($request->tipo === "ajuste") {
                    $cuenta->saldo = $request->monto;
                } elseif ($request->tipo === "cargo_extra") {
                    $cuenta->saldo += $request->monto;
                } elseif ($request->tipo === "descuento") {
                    $cuenta->saldo -= $request->monto;
                }

                $mitad = $cuenta->monto / 2;
                if ($cuenta->saldo <= 0) {
                    $cuenta->estado = "pagado";
                } elseif ($cuenta->saldo < $mitad) {
                    $cuenta->estado = "parcial";
                } else {
                    $cuenta->estado = "pendiente";
                }

                $cuenta->save();
            }
            DB::commit();
            return redirect()->back()
                ->with('success', 'Exito')->with("success_message", "Movimiento guardado correctamente");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error')->with("success_message", "Ha ocurrido un error al guardar la cuenta por cobrar");
        }
    }

    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            $cuenta = CuentasCobrar::find($id);
            if ($cuenta) {
                $cuenta->delete();
            }
            DB::commit();
            return redirect()->back()
                ->with('success', 'Exito')->with("success_message", "Cuenta por cobrar eliminada correctamente");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error')->with("success_message", "Ha ocurrido un error al eliminar la cuenta por cobrar");
        }
    }

    public function show(string $id)
    {
        try {
            $cuenta = CuentasCobrar::with('movements')->find($id);
            $business_id = Session::get('business') ?? null;
            $business = Business::find($business_id ?? $business_id);
            $dtes = Http::get(env("OCTOPUS_API_URL") . '/dtes/?nit=' . $business->nit)->json();

            $dteByCodGeneracion = [];
            foreach ($dtes as $dte) {
                if (isset($dte["codGeneracion"])) {
                    $dteByCodGeneracion[$dte["codGeneracion"]] = $dte;
                }
            }

            foreach ($cuenta->movements as $movement) {
                if (isset($dteByCodGeneracion[$movement->numero_factura])) {
                    $movement->invoice = $dteByCodGeneracion[$movement->numero_factura];
                } else {
                    $movement->invoice = null;
                }
            }

            return response()->json([
                "html" => view(
                    "layouts.partials.ajax.business.show-cuentas",
                    [
                        "cuenta" => $cuenta,
                        "movimientos" => $cuenta->movements
                    ]
                )->render(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "html" => view(
                    "layouts.partials.ajax.business.show-cuentas",
                    [
                        "cuenta" => null,
                        "movimientos" => []
                    ]
                )->render(),
            ]);
        }
    }
}
