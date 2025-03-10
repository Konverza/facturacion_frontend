<?php

namespace App\Http\Controllers\Business;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class AssociatedDocumentsController extends Controller
{
    public $dte;

    public function __construct()
    {
        $this->dte = session("dte");
    }

    public function store(Request $request)
    {
        try {
            Log::info($request->all());
            $numero_documento = $request->numero_doc;
            if ($request->tipo_documento_doctor === "1") {
                if (strlen($numero_documento) !== 14) {
                    return response()->json([
                        "success" => false,
                        "message" => "El número de documento debe tener 14 dígitos.",
                    ]);
                }
            }

            $medico = null;
            if ($request->documento_asociado === "3") {
                $medico = [
                    "nombre" => $request->nombre_medico,
                    "tipo_servicio" => $request->tipo_servicio,
                    "tipo_documento" => $request->tipo_documento_doctor,
                    "numero_documento" => $request->numero_doc,
                ];
            }

            $this->dte["otros_documentos"][] = [
                "id" => rand(1, 1000),
                "documento_asociado" => $request->documento_asociado,
                "identificacion_documento" => $request->identificacion_documento ?? null,
                "descripcion_documento" => $request->descripcion_documento ?? null,
                "medico" => $medico,
            ];

            if ($request->documento_asociado === "4") {

                if (strlen($request->placas) < 5) {
                    return response()->json([
                        "success" => false,
                        "message" => "Las placas deben tener al menos 5 caracteres.",
                    ]);
                }

                if (strlen($request->placas) > 70) {
                    return response()->json([
                        "success" => false,
                        "message" => "Las placas no deben tener más de 70 caracteres.",
                    ]);
                }

                if (strlen($request->numero_identificacion) > 5) {
                    return response()->json([
                        "success" => false,
                        "message" => "El número de identificación no debe tener más de 5 caracteres.",
                    ]);
                }

                if (strlen($request->numero_identificacion) > 100) {
                    return response()->json([
                        "success" => false,
                        "message" => "El número de identificación no debe tener más de 100 caracteres.",
                    ]);
                }

                $key = array_key_last($this->dte["otros_documentos"]);
                $this->dte["otros_documentos"][$key] += [
                    "placas" => $request->placas,
                    "modo_transporte" => $request->modo_transporte,
                    "numero_identificacion" => $request->numero_identificacion,
                    "nombre_conductor" => $request->nombre_conductor,
                ];
            }

            session(["dte" => $this->dte]);

            switch ($request->documento_asociado) {
                case "3":
                    $message = "Médico relacionado agregado correctamente";
                    $table = "associated-doctors";
                    $view = view("layouts.partials.ajax.business.table-associated-doctors", [
                        "dte" => $this->dte
                    ])->render();
                    break;
                default:
                    $message = "Documento asociado agregado correctamente";
                    $table = "associated-documents";
                    $view = view("layouts.partials.ajax.business.table-associated-documents", [
                        "dte" => $this->dte
                    ])->render();
                    break;
            }

            return response()->json([
                "success" => true,
                "message" => $message,
                "table_data" => $view,
                "modal" => "add-otros-documentos-asociados",
                "table" => $table
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                "success" => false,
                "message" => "Error al agregar documento asociado",
                "error" => $e->getMessage()
            ]);
        }
    }

    public function delete_transport(string $id)
    {
        try {
            foreach ($this->dte["transportes_relacionados"] as $key => $transport) {
                if ($transport["id"] == $id) {
                    unset($this->dte["transportes_relacionados"][$key]);
                }
            }
            session(["dte" => $this->dte]);
            return response()->json([
                "success" => true,
                "message" => "Transporte asociado eliminado correctamente",
                "table_data" => view("layouts.partials.ajax.business.table-associated-transports", [
                    "dte" => $this->dte,
                ])->render(),
                "table" => "associated-transports"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Error al eliminar transporte asociado",
                "error" => $e->getMessage()
            ]);
        }
    }

    public function delete(string $id)
    {
        try {
            foreach ($this->dte["otros_documentos"] as $key => $document) {
                if ($document["id"] == $id) {
                    unset($this->dte["otros_documentos"][$key]);
                }
            }

            session(["dte" => $this->dte]);
            return response()->json([
                "success" => true,
                "message" => "Documento asociado eliminado correctamente",
                "table_data" => view("layouts.partials.ajax.business.table-associated-documents", [
                    "dte" => $this->dte
                ])->render(),
                "table" => "associated-documents",
                "table_data_2" => view("layouts.partials.ajax.business.table-associated-doctors", [
                    "dte" => $this->dte
                ])->render(),
                "table_2" => "associated-doctors"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Error al eliminar documento asociado",
                "error" => $e->getMessage()
            ]);
        }
    }
}
