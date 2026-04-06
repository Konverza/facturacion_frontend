<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessCustomer;
use App\Models\BusinessQuotationDeliveryTime;
use App\Models\BusinessQuotationPaymentMethod;
use App\Models\Project;
use App\Models\Quotation;
use App\Services\OctopusService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class QuotationController extends Controller
{
    public function __construct(private OctopusService $octopusService)
    {
    }

    public function index()
    {
        $quotations = Quotation::where('business_id', session('business'))
            ->orderByDesc('updated_at')
            ->paginate(10);

        return view('business.quotations.index', [
            'quotations' => $quotations,
        ]);
    }

    public function create(Request $request)
    {
        $fromProject = $request->boolean('from_project') && session()->has('project_source_id');
        $sessionDte = session('dte');

        if ($fromProject && is_array($sessionDte)) {
            $dte = $sessionDte;
            $sourceType = (string) ($dte['type'] ?? '01');
            $targetType = (string) ($request->input('dte_type', $sourceType));
            $dte = $this->normalizeContentForDocumentType($dte, $sourceType, $targetType);
            $dte['type'] = $targetType;
        } else {
            session()->forget('project_source_id');
            $dte = $this->defaultDteContent('01');
        }

        session(['dte' => $dte]);

        return $this->formView('Crear cotizacion', 'business.quotations.create', null, $dte);
    }

    public function store(Request $request)
    {
        $validated = $this->validateQuotation($request);
        $current = session('dte', $this->defaultDteContent($validated['dte_type']));
        $businessId = (int) session('business');

        $current = $this->normalizeContentForDocumentType(
            is_array($current) ? $current : [],
            (string) ($current['type'] ?? $validated['dte_type']),
            $validated['dte_type']
        );

        $paymentMethod = BusinessQuotationPaymentMethod::where('business_id', $businessId)
            ->findOrFail((int) $validated['payment_method_id']);
        $deliveryTime = BusinessQuotationDeliveryTime::where('business_id', $businessId)
            ->findOrFail((int) $validated['delivery_time_id']);

        $customer = $this->buildCustomerData($validated);
        $current['type'] = $validated['dte_type'];
        $current['customer'] = $customer;

        $quotation = Quotation::create([
            'business_id' => session('business'),
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'type' => $validated['dte_type'],
            'content' => $current,
            'quotation_meta' => $this->buildMeta($validated, $paymentMethod, $deliveryTime),
        ]);

        $projectSourceId = session('project_source_id');
        if ($projectSourceId) {
            $project = Project::where('business_id', session('business'))->find($projectSourceId);
            if ($project) {
                $project->quotation_id = $quotation->id;
                $project->status = 'quotation';
                $project->save();
            }
        }

        session()->forget('dte');
        session()->forget('project_source_id');

        return redirect()->route('business.quotations.show', $quotation->id)
            ->with('success', 'Exito')
            ->with('success_message', 'Cotizacion guardada correctamente');
    }

    public function show(string $id)
    {
        $quotation = $this->findQuotation($id);
        $business = Business::findOrFail(session('business'));
        $companyData = $this->octopusService->getDatosEmpresa((string) $business->nit);
        $logo = $this->resolveLogo((string) $business->nit, is_array($companyData) ? $companyData : []);
        $canProfitabilityReport = $this->canAccessProfitabilityReport($business);

        return view('business.quotations.show', [
            'quotation' => $quotation,
            'content' => is_array($quotation->content) ? $quotation->content : [],
            'meta' => is_array($quotation->quotation_meta) ? $quotation->quotation_meta : [],
            'business' => $business,
            'companyData' => is_array($companyData) ? $companyData : [],
            'logo' => $logo,
            'canProfitabilityReport' => $canProfitabilityReport,
        ]);
    }

    public function edit(string $id)
    {
        $quotation = $this->findQuotation($id);
        $content = is_array($quotation->content) ? $quotation->content : $this->defaultDteContent($quotation->type);
        session(['dte' => $content]);

        return $this->formView('Editar cotizacion', 'business.quotations.edit', $quotation, $content);
    }

    public function update(Request $request, string $id)
    {
        $quotation = $this->findQuotation($id);
        $validated = $this->validateQuotation($request);
        $businessId = (int) session('business');

        $paymentMethod = BusinessQuotationPaymentMethod::where('business_id', $businessId)
            ->findOrFail((int) $validated['payment_method_id']);
        $deliveryTime = BusinessQuotationDeliveryTime::where('business_id', $businessId)
            ->findOrFail((int) $validated['delivery_time_id']);

        $current = session('dte', $this->defaultDteContent($validated['dte_type']));
        $current = $this->normalizeContentForDocumentType(
            is_array($current) ? $current : [],
            (string) ($current['type'] ?? $validated['dte_type']),
            $validated['dte_type']
        );
        $current['type'] = $validated['dte_type'];
        $current['customer'] = $this->buildCustomerData($validated);

        $quotation->update([
            'name' => $validated['name'],
            'type' => $validated['dte_type'],
            'content' => $current,
            'quotation_meta' => $this->buildMeta($validated, $paymentMethod, $deliveryTime),
        ]);

        session()->forget('dte');

        return redirect()->route('business.quotations.show', $quotation->id)
            ->with('success', 'Exito')
            ->with('success_message', 'Cotizacion actualizada correctamente');
    }

    public function destroy(string $id)
    {
        $quotation = $this->findQuotation($id);
        $quotation->delete();

        return redirect()->route('business.quotations.index')
            ->with('success', 'Exito')
            ->with('success_message', 'Cotizacion eliminada correctamente');
    }

    public function convert(Request $request, string $id)
    {
        $request->validate([
            'document_type' => 'required|in:01,03',
        ]);

        $targetDocumentType = $request->input('document_type');

        $quotation = $this->findQuotation($id);
        $content = is_array($quotation->content) ? $quotation->content : [];
        $sourceDocumentType = (string) ($content['type'] ?? $quotation->type ?? '01');

        $content = $this->normalizeContentForDocumentType($content, $sourceDocumentType, $targetDocumentType);
        $content['type'] = $targetDocumentType;
        $content['quotation_id'] = $quotation->id;
        $content['customer'] = $this->resolveCustomerForDte($content, $targetDocumentType);
        $content = $this->attachQuotationObservation($content, $quotation);

        session([
            'dte' => $content,
            'quotation_source_id' => $quotation->id,
        ]);

        return redirect()->route('business.dte.create', [
            'document_type' => $targetDocumentType,
        ])->with('success', 'Exito')
            ->with('success_message', 'Cotizacion cargada para convertir a DTE');
    }

    private function resolveCustomerForDte(array $content, string $targetDocumentType): array
    {
        $current = isset($content['customer']) && is_array($content['customer'])
            ? $content['customer']
            : [];

        $customerId = $current['id'] ?? ($content['customer_id'] ?? null);
        $businessId = (int) session('business');

        if ($customerId) {
            $customer = BusinessCustomer::where('business_id', $businessId)
                ->where('id', $customerId)
                ->first();

            if ($customer) {
                $resolved = [
                    'id' => $customer->id,
                    'tipoDocumento' => $customer->tipoDocumento,
                    'numDocumento' => $customer->numDocumento,
                    'nrc' => $customer->nrc,
                    'nombre' => $customer->nombre,
                    'nombreComercial' => $customer->nombreComercial,
                    'codActividad' => $customer->codActividad,
                    'descActividad' => $current['descActividad'] ?? null,
                    'departamento' => $customer->departamento,
                    'municipio' => $customer->municipio,
                    'complemento' => $customer->complemento,
                    'telefono' => $customer->telefono,
                    'correo' => $customer->correo,
                    'tipoPersona' => $customer->tipoPersona,
                    'codPais' => $customer->codPais,
                    'price_variant_id' => $customer->price_variant_id,
                ];

                return $this->normalizeCustomerDocumentForDteType($resolved, $targetDocumentType);
            }
        }

        $numDocumento = $current['numDocumento']
            ?? ($current['numero_documento'] ?? null)
            ?? ($content['numero_documento'] ?? null);

        $resolved = [
            'id' => $customerId,
            'tipoDocumento' => $current['tipoDocumento'] ?? $this->inferDocumentType($numDocumento),
            'numDocumento' => $numDocumento,
            'nrc' => $current['nrc'] ?? ($content['nrc_customer'] ?? null),
            'nombre' => $current['nombre'] ?? ($current['nombre_receptor'] ?? null),
            'nombreComercial' => $current['nombreComercial'] ?? null,
            'codActividad' => $current['codActividad'] ?? null,
            'descActividad' => $current['descActividad'] ?? null,
            'departamento' => $current['departamento'] ?? null,
            'municipio' => $current['municipio'] ?? null,
            'complemento' => $current['complemento'] ?? null,
            'telefono' => $current['telefono'] ?? ($content['telefono'] ?? null),
            'correo' => $current['correo'] ?? ($content['correo'] ?? null),
            'tipoPersona' => $current['tipoPersona'] ?? null,
            'codPais' => $current['codPais'] ?? null,
            'price_variant_id' => $current['price_variant_id'] ?? null,
        ];

        return $this->normalizeCustomerDocumentForDteType($resolved, $targetDocumentType);
    }

    private function normalizeCustomerDocumentForDteType(array $customer, string $targetDocumentType): array
    {
        // Regla de negocio: para CCF (03) el documento no debe llevar guiones.
        if ($targetDocumentType !== '03') {
            return $customer;
        }

        $docType = isset($customer['tipoDocumento']) ? (string) $customer['tipoDocumento'] : null;
        $numDocumento = isset($customer['numDocumento']) ? (string) $customer['numDocumento'] : null;

        if ($numDocumento === null || trim($numDocumento) === '') {
            return $customer;
        }

        // Aplicar a documentos fiscales más comunes (NIT y DUI).
        if (in_array($docType, ['13', '36'], true)) {
            $customer['numDocumento'] = preg_replace('/\D+/', '', $numDocumento);
        }

        return $customer;
    }

    private function inferDocumentType(?string $numDocumento): ?string
    {
        if (!is_string($numDocumento) || trim($numDocumento) === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $numDocumento);
        if (strlen($digits) === 14) {
            return '36';
        }

        if (strlen($digits) === 9) {
            return '13';
        }

        return null;
    }

    public function pdf(string $id)
    {
        $quotation = $this->findQuotation($id);
        $content = is_array($quotation->content) ? $quotation->content : [];
        $meta = is_array($quotation->quotation_meta) ? $quotation->quotation_meta : [];

        $business = Business::findOrFail(session('business'));
        $companyData = $this->octopusService->getDatosEmpresa((string) $business->nit);
        $logo = $this->resolveLogo((string) $business->nit, is_array($companyData) ? $companyData : []);

        $pdf = Pdf::loadView('business.quotations.pdf', [
            'quotation' => $quotation,
            'content' => $content,
            'meta' => $meta,
            'business' => $business,
            'companyData' => is_array($companyData) ? $companyData : [],
            'logo' => $logo,
        ])->setPaper('letter', 'portrait');

        return $pdf->stream('cotizacion-' . $quotation->id . '.pdf');
    }

    public function profitabilityPdf(string $id)
    {
        $quotation = $this->findQuotation($id);
        $content = is_array($quotation->content) ? $quotation->content : [];
        $meta = is_array($quotation->quotation_meta) ? $quotation->quotation_meta : [];

        $business = Business::findOrFail(session('business'));
        if (!$this->canAccessProfitabilityReport($business)) {
            abort(403, 'El modulo de reporte de rentabilidad no esta habilitado para este negocio.');
        }

        $companyData = $this->octopusService->getDatosEmpresa((string) $business->nit);
        $logo = $this->resolveLogo((string) $business->nit, is_array($companyData) ? $companyData : []);

        $pdf = Pdf::loadView('business.quotations.profitability-pdf', [
            'quotation' => $quotation,
            'content' => $content,
            'meta' => $meta,
            'business' => $business,
            'companyData' => is_array($companyData) ? $companyData : [],
            'logo' => $logo,
        ])->setPaper('letter', 'portrait');

        return $pdf->stream('cotizacion-rentabilidad-' . $quotation->id . '.pdf');
    }

    private function formView(string $title, string $view, ?Quotation $quotation, array $dte)
    {
        $businessCustomers = BusinessCustomer::where('business_id', session('business'))
            ->orderByDesc('id')
            ->get();

        $business = Business::find(session('business'));
        $unidades = $this->octopusService->getCatalog('CAT-014');
        $paymentMethods = BusinessQuotationPaymentMethod::where('business_id', session('business'))
            ->orderBy('name')
            ->get();
        $deliveryTimes = BusinessQuotationDeliveryTime::where('business_id', session('business'))
            ->orderBy('name')
            ->get();
        $meta = $quotation ? ($quotation->quotation_meta ?? []) : [];

        return view($view, [
            'pageTitle' => $title,
            'quotation' => $quotation,
            'dte' => $dte,
            'number' => $dte['type'] ?? '01',
            'business_customers' => $businessCustomers,
            'unidades_medidas' => $unidades,
            'business' => $business,
            'meta' => $meta,
            'paymentMethodOptions' => $paymentMethods->pluck('name', 'id')->toArray(),
            'deliveryTimeOptions' => $deliveryTimes->pluck('name', 'id')->toArray(),
            'selectedPaymentMethodId' => $this->resolveSelectedOptionId(
                $meta,
                $paymentMethods->pluck('name', 'id')->toArray(),
                'forma_pago_id',
                'forma_pago_tipo'
            ),
            'selectedDeliveryTimeId' => $this->resolveSelectedOptionId(
                $meta,
                $deliveryTimes->pluck('name', 'id')->toArray(),
                'tiempo_entrega_id',
                'tiempo_entrega'
            ),
            'hasPaymentMethods' => $paymentMethods->isNotEmpty(),
            'hasDeliveryTimes' => $deliveryTimes->isNotEmpty(),
        ]);
    }

    private function validateQuotation(Request $request): array
    {
        $businessId = (int) session('business');

        return $request->validate([
            'name' => 'required|string|max:255',
            'dte_type' => 'required|in:01,03',
            'customer_id' => 'nullable|integer',
            'numero_documento' => 'nullable|string|max:30',
            'nombre_receptor' => 'required|string|max:255',
            'nrc_customer' => 'nullable|string|max:30',
            'correo' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:30',
            'vigencia_dias' => 'required|integer|min:1|max:365',
            'delivery_time_id' => [
                'required',
                'integer',
                Rule::exists('business_quotation_delivery_times', 'id')->where(
                    fn($query) => $query->where('business_id', $businessId)
                ),
            ],
            'payment_method_id' => [
                'required',
                'integer',
                Rule::exists('business_quotation_payment_methods', 'id')->where(
                    fn($query) => $query->where('business_id', $businessId)
                ),
            ],
            'thank_you_message' => 'nullable|string|max:500',
            'terms_conditions' => 'nullable|string|max:1000',
        ]);
    }

    private function buildCustomerData(array $validated): array
    {
        return [
            'id' => $validated['customer_id'] ?? null,
            'numDocumento' => $validated['numero_documento'] ?? null,
            'nombre' => $validated['nombre_receptor'],
            'nrc' => $validated['nrc_customer'] ?? null,
            'correo' => $validated['correo'] ?? null,
            'telefono' => $validated['telefono'] ?? null,
        ];
    }

    private function buildMeta(
        array $validated,
        BusinessQuotationPaymentMethod $paymentMethod,
        BusinessQuotationDeliveryTime $deliveryTime
    ): array
    {
        return [
            'vigencia_dias' => (int) $validated['vigencia_dias'],
            'tiempo_entrega_id' => $deliveryTime->id,
            'tiempo_entrega' => $deliveryTime->name,
            'forma_pago_id' => $paymentMethod->id,
            'forma_pago_tipo' => $paymentMethod->name,
            'thank_you_message' => $validated['thank_you_message'] ?? 'Gracias por su preferencia.',
            'terms_conditions' => $validated['terms_conditions'] ?? null,
        ];
    }

    private function resolveSelectedOptionId(
        array $meta,
        array $availableOptions,
        string $idKey,
        string $legacyLabelKey
    ): ?string {
        $selectedById = isset($meta[$idKey]) ? (string) $meta[$idKey] : null;
        if ($selectedById !== null && isset($availableOptions[$selectedById])) {
            return $selectedById;
        }

        $legacyLabel = isset($meta[$legacyLabelKey]) ? trim((string) $meta[$legacyLabelKey]) : '';
        if ($legacyLabel === '') {
            return null;
        }

        foreach ($availableOptions as $id => $label) {
            if (mb_strtolower(trim((string) $label)) === mb_strtolower($legacyLabel)) {
                return (string) $id;
            }
        }

        return null;
    }

    private function defaultDteContent(string $type): array
    {
        return [
            'type' => $type,
            'products' => [],
            'subtotal' => 0,
            'total' => 0,
            'total_pagar' => 0,
            'total_descuentos' => 0,
            'total_ventas_gravadas' => 0,
            'total_ventas_exentas' => 0,
            'total_ventas_no_sujetas' => 0,
            'iva' => 0,
            'retener_iva' => 'inactive',
            'retener_renta' => 'inactive',
            'percibir_iva' => 'inactive',
            'total_iva_retenido' => 0,
            'isr' => 0,
            'remove_discounts' => false,
        ];
    }

    private function normalizeContentForDocumentType(array $content, string $sourceType, string $targetType): array
    {
        $products = isset($content['products']) && is_array($content['products'])
            ? $content['products']
            : [];

        $normalizedProducts = [];

        foreach ($products as $product) {
            if (!is_array($product)) {
                $normalizedProducts[] = $product;
                continue;
            }

            $tipoVenta = (string) ($product['tipo'] ?? 'Gravada');
            $cantidad = (float) ($product['cantidad'] ?? 0);

            $lineCurrent =
                (float) ($product['ventas_gravadas'] ?? 0)
                + (float) ($product['ventas_exentas'] ?? 0)
                + (float) ($product['ventas_no_sujetas'] ?? 0);

            if ($lineCurrent <= 0) {
                $lineCurrent = (float) ($product['total'] ?? 0);
            }

            $baseLine = $lineCurrent;
            if ($tipoVenta === 'Gravada' && $this->lineValuesIncludeIva($product, $lineCurrent, $sourceType)) {
                $baseLine = $lineCurrent / 1.13;
            }

            $baseLine = round((float) $baseLine, 8);

            $baseUnit = $cantidad > 0
                ? round($baseLine / $cantidad, 8)
                : round((float) ($product['precio_sin_tributos'] ?? 0), 8);

            if ($baseUnit <= 0) {
                $fallbackPrice = (float) ($product['precio'] ?? 0);
                if ($tipoVenta === 'Gravada' && $this->lineValuesIncludeIva($product, $lineCurrent, $sourceType)) {
                    $fallbackPrice = $fallbackPrice / 1.13;
                }
                $baseUnit = round($fallbackPrice, 8);
            }

            $lineTarget = $baseLine;
            if ($tipoVenta === 'Gravada' && $targetType === '01') {
                $lineTarget = round($baseLine * 1.13, 8);
            }

            $precioTarget = $cantidad > 0
                ? round($lineTarget / $cantidad, 8)
                : ($tipoVenta === 'Gravada' && $targetType === '01' ? round($baseUnit * 1.13, 8) : $baseUnit);

            $product['precio_sin_tributos'] = $baseUnit;
            $product['precio'] = $precioTarget;
            $product['ventas_gravadas'] = $tipoVenta === 'Gravada' ? $lineTarget : 0;
            $product['ventas_exentas'] = $tipoVenta === 'Exenta' ? $baseLine : 0;
            $product['ventas_no_sujetas'] = $tipoVenta === 'No sujeta' ? $baseLine : 0;
            $product['total'] = round(
                (float) $product['ventas_gravadas']
                + (float) $product['ventas_exentas']
                + (float) $product['ventas_no_sujetas'],
                8
            );

            if ($tipoVenta === 'Gravada') {
                $ivaLine = $targetType === '01'
                    ? (($lineTarget / 1.13) * 0.13)
                    : ($baseLine * 0.13);
                $product['iva'] = round((float) $ivaLine, 8);
            } else {
                $product['iva'] = 0;
            }

            $normalizedProducts[] = $product;
        }

        $content['products'] = $normalizedProducts;
        $content = $this->recalculateContentTotals($content, $targetType);

        // Al convertir desde cotización, la forma de pago debe recalcularse según el total del DTE destino.
        $content['metodos_pago'] = [];
        $content['monto_abonado'] = 0;
        $content['monto_pendiente'] = round((float) ($content['total_pagar'] ?? 0), 8);

        return $content;
    }

    private function lineValuesIncludeIva(array $product, float $lineCurrent, string $sourceType): bool
    {
        if ($lineCurrent <= 0) {
            return false;
        }

        if ($sourceType === '01') {
            return true;
        }

        if (!in_array($sourceType, ['03', '05', '06'], true)) {
            return false;
        }

        $ivaLine = (float) ($product['iva'] ?? 0);
        if ($ivaLine > 0) {
            $expectedFromNet = round($lineCurrent * 0.13, 8);
            $expectedFromGross = round(($lineCurrent / 1.13) * 0.13, 8);
            $diffFromNet = abs($ivaLine - $expectedFromNet);
            $diffFromGross = abs($ivaLine - $expectedFromGross);

            if ($diffFromGross + 0.000001 < $diffFromNet) {
                return true;
            }
        }

        $precio = (float) ($product['precio'] ?? 0);
        $precioSinTributos = (float) ($product['precio_sin_tributos'] ?? 0);
        if ($precio <= 0 || $precioSinTributos <= 0 || abs($precio - $precioSinTributos) > 0.000001) {
            return false;
        }

        $cantidad = (float) ($product['cantidad'] ?? 0);
        if ($cantidad <= 0) {
            return false;
        }

        $lineFromPrice = $precio * $cantidad;
        if (abs($lineCurrent - $lineFromPrice) > 0.01) {
            return false;
        }

        if ($ivaLine <= 0) {
            return false;
        }

        $expectedFromGross = round(($lineCurrent / 1.13) * 0.13, 8);

        return abs($ivaLine - $expectedFromGross) <= 0.02;
    }

    private function recalculateContentTotals(array $content, string $documentType): array
    {
        $products = isset($content['products']) && is_array($content['products'])
            ? $content['products']
            : [];

        $totalVentasGravadas = array_sum(array_map(
            fn(array $product) => (float) ($product['ventas_gravadas'] ?? 0),
            $products
        ));
        $totalVentasExentas = array_sum(array_map(
            fn(array $product) => (float) ($product['ventas_exentas'] ?? 0),
            $products
        ));
        $totalVentasNoSujetas = array_sum(array_map(
            fn(array $product) => (float) ($product['ventas_no_sujetas'] ?? 0),
            $products
        ));

        $descuentoGravada = (float) ($content['descuento_venta_gravada'] ?? 0);
        $descuentoExenta = (float) ($content['descuento_venta_exenta'] ?? 0);
        $descuentoNoSujeta = (float) ($content['descuento_venta_no_sujeta'] ?? 0);
        $totalDescuentos = $descuentoGravada + $descuentoExenta + $descuentoNoSujeta;

        $totalVentasGravadasDescuento = max(0, $totalVentasGravadas - $descuentoGravada);
        $iva = round($totalVentasGravadasDescuento * 0.13, 8);

        $taxes = [
            'turismo_por_alojamiento',
            'turismo_salida_pais_via_aerea',
            'fovial',
            'contrans',
            'bebidas_alcoholicas',
            'tabaco_cigarillos',
            'tabaco_cigarros',
        ];

        $totalExtraTaxes = 0.0;
        foreach ($taxes as $taxKey) {
            $taxValue = round((float) ($content[$taxKey] ?? 0), 8);
            $content[$taxKey] = $taxValue;
            $totalExtraTaxes += $taxValue;
        }

        $subtotal = round($totalVentasGravadas + $totalVentasExentas + $totalVentasNoSujetas, 8);
        $totalTaxes = round($totalExtraTaxes + $iva, 8);

        $total = $subtotal;
        if (!in_array($documentType, ['14', '11', '07', '01'], true)) {
            $total = round($subtotal + $totalTaxes, 8);
        }

        $totalPagar = round($total - $totalDescuentos, 8);
        $montoAbonado = (float) ($content['monto_abonado'] ?? 0);

        $content['total_ventas_gravadas'] = round($totalVentasGravadas, 8);
        $content['total_ventas_exentas'] = round($totalVentasExentas, 8);
        $content['total_ventas_no_sujetas'] = round($totalVentasNoSujetas, 8);
        $content['total_ventas_gravadas_descuento'] = round($totalVentasGravadasDescuento, 8);
        $content['subtotal'] = $subtotal;
        $content['iva'] = $iva;
        $content['total_taxes'] = $totalTaxes;
        $content['total_descuentos'] = round($totalDescuentos, 8);
        $content['total'] = $total;
        $content['total_pagar'] = $totalPagar;
        $content['monto_pendiente'] = round($totalPagar - $montoAbonado, 8);

        return $content;
    }

    private function attachQuotationObservation(array $content, Quotation $quotation): array
    {
        $name = trim((string) ($quotation->name ?? ''));
        $legend = 'Cotización - ' . ($name !== '' ? $name : ('#' . $quotation->id));

        $currentObservation = trim((string) ($content['extension']['observaciones'] ?? ''));
        if ($currentObservation === '') {
            $content['extension']['observaciones'] = $legend;
            return $content;
        }

        if (mb_stripos($currentObservation, $legend) !== false) {
            return $content;
        }

        $content['extension']['observaciones'] = $legend . "\n" . $currentObservation;
        return $content;
    }

    private function findQuotation(string $id): Quotation
    {
        return Quotation::where('business_id', session('business'))->findOrFail($id);
    }

    private function resolveLogo(string $nit, array $companyData = []): ?string
    {
        $baseUrl = rtrim((string) env('OCTOPUS_API_URL'), '/');
        if ($baseUrl === '') {
            return $this->localLogoFallback();
        }

        $response = Http::timeout(20)->get($baseUrl . '/datos_empresa/nit/' . $nit . '/logo');

        if (!$response->successful()) {
            // Try reading logo hints from /datos_empresa payload before using local fallback.
            $fromCompanyPayload = $this->resolveLogoFromValue($this->extractLogoValueFromPayload($companyData), $baseUrl);
            return $fromCompanyPayload ?? $this->localLogoFallback();
        }

        $contentType = $response->header('Content-Type');
        if ($contentType && str_contains($contentType, 'image/')) {
            return 'data:' . $contentType . ';base64,' . base64_encode($response->body());
        }

        $json = $response->json();
        $logoValue = $this->extractLogoValueFromPayload(is_array($json) ? $json : ['value' => $json]);

        $resolved = $this->resolveLogoFromValue($logoValue, $baseUrl)
            ?? $this->resolveLogoFromValue($this->extractLogoValueFromPayload($companyData), $baseUrl)
            ?? $this->localLogoFallback();

        return $resolved;
    }

    private function extractLogoValueFromPayload(array $payload): ?string
    {
        $candidates = [
            $payload['url'] ?? null,
            $payload['logo'] ?? null,
            $payload['image'] ?? null,
            $payload['base64'] ?? null,
            $payload['value'] ?? null,
            $payload['data']['url'] ?? null,
            $payload['data']['logo'] ?? null,
            $payload['data']['image'] ?? null,
            $payload['data']['base64'] ?? null,
            $payload['data']['logo_url'] ?? null,
            $payload['data']['logoUrl'] ?? null,
            $payload['data']['empresa']['logo'] ?? null,
            $payload['data']['empresa']['logo_url'] ?? null,
            $payload['data']['empresa']['logoUrl'] ?? null,
            $payload['logo_url'] ?? null,
            $payload['logoUrl'] ?? null,
        ];

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && trim($candidate) !== '') {
                return trim($candidate);
            }
        }

        return null;
    }

    private function resolveLogoFromValue(?string $logoValue, string $baseUrl): ?string
    {
        if (!is_string($logoValue) || trim($logoValue) === '') {
            return null;
        }

        $logoValue = trim($logoValue);

        if (str_starts_with($logoValue, 'data:image/')) {
            return $logoValue;
        }

        if ($this->looksLikeBase64($logoValue)) {
            return 'data:image/png;base64,' . $logoValue;
        }

        $logoUrl = $logoValue;
        if (str_starts_with($logoUrl, '//')) {
            $logoUrl = 'https:' . $logoUrl;
        } elseif (str_starts_with($logoUrl, '/')) {
            $logoUrl = $baseUrl . $logoUrl;
        }

        if (!filter_var($logoUrl, FILTER_VALIDATE_URL)) {
            return null;
        }

        $logoResponse = Http::timeout(20)->get($logoUrl);
        if (!$logoResponse->successful()) {
            return null;
        }

        $logoContentType = $logoResponse->header('Content-Type') ?? 'image/png';
        if (!str_contains($logoContentType, 'image/')) {
            return null;
        }

        return 'data:' . $logoContentType . ';base64,' . base64_encode($logoResponse->body());
    }

    private function localLogoFallback(): ?string
    {
        $fallbackPath = public_path('images/logo.png');
        if (!is_file($fallbackPath)) {
            return null;
        }

        $bytes = @file_get_contents($fallbackPath);
        if ($bytes === false) {
            return null;
        }

        return 'data:image/png;base64,' . base64_encode($bytes);
    }

    private function looksLikeBase64(string $value): bool
    {
        if ($value === '' || preg_match('/\s/', $value)) {
            return false;
        }

        if (!preg_match('/^[A-Za-z0-9+\/=]+$/', $value)) {
            return false;
        }

        return (bool) base64_decode($value, true);
    }

    private function canAccessProfitabilityReport(Business $business): bool
    {
        return (bool) ($business->enable_product_costs ?? false);
    }
}
