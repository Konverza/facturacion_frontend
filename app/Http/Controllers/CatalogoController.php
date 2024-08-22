<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatalogoController extends Controller
{
    public function getValues($catalogo)
    {
        if(!str_starts_with($catalogo, 'cat_')){
            return response()->json([]);
        }

        if(!DB::getSchemaBuilder()->hasTable($catalogo)){
            return response()->json([]);
        }

        $results = DB::table($catalogo)
            ->select('codigo', 'valores')
            ->get();

        $resultsJson = $results->map(function ($item) {
            return [
                'value' => $item->codigo,
                'label' => $item->codigo . ' - ' . $item->valores
            ];
        });

        return response()->json($resultsJson);
    }
}
