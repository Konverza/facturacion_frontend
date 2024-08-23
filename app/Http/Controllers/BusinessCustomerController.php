<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\BusinessUser;
use App\Models\BusinessCustomer;

class BusinessCustomerController extends Controller
{
    public function index()
    {
        $business_user = BusinessUser::where('user_id', auth()->id())->first();
        $business_customers = BusinessCustomer::where('business_id', $business_user->business_id)->get();

        $tiposDocs = DB::table('cat_022')
            ->select('codigo', 'valores')
            ->get();

        return view('business.clientes', compact('business_customers', 'tiposDocs'));
    }

    public function store(Request $request)
    {
        try{
            $business_user = BusinessUser::where('user_id', auth()->id())->first();
            $business_id = $business_user->business_id;
            $business_customer = new BusinessCustomer([
                'business_id' => $business_id,
                'tipoDocumento' => $request->tipoDocumento,
                'numDocumento' => str_replace('-', '', $request->numDocumento),
                'nrc' => $request->nrc ?? null,
                'nombre' => $request->nombre,
                'codActividad' => $request->codActividad ? explode(' - ', $request->codActividad)[0] : null,
                'nombreComercial' => $request->nombreComercial ?? null,
                'departamento' => $request->departamento,
                'municipio' => $request->municipio,
                'complemento' => $request->complemento,
                'telefono' => $request->telefono,
                'correo' => $request->correo,
                'codPais' => $request->codPais ? explode(' - ', $request->codPais)[0] : null,
                'tipoPersona' => $request->tipoPersona,
            ]);
            $business_customer->save();

            return response()->json(['success' => 'Cliente registrado exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al registrar cliente']);
        }
    }
}
