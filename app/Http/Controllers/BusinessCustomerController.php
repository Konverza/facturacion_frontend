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

    public function show($id){
        $business_customer = BusinessCustomer::find($id);
        return response()->json($business_customer);
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

    public function update(Request $request, $id)
    {
        try{
            $business_customer = BusinessCustomer::find($id);
            $business_customer->tipoDocumento = $request->tipoDocumento;
            $business_customer->numDocumento = str_replace('-', '', $request->numDocumento);
            $business_customer->nrc = $request->nrc ?? null;
            $business_customer->nombre = $request->nombre;
            $business_customer->codActividad = $request->codActividad ? explode(' - ', $request->codActividad)[0] : null;
            $business_customer->nombreComercial = $request->nombreComercial ?? null;
            $business_customer->departamento = $request->departamento;
            $business_customer->municipio = $request->municipio;
            $business_customer->complemento = $request->complemento;
            $business_customer->telefono = $request->telefono;
            $business_customer->correo = $request->correo;
            $business_customer->codPais = $request->codPais ? explode(' - ', $request->codPais)[0] : null;
            $business_customer->tipoPersona = $request->tipoPersona;
            $business_customer->save();

            return response()->json(['success' => 'Cliente actualizado exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar cliente']);
        }
    }

    public function destroy($id)
    {
        try{
            $business_customer = BusinessCustomer::find($id);
            $business_customer->delete();

            return response()->json(['success' => 'Cliente eliminado exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar cliente']);
        }
    }

}
