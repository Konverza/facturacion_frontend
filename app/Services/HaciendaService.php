<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Log;


class HaciendaService
{
    protected $hacienda_auth_url;
    protected $hacienda_obtencion_url;
    protected $nit;


    public function __construct(string $nit)
    {
        $this->hacienda_auth_url = env("HACIENDA_AUTH_URL");
        $this->hacienda_obtencion_url = env("HACIENDA_OBTENCION_URL");
        $this->nit = $nit;
    }

    protected function getAuthToken()
    {
        $nit = $this->nit;
        $hacienda_auth_url = $this->hacienda_auth_url;
        $cacheKey = "hacienda_token_{$nit}";
        
        // Verificar si existe token en caché
        $cachedToken = Cache::get($cacheKey);
        if ($cachedToken) {
            return $cachedToken;
        }
        
        // Obtener credenciales de PRUEBAS
        $username = env("PRUEBAS_USER");
        $password = env("PRUEBAS_PASS");
        $pruebasUrl = env("PRUEBAS_URL");
        
        // Paso 1: Login en PRUEBAS_URL
        $token_response = Http::asForm()->post($pruebasUrl . "/auth/login", [
            "username" => $username,
            "password" => $password
        ]);
        
        if (!$token_response->successful()) {
            Log::error("Error en login PRUEBAS_URL", [
                'status' => $token_response->status(),
                'body' => $token_response->body()
            ]);
            return null;
        }
        
        $token = $token_response->json()['access_token'] ?? null;
        
        if (!$token) {
            Log::error("No se obtuvo access_token de PRUEBAS", [
                'response' => $token_response->json()
            ]);
            return null;
        }
        
        // Paso 2: Obtener credenciales del NIT
        $credentials_response = Http::withToken($token)
            ->get($pruebasUrl . "/credenciales/nit/" . $nit);
            
        if (!$credentials_response->successful()) {
            Log::error("Error al obtener credenciales", [
                'nit' => $nit,
                'status' => $credentials_response->status(),
                'body' => $credentials_response->body()
            ]);
            return null;
        }
        
        $credential = $credentials_response->json();
        $api_password = $credential['api_password'] ?? null;
        
        if (!$api_password) {
            Log::error("No se obtuvo api_password de credenciales", [
                'nit' => $nit,
                'response' => $credential
            ]);
            return null;
        }
        
        // Paso 3: Autenticar con Hacienda
        try {
            $response = Http::timeout(30)->asForm()->post($hacienda_auth_url, [
                'user' => $nit,
                'pwd' => $api_password
            ]);
            
            if (!$response->successful()) {
                Log::error("Error en autenticación con Hacienda", [
                    'nit' => $nit,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $data = $response->json();
            $haciendaToken = $data['body']['token'] ?? null;
            
            if (!$haciendaToken) {
                Log::error("No se obtuvo token de Hacienda", [
                    'nit' => $nit,
                    'response' => $data
                ]);
                return null;
            }
            
            // Remover "Bearer " si viene en el token, ya que withToken() lo agrega automáticamente
            $haciendaToken = str_replace('Bearer ', '', $haciendaToken);
            
            // Guardar en caché por 24 horas
            Cache::put($cacheKey, $haciendaToken, now()->addHours(24));
            
            return $haciendaToken;
        } catch (\Exception $e) {
            Log::error("Excepción al autenticar con Hacienda", [
                'nit' => $nit,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    public function fetchDtes()
    {
        $token = $this->getAuthToken();
        
        if (!$token) {
            Log::error("fetchDtes() falló: no hay token", ['nit' => $this->nit]);
            return [];
        }

        try {
            $response = Http::withToken($token)->timeout(60)->post($this->hacienda_obtencion_url, [
                'nitEmision' => $this->nit,
                'duiEmision' => $this->nit,
                'tipoRpt' => "R"
            ]);

            if (!$response->successful()) {
                Log::error("Error en petición a Hacienda", [
                    'nit' => $this->nit,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return [];
            }

            $data = $response->json();
            $status = $data['status'] ?? 'UNKNOWN';
            $body = $data['body'] ?? [];
            
            return match ($status) {
                'OK' => $body,
                default => [],
            };
        } catch (\Exception $e) {
            Log::error("Error al obtener DTEs desde Hacienda: " . $e->getMessage(), [
                'nit' => $this->nit,
            ]);
            return [];
        }
    }

    public function sendDteToOctopus(array $dte, string $nit)
    {
        try {
            $response = Http::timeout(60)->post(env("OCTOPUS_API_URL") . "/dtes_recibidos/", [
                'nit' => $nit,
                'dte' => $dte,
            ]);
            return $response->json();
        } catch (\Exception $e) {
            return [
                'error' => true,
                'message' => 'Error al enviar el DTE a Octopus: ' . $e->getMessage(),
            ];
        }
    }
}