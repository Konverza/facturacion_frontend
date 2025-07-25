<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\UsuarioMail;
use App\Models\Business;
use App\Models\BusinessPlan;
use App\Models\BusinessUser;
use App\Models\Plan;
use App\Models\PuntoVenta;
use App\Models\Sucursal;
use App\Models\User;
use App\Services\OctopusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class BusinessController extends Controller
{
    public $octopus_service;
    public $octopus_url;
    public $octopus_cats_url;

    public function __construct()
    {
        $this->octopus_service = new OctopusService();
        $this->octopus_url = env("OCTOPUS_API_URL");
        $this->octopus_cats_url = env("OCTOPUS_CATS_URL");
    }

    public function index()
    {
        $business = Business::with('plan')->get();
        $inicio_mes = date('Y-m-01');
        $fin_mes = date('Y-m-t');

        foreach ($business as $value) {
            $params = [
                'nit' => $value->nit,
                'fechaInicio' => "{$inicio_mes}T00:00:00",
                'fechaFin' => "{$fin_mes}T23:59:59"
            ];
            $statistics = Http::timeout(30)->get($this->octopus_url . '/dtes/statistics/?' . http_build_query($params))
                ->json();
            $value->statistics = $statistics;
        }
        $business = $business->sortByDesc('id');

        return view('admin.business.index', compact('business', 'inicio_mes', 'fin_mes'));
    }

    public function create()
    {
        $tipo_establecimiento = $this->octopus_service->getCatalog("CAT-009");
        $departamentos = $this->octopus_service->getCatalog("CAT-012");
        $actividades_economicas = $this->octopus_service->getCatalog("CAT-019", null, true, true);
        $plans = Plan::all();

        return view('admin.business.create', [
            'departamentos' => $departamentos,
            'tipo_establecimiento' => $tipo_establecimiento,
            'plans' => $plans,
            'actividades_economicas' => $actividades_economicas
        ]);
    }

    public function store(Request $request)
    {
        $codigo_actividad_economica = $request->actividad_economica;
        $actividades_economicas = $this->octopus_service->getCatalog("CAT-019", null, true, true);
        $descripcion_actividad_economica = $actividades_economicas[$codigo_actividad_economica];

        $data_business_repsonse = Http::post($this->octopus_url . "/datos_empresa/", [
            "nombre" => $request->razon_social,
            "nit" => $request->nit,
            "nrc" => str_replace("-", "", $request->nrc),
            "codActividad" => $codigo_actividad_economica,
            "descActividad" => $descripcion_actividad_economica,
            "nombreComercial" => $request->nombre_comercial,
            "tipoEstablecimiento" => $request->tipo_establecimiento,
            "departamento" => $request->departamento,
            "municipio" => $request->municipio,
            "complemento" => $request->complemento,
            "telefono" => $request->telefono,
            "correo" => $request->correo,
            "codEstable" => $request->codigo_establecimiento,
            "codEstableMH" => $request->codigo_establecimiento_mh,
            "codPuntoVenta" => $request->codigo_punto_venta,
            "codPuntoVentaMH" => $request->codigo_punto_venta_mh,
        ]);

        if ($data_business_repsonse->status() == 201) {
            $api_password = $request->api_password;
            $certificate_password = $request->certificate_password;
            $certificado_file = $request->file("certificado_file");

            $credentials = [
                ["name" => "nit", "contents" => $request->nit],
                ["name" => "nrc", "contents" => str_replace("-", "", $request->nrc)],
                ["name" => "api_password", "contents" => $api_password],
                ["name" => "certificate_password", "contents" => $certificate_password],
                [
                    "name" => "file",
                    "contents" =>
                        fopen($certificado_file, "r"),
                    "filename" => $certificado_file->getClientOriginalName()
                ]
            ];

            $credentials_response = Http::attach($credentials)->post($this->octopus_url . "/credenciales/");

            if ($credentials_response->status() == 201) {
                $logo_file = $request->file("logo");
                $logo = [
                    ["name" => "nit", "contents" => $request->nit],
                    [
                        "name" => "file",
                        "contents" => fopen($logo_file, "r"),
                        "filename" => $logo_file->getClientOriginalName()
                    ]
                ];

                $logo_response = Http::attach($logo)->post($this->octopus_url . "/credenciales/logo");
                DB::beginTransaction();
                try {
                    if ($logo_response->status() == 201) {
                        $business = new Business();
                        $business->nit = $request->nit;
                        $business->nombre = $request->nombre_comercial;
                        $business->plan_id = $request->plan_id;
                        $business->dui = $request->dui;
                        $business->telefono = $request->telefono_responsable;
                        $business->correo_responsable = $request->correo_responsable;
                        $business->nombre_responsable = $request->nombre_responsable;
                        $business->save();

                        $business_plan = new BusinessPlan();
                        $business_plan->nit = $request->nit;
                        $business_plan->plan_id = $request->plan_id;
                        $business_plan->dtes = json_encode($request->dtes);
                        $business_plan->save();

                        $password = Str::password(8, true, true, false);

                        $searchUser = User::where('email', $request->correo_responsable)->first();
                        if ($searchUser) {
                            $user = $searchUser;
                        } else {
                            $user = new User();
                            $user->name = $request->nombre_responsable;
                            $user->email = $request->correo_responsable;
                            $user->password = bcrypt($password);
                            $user->save();
                            $user->assignRole('business');
                        }

                        $sucursal = new Sucursal([
                            'nombre' => "Casa Matriz",
                            'departamento' => $request->departamento,
                            'municipio' => $request->municipio,
                            'complemento' => $request->complemento,
                            'telefono' => $request->telefono,
                            'correo' => $request->correo,
                            'codSucursal' => "M001",
                            'business_id' => $business->id
                        ]);
                        $sucursal->save();

                        $punto_venta = new PuntoVenta([
                            'nombre' => "Punto de Venta Principal",
                            'sucursal_id' => $sucursal->id,
                            'codPuntoVenta' => "P001",
                        ]);
                        $punto_venta->save();

                        $business_user = new BusinessUser();
                        $business_user->user_id = $user->id;
                        $business_user->business_id = $business->id;
                        $business_user->role = "negocio";
                        $business_user->default_pos_id = $punto_venta->id;
                        $business_user->save();

                        Mail::to($request->correo_responsable)
                            ->send(new UsuarioMail(
                                $request->nombre_responsable,
                                $request->correo_responsable,
                                $password
                            ));

                        DB::commit();
                        return redirect()->route('admin.business.index')
                            ->with('success', 'Empresa registrada correctamente');
                    } else {
                        DB::rollBack();
                        return back()->with('error', 'Ha ocurrido un error al subir el logo')
                            ->with("error_message", "Error: " . $logo_response->json()['detail'] ?? 'Error desconocido')
                            ->withInput();
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    return back()->with('error', 'Ha ocurrido un error al guardar la empresa')
                        ->with("error_message", "Error: " . $e->getMessage())
                        ->withInput();
                }
            } else {
                return back()->with('error', 'Ha ocurrido un error al registrar las credenciales')
                    ->with("error_message", "Error: " . $credentials_response->json()['detail'] ?? 'Error desconocido')
                    ->withInput();
            }
        } else {
            return back()->with('error', 'Ha ocurrido un error al registrar la empresa')
                ->with("error_message", "Error: " . $data_business_repsonse->json()['detail'] ?? 'Error desconocido')
                ->withInput();
        }
    }

    public function getMunicipios(Request $request)
    {
        $response = Http::get($this->octopus_cats_url . "CAT-012");
        $data = $response->json();
        $municipios = [];
        foreach ($data as $value) {
            if ($value['codigo'] == $request->codigo) {
                foreach ($value["municipios"] as $municipio) {
                    $municipios[$municipio['codigo']] = $municipio['nombre'];
                }
            }
        }
        return response()->json([
            'municipios' => $municipios,
            'html' => view("layouts.partials.ajax.admin.select-municipios", [
                'municipios' => $municipios,
                'municipio' => $request->municipio ?? null
            ])->render()
        ]);
    }

    public function edit(string $id)
    {
        $empresa = Business::find($id);
        if (!$empresa) {
            return redirect()->route('admin.business.index')
                ->with('error', 'No se encontró la empresa');
        }
        $empresa_api = Http::get($this->octopus_url . "/datos_empresa/nit/" . $empresa->nit);
        $empresa_api = $empresa_api->json();
        $business_plan = BusinessPlan::where('nit', $empresa["nit"])->first();
        $tipo_establecimiento = $this->octopus_service->getCatalog("CAT-009");
        $departamentos = $this->octopus_service->getCatalog("CAT-012");
        $municipios = $this->octopus_service->getCatalog("CAT-012", $empresa_api["departamento"]);
        $actividades_economicas = $this->octopus_service->getCatalog("CAT-019", null, true, true);

        $municipio_anterior = DB::table('cat_013')
            ->where('codigo', $empresa_api["municipio"])
            ->where('departamento', $empresa_api["departamento"])
            ->first();

        $plans = Plan::all();

        return view('admin.business.edit', [
            'departamentos' => $departamentos,
            'municipios' => $municipios,
            'municipio_anterior' => $municipio_anterior,
            'tipo_establecimiento' => $tipo_establecimiento,
            'plans' => $plans,
            'actividades_economicas' => $actividades_economicas,
            'empresa' => $empresa_api,
            'business_plan' => $business_plan,
            'business' => $empresa
        ]);
    }

}
