<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\BusinessCustomer;
use App\Models\BusinessUser;
use App\Services\OctopusService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CustomerContoller extends Controller
{
    public $octopus_service;
    public $departamentos;
    public $tipos_documentos;
    public $actividades_economicas;
    public $countries;
    public $dte; 

    public function __construct()
    {
        $this->octopus_service = new OctopusService();
        $this->dte = session("dte", []);
    }

    public function index()
    {
        try {
            $business_customers = BusinessCustomer::where("business_id", session("business"))->orderBy("id", "desc")->get();
            return view('business.customers.index', [
                'business_customers' => $business_customers
            ]);
        } catch (\Exception $e) {
            return redirect()->route('business.customers.index')
                ->with([
                    "error" => "Error",
                    "error_message" => "Ha ocurrido un error al cargar los clientes"
                ]);
        }
    }

    public function create()
    {
        try {
            $departamentos = $this->octopus_service->getCatalog("CAT-012");
            $tipos_documentos = $this->octopus_service->getCatalog("CAT-022");
            $actividades_economicas = $this->octopus_service->getCatalog("CAT-019");
            $countries = $this->octopus_service->getCatalog("CAT-020");
            return view('business.customers.create', [
                'departamentos' => $departamentos,
                'tipos_documentos' => $tipos_documentos,
                'actividades_economicas' => $actividades_economicas,
                'countries' => $countries
            ]);
        } catch (\Exception $e) {
            return redirect()->route('business.customers.index')
                ->with([
                    "error" => "Error",
                    "error_message" => "Ha ocurrido un error al cargar los clientes"
                ]);
        }
    }

    public function store(Request $request)
    {
        try {

            $numero_documento = $request->numero_documento;
            if ($request->tipo_documento === "36") {
                if (strlen($numero_documento) !== 14) {
                    return redirect()->back()->withErrors(['numero_documento' => 'El número de documento debe tener exactamente 14 dígitos.'])->withInput();
                }
            } else if ($request->tipo_documento === "13") {
                if (!preg_match('/^\d{8}-\d{1}$/', $numero_documento)) {
                    return redirect()->back()->withErrors(['numero_documento' => 'El número de documento debe tener el formato XXXXXXXX-X.'])->withInput();
                }
            }

            $business_user = BusinessUser::where("business_id", session("business"))->first();
            DB::beginTransaction();

            $business_customer = new BusinessCustomer([
                "business_id" => $business_user->business_id,
                "tipoDocumento" => $request->tipo_documento,
                "numDocumento" => $request->numero_documento,
                "nrc" => $request->nrc,
                "nombre" => $request->nombre,
                "codActividad" => $request->actividad_economica,
                "nombreComercial" => $request->nombre_comercial,
                "departamento" => $request->departamento,
                "municipio" => $request->municipio,
                "complemento" => $request->complemento,
                "telefono" => $request->telefono,
                "correo" => $request->correo,
                "codPais" => $request->codigo_pais,
                "tipoPersona" => $request->tipo_persona,
                "special_price" => $request->has('special_price') ? true : false
            ]);

            $business_customer->save();
            DB::commit();
            return redirect()->route('business.customers.index')
                ->with("success", "Cliente guardado")
                ->with("success_message", "El cliente ha sido guardado correctamente");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with("error", "Error")->with("error_message", "Ha ocurrido un error al guardar el cliente");
        }
    }

    public function destroy(string $id)
    {
        try {
            $business_customer = BusinessCustomer::where("id", $id)->first();
            if ($business_customer) {
                $business_customer->delete();
                return redirect()->route('business.customers.index')
                    ->with("success", "Cliente eliminado")
                    ->with("success_message", "El cliente ha sido eliminado correctamente");
            } else {
                return redirect()->route('business.customers.index')
                    ->with("error", "Error")
                    ->with("error_message", "El cliente no existe");
            }
        } catch (\Exception $e) {
            return redirect()->route('business.customers.index')
                ->with("error", "Error")
                ->with("error_message", "Ha ocurrido un error al eliminar el cliente");
        }
    }

    public function edit(string $id)
    {
        try {
            $business_customer = BusinessCustomer::where("id", $id)->first();
            $municipios = $this->getMunicipios($business_customer->departamento);
            return view('business.customers.edit', [
                'customer' => $business_customer,
                'departamentos' => $this->octopus_service->getCatalog("CAT-012"),
                'tipos_documentos' => $this->octopus_service->getCatalog("CAT-022"),
                'actividades_economicas' => $this->octopus_service->getCatalog("CAT-019"),
                'countries' => $this->octopus_service->getCatalog("CAT-020"),
                'municipios' => $municipios
            ]);
        } catch (\Exception $e) {
            return redirect()->route('business.customers.index')
                ->with([
                    "error" => "Error",
                    "error_message" => "Ha ocurrido un error al cargar los clientes"
                ]);
        }
    }

    public function update(Request $request, string $id)
    {
        try {

            $numero_documento = $request->numero_documento;
            if ($request->tipo_documento === "36") {
                if (strlen($numero_documento) !== 14) {
                    return redirect()->back()->withErrors(['numero_documento' => 'El número de documento debe tener exactamente 14 dígitos.']);
                }
            } else if ($request->tipo_documento === "13") {
                if (!preg_match('/^\d{8}-\d{1}$/', $numero_documento)) {
                    return redirect()->back()->withErrors(['numero_documento' => 'El número de documento debe tener el formato XXXXXXXX-X.']);
                }
            }

            $business_customer = BusinessCustomer::findOrFail($id); 
            DB::beginTransaction();
            $business_customer->update([
                "tipoDocumento" => $request->tipo_documento,
                "numDocumento" => $request->numero_documento,
                "nrc" => $request->nrc,
                "nombre" => $request->nombre,
                "codActividad" => $request->actividad_economica,
                "nombreComercial" => $request->nombre_comercial,
                "departamento" => $request->departamento,
                "municipio" => $request->municipio,
                "complemento" => $request->complemento,
                "telefono" => $request->telefono,
                "correo" => $request->correo,
                "codPais" => $request->codigo_pais,
                "tipoPersona" => $request->tipo_persona,
                "special_price" => $request->has('special_price') ? true : false
            ]);
            DB::commit();

            return redirect()->route('business.customers.index')->with([
                "success" => "Cliente actualizado",
                "success_message" => "El cliente ha sido actualizado correctamente"
            ]);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('business.customers.index')->with([
                "error" => "Error",
                "error_message" => "El cliente no existe"
            ]);
        } catch (\Exception $e) {
            DB::rollBack(); // Si falla, revierte cambios
            return redirect()->route('business.customers.index')->with([
                "error" => "Error",
                "error_message" => "Ha ocurrido un error al actualizar el cliente"
            ]);
        }
    }

    public function show(string $id)
    {
        try {
            $business_customer = BusinessCustomer::where("id", $id)->first();
            session()->put("dte", array_merge(session("dte", []), [
                "customer" => $business_customer
            ]));

            if($this->dte["type"] === "03" || $this->dte["type"] === "05") {
                $business_customer->numDocumento = str_replace("-", "", $business_customer->numDocumento);
            }

            return response()->json([
                "customer" => $business_customer,
                "select_tipos_documentos" => view("layouts.partials.ajax.business.select-tipos-documentos", [
                    "tipo_documento" => $business_customer->tipoDocumento,
                    "tipos_documentos" => $this->octopus_service->getCatalog("CAT-022")
                ])->render(),
                "select_actividad_economica" => view("layouts.partials.ajax.business.select-actividad-economica", [
                    "actividad_economica" => $business_customer->codActividad,
                    "actividades_economicas" => $this->octopus_service->getCatalog("CAT-019", null, true, true)
                ])->render(),
                "select_departamentos" => view("layouts.partials.ajax.business.select-departamentos", [
                    "departamento" => $business_customer->departamento,
                    "departamentos" => $this->octopus_service->getCatalog("CAT-012")
                ])->render(),
                "select_municipios" => view("layouts.partials.ajax.admin.select-municipios", [
                    "municipio" => $business_customer->municipio,
                    "municipios" => $this->getMunicipios($business_customer->departamento)
                ])->render(),
                "select_countries" => view("layouts.partials.ajax.business.select-countries", [
                    "country" => $business_customer->codPais,
                    "countries" => $this->octopus_service->getCatalog("CAT-020")
                ])->render(),
                "select_tipo_persona" => view("layouts.partials.ajax.business.select-tipos-personas", [
                    "tipo_persona" => $business_customer->tipoPersona
                ])->render()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "error" => "Error",
                "error_message" => "Ha ocurrido un error al cargar el cliente"
            ]);
        }
    }

    public function getMunicipios($departamento)
    {
        try {
            $municipios = $this->octopus_service->getCatalog("CAT-012", $departamento);
            return $municipios;
        } catch (\Exception $e) {
            Log::error("Error obteniendo municipios: " . $e->getMessage());
            return [];
        }
    }
}
