<?php


namespace App\Http\Controllers\Business;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessUser;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class DocumentController extends Controller
{
    public function index()
    {
        try {
            $business_id = Session::get('business') ?? null;
            $user = User::with('businesses.business')->find(auth()->user()->id);
            $business_user = BusinessUser::where("user_id", $user->id)->first();
            $business = Business::find($business_id ?? $business_user->business_id);
            $dtes = Http::get(env("OCTOPUS_API_URL") . '/dtes/?nit=' . $business->nit)->json();
            $receptores_nit = ['03', '05', '06'];
            $receptores_num = ['01', '07', '11', '14'];

            $types = [
                '01' => 'Factura Electrónica',
                '03' => 'Comprobante de crédito fiscal',
                '05' => 'Nota de crédito',
                '06' => 'Nota de débito',
                '07' => 'Comprobante de retención',
                '11' => 'Factura de exportación',
                '14' => 'Factura de sujeto excluido'
            ];

            if (request()->has("type")) {
                $dtes = array_filter($dtes, function ($dte) {
                    return $dte["estado"] == request("type");
                });
            }

            $dtes = array_map(function ($dte) {
                $dte["documento"] = json_decode($dte["documento"]);
                return $dte;
            }, $dtes);

            usort($dtes, function ($a, $b) {
                return $b["id"] <=> $a["id"];
            });

            return view("business.documents.index", [
                "invoices" => $dtes,
                "receptores_nit" => $receptores_nit,
                "receptores_num" => $receptores_num,
                "types" => $types
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'error' => 'Error',
                'error_message' => 'Error al cargar los documentos'
            ]);
        }
    }
}
