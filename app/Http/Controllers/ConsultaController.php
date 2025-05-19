<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OctopusService;

class ConsultaController extends Controller
{
    public function index()
    {
        return view('consulta');
    }

    public function search(Request $request)
    {
        $codGeneracion = $request->input('codGeneracion');
        return redirect()->route('consulta.show', ['codGeneracion' => $codGeneracion]);
    }

    public function show($codGeneracion)
    {
        $octopusService = new OctopusService();
        $dte = $octopusService->get("/dtes/$codGeneracion");
        if($dte){
            if(isset($dte["detail"])){
                return redirect()->route('consulta')->with([
                    "error" => "Error",
                    "error_message" => $dte["detail"]
                ]);
            } else {
                $dte["documento"] = json_decode($dte["documento"], true);
            }
        }
        $tipos_dte = $octopusService->getCatalog("CAT-002");
        return view('consulta' , ['dte' => $dte, 'codGeneracion' => $codGeneracion, 'tipos_dte' => $tipos_dte]);
    }
}
