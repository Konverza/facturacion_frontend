<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class Contribuyente implements FromCollection, WithColumnFormatting
{
    
    protected $dtes;

    public function __construct($dtes)
    {
        $this->dtes = $dtes;
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

            $tipoOperacion = "";
            $totalNoGravado = $dte["documento"]->resumen->totalNoGravado ?? 0;
            $totalGravado = $dte["documento"]->resumen->totalGravada ?? 0;
            $totalExento = $dte["documento"]->resumen->totalExenta ?? 0;
            $totalNoSuj = $dte["documento"]->resumen->totalNoSuj ?? 0;
            $totalPagar = $dte["documento"]->resumen->totalPagar ?? 0;
            
            if($totalExento > 0 || $totalNoGravado > 0){
                $tipoOperacion = "02";
            } elseif ($totalGravado > 0 && $totalExento == 0 && $totalNoSuj == 0) {
                $tipoOperacion = "01";
            } elseif ($totalNoSuj > 0 && $totalGravado == 0 && $totalExento == 0) {
                $tipoOperacion = "03";
            } else {
                $tipoOperacion = "04"; 
            }

            $data[] = [
                \Carbon\Carbon::parse($dte["fhProcesamiento"])->format('d/m/Y'),
                "4",
                $dte["tipo_dte"],
                str_replace('-', '', $dte["documento"]->identificacion->numeroControl),
                $dte["selloRecibido"],
                str_replace('-', '', $dte["codGeneracion"]),
                null,
                $dte["documento"]->receptor->nit,
                $dte["documento"]->receptor->nombre,
                round($totalExento, 2),
                round($totalNoSuj, 2),
                round($totalGravado, 2),
                round($totalIva, 2),
                0.00,
                0.00,
                $totalPagar,
                null,
                $tipoOperacion,
                "03",
                "1"  
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
