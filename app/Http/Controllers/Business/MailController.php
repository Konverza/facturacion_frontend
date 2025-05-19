<?php

namespace App\Http\Controllers\Business;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\DteMail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function send(Request $request)
    {

        $request->validate([
            "codGeneracion" => "required|string",
            "email" => "required|email",
        ]);

        $codGeneracion = $request->codGeneracion;
        $email = $request->email;
        $dte = Http::get(env("OCTOPUS_API_URL") . '/dtes/' . $codGeneracion)->json();

        if ($dte) {
            Mail::to($email)->send(new DteMail($dte, $dte["enlace_pdf"], $dte["enlace_json"]));
            return redirect()->route("business.documents.index")
                ->with([
                    "success" => "Correo enviado",
                    "success_message" => "El correo fue enviado correctamente"
                ]);
        } else {
            return redirect()->route("business.documents.index")
                ->with([
                    "error" => "Error al enviar correo",
                    "error_message" => "No se pudo enviar el correo"
                ]);
        }
    }
}
