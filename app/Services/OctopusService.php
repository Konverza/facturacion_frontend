<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class OctopusService
{
    protected $octopus_cats_url;
    protected $octopus_url;

    public function __construct()
    {
        $this->octopus_cats_url = env("OCTOPUS_CATS_URL");
        $this->octopus_url = env("OCTOPUS_API_URL");
    }

    public function getCatalog(string $catalogCode, $value = null, $diff = false, $doble_value = false)
    {
        $cacheKey = "octopus_catalog_$catalogCode";
        try {
            $values = Cache::remember($cacheKey, now()->addHours(6), function () use ($catalogCode) {
                $response = Http::timeout(30)->get($this->octopus_cats_url . $catalogCode);
                $data = $response->json();
                return $data['values'] ?? $data ?? [];
            });

            switch ($catalogCode) {
                case "CAT-012":
                case "CAT-013":
                    if ($value) {
                        return $this->getMunicipiosFromDepartamentos($values, $value);
                    }
                    return $this->formatCatalog($values, 'codigo', 'nombre', $diff, $doble_value);

                case "CAT-019":
                case "CAT-025":
                case "CAT-028":
                case "CAT-027":
                case "CAT-017":
                case "CAT-010":
                case "CAT-030":
                case "CAT-031":
                    return $this->formatCatalog($values, 'code', 'value', $diff, $doble_value);
                case "CAT-002":
                case "CAT-009":
                case "CAT-014":
                case "CAT-022":
                case "CAT-020":
                    return $this->formatCatalog($values, 'code', 'value', $diff, $doble_value);
                default:
                    throw new \InvalidArgumentException("Invalid catalog code: $catalogCode");
            }
        } catch (\Exception $e) {
            return redirect()->back()->with([
                "error" => "Error",
                "error_message" => "Error al obtener los datos" . $e->getMessage()
            ]);
        }
    }

    public function get($url)
    {
        try {
            $response = Http::timeout(30)->get($this->octopus_url . $url);
            return $response->json();
        } catch (\Exception $e) {
            return redirect()->back()->with([
                "error" => "Error",
                "error_message" => "Error al obtener los datos" . $e->getMessage()
            ]);
        }
    }

    private function getMunicipiosFromDepartamentos(array $departamentos, $value)
    {
        $municipios = [];
        foreach ($departamentos as $departamento) {
            if ($departamento['codigo'] == $value) {
                foreach ($departamento['municipios'] as $municipio) {
                    $municipios[$municipio['codigo']] = $municipio['nombre'];
                }
            }
        }
        return $municipios;
    }

    private function formatCatalog(array $data, string $key, $value, $diff, $doble_value)
    {
        $catalog = [];
        foreach ($data as $item) {
            if ($diff) {
                $catalog[$item[$key]] = $item['code'] . " - " . $item[$value];
                continue;
            } else if ($doble_value) {
                $key = $item[$value] . " - " . $item['code'];
                $catalog[$key] = $key;
            } else {
                $catalog[$item[$key]] = $item[$value];
            }
        }
        return $catalog;
    }

    public function simpleDepartamentos()
    {
        $values = Cache::remember("octopus_catalog_departamentos", now()->addHours(6), function () {
            $response = Http::timeout(30)->get("{$this->octopus_cats_url}CAT-012");
            $departamentos = $response->json();
            $resultado = [];

            foreach ($departamentos as $departamento) {
                $codigoDepto = $departamento['codigo'];
                $resultado[$codigoDepto] = [
                    'nombre' => $departamento['nombre'],
                    'municipios' => []
                ];

                foreach ($departamento['municipios'] as $municipio) {
                    $codigoMunicipio = $municipio['codigo'];
                    $resultado[$codigoDepto]['municipios'][$codigoMunicipio] = [
                        'nombre' => $municipio['nombre'],
                        'distritos' => []
                    ];

                    foreach ($municipio['distritos'] as $distrito) {
                        // Se puede usar el mismo código del municipio para el distrito si no hay código de distrito.
                        $codigoDistrito = $codigoMunicipio;
                        $resultado[$codigoDepto]['municipios'][$codigoMunicipio]['distritos'][$codigoDistrito] = [
                            'nombre' => $distrito['nombre']
                        ];
                    }
                }
            }

            return $resultado ?: [];
        });

        return $values;
    }
}
