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

class DteExport extends DefaultValueBinder implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithColumnFormatting, WithEvents, WithColumnWidths, WithCustomValueBinder
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

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $processed_dtes = [];

        foreach($this->dtes as $dte) {

            $monto_operacion = match ($dte['tipo_dte']) {
                '01' => $dte['documento']->resumen->totalPagar,
                '03' => $dte['documento']->resumen->totalPagar,
                '05' => $dte['documento']->resumen->montoTotalOperacion,
                '07' => $dte['documento']->resumen->totalSujetoRetencion,
                '11' => $dte['documento']->resumen->totalPagar,
                '14' => $dte['documento']->resumen->totalCompra,
                '15' => $dte['documento']->resumen->valorTotal,
                default => 0,
            };

            $formas_de_pago = "";
            if (property_exists($dte['documento']->resumen, 'pagos')) {
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
                "tipo_dte" =>  $this->types[$dte['tipo_dte']],
                "fhProcesamiento" => \Carbon\Carbon::parse($dte['fhProcesamiento'])->format('d/m/Y h:i:s A'),
                "estado" => $dte['estado'],
                "codigo_generacion" => $dte['codGeneracion'],
                "numero_control" => $dte['documento']->identificacion->numeroControl,
                "sello_recibido" => $dte['selloRecibido'],
                "nombre_receptor" => $dte['nombre_receptor'],
                "documento_receptor" => $dte['documento_receptor'],
                "monto_operacion" => $monto_operacion,
                "formas_de_pago" => $formas_de_pago,
                "codigo_sucursal" => $dte['codSucursal'],
                "codigo_punto_venta" => $dte['codPuntoVenta'],
                "observaciones" => $dte['observaciones'] != '[]' ? $dte['observaciones'] : '',
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
            'Fecha de Procesamiento',
            'Estado',
            'Código de Generación',
            'Número de Control',
            'Sello Recibido',
            'Nombre del Receptor',
            'Documento del Receptor',
            'Monto de Operación',
            'Formas de Pago',
            'Código de Sucursal',
            'Código de Punto de Venta',
            'Observaciones',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => '@', // Tipo DTE
            'B' => 'dd/mm/yyyy hh:mm:ss AM/PM', // Fecha de Procesamiento
            'C' => '@', // Estado
            'D' => '@', // Código de Generación
            'E' => '@', // Número de Control
            'F' => '@', // Sello Recibido
            'G' => '@', // Nombre del Receptor
            'I' => '_-$* #,##0.00_-;-$* #,##0.00_-;_-$* "-"??_-;_-@_-', // Monto de Operación
            'J' => '@', // Formas de Pago
            'K' => '@', // Código de Sucursal
            'L' => '@', // Código de Punto de Venta
            'M' => '@', // Observaciones
        ];
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

                // Aplicar autofiltros a todas las columnas con datos (A:L)
                $sheet->setAutoFilter('A1:M1');

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
                $event->sheet->getStyle('A1:M1')->applyFromArray($headerStyle);

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
                $event->sheet->getStyle("A1:M{$lastRow}")->applyFromArray($tableStyle);
                $sheet->getStyle('M:M')->getAlignment()->setWrapText(true);
            },
        ];
    }

    public function columnWidths(): array
    {
        return [
            'M' => 55, // Observaciones
        ];
    }

    public function bindValue(\PhpOffice\PhpSpreadsheet\Cell\Cell $cell, $value)
    {
        if (in_array($cell->getColumn(), ["H"])) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }
        return parent::bindValue($cell, $value);
    }
}
