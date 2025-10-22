<?php

namespace App\Imports;

use App\Models\BusinessProduct;
use App\Models\BranchProductStock;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');
/**
 * Class BusinessProductImport
 *
 * This class is responsible for importing business products from an Excel file.
 * It implements the ToModel and WithHeadingRow interfaces from the Maatwebsite Excel package.
 */
class BusinessProductImport implements ToModel, WithHeadingRow, WithUpserts, WithChunkReading, WithBatchInserts
{

    private $business_id;
    private $unidades_medidas;
    private $sucursal_id;

    /**
     * BusinessProductImport constructor.
     *
     * @param int $business_id
     * @param array $unidades_medidas
     * @param int $sucursal_id
     */
    public function __construct(int $business_id, array $unidades_medidas, int $sucursal_id)
    {
        $this->business_id = $business_id;
        $this->unidades_medidas = $unidades_medidas;
        $this->sucursal_id = $sucursal_id;
    }

    /**
     * Method to handle the import of each row as a model.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Check for missing required fields
        $requiredKeys = ["Código", "Descripción", "Tipo de Item", "Unidad de Medida"];
        $missingKeys = [];
        foreach ($requiredKeys as $key) {
            if (!isset($row[$key]) || empty($row[$key])) {
            $missingKeys[] = $key;
            }
        }
        if (!isset($row["Precio Unitario (IVA Incluido)"]) && !isset($row["Precio Unitario (Sin IVA)"])) {
            $missingKeys[] = "Precio Unitario (IVA Incluido) o Precio Unitario (Sin IVA)";
        }

        if (!empty($missingKeys)) {
            Log::warning('Row skipped due to missing required fields', ['missing_keys' => $missingKeys, 'row' => $row]);
            return null; // Skip rows that do not have the required fields
        }

        if (
            (isset($row["Precio Unitario (IVA Incluido)"]) && $row["Precio Unitario (IVA Incluido)"] == 0) &&
            (isset($row["Precio Unitario (Sin IVA)"]) && $row["Precio Unitario (Sin IVA)"] == 0)
        ) {
            Log::warning('Row skipped due to zero prices', ['row' => $row]);
            return null; // Skip rows with zero prices
        }

        $tipoItem = mb_strtolower($row["Tipo de Item"]);
        switch ($tipoItem) {
            case 'bienes':
                $tipoItem = 1;
                break;
            case 'servicios':
                $tipoItem = 2;
                break;
            case 'ambos (bienes y servicios)':
                $tipoItem = 3;
                break;
            default:
                $tipoItem = 1;
        }

        $uniMedida = mb_strtolower($row["Unidad de Medida"]);
        $uniMedida = array_search($uniMedida, array_map('mb_strtolower', $this->unidades_medidas), true); 
        $uniMedida = ($uniMedida === false) ? '59' : $uniMedida;

        $hasStock = $row["¿Guardar Inventario?"] == "Sí" && $tipoItem != 2 ? 1 : 0;
        $stockInicial = $row["Stock"] ?? 0;

        // Crear el producto con is_global = false
        // Los productos sin inventario (has_stock = false) se marcarán como globales automáticamente
        $product = new BusinessProduct([
            'business_id' => $this->business_id,
            'tipoItem' => $tipoItem,
            'codigo' => $row["Código"],
            'uniMedida' => $uniMedida,
            'descripcion' => $row["Descripción"],
            'precioUni' => isset($row["Precio Unitario (IVA Incluido)"]) && $row["Precio Unitario (IVA Incluido)"] != 0
                ? $row["Precio Unitario (IVA Incluido)"]
                : (isset($row["Precio Unitario (Sin IVA)"]) && $row["Precio Unitario (Sin IVA)"] != 0
                    ? $row["Precio Unitario (Sin IVA)"] * 1.13
                    : 0),
            'precioSinTributos' => isset($row["Precio Unitario (Sin IVA)"]) && $row["Precio Unitario (Sin IVA)"] != 0
                ? $row["Precio Unitario (Sin IVA)"]
                : (isset($row["Precio Unitario (IVA Incluido)"]) && $row["Precio Unitario (IVA Incluido)"] != 0
                    ? $row["Precio Unitario (IVA Incluido)"] / 1.13
                    : 0),
            'special_price' => isset($row["Precio con descuento (Sin IVA)"]) && $row["Precio con descuento (Sin IVA)"] != 0
                ? $row["Precio con descuento (Sin IVA)"]
                : (isset($row["Precio con descuento (IVA incluido)"]) && $row["Precio con descuento (IVA incluido)"] != 0
                    ? $row["Precio con descuento (IVA incluido)"] / 1.13
                    : 0),
            'special_price_with_iva' => isset($row["Precio con descuento (IVA incluido)"]) && $row["Precio con descuento (IVA incluido)"] != 0
                ? $row["Precio con descuento (IVA incluido)"]
                : (isset($row["Precio con descuento (Sin IVA)"]) && $row["Precio con descuento (Sin IVA)"] != 0
                    ? $row["Precio con descuento (Sin IVA)"] * 1.13
                    : 0),
            'cost' => $row["Costo de Compra"] ?? 0,
            'tributos' => '["20"]',
            'stockInicial' => 0, // Ya no se usa, se maneja por sucursal
            'stockActual' => 0, // Ya no se usa, se maneja por sucursal
            'stockMinimo' => 1,
            'has_stock' => $hasStock,
            'is_global' => !$hasStock, // Si no maneja stock, es global automáticamente
            'image_url' => null,
            'category_id' => null
        ]);

        // Después de que el producto se guarde o actualice (por WithUpserts),
        // necesitamos crear/actualizar el registro en BranchProductStock
        // Pero como estamos usando ToModel, necesitamos hacerlo en un hook posterior
        // Por ahora, guardamos esta información para procesarla después
        DB::afterCommit(function () use ($product, $stockInicial, $hasStock) {
            // Buscar el producto guardado
            $savedProduct = BusinessProduct::where('business_id', $this->business_id)
                ->where('codigo', $product->codigo)
                ->where('descripcion', $product->descripcion)
                ->first();

            if ($savedProduct) {
                if ($hasStock) {
                    // Si maneja stock, crear/actualizar registro en BranchProductStock
                    BranchProductStock::updateOrCreate(
                        [
                            'business_product_id' => $savedProduct->id,
                            'sucursal_id' => $this->sucursal_id,
                        ],
                        [
                            'stockActual' => $stockInicial,
                            'stockMinimo' => $savedProduct->stockMinimo,
                            'estado_stock' => $stockInicial > $savedProduct->stockMinimo ? 'disponible' : 
                                            ($stockInicial > 0 ? 'por_agotarse' : 'agotado'),
                        ]
                    );
                } else {
                    // Si no maneja stock, asegurarse de que el producto sea global
                    if (!$savedProduct->is_global) {
                        $savedProduct->is_global = true;
                        $savedProduct->save();
                    }
                }
            }
        });

        return $product;
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

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 500; // The size of each chunk for reading the file
    }

    /**
     * @return int
     */
    public function batchSize(): int
    {
        return 500; // The size of each batch for inserting the records
    }
}