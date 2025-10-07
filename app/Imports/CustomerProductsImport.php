<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Import que combina información de cliente y producto en cada fila.
 * Cada fila representa UNA línea de producto para un cliente. Clientes repetidos
 * (según tipoDocumento+numDocumento o nombre) se agrupan luego en el controlador.
 */
class CustomerProductsImport implements ToCollection, WithHeadingRow
{
    /** @var array<int, array<string,mixed>> */
    protected array $rows = [];
    /** @var array<int, array<string,mixed>> */
    protected array $discarded = [];
    protected string $type;

    public function __construct(string $type = '01')
    {
        $this->type = $type; // para validaciones específicas de CCF (03)
    }

    public function getRows(): array
    {
        return $this->rows;
    }

    public function getDiscarded(): array
    {
        return $this->discarded;
    }

    /** Normaliza encabezados a snake_case seguro */
    protected function norm(string $key): string
    {
        $k = Str::of($key)->lower();
        $k = Str::of(iconv('UTF-8', 'ASCII//TRANSLIT', (string)$k))->lower();
        $k = Str::of((string)$k)->replace(['-', '/', '\\'], ' ')->replace('  ', ' ');
        return Str::of((string)$k)->trim()->replace(' ', '_')->value();
    }

    /** Devuelve primer valor no vacío entre candidatos */
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
        $rowIndex = 1; // para referencia (encabezado ya normalizado por WithHeadingRow)
        foreach ($rows as $row) {
            $raw = [];
            foreach ($row->toArray() as $k => $v) {
                if ($k === null) { continue; }
                $raw[$this->norm((string)$k)] = is_string($v) ? trim($v) : $v;
            }

            $entry = [
                // Cliente
                'tipoDocumento'   => $this->pick($raw, ['tipo_de_documento','tipo_documento','doc_tipo','tipoid']),
                'numDocumento'    => $this->pick($raw, ['numero_de_documento','numero_documento','num_documento','documento']),
                'nrc'             => $this->pick($raw, ['nrc','nrc_solo_si_es_contribuyente']),
                'nombre'          => $this->pick($raw, ['nombre_completo_razon_social_o_denominacion','nombre','razon_social','denominacion']),
                'nombreComercial' => $this->pick($raw, ['nombre_comercial','nombre_comercial_si_aplica']),
                'codActividad'    => $this->pick($raw, ['actividad_economica','cod_actividad']),
                'departamento'    => $this->pick($raw, ['departamento','depto']),
                'municipio'       => $this->pick($raw, ['municipio']),
                'complemento'     => $this->pick($raw, ['direccion','direccion_complemento','complemento']),
                'telefono'        => $this->pick($raw, ['telefono','tel']),
                'correo'          => $this->pick($raw, ['correo_electronico','correo','email']),
                'pais'            => $this->pick($raw, ['pais_clientes_exportacion','pais']),
                'tipoPersona'     => $this->pick($raw, ['tipo_de_persona_clientes_exportacion','tipo_persona']),
                // Producto
                'tipo_item_txt'        => $this->pick($raw, ['tipo_de_item','tipo_item','tipoitem']),
                'unidad_medida_txt'    => $this->pick($raw, ['unidad_de_medida','unidad_medida','unidad']),
                'cantidad'             => $this->pick($raw, ['cantidad','qty','cantidad_producto']),
                'precio_unitario_sin_iva' => $this->pick($raw, ['precio_unitario_(sin_iva)','precio_unitario_sin_iva','precio_sin_iva','precio_unitario_base']),
                'descripcion'          => $this->pick($raw, ['descripcion','descripcion_producto','detalle']),
                'tipo_venta_txt'       => $this->pick($raw, ['tipo_de_venta','tipo_venta','naturaleza_venta']),
            ];

            $reasons = [];
            $allNull = collect($entry)->filter(fn($v) => $v !== null && $v !== '')->isEmpty();
            if ($allNull) {
                $reasons[] = 'Fila vacía';
            }

            if (empty($entry['descripcion'])) { $reasons[] = 'Descripción faltante'; }
            if (empty($entry['cantidad'])) { $reasons[] = 'Cantidad faltante'; }
            if (empty($entry['precio_unitario_sin_iva'])) { $reasons[] = 'Precio unitario faltante'; }

            // Validaciones específicas Crédito Fiscal (03)
            if ($this->type === '03') {
                if (empty($entry['nrc'])) { $reasons[] = 'NRC requerido para Crédito Fiscal'; }
                if (empty($entry['codActividad'])) { $reasons[] = 'Actividad económica requerida para Crédito Fiscal'; }
            }

            // Validar cantidad y precio > 0
            if (!empty($entry['cantidad']) && (float)$entry['cantidad'] <= 0) { $reasons[] = 'Cantidad debe ser > 0'; }
            if (!empty($entry['precio_unitario_sin_iva']) && (float)$entry['precio_unitario_sin_iva'] <= 0) { $reasons[] = 'Precio unitario debe ser > 0'; }

            if (count($reasons) > 0) {
                $this->discarded[] = [
                    'row' => $rowIndex + 1, // +1 para reflejar fila real en Excel incluyendo encabezados
                    'data' => $entry,
                    'reasons' => $reasons,
                ];
            } else {
                $this->rows[] = $entry;
            }
            $rowIndex++;
        }
    }
}
