<?php

namespace App\Imports;

use App\Models\BusinessCustomer;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');

class BusinessCustomerImport implements ToCollection, WithHeadingRow, WithMultipleSheets
{
    private int $business_id;
    private array $tipo_documentos;
    private array $tipo_personas;
    private array $cod_actividades;
    private array $cod_paises;
    private array $departamentos;
    private string $mode;
    private array $update_fields;
    private array $errors = [];
    private array $summary = [
        'processed' => 0,
        'inserted' => 0,
        'updated' => 0,
        'skipped' => 0,
        'errors' => 0,
    ];

    private const FIELD_MAP = [
        'tipoDocumento' => ['Tipo de Documento'],
        'numDocumento' => ['Numero de Documento', 'Número de Documento'],
        'nrc' => ['NRC (Solo si es contribuyente)'],
        'nombre' => ['Nombre Completo, Razon Social o Denominacion'],
        'codActividad' => ['Actividad Economica', 'Actividad Económica'],
        'nombreComercial' => ['Nombre Comercial (si aplica)'],
        'departamento' => ['Departamento'],
        'municipio' => ['Municipio'],
        'complemento' => ['Direccion', 'Dirección'],
        'telefono' => ['Telefono', 'Teléfono'],
        'correo' => ['Correo Electronico', 'Correo Electrónico'],
        'codPais' => ['Pais (Clientes Exportacion)', 'País (Clientes Exportación)'],
        'tipoPersona' => ['Tipo de Persona (Clientes Exportacion)', 'Tipo de Persona (Clientes Exportación)'],
        'special_price' => ['¿Aplicar Descuento?', 'Aplicar Descuento'],
    ];

    private const REQUIRED_FULL_FIELDS = [
        'tipoDocumento',
        'numDocumento',
        'nombre',
        'departamento',
        'municipio',
        'complemento',
        'telefono',
        'correo',
    ];

