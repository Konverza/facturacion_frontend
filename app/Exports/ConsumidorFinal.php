<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class ConsumidorFinal implements FromCollection, WithColumnFormatting, WithStrictNullComparison
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
        return collect($this->dtes);
    }

    public function columnFormats(): array
    {
        return [
            "K" => NumberFormat::FORMAT_NUMBER_00,
            "L" => NumberFormat::FORMAT_NUMBER_00,
            "M" => NumberFormat::FORMAT_NUMBER_00,
            "N" => NumberFormat::FORMAT_NUMBER_00,
            "O" => NumberFormat::FORMAT_NUMBER_00,
            "P" => NumberFormat::FORMAT_NUMBER_00,
            "Q" => NumberFormat::FORMAT_NUMBER_00,
            "R" => NumberFormat::FORMAT_NUMBER_00,
            "S" => NumberFormat::FORMAT_NUMBER_00,
            "T" => NumberFormat::FORMAT_NUMBER_00,
        ];
    }
}
