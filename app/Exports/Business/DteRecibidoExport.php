<?php

namespace App\Exports\Business;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DteRecibidoExport extends DefaultValueBinder implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithColumnFormatting, WithEvents, WithColumnWidths, WithCustomValueBinder
{
    protected $dtes;
    protected $formas_pago;
    protected $types;

    public function __construct($dtes, $formas_pago = [], $types = [])
    {
        $this->formas_pago = $formas_pago;
        $this->dtes = $dtes;
        $this->types = $types;
    }

    public function collection()
    {
        $processed_dtes = [];

        foreach ($this->dtes as $dte) {
            $hasResumen = property_exists($dte['documento'] ?? (object) [], 'resumen');

            $monto_operacion = match ($dte['tipo_dte']) {
                '01' => $dte['documento']->resumen->totalPagar ?? 0,
                '03' => $dte['documento']->resumen->totalPagar ?? 0,
                '05' => $dte['documento']->resumen->montoTotalOperacion ?? 0,
                '07' => $dte['documento']->resumen->totalSujetoRetencion ?? 0,
                '09' => $hasResumen
                    ? ($dte['documento']->resumen->montoTotalOperacion ?? 0)
                    : ($dte['documento']->cuerpoDocumento->liquidoApagar ?? 0),
                '11' => $dte['documento']->resumen->totalPagar ?? 0,
                '14' => $dte['documento']->resumen->totalCompra ?? 0,
                '15' => $dte['documento']->resumen->valorTotal ?? 0,
                default => 0,
            };

            $formas_de_pago = '';
            if (property_exists($dte['documento']->resumen ?? (object) [], 'pagos')) {
                $pagos = $dte['documento']->resumen->pagos ?? [];
                $formas_array = [];
                foreach ($pagos as $pago) {
                    $forma = $this->formas_pago[$pago->codigo] ?? 'Desconocido';
                    $formas_array[] = $forma;
                }
                $formas_de_pago = !empty($formas_array) ? implode(', ', $formas_array) : 'Desconocido';
            } else {
                $formas_de_pago = 'Desconocido';
            }

            $processed_dtes[] = [
                'tipo_dte' => $this->types[$dte['tipo_dte']] ?? $dte['tipo_dte'],
                'fh_emision' => isset($dte['fhEmision']) ? \Carbon\Carbon::parse($dte['fhEmision'])->format('d/m/Y h:i:s A') : '',
                'estado' => $dte['estado'] ?? '',
                'codigo_generacion' => $dte['codGeneracion'] ?? '',
                'numero_control' => $dte['documento']->identificacion->numeroControl ?? '',
                'sello_recibido' => $dte['selloRecibido'] ?? '',
                'nombre_emisor' => $dte['nombre_emisor'] ?? '',
                'documento_emisor' => $dte['documento_emisor'] ?? '',
                'monto_operacion' => $monto_operacion,
                'formas_de_pago' => $formas_de_pago,
                'observaciones' => $dte['observaciones'] ?? '',
            ];
        }

        return collect($processed_dtes);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            'A1:Z1' => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFCCCCCC']],
            ],
            'A:Z' => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'Tipo DTE',
            'Fecha de Emisión',
            'Estado',
            'Código de Generación',
            'Número de Control',
            'Sello Recibido',
            'Nombre del Emisor',
            'Documento del Emisor',
            'Monto de Operación',
            'Formas de Pago',
            'Observaciones',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => '@',
            'B' => 'dd/mm/yyyy hh:mm:ss AM/PM',
            'C' => '@',
            'D' => '@',
            'E' => '@',
            'F' => '@',
            'G' => '@',
            'H' => '@',
            'I' => '_-$* #,##0.00_-;-$* #,##0.00_-;_-$* "-"??_-;_-@_-',
            'J' => '@',
            'K' => '@',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->setAutoFilter('A1:K1');

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

                $event->sheet->getStyle('A1:K1')->applyFromArray($headerStyle);
                $sheet->freezePane('A2');

                $tableStyle = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ];

                $lastRow = $sheet->getHighestRow();
                $event->sheet->getStyle("A1:K{$lastRow}")->applyFromArray($tableStyle);
                $sheet->getStyle('K:K')->getAlignment()->setWrapText(true);
            },
        ];
    }

    public function columnWidths(): array
    {
        return [
            'K' => 55,
        ];
    }

    public function bindValue(\PhpOffice\PhpSpreadsheet\Cell\Cell $cell, $value)
    {
        if (in_array($cell->getColumn(), ['H'])) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }
        return parent::bindValue($cell, $value);
    }
}
