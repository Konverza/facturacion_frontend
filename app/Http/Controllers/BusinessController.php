<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Business;
use App\Models\BusinessCustomer;
use App\Models\BusinessUser;
use App\Models\BusinessProduct;
use App\Models\BusinessPlan;
use App\Models\Tributes;

use App\Exports\Contribuyente;
use App\Exports\ConsumidorFinal;

class BusinessController extends Controller
{
    public function dashboard()
    {
        $business_user = BusinessUser::where('user_id', auth()->id())->first();
        $business = Business::find($business_user->business_id);
        $business_plan = BusinessPlan::find($business_user->business_id);
        $statistics = Http::get(env("OCTOPUS_API_URL") . '/dtes/statistics/?nit=' . $business->nit)->json();
        $datos_empresa = Http::get(env("OCTOPUS_API_URL") . '/datos_empresa/nit/' . $business->nit)->json();
        $dtes = Http::get(env("OCTOPUS_API_URL") . '/dtes/?nit=' . $business->nit)->json();
        $pruebas = [];
        $productos = BusinessProduct::where('business_id', $business->id)->count('id');
        $customers = BusinessCustomer::where('business_id', $business->id)->count('id');
        return view('business.dashboard', compact('statistics', 'datos_empresa', 'dtes', 'pruebas', 'productos', 'customers', 'business_plan'));
    }

    public function factura(Request $request)
    {
        $business_user = BusinessUser::where('user_id', auth()->id())->first();
        $business = Business::find($business_user->business_id);
        $datos_empresa = Http::get(env("OCTOPUS_API_URL") . '/datos_empresa/nit/' . $business->nit)->json();
        $catalogos = [
            "CAT_010" => DB::table('cat_010')->select('codigo', 'valores')->get(),
            "CAT_014" => DB::table('cat_014')->select('codigo', 'valores')->get(),
            "CAT_017" => DB::table('cat_017')->select('codigo', 'valores')->get(),
            "CAT_018" => DB::table('cat_018')->select('codigo', 'valores')->get(),
            "CAT_020" => DB::table('cat_020')->select('codigo', 'valores')->get(),
            "CAT_021" => DB::table('cat_021')->select('codigo', 'valores')->get(),
            "CAT_027" => DB::table('cat_027')->select('codigo', 'valores')->get(),
            "CAT_028" => DB::table('cat_028')->select('codigo', 'valores')->get(),
            "CAT_031" => DB::table('cat_031')->select('codigo', 'valores')->get(),
        ];
        $tributos = Tributes::all();

        switch ($request->dte) {
            case '01':
                $view = "business.factura";
                break;
            case '03':
                $view = "business.credito_fiscal";
                break;
            case '05':
                $view = "business.nota_credito";
                break;
            case '06':
                $view = "business.nota_debito";
                break;
            case '07':
                $view = "business.comprobante_retencion";
                break;
            case '11':
                $view = "business.exportacion";
                break;
            case '14':
                $view = "business.sujeto_excluido";
                break;
            default:
                return redirect()->route("business.dashboard");
        }

        return view($view, compact('datos_empresa', 'catalogos', 'tributos'));
    }

    public function sucursales()
    {
        return view('business.sucursales');
    }

