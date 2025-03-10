<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class BotmakerService
{
    protected $botmaker_url;

    public function __construct()
    {
        $this->botmaker_url = env("BOTMAKER_API_URL");
    }

    public function enviar_whatsapp($data)
    {
        $response = Http::withHeaders([
            "Content-Type" => "application/json",
            "Accept" => "application/json",
            "access-token" => env("BOTMAKER_TOKEN")
        ])->post($this->botmaker_url, [
            "chatPlatform" => "whatsapp",
            "chatChannelNumber" => env("BOTMAKER_FROM"),
            "platformContactId" => $data['phone'],
            "ruleNameOrId" => "enviar_dte_1",
            "clientPayload" => "string",
            "params" => [
                "receptor" => $data['receptor'],
                "empresa" => $data['empresa'],
                "codGeneracion" => $data['codGeneracion'],
                "numeroControl" => $data['numeroControl'],
                "selloRecepcion" => $data['selloRecepcion'],
                "fhProcesamiento" => $data['fhProcesamiento'],
                "enlace_json" => $data['enlace_json'],
                "headerDocumentUrl" => $data['enlace_pdf'],
                "headerDocumentCaption" => $data["codGeneracion"].".pdf",
            ]
        ]);

        return $response->json();
    }
}