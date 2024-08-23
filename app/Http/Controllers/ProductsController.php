<?php

namespace App\Http\Controllers;

use Bus;
use Illuminate\Http\Request;

use App\Models\BusinessProduct;
use App\Models\BusinessUser;
use App\Models\Tributes;
class ProductsController extends Controller
{
    public function index()
    {
        $tributos = Tributes::all();
        return view('business.productos', compact('tributos'));
    }

    public function store(Request $request)
    {
        $business_user = BusinessUser::where('user_id', auth()->id())->first();
        $business_id = $business_user->business_id;

        $request->validate([
            'tipoItem' => 'required|int',
            'codigo' => 'required|string',
            'uniMedida' => 'required|string',
            'descripcion' => 'required|string',
            'precioUni' => 'required|numeric',
            'precioSinTributos' => 'required|numeric',
            'tributos' => 'required|array',
        ]);

        $product = new BusinessProduct([
            "business_id" => $business_id,
            "tipoItem" => $request->input('tipoItem'),
            "codigo" => $request->input('codigo'),
            "uniMedida" => $request->input('uniMedida'),
            "descripcion" => $request->input('descripcion'),
            "precioUni" => $request->input('precioUni'),
            "precioSinTributos" => $request->input('precioSinTributos'),
            "tributos" => json_encode($request->input('tributos'))
        ]);

        $product->save();
        return response()->json(["success" => true, "message" => "Producto guardado"]);
    }
}
