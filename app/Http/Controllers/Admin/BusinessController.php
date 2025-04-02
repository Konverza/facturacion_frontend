<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessPlan;
use App\Models\BusinessUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\UsuarioMail;
use App\Models\Plan;
use App\Services\OctopusService;

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
        return view('admin.business.index', compact('business'));
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
        $codigo_actividad_economica  = $request->actividad_economica;
        $actividades_economicas = $this->octopus_service->getCatalog("CAT-019", null, true, true);
        $descripcion_actividad_economica = $actividades_economicas[$codigo_actividad_economica];

        $data_business_repsonse = Http::post($this->octopus_url . "/datos_empresa", [
            "nombre" => $request->razon_social,
            "nit" => $request->nit,
            "nrc" => $request->nrc,
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
                ["name" => "nrc", "contents" => "$request->nrc"],
                ["name" => "api_password", "contents" => $api_password],
                ["name" => "certificate_password", "contents" => $certificate_password],
                ["name" => "file", "contents" =>
                fopen($certificado_file, "r"), "filename" => $certificado_file->getClientOriginalName()]
            ];

            $credentials_response = Http::attach($credentials)->post($this->octopus_url . "/credenciales");

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

                        $business_user = new BusinessUser();
                        $business_user->user_id = $user->id;
                        $business_user->business_id = $business->id;
                        $business_user->role = "negocio";
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
                        return back()->with('error', 'Ha ocurrio un error al subir el logo');
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    return back()->with('error', 'Ha ocurrido un error al guardar la empresa');
                }
            } else {
                return back()->with('error', 'Ha ocurrido un error al registrar las credenciales');
            }
        } else {
            return back()->with('error', 'Ha ocurrido un error al registrar la empresa');
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
        $actividades_economicas = $this->octopus_service->getCatalog("CAT-019", null, true, true);
        $plans = Plan::all();

        return view('admin.business.edit', [
            'departamentos' => $departamentos,
            'tipo_establecimiento' => $tipo_establecimiento,
            'plans' => $plans,
            'actividades_economicas' => $actividades_economicas,
            'empresa' => $empresa_api,
            'business_plan' => $business_plan,
            'business' => $empresa
        ]);
    }
}
