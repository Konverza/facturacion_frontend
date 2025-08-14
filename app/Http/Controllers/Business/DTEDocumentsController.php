<?php

namespace App\Http\Controllers\Business;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Business;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use NumberFormatter;

class DTEDocumentsController extends Controller
{

    public $dte;
    public $business_id;
    public $business;
    public $dtes;

    public function __construct()
    {
        $this->dte = session("dte");
        $this->business_id = session("business");
        $this->business = Business::find($this->business_id);
        $this->dtes = Http::timeout(30)->get(env("OCTOPUS_API_URL") . '/dtes/?nit=' . $this->business->nit)->json();
    }

    public function store(Request $request)
    {
        try {

            if (isset($this->dte["documentos_retencion"]) && count($this->dte["documentos_retencion"]) > 0) {
                foreach ($this->dte["documentos_retencion"] as $documento) {
                    if ($documento["tipo_generacion"] !== $request->tipo_generacion) {
                        return response()->json([
                            "success" => false,
                            "message" => "Solo es posible agregar un tipo de documento"
                        ]);
                    }
                }
            }

            $this->dte["documentos_retencion"][] = [
                "id" => rand(1, 1000),
                "tipo_generacion" => $request->tipo_generacion,
                "tipo_documento" => 1,
                "numero_documento" => $request->numero_documento,
                "codigo_retencion" => $request->codigo_tributo,
                "descripcion_retencion" => $request->descripcion_retencion,
                "fecha_documento" => $request->fecha_documento,
                "monto_sujeto_retencion" => $request->monto_sujeto_retencion,
                "iva_retenido" => $request->iva_retenido
            ];

            $this->totals();
            session(["dte" => $this->dte]);
            return response()->json([
                "success" => true,
                "message" => "Documento agregado correctamente",
                "table_data" => view("layouts.partials.ajax.business.table-documents-retention", [
                    "dte" => $this->dte
                ])->render(),
                "total_iva_retenido_texto" => $this->convertNumbertToLetter($this->dte["total_iva_retenido"]),
                "modal" => "add-documento-fisico",
                "table" => "documents-retention"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function selected(Request $request)
    {
        try {
            $codGeneracion = $request->codGeneracion;
            $dtes = $this->dtes["items"] ?? [];
            $dte = null;
            foreach ($dtes as $dte_) {
                if ($dte_["codGeneracion"] === $codGeneracion) {
                    $dte = $dte_;
                    break;
                }
            }

            if ($dte === null) {
                return response()->json([
                    "success" => false,
                    "message" => "No se encontró el documento electrónico."
                ]);
            }

            $monto = 0;
            $dte = json_decode(json_encode($dte), true);
            $documento = json_decode($dte["documento"], true);

            if (isset($documento["resumen"]["totalGravada"])) {
                $monto = $documento["resumen"]["totalGravada"];
            } elseif (isset($documento["resumen"]["totalPagar"])) {
                $monto = $documento["resumen"]["totalPagar"];
            } else {
                return response()->json([
                    "success" => false,
                    "message" => "No se encontró un monto válido en el documento."
                ]);
            }

            return response()->json([
                "success" => true,
                "monto" => $monto
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function storeElectric(Request $request)
    {
        try {

            $codGeneracion = $request->cod_generacion;
            $dtes = $this->dtes["items"] ?? [];

            $dte = null;
            foreach ($dtes as $dte_) {
                if ($dte_["codGeneracion"] === $codGeneracion) {
                    $dte = $dte_;
                    break;
                }
            }

            if ($dte === null) {
                return response()->json([
                    "success" => false,
                    "message" => "No se encontró el documento electrónico."
                ]);
            }

            $dte = json_decode(json_encode($dte), true);
            $documento = json_decode($dte["documento"], true);

            if (isset($this->dte["documentos_retencion"]) && count($this->dte["documentos_retencion"]) > 0) {
                foreach ($this->dte["documentos_retencion"] as $documento) {
                    if ($documento["tipo_generacion"] !== $dte["tipo_dte"]) {
                        return response()->json([
                            "success" => false,
                            "message" => "Solo es posible agregar un tipo de documento"
                        ]);
                    }
                }
            }

            $this->dte["documentos_retencion"][] = [
                "id" => rand(1, 1000),
                "tipo_generacion" => $dte["tipo_dte"],
                "tipo_documento" => 1,
                "numero_documento" => $dte["codGeneracion"],
                "codigo_retencion" => $request->codigo_tributo,
                "descripcion_retencion" => $request->descripcion_retencion,
                "fecha_documento" => Carbon::parse($dte["fhProcesamiento"])->format("Y-m-d"),
                "monto_sujeto_retencion" => $request->monto_sujeto_retencion,
                "iva_retenido" => $request->iva_retenido
            ];

            $this->totals();
            session(["dte" => $this->dte]);
            return response()->json([
                "success" => true,
                "message" => "Documento agregado correctamente",
                "table_data" => view("layouts.partials.ajax.business.table-documents-retention", [
                    "dte" => $this->dte
                ])->render(),
                "total_iva_retenido_texto" => $this->convertNumbertToLetter($this->dte["total_iva_retenido"]),
                "modal" => "add-documento-electronico",
                "table" => "documents-retention"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function storeHacienda(Request $request)
    {
        try {
            if (isset($this->dte["documentos_retencion"]) && count($this->dte["documentos_retencion"]) > 0) {
                foreach ($this->dte["documentos_retencion"] as $documento) {
                    if ($documento["tipo_generacion"] !== $request->tipo_dte) {
                        return response()->json([
                            "success" => false,
                            "message" => "Solo es posible agregar un tipo de documento"
                        ]);
                    }
                    if ($documento["numero_documento"] === $request->cod_generacion) {
                        return response()->json([
                            "success" => false,
                            "message" => "El documento ya ha sido agregado"
                        ]);
                    }
                }
            }

            $this->dte["documentos_retencion"][] = [
                "id" => rand(1, 1000),
                "tipo_generacion" => $request->tipo_dte,
                "tipo_documento" => 2,
                "numero_documento" => $request->cod_generacion,
                "codigo_retencion" => $request->codigo_tributo,
                "descripcion_retencion" => $request->descripcion_retencion,
                "fecha_documento" => $request->fecha_emision,
                "monto_sujeto_retencion" => $request->monto_sujeto_retencion,
                "iva_retenido" => $request->iva_retenido
            ];

            $this->totals();
            session(["dte" => $this->dte]);
            return response()->json([
                "success" => true,
                "message" => "Documento agregado correctamente",
                "table_data" => view("layouts.partials.ajax.business.table-documents-retention", [
                    "dte" => $this->dte
                ])->render(),
                "total_iva_retenido_texto" => $this->convertNumbertToLetter($this->dte["total_iva_retenido"]),
                "modal" => "add-documento-hacienda",
                "table" => "documents-retention"
            ]);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function delete(string $id)
    {
        try {
            $this->dte["documentos_retencion"] = array_filter(
                $this->dte["documentos_retencion"],
                function ($documento) use ($id) {
                    return $documento["id"] != $id;
                }
            );

            $this->totals();
            session(["dte" => $this->dte]);
            return response()->json([
                "success" => true,
                "message" => "Documento eliminado correctamente",
                "table_data" => view("layouts.partials.ajax.business.table-documents-retention", [
                    "dte" => $this->dte
                ])->render(),
                "table" => "documents-retention",
                "total_iva_retenido_texto" => $this->convertNumbertToLetter($this->dte["total_iva_retenido"])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function totals()
    {
        $this->dte["monto_sujeto_retencion_total"] = array_sum(array_column($this->dte["documentos_retencion"] ?? 0, "monto_sujeto_retencion"));
        $this->dte["total_iva_retenido"] = array_sum(array_column($this->dte["documentos_retencion"] ?? 0, "iva_retenido"));
        $this->dte["total_iva_retenido_texto"] = $this->convertNumbertToLetter($this->dte["total_iva_retenido"]);
        session(["dte" => $this->dte]);
    }

    public function convertNumbertToLetter($monto)
    {
        $formatter = new NumberFormatter("es", NumberFormatter::SPELLOUT);
        $dolares = floor($monto);
        $centavos = round(($monto - $dolares) * 100);
        $texto = ucfirst($formatter->format($dolares)) . " dólar" . ($dolares != 1 ? "es" : "");
        if ($centavos > 0) {
            $texto .= " con " . $formatter->format($centavos) . " centavo" . ($centavos != 1 ? "s" : "");
        }
        return $texto;
    }

    public function queryDTE(Request $request)
    {

        try {
            $business = Business::find(session("business"));

            $url = env("URL_CONSULTA");
            $response = Http::get($url, [
                "codigoGeneracion" => $request->codigoGeneracion,
                "fechaEmi" => $request->fechaEmi,
                "ambiente" => env("AMBIENTE_HACIENDA")
            ]);

            if (!$response->successful()) {
                return response()->json([
                    "success" => false,
                    "message" => "Error al consultar el DTE, Hacienda no proporcionó datos"
                ]);
            }

            $data = $response->json();

            if ($data["estadoDoc"] == "Error") {
                return response()->json([
                    "success" => false,
                    "message" => $data["descripcionEstado"] ?? "No se encontró información relacionada con este DTE"
                ]);
            }

            if ($data["numeIdenRecep"] != $business->nit) {
                return response()->json([
                    "success" => false,
                    "message" => "El NIT del receptor del DTE no coincide con el NIT de la empresa"
                ]);
            }

            if (!in_array($data["tipoDte"], ["01", "03", "14"])) {
                return response()->json([
                    "success" => false,
                    "message" => "El tipo de DTE no es válido, permitidos: Consumidor Final, Crédito Fiscal, Factura de Sujeto Excluido"
                ]);
            }

            return response()->json([
                "success" => true,
                "message" => "DTE consultado correctamente",
                "comprobante_retencion" => true,
                "data" => [
                    "tipoDte" => $data["tipoDte"],
                    "fechaEmision" => $request->fechaEmi,
                    "codGeneracion" => $request->codigoGeneracion,
                    "sujetoRetencion" => $data["tipoDte"] == "01" ? round($data["documento"]["resumen"]["subTotal"] / 1.13, 2) : $data["documento"]["resumen"]["subTotal"],
                    "ivaRetenido" => $data["documento"]["resumen"]["ivaRete1"] ?? 0,
                    "descripcion" => "Retención al DTE: {$request->codigoGeneracion}"
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage() . " " . $e->getFile() . ":" . $e->getLine()
            ]);
        }
    }
}
