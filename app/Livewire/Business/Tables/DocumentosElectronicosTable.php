<?php

namespace App\Livewire\Business\Tables;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class DocumentosElectronicosTable extends Component
{
    use WithPagination;

    public $nit;
    public $number;
    public $perPage = 5;
    public $search = '';
    public $page = 1;

    // Nuevos filtros
    public $numero_documento = '';
    public $tipo_documento = '';
    public $emitido_desde = '';
    public $emitido_hasta = '';

    protected $listeners = ['refreshNumeroDocumento'];

    public function refreshNumeroDocumento($nuevoNumeroDocumento)
    {
        $this->numero_documento = $nuevoNumeroDocumento;
        $this->page = 1;
    }

    public function mount($nit, $number, $numero_documento)
    {
        $this->nit = $nit;
        $this->number = $number;
        $this->numero_documento = $numero_documento;
    }

    public function updating($property)
    {
        if (in_array($property, ['search', 'perPage', 'numero_documento', 'tipo_documento', 'emitido_desde', 'emitido_hasta'])) {
            $this->page = 1;
        }
    }

    public function clearFilters()
    {
        $this->reset(['numero_documento', 'tipo_documento', 'emitido_desde', 'emitido_hasta', 'search']);
        $this->page = 1;
    }

    public function render()
    {
        // Construir parámetros para la API
        $params = [
            'nit' => $this->nit,
            'q' => $this->search,
            'tipo_dte' => $this->tipo_documento,
            'documento_receptor' => $this->numero_documento,
            'emisionInicio' => $this->emitido_desde ? $this->emitido_desde . 'T00:00:00' : null,
            'emisionFin' => $this->emitido_hasta ? $this->emitido_hasta . 'T23:59:59' : null,
        ];

        // Limpiar nulos
        $params = array_filter($params, function($v) { return $v !== null && $v !== ''; });

        $response = Http::timeout(30)->get(env('OCTOPUS_API_URL') . '/dtes/', $params);
        $data = $response->json();
        $items = collect($data['items'] ?? [])->filter(function ($dte) {
            return $dte['estado'] === 'PROCESADO';
        });

        // Filtros según el tipo de documento relacionado (solo para la tabla, no para la API)
        if ($this->number === '04') {
            $items = $items->filter(function ($dte) {
                return in_array($dte['tipo_dte'], ['01', '03']);
            });
        } elseif ($this->number === '05' || $this->number === '06') {
            $items = $items->filter(function ($dte) {
                return in_array($dte['tipo_dte'], ['03', '07']);
            });
        } elseif ($this->number === '07') {
            $items = $items->filter(function ($dte) {
                return in_array($dte['tipo_dte'], ['01', '03', '14']);
            });
        }

        $total = $items->count();
        $total_pages = (int) ceil($total / $this->perPage);
        $page = $this->page;
        $items = $items->forPage($page, $this->perPage)->values();

        // Tipos de documentos para mostrar en la tabla
        $tipos_documentos = [];
        switch ($this->number) {
            case '01':
                $tipos_documentos = [
                    '04' => 'Nota de remisión',
                    '09' => 'Documento contable de liquidación',
                ]; break;
            case '03':
                $tipos_documentos = [
                    '04' => 'Nota de remisión',
                    '08' => 'Comprobante de liquidación',
                    '09' => 'Documento contable de liquidación',
                ]; break;
            case '04':
                $tipos_documentos = [
                    '01' => 'Factura Consumidor Final',
                    '03' => 'Comprobante de crédito fiscal',
                ]; break;
            case '05':
            case '06':
                $tipos_documentos = [
                    '03' => 'Comprobante de crédito fiscal',
                    '07' => 'Comprobante de retención',
                ]; break;
        }

        return view('livewire.business.tables.documentos-electronicos-table', [
            'dtes' => $items,
            'tipos_documentos' => $tipos_documentos,
            'total' => $total,
            'page' => $page,
            'total_pages' => $total_pages,
            'perPage' => $this->perPage,
        ]);
    }
}
