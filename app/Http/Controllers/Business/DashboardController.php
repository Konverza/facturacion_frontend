<?php

namespace App\Http\Controllers\Business;

use App\Models\AdBanner;
use App\Models\PuntoVenta;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessCustomer;
use App\Models\BusinessPlan;
use App\Models\BusinessProduct;
use App\Models\BusinessUser;
use App\Models\DTE;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public $octopus_url;

    public function __construct()
    {
        $this->octopus_url = env("OCTOPUS_API_URL");
    }

    private $types = [
        '01' => 'Factura Consumidor Final',
        '03' => 'Comprobante de crédito fiscal',
        '04' => 'Nota de Remisión',
        '05' => 'Nota de crédito',
        '06' => 'Nota de débito',
        '07' => 'Comprobante de retención',
        '08' => 'Comprobante de liquidación',
        '09' => 'Documento Contable de Liquidación',
        '11' => 'Factura de exportación',
        '14' => 'Factura de sujeto excluido',
        '15' => 'Comprobante de Donación'
    ];

    public function index()
    {
        try {
            $business_id = Session::get('business') ?? null;
            $user = User::with('businesses.business')->find(auth()->user()->id);
            $business_user = BusinessUser::where("user_id", $user->id)->first();
            $business = Business::find($business_id);
            $business_plan = BusinessPlan::where("nit", $business->nit)->with('plan')->first();
            $dtes_pending = DTE::where('business_id', $business->id);
            if ($user->only_fcf) {
                $dtes_pending = $dtes_pending->where('type', '01');
            } else {
                
            }
            $dtes_pending = $dtes_pending->get();

            if (!$business) {
                return back()->with('error', 'No se encontró la empresa asociada.');
            }

            $inicio_mes = date('Y-m-01');
            $fin_mes = date('Y-m-t');
            $params = [
                'nit' => $business->nit,
                'fechaInicio' => "{$inicio_mes}T00:00:00",
                'fechaFin' => "{$fin_mes}T23:59:59"
            ];
            //Solicitudes a la API
            $statistics = Http::timeout(30)->get($this->octopus_url . '/dtes/statistics/?' . http_build_query($params))
                ->json();
            $statistics_by_dte = Http::timeout(30)->get($this->octopus_url . '/dtes/statistics_by_dte/?nit=' . $business->nit)
                ->json();
            $datos_empresa = Http::timeout(30)->get($this->octopus_url . '/datos_empresa/nit/' . $business->nit)
                ->json();

            // $dtes = $user->only_fcf ? Http::timeout(30)->get("{$this->octopus_url}/dtes/?nit={$business->nit}&limit=5&tipo_dte=01")->json() : Http::timeout(30)->get("{$this->octopus_url}/dtes/?nit={$business->nit}&limit=5")->json();
            $params = [
                "nit" => $business->nit,
                "limit" => 5,
            ];
            if ($user->only_fcf) { $params["tipo_dte"] = "01"; }
            if ($business_user->only_default_pos) {
                $puntoVenta = PuntoVenta::find($business_user->default_pos_id);
                $params["codSucursal"] = $puntoVenta->sucursal->codSucursal;
                $params["codPuntoVenta"] = $puntoVenta->codPuntoVenta;
            }

            
            $dtes = Http::timeout(30)->get($this->octopus_url . '/dtes/', $params)->json();
            // dd($dtes);

            // Datos locales
            $products = BusinessProduct::where('business_id', $business->id)->count('id');
            $customers = BusinessCustomer::where('business_id', $business->id)->count('id');
            $types = $this->types;
            $ads = AdBanner::all();

            return view('business.dashboard.index', compact(
                'statistics',
                'datos_empresa',
                'dtes',
                'products',
                'customers',
                'business_plan',
                'types',
                'dtes_pending',
                'inicio_mes',
                'fin_mes',
                'ads',
                'statistics_by_dte'
            ));
        } catch (\Exception $e) {
            return back()->with('error', 'Error')->with("error_message", "Ha ocurrido un error al cargar los datos. Contacte al administrador." . $e->getMessage());
        }
    }


    public function business()
    {
        $user = auth()->user();

        if (!$user) {
            abort(404, "Usuario no encontrado");
        }

        $user = User::with("businesses.business")->find($user->id);
        if (!$user) {
            return redirect()->route('login')->with([
                'error' => 'Oops!',
                'error_message' => 'Usuario no encontrado'
            ]);
        }

        if (!$user->businesses || $user->businesses->isEmpty()) {
            return redirect()->route('dashboard')->with([
                'error' => 'Oops!',
                'error_message' => 'No tiene empresas asociadas'
            ]);
        }

        $businesses = Business::whereIn(
            'id',
            $user->businesses->pluck('business_id')->toArray()
        )->get();
        return view('business.select-business', compact('businesses'));
    }

    public function selectBusiness(Request $request)
    {
        $request->validate([
            'business' => 'required|exists:business,id',
        ]);

        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login')->with([
                'error' => 'Oops!',
                'error_message' => 'Usuario no encontrado'
            ]);
        }

        $userBusiness = $user->businesses()->where('business_id', $request->business)->exists();
        if (!$userBusiness) {
            return redirect()->route('business.select')->with([
                'error' => 'Oops!',
                'error_message' => 'No tiene permisos para acceder a esta empresa'
            ]);
        }

        $business = Business::find($request->business);
        if (!$business) {
            return redirect()->route('business.select')->with([
                'error' => 'Oops!',
                'error_message' => 'Empresa no encontrada'
            ]);
        }
        if (!$business->active) {
            return redirect()->route('business.select')->with([
                'error' => 'Error',
                'error_message' => 'El negocio se ha desactivado por falta de pago. Por favor, realice su pago y contacte a soporte para reactivarlo.'
            ]);
        }

        Session::forget('sucursal');
        Session::put('business', $request->business);
        return redirect()->route('business.index');
    }

    public function sucursales()
    {
        $business_id = Session::get('business') ?? null;
        $business = Business::find($business_id);
        if (!$business) {
            return redirect()->route('business.select')->with([
                'error' => 'Oops!',
                'error_message' => 'Empresa no encontrada'
            ]);
        }
        $sucursales = Sucursal::where('business_id', $business->id)
            ->orderBy('nombre', 'asc')
            ->get();
        return view('business.select-sucursal', compact('sucursales'));
    }

    public function selectSucursal(Request $request)
    {
        $request->validate([
            'sucursal' => 'nullable|exists:sucursals,id',
        ]);

        if ($request->has('no_sucursal')) {
            Session::forget('sucursal');
            return redirect()->route('business.index');
        }

        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login')->with([
                'error' => 'Oops!',
                'error_message' => 'Usuario no encontrado'
            ]);
        }

        $businessSucursal = Sucursal::where('id', $request->sucursal)
            ->where('business_id', Session::get('business'))
            ->exists();
        if (!$businessSucursal) {
            return redirect()->route('business.select-sucursal')->with([
                'error' => 'Oops!',
                'error_message' => 'No tiene permisos para acceder a esta sucursal'
            ]);
        }

        $sucursal = Sucursal::find($request->sucursal);
        if (!$sucursal) {
            return redirect()->route('business.select-sucursal')->with([
                'error' => 'Oops!',
                'error_message' => 'Sucursal no encontrada'
            ]);
        }

        Session::put('sucursal', $request->sucursal);
        return redirect()->route('business.index');
    }

}
