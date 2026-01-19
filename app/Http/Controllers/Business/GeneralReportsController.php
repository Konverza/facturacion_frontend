<?php

namespace App\Http\Controllers\Business;

use App\Exports\Business\GeneralReportExport;
use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessProduct;
use App\Models\PuntoVenta;
use App\Models\Sucursal;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class GeneralReportsController extends Controller
{
    public function index()
    {
        $business_id = Session::get('business') ?? null;
        $business = Business::find($business_id);
        $sucursales = Sucursal::where('business_id', $business_id)->with('puntosVentas')->get();
        $products = BusinessProduct::where('business_id', $business_id)
            ->orderBy('descripcion')
            ->get(['id', 'codigo', 'descripcion']);

        $product_options = ['' => 'Todos'];
        foreach ($products as $product) {
            $label = trim(($product->codigo ? $product->codigo . ' - ' : '') . $product->descripcion);
            $product_options[(string) $product->id] = $label;
        }

        $sucursal_options = $sucursales->pluck('nombre', 'codSucursal')->toArray();
        $sucursal_options = array_merge(['' => 'Todas'], $sucursal_options);

        $punto_venta_options = [];
        foreach ($sucursales as $sucursal) {
            foreach ($sucursal->puntosVentas as $puntoVenta) {
                $punto_venta_options[$puntoVenta->codPuntoVenta] = "{$sucursal->nombre} - {$puntoVenta->nombre}";
            }
        }
        $punto_venta_options = array_merge(['' => 'Todos'], $punto_venta_options);

        return view('business.reporting.general-reports', [
            'business' => $business,
            'sucursal_options' => $sucursal_options,
            'punto_venta_options' => $punto_venta_options,
            'product_options' => $product_options,
        ]);
    }

    public function generate(Request $request)
    {
        $business_id = Session::get('business') ?? null;
        $business = Business::find($business_id);

        $request->validate([
            'report_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'format' => 'required|in:pdf,excel',
        ]);

        if (in_array($request->report_type, ['ventas_punto_venta']) && !$request->codPuntoVenta) {
            return back()->withErrors(['codPuntoVenta' => 'Debe seleccionar un punto de venta.']);
        }

        if (in_array($request->report_type, ['ventas_sucursal']) && !$request->codSucursal) {
            return back()->withErrors(['codSucursal' => 'Debe seleccionar una sucursal.']);
        }

        if (in_array($request->report_type, ['ventas_producto_especifico']) && !$request->producto) {
            return back()->withErrors(['producto' => 'Debe seleccionar un producto.']);
        }

        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->endOfDay();

        $parameters = [
            'nit' => $business->nit ?? null,
            'emisionInicio' => $start->format('Y-m-d\TH:i:s'),
            'emisionFin' => $end->format('Y-m-d\TH:i:s'),
            'codSucursal' => $request->codSucursal ?: null,
            'codPuntoVenta' => $request->codPuntoVenta ?: null,
            'estado' => 'PROCESADO',
        ];

        $dtes = $this->fetchDtes($parameters);

        $sucursal = null;
        $puntoVenta = null;
        if ($request->codSucursal) {
            $sucursal = Sucursal::where('codSucursal', $request->codSucursal)->first();
        }
        if ($request->codPuntoVenta) {
            $puntoVenta = PuntoVenta::where('codPuntoVenta', $request->codPuntoVenta)->first();
        }

        $filters = $this->buildFilters($request, $sucursal, $puntoVenta);

        [$headers, $rows, $totals, $sections] = match ($request->report_type) {
            'ventas_globales' => $this->reportVentasGlobales($dtes, $start, $end),
            'ventas_punto_venta' => $this->reportVentasPuntoVenta($dtes, $start, $end),
            'ventas_sucursal' => $this->reportVentasSucursal($dtes, $start, $end),
            'ventas_productos_periodo' => $this->reportVentasProductosPeriodo($dtes),
            'ventas_producto_especifico' => $this->reportVentasProductoEspecifico($dtes, $start, $end, (string) $request->producto),
            'ventas_credito' => $this->reportVentasCondicion($dtes, $start, $end, 2),
            'ventas_contado' => $this->reportVentasCondicion($dtes, $start, $end, 1),
            default => $this->reportVentasGlobales($dtes, $start, $end),
        };

        $title = $this->getReportTitle($request->report_type);

        if ($request->input('format') === 'excel') {
            $fileName = 'reporte_' . $request->report_type . '_' . now()->format('Ymd_His') . '.xlsx';
            return Excel::download(new GeneralReportExport($rows, $headers), $fileName);
        }

        $pdf = Pdf::loadView('business.reporting.pdf.general-report', [
            'business' => $business,
            'title' => $title,
            'filters' => $filters,
            'headers' => $headers,
            'rows' => $rows,
            'totals' => $totals,
            'sections' => $sections,
        ]);

        return $pdf->stream('reporte_' . $request->report_type . '_' . now()->format('Ymd_His') . '.pdf');
    }

    private function fetchDtes(array $parameters): array
    {
        $page = 1;
        $limit = 500;
        $items = [];

        do {
            $response = Http::get(env('OCTOPUS_API_URL') . '/dtes/', array_merge($parameters, [
                'page' => $page,
                'limit' => $limit,
                'sort' => 'asc',
            ]));

            $data = $response->json() ?? [];
            $batch = $data['items'] ?? [];

            foreach ($batch as $dte) {
                $dte['documento'] = $this->normalizeDocumento($dte['documento'] ?? null);
                $items[] = $dte;
            }

            $totalPages = $data['total_pages'] ?? $page;
            $page++;
        } while ($page <= $totalPages);

        return $items;
    }

    private function normalizeDocumento($documento)
    {
        if (is_null($documento)) {
            return null;
        }
        if (is_string($documento)) {
            $decoded = json_decode($documento, true);
            return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
        }
        if (is_object($documento)) {
            return json_decode(json_encode($documento), true);
        }
        if (is_array($documento)) {
            return $documento;
        }
        return null;
    }

    private function buildFilters(Request $request, ?Sucursal $sucursal, ?PuntoVenta $puntoVenta): array
    {
        $filters = [
            'Período' => Carbon::parse($request->start_date)->format('d/m/Y') . ' - ' . Carbon::parse($request->end_date)->format('d/m/Y'),
        ];

        if ($request->codSucursal && $sucursal) {
            $filters['Sucursal'] = $sucursal->nombre;
        }

        if ($request->codPuntoVenta && $puntoVenta) {
            $filters['Punto de venta'] = $puntoVenta->nombre;
        }

        if ($request->producto) {
            $product = BusinessProduct::where('id', $request->producto)->first();
            $filters['Producto'] = $product ? trim(($product->codigo ? $product->codigo . ' - ' : '') . $product->descripcion) : $request->producto;
        }

        $filters['Estado'] = 'PROCESADO';
        $filters['Tipo DTE'] = '01, 03, 05';

        $filters['Generado'] = now()->format('d/m/Y H:i');

        return $filters;
    }

    private function getReportTitle(string $type): string
    {
        return match ($type) {
            'ventas_globales' => 'Reporte de ventas globales',
            'ventas_punto_venta' => 'Reporte de ventas por punto de venta',
            'ventas_sucursal' => 'Reporte de ventas por sucursal',
            'ventas_productos_periodo' => 'Reporte de ventas de productos por período',
            'ventas_producto_especifico' => 'Reporte de ventas de producto específico',
            'ventas_credito' => 'Reporte de ventas al crédito',
            'ventas_contado' => 'Reporte de ventas al contado',
            default => 'Reporte general',
        };
    }

    private function reportVentasGlobales(array $dtes, Carbon $start, Carbon $end): array
    {
        $daily = $this->initDailyTotals($start, $end);

        foreach ($dtes as $dte) {
            $tipo = $dte['tipo_dte'] ?? data_get($dte, 'documento.identificacion.tipoDte');
            if ($this->isExcludedForSales($tipo) || !$this->isIncludedSalesType($tipo)) {
                continue;
            }

            $dateKey = $this->getDateKey($dte);
            if (!$dateKey || !isset($daily[$dateKey])) {
                continue;
            }

            $monto = $this->getMontoTotal(data_get($dte, 'documento.resumen', []), $tipo);
            $monto *= $this->getSalesMultiplier($tipo);

            $daily[$dateKey]['cantidad'] += 1;
            $daily[$dateKey]['total'] += $monto;
        }

        return $this->buildDailyReport($daily);
    }

    private function reportVentasPuntoVenta(array $dtes, Carbon $start, Carbon $end): array
    {
        return $this->reportVentasGlobales($dtes, $start, $end);
    }

    private function reportVentasSucursal(array $dtes, Carbon $start, Carbon $end): array
    {
        $daily = $this->initDailyTotals($start, $end);
        $sections = [];

        foreach ($dtes as $dte) {
            $tipo = $dte['tipo_dte'] ?? data_get($dte, 'documento.identificacion.tipoDte');
            if ($this->isExcludedForSales($tipo) || !$this->isIncludedSalesType($tipo)) {
                continue;
            }

            $dateKey = $this->getDateKey($dte);
            if (!$dateKey || !isset($daily[$dateKey])) {
                continue;
            }

            $puntoVenta = $dte['codPuntoVenta'] ?? 'Sin punto de venta';
            $monto = $this->getMontoTotal(data_get($dte, 'documento.resumen', []), $tipo);
            $monto *= $this->getSalesMultiplier($tipo);

            $daily[$dateKey]['cantidad'] += 1;
            $daily[$dateKey]['total'] += $monto;

            if (!isset($sections[$puntoVenta])) {
                $sections[$puntoVenta] = $this->initDailyTotals($start, $end);
            }

            $sections[$puntoVenta][$dateKey]['cantidad'] += 1;
            $sections[$puntoVenta][$dateKey]['total'] += $monto;
        }

        $headers = ['Fecha', 'Documentos', 'Ventas'];
        $rows = $this->dailyRows($daily);
        $totals = empty($rows) ? [] : $this->dailyTotals($daily);

        $sectionData = [];
        foreach ($sections as $puntoVenta => $data) {
            $sectionRows = $this->dailyRows($data);
            if (empty($sectionRows)) {
                continue;
            }
            $sectionData[] = [
                'title' => $puntoVenta,
                'headers' => $headers,
                'rows' => $sectionRows,
                'totals' => $this->dailyTotals($data),
            ];
        }

        return [$headers, $rows, $totals, $sectionData];
    }

    private function reportVentasProductosPeriodo(array $dtes): array
    {
        $products = [];

        foreach ($dtes as $dte) {
            $tipo = $dte['tipo_dte'] ?? data_get($dte, 'documento.identificacion.tipoDte');
            if ($this->isExcludedForSales($tipo) || !$this->isIncludedSalesType($tipo)) {
                continue;
            }

            $multiplier = $this->getSalesMultiplier($tipo);
            $cuerpo = data_get($dte, 'documento.cuerpoDocumento', []);

            if (!is_array($cuerpo)) {
                continue;
            }

            foreach ($cuerpo as $item) {
                if (is_object($item)) {
                    $item = json_decode(json_encode($item), true);
                }
                if (!is_array($item)) {
                    continue;
                }
                $codigo = Arr::get($item, 'codigo', 'N/D');
                $descripcion = Arr::get($item, 'descripcion', 'Sin descripción');
                $key = $codigo . '|' . $descripcion;

                if (!isset($products[$key])) {
                    $products[$key] = [
                        'codigo' => $codigo,
                        'descripcion' => $descripcion,
                        'cantidad' => 0,
                        'total' => 0,
                    ];
                }

                $products[$key]['cantidad'] += (float) Arr::get($item, 'cantidad', 1);
                $products[$key]['total'] += $this->getMontoItem($item) * $multiplier;
            }
        }

        $products = collect($products)->sortByDesc('total')->values()->all();

        $headers = ['Código', 'Descripción', 'Cantidad', 'Ventas'];
        $rows = array_map(function ($row) {
            return [
                $row['codigo'],
                $row['descripcion'],
                number_format($row['cantidad'], 2, '.', ','),
                $this->formatMoney($row['total']),
            ];
        }, $products);

        $totals = [
            'label' => 'TOTAL',
            'cantidad' => collect($products)->sum('cantidad'),
            'total' => collect($products)->sum('total'),
        ];

        return [$headers, $rows, $totals, []];
    }

    private function reportVentasProductoEspecifico(array $dtes, Carbon $start, Carbon $end, string $producto): array
    {
        $producto = trim($producto);
        $productModel = BusinessProduct::where('id', $producto)->first();
        $codigoMatch = mb_strtolower((string) ($productModel->codigo ?? ''));
        $descripcionMatch = mb_strtolower((string) ($productModel->descripcion ?? $producto));
        $daily = $this->initDailyTotals($start, $end, true);

        foreach ($dtes as $dte) {
            $tipo = $dte['tipo_dte'] ?? data_get($dte, 'documento.identificacion.tipoDte');
            if ($this->isExcludedForSales($tipo) || !$this->isIncludedSalesType($tipo)) {
                continue;
            }

            $dateKey = $this->getDateKey($dte);
            if (!$dateKey || !isset($daily[$dateKey])) {
                continue;
            }

            $multiplier = $this->getSalesMultiplier($tipo);
            $cuerpo = data_get($dte, 'documento.cuerpoDocumento', []);
            if (!is_array($cuerpo)) {
                continue;
            }

            foreach ($cuerpo as $item) {
                if (is_object($item)) {
                    $item = json_decode(json_encode($item), true);
                }
                if (!is_array($item)) {
                    continue;
                }

                $codigo = mb_strtolower((string) Arr::get($item, 'codigo', ''));
                $descripcion = mb_strtolower((string) Arr::get($item, 'descripcion', ''));
                if ($producto === '') {
                    continue;
                }
                if ($codigoMatch && !str_contains($codigo, $codigoMatch) && !str_contains($descripcion, $descripcionMatch)) {
                    continue;
                }
                if (!$codigoMatch && !str_contains($codigo, $descripcionMatch) && !str_contains($descripcion, $descripcionMatch)) {
                    continue;
                }

                $daily[$dateKey]['cantidad'] += (float) Arr::get($item, 'cantidad', 1);
                $daily[$dateKey]['total'] += $this->getMontoItem($item) * $multiplier;
            }
        }

        $headers = ['Fecha', 'Cantidad', 'Ventas'];
        $rows = array_map(function ($row) {
            return [
                $row['fecha'],
                number_format($row['cantidad'], 2, '.', ','),
                $this->formatMoney($row['total']),
            ];
        }, $daily);

        $totals = [
            'label' => 'TOTAL',
            'cantidad' => collect($daily)->sum('cantidad'),
            'total' => collect($daily)->sum('total'),
        ];

        return [$headers, $rows, $totals, []];
    }

    private function reportVentasCondicion(array $dtes, Carbon $start, Carbon $end, int $condicion): array
    {
        $daily = $this->initDailyTotals($start, $end);

        foreach ($dtes as $dte) {
            $tipo = $dte['tipo_dte'] ?? data_get($dte, 'documento.identificacion.tipoDte');
            if ($this->isExcludedForSales($tipo) || !$this->isIncludedSalesType($tipo)) {
                continue;
            }

            $resumen = data_get($dte, 'documento.resumen', []);
            $condicionOperacion = (int) data_get($resumen, 'condicionOperacion', 0);
            if ($condicionOperacion !== $condicion) {
                continue;
            }

            $dateKey = $this->getDateKey($dte);
            if (!$dateKey || !isset($daily[$dateKey])) {
                continue;
            }

            $monto = $this->getMontoTotal($resumen, $tipo) * $this->getSalesMultiplier($tipo);
            $daily[$dateKey]['cantidad'] += 1;
            $daily[$dateKey]['total'] += $monto;
        }

        return $this->buildDailyReport($daily);
    }

    private function buildDailyReport(array $daily): array
    {
        $headers = ['Fecha', 'Documentos', 'Ventas'];
        $rows = $this->dailyRows($daily);
        $totals = empty($rows) ? [] : $this->dailyTotals($daily);

        return [$headers, $rows, $totals, []];
    }

    private function initDailyTotals(Carbon $start, Carbon $end, bool $includeCantidadProducto = false): array
    {
        $daily = [];
        foreach (CarbonPeriod::create($start, $end) as $date) {
            $key = $date->format('Y-m-d');
            $daily[$key] = [
                'fecha' => $date->format('d/m/Y'),
                'cantidad' => 0,
                'total' => 0,
            ];
        }
        return $daily;
    }

    private function dailyRows(array $daily): array
    {
        $filtered = array_filter($daily, function ($row) {
            return (float) $row['cantidad'] !== 0.0 || (float) $row['total'] !== 0.0;
        });

        return array_map(function ($row) {
            return [
                $row['fecha'],
                $row['cantidad'],
                $this->formatMoney($row['total']),
            ];
        }, $filtered);
    }

    private function dailyTotals(array $daily): array
    {
        return [
            'label' => 'TOTAL',
            'cantidad' => collect($daily)->sum('cantidad'),
            'total' => collect($daily)->sum('total'),
        ];
    }

    private function getDateKey(array $dte): ?string
    {
        $date = data_get($dte, 'documento.identificacion.fecEmi');
        if (!$date) {
            $date = data_get($dte, 'fhEmision');
        }
        if (!$date) {
            return null;
        }

        return Carbon::parse($date)->format('Y-m-d');
    }

    private function isExcludedForSales(?string $tipo): bool
    {
        return $tipo === '14';
    }

    private function isIncludedSalesType(?string $tipo): bool
    {
        return in_array($tipo, ['01', '03', '05'], true);
    }

    private function getSalesMultiplier(?string $tipo): int
    {
        return $tipo === '05' ? -1 : 1;
    }

    private function getMontoTotal(array $resumen = null, $tipo = null): float
    {
        if (!$resumen) {
            return 0;
        }

        $total_pagar = data_get($resumen, 'totalPagar');
        if (!is_null($total_pagar)) {
            return (float) $total_pagar;
        }

        $monto_total_operacion = data_get($resumen, 'montoTotalOperacion');
        if (!is_null($monto_total_operacion)) {
            return (float) $monto_total_operacion;
        }

        $total = data_get($resumen, 'total');
        if (!is_null($total)) {
            return (float) $total;
        }

        $total_compra = data_get($resumen, 'totalCompra');
        if (!is_null($total_compra)) {
            return (float) $total_compra;
        }

        $valor_total = data_get($resumen, 'valorTotal');
        if (!is_null($valor_total)) {
            return (float) $valor_total;
        }

        $total_gravada = (float) data_get($resumen, 'totalGravada', 0);
        $total_exenta = (float) data_get($resumen, 'totalExenta', 0);
        $total_no_suj = (float) data_get($resumen, 'totalNoSuj', 0);

        return $total_gravada + $total_exenta + $total_no_suj;
    }

    private function getMontoItem(array $item): float
    {
        $venta_gravada = (float) Arr::get($item, 'ventaGravada', 0);
        $venta_exenta = (float) Arr::get($item, 'ventaExenta', 0);
        $venta_no_suj = (float) Arr::get($item, 'ventaNoSuj', 0);

        if ($venta_gravada + $venta_exenta + $venta_no_suj > 0) {
            return $venta_gravada + $venta_exenta + $venta_no_suj;
        }

        $compra = Arr::get($item, 'compra');
        if (!is_null($compra)) {
            return (float) $compra;
        }

        $valor = Arr::get($item, 'valor');
        if (!is_null($valor)) {
            return (float) $valor;
        }

        return (float) Arr::get($item, 'precioUni', 0) * (float) Arr::get($item, 'cantidad', 1);
    }

    private function formatMoney($value): string
    {
        return '$' . number_format((float) $value, 2, '.', ',');
    }
}
