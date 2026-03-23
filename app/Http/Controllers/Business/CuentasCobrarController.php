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
use Illuminate\Validation\Rule;

class CuentasCobrarController extends Controller
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

    private function updateCuentaEstado(CuentasCobrar $cuenta): void
    {
        if ($cuenta->saldo <= 0) {
            $cuenta->saldo = 0;
            $cuenta->estado = 'pagado';
            return;
        }

        if ($cuenta->saldo < $cuenta->monto) {
            $cuenta->estado = 'parcial';
            return;
        }

        $cuenta->estado = 'pendiente';
    }

    public function index()
    {
        try {
            $business = $this->getBusinessFromSession();
            if (!$business) {
                return redirect()->back()->with([
                    'error' => 'Error',
                    'error_message' => 'No se encontró el negocio seleccionado',
                ]);
            }

            $cuentas = CuentasCobrar::withCount('movements')
                ->where('business_id', $business->id)
                ->orderByDesc('created_at')
                ->get();
            $business_customers = BusinessCustomer::where('business_id', $business->id)->get();


            return view('business.cuentas_cobrar.index', [
                'cuentas' => $cuentas,
                'business' => $business,
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
                'fecha_vencimiento' => 'required|date',
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
                ->with('error', 'Error')->with("error_message", "Ha ocurrido un error al guardar la cuenta por cobrar");
        }
    }

    public function movement(Request $request)
    {
        try {
            $validated = $request->validate([
                'cuenta_id' => 'required|numeric|exists:cuentas_por_cobrar,id',
                'numero_factura' => 'nullable|string|required_unless:tipo,ajuste',
                'monto' => 'required|numeric|min:0.01',
                'fecha_pago' => 'required|date',
                'tipo' => ['required', Rule::in(['pago', 'ajuste', 'cargo_extra', 'descuento'])],
                'observaciones' => 'nullable|string'
            ]);

            DB::beginTransaction();
            $business = $this->getBusinessFromSession();
            if (!$business) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Error')
                    ->with('error_message', 'No se encontró el negocio seleccionado');
            }

            $cuenta = CuentasCobrar::where('business_id', $business->id)
                ->find($validated['cuenta_id']);

            if (!$cuenta) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Error')
                    ->with('error_message', 'La cuenta por cobrar no existe o no pertenece al negocio actual');
            }

            $tipo = $validated['tipo'];
            $monto = (float) $validated['monto'];

            if (in_array($tipo, ['pago', 'descuento'], true) && $monto > (float) $cuenta->saldo) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Error')
                    ->with('error_message', 'El monto no puede ser mayor al saldo pendiente');
            }

            Movements::create([
                "cuenta_id" => $cuenta->id,
                "numero_factura" => $tipo === "ajuste" ? null : $validated['numero_factura'],
                "tipo" => $tipo,
                "fecha" => $validated['fecha_pago'],
                "monto" => $monto,
                "observaciones" => $validated['observaciones'] ?? null
            ]);

            if ($tipo === "pago") {
                $cuenta->saldo -= $monto;
            } elseif ($tipo === "ajuste") {
                $cuenta->saldo = $monto;
            } elseif ($tipo === "cargo_extra") {
                $cuenta->saldo += $monto;
            } elseif ($tipo === "descuento") {
                $cuenta->saldo -= $monto;
            }

            $this->updateCuentaEstado($cuenta);
            $cuenta->save();

            DB::commit();
            return redirect()->back()
                ->with('success', 'Exito')->with("success_message", "Movimiento guardado correctamente");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error')->with("error_message", "Ha ocurrido un error al guardar el movimiento");
        }
    }

    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            $business = $this->getBusinessFromSession();
            $cuenta = CuentasCobrar::where('business_id', $business?->id)->find($id);
            if ($cuenta) {
                $cuenta->delete();
            }
            DB::commit();
            return redirect()->back()
                ->with('success', 'Exito')->with("success_message", "Cuenta por cobrar eliminada correctamente");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error')->with("error_message", "Ha ocurrido un error al eliminar la cuenta por cobrar");
        }
    }

    public function show(string $id)
    {
        try {
            $business = $this->getBusinessFromSession();
            $cuenta = CuentasCobrar::with('movements')
                ->where('business_id', $business?->id)
                ->find($id);
            if (!$cuenta) {
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

    public function getInvoiceLink(Request $request, string $id)
    {
        try {
            $business = $this->getBusinessFromSession();
            $cuenta = CuentasCobrar::where('business_id', $business?->id)->find($id);

            if (!$cuenta || !$business || !$business->nit || !$cuenta->numero_factura) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró la factura para esta cuenta',
                ], 404);
            }

            $dte = $this->findDteByCodGeneracion($business->nit, $cuenta->numero_factura);

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

    public function getInvoiceLinkByCodGeneracion(Request $request, string $codGeneracion)
    {
        try {
            $business = $this->getBusinessFromSession();

            if (!$business || !$business->nit || !$codGeneracion) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró la factura solicitada',
                ], 404);
            }

            $dte = $this->findDteByCodGeneracion($business->nit, $codGeneracion);

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
