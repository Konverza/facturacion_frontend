<?php

namespace App\Http\Controllers\Business;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

use App\Services\BotmakerService;
class WhatsAppController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            "codGeneracion" => "required|string",
            "phone" => "required|string",
        ]);

        $phone = $request->phone;
        $codGeneracion = $request->codGeneracion;

        if (substr($phone, 0, 3) != "503") {
            return back()->with("error", "Error")->with("error_message", "El número de teléfono debe contener el código de país 503");
        }

        $dte = Http::get(env("OCTOPUS_API_URL") . '/dtes/' . $codGeneracion)->json();
        if ($dte) {
            $documento = json_decode($dte['documento'], true);
            $data = [
                "phone" => $phone,
                "receptor" => $dte["tipo_dte"] == "14" ? $documento["sujetoExcluido"]["nombre"] : $documento["receptor"]["nombre"],
                "empresa" => $documento["emisor"]["nombre"],
                "codGeneracion" => $codGeneracion,
                "numeroControl" => $documento["identificacion"]["numeroControl"],
                "selloRecepcion" => $dte["selloRecibido"],
                "fhProcesamiento" => \Carbon\Carbon::parse($dte["fhProcesamiento"])->format("d/m/Y H:i:s"),
                "enlace_json" => $dte["enlace_json"],
                "enlace_pdf" => $dte["enlace_pdf"],
            ];

            $botmaker = new BotmakerService();
            $response = $botmaker->enviar_whatsapp($data);

            if ($response["problems"] == null) {
                return back()->with("success", "Exito")->with("success_message", "Mensaje enviado correctamente");
            } else {
                return back()->with("error", "Error")->with("error_message", "No se pudo enviar el mensaje");
            }
        } else {
            return back()->with("error", "Error")->with("error_message", "No se pudo enviar el mensaje");
        }
    }
}
