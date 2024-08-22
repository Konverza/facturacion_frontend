<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartamentosController extends Controller
{
    public function getDepartamentos(){
        $results = DB::table("cat_012")
            ->select('codigo', 'valores')
            ->get();
        return response()->json($results);
    }

    public function getMunicipios($departamento){
        $results = DB::table("cat_013")
            ->select('codigo', 'valores')
            ->where('departamento', '=', $departamento)
            ->get();
        return response()->json($results);
    }

    public function getAll(){
        $departamentos = DB::table("cat_012")
            ->select('codigo', 'valores')
            ->get();
        $municipios = DB::table("cat_013")
            ->select('codigo', 'valores', 'departamento')
            ->get();
        # Append municipios to departamentos array
        foreach($departamentos as $departamento){
            $departamento->municipios = [];
            foreach($municipios as $municipio){
                if($municipio->departamento == $departamento->codigo){
                    array_push($departamento->municipios, $municipio);
                }
            }
        }

        return response()->json($departamentos);
    }
}
