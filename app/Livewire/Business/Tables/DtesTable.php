<?php

namespace App\Livewire\Business\Tables;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Models\Business;
use App\Models\BusinessPlan;
use App\Models\Sucursal;


class DtesTable extends Component
{
    use WithPagination;

    public $nit;
    public $page = 1;
    public $fechaInicio;
    public $fechaFin;
    public $codSucursal;
    public $codPuntoVenta;
    public $tipo_dte;
    public $estado;
    public $sort = 'desc';
    public $perPage = 10;
    public $documento_receptor;
    public $receptores_unicos = [
        '' => 'Todos'
    ];
    public $types = [
        '' => 'Todos',
        '01' => 'Factura Electrónica',
        '03' => 'Comprobante de crédito fiscal',
        '04' => 'Nota de Remisión',
        '05' => 'Nota de crédito',
        '06' => 'Nota de débito',
        '07' => 'Comprobante de retención',
        '11' => 'Factura de exportación',
        '14' => 'Factura de sujeto excluido'
    ];
    public $q;
    public $statistics;
    protected $updatesQueryString = ['page'];

    public function updating($property)
    {
        if (in_array($property, ['fechaInicio', 'fechaFin', 'codSucursal', 'codPuntoVenta', 'tipo_dte', 'estado', 'sort', 'perPage', 'documento_receptor', 'q'])) {
            $this->page = 1; // Reset page when any of these properties change
        }
    }

    public function clearFilters()
    {
        $this->reset([
            'fechaInicio',
            'fechaFin',
            'codSucursal',
            'codPuntoVenta',
            'tipo_dte',
            'estado',
            'sort',
            'perPage',
            'documento_receptor',
            'q'
        ]);
        $this->page = 1; // Reset page when filters are cleared
    }

    public function render()
    {
        // Obtener el NIT del negocio desde la sesión
        $business_id = Session::get('business') ?? null;
        $business = Business::find($business_id);
        $this->nit = $business->nit ?? null;

        // Parametros
        $parameters = [
            'nit' => $this->nit,
            'fechaInicio' => $this->fechaInicio ? "{$this->fechaInicio}T00:00:00" : null,
            'fechaFin' => $this->fechaFin ? "{$this->fechaFin}T23:59:59" : null,
            'codSucursal' => $this->codSucursal,
            'codPuntoVenta' => $this->codPuntoVenta,
            'tipo_dte' => $this->tipo_dte,
            'estado' => $this->estado,
            'documento_receptor' => $this->documento_receptor,
            'q' => $this->q
        ];


        // Obtener el plan de negocio y los tipos de DTE disponibles
        $business_plan = BusinessPlan::where("nit", $business->nit)->first();
        $plan_dtes = json_decode($business_plan->dtes);
        $dte_options = [];
        foreach ($plan_dtes as $dte) {
            $dte_options[$dte] = $this->types[$dte];
        }
        $dte_options = array_merge(['' => 'Todos'], $dte_options);

        // Realizar la solicitud a la API de Octopus para obtener los DTEs
        $response_dtes = Http::get(env("OCTOPUS_API_URL") . "/dtes",   array_merge($parameters, [
            'page' => $this->page,
            'limit' => $this->perPage,
            'sort' => $this->sort,
        ]));
        $data = $response_dtes->json();
        $dtes = array_map(function ($dte) {
            $dte["documento"] = json_decode($dte["documento"]);
            return $dte;
        }, $data['items'] ?? []);

        // Obtener la lista de receptores únicos
        $response_receptores = Http::get(env("OCTOPUS_API_URL") . "/dtes/receptor-list/{$this->nit}");
        $receptores = $response_receptores->json() ?? [];
        $this->receptores_unicos = [];
        foreach ($receptores as $receptor) {
            if (isset($receptor['documento_receptor'], $receptor['nombre_receptor'])) {
                $this->receptores_unicos[$receptor['documento_receptor']] = $receptor['nombre_receptor'];
            }
        }
        $this->receptores_unicos = array_merge(['' => 'Todos'], $this->receptores_unicos);

        // Obtener las sucursales y puntos de venta del negocio
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

        // Obtener estadísticas de DTEs
        $response_stats = Http::get(env("OCTOPUS_API_URL") . "/dtes/statistics/", $parameters);
        $this->statistics = $response_stats->json() ?? [];

        return view('livewire.business.tables.dtes-table', [
            'dtes' => $dtes,
            'total' => $data['total'] ?? 0,
            'total_pages' => $data['total_pages'] ?? 0,
            'dte_options' => $dte_options,
            'sucursal_options' => $sucursal_options,
            'punto_venta_options' => $punto_venta_options,
        ]);
    }
}
