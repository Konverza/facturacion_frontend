<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessUser;
use App\Models\InvoiceBag;
use App\Models\InvoiceBagInvoice;
use App\Models\PuntoVenta;
use App\Services\OctopusService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class InvoiceBagController extends Controller
{
    public function index()
    {
        $businessId = Session::get('business');
        $userId = auth()->id();

        $this->closeStaleBags($businessId, $userId);

        $bags = InvoiceBag::with(['invoices' => function ($query) {
                $query->where('individual_converted', false);
            }])
            ->withCount([
            'invoices as bag_invoices_count' => function ($query) {
                $query->where('individual_converted', false);
            }
        ])
            ->where('business_id', $businessId)
            ->where('user_id', $userId)
            ->orderBy('bag_date', 'desc')
            ->orderBy('correlative', 'desc')
            ->get();

        $bags->each(function ($bag) {
            $bag->bag_total = $bag->invoices->sum(function ($invoice) {
                return (float) data_get($invoice, 'totals.total_pagar', 0);
            });
        });

        return view('business.invoice-bags.index', compact('bags'));
    }

    public function show($id)
    {
        $businessId = Session::get('business');
        $userId = auth()->id();

        $this->closeStaleBags($businessId, $userId);

        $bag = InvoiceBag::with('invoices')
            ->where('business_id', $businessId)
            ->where('user_id', $userId)
            ->findOrFail($id);
        $bagInvoices = $bag->invoices->where('individual_converted', false);
        $pendingInvoices = $bagInvoices->where('status', 'pending');

        return view('business.invoice-bags.show', compact('bag', 'bagInvoices', 'pendingInvoices'));
    }

    public function storeFromDte(Request $request)
    {
        $businessId = Session::get('business');
        $userId = auth()->id();

        $businessUser = BusinessUser::with('business')
            ->where('business_id', $businessId)
            ->where('user_id', $userId)
            ->first();

        $business = $businessUser?->business;
        if (!$business || !$business->invoice_bag_enabled) {
            return redirect()->back()->withErrors([
                'bolson' => 'El Bolsón de Facturas no está habilitado para este negocio.'
            ]);
        }

        $dte = session('dte', []);
        $products = $dte['products'] ?? [];
        if (empty($products)) {
            return redirect()->back()->withErrors([
                'bolson' => 'Debe agregar al menos un producto para añadir al bolsón.'
            ]);
        }

        $omit = $request->boolean('omitir_datos_receptor');
        if (!$omit && $request->tipo_documento) {
            $numeroDocumento = (string) $request->numero_documento;
            if ($request->tipo_documento === '36') {
                $digits = preg_replace('/\D+/', '', $numeroDocumento);
                if (strlen($digits) !== 14) {
                    return redirect()->back()->withErrors([
                        'numero_documento' => 'El NIT debe tener exactamente 14 dígitos.'
                    ]);
                }
            } elseif ($request->tipo_documento === '13') {
                if (!preg_match('/^\d{8}-\d{1}$/', $numeroDocumento)) {
                    return redirect()->back()->withErrors([
                        'numero_documento' => 'El DUI debe tener formato 00000000-0.'
                    ]);
                }
            }
        }

        $bagDate = Carbon::now()->toDateString();

        $posIdResolved = $request->pos_id ?: ($businessUser?->default_pos_id);

        try {
            $invoice = DB::transaction(function () use ($businessId, $userId, $bagDate, $products, $dte, $request, $omit, $posIdResolved) {
                $bag = InvoiceBag::where('business_id', $businessId)
                    ->where('user_id', $userId)
                    ->where('bag_date', $bagDate)
                    ->where('status', 'open')
                    ->first();

                if (!$bag) {
                    $correlative = InvoiceBag::where('business_id', $businessId)
                        ->where('bag_date', $bagDate)
                        ->max('correlative');
                    $correlative = $correlative ? $correlative + 1 : 1;
                    $bagCode = Carbon::now()->format('Ymd') . '-' . str_pad($correlative, 4, '0', STR_PAD_LEFT);

                    $bag = InvoiceBag::create([
                        'business_id' => $businessId,
                        'user_id' => $userId,
                        'bag_date' => $bagDate,
                        'correlative' => $correlative,
                        'bag_code' => $bagCode,
                        'status' => 'open',
                    ]);
                }

                $invoiceCorrelative = InvoiceBagInvoice::where('invoice_bag_id', $bag->id)->max('correlative');
                $invoiceCorrelative = $invoiceCorrelative ? $invoiceCorrelative + 1 : 1;

                $customerData = [
                    'tipo_documento' => $request->tipo_documento,
                    'numero_documento' => $request->numero_documento,
                    'nrc_customer' => $request->nrc_customer,
                    'nombre_receptor' => $request->nombre_receptor,
                    'correo' => $request->correo,
                    'telefono' => $request->telefono,
                    'departamento' => $request->departamento,
                    'municipio' => $request->municipio,
                    'complemento' => $request->complemento,
                    'omitir_datos_receptor' => $omit,
                ];

                $totals = [
                    'subtotal' => $dte['subtotal'] ?? 0,
                    'total_ventas_gravadas' => $dte['total_ventas_gravadas'] ?? 0,
                    'total_ventas_exentas' => $dte['total_ventas_exentas'] ?? 0,
                    'total_ventas_no_sujetas' => $dte['total_ventas_no_sujetas'] ?? 0,
                    'total_descuentos' => $dte['total_descuentos'] ?? 0,
                    'iva' => $dte['iva'] ?? 0,
                    'total' => $dte['total'] ?? 0,
                    'total_pagar' => $dte['total_pagar'] ?? $dte['total'] ?? 0,
                ];

                return InvoiceBagInvoice::create([
                    'invoice_bag_id' => $bag->id,
                    'business_id' => $businessId,
                    'user_id' => $userId,
                    'invoice_uuid' => (string) Str::uuid(),
                    'correlative' => $invoiceCorrelative,
                    'status' => 'pending',
                    'omitted_receptor' => $omit,
                    'pos_id' => $posIdResolved ?: null,
                    'customer_data' => $customerData,
                    'products' => $products,
                    'totals' => $totals,
                    'dte_snapshot' => $dte,
                ]);
            });

            $posId = $invoice->pos_id ?: ($businessUser?->default_pos_id);
            $sucursalId = null;
            if ($posId) {
                $pos = PuntoVenta::find($posId);
                $sucursalId = $pos?->sucursal_id;
            }

            app(DTEController::class)->updateStocks(
                $invoice->invoice_uuid,
                $invoice->products ?? [],
                $businessId,
                'salida',
                $sucursalId,
                '01',
                $posId
            );

            session()->forget('dte');

            return view('business.invoice-bags.ticket-redirect', [
                'ticketUrl' => route('business.invoice-bags.ticket-pdf', $invoice->id),
                'dashboardUrl' => route('business.dashboard'),
            ]);
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'bolson' => 'Ocurrió un error al guardar en el bolsón. Intente nuevamente.'
            ]);
        }
    }

    public function ticket($invoiceId)
    {
        $businessId = Session::get('business');
        $userId = auth()->id();

        $invoice = InvoiceBagInvoice::with('bag')
            ->where('business_id', $businessId)
            ->where('user_id', $userId)
            ->findOrFail($invoiceId);

        return view('business.invoice-bags.ticket', compact('invoice'));
    }

    public function ticketPdf($invoiceId)
    {
        $businessId = Session::get('business');
        $userId = auth()->id();
        $business = Business::where('id', $businessId)->first();
        $business_data = app(OctopusService::class)->getDatosEmpresa($business->nit);

        $invoice = InvoiceBagInvoice::with('bag')
            ->where('business_id', $businessId)
            ->where('user_id', $userId)
            ->findOrFail($invoiceId);

        $pdf = Pdf::loadView('business.invoice-bags.reports.ticket', compact('invoice', 'business_data'))
            ->setPaper([0, 0, 164.41, 800]); // 58mm ancho, alto amplio

        return $pdf->stream('ticket-bolson-' . $invoice->invoice_uuid . '.pdf');
    }

    public function reportSummaryPdf($bagId)
    {
        $businessId = Session::get('business');
        $userId = auth()->id();
        $business = Business::where('id', $businessId)->first();
        $business_data = app(OctopusService::class)->getDatosEmpresa($business->nit);

        $bag = InvoiceBag::with('invoices')
            ->where('business_id', $businessId)
            ->where('user_id', $userId)
            ->findOrFail($bagId);

        $bagInvoices = $bag->invoices->where('individual_converted', false);
        $bagTotal = $bagInvoices->sum(function ($invoice) {
            return (float) data_get($invoice, 'totals.total_pagar', 0);
        });

        $pdf = Pdf::loadView('business.invoice-bags.reports.summary', [
            'bag' => $bag,
            'bagTotal' => $bagTotal,
            'bagInvoices' => $bagInvoices,
            'business_data' => $business_data,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('bolson-resumen-' . $bag->bag_code . '.pdf');
    }

    public function reportDetailPdf($bagId)
    {
        $businessId = Session::get('business');
        $userId = auth()->id();
        $business = Business::where('id', $businessId)->first();
        $business_data = app(OctopusService::class)->getDatosEmpresa($business->nit);

        $bag = InvoiceBag::with('invoices')
            ->where('business_id', $businessId)
            ->where('user_id', $userId)
            ->findOrFail($bagId);

        $bagInvoices = $bag->invoices->where('individual_converted', false);
        $bagTotal = $bagInvoices->sum(function ($invoice) {
            return (float) data_get($invoice, 'totals.total_pagar', 0);
        });

        $pdf = Pdf::loadView('business.invoice-bags.reports.detail', [
            'bag' => $bag,
            'bagTotal' => $bagTotal,
            'bagInvoices' => $bagInvoices,
            'business_data' => $business_data,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('bolson-detalle-' . $bag->bag_code . '.pdf');
    }

    public function showInvoice($invoiceId)
    {
        $businessId = Session::get('business');
        $userId = auth()->id();

        $invoice = InvoiceBagInvoice::with('bag')
            ->where('business_id', $businessId)
            ->where('user_id', $userId)
            ->findOrFail($invoiceId);

        return view('business.invoice-bags.invoice', compact('invoice'));
    }

    public function convertToDte($invoiceId)
    {
        $businessId = Session::get('business');
        $userId = auth()->id();

        $invoice = InvoiceBagInvoice::where('business_id', $businessId)
            ->where('user_id', $userId)
            ->findOrFail($invoiceId);

        if ($invoice->status !== 'pending') {
            return redirect()->back()->withErrors([
                'bolson' => 'Esta factura no puede convertirse a DTE.'
            ]);
        }

        $snapshot = $invoice->dte_snapshot ?? [];
        if (!is_array($snapshot) || empty($snapshot)) {
            return redirect()->back()->withErrors([
                'bolson' => 'No se encontró información suficiente para convertir a DTE.'
            ]);
        }

        $snapshot['type'] = '01';
        $snapshot['omitir_datos_receptor'] = (bool) ($invoice->omitted_receptor ?? false);
        $snapshot['skip_inventory'] = true;
        $snapshot['invoice_bag_invoice_id'] = $invoice->id;
        session(['dte' => $snapshot]);

        app(DTEProductController::class)->totals();

        return redirect()->route('business.dte.create', ['document_type' => '01']);
    }

    public function voidInvoice($invoiceId)
    {
        $businessId = Session::get('business');
        $userId = auth()->id();

        $invoice = InvoiceBagInvoice::where('business_id', $businessId)
            ->where('user_id', $userId)
            ->findOrFail($invoiceId);

        if ($invoice->status !== 'pending') {
            return redirect()->back()->withErrors([
                'bolson' => 'Esta factura no puede anularse.'
            ]);
        }

        $sucursalId = null;
        if ($invoice->pos_id) {
            $pos = PuntoVenta::find($invoice->pos_id);
            $sucursalId = $pos?->sucursal_id;
        }

        app(DTEController::class)->updateStocks(
            $invoice->invoice_uuid,
            $invoice->products ?? [],
            $businessId,
            'entrada',
            $sucursalId,
            '01',
            $invoice->pos_id
        );

        $invoice->update([
            'status' => 'voided',
            'voided_at' => now(),
        ]);

        return redirect()->back();
    }

    public function sendToHacienda($bagId)
    {
        $businessId = Session::get('business');
        $userId = auth()->id();

        $bag = InvoiceBag::with('invoices')
            ->where('business_id', $businessId)
            ->where('user_id', $userId)
            ->findOrFail($bagId);

        if ($bag->status !== 'open') {
            return redirect()->back()->withErrors([
                'bolson' => 'Este bolsón ya fue procesado.'
            ]);
        }

        $pendingInvoices = $bag->invoices->where('status', 'pending');
        if ($pendingInvoices->isEmpty()) {
            return redirect()->back()->withErrors([
                'bolson' => 'No hay facturas pendientes en este bolsón.'
            ]);
        }

        $mergedProducts = [];
        foreach ($pendingInvoices as $invoice) {
            $mergedProducts = array_merge($mergedProducts, $invoice->products ?? []);
        }

        $invoiceIds = $pendingInvoices->pluck('id')->values()->all();

        session(['dte' => [
            'type' => '01',
            'products' => $mergedProducts,
            'customer' => [
                'omitir_datos_receptor' => true,
            ],
            'omitir_datos_receptor' => true,
            'skip_inventory' => true,
            'invoice_bag_id' => $bag->id,
            'invoice_bag_invoice_ids' => $invoiceIds,
        ]]);

        app(DTEProductController::class)->totals();

        return redirect()->route('business.dte.create', ['document_type' => '01']);
    }

    private function closeStaleBags($businessId, $userId): void
    {
        $today = Carbon::now()->toDateString();

        InvoiceBag::where('business_id', $businessId)
            ->where('user_id', $userId)
            ->where('status', 'open')
            ->whereDate('bag_date', '<', $today)
            ->whereDoesntHave('invoices', function ($query) {
                $query->where('status', 'pending');
            })
            ->update([
                'status' => 'closed',
                'sent_at' => now(),
            ]);
    }
}
