<?php

namespace App\Http\Controllers;

use App\Mail\DteMail;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;


class MailController extends Controller
{
    public function mandar_correo(Request $request)
    {
        $request->validate([
            'codGeneracion' => 'required|string',
            'correo' => 'required|email',
        ]);

        $codGeneracion = $request->input('codGeneracion');
        $dte = Http::get(env("OCTOPUS_API_URL").'/dtes/' . $codGeneracion)->json();
        $correo = $request->input('correo');

        Mail::to($correo)->send(new DteMail($codGeneracion, $dte["enlace_pdf"], $dte["enlace_json"]));

        return redirect()->route('business.dtes')->with('success', 'Correo enviado');
    }
}
