<?php

namespace App\Livewire\Business\Reports;

use App\Models\Business;
use App\Models\BusinessPlan;
use App\Models\BusinessReportFilter;
use App\Models\BusinessUser;
use App\Models\PuntoVenta;
use App\Models\Sucursal;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class GeneralDashboard extends Component
{
    public $nit;
    public $fechaInicio;
    public $fechaFin;
    public $emisionInicio;
    public $emisionFin;
    public $codSucursal;
    public $codPuntoVenta;
    public $tipo_dte;
    public $estado;
    public $sort = 'desc';
    public $limit = 500;
    public $documento_receptor;
    public $q;
    public $condicion_operacion;
    public $minMonto;
    public $maxMonto;
    public $periodo;
    public $saved_filters = [];
    public $filter_name;
    public $selected_filter;
    public $filter_error;

    public $receptores_unicos = [
        '' => 'Todos',
    ];

    public $types = [
        '' => 'Todos',
        '01' => 'Factura Consumidor Final',
        '03' => 'Comprobante de crédito fiscal',
        '04' => 'Nota de Remisión',
        '05' => 'Nota de crédito',
        '06' => 'Nota de débito',
        '07' => 'Comprobante de retención',
        '08' => 'Comprobante de liquidación',
        '09' => 'Documento Contable de Liquidación',
        '11' => 'Factura de exportación',
        '14' => 'Factura de sujeto excluido',
        '15' => 'Comprobante de Donación',
    ];

    public $dashboard = [];
    public $dtes = [];
    public $total_registros = 0;
    public $total_mostrados = 0;

    public function mount()
    {
        if (Session::has('sucursal')) {
            $sucursal_id = Session::get('sucursal');
            $sucursal = Sucursal::find($sucursal_id);
            $this->codSucursal = $sucursal->codSucursal ?? null;
            $this->codPuntoVenta = $sucursal->puntosVentas->first()->codPuntoVenta ?? null;
        } else {
            $this->codSucursal = null;
            $this->codPuntoVenta = null;
        }

        $this->periodo = 'mes_actual';
        $this->aplicarPeriodoRapido();
    }

    public function updatedPeriodo()
    {
        $this->aplicarPeriodoRapido();
    }

    public function clearFilters()
    {
        $this->reset([
            'fechaInicio',
            'fechaFin',
            'emisionInicio',
            'emisionFin',
            'codSucursal',
            'codPuntoVenta',
            'tipo_dte',
            'estado',
            'sort',
            'limit',
            'documento_receptor',
            'q',
            'condicion_operacion',
            'minMonto',
            'maxMonto',
            'periodo',
        ]);

        $this->limit = 500;
        $this->periodo = 'mes_actual';
        $this->aplicarPeriodoRapido();
        $this->filter_error = null;
    }

    public function saveFilter()
    {
        $name = trim((string) $this->filter_name);
        if ($name === '') {
            return;
        }

        $business_id = Session::get('business') ?? null;
        if (!$business_id || !auth()->user()) {
            return;
        }

        $exists = BusinessReportFilter::where('user_id', auth()->id())
            ->where('business_id', $business_id)
            ->where('name', $name)
            ->exists();

        if ($exists) {
            $this->filter_error = 'Ya existe un filtro con ese nombre. Usa otro nombre.';
            return;
        }

        BusinessReportFilter::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'business_id' => $business_id,
                'name' => $name,
            ],
            [
                'filters' => $this->getFilterState(),
            ]
        );

        $this->filter_error = null;
        $this->selected_filter = $name;
    }

    public function loadFilter()
    {
        $name = (string) $this->selected_filter;
        $business_id = Session::get('business') ?? null;
        if (!$business_id || !auth()->user()) {
            return;
        }

        $filters = $this->saved_filters[$name] ?? null;
        if (!$filters) {
            $record = BusinessReportFilter::where('user_id', auth()->id())
                ->where('business_id', $business_id)
                ->where('name', $name)
                ->first();
            $filters = $record?->filters;
        }

        if (!is_array($filters)) {
            return;
        }

        foreach ($filters as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }

        $this->filter_error = null;
    }

    public function deleteFilter()
    {
        $name = (string) $this->selected_filter;
        $business_id = Session::get('business') ?? null;
        if (!$business_id || !auth()->user()) {
            return;
        }

        BusinessReportFilter::where('user_id', auth()->id())
            ->where('business_id', $business_id)
            ->where('name', $name)
            ->delete();

        $this->selected_filter = null;
        $this->filter_error = null;
    }

    public function render()
    {
        $business_id = Session::get('business') ?? null;
        $business = Business::find($business_id);
        $business_user = BusinessUser::where('user_id', auth()->user()->id)->first();
        $this->nit = $business->nit ?? null;

        if ($business_user?->only_default_pos) {
            $puntoVenta = PuntoVenta::find($business_user->default_pos_id);
            $this->codSucursal = $puntoVenta?->sucursal?->codSucursal;
            $this->codPuntoVenta = $puntoVenta?->codPuntoVenta;
        }

        if (auth()->user()?->only_fcf) {
            $this->tipo_dte = '01';
        }

        $parameters = [
            'nit' => $this->nit,
            'fechaInicio' => $this->fechaInicio ? "{$this->fechaInicio}T00:00:00" : null,
            'fechaFin' => $this->fechaFin ? "{$this->fechaFin}T23:59:59" : null,
            'emisionInicio' => $this->emisionInicio ? "{$this->emisionInicio}T00:00:00" : null,
            'emisionFin' => $this->emisionFin ? "{$this->emisionFin}T23:59:59" : null,
            'codSucursal' => $this->codSucursal,
            'codPuntoVenta' => $this->codPuntoVenta,
            'tipo_dte' => $this->tipo_dte,
            'estado' => $this->estado,
            'documento_receptor' => $this->documento_receptor,
            'q' => $this->q,
        ];

        $business_plan = BusinessPlan::where('nit', $business->nit ?? null)->first();
        $plan_dtes = json_decode($business_plan?->dtes ?? '[]') ?? [];
        foreach ($this->types as $dte_key => $dte_value) {
            if ($dte_key !== '' && !in_array($dte_key, $plan_dtes)) {
                unset($this->types[$dte_key]);
            }
        }
        $dte_options = array_merge(['' => 'Todos'], $this->types);

        $response_dtes = Http::get(env('OCTOPUS_API_URL') . '/dtes/', array_merge($parameters, [
            'page' => 1,
            'limit' => $this->limit,
            'sort' => $this->sort,
        ]));
        $data = $response_dtes->json() ?? [];
        $items = $data['items'] ?? [];
        $this->total_registros = $data['total'] ?? count($items);

        $this->dtes = array_map(function ($dte) {
            $documento = $dte['documento'] ?? null;
            $dte['documento'] = $this->normalizeDocumento($documento);
            return $dte;
        }, $items);
        $this->total_mostrados = count($this->dtes);

        $response_receptores = Http::get(env('OCTOPUS_API_URL') . "/dtes/receptor-list/{$this->nit}");
        $receptores = $response_receptores->json() ?? [];
        $this->receptores_unicos = [];
        foreach ($receptores as $receptor) {
            if (isset($receptor['documento_receptor'], $receptor['nombre_receptor'])) {
                $this->receptores_unicos[$receptor['documento_receptor']] = $receptor['nombre_receptor'];
            }
        }
        $this->receptores_unicos = array_merge(['' => 'Todos'], $this->receptores_unicos);

        $sucursales = Sucursal::where('business_id', $business_id)
            ->with('puntosVentas')
            ->get();
        $sucursal_options = $sucursales->pluck('nombre', 'codSucursal')->toArray();
        $sucursal_options = array_merge(['' => 'Todas'], $sucursal_options);
        $punto_venta_options = [];
        foreach ($sucursales as $sucursal) {
            foreach ($sucursal->puntosVentas as $puntoVenta) {
                $punto_venta_options[$puntoVenta->codPuntoVenta] = "{$sucursal->nombre} - {$puntoVenta->nombre}";
            }
        }
        $punto_venta_options = array_merge(['' => 'Todos'], $punto_venta_options);

        $this->saved_filters = BusinessReportFilter::where('user_id', auth()->id())
            ->where('business_id', $business_id)
            ->orderBy('name')
            ->get()
            ->pluck('filters', 'name')
            ->toArray();

        $saved_filter_options = ['' => 'Selecciona un filtro'];
        foreach ($this->saved_filters as $name => $filters) {
            $saved_filter_options[$name] = $name;
        }

        $this->dashboard = $this->buildDashboard($this->dtes, $sucursal_options, $punto_venta_options);

        return view('livewire.business.reports.general-dashboard', [
            'dtes' => $this->dtes,
            'dte_options' => $dte_options,
            'sucursal_options' => $sucursal_options,
            'punto_venta_options' => $punto_venta_options,
            'saved_filter_options' => $saved_filter_options,
            'only_default_pos' => $business_user?->only_default_pos ?? false,
            'only_fcf' => auth()->user()?->only_fcf ?? false,
        ]);
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

    private function aplicarPeriodoRapido()
    {
        $now = Carbon::now();

        switch ($this->periodo) {
            case 'hoy':
                $this->emisionInicio = $now->format('Y-m-d');
                $this->emisionFin = $now->format('Y-m-d');
                break;
            case 'ayer':
                $ayer = $now->copy()->subDay();
                $this->emisionInicio = $ayer->format('Y-m-d');
                $this->emisionFin = $ayer->format('Y-m-d');
                break;
            case 'semana_actual':
                $this->emisionInicio = $now->copy()->startOfWeek()->format('Y-m-d');
                $this->emisionFin = $now->copy()->endOfWeek()->format('Y-m-d');
                break;
            case 'mes_actual':
                $this->emisionInicio = $now->copy()->startOfMonth()->format('Y-m-d');
                $this->emisionFin = $now->copy()->endOfMonth()->format('Y-m-d');
                break;
            case 'mes_anterior':
                $prev = $now->copy()->subMonth();
                $this->emisionInicio = $prev->copy()->startOfMonth()->format('Y-m-d');
                $this->emisionFin = $prev->copy()->endOfMonth()->format('Y-m-d');
                break;
            default:
                break;
        }
    }

    private function buildDashboard(array $dtes, array $sucursal_options, array $punto_venta_options): array
    {
        $stats = [
            'total_ventas' => 0,
            'total_documentos' => 0,
            'ticket_promedio' => 0,
            'clientes_unicos' => 0,
            'productos_unicos' => 0,
            'ventas_gravadas' => 0,
            'ventas_exentas' => 0,
            'ventas_no_sujetas' => 0,
            'compras_sujeto_excluido' => 0,
            'total_contado' => 0,
            'total_credito' => 0,
            'total_otro' => 0,
            'total_procesados' => 0,
            'total_rechazados' => 0,
            'total_anulados' => 0,
        ];

        $ventas_por_tipo = [];
        $ventas_por_sucursal = [];
        $ventas_por_punto = [];
        $ventas_por_producto = [];
        $ventas_por_cliente = [];
        $ventas_por_estado = [];
        $ventas_por_condicion = [
            1 => ['total' => 0, 'cantidad' => 0],
            2 => ['total' => 0, 'cantidad' => 0],
            3 => ['total' => 0, 'cantidad' => 0],
        ];

        $clientes = [];
        $productos = [];

        foreach ($dtes as $dte) {
            $estado = $dte['estado'] ?? null;
            $tipo = $dte['tipo_dte'] ?? data_get($dte, 'documento.identificacion.tipoDte');
            $documento = $dte['documento'] ?? null;
            $resumen = data_get($documento, 'resumen', []);
            $condicion = data_get($resumen, 'condicionOperacion');
            $monto = $this->getMontoTotal($resumen, $tipo);

            if (!is_null($this->condicion_operacion) && (string) $condicion !== (string) $this->condicion_operacion) {
                continue;
            }
            if (!is_null($this->minMonto) && $monto < (float) $this->minMonto) {
                continue;
            }
            if (!is_null($this->maxMonto) && $monto > (float) $this->maxMonto) {
                continue;
            }

            $ventas_por_estado[$estado] = ($ventas_por_estado[$estado] ?? 0) + $monto;

            if ($estado === 'ANULADO') {
                $stats['total_anulados'] += $monto;
                continue;
            }
            if ($estado === 'RECHAZADO') {
                $stats['total_rechazados'] += $monto;
                continue;
            }

            if ($tipo === '14') {
                $stats['compras_sujeto_excluido'] += $monto;
                continue;
            }

            $multiplier = $tipo === '05' ? -1 : 1;
            $monto_venta = $monto * $multiplier;

            $stats['total_procesados'] += $monto_venta;
            $stats['total_documentos'] += 1;
            $stats['total_ventas'] += $monto_venta;

            $stats['ventas_gravadas'] += (float) data_get($resumen, 'totalGravada', 0) * $multiplier;
            $stats['ventas_exentas'] += (float) data_get($resumen, 'totalExenta', 0) * $multiplier;
            $stats['ventas_no_sujetas'] += (float) data_get($resumen, 'totalNoSuj', 0) * $multiplier;

            if (isset($ventas_por_condicion[$condicion])) {
                $ventas_por_condicion[$condicion]['total'] += $monto_venta;
                $ventas_por_condicion[$condicion]['cantidad'] += 1;
            }

            $codSucursal = $dte['codSucursal'] ?? null;
            $codPuntoVenta = $dte['codPuntoVenta'] ?? null;

            if ($codSucursal) {
                if (!isset($ventas_por_sucursal[$codSucursal])) {
                    $ventas_por_sucursal[$codSucursal] = [
                        'codigo' => $codSucursal,
                        'nombre' => $sucursal_options[$codSucursal] ?? $codSucursal,
                        'total' => 0,
                        'cantidad' => 0,
                    ];
                }
                $ventas_por_sucursal[$codSucursal]['total'] += $monto_venta;
                $ventas_por_sucursal[$codSucursal]['cantidad'] += 1;
            }

            if ($codPuntoVenta) {
                if (!isset($ventas_por_punto[$codPuntoVenta])) {
                    $ventas_por_punto[$codPuntoVenta] = [
                        'codigo' => $codPuntoVenta,
                        'nombre' => $punto_venta_options[$codPuntoVenta] ?? $codPuntoVenta,
                        'total' => 0,
                        'cantidad' => 0,
                    ];
                }
                $ventas_por_punto[$codPuntoVenta]['total'] += $monto_venta;
                $ventas_por_punto[$codPuntoVenta]['cantidad'] += 1;
            }

            if ($tipo) {
                if (!isset($ventas_por_tipo[$tipo])) {
                    $ventas_por_tipo[$tipo] = [
                        'codigo' => $tipo,
                        'nombre' => $this->types[$tipo] ?? $tipo,
                        'total' => 0,
                        'cantidad' => 0,
                    ];
                }
                $ventas_por_tipo[$tipo]['total'] += $monto_venta;
                $ventas_por_tipo[$tipo]['cantidad'] += 1;
            }

            $doc_receptor = $dte['documento_receptor'] ?? data_get($documento, 'receptor.nit');
            $nombre_receptor = $dte['nombre_receptor'] ?? data_get($documento, 'receptor.nombre');
            if ($doc_receptor) {
                $clientes[$doc_receptor] = $nombre_receptor ?: $doc_receptor;
                if (!isset($ventas_por_cliente[$doc_receptor])) {
                    $ventas_por_cliente[$doc_receptor] = [
                        'documento' => $doc_receptor,
                        'nombre' => $clientes[$doc_receptor],
                        'total' => 0,
                        'cantidad' => 0,
                    ];
                }
                $ventas_por_cliente[$doc_receptor]['total'] += $monto_venta;
                $ventas_por_cliente[$doc_receptor]['cantidad'] += 1;
            }

            $cuerpo = data_get($documento, 'cuerpoDocumento', []);
            if (is_array($cuerpo)) {
                foreach ($cuerpo as $item) {
                    if (is_object($item)) {
                        $item = json_decode(json_encode($item), true);
                    }
                    if (!is_array($item)) {
                        continue;
                    }
                    $monto_item = $this->getMontoItem($item);
                    $descripcion = Arr::get($item, 'descripcion', 'Sin descripción');
                    $codigo = Arr::get($item, 'codigo', 'N/D');
                    $key = $codigo . '|' . $descripcion;

                    if (!isset($ventas_por_producto[$key])) {
                        $ventas_por_producto[$key] = [
                            'codigo' => $codigo,
                            'descripcion' => $descripcion,
                            'total' => 0,
                            'cantidad' => 0,
                        ];
                    }

                    $ventas_por_producto[$key]['total'] += $monto_item * $multiplier;
                    $ventas_por_producto[$key]['cantidad'] += (float) Arr::get($item, 'cantidad', 1);

                    $productos[$key] = true;
                }
            }
        }

        $stats['clientes_unicos'] = count($clientes);
        $stats['productos_unicos'] = count($productos);
        $stats['ticket_promedio'] = $stats['total_documentos'] > 0 ? $stats['total_ventas'] / $stats['total_documentos'] : 0;
        $stats['total_contado'] = $ventas_por_condicion[1]['total'] ?? 0;
        $stats['total_credito'] = $ventas_por_condicion[2]['total'] ?? 0;
        $stats['total_otro'] = $ventas_por_condicion[3]['total'] ?? 0;

        $ventas_por_tipo = collect($ventas_por_tipo)->sortByDesc('total')->values()->all();
        $ventas_por_sucursal = collect($ventas_por_sucursal)->sortByDesc('total')->values()->all();
        $ventas_por_punto = collect($ventas_por_punto)->sortByDesc('total')->values()->all();
        $ventas_por_producto = collect($ventas_por_producto)->sortByDesc('total')->take(10)->values()->all();
        $ventas_por_cliente = collect($ventas_por_cliente)->sortByDesc('total')->take(10)->values()->all();

        return [
            'stats' => $stats,
            'ventas_por_tipo' => $ventas_por_tipo,
            'ventas_por_sucursal' => $ventas_por_sucursal,
            'ventas_por_punto' => $ventas_por_punto,
            'ventas_por_producto' => $ventas_por_producto,
            'ventas_por_cliente' => $ventas_por_cliente,
            'ventas_por_estado' => $ventas_por_estado,
            'ventas_por_condicion' => $ventas_por_condicion,
        ];
    }

    private function getFilterState(): array
    {
        return [
            'fechaInicio' => $this->fechaInicio,
            'fechaFin' => $this->fechaFin,
            'emisionInicio' => $this->emisionInicio,
            'emisionFin' => $this->emisionFin,
            'codSucursal' => $this->codSucursal,
            'codPuntoVenta' => $this->codPuntoVenta,
            'tipo_dte' => $this->tipo_dte,
            'estado' => $this->estado,
            'sort' => $this->sort,
            'limit' => $this->limit,
            'documento_receptor' => $this->documento_receptor,
            'q' => $this->q,
            'condicion_operacion' => $this->condicion_operacion,
            'minMonto' => $this->minMonto,
            'maxMonto' => $this->maxMonto,
            'periodo' => $this->periodo,
        ];
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

    public function formatMoney($value): string
    {
        return '$' . number_format((float) $value, 2, '.', ',');
    }
}
