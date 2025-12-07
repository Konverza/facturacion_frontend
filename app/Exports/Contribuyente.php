<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class Contribuyente implements FromCollection, WithColumnFormatting, WithStrictNullComparison
{
    
    protected $dtes;
    protected $tipoOperacion;
    protected $tipoIngreso;

    public function __construct($dtes, $tipoOperacion, $tipoIngreso)
    {
        $this->dtes = $dtes;
        $this->tipoOperacion = $tipoOperacion;
        $this->tipoIngreso = $tipoIngreso;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = [];

        foreach($this->dtes as $dte){

            $totalIva = 0;
            if(isset($dte["documento"]->resumen->tributos)){
                foreach($dte["documento"]->resumen->tributos as $tributo){
                    if($tributo->codigo == "20"){
                        $totalIva = $tributo->valor;
                    }
                }
            }

            $totalNoGravado = $dte["documento"]->resumen->totalNoGravado ?? 0;
            $totalGravado = $dte["documento"]->resumen->totalGravada ?? 0;
            $totalExento = $dte["documento"]->resumen->totalExenta ?? 0;
            $totalNoSuj = $dte["documento"]->resumen->totalNoSuj ?? 0;
            $totalPagar = $dte["documento"]->resumen->totalPagar ?? 0;

            $data[] = [
                \Carbon\Carbon::parse($dte["fhEmision"])->format('d/m/Y'), // Fecha de emisión (A)
                "4", // clase de documento (B)
                $dte["tipo_dte"], // tipo de documento (C)
                str_replace('-', '', $dte["documento"]->identificacion->numeroControl), // número de documento (D)
                $dte["selloRecibido"], // número de serie de documento (E)
                str_replace('-', '', $dte["codGeneracion"]), // número de resolución (F)
                null, // Número de control interno (G)
                $dte["documento"]->receptor->nit, // NIT o NRC receptor (H)
                $dte["documento"]->receptor->nombre, // Nombre o razón social del receptor (I)
                round($totalExento, 2), // Ventas Exentas (J)
                round($totalNoSuj, 2), // Ventas No sujetas (K)
                round($totalGravado, 2), // Ventas Gravadas (L)
                round($totalIva, 2), // Débito fiscal (M)
                0.00, // Ventas a cuentas de terceros no domiciliados (N)
                0.00, // Débito fiscal por ventas a cuentas de terceros no domiciliados (O)
                $totalPagar, // Total ventas (P)
                null, // DUI del cliente (Q)
                $this->tipoOperacion, // Tipo de operación (Renta) (R)
                $this->tipoIngreso, // Tipo de Ingreso (Renta) (S)
                "1"  // Número de Anexo, siempre "1" (T)
            ];
        }

        return collect([$data]);
    }

    public function columnFormats(): array
    {
        return [
            'J' => NumberFormat::FORMAT_NUMBER_00,
            'K' => NumberFormat::FORMAT_NUMBER_00,
            'L' => NumberFormat::FORMAT_NUMBER_00,
            'M' => NumberFormat::FORMAT_NUMBER_00,
            'N' => NumberFormat::FORMAT_NUMBER_00,
            'O' => NumberFormat::FORMAT_NUMBER_00,
            'P' => NumberFormat::FORMAT_NUMBER_00,

        ];
    }
}
