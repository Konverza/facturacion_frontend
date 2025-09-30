<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomerListImport implements ToCollection, WithHeadingRow
{
    /**
     * Items resultantes normalizados
     * @var array<int, array<string, mixed>>
     */
    protected array $items = [];

    /**
     * Devuelve los clientes parseados.
     * Estructura por item:
     * [
     *   'tipoDocumento','numDocumento','nrc','nombre','nombreComercial','codActividad',
     *   'departamento','municipio','complemento','telefono','correo','pais','tipoPersona'
     * ]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Normaliza clave de encabezado a snake_case seguro.
     */
    protected function norm(string $key): string
    {
        // Reemplaza caracteres especiales y espacios por guiones bajos
        $k = Str::of($key)->lower();
        $k = Str::of(iconv('UTF-8', 'ASCII//TRANSLIT', (string)$k))->lower();
        $k = Str::of((string)$k)->replace(['-', '/', '\\'], ' ')->replace('  ', ' ');
        return Str::of((string)$k)->trim()->replace(' ', '_')->value();
    }

    /**
     * Busca el primer valor no vacío entre varias posibles claves del row
     * devolviendo null si no encuentra.
     */
    protected function pick(array $row, array $candidates): mixed
    {
        foreach ($candidates as $c) {
            if (array_key_exists($c, $row)) {
                $val = $row[$c];
                if ($val !== null && $val !== '') {
                    return is_string($val) ? trim($val) : $val;
                }
            }
        }
        return null;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Normalizar claves
            $raw = [];
            foreach ($row->toArray() as $k => $v) {
                if ($k === null) { continue; }
                $raw[$this->norm((string)$k)] = is_string($v) ? trim($v) : $v;
            }

            $item = [
                'tipoDocumento'   => $this->pick($raw, ['tipo_de_documento', 'tipo_documento', 'doc_tipo', 'tipoid']),
                'numDocumento'    => $this->pick($raw, ['numero_de_documento', 'numero_documento', 'num_documento', 'documento']),
                'nrc'             => $this->pick($raw, ['nrc', 'nrc_solo_si_es_contribuyente']),
                'nombre'          => $this->pick($raw, ['nombre_completo_razon_social_o_denominacion', 'nombre', 'razon_social', 'denominacion']),
                'nombreComercial' => $this->pick($raw, ['nombre_comercial', 'nombre_comercial_si_aplica']),
                'codActividad'    => $this->pick($raw, ['actividad_economica', 'cod_actividad']),
                'departamento'    => $this->pick($raw, ['departamento', 'depto']),
                'municipio'       => $this->pick($raw, ['municipio']),
                'complemento'     => $this->pick($raw, ['direccion', 'direccion_complemento', 'complemento']),
                'telefono'        => $this->pick($raw, ['telefono', 'tel']),
                'correo'          => $this->pick($raw, ['correo_electronico', 'correo', 'email']),
                // Campos usados en exportación
                'pais'            => $this->pick($raw, ['pais_clientes_exportacion', 'pais']),
                'tipoPersona'     => $this->pick($raw, ['tipo_de_persona_clientes_exportacion', 'tipo_persona']),
            ];

            // Filtrar filas vacías (sin documento ni nombre)
            if (empty($item['numDocumento']) && empty($item['nombre'])) {
                continue;
            }

            $this->items[] = $item;
        }
    }
}