    public function dtes()
    {
        $business_user = BusinessUser::where('user_id', auth()->id())->first();
        $business = Business::find($business_user->business_id);
        $response = Http::get(env("OCTOPUS_API_URL") . '/dtes/?nit=' . $business->nit);
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

    public function buscar_dtes(Request $request)
    {
        $business_user = BusinessUser::where('user_id', auth()->id())->first();
        $business = Business::find($business_user->business_id);
        $response = Http::get(env("OCTOPUS_API_URL") . '/dtes/?nit=' . $business->nit);
        $dtes = $response->json();

        $nitBusqueda = $request->nitBusqueda;
        $tipoDocumentoElectronico = $request->tipoDocumentoElectronico;
        $desdeBusqueda = \Carbon\Carbon::parse($request->desdeBusqueda);
        $hastaBusqueda = \Carbon\Carbon::parse($request->hastaBusqueda);

        // Filter the invoices based on the search criteria
        $dtes_matching = [];
        foreach ($dtes as $dte) {
            if ($dte["tipo_dte"] == $tipoDocumentoElectronico) {
                if ($dte["estado"] == "PROCESADO") {
                    $fechaDte = \Carbon\Carbon::parse($dte["fhProcesamiento"]);
                    if ($fechaDte->between($desdeBusqueda, $hastaBusqueda)) {
                        $documento = json_decode($dte["documento"]);
                        $receptor = $documento->receptor;
                        if ($tipoDocumentoElectronico == "01") {
                            if ($receptor->numDocumento == $nitBusqueda) {
                                $dtes_matching[] = [
                                    "tipo_dte" => $dte["tipo_dte"],
                                    "fecha_emision" => $fechaDte->format('Y-m-d'),
                                    "codigo_generacion" => $dte["codGeneracion"],
                                    "monto" => $monto
                                ];
                            }
                        } else {
                            if ($receptor->nit == $nitBusqueda) {
                                $monto = ($tipoDocumentoElectronico == "07") ? $documento->resumen->totalIVAretenido : $documento->resumen->totalPagar;
                                $dtes_matching[] = [
                                    "tipo_dte" => $dte["tipo_dte"],
                                    "fecha_emision" => $fechaDte->format('Y-m-d'),
                                    "codigo_generacion" => $dte["codGeneracion"],
                                    "monto" => $monto
                                ];
                            }
                        }
                    }
                }
            }
        }
        return response()->json($dtes_matching);
    }

    public function anular_dte(Request $request)
    {
        $codGeneracion = $request->codGeneracion;
        $motivo = $request->motivo;
        $dte = Http::get(env("OCTOPUS_API_URL") . '/dtes/' . $codGeneracion)->json();
        $documento = json_decode($dte["documento"]);

        $business_user = BusinessUser::where('user_id', auth()->id())->first();
        $business = Business::find($business_user->business_id);
        $nit = $business->nit;

        $tipoDoc = null;
        $nombre = null;
        $numDocumento = null;


        if ($dte['tipo_dte'] == '14') {
            $receptor = $documento->sujetoExcluido;
        } else {
            $receptor = $documento->receptor;
        }

        $nombre = $receptor->nombre;
        if (in_array($dte['tipo_dte'], ['03', '05', '06'])) {
            $tipoDoc = "36";
            $numDocumento = $receptor->nit;
        } else {
            $tipoDoc = $receptor->tipoDocumento;
            $numDocumento = $receptor->numDocumento;
        }

        $response = Http::post(env("OCTOPUS_API_URL") . '/anulacion/', [
            "nit" => $nit,
            "documento" => [
                "codigoGeneracion" => $codGeneracion,
                "fechaEmision" => $documento->identificacion->fecEmi,
                "horaEmision" => $documento->identificacion->horEmi,
                "codigoGeneracionR" => null,
            ],
            "motivo" => [
                "tipoAnulacion" => 2,
                "motivoAnulacion" => $motivo,
                "nombreResponsable" => auth()->user()->name,
                "tipoDocResponsable" => "36",
                "numDocResponsable" => $nit,
                "nombreSolicita" => $nombre ?? auth()->user()->name,
                "tipoDocSolicita" => $tipoDoc ?? "36",
                "numDocSolicita" => $numDocumento ?? $nit,
            ]
        ]);
        $data = $response->json();
        if ($response->status() == 201) {
            return redirect()->route('business.dtes')->with('success', $data["descripcionMsg"]);
        } else {
            return redirect()->route('business.dtes')->with('error', $data["detail"]["descripcionMsg"]);
        }
    }

    public function send_dte(Request $request)
    {
        $dte = $request->dte;
        // Send the same request to the API
        $response = Http::post(env("OCTOPUS_API_URL") . "/" . $dte . "/", $request->all());
        // Redirect back with a success message
        return response()->json([
            "status" => $response->status(),
            "message" => $response->json()
        ]);
    }

    public function tabla_productos(Request $request)
    {
        // Page Length
        $pageNumber = ($request->start / $request->length) + 1;
        $pageLength = $request->length;
        $skip = ($pageNumber - 1) * $pageLength;

        //Page Order
        $orderColumnIndex = $request->order[0]['column'] ?? '0';
        $orderBy = $request->order[0]['dir'] ?? 'desc';

        //Get data from products
        $business_user = BusinessUser::where('user_id', auth()->id())->first();
        $business = Business::find($business_user->business_id);

        $query = DB::table('business_product')->select('*');
        $query->where('business_id', $business->id);

        //Search
        $search = $request->search;
        $query = $query->where(function ($query) use ($search) {
            $query->where('codigo', 'like', '%' . $search . '%')
                ->orWhere('descripcion', 'like', '%' . $search . '%');
        });

        $orderByName = 'codigo';
        switch ($orderColumnIndex) {
            case '0':
                $orderByName = 'codigo';
                break;
            case '1':
                $orderByName = 'descripcion';
                break;
        }
        $query->orderBy($orderByName, $orderBy);
        $recordsFiltered = $recordsTotal = $query->count();
        $products = $query->skip($skip)->take($pageLength)->get();

        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $products
        ], 200);
    }

    public function get_producto(Request $request)
    {
        $product = BusinessProduct::find($request->id)->toArray();
        $tributos = json_decode($product["tributos"]);
        foreach ($tributos as $tributo) {
            $product["tributos_bd"][] = Tributes::where('codigo', $tributo)->select('codigo', 'descripcion', 'es_porcentaje', 'valor')->first();
        }
        $product["tributos"] = $product["tributos_bd"];
        unset($product["tributos_bd"]);
        return response()->json($product);
    }

    public function tabla_clientes(Request $request)
    {
        // Page Length
        $pageNumber = ($request->start / $request->length) + 1;
        $pageLength = $request->length;
        $skip = ($pageNumber - 1) * $pageLength;

        //Page Order
        $orderColumnIndex = $request->order[0]['column'] ?? '0';
        $orderBy = $request->order[0]['dir'] ?? 'desc';

        //Get data from products
        $business_user = BusinessUser::where('user_id', auth()->id())->first();
        $business = Business::find($business_user->business_id);

        $query = DB::table('business_customers')->select('*');
        $query->where('business_id', $business->id);

        //Search
        $search = $request->search;
        $query = $query->where(function ($query) use ($search) {
            $query->where('numDocumento', 'like', '%' . $search . '%')
                ->orWhere('nombre', 'like', '%' . $search . '%')
                ->orWhere('nombreComercial', 'like', '%' . $search . '%');
        });

        $orderByName = 'numDocumento';
        switch ($orderColumnIndex) {
            case '0':
                $orderByName = 'numDocumento';
                break;
            case '1':
                $orderByName = 'nombre';
                break;
        }
        $query->orderBy($orderByName, $orderBy);
        $recordsFiltered = $recordsTotal = $query->count();
        $products = $query->skip($skip)->take($pageLength)->get();

        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $products
        ], 200);
    }

    public function get_cliente(Request $request)
    {
        $customer = BusinessCustomer::find($request->id)->toArray();
        return response()->json($customer);
    }

    public function anexos(Request $request)
    {
        $business_user = BusinessUser::where('user_id', auth()->id())->first();
        $business = Business::find($business_user->business_id);
        $dtes = Http::get(env("OCTOPUS_API_URL") . '/dtes/?nit=' . $business->nit)->json();
        $dte_collection = null;

        if ($request->tipo == "1") {
            foreach ($dtes as $dte) {
                if ($dte["estado"] == "PROCESADO") {
                    if (in_array($dte["tipo_dte"], ["03", "05", "06"])) {
                        $fecha_dte = \Carbon\Carbon::parse($dte["fhProcesamiento"]);
                        $desde = \Carbon\Carbon::parse($request->fecha_inicio)->startOfDay();
                        $hasta = \Carbon\Carbon::parse($request->fecha_fin)->endOfDay();
                        if ($fecha_dte->between($desde, $hasta)) {
                            $dte["documento"] = json_decode($dte["documento"]);
                            $dte_collection[] = $dte;
                        }
                    }
                } else {
                    continue;
                }
                // Filtrar por tipo de DTE
            }
            $dte_collection = collect($dte_collection);
            return Excel::download(new Contribuyente($dte_collection), 'anexo-f07-contribuyentes.csv', \Maatwebsite\Excel\Excel::CSV);
        } else {
            $dte_collection = collect();
            foreach ($dtes as $dte) {
                if ($dte["estado"] === "PROCESADO" && in_array($dte["tipo_dte"], ["01", "11"])) {
                    $fecha_dte = \Carbon\Carbon::parse($dte["fhProcesamiento"])->toDateString(); // Solo la fecha
                    $desde = \Carbon\Carbon::parse($request->fecha_inicio)->startOfDay();
                    $hasta = \Carbon\Carbon::parse($request->fecha_fin)->endOfDay();

                    if (\Carbon\Carbon::parse($dte["fhProcesamiento"])->between($desde, $hasta)) {
                        $dte["documento"] = json_decode($dte["documento"]);
                        $dte_collection->push($dte);
                    }
                }
            }

            // Agrupar por fecha y ordenar dentro de cada grupo por fecha y hora
            $grouped_dtes = $dte_collection
                ->sortBy('fhProcesamiento') // Ordenar globalmente antes de agrupar
                ->groupBy(fn($dte) => \Carbon\Carbon::parse($dte["fhProcesamiento"])->toDateString())
                ->map(fn($dtes) => $dtes->groupBy('tipo_dte'));

            $result = [];

            foreach ($grouped_dtes as $fecha => $tipos) {
                foreach ($tipos as $tipo => $dtes) {
                    // Obtener el primer y último codGeneracion del grupo por fecha
                    $primer_cod = $dtes->first()["codGeneracion"];
                    $ultimo_cod = $dtes->last()["codGeneracion"];
                    $tipoOperacion = "";

                    // Sumar valores de los DTEs
                    $totalExento = $dtes->sum(fn($dte) => $dte["documento"]->resumen->totalExenta ?? 0);
                    $totalNoSuj = $dtes->sum(fn($dte) => $dte["documento"]->resumen->totalNoSuj ?? 0);
                    $totalGravado = $tipo === "01" ? $dtes->sum(fn($dte) => $dte["documento"]->resumen->totalGravada ?? 0) : 0;
                    $totalPagar = $totalExento + $totalNoSuj + $totalGravado;

                    if($totalGravado > 0 && $totalNoSuj == 0 && $totalExento == 0){
                        $tipoOperacion = "01";
                    } elseif(($totalGravado == 0) && ($totalNoSuj > 0 || $totalExento > 0)){
                        $tipoOperacion = "02";
                    } else {
                        $tipoOperacion = "04";
                    }

                    // Formatear la fecha
                    $fecha_formateada = \Carbon\Carbon::parse($fecha)->format('d/m/Y');

                    // Generar el array con la estructura solicitada
                    $result[] = [
                        $fecha_formateada, // fhprocesamiento en dd/mm/YYYY
                        "4", // Clase
                        $tipo, // Tipo de DTE
                        "N/A",
                        "N/A",
                        "N/A",
                        "N/A", // Cuatro veces "N/A"
                        $primer_cod, // Primer codGeneracion del grupo
                        $ultimo_cod, // Último codGeneracion del grupo
                        null, // null
                        $totalExento, // Suma de totalExenta
                        0, // 0.00
                        $totalNoSuj, // Suma de totalNoSuj
                        $totalGravado, // totalGravada si tipo es "01", si no, 0
                        0,
                        0,
                        0,
                        0,
                        0, // Cinco veces 0
                        $totalPagar, // Suma totalExenta + totalNoSuj + totalGravada
                        $tipoOperacion,
                        "03", // "03"
                        "2", // "2"
                    ];
                }
            }

            return Excel::download(new ConsumidorFinal($result), 'anexo-f07-consumidor-final.csv', \Maatwebsite\Excel\Excel::CSV);
        }

    }
}
