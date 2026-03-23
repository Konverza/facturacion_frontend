<?php

namespace App\Livewire\Business\Tables;

use Illuminate\Support\Facades\Http;
use Livewire\Component;

class CuentasCobrarDtesTable extends Component
{
    public $nit;
    public $perPage = 5;
    public $search = '';
    public $page = 1;
    public $tipo_documento = '';
    public $emitido_desde = '';
    public $emitido_hasta = '';

    public function mount($nit)
    {
        $this->nit = $nit;
    }

    public function updating($property)
    {
        if (in_array($property, ['search', 'perPage', 'tipo_documento', 'emitido_desde', 'emitido_hasta'])) {
            $this->page = 1;
        }
    }

    public function clearFilters()
    {
        $this->reset(['search', 'tipo_documento', 'emitido_desde', 'emitido_hasta']);
        $this->page = 1;
    }

    public function render()
    {
        $params = [
            'nit' => $this->nit,
            'q' => $this->search,
            'tipo_dte' => $this->tipo_documento,
            'estado' => 'PROCESADO',
            'emisionInicio' => $this->emitido_desde ? $this->emitido_desde . 'T00:00:00' : null,
            'emisionFin' => $this->emitido_hasta ? $this->emitido_hasta . 'T23:59:59' : null,
            'page' => $this->page,
            'limit' => $this->perPage,
            'sort' => 'desc',
        ];

        $params = array_filter($params, function ($v) {
            return $v !== null && $v !== '';
        });

        $response = Http::timeout(30)->get(env('OCTOPUS_API_URL') . '/dtes/', $params);
        $data = $response->json() ?? [];

        $items = collect($data['items'] ?? [])->values();
        $totalPages = max(1, (int) ($data['total_pages'] ?? 1));
        $page = min(max(1, (int) $this->page), $totalPages);
        $this->page = $page;

        return view('livewire.business.tables.cuentas-cobrar-dtes-table', [
            'dtes' => $items,
            'page' => $page,
            'total_pages' => $totalPages,
            'perPage' => $this->perPage,
        ]);
    }
}
