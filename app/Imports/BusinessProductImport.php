<?php

namespace App\Imports;

use App\Models\BusinessProduct;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');
/**
 * Class BusinessProductImport
 *
 * This class is responsible for importing business products from an Excel file.
 * It implements the ToModel and WithHeadingRow interfaces from the Maatwebsite Excel package.
 */
class BusinessProductImport implements ToModel, WithHeadingRow, WithUpserts
{

    private $business_id;
    private $unidades_medidas;

    /**
     * BusinessProductImport constructor.
     *
     * @param int $business_id
     * @param array $unidades_medidas
     */
    public function __construct(int $business_id, array $unidades_medidas)
    {
        $this->business_id = $business_id;
        $this->unidades_medidas = $unidades_medidas;
    }

    /**
     * Method to handle the import of each row as a model.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Check if the row has the required fields and if the prices are valid
        if (!isset($row["codigo"]) || !isset($row["descripcion"]) || !isset($row["tipoItem"]) || !isset($row["uniMedida"]) || !isset($row["precioUni"]) || !isset($row["precioSinIVA"])) {
            return null; // Skip rows that do not have the required fields
        }

        if (($row["precioUni"] == 0 && $row["precioSinIVA"] == 0) || empty($row["tipoItem"]) || empty($row["uniMedida"]) || empty($row["descripcion"])) {
            return null; // Skip rows with zero prices and empty names
        }

        $tipoItem = mb_strtolower($row["tipoItem"]);
        switch ($tipoItem) {
            case 'bien':
                $tipoItem = 1;
                break;
            case 'servicio':
                $tipoItem = 2;
                break;
            case 'bien y servicio':
                $tipoItem = 3;
                break;
            default:
                $tipoItem = 1;
        }

        $uniMedida = mb_strtolower($row["uniMedida"]);
        $uniMedida = array_search($uniMedida, $this->unidades_medidas, true);
        $uniMedida = ($uniMedida === false) ? '59' : $uniMedida;

        return new BusinessProduct([
            'business_id' => $this->business_id,
            'tipoItem' => $tipoItem,
            'codigo' => $row["codigo"],
            'uniMedida' => $uniMedida,
            'descripcion' => $row["descripcion"],
            'precioUni' => isset($row["precioUni"]) && $row["precioUni"] != 0
                ? $row["precioUni"]
                : (isset($row["precioSinIVA"]) && $row["precioSinIVA"] != 0
                    ? $row["precioSinIVA"] * 1.13
                    : 0),
            'precioSinTributos' => isset($row["precioSinIVA"]) && $row["precioSinIVA"] != 0
                ? $row["precioSinIVA"]
                : (isset($row["precioUni"]) && $row["precioUni"] != 0
                    ? $row["precioUni"] / 1.13
                    : 0),
            'tributos' => '["20"]',
            'stockInicial' => $row["stockInicial"] ?? 0,
            'stockActual' => $row["stockInicial"] ?? 0,
            'stockMinimo' => 1,
            'has_stock' => $tipoItem == 1 || $tipoItem == 3 ? 1 : 0,
            'image_url' => null,
            'category_id' => null
        ]);
    }
    /**
     * Method to define the heading row for the import.
     *
     * @return int
     */
    public function headingRow(): int
    {
        return 1; // The first row is the heading row
    }

    /**
     * @return string|array
     */
    public function uniqueBy()
    {
        return ['codigo', 'descripcion', 'business_id'];
    }
}