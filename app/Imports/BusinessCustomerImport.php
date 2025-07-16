<?php

namespace App\Imports;

use App\Models\BusinessCustomer;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');

class BusinessCustomerImport implements ToModel, WithHeadingRow, WithUpserts, WithChunkReading, WithBatchInserts
{
    private $business_id;
    private $tipo_documentos;
    private $tipo_personas;
    private $cod_actividades;
    private $cod_paises;
    private $departamentos;
    /**
     * BusinessCustomerImport constructor.
     *
     * @param int $business_id
     * @param array $tipo_documentos
     * @param array $tipo_personas
     * @param array $cod_actividades
     * @param array $cod_paises
     * @param array $departamentos
     */
    public function __construct(
        int $business_id,
        array $tipo_documentos,
        array $tipo_personas,
        array $cod_actividades,
        array $cod_paises,
        array $departamentos
    ) {
        $this->business_id = $business_id;
        $this->tipo_documentos = $tipo_documentos;
        $this->tipo_personas = $tipo_personas;
        $this->cod_actividades = $cod_actividades;
        $this->cod_paises = $cod_paises;
        $this->departamentos = $departamentos;
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
        $requiredKeys = ["Tipo de Documento", "Número de Documento", "Nombre Completo, Razon Social o Denominacion", "Departamento", "Municipio", "Direccion", "Telefono", "Correo Electronico"];
        $missingKeys = [];
        foreach ($requiredKeys as $key) {
            if (!isset($row[$key]) || empty($row[$key])) {
                $missingKeys[] = $key;
            }
        }
        if (!empty($missingKeys)) {
            Log::warning('Row skipped due to missing required fields', ['missing_keys' => $missingKeys, 'row' => $row]);
            return null; // Skip rows that do not have the required fields
        }

        $tipoDocumento = mb_strtolower($row["Tipo de Documento"]);
        $tipoDocumento = array_search($tipoDocumento, array_map('mb_strtolower', $this->tipo_documentos), true);
        if ($tipoDocumento === false) {
            Log::warning('Row skipped due to invalid Tipo de Documento', ['row' => $row]);
            return null; // Skip rows with invalid Tipo de Documento 
        }

        $tipoPersona = mb_strtolower($row["Tipo de Persona (Clientes Exportacion)"]);
        $tipoPersona = array_search($tipoPersona, array_map('mb_strtolower', $this->tipo_personas), true);


        $actividad = mb_strtolower($row["Actividad Económica"]);
        $codActividad = explode(" - ", $actividad)[0] ?? null;

        $pais = mb_strtolower($row["Pais (Clientes Exportacion)"]);
        $codPais = explode(" - ", $pais)[0] ?? null;

        $departamento = $this->obtenerCodigoDepartamento($this->departamentos, $row["Departamento"]);
        if ($departamento === false) {
            Log::warning('Row skipped due to invalid Departamento', ['row' => $row]);
            return null; // Skip rows with invalid Departamento
        }
        $municipio = $this->obtenerCodigoMunicipio($this->departamentos, $departamento, $row["Municipio"]);
        if ($municipio === false) {
            Log::warning('Row skipped due to invalid Municipio', ['row' => $row]);
            return null; // Skip rows with invalid Municipio
        }


        return new BusinessCustomer([
            'business_id' => $this->business_id,
            'tipoDocumento' => $tipoDocumento,
            'numDocumento' => $row["Número de Documento"],
            'nrc' => $row["NRC"] ?? null,
            'nombre' => $row["Nombre Completo, Razon Social o Denominacion"],
            'codActividad' => $codActividad,
            'nombreComercial' => $row["Nombre Comercial"] ?? null,
            'departamento' => $departamento,
            'municipio' => $municipio,
            'complemento' => $row["Direccion"] ?? null,
            'telefono' => $row["Telefono"] ?? null,
            'correo' => $row["Correo Electronico"] ?? null,
            'codPais' => $codPais ?? null,
            'tipoPersona' => $tipoPersona ?? null,
            'special_price' => isset($row["¿Aplicar Descuento?"]) && mb_strtolower($row["¿Aplicar Descuento?"]) === "sí" ? 1 : 0
        ]);
    }

    // Función para quitar tildes y normalizar
    public function normalizar($texto)
    {
        $texto = mb_strtolower($texto, 'UTF-8');
        $texto = strtr($texto, [
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
            'ñ' => 'n',
            'Á' => 'a',
            'É' => 'e',
            'Í' => 'i',
            'Ó' => 'o',
            'Ú' => 'u',
            'Ñ' => 'n',
        ]);
        return $texto;
    }

    // Función para obtener código de departamento por nombre
    public function obtenerCodigoDepartamento($array, $nombreBuscado)
    {
        $nombreBuscado = $this->normalizar($nombreBuscado);

        foreach ($array as $codigo => $departamento) {
            if ($this->normalizar($departamento['nombre']) === $nombreBuscado) {
                return $codigo;
            }
        }
        return null; // No encontrado
    }

    // Función para obtener código de municipio por nombre y código de departamento
    public function obtenerCodigoMunicipio($array, $codigoDepartamento, $nombreMunicipio)
    {
        if (!isset($array[$codigoDepartamento])) {
            return null;
        }

        $nombreMunicipio = $this->normalizar($nombreMunicipio);
        $municipios = $array[$codigoDepartamento]['municipios'];

        foreach ($municipios as $codigo => $municipio) {
            if ($this->normalizar($municipio['nombre']) === $nombreMunicipio) {
                return $codigo;
            }
        }
        return null; // No encontrado
    }

    public function headingRow(): int
    {
        return 1; // The first row is the heading row
    }

    /**
     * @return string|array
     */
    public function uniqueBy()
    {
        return ['numDocumento', 'nombre', 'business_id'];
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
