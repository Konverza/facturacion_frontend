<?php

namespace App\Http\Controllers\Business;

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
        '01' => 'Factura Electrónica',
        '03' => 'Comprobante de crédito fiscal',
        '05' => 'Nota de crédito',
        '06' => 'Nota de débito',
        '07' => 'Comprobante de retención',
        '11' => 'Factura de exportación',
        '14' => 'Factura de sujeto excluido'
    ];

    public function index()
    {
        try {
            $business_id = Session::get('business') ?? null;
            $user = User::with('businesses.business')->find(auth()->user()->id);
            $business_user = BusinessUser::where("user_id", $user->id)->first();
            $business = Business::find($business_id ?? $business_user->business_id);
            $business_plan = BusinessPlan::find($business_user->business_id);
            $dtes_pending = DTE::where('business_id', $business->id)->get();

            if (!$business) {
                return back()->with('error', 'No se encontró la empresa asociada.');
            }

            //Solicitudes a la API
            $statistics = Http::timeout(30)->get($this->octopus_url . '/dtes/statistics/?nit=' . $business->nit)
                ->json();
            $datos_empresa = Http::timeout(30)->get($this->octopus_url . '/datos_empresa/nit/' . $business->nit)
                ->json();
            $dtes = Http::timeout(30)->get($this->octopus_url . '/dtes/?nit=' . $business->nit)->json();
 
            // Datos locales
            $products = BusinessProduct::where('business_id', $business->id)->count('id');
            $customers = BusinessCustomer::where('business_id', $business->id)->count('id');
            $types = $this->types;

            return view('business.dashboard.index', compact(
                'statistics',
                'datos_empresa',
                'dtes',
                'products',
                'customers',
                'business_plan',
                'types',
                'dtes_pending'
            ));
        } catch (\Exception $e) {
            return back()->with('error', 'Error')->with("error_message", "Ha ocurrido un error al cargar los datos. Contacte al administrador.");
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

        Session::put('business', $request->business);
        return redirect()->route('business.index');
    }
}