    public function __construct(
        int $business_id,
        array $tipo_documentos,
        array $tipo_personas,
        array $cod_actividades,
        array $cod_paises,
        array $departamentos,
        string $mode = 'full',
        array $update_fields = []
    ) {
        $this->business_id = $business_id;
        $this->tipo_documentos = $tipo_documentos;
        $this->tipo_personas = $tipo_personas;
        $this->cod_actividades = $cod_actividades;
        $this->cod_paises = $cod_paises;
        $this->departamentos = $departamentos;
        $this->mode = $mode === 'partial' ? 'partial' : 'full';
        $this->update_fields = collect($update_fields)
            ->filter(fn ($field) => is_string($field) && isset(self::FIELD_MAP[$field]) && $field !== 'numDocumento')
            ->values()
            ->all();
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $excelRow = $index + 2;
            $normalizedRow = $this->normalizeRow($row->toArray());

            if ($this->isEmptyRow($normalizedRow)) {
                continue;
            }

            $this->summary['processed']++;

            $action = $this->mode === 'partial'
                ? $this->processPartial($normalizedRow, $excelRow)
                : $this->processFull($normalizedRow, $excelRow);

            if ($action === 'inserted') {
                $this->summary['inserted']++;
            } elseif ($action === 'updated') {
                $this->summary['updated']++;
            } else {
                $this->summary['skipped']++;
            }
        }
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function sheets(): array
    {
        return [
            'Datos' => $this,
        ];
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getSummary(): array
    {
        return $this->summary;
    }

    private function processFull(array $row, int $excelRow): string
    {
        $data = $this->extractData($row);
        $missingFields = [];

        foreach (self::REQUIRED_FULL_FIELDS as $field) {
            if ($this->isEmpty($data[$field] ?? null)) {
                $missingFields[] = $this->fieldLabel($field);
            }
        }

        if (!empty($missingFields)) {
            $this->addError($excelRow, $data['numDocumento'] ?? null, 'Faltan campos requeridos: ' . implode(', ', $missingFields));
            return 'skipped';
        }

        $tipoDocumento = $this->resolveTipoDocumento($data['tipoDocumento']);
        if ($tipoDocumento === null) {
            $this->addError($excelRow, $data['numDocumento'], 'Tipo de Documento invalido');
            return 'skipped';
        }

        $numDocumento = $this->normalizeAndValidateDocument($data['numDocumento'], $tipoDocumento);
        if ($numDocumento === null) {
            $mensaje = $tipoDocumento === '36'
                ? 'NIT invalido: debe contener 14 digitos despues de normalizar'
                : ($tipoDocumento === '13'
                    ? 'DUI invalido: debe contener 9 digitos despues de normalizar'
                    : 'Numero de Documento invalido');
            $this->addError($excelRow, $data['numDocumento'], $mensaje);
            return 'skipped';
        }

        $departamento = $this->obtenerCodigoDepartamento($this->departamentos, $data['departamento']);
        if ($departamento === null) {
            $this->addError($excelRow, $numDocumento, 'Departamento invalido');
            return 'skipped';
        }

        $municipio = $this->obtenerCodigoMunicipio($this->departamentos, $departamento, $data['municipio']);
        if ($municipio === null) {
            $this->addError($excelRow, $numDocumento, 'Municipio invalido');
            return 'skipped';
        }

        $payload = [
            'tipoDocumento' => $tipoDocumento,
            'numDocumento' => $numDocumento,
            'nrc' => $this->isEmpty($data['nrc']) ? null : str_replace('-', '', trim((string) $data['nrc'])),
            'nombre' => trim((string) $data['nombre']),
            'codActividad' => $this->resolveActividad($data['codActividad']),
            'nombreComercial' => $this->isEmpty($data['nombreComercial']) ? null : trim((string) $data['nombreComercial']),
            'departamento' => $departamento,
            'municipio' => $municipio,
            'complemento' => trim((string) $data['complemento']),
            'telefono' => trim((string) $data['telefono']),
            'correo' => trim((string) $data['correo']),
            'codPais' => $this->resolvePais($data['codPais']),
            'tipoPersona' => $this->resolveTipoPersona($data['tipoPersona']),
            'special_price' => $this->resolveSpecialPrice($data['special_price']),
        ];

        $existingCustomer = $this->findCustomerByDocument($numDocumento);

        if ($existingCustomer) {
            $existingCustomer->update($payload);
            return 'updated';
        }

        BusinessCustomer::create(array_merge($payload, [
            'business_id' => $this->business_id,
        ]));

        return 'inserted';
    }

    private function processPartial(array $row, int $excelRow): string
    {
        $data = $this->extractData($row);

        if ($this->isEmpty($data['numDocumento'] ?? null)) {
            $this->addError($excelRow, null, 'El campo Numero de Documento es obligatorio');
            return 'skipped';
        }

        $customer = $this->findCustomerByDocument((string) $data['numDocumento']);
        if (!$customer) {
            $this->addError($excelRow, (string) $data['numDocumento'], 'No se pudo actualizar: el cliente no existe en este negocio');
            return 'skipped';
        }

        if (in_array((string) $customer->tipoDocumento, ['13', '36'], true)) {
            $normalizedLookupDocument = $this->normalizeAndValidateDocument($data['numDocumento'], (string) $customer->tipoDocumento);
            if ($normalizedLookupDocument === null) {
                $mensaje = $customer->tipoDocumento === '36'
                    ? 'NIT invalido: debe contener 14 digitos despues de normalizar'
                    : 'DUI invalido: debe contener 9 digitos despues de normalizar';
                $this->addError($excelRow, (string) $data['numDocumento'], $mensaje);
                return 'skipped';
            }
        }

        $payload = [];
        $newDepartamento = null;

        foreach ($this->update_fields as $field) {
            $value = $data[$field] ?? null;
            if ($this->isEmpty($value)) {
                continue;
            }

            if ($field === 'tipoDocumento') {
                $tipoDocumento = $this->resolveTipoDocumento($value);
                if ($tipoDocumento === null) {
                    $this->addError($excelRow, $customer->numDocumento, 'Tipo de Documento invalido');
                    return 'skipped';
                }
                $payload['tipoDocumento'] = $tipoDocumento;
                continue;
            }

            if ($field === 'nrc') {
                $payload['nrc'] = str_replace('-', '', trim((string) $value));
                continue;
            }

            if ($field === 'codActividad') {
                $payload['codActividad'] = $this->resolveActividad($value);
                continue;
            }

            if ($field === 'codPais') {
                $payload['codPais'] = $this->resolvePais($value);
                continue;
            }

            if ($field === 'tipoPersona') {
                $payload['tipoPersona'] = $this->resolveTipoPersona($value);
                continue;
            }

            if ($field === 'special_price') {
                $payload['special_price'] = $this->resolveSpecialPrice($value);
                continue;
            }

            if ($field === 'departamento') {
                $newDepartamento = $this->obtenerCodigoDepartamento($this->departamentos, (string) $value);
                if ($newDepartamento === null) {
                    $this->addError($excelRow, $customer->numDocumento, 'Departamento invalido');
                    return 'skipped';
                }
                $payload['departamento'] = $newDepartamento;
                continue;
            }

            if ($field === 'municipio') {
                $departamentoToUse = $newDepartamento ?? $customer->departamento;
                $municipio = $this->obtenerCodigoMunicipio($this->departamentos, $departamentoToUse, (string) $value);
                if ($municipio === null) {
                    $this->addError($excelRow, $customer->numDocumento, 'Municipio invalido');
                    return 'skipped';
                }
                $payload['municipio'] = $municipio;
                continue;
            }

            $payload[$field] = trim((string) $value);
        }

        if (empty($payload)) {
            $this->addError($excelRow, $customer->numDocumento, 'No se encontraron valores para los campos seleccionados');
            return 'skipped';
        }

        $customer->update($payload);
        return 'updated';
    }

    private function extractData(array $row): array
    {
        $data = [];
        foreach (self::FIELD_MAP as $field => $labels) {
            $value = null;
            foreach ($labels as $label) {
                $headerKey = $this->normalizeHeader($label);
                if (array_key_exists($headerKey, $row)) {
                    $value = $row[$headerKey];
                    break;
                }
            }
            $data[$field] = is_string($value) ? trim($value) : $value;
        }

        return $data;
    }

    private function normalizeRow(array $row): array
    {
        $normalized = [];
        foreach ($row as $key => $value) {
            if ($key === null) {
                continue;
            }
            $normalized[$this->normalizeHeader((string) $key)] = is_string($value) ? trim($value) : $value;
        }
        return $normalized;
    }

    private function normalizeHeader(string $text): string
    {
        $normalized = Str::of($text)
            ->lower()
            ->ascii()
            ->replaceMatches('/[^a-z0-9]+/', '')
            ->value();

        return $normalized;
    }

    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $value) {
            if (!$this->isEmpty($value)) {
                return false;
            }
        }
        return true;
    }

    private function isEmpty($value): bool
    {
        if ($value === null) {
            return true;
        }
        if (is_string($value)) {
            return trim($value) === '';
        }
        return false;
    }

    private function fieldLabel(string $field): string
    {
        return self::FIELD_MAP[$field][0] ?? $field;
    }

    private function resolveTipoDocumento($value): ?string
    {
        if ($this->isEmpty($value)) {
            return null;
        }

        $raw = trim((string) $value);
        if (array_key_exists($raw, $this->tipo_documentos)) {
            return (string) $raw;
        }

        $needle = mb_strtolower($raw);
        foreach ($this->tipo_documentos as $code => $label) {
            if (mb_strtolower((string) $label) === $needle) {
                return (string) $code;
            }
        }

        return null;
    }

    private function normalizeAndValidateDocument($document, string $tipoDocumento): ?string
    {
        if ($this->isEmpty($document)) {
            return null;
        }

        $document = trim((string) $document);

        if ($tipoDocumento === '13' || $tipoDocumento === '36') {
            $digits = preg_replace('/\D+/', '', $document);
            $expectedLength = $tipoDocumento === '36' ? 14 : 9;

            if (strlen($digits) !== $expectedLength) {
                return null;
            }

            return $digits;
        }

        return $document;
    }

    private function resolveTipoPersona($value): ?string
    {
        if ($this->isEmpty($value)) {
            return null;
        }

        $raw = trim((string) $value);
        if (array_key_exists($raw, $this->tipo_personas)) {
            return (string) $raw;
        }

        $needle = mb_strtolower($raw);
        foreach ($this->tipo_personas as $code => $label) {
            if (mb_strtolower((string) $label) === $needle) {
                return (string) $code;
            }
        }

        return null;
    }

    private function resolveActividad($value): ?string
    {
        if ($this->isEmpty($value)) {
            return null;
        }

        $raw = trim((string) $value);
        $candidate = explode(' - ', $raw)[0];
        return trim((string) $candidate) !== '' ? trim((string) $candidate) : null;
    }

    private function resolvePais($value): ?string
    {
        if ($this->isEmpty($value)) {
            return null;
        }

        $raw = trim((string) $value);
        $candidate = explode(' - ', $raw)[0];
        return trim((string) $candidate) !== '' ? trim((string) $candidate) : null;
    }

    private function resolveSpecialPrice($value): int
    {
        if ($this->isEmpty($value)) {
            return 0;
        }

        $normalized = mb_strtolower(trim((string) $value));
        return in_array($normalized, ['si', 'sí', '1', 'true', 'yes'], true) ? 1 : 0;
    }

    private function findCustomerByDocument(string $document): ?BusinessCustomer
    {
        $document = trim($document);
        if ($document === '') {
            return null;
        }

        $customer = BusinessCustomer::where('business_id', $this->business_id)
            ->where('numDocumento', $document)
            ->first();

        if ($customer) {
            return $customer;
        }

        $digits = preg_replace('/\D+/', '', $document);
        if ($digits === '') {
            return null;
        }

        return BusinessCustomer::where('business_id', $this->business_id)
            ->whereRaw("REPLACE(REPLACE(numDocumento, '-', ''), ' ', '') = ?", [$digits])
            ->first();
    }

    private function addError(int $excelRow, ?string $numDocumento, string $reason): void
    {
        $this->summary['errors']++;
        $this->errors[] = [
            'row' => $excelRow,
            'numDocumento' => $numDocumento,
            'reason' => $reason,
        ];
    }

    public function normalizar($texto)
    {
        $texto = mb_strtolower((string) $texto, 'UTF-8');
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

    public function obtenerCodigoDepartamento($array, $nombreBuscado)
    {
        $nombreBuscado = $this->normalizar($nombreBuscado);

        foreach ($array as $codigo => $departamento) {
            if ($this->normalizar($departamento['nombre']) === $nombreBuscado) {
                return $codigo;
            }
        }
        return null;
    }

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
        return null;
    }
}
