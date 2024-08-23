<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use App\Models\Business;
use App\Models\BusinessCustomer;
use App\Models\BusinessUser;
use App\Models\BusinessProduct;


class BusinessController extends Controller
{
    public function dashboard()
    {
        $business_user = BusinessUser::where('user_id', auth()->id())->first();
        $business = Business::find($business_user->business_id);
        $statistics = Http::get(env("OCTOPUS_API_URL").'/dtes/statistics/?nit='.$business->nit)->json();
        $datos_empresa = Http::get(env("OCTOPUS_API_URL").'/datos_empresa/nit/'.$business->nit)->json();
        $dtes = Http::get(env("OCTOPUS_API_URL").'/dtes/?nit='.$business->nit)->json();
        $pruebas = Http::get(env("OCTOPUS_API_URL").'/datos_empresa/pruebas/'.$business->nit)->json();
        $productos = BusinessProduct::where('business_id', $business->id)->count('id');
        $customers = BusinessCustomer::where('business_id', $business->id)->count('id');
        return view('business.dashboard', compact('statistics', 'datos_empresa', 'dtes', 'pruebas', 'productos', 'customers'));
    }

    public function factura()
    {
        $business_user = BusinessUser::where('user_id', auth()->id())->first();
        $business = Business::find($business_user->business_id);
        $datos_empresa = Http::get(env("OCTOPUS_API_URL").'/datos_empresa/nit/'.$business->nit)->json();
        return view('business.factura', compact('datos_empresa'));
    }

    public function sucursales()
    {
        return view('business.sucursales');
    }

    public function dtes()
    {
        $business_user = BusinessUser::where('user_id', auth()->id())->first();
        $business = Business::find($business_user->business_id);
        $response = Http::get(env("OCTOPUS_API_URL").'/dtes/?nit=' . $business->nit);
        $dtes = $response->json();

        // Check query params to filter invoices
        if (request()->has('type')) {
            $dtes = array_filter($dtes, function ($dte) {
                return $dte['estado'] == request('type');
            });
        }

        $dtes = array_map(function ($dte) {
            $dte['documento'] = json_decode($dte["documento"]);
            return $dte;
        }, $dtes);

        return view('business.invoices', ['invoices' => $dtes]);
    }

    public function send_dte(Request $request)
    {
        // Send the same request to the API
        $response = Http::post(env("OCTOPUS_API_URL").'/factura/', $request->all());
        // Redirect back with a success message
        return response()->json([
            "status" => $response->status(),
            "message" => $response->json()
        ]);
    }
}
