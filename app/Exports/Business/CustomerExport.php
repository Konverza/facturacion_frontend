<?php

namespace App\Exports\Business;

use App\Models\BusinessCustomer;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CustomerExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting, WithEvents
{
    protected $search;
    protected $searchNombre;
    protected $searchNumDocumento;

    protected $exactSearch;

    public function __construct($search, $searchNombre, $searchNumDocumento, $exactSearch)
    {
        $this->search = $search;
        $this->searchNombre = $searchNombre;
        $this->searchNumDocumento = $searchNumDocumento;
        $this->exactSearch = $exactSearch;
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
}
