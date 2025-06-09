<?php

namespace App\Http\Controllers\Business;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessUser;
use App\Models\User;
use App\Services\OctopusService;
use Illuminate\Support\Facades\Http;

class ProfileController extends Controller
{
    public $octopus_url;
    public $octopus_service;

    public function __construct()
    {
        $this->octopus_url = env("OCTOPUS_API_URL");
        $this->octopus_service = new OctopusService();
    }

    public function index()
    {
        try {
            $business_id = session('business') ?? null;
            $user = User::with('businesses.business')->find(auth()->user()->id);
            $business_user = BusinessUser::where("user_id", $user->id)->first();
            $business = Business::find($business_id ?? $business_user->business_id);
            $datos_empresa = Http::get($this->octopus_url . '/datos_empresa/nit/' . $business->nit)->json();
            $logo = Http::get($this->octopus_url . '/datos_empresa/nit/' . $business->nit . '/logo')->json() ?? null;

            $actividades_economicas = $this->octopus_service->getCatalog("CAT-019", null, true, true);
            $tipos_establecimientos = $this->octopus_service->getCatalog("CAT-009");
            $departamentos = $this->octopus_service->getCatalog("CAT-012");
            $municipios =   $this->getMunicipios($datos_empresa["departamento"]);

            return view('business.profile.index', [
                'business' => $business,
                'datos_empresa' => $datos_empresa,
                'logo' => $logo,
                'actividades_economicas' => $actividades_economicas,
                'tipos_establecimientos' => $tipos_establecimientos,
                'departamentos' => $departamentos,
                'municipios' => $municipios,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return redirect()->route('business.index')->with([
                'error' => 'Error',
                'error_message' => 'Error al cargar los datos de la empresa'
            ]);
        }
    }

    public function getMunicipios($departamento)
    {
        $municipios = $this->octopus_service->getCatalog("CAT-012", $departamento);
        return $municipios;
    }

    public function datos_empresa(Request $request)
    {
        $codigo_actividad_economica  = $request->actividad_economica;
        $actividades_economicas = $this->octopus_service->getCatalog("CAT-019", null, true, true);
        $descripcion_actividad_economica = $actividades_economicas[$codigo_actividad_economica];
        $logo_success = true;

        $data_business_response = Http::put($this->octopus_url . "/datos_empresa/" . $request->id, [
            "nombre" => $request->nombre,
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

        if ($request->hasFile("logo")) {
            $logo_file = $request->file("logo");
            $logo = [
                ["name" => "nit", "contents" => $request->nit],
                ["name" => "file", "contents" => fopen($logo_file, "r"), "filename" => $logo_file->getClientOriginalName()]
            ];
            $logo_response = Http::attach($logo)->post("{$this->octopus_url}/credenciales/logo");
            $logo_success = $logo_response->successful();
        }

        if ($data_business_response->successful() && $logo_success) {
            return redirect()->route('business.profile.index')->with('success', 'Datos actualizados')->with("success_message", "Datos de la empresa actualizados correctamente");
        } else {
            return redirect()->route('business.profile.index')->with('error', 'Error')->with("error_message", "Error al actualizar los datos de la empresa");
        }
    }
}
