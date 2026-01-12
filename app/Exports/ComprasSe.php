<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class ComprasSe implements FromCollection, WithColumnFormatting, WithStrictNullComparison
{

    protected $dtes;
    protected $tipoOperacion;
    protected $clasificacion;
    protected $sector;
    protected $tipoCosto;
    public function __construct($dtes, $tipoOperacion, $clasificacion, $sector, $tipoCosto)
    {
        $this->dtes = $dtes;
        $this->tipoOperacion = $tipoOperacion;
        $this->clasificacion = $clasificacion;
        $this->sector = $sector;
        $this->tipoCosto = $tipoCosto;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = [];

        foreach($this->dtes as $dte){

            $tipoDocumento = [
                "36" => 1,
                "13" => 2,
                "37" => 3,
                "03" => 3,
                "02" => 3
            ]; 


            $data[] = [
                $tipoDocumento[$dte["documento"]->sujetoExcluido->tipoDocumento] ?? null, // Tipo de documento (A)
                str_replace('-', '', $dte["documento"]->sujetoExcluido->numDocumento), // Número de documento (B)
                $dte["documento"]->sujetoExcluido->nombre ?? null, // Nombre o razón social (C)
                \Carbon\Carbon::parse($dte["fhEmision"])->format('d/m/Y'), // Fecha de emisión (D)
                $dte["selloRecibido"], // número de serie de documento (E)
                str_replace('-', '', $dte["codGeneracion"]), // número de resolución (F)
                $dte["documento"]->resumen->totalCompra ?? 0, // Monto total de la compra (G)
                0.00, // Monto de la retención de IVA 13% (H)
                "2", // Tipo de operación (I)
                $this->clasificacion, // Clasificación (J)
                $this->sector, // Sector (K)
                $this->tipoCosto, // Tipo de costo (L)
                "5" // Número de anexo (M)
            ];
        }

        return collect($data);
    }

    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_NUMBER_00,
            'H' => NumberFormat::FORMAT_NUMBER_00
        ];
    }
}
