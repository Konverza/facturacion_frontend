<?php

namespace App\Exports\Business;

use App\Models\BusinessProduct;
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

class ProductsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting, WithEvents
{
    protected $search;
    protected $searchName;
    protected $searchCode;
    protected $exactSearch;
    protected $stockState;

    public function __construct($search, $searchName, $searchCode, $exactSearch, $stockState = 'todos')
    {
        $this->search = $search;
        $this->searchName = $searchName;
        $this->searchCode = $searchCode;
        $this->exactSearch = $exactSearch;
        $this->stockState = $stockState;
    }

    public function query()
    {
        return BusinessProduct::query()
            ->where("business_id", session("business"))
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('codigo', 'like', "%{$this->search}%")
                        ->orWhere('descripcion', 'like', "%{$this->search}%");
                });
            })
            ->when($this->searchName, function ($query) {
                $query->where('descripcion', $this->exactSearch ? $this->searchName : 'like', "%{$this->searchName}%");
            })
            ->when($this->searchCode, function ($query) {
                $query->where('codigo', $this->exactSearch ? $this->searchCode : 'like', "%{$this->searchCode}%");
            })
            ->when($this->stockState, function ($query) {
                if ($this->stockState !== 'todos' && $this->stockState !== 'n/a') {
                    $query->where('estado_stock', $this->stockState);
                } elseif ($this->stockState === 'n/a') {
                    $query->where('has_stock', false);
                }
            })
            ->with('category')
            ->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'Código',
            'Descripción',
            'Categoría',
            'Precio sin IVA',
            'Precio con IVA',
            'Stock Actual',
            'Estado Stock',
            'Fecha Creación'
        ];
    }

    public function map($product): array
    {
        return [
            $product->codigo,
            $product->descripcion,
            $product->category ? $product->category->name : 'N/A',
            $product->precioSinTributos,
            $product->precioUni,
            $product->has_stock ? ($product->stockActual ?? 0) : 'N/A',
            $this->getStockStatus($product),
            $product->created_at->format('d/m/Y H:i'),
        ];
    }

    protected function getStockStatus($product)
    {
        if (!$product->has_stock)
            return 'N/A';

        return match ($product->estado_stock) {
            'disponible' => 'Disponible',
            'por_agotarse' => 'Por agotarse',
            default => 'Agotado',
        };
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Obtener la hoja activa
                $sheet = $event->sheet->getDelegate();

                // Aplicar autofiltros a todas las columnas con datos (A:H)
                $sheet->setAutoFilter('A1:H1');

                // Estilo adicional para la fila de encabezados
                $headerStyle = [
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '4F81BD'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ];

                // Aplicar estilo a la fila de encabezados
                $event->sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

                // Congelar la fila de encabezados
                $sheet->freezePane('A2');

                // Añadir bordes a toda la tabla
                $tableStyle = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ];

                $lastRow = $sheet->getHighestRow();
                $event->sheet->getStyle("A1:H{$lastRow}")->applyFromArray($tableStyle);
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo general para las celdas de datos
            'A2:H' . $sheet->getHighestRow() => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],

            // Alinear precios a la derecha
            'D2:E' . $sheet->getHighestRow() => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                ],
            ],

            // Alinear stock al centro
            'F2:F' . $sheet->getHighestRow() => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => '_-$* #,##0.00000000_-;-$* #,##0.00000000_-;_-$* "-"????????_-;_-@_-',
            'E' => '_-$* #,##0.00000000_-;-$* #,##0.00000000_-;_-$* "-"????????_-;_-@_-',
        ];
    }
}