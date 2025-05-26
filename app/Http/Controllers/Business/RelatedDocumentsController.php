<?php

namespace App\Http\Controllers\Business;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Business;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RelatedDocumentsController extends Controller
{
    public $dte;

    public function __construct()
    {
        $this->dte = session("dte");
    }

    public function get()
    {
        if (isset($this->dte["documentos_relacionados"]) && count($this->dte["documentos_relacionados"]) > 0) {
            return response()->json([
                "success" => true,
            ]);
        } else {
            return response()->json([
                "success" => false,
            ]);
        }
    }

    public function store(Request $request)
    {
        try {

            if ($request->tipo_documento === $this->dte["type"]) {
                return response()->json([
                    "success" => false,
                    "message" => "No se pueden agregar documentos relacionados del mismo tipo del que se está generando."
                ]);
            }

            if (isset($this->dte["documentos_relacionados"])) {
                foreach ($this->dte["documentos_relacionados"] as $document) {
                    if ($document["tipo_documento"] !== $request->tipo_documento) {
                        return response()->json([
                            "success" => false,
                            "message" => "No se pueden agregar dos tipos de documentos relacionados diferentes"
                        ]);
                    }
                }
            }

            $this->dte["documentos_relacionados"][] = [
                "id" => rand(1, 1000),
                "tipo_documento" => $request->tipo_documento,
                "tipo_generacion" => $request->tipo_generacion,
                "numero_documento" => $request->numero_documento,
                "fecha_documento" => $request->fecha_documento,
            ];

            session(["dte" => $this->dte]);
            return response()->json([
                "success" => true,
                "message" => "Documento relacionado agregado correctamente",
                "table_data" => view("layouts.partials.ajax.business.table-related-documents", [
                    "dte" => $this->dte
                ])->render(),
                "modal" => "add-documento-fisico",
                "table" => "related-documents",
                "select_data" => view("layouts.partials.ajax.business.select-related-documents", [
                    "dte" => $this->dte
                ])->render(),
                "select" => "select-documento-relacionado"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Error al agregar documento relacionado"
            ]);
        }
    }

    public function storeElectric(Request $request)
    {
        try {

            $business_id = session("business");
            $business = Business::find($business_id);
            $dtes = Http::timeout(30)->get(env("OCTOPUS_API_URL") . '/dtes/?nit=' . $business->nit)->json();

            if (count($dtes) === 0) {
                return response()->json([
                    "success" => false,
                    "message" => "No se encontraron documentos electrónicos"
                ]);
            }

            if ($request->tipo_documento === $this->dte["type"]) {
                return response()->json([
                    "success" => false,
                    "message" => "No se pueden agregar documentos relacionados del mismo tipo del que se está generando."
                ]);
            }

            $document = null;
            foreach ($dtes as $dte) {
                if ($dte["codGeneracion"] === $request->codGeneracion) {
                    $document = $dte;
                    break;
                }
            }

            if ($document === null) {
                return response()->json([
                    "success" => false,
                    "message" => "No se encontró el documento electrónico"
                ]);
            }

            if (isset($this->dte["documentos_relacionados"])) {
                foreach ($this->dte["documentos_relacionados"] as $asociado) {
                    if ($asociado["tipo_documento"] !== $document["tipo_dte"]) {
                        return response()->json([
                            "success" => false,
                            "message" => "No se pueden agregar dos tipos de documentos relacionados diferentes"
                        ]);
                    }
                }
            }

            $this->dte["documentos_relacionados"][] = [
                "id" => rand(1, 1000),
                "tipo_documento" => $document["tipo_dte"],
                "tipo_generacion" => 2,
                "numero_documento" => $request->codGeneracion,
                "fecha_documento" => Carbon::parse($document["fhProcesamiento"])->format("Y-m-d"),
            ];

            session(["dte" => $this->dte]);
            return response()->json([
                "success" => true,
                "message" => "Documento relacionado agregado correctamente",
                "table_data" => view("layouts.partials.ajax.business.table-related-documents", [
                    "dte" => $this->dte
                ])->render(),
                "modal" => "add-documento-electronico",
                "table" => "related-documents",
                "select_data" => view("layouts.partials.ajax.business.select-related-documents", [
                    "dte" => $this->dte
                ])->render(),
                "select" => "select-documento-relacionado"
            ]);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                "success" => false,
                "message" => "Error al agregar documento relacionado"
            ]);
        }
    }

    public function delete(string $id)
    {
        try {
            $numero_documento = null;
            foreach ($this->dte["documentos_relacionados"] as $key => $document) {
                if ($document["id"] == $id) {
                    $numero_documento = $document["numero_documento"];
                    unset($this->dte["documentos_relacionados"][$key]);
                    break;
                }
            }

            if ($this->dte["type"] === "05" || $this->dte["type"] === "06") {
                if ($numero_documento !== null && isset($this->dte["products"]) && count($this->dte["products"]) > 0) {
                    foreach ($this->dte["products"] as $key => $product) {
                        if (isset($product["documento_relacionado"]) && $product["documento_relacionado"] == $numero_documento) {
                            unset($this->dte["products"][$key]);
                        }
                    }
                }
            }

            session(["dte" => $this->dte]);
            return response()->json([
                "success" => true,
                "message" => "Documento relacionado eliminado correctamente",
                "table_data" => view("layouts.partials.ajax.business.table-related-documents", [
                    "dte" => $this->dte
                ])->render(),
                "table" => "related-documents",
                "select_data" => view("layouts.partials.ajax.business.select-related-documents", [
                    "dte" => $this->dte
                ])->render(),
                "select" => "select-documento-relacionado",
                "table_data_2" => view("layouts.partials.ajax.business.table-products-dte", [
                    "dte" => $this->dte
                ])->render(),
                "table_2" => "products-dte",
            ]);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                "success" => false,
                "message" => "Error al eliminar documento relacionado"
            ]);
        }
    }
}
