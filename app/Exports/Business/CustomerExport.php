<?php

namespace App\Exports\Business;

use App\Models\BusinessCustomer;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CustomerExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    protected $search;
    protected $searchNombre;
    protected $searchNumDocumento;

    protected $exactSearch;
    protected $departamentos;
    protected $octopusService;

    public function __construct($search, $searchNombre, $searchNumDocumento, $exactSearch, $departamentos, $octopusService)
    {
        $this->search = $search;
        $this->searchNombre = $searchNombre;
        $this->searchNumDocumento = $searchNumDocumento;
        $this->exactSearch = $exactSearch;
        $this->departamentos = $departamentos;
        $this->octopusService = $octopusService;
    }
    public function query()
    {
        return BusinessCustomer::query()
            ->where("business_id", session("business"))
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('numDocumento', 'like', "%{$this->search}%")
                        ->orWhere('nrc', 'like', "%{$this->search}%")
                        ->orWhere('nombre', 'like', "%{$this->search}%")
                        ->orWhere('nombreComercial', 'like', "%{$this->search}%");
                });
            })
            ->when($this->searchNombre, function ($query) {
                $query->where('nombre', $this->exactSearch ? $this->searchNombre : 'like', "%{$this->searchNombre}%");
            })
            ->when($this->searchNumDocumento, function ($query) {
                $query->where('numDocumento', $this->exactSearch ? $this->searchNumDocumento : 'like', "%{$this->searchNumDocumento}%");
            })
            ->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'Nombre',
            'Nombre Comercial',
            'NRC',
            'Num. Documento',
            'Teléfono',
            'Email',
            'Departamento',
            'Municipio',
            'Dirección',
            'Fecha de Creación',
        ];
    }

    public function map($customer): array
    {
        return [
            $customer->nombre,
            $customer->nombreComercial,
            $customer->nrc,
            $customer->numDocumento,
            $customer->telefono,
            $customer->correo,
            $customer->departamento ? $this->obtenerNombreDepartamento($customer->departamento) : '',
            $customer->municipio ? $this->obtenerNombreMunicipio($customer->departamento, $customer->municipio) : '',
            $customer->complemento,
            $customer->created_at ? $customer->created_at->format('Y-m-d H:i:s') : '',
        ];
    }

    protected function obtenerNombreDepartamento(string $codigoDepto): ?string {
        return $this->departamentos[$codigoDepto]['nombre'] ?? null;
    }

    protected function obtenerNombreMunicipio(string $codigoDepto, string $codigoMunicipio): ?string {
        return $this->departamentos[$codigoDepto]['municipios'][$codigoMunicipio]['nombre'] ?? null;
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->setAutoFilter('A1:J1');
                $sheet->getStyle('A1:J1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '4F81BD'],
                    ],
                ]);
            },
        ];
    }
}