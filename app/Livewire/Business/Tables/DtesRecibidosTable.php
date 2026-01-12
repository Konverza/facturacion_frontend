<?php

namespace App\Livewire\Business\Tables;

use App\Services\OctopusService;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Models\Business;
use App\Models\DteImportProcess;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Business\DteExport;

class DtesRecibidosTable extends Component
{
    use WithPagination;

    public $nit;
    public $page = 1;
    public $fechaInicio;
    public $fechaFin;
    public $tipo_dte;
    public $sort = 'desc';
    public $perPage = 10;
    public $documento_emisor;
    public $emisores_unicos = [
        '' => 'Todos'
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
        '15' => 'Comprobante de Donación'
    ];
    public $q;
    public $statistics;
    protected $updatesQueryString = ['page'];
    protected $octopus_service;
    public $formas_pago;

    public function mount()
    {
        $this->octopus_service = new OctopusService();
        $this->formas_pago = $this->octopus_service->getCatalog("CAT-017");
    }

    public function updating($property)
    {
        if (in_array($property, ['fechaInicio', 'fechaFin', 'tipo_dte', 'sort', 'perPage', 'documento_emisor', 'q'])) {
            $this->page = 1; // Reset page when any of these properties change
        }
    }

    public function clearFilters()
    {
        $this->reset([
            'fechaInicio',
            'fechaFin',
            'tipo_dte',
            'documento_emisor',
            'q'
        ]);
        $this->page = 1;
    }

    public function render()
    {
        $business_id = Session::get('business') ?? null;
        $business = Business::find($business_id);
        $this->nit = $business->nit ?? null;

        // Parametros 
        $parameters = [
            'nit' => $this->nit,
            'fechaInicio' => $this->fechaInicio ? "{$this->fechaInicio}T00:00:00" : null,
            'fechaFin' => $this->fechaFin ? "{$this->fechaFin}T23:59:59" : null,
            'tipo_dte' => $this->tipo_dte,
            'documento_emisor' => $this->documento_emisor,
            'q' => $this->q,
            'sort' => $this->sort,
            'page' => $this->page,
            'pageSize' => $this->perPage
        ];

        // Realizar la solicitud a la API de Octopus para obtener los DTEs
        $response_dtes = Http::get(env("OCTOPUS_API_URL") . "/dtes_recibidos/",   array_merge($parameters, [
            'page' => $this->page,
            'limit' => $this->perPage,
            'sort' => $this->sort,
        ]));
        $data = $response_dtes->json();
        $dtes = array_map(function ($dte) {
            $dte["documento"] = json_decode($dte["documento"]);
            return $dte;
        }, $data['items'] ?? []);

        // Obtener la lista de emisores únicos
        $response_emisores = Http::get(env("OCTOPUS_API_URL") . "/dtes_recibidos/emisor-list/{$this->nit}");
        $emisores = $response_emisores->json() ?? [];
        $this->emisores_unicos = [];
        foreach ($emisores as $emisor) {
            if (isset($emisor['documento_emisor'], $emisor['nombre_emisor'])) {
                $this->emisores_unicos[$emisor['documento_emisor']] = $emisor['nombre_emisor'];
            }
        }
        $this->emisores_unicos = array_merge(['' => 'Todos'], $this->emisores_unicos);
        // Obtener estadísticas de DTEs
        $response_stats = Http::get(env("OCTOPUS_API_URL") . "/dtes_recibidos/statistics/", $parameters);
        $this->statistics = $response_stats->json() ?? [];

        // Obtener el último proceso de importación completado
        $lastImport = DteImportProcess::where('nit', $this->nit)
            ->where('status', 'completed')
            ->orderBy('updated_at', 'desc')
            ->first();

        return view('livewire.business.tables.dtes-recibidos-table', [
            'dtes' => $dtes,
            'total' => $data['total'] ?? 0,
            'total_pages' => $data['total_pages'] ?? 0,
            'dte_options' => $this->types,
            'formas_pago' => $this->formas_pago,
            'statistics' => $this->statistics,
            'lastImport' => $lastImport,
        ]);
    }
}
