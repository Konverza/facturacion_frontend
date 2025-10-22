<?php

namespace App\Exports\Business;

use App\Models\BusinessProduct;
use App\Models\BranchProductStock;
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
    protected $sucursalId;
    protected $unidadesMedidas;
    protected $business;

    public function __construct($search, $searchName, $searchCode, $exactSearch, $stockState = 'todos', $sucursalId = null, $unidadesMedidas = [], $business = null)
    {
        $this->search = $search;
        $this->searchName = $searchName;
        $this->searchCode = $searchCode;
        $this->exactSearch = $exactSearch;
        $this->stockState = $stockState;
        $this->sucursalId = $sucursalId;
        $this->unidadesMedidas = $unidadesMedidas;
        $this->business = $business;
    }

    public function query()
    {
        $query = BusinessProduct::query()
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
                    // Filtrar por estado de stock en la sucursal seleccionada
                    if ($this->sucursalId) {
                        $query->whereHas('branchStocks', function ($q) {
                            $q->where('sucursal_id', $this->sucursalId)
                              ->where('estado_stock', $this->stockState);
                        });
                    }
                } elseif ($this->stockState === 'n/a') {
                    $query->where('has_stock', false);
                }
            })
            ->when($this->sucursalId, function ($query) {
                // Filtrar productos disponibles en la sucursal seleccionada
                $query->where(function ($q) {
                    $q->where('is_global', true)
                      ->orWhereHas('branchStocks', function ($subQ) {
                          $subQ->where('sucursal_id', $this->sucursalId);
                      });
                });
            })
            ->with(['category', 'branchStocks' => function ($query) {
                if ($this->sucursalId) {
                    $query->where('sucursal_id', $this->sucursalId);
                }
            }])
            ->orderBy('created_at', 'desc');

        return $query;
    }

    public function headings(): array
    {
        return [
            'Código',
            'Descripción',
            'Tipo de Item',
            'Unidad de Medida',
            'Categoría',
            'Precio Unitario (Sin IVA)',
            'Precio Unitario (IVA Incluido)',
            'Precio con descuento (Sin IVA)',
            'Precio con descuento (IVA incluido)',
            'Costo de Compra',
            '¿Guardar Inventario?',
            'Stock',
            'Stock Mínimo',
            'Estado Stock',
            'Fecha Creación'
        ];
    }

    public function map($product): array
    {
        // Obtener el tipo de item en formato texto
        $tipoItem = match ($product->tipoItem) {
            1 => 'Bienes',
            2 => 'Servicios',
            3 => 'Ambos (Bienes y Servicios)',
            default => 'Bienes'
        };

        // Obtener la unidad de medida en formato texto
        $unidadMedida = $this->unidadesMedidas[$product->uniMedida] ?? 'Unidad';

        // Obtener el stock y estado de la sucursal seleccionada
        $stockActual = 'N/A';
        $estadoStock = 'N/A';
        
        if ($product->has_stock) {
            if ($this->sucursalId) {
                $branchStock = $product->branchStocks->first();
                $stockActual = $branchStock ? $branchStock->stockActual : 0;
                $estadoStock = $branchStock ? $this->getStockStatusText($branchStock->estado_stock) : 'N/A';
            } else {
                // Si no hay sucursal seleccionada, sumar todo el stock
                $stockActual = $product->branchStocks->sum('stockActual');
                $estadoStock = 'Múltiples sucursales';
            }
        }

        return [
            $product->codigo,
            $product->descripcion,
            $tipoItem,
            $unidadMedida,
            $product->category ? $product->category->name : 'Sin categoría',
            $product->precioSinTributos,
            $product->precioUni,
            $product->special_price ?: 0,
            $product->special_price_with_iva ?: 0,
            $product->cost ?: 0,
            $product->has_stock ? 'Sí' : 'No',
            $stockActual,
            $product->stockMinimo,
            $estadoStock,
            $product->created_at->format('d/m/Y H:i'),
        ];
    }

    protected function getStockStatusText($estado)
    {
        return match ($estado) {
            'disponible' => 'Disponible',
            'por_agotarse' => 'Por agotarse',
            'agotado' => 'Agotado',
            default => 'N/A',
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

                // Aplicar autofiltros a todas las columnas con datos (A:O)
                $sheet->setAutoFilter('A1:O1');

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
                $event->sheet->getStyle('A1:O1')->applyFromArray($headerStyle);

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
                $event->sheet->getStyle("A1:O{$lastRow}")->applyFromArray($tableStyle);
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo general para las celdas de datos
            'A2:O' . $sheet->getHighestRow() => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],

            // Alinear precios a la derecha
            'F2:J' . $sheet->getHighestRow() => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                ],
            ],

            // Alinear stock al centro
            'L2:N' . $sheet->getHighestRow() => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => '_-$* #,##0.00000000_-;-$* #,##0.00000000_-;_-$* "-"????????_-;_-@_-',
            'G' => '_-$* #,##0.00000000_-;-$* #,##0.00000000_-;_-$* "-"????????_-;_-@_-',
            'H' => '_-$* #,##0.00000000_-;-$* #,##0.00000000_-;_-$* "-"????????_-;_-@_-',
            'I' => '_-$* #,##0.00000000_-;-$* #,##0.00000000_-;_-$* "-"????????_-;_-@_-',
            'J' => '_-$* #,##0.00000000_-;-$* #,##0.00000000_-;_-$* "-"????????_-;_-@_-',
        ];
    }
}