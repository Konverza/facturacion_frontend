<?php

namespace App\Http\Controllers\Business;

use App\Exports\ConsumidorFinal;
use App\Exports\Contribuyente;
use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessCustomer;
use App\Models\BusinessProduct;
use App\Models\BusinessProductMovement;
use App\Models\BusinessUser;
use App\Models\CuentasCobrar;
use App\Models\DTE;
use App\Models\InvoiceBag;
use App\Models\InvoiceBagInvoice;
use App\Models\PuntoVenta;
use App\Models\Sucursal;
use App\Models\Tributes;
use App\Services\OctopusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CustomerListImport;
use Illuminate\Support\Arr;

class DTEController extends Controller
{
    public $octopus_service;
    public $unidades_medidas;
    public $departamentos;
    public $tipos_documentos;
    public $actividades_economicas;
    public $countries;
    public $recinto_fiscal;
    public $regimen_exportacion;
    public $tipos_establecimientos;
    public $formas_pago;
    public $tipo_servicio;
    public $modo_transporte;
    public $incoterms;
    public $bienTitulo;
    public $dte;

    public function __construct()
    {
        $this->octopus_service = new OctopusService();
        $this->unidades_medidas = $this->octopus_service->getCatalog("CAT-014");
        $this->departamentos = $this->octopus_service->getCatalog("CAT-012");
        $this->tipos_documentos = $this->octopus_service->getCatalog("CAT-022");
        $this->actividades_economicas = $this->octopus_service->getCatalog("CAT-019", null, true, true);
        $this->countries = $this->octopus_service->getCatalog("CAT-020");
        $this->recinto_fiscal = $this->octopus_service->getCatalog("CAT-027", null, true, true);
        $this->regimen_exportacion = $this->octopus_service->getCatalog("CAT-028", null, true, true);
        $this->tipos_establecimientos = $this->octopus_service->getCatalog("CAT-009");
        $this->formas_pago = $this->octopus_service->getCatalog("CAT-017");
        $this->tipo_servicio = $this->octopus_service->getCatalog("CAT-010");
        $this->modo_transporte = $this->octopus_service->getCatalog("CAT-030");
        $this->incoterms = $this->octopus_service->getCatalog("CAT-031", null, true, true);
        $this->bienTitulo = $this->octopus_service->getCatalog("CAT-025");
        $this->dte = session("dte", []);
        // Asegurar llaves por defecto para evitar "Undefined array key" en flujos masivos / JSON
        $this->ensureDteDefaults();
        session(['dte' => $this->dte]);
    }

    public function create(Request $request)
    {
        try {
            $business_user = BusinessUser::where("business_id", session("business"))
                ->where("user_id", auth()->user()->id)
                ->first();
            $number = $request->input("document_type");
            if (!$number) { // fallback seguro si no se envía el parámetro
                $number = $this->dte['type'] ?? '01';
            }
            $id = $request->input("id") ?? "";
            $business_products = BusinessProduct::where("business_id", session("business"))
                ->whereIn("estado_stock", ["disponible", "por_agotarse"])
                ->get();
            $business_customers = BusinessCustomer::where("business_id", $business_user->business_id)->get();
            $business = Business::find($business_user->business_id);
            $datos_empresa = $this->octopus_service->get("/datos_empresa/nit/" . $business->nit);
            if ($id) {
                $dte = DTE::find($id);
                $this->dte = json_decode($dte->content, true);
                $this->dte["id"] = $id;
                $this->dte["type"] = $dte->type;
                if (!$request->input("use_template")) {
                    $this->dte["status"] = $dte->status;
                    $this->dte["name"] = $dte->name;
                } else {
                    $this->dte["status"] = null;
                    $this->dte["name"] = null;
                    $this->dte["use_template"] = true;
                }
            } else {
                $this->dte["status"] = null;
                $this->dte["name"] = null;
            }

            if (session()->has("dte") && session("dte.type") !== $number) {
                session()->forget("dte");
                $this->dte = [];
            }

            $this->dte["type"] = $number;
            session(["dte" => $this->dte]);

            $types = [
                '01' => 'Factura Consumidor Final',
                '03' => 'Comprobante de crédito fiscal',
                '04' => 'Nota de Remisión',
                '05' => 'Nota de crédito',
                '06' => 'Nota de débito',
                '07' => 'Comprobante de retención',
                '11' => 'Factura de exportación',
                '14' => 'Factura de sujeto excluido',
                '15' => 'Comprobante de Donación'
            ];

            $document_type = $types[$number];
            $currentDate = date("Y-m-d");

            if (!$id || $id !== "") {
                if ($number !== "15") {
                    $dteProductController = new DTEProductController();
                    $dteProductController->totals();
                }
            }

            $sucursals = Sucursal::where("business_id", session("business"))->get()->pluck("nombre", "id")->toArray();

            $default_pos = $business_user->default_pos_id ? PuntoVenta::with("sucursal")->find($business_user->default_pos_id) : null;


            // Municipio: sólo si existe customer.departamento y está en catálogo
            $municipios = [];
            if (isset($this->dte['customer']['departamento']) && $this->dte['customer']['departamento'] && isset($this->departamentos[$this->dte['customer']['departamento']])) {
                try {
                    $municipios = $this->getMunicipios($this->dte['customer']['departamento']);
                } catch (\Throwable $e) {
                    Log::warning('No se pudieron obtener municipios: ' . $e->getMessage());
                }
            }

            $data = [
                "business" => $business,
                "sucursals" => $sucursals,
                "business_user" => $business_user,
                "default_pos" => $default_pos,
                "document_type" => $document_type,
                "currentDate" => $currentDate,
                "departamentos" => $this->departamentos,
                "number" => $number,
                "recintoFiscal" => $this->recinto_fiscal,
                "regimenExportacion" => $this->regimen_exportacion,
                "business_products" => $business_products,
                "business_customers" => $business_customers,
                "unidades_medidas" => $this->unidades_medidas,
                "tipos_documentos" => $this->tipos_documentos,
                "actividades_economicas" => $this->actividades_economicas,
                "countries" => $this->countries,
                "datos_empresa" => $datos_empresa,
                "tipos_establecimientos" => $this->tipos_establecimientos,
                "dte" => $this->dte,
                "municipios" => $municipios,
                "metodos_pago" => $this->formas_pago,
                "tipo_servicio" => $this->tipo_servicio,
                "modo_transporte" => $this->modo_transporte,
                "incoterms" => $this->incoterms,
                "bienTitulo" => $this->bienTitulo,
                // "dtes" => $dtes
            ];

            $view = "";
            switch ($number) {
                case "01":
                    $view = "business.dtes.factura";
                    break;

                case "03":
                    $view = "business.dtes.comprobante_credito_fiscal";
                    break;

                case "04":
                    $view = "business.dtes.nota_remision";
                    break;

                case "05":
                    $view = "business.dtes.nota_credito";
                    break;

                case "06":
                    $view = "business.dtes.nota_debito";
                    break;

                case "07":
                    $view = "business.dtes.comprobante_retencion";
                    break;

                case "11":
                    $view = "business.dtes.factura_exportacion";
                    break;

                case "14":
                    $view = "business.dtes.factura_sujeto_excluido";
                    break;

                case "15":
                    $view = "business.dtes.comprobante_donacion";
                    break;
            }
            return view($view, $data);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return redirect()->route("business.index")
                ->with("error", "Error")->with("error_message", "Ha ocurrido un error al cargar la vista");
        }
    }

    public function cancel()
    {
        session()->forget('dte');
        return redirect()->route("business.index");
    }

    public function factura(Request $request)
    {
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

        $data = $this->processDTE($request, "01", "/factura/");
        $this->handleResponse($data, $request);
    }

    public function credito_fiscal(Request $request)
    {
        if (!$request->has("save_as_template")) {
            $request->validate([
                "nrc_customer" => "required|string",
            ]);
            $numero_documento = $request->numero_documento;
            $isNIT = strlen($numero_documento) === 14 && ctype_digit($numero_documento);
            $isDUI = strlen($numero_documento) === 9 && ctype_digit($numero_documento);

            if (
                !$isNIT && !$isDUI
            ) {
                return redirect()->back()->withErrors([
                    'numero_documento' => 'El número de documento debe tener exactamente 14 o 9 dígitos'
                ]);
            }

            if ($request->nrc_customer && strlen($request->nrc_customer) > 8) {
                return redirect()->back()->withErrors([
                    'nrc_customer' => 'El NRC debe tener como máximo 8 dígitos.'
                ]);
            }
        }

        $data = $this->processDTE($request, "03", "/credito_fiscal/");
        $response = $this->handleResponse($data, $request);
        if ($response === "PROCESADO") {
            return redirect()->route('business.documents.index')
                ->with([
                    'success' => "Exito",
                    'success_message' => "Documento generado correctamente",
                ]);
        } elseif ($response === "BORRADOR") {
            return redirect()->route('business.index')
                ->with('success', "Exito")
                ->with(
                    "success_message",
                    "Documento guardado como borrador"
                );
        } else {
            return redirect()->route('business.documents.index')
                ->with('error', "Error")
                ->with(
                    "error_message",
                    "Ha ocurrido un error al generar el documento."
                );
        }
    }

    public function nota_remision(Request $request)
    {
        $request->validate([
            "bienTitulo" => "required|string",
        ]);

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

        $data = $this->processDTE($request, "04", "/nota_remision/");
        $response = $this->handleResponse($data, $request);
        if ($response === "PROCESADO") {
            return redirect()->route('business.documents.index')
                ->with([
                    'success' => "Exito",
                    'success_message' => "Documento generado correctamente",
                ]);
        } elseif ($response === "BORRADOR") {
            return redirect()->route('business.index')
                ->with('success', "Exito")
                ->with(
                    "success_message",
                    "Documento guardado como borrador"
                );
        } else {
            return redirect()->route('business.documents.index')
                ->with('error', "Error")
                ->with(
                    "error_message",
                    "Ha ocurrido un error al generar el documento."
                );
        }
    }

    public function nota_credito(Request $request)
    {
        $request->validate([
            "nrc_customer" => "required|string",
        ]);

        $numero_documento = $request->numero_documento;
        $isNIT = strlen($numero_documento) === 14 && ctype_digit($numero_documento);
        $isDUI = strlen($numero_documento) === 9 && ctype_digit($numero_documento);
        if (
            !$isNIT && !$isDUI
        ) {
            return redirect()->back()->withErrors([
                'numero_documento' => 'El número de documento debe tener exactamente 14 o 9 dígitos'
            ]);
        }

        if ($request->nrc_customer && strlen($request->nrc_customer) > 8) {
            return redirect()->back()->withErrors([
                'nrc_customer' => 'El NRC debe tener como máximo 8 dígitos.'
            ]);
        }

        $data = $this->processDTE($request, "05", "/nota_credito/");
        $response = $this->handleResponse($data, $request);
        if ($response === "PROCESADO") {
            return redirect()->route('business.documents.index')
                ->with([
                    'success' => "Exito",
                    'success_message' => "Documento generado correctamente",
                ]);
        } elseif ($response === "BORRADOR") {
            return redirect()->route('business.index')
                ->with('success', "Exito")
                ->with(
                    "success_message",
                    "Documento guardado como borrador"
                );
        } else {
            return redirect()->route('business.documents.index')
                ->with('error', "Error")
                ->with(
                    "error_message",
                    "Ha ocurrido un error al generar el documento."
                );
        }
    }

    public function nota_debito(Request $request)
    {
        $request->validate([
            "nrc_customer" => "required|string",
        ]);

        $numero_documento = $request->numero_documento;
        $isNIT = strlen($numero_documento) === 14 && ctype_digit($numero_documento);
        $isDUI = strlen($numero_documento) === 9 && ctype_digit($numero_documento);
        if (
            !$isNIT && !$isDUI
        ) {
            return redirect()->back()->withErrors([
                'numero_documento' => 'El número de documento debe tener exactamente 14 o 9 dígitos'
            ]);
        }

        if ($request->nrc_customer && strlen($request->nrc_customer) > 8) {
            return redirect()->back()->withErrors([
                'nrc_customer' => 'El NRC debe tener como máximo 8 dígitos.'
            ]);
        }

        $data = $this->processDTE($request, "06", "/nota_debito/");
        $this->handleResponse($data, $request);
    }

    public function comprobante_retencion(Request $request)
    {
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

        if ($request->nrc_customer && strlen($request->nrc_customer) > 8) {
            return redirect()->back()->withErrors([
                'nrc_customer' => 'El NRC debe tener como máximo 8 dígitos.'
            ]);
        }

        $data = $this->processDTE($request, "07", "/comprobante_retencion/");
        $this->handleResponse($data, $request);
    }

    public function factura_exportacion(Request $request)
    {

        if (!$request->has("save_as_template")) {
            $request->validate([
                "regimen_exportacion" => "nullable|string",
                "recinto_fiscal" => "nullable|string",
                "tipo_item_exportar" => "required|string",
                "codigo_pais" => "required|string",
                "tipo_persona" => "required|string",
            ]);
        }

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
        $data = $this->processDTE($request, "11", "/factura_exportacion/");
        $this->handleResponse($data, $request);
    }

    public function factura_sujeto_excluido(Request $request)
    {
        if (!$request->has("save_as_template")) {
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
        }

        $data = $this->processDTE($request, "14", "/sujeto_excluido/");
        $this->handleResponse($data, $request);
    }

    public function comprobante_donacion(Request $request)
    {
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

        $data = $this->processDTE($request, "15", "/comprobante_donacion/");
        $this->handleResponse($data, $request);
    }

    public function buildDTE(Request $request, $type, $business_id)
    {
        $business = Business::find($business_id);
        $receptor = $this->getReceptorData($request, $type);

        $punto_venta = PuntoVenta::find($request->pos_id);

        if (!$punto_venta) {
            return redirect()->back()->withErrors(['pos_id' => 'El punto de venta seleccionado no es válido.']);
        }

        $extension = $this->extension($request);

        $dte = [
            "fecEmi" => $request->fecEmi ?? null,
            "horEmi" => $request->horEmi ?? null,
            "nit" => $business->nit,
            "cuerpoDocumento" => [],
            "documentoRelacionado" => $this->documentosRelacionados(),
            "otrosDocumentos" => $this->otrosDocumentos(),
            "ventaTercero" => $this->ventaTerceros($request),
            "resumen" => [],
            "extension" => $extension,
            "apendice" => null,
            "numPagoElectronico" => null,
            "sucursal" => [
                "codSucursal" => $punto_venta->sucursal->codSucursal,
                "codPuntoVenta" => $punto_venta->codPuntoVenta,
                "departamento" => $punto_venta->sucursal->departamento,
                "municipio" => $punto_venta->sucursal->municipio,
                "complemento" => $punto_venta->sucursal->complemento,
                "telefono" => $punto_venta->sucursal->telefono,
                "correo" => $punto_venta->sucursal->correo,
            ]
        ];

        if ($type === "14") {
            $dte["sujetoExcluido"] = $receptor;
        } elseif ($type === "15") {
            $dte["donante"] = $receptor;
        } else {
            $dte["receptor"] = $receptor;
        }

        if ($type === "11") {
            $dte["emisor"]["regimen"] = $request->regimen_exportacion;
            $dte["emisor"]["recintoFiscal"] = $request->recinto_fiscal;
            $dte["emisor"]["tipoItemExpor"] = $request->tipo_item_exportar;

            $this->dte["emisor"]["regimen"] = $request->regimen_exportacion;
            $this->dte["emisor"]["recintoFiscal"] = $request->recinto_fiscal;
            $this->dte["emisor"]["tipoItemExpor"] = $request->tipo_item_exportar;
            session(["dte" => $this->dte]);
        }
        if ($type === "04") {
            $dte["resumen"]["descuNoSuj"] = round((float) $this->dte["descuento_venta_no_sujeta"] ?? 0, 2);
            $dte["resumen"]["descuExenta"] = round((float) $this->dte["descuento_venta_exenta"] ?? 0, 2);
            $dte["resumen"]["descuGravada"] = round((float) $this->dte["descuento_venta_gravada"] ?? 0, 2);
            $dte["resumen"]["porcentajeDescuento"] = 0;
        } elseif ($type === "07") {
            $dte["resumen"]["totalSujetoRetencion"] = round((float) $this->dte["monto_sujeto_retencion_total"] ?? 0, 2);
            $dte["resumen"]["totalIVAretenido"] = round((float) $this->dte["total_iva_retenido"] ?? 0, 2);
        } elseif ($type === "11") {
            $dte["resumen"]["totalGravada"] = round((float) $this->dte["total_ventas_gravadas"] ?? 0, 2);
            $dte["resumen"]["descuento"] = round((float) $this->dte["descuento_venta_gravada"] ?? 0, 2);
            $dte["resumen"]["condicionOperacion"] = $request->condicion_operacion;
            $dte["resumen"]["pagos"] = $this->pagos();
            $dte["resumen"]["flete"] = round((float) $this->dte["flete"] ?? 0, 2);
            $dte["resumen"]["seguro"] = round((float) $this->dte["seguro"] ?? 0, 2);
            $dte["resumen"]["codIncoterms"] = $request->incoterms ?? null;
            $dte["resumen"]["descIncoterms"] = $this->incoterms[$request->incoterms] ?? null;
            $dte["resumen"]["observaciones"] = $this->dte["extension"]["observaciones"] ?? null;
        } elseif ($type === "14") {
            $dte["resumen"]["descu"] = round((float) $this->dte["total_descuentos"] ?? 0, 2);
            $dte["resumen"]["totalDescu"] = round((float) $this->dte["total_descuentos"] ?? 0, 2);
            $dte["resumen"]["ivaRete1"] = $this->dte["retener_iva"] === "active" ? round((float) $this->dte["total_iva_retenido"] ?? 0, 2) : 0;
            $dte["resumen"]["condicionOperacion"] = $request->condicion_operacion;
            $dte["resumen"]["reteRenta"] = round((float) $this->dte["isr"] ?? 0, 2);
            $dte["resumen"]["observaciones"] = $this->dte["extension"]["observaciones"] ?? null;
        } elseif ($type === "15") {
            $dte["resumen"]["pagos"] = $this->pagos();
        } else {
            $dte["resumen"]["descuNoSuj"] = round((float) $this->dte["descuento_venta_no_sujeta"] ?? 0, 2);
            $dte["resumen"]["descuExtenta"] = round((float) $this->dte["descuento_venta_exenta"] ?? 0, 2);
            $dte["resumen"]["descuGravada"] = round((float) $this->dte["descuento_venta_gravada"] ?? 0, 2);
            $dte["resumen"]["porcentajeDescuento"] = 0;
            $dte["resumen"]["ivaRete1"] = $this->dte["retener_iva"] === "active" ? round((float) $this->dte["total_iva_retenido"] ?? 0, 2) : 0;
            $dte["resumen"]["reteRenta"] = $this->dte["retener_renta"] === "active" ? round((float) $this->dte["isr"] ?? 0, 2) : 0;
            $dte["resumen"]["saldoFavor"] = 0;
            $dte["resumen"]["condicionOperacion"] = $request->condicion_operacion;
            $dte["resumen"]["pagos"] = $this->pagos();
        }

        if ($type === "03" || $type === "05" || $type === "06") {
            $dte["resumen"]["ivaPerci1"] = $this->dte["percibir_iva"] === "active" ? round((float) $this->dte["total_iva_retenido"] ?? 0, 2) : 0;
        }

        if ($type !== "14" && $type !== "15") {
            $dte["resumen"]["tributos"] = $this->getTributos();
        }

        if ($type === "07") {
            $dte["cuerpoDocumento"] = $this->getCuerpoDocumentoComprobanteRetencion();
        } elseif ($type === "15") {
            $dte["cuerpoDocumento"] = $this->getCuerpoDocumentoComprobanteDonacion();
        } else {
            if (isset($this->dte["products"]) && count($this->dte["products"]) > 0) {
                foreach ($this->dte["products"] as $product) {
                    $dte["cuerpoDocumento"][] = $this->getProductData($product, $type, $this->dte["documentos_relacionados"] ?? []);
                }
            }
        }

        // Construir apéndice si hay datos de sucursal del cliente o tiene la característica habilitada
        $apendice = [];

        // Agregar indicador de que tiene sucursales habilitadas
        if ($business->has_customer_branches) {
            $apendice[] = [
                "campo" => "hasBranches",
                "etiqueta" => "Tiene Sucursal",
                "valor" => "1"
            ];
        }

        // Si hay sucursal seleccionada, agregar sus datos
        if (isset($this->dte['customer_branch'])) {
            $branch = $this->dte['customer_branch'];

            $apendice[] = [
                "campo" => "codigoSucursal",
                "etiqueta" => "Sucursal Código",
                "valor" => $branch['branch_code'] ?? ''
            ];

            $apendice[] = [
                "campo" => "nombreSucursal",
                "etiqueta" => "Sucursal Cliente",
                "valor" => $branch['nombre'] ?? ''
            ];

            $apendice[] = [
                "campo" => "departamentoSucursal",
                "etiqueta" => "Departamento Sucursal",
                "valor" => $branch['departamento'] ?? ''
            ];

            $apendice[] = [
                "campo" => "municipioSucursal",
                "etiqueta" => "Municipio Sucursal",
                "valor" => $branch['municipio'] ?? ''
            ];

            $apendice[] = [
                "campo" => "complementoSucursal",
                "etiqueta" => "Dirección Sucursal",
                "valor" => $branch['complemento'] ?? ''
            ];
        }

        // Agregar número de orden de compra si existe
        if (isset($this->dte['orden_compra']) && !empty($this->dte['orden_compra'])) {
            $apendice[] = [
                "campo" => "OrdenCompra",
                "etiqueta" => "Número de Orden de Compra",
                "valor" => $this->dte['orden_compra']
            ];
        }

        // Asignar apéndice al DTE solo si hay elementos
        $dte["apendice"] = !empty($apendice) ? $apendice : null;

        return $dte;
    }

    public function processDTE(Request $request, $type, $endpoint)
    {
        try {
            // Guardar orden de compra si existe
            if ($request->has('orden_compra') && $request->orden_compra) {
                $this->dte['orden_compra'] = $request->orden_compra;
            }

            // Guardar información de sucursal del cliente si fue seleccionada
            if ($request->has('customer_branch_id') && $request->customer_branch_id) {
                $branch = \App\Models\BusinessCustomersBranch::find($request->customer_branch_id);
                if ($branch) {
                    $this->dte['customer_branch'] = [
                        'id' => $branch->id,
                        'branch_code' => $branch->branch_code,
                        'nombre' => $branch->nombre,
                        'departamento' => $branch->departamento,
                        'municipio' => $branch->municipio,
                        'complemento' => $branch->complemento,
                    ];
                }
            }

            // Guardar en sesión
            session(['dte' => $this->dte]);

            if ($this->dte["type"] !== "07") {
                if (!isset($this->dte["products"]) || count($this->dte["products"]) === 0) {
                    if ($request->boolean('json_mode')) {
                        return ['estado' => 'RECHAZADO', 'observaciones' => 'Debe agregar al menos un producto'];
                    }
                    return redirect()->back()->with(['error' => "Error", 'error_message' => "Debe agregar al menos un producto"])->send();
                }
            }

            // Validación de stock: sólo para tipos que impactan inventario
            if (!in_array($this->dte['type'], ["07", "14", "04", "15"])) {
                // Obtener sucursal y POS
                $sucursalId = null;
                $posId = null;
                $pos = null;

                if ($request->pos_id) {
                    $pos = PuntoVenta::find($request->pos_id);
                    $posId = $pos?->id;
                    $sucursalId = $pos?->sucursal_id;
                }

                // Agrupar cantidad solicitada por producto de base de datos con stock habilitado
                $cantidadesPorProducto = [];
                $productosCargados = [];
                foreach ($this->dte['products'] as $p) {
                    if (is_array($p) && isset($p['product']) && is_array($p['product']) && isset($p['product']['id'])) {
                        $bp = $productosCargados[$p['product']['id']] ?? BusinessProduct::find($p['product']['id']);
                        if ($bp) {
                            $productosCargados[$bp->id] = $bp; // cache simple
                            if ($bp->has_stock) {
                                $cantidadesPorProducto[$bp->id] = ($cantidadesPorProducto[$bp->id] ?? 0) + (float) ($p['cantidad'] ?? 0);
                            }
                            // Si no tiene stock habilitado, se ignora (no se valida), según condición #2
                        }
                        // Si no existe en base de datos, se ignora (no se valida), según condición #2
                    }
                    // Si el item no es de base de datos, se ignora (no se valida)
                }

                // Verificar existencia suficiente para cada producto con stock habilitado
                $faltantes = [];
                foreach ($cantidadesPorProducto as $productId => $cantidadSolicitada) {
                    $bp = $productosCargados[$productId] ?? BusinessProduct::find($productId);
                    Log::info("Validando stock para producto ID {$productId}: solicitado {$cantidadSolicitada}");
                    Log::info("Producto info: " . json_encode($bp));
                    if ($bp && $bp->has_stock && !$bp->is_global) {
                        // Determinar stock disponible según orden: POS > Sucursal > Global
                        $disponible = 0;
                        $inventorySource = 'none';

                        // Prioridad 1: POS con inventario independiente
                        if ($posId && $pos && $pos->has_independent_inventory) {
                            $posStock = $bp->getStockForPos($posId);
                            $disponible = $posStock ? (float) $posStock->stockActual : 0;
                            $inventorySource = 'pos';
                        }
                        // Prioridad 2: Sucursal (si POS no tiene inventario independiente o no existe POS)
                        elseif ($sucursalId) {
                            $branchStock = $bp->getStockForBranch($sucursalId);
                            $disponible = $branchStock ? (float) $branchStock->stockActual : 0;
                            $inventorySource = 'branch';
                        }
                        // Prioridad 3: Global (si el producto es global)
                        elseif ($bp->is_global) {
                            $disponible = (float) $bp->stockActual;
                            $inventorySource = 'global';
                        }
                        // Sin contexto de inventario
                        else {
                            $disponible = 0;
                            $inventorySource = 'none';
                        }

                        if ($cantidadSolicitada > $disponible) {
                            $locationInfo = match ($inventorySource) {
                                'pos' => " en el punto de venta seleccionado",
                                'branch' => " en la sucursal seleccionada",
                                'global' => " (inventario global)",
                                default => ""
                            };
                            $faltantes[] = $bp->descripcion . " (solicitado: " . $cantidadSolicitada . ", disponible{$locationInfo}: " . $disponible . ")";
                        }
                    }
                }
                if (count($faltantes) > 0) {
                    if ($request->boolean('json_mode')) {
                        return ['estado' => 'RECHAZADO', 'observaciones' => 'Stock insuficiente para: ' . implode('; ', $faltantes)];
                    }
                    return redirect()->back()->with(['error' => 'Error', 'error_message' => 'Stock insuficiente para: ' . implode('; ', $faltantes)])->send();
                }
            }

            if ($this->dte["type"] === "15") {

                if (!$this->otrosDocumentos()) {
                    if ($request->boolean('json_mode')) {
                        return ['estado' => 'RECHAZADO', 'observaciones' => 'Debe agregar al menos un documento asociado a la donación'];
                    }
                    return redirect()->back()->with(['error' => "Error", 'error_message' => "Debe agregar al menos un documento asociado a la donación"])->send();
                }

                $total_pagar = array_sum(array_column(
                    array_filter($this->dte["products"], function ($product) {
                        return $product["tipo_donacion"] == 1;
                    }),
                    "valor_donado"
                ));
                if ($total_pagar > 0 && !$this->pagos()) {
                    if ($request->boolean('json_mode')) {
                        return ['estado' => 'RECHAZADO', 'observaciones' => 'Debe agregar al menos una forma de pago'];
                    }
                    return redirect()->back()->with(['error' => "Error", 'error_message' => "Debe agregar al menos una forma de pago"])->send();
                }
            }

            if (isset($this->dte["documentos_relacionados"]) && count($this->dte["documentos_relacionados"]) > 0 && isset($this->dte["products"]) && count($this->dte["products"]) > 0) {
                foreach ($this->dte["documentos_relacionados"] as $documento) {
                    $related = false;
                    foreach ($this->dte["products"] as $product) {
                        if (isset($product["documento_relacionado"]) && $product["documento_relacionado"] === $documento["numero_documento"]) {
                            $related = true;
                            break;
                        }
                    }
                    if (!$related) {
                        if ($request->boolean('json_mode')) {
                            return ['estado' => 'RECHAZADO', 'observaciones' => 'Cada documento relacionado debe estar asociado al menos a un producto ingresado.'];
                        }
                        return redirect()->back()->with(['error' => "Error", 'error_message' => "Cada documento relacionado debe estar asociado al menos a un producto ingresado."])->send();
                    }
                }
            }

            if ($this->dte["type"] !== "04") {
                if (isset($this->dte["monto_abonado"]) && isset($this->dte["total_pagar"])) {
                    if (round($this->dte["monto_abonado"], 2) != round($this->dte["total_pagar"], 2)) {
                        $obs = "El monto total pagado no coincide con el total a pagar. Monto abonado: $" . round($this->dte["monto_abonado"], 2) . ", Total a pagar: $" . round($this->dte["total_pagar"], 2);
                        if ($request->boolean('json_mode')) {
                            return ['estado' => 'RECHAZADO', 'observaciones' => $obs];
                        }
                        return redirect()->back()->with(['error' => "Error", 'error_message' => $obs])->send();
                    }
                }
            }

            $business_id = Session::get('business') ?? null;
            $dte = $this->buildDTE($request, $type, $business_id);

            if ($request->input("action") === "draft") {
                if ($request->id_dte !== "" && $request->id_dte !== null) {
                    $this->updateDtePending($request->id_dte, "pending", "Documento actualizado como borrador");
                } else {
                    $this->createDtePending("Documento guardado como borrador");
                }
                session()->forget('dte');
                $data = [
                    "estado" => "BORRADOR",
                    "observaciones" => "Documento guardado como borrador"
                ];
                return $request->boolean('json_mode') ? $data : $data; // (en modo normal seguirá flujo de handleResponse más adelante si se adaptara)
            }

            if ($request->input("action") === "template") {
                if ($request->id_dte !== "" && $request->id_dte !== null) {
                    $this->updateDtePending($request->id_dte, "template", "Documento actualizado como plantilla", $request->template_name);
                } else {
                    $this->createDtePending("Documento guardado como plantilla", "template", $request->template_name);
                }
                session()->forget('dte');
                $data = [
                    "estado" => "PLANTILLA",
                    "observaciones" => "Documento guardado como plantilla"
                ];
                return $request->boolean('json_mode') ? $data : $data;
            }

            // dd($dte);
            $response = Http::timeout(30)->post(env("OCTOPUS_API_URL") . $endpoint, $dte);
            $data = json_decode($response->body(), true) ?? [];
            return $data;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            $this->createDtePending($e->getMessage(), "error");
            $data = [
                "estado" => "RECHAZADO",
                "observaciones" => $e->getMessage()
            ];
            return $data;
        }
    }

    public function handleResponse($data, $request)
    {
        $business_id = Session::get('business') ?? null;
        if (isset($data["estado"])) {
            if ($data["estado"] === "PROCESADO" || $data["estado"] === "CONTINGENCIA") {
                $skipInventory = $request->boolean('skip_inventory');

                // Obtener sucursal y POS usado
                $sucursalId = null;
                $posId = $request->pos_id;
                if ($posId) {
                    $pos = PuntoVenta::find($posId);
                    $sucursalId = $pos?->sucursal_id;
                }

                // Para tipo 14 (Sujeto Excluido), actualizar stocks como entrada (compra)
                if ($this->dte["type"] === "14") {
                    if (!$skipInventory) {
                        $this->updateStocks($data["codGeneracion"], $this->dte["products"], $business_id, "entrada", $sucursalId, "14", $posId);
                    }
                    if ($request->condicion_operacion === 2) {
                        $this->createCXC($data, $request);
                    }
                }
                // Para tipo 05 (Nota de Crédito), devolver stocks (entrada)
                elseif (!$skipInventory && $this->dte["type"] === "05") {
                    $this->updateStocks($data["codGeneracion"], $this->dte["products"], $business_id, "entrada", $sucursalId, $this->dte["type"], $posId);
                }
                // Para ventas normales (tipos: 01, 03, 11, etc.), descontar stocks (salida)
                elseif ($this->dte["type"] !== "07" && $this->dte["type"] !== "04" && $this->dte["type"] !== "06" && $this->dte["type"] !== "15") {
                    if (!$skipInventory) {
                        $this->updateStocks($data["codGeneracion"], $this->dte["products"], $business_id, "salida", $sucursalId, $this->dte["type"], $posId);
                    }
                    if ($request->condicion_operacion === 2) {
                        $this->createCXC($data, $request);
                    }
                }

                if ($request->filled('invoice_bag_invoice_id')) {
                    InvoiceBagInvoice::where('id', $request->input('invoice_bag_invoice_id'))
                        ->update([
                            'status' => 'converted',
                            'converted_at' => now(),
                            'dte_id' => $data['codGeneracion'] ?? null,
                            'individual_converted' => true,
                        ]);
                }

                if ($request->filled('invoice_bag_id')) {
                    $bagId = $request->input('invoice_bag_id');
                    $invoiceIds = $request->input('invoice_bag_invoice_ids');
                    $ids = [];
                    if (is_array($invoiceIds)) {
                        $ids = $invoiceIds;
                    } elseif (is_string($invoiceIds) && $invoiceIds !== '') {
                        $ids = array_filter(explode(',', $invoiceIds));
                    }

                    InvoiceBag::where('id', $bagId)
                        ->update([
                            'status' => 'sent',
                            'sent_dte_codigo' => $data['codGeneracion'] ?? null,
                            'sent_at' => now(),
                        ]);

                    $query = InvoiceBagInvoice::where('invoice_bag_id', $bagId)
                        ->where('status', 'pending');

                    if (!empty($ids)) {
                        $query->whereIn('id', $ids);
                    }

                    $query->update([
                        'status' => 'included',
                        'dte_id' => $data['codGeneracion'] ?? null,
                        'individual_converted' => false,
                    ]);
                }

                if ($request->id_dte !== "" && $request->id_dte !== null && !$request->use_template) {
                    DTE::where("id", $request->id_dte)->delete();
                }

                session()->forget('dte');
                return redirect()->route('business.documents.index')
                    ->with([
                        'success' => "Exito",
                        'success_message' => "Documento generado correctamente",
                    ])->send();
            } elseif ($data["estado"] === "RECHAZADO") {
                if ($request->id_dte !== "" && $request->id_dte !== null && !$request->use_template) {
                    $this->updateDtePending($request->id_dte, "error", $data["observaciones"] ?? "Error al generar el documento");
                } else {
                    $this->createDtePending($data["observaciones"] ?? "Error al generar el documento", "error");
                }
                return redirect()->route('business.documents.index')
                    ->with('error', "Error")
                    ->with(
                        "error_message",
                        "Ha ocurrido un error al generar el documento."
                    )->send();
            } elseif ($data["estado"] === "BORRADOR") {
                return redirect()->route('business.index')
                    ->with('success', "Exito")
                    ->with(
                        "success_message",
                        "Documento guardado como borrador"
                    )->send();
            } elseif ($data["estado"] === "PLANTILLA") {
                return redirect()->route('business.index')
                    ->with('success', "Exito")
                    ->with(
                        "success_message",
                        "Documento guardado como plantilla"
                    )->send();
            }
        } else {
            Log::error($data);
            $this->createDtePending(json_encode($data), "error");
            return redirect()->route('business.documents.index')
                ->with('error', "Error")
                ->with(
                    "error_message",
                    "Ha ocurrido un error al generar el documento."
                )->send();
        }
    }

    public function getReceptorData(Request $request, $type)
    {
        // Helpers locales para normalización y resolución por catálogos
        $normalize = function ($text) {
            if ($text === null)
                return null;
            $text = mb_strtolower(trim((string) $text), 'UTF-8');
            $replacements = [
                'á' => 'a',
                'é' => 'e',
                'í' => 'i',
                'ó' => 'o',
                'ú' => 'u',
                'ñ' => 'n',
                'Á' => 'a',
                'É' => 'e',
                'Í' => 'i',
                'Ó' => 'o',
                'Ú' => 'u',
                'Ñ' => 'n',
            ];
            return strtr($text, $replacements);
        };

        $resolveActividad = function ($value) use ($normalize) {
            if ($value === null || $value === '')
                return [null, null];
            // Acepta valores como "001 - Comercio al por menor" o solo código "001" o solo texto
            $code = null;
            $desc = null;
            $parts = preg_split('/\s*-\s*/', (string) $value, 2);
            if (count($parts) === 2 && ctype_digit($parts[0])) {
                $code = $parts[0];
                $desc = $parts[1];
            } elseif (ctype_digit((string) $value)) {
                $code = (string) $value;
            } else {
                // buscar por descripción aproximada
                foreach ($this->actividades_economicas as $k => $v) {
                    if ($normalize($v) === $normalize($value)) {
                        $code = (string) $k;
                        $desc = $v;
                        break;
                    }
                }
            }
            if ($code !== null && $desc === null) {
                $desc = $this->actividades_economicas[$code] ?? null;
            }
            // Extraer solo descripción después del guion si aplica
            if ($desc !== null && strpos($desc, '-') !== false) {
                $tmp = explode('-', $desc, 2);
                $desc = trim($tmp[1]);
            }
            return [$code, $desc];
        };

        $resolveDepartamento = function ($value) use ($normalize) {
            if ($value === null || $value === '')
                return null;
            // value puede ser código o nombre
            if (isset($this->departamentos[$value]))
                return $value;
            foreach ($this->departamentos as $code => $dep) {
                $nombre = is_array($dep) ? ($dep['nombre'] ?? (is_string($dep) ? $dep : '')) : (string) $dep;
                if ($normalize($nombre) === $normalize($value))
                    return (string) $code;
            }
            return null;
        };

        $resolveMunicipio = function ($departamentoCode, $value) use ($normalize) {
            if ($departamentoCode === null || $value === null || $value === '')
                return null;
            $munis = $this->octopus_service->getCatalog("CAT-012", $departamentoCode);
            if (isset($munis[$value]))
                return $value;
            foreach ($munis as $code => $mun) {
                $nombre = is_array($mun) ? ($mun['nombre'] ?? (is_string($mun) ? $mun : '')) : (string) $mun;
                if ($normalize($nombre) === $normalize($value))
                    return (string) $code;
            }
            return null;
        };

        $normalizeTipoPersona = function ($value) use ($normalize) {
            if ($value === null || $value === '')
                return null;
            // Mapear por nombre al código según CAT correspondiente que viene en $this->tipo_servicio? No hay catálogo directo acá;
            // aplicamos convención más usada en import: buscar key cuyo valor textual coincida
            $map = [
                'juridica' => '02',
                'natural' => '01',
            ];
            $v = $normalize($value);
            return $map[$v] ?? $value; // si ya viene código, lo dejamos
        };

        $actividad = $this->actividades_economicas[$request->actividad_economica] ?? null;
        $descActividad = explode("-", $actividad);
        $descActividad = $descActividad[1] ?? null;

        // Normalizaciones: tipo y número de documento, nrc y teléfono
        $docCode = $this->normalizeDocType($request->tipo_documento);
        $numDoc = $this->normalizeNumeroDocumento($request->numero_documento, $docCode, $type);
        $nrcDigits = $this->onlyDigits($request->nrc_customer);

        // Regla específica para Factura Consumidor Final (type 01):
        // Si el tipo de documento es DUI (13) pero hay NRC presente,
        // convertir el tipo de documento a NIT (36) y asegurar que el número quede sólo con dígitos (sin guiones).
        if ($type === '01' && $docCode === '13' && $nrcDigits) {
            $docCode = '36';
            $numDigits = preg_replace('/\D+/', '', (string) $request->numero_documento);
            $numDoc = $numDigits !== '' ? $numDigits : null;
        }

        // Regla específica para Factura de Sujeto Excluido (type 14):
        // El DUI se escribe sin guion
        if ($type === '14' && $docCode === '13' && $numDoc !== null) {
            $numDoc = preg_replace('/\D+/', '', (string) $numDoc);
        }

        $telefonoStr = ($request->telefono !== null && $request->telefono !== '') ? (string) $request->telefono : null;

        // Resolver actividad, depto/mun, y tipo persona normalizados
        [$codActividad, $descAct] = $resolveActividad($request->actividad_economica);
        $departamentoCode = $resolveDepartamento($request->departamento);
        $municipioCode = $resolveMunicipio($departamentoCode, $request->municipio);
        $tipoPersonaCode = $normalizeTipoPersona($request->tipo_persona);

        if (!isset($this->dte["customer"])) {
            $this->dte["customer"] = [
                "tipoDocumento" => $docCode,
                "numDocumento" => $numDoc,
                "nrc" => $nrcDigits,
                "nombre" => $request->nombre_customer,
                "codActividad" => $codActividad ?? $request->actividad_economica,
                "nombreComercial" => $request->nombre_comercial,
                "departamento" => $departamentoCode ?? "06", // default San Salvador
                "municipio" => $municipioCode ?? "01", // default primer Municipio
                "complemento" => $request->complemento,
                "telefono" => $telefonoStr,
                "correo" => $request->correo,
                "tipoPersona" => $tipoPersonaCode ?? $request->tipo_persona,
            ];
        }

        if ($request->has("save_as_template")) {
            return [
                "nombre" => "Clientes Plantilla",
                "telefono" => null,
                "correo" => null,
                "direccion" => null,
                "tipoDocumento" => null,
                "numDocumento" => null
            ];
        }

        switch ($type) {
            case "01":
                return $request->has("omitir_datos_receptor") ?
                    [
                        "nombre" => "Clientes Varios",
                        "telefono" => null,
                        "correo" => null,
                        "direccion" => null,
                        "tipoDocumento" => null,
                        "numDocumento" => null
                    ] :
                    [
                        "nombre" => $request->nombre_receptor,
                        "telefono" => $telefonoStr,
                        "correo" => $request->correo,
                        "direccion" => [
                            "departamento" => $departamentoCode ?? "06", // default San Salvador
                            "municipio" => $municipioCode ?? "01", // default primer Municipio
                            "complemento" => $request->complemento
                        ],
                        "tipoDocumento" => $docCode,
                        "numDocumento" => $numDoc,
                        "codActividad" => $codActividad ?? $request->actividad_economica,
                        "descActividad" => $descAct ?? $actividad,
                        "nrc" => $nrcDigits ?: null,
                    ];
            case "03":
                return [
                    "nombre" => $request->nombre_customer,
                    "nombreComercial" => $request->nombre_comercial,
                    "codActividad" => $codActividad ?? $request->actividad_economica,
                    "descActividad" => $descAct ?? $descActividad,
                    "telefono" => $telefonoStr,
                    "correo" => $request->correo,
                    "direccion" => [
                        "departamento" => $departamentoCode ?? "06", // default San Salvador
                        "municipio" => $municipioCode ?? "01", // default primer Municipio
                        "complemento" => $request->complemento
                    ],
                    "nit" => $numDoc,
                    "nrc" => $nrcDigits,
                ];
            case "04":
                return [
                    "nombre" => $request->nombre_receptor,
                    "nombreComercial" => $request->nombre_comercial,
                    "codActividad" => $codActividad ?? $request->actividad_economica,
                    "descActividad" => $descAct ?? $descActividad,
                    "telefono" => $telefonoStr,
                    "correo" => $request->correo,
                    "direccion" => [
                        "departamento" => $departamentoCode ?? "06", // default San Salvador
                        "municipio" => $municipioCode ?? "01", // default primer Municipio
                        "complemento" => $request->complemento
                    ],
                    "tipoDocumento" => $docCode,
                    "numDocumento" => $numDoc,
                    "bienTitulo" => $request->bienTitulo,
                ];
            case "05":
            case "06":
                return [
                    "nrc" => $nrcDigits,
                    "nombre" => $request->nombre_customer,
                    "codActividad" => $codActividad ?? $request->actividad_economica,
                    "descActividad" => $descAct ?? $descActividad,
                    "direccion" => [
                        "departamento" => $departamentoCode ?? "06", // default San Salvador
                        "municipio" => $municipioCode ?? "01", // default primer Municipio
                        "complemento" => $request->complemento
                    ],
                    "telefono" => $telefonoStr,
                    "correo" => $request->correo,
                    "nit" => $numDoc,
                    "nombreComercial" => $request->nombre_comercial,
                ];

            case "07":
                $actividad = $this->actividades_economicas[$request->actividad_economica] ?? null;
                $descActividad = explode("-", $actividad);
                $descActividad = trim($descActividad[1] ?? null);
                return [
                    "tipoDocumento" => $docCode,
                    "numDocumento" => $numDoc,
                    "nrc" => $nrcDigits,
                    "nombre" => $request->nombre_customer,
                    "nombreComercial" => $request->nombre_comercial,
                    "codActividad" => $codActividad ?? $request->actividad_economica,
                    "descActividad" => $descAct ?? $descActividad,
                    "direccion" => [
                        "departamento" => $departamentoCode ?? "06", // default San Salvador
                        "municipio" => $municipioCode ?? "01", // default primer Municipio
                        "complemento" => $request->complemento
                    ],
                    "telefono" => $telefonoStr,
                    "correo" => $request->correo,
                ];
            case "11":
                $pais = $this->countries[$request->codigo_pais] ?? null;
                return [
                    "tipoDocumento" => $docCode,
                    "numDocumento" => $numDoc,
                    "nombre" => $request->nombre_customer,
                    "descActividad" => $descAct ?? $descActividad,
                    "codPais" => $request->codigo_pais,
                    "nombrePais" => $pais,
                    "complemento" => $request->complemento,
                    "nombreComercial" => $request->nombre_customer,
                    "tipoPersona" => $tipoPersonaCode ?? $request->tipo_persona,
                    "telefono" => $telefonoStr,
                    "correo" => $request->correo,
                ];
            case "14":
                return [
                    "tipoDocumento" => $docCode,
                    "numDocumento" => $numDoc,
                    "nombre" => $request->nombre_customer,
                    "codActividad" => $codActividad ?? $request->actividad_economica,
                    "descActividad" => $descAct ?? $descActividad,
                    "direccion" => [
                        "departamento" => $departamentoCode ?? "06", // default San Salvador
                        "municipio" => $municipioCode ?? "01", // default primer Municipio
                        "complemento" => $request->complemento
                    ],
                    "telefono" => $telefonoStr,
                    "correo" => $request->correo,
                ];
            case "15":
                $actividad = $this->actividades_economicas[$request->actividad_economica] ?? null;
                $descActividad = explode("-", $actividad);
                $descActividad = trim($descActividad[1] ?? null);
                return [
                    "tipoDocumento" => $docCode,
                    "numDocumento" => $numDoc,
                    "nrc" => $nrcDigits,
                    "nombre" => $request->nombre_customer,
                    "nombreComercial" => $request->nombre_comercial,
                    "codActividad" => $codActividad ?? $request->actividad_economica,
                    "descActividad" => $descAct ?? $descActividad,
                    "direccion" => [
                        "departamento" => $departamentoCode ?? "06", // default San Salvador
                        "municipio" => $municipioCode ?? "01", // default primer Municipio
                        "complemento" => $request->complemento
                    ],
                    "telefono" => $telefonoStr,
                    "correo" => $request->correo,
                    "codDomiciliado" => $request->cod_domiciliado,
                    "codPais" => $request->codigo_pais,
                ];
            default:
                return [];
        }
    }

    public function getProductData($product, $type, $documentos_relacionados = null)
    {
        if ($type !== "14") {
            $tributos = is_array($product["product"]) ? json_decode($product["product"]["tributos"], true) : json_decode($product["tributos"], true);
            $tributos = array_filter($tributos, function ($value) {
                return $value != "20";
            });
            $tributos = array_values($tributos);
        }

        if ($type === "11") {
            return [
                "cantidad" => $product["cantidad"],
                "codigo" => is_array($product["product"]) ? strval($product["product"]["codigo"]) : null,
                "uniMedida" => $product["unidad_medida"],
                "descripcion" => $product["descripcion"],
                "precioUni" => round($product["precio_sin_tributos"], 8),
                "montoDescu" => round($product["descuento"], 8),
                "ventaGravada" => round($product["ventas_gravadas"], 8),
                "tributos" => count($tributos) > 0 ? $tributos : null,
                "noGravado" => 0,
            ];
        } elseif ($type === "14") {
            // Para sujeto excluido no hay IVA; el campo 'compra' debe reflejar el valor de la línea (base) después de descuento.
            // En la construcción previa marcamos todo como exento, por lo que ventas_gravadas = 0 y ventas_exentas contiene la base.
            $cantidad = (float) ($product['cantidad'] ?? 0);
            $precioBase = (float) ($product['precio_sin_tributos'] ?? $product['precio'] ?? 0); // siempre sin IVA
            $descuento = (float) ($product['descuento'] ?? 0);
            $lineaBruta = $cantidad * $precioBase; // base total antes de descuento
            // Si tenemos ventas_exentas y es > 0 úsala; de lo contrario, derivar de linea bruta (caso formulario que no setea ventas_exentas explícitamente aún)
            $baseDeclarada = (isset($product['ventas_exentas']) && (float) $product['ventas_exentas'] > 0)
                ? (float) $product['ventas_exentas']
                : $lineaBruta;
            // Ajuste final de compra = base - descuento (no debe ser negativa)
            $compra = max(0, $baseDeclarada - $descuento);
            // Redondeos: precioUni a 8 decimales para consistencia API; compra y montoDescu también a 8 para evitar holgura
            return [
                'tipoItem' => $product['tipo_item'],
                'cantidad' => $cantidad,
                'codigo' => null,
                'uniMedida' => $product['unidad_medida'],
                'descripcion' => $product['descripcion'],
                'precioUni' => round($precioBase, 8),
                'montoDescu' => round($descuento, 8),
                'compra' => round($compra, 8),
            ];
        } else {

            $documentoRelacionado = isset($product["documento_relacionado"]) && $product["documento_relacionado"] !== null ? $product["documento_relacionado"] : null;
            if ($documentoRelacionado && $documentos_relacionados) {
                // Find in documentos_relacionados where numero_documento matches documentoRelacionado
                $relatedDoc = collect($documentos_relacionados)->firstWhere('numero_documento', $documentoRelacionado);
                if ($relatedDoc && $relatedDoc["tipo_documento"] == "07") {
                    $tributos = null;
                }
            }

            return [
                "tipoItem" => is_array($product["product"]) ? $product["product"]["tipoItem"] : $product["tipo_item"],
                "numeroDocumento" => $documentoRelacionado !== "" ? $documentoRelacionado : null,
                "cantidad" => $product["cantidad"],
                "codigo" => is_array($product["product"]) ? strval($product["product"]["codigo"]) : null,
                "codTributo" => null,
                "uniMedida" => $product["unidad_medida"],
                "descripcion" => $product["descripcion"],
                "precioUni" => round(in_array($type, ["03", "04", "05", "06"]) ? $product["precio_sin_tributos"] : $product["precio"], 8),
                "montoDescu" => round($product["descuento"], 8),
                "ventaNoSuj" => round($product["ventas_no_sujetas"], 8),
                "ventaExenta" => round($product["ventas_exentas"], 8),
                "ventaGravada" => round($product["ventas_gravadas"], 8),
                "tributos" => $tributos && count($tributos) > 0 ? $tributos : null,
                "psv" => round((float) $product["precio"], 8),
                "ivaItem" => round($product["iva"], 8),
                "noGravado" => 0,
            ];
        }
    }

    public function getCuerpoDocumentoComprobanteRetencion()
    {
        $documentos_relacionados = [];
        $num = 0;
        foreach ($this->dte["documentos_retencion"] as $documento) {
            $num++;
            $documentos_relacionados[] = [
                "numItem" => $num,
                "tipoDte" => $documento["tipo_generacion"],
                "tipoDoc" => intval($documento["tipo_documento"]),
                "numDocumento" => $documento["numero_documento"],
                "fechaEmision" => $documento["fecha_documento"],
                "montoSujetoGrav" => round((float) $documento["monto_sujeto_retencion"], 2),
                "codigoRetencionMH" => $documento["codigo_retencion"],
                "ivaRetenido" => round((float) $documento["iva_retenido"], 2),
                "descripcion" => $documento["descripcion_retencion"],
            ];
        }
        return $documentos_relacionados;
    }

    public function getCuerpoDocumentoComprobanteDonacion()
    {
        $donaciones = [];
        $num = 0;
        foreach ($this->dte["products"] as $donacion) {
            $num++;
            $donaciones[] = [
                "tipoDonacion" => $donacion["tipo_donacion"],
                "cantidad" => $donacion["cantidad"],
                "codigo" => null,
                "uniMedida" => $donacion["unidad_medida"],
                "descripcion" => $donacion["descripcion"],
                "depreciacion" => round($donacion["depreciacion"], 2),
                "valorUni" => round($donacion["valor_unitario"], 2),
                "valor" => round($donacion["valor_donado"], 2),
            ];
        }
        return $donaciones;
    }

    public function createCXC($data, $request)
    {
        try {
            DB::beginTransaction();
            CuentasCobrar::create([
                "numero_factura" => $data["codGeneracion"],
                "cliente" => $request->nombre_receptor,
                "monto" => $this->dte["total_pagar"],
                "saldo" => $this->dte["total_pagar"],
                "estado" => "pendiente",
                "fecha_vencimiento" => now()->addDays(30),
                "business_id" => session("business"),
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('business.documents.index')
                ->with('error', "Error")
                ->with(
                    "error_message",
                    "Ha ocurrido un error al crear la cuenta por cobrar."
                )->send();
        }
    }

    public function createDtePending($error, $status = "pending", $name = null)
    {
        try {
            DB::beginTransaction();
            DTE::create([
                "business_id" => session("business"),
                "content" => json_encode($this->dte),
                "type" => $this->dte["type"],
                "name" => $name,
                "status" => $status,
                "error_message" => $error
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('business.documents.index')
                ->with('error', "Error")
                ->with(
                    "error_message",
                    "Ha ocurrido un error al crear el borrador del documento."
                )->send();
        }
    }

    public function updateDtePending($id, $status = "pending", $error, $name = null)
    {
        try {
            DB::beginTransaction();
            DTE::where("id", $id)->update([
                "content" => json_encode($this->dte),
                "status" => $status,
                "name" => $name,
                "error_message" => $error
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('business.documents.index')
                ->with('error', "Error")
                ->with(
                    "error_message",
                    "Ha ocurrido un error al actualizar el borrador del documento."
                )->send();
        }
    }

    public function documentosRelacionados()
    {
        if (!empty($this->dte["documentos_relacionados"])) {
            return array_values(array_map(fn($documento) => [
                "tipoDocumento" => $documento["tipo_documento"],
                "tipoGeneracion" => intval($documento["tipo_generacion"]),
                "numeroDocumento" => $documento["numero_documento"],
                "fechaEmision" => $documento["fecha_documento"]
            ], $this->dte["documentos_relacionados"]));
        }
        return null;
    }

    public function otrosDocumentos()
    {
        if (empty($this->dte["otros_documentos"])) {
            return null;
        }

        return array_map(function ($documento) {
            $otros_documentos = [
                "codDocAsociado" => intval($documento["documento_asociado"]),
                "descDocumento" => $documento["descripcion_documento"] ?? null,
                "detalleDocumento" => $documento["identificacion_documento"] ?? null,
            ];

            if (isset($documento["medico"]) && !is_null($documento["medico"])) {
                $otros_documentos["medico"] = [
                    "nombre" => $documento["medico"]["nombre"] ?? null,
                    "nit" => isset($documento["medico"]["tipo_documento"]) && $documento["medico"]["tipo_documento"] === "1"
                        ? ($documento["medico"]["numero_documento"] ?? null)
                        : null,
                    "docIdentificacion" => isset($documento["medico"]["tipo_documento"]) && $documento["medico"]["tipo_documento"] !== "1"
                        ? ($documento["medico"]["numero_documento"] ?? null)
                        : null,
                    "tipoServicio" => isset($documento["medico"]["tipo_servicio"]) ? intval($documento["medico"]["tipo_servicio"]) : null,
                ];
            } elseif ($this->dte["type"] === "11") {
                $otros_documentos["placaTrans"] = $documento["placas"] ?? null;
                $otros_documentos["modoTransp"] = $documento["modo_transporte"] ?? null;
                $otros_documentos["numConductor"] = $documento["numero_identificacion"] ?? null;
                $otros_documentos["nombreConductor"] = $documento["nombre_conductor"] ?? null;
            }

            return $otros_documentos;
        }, $this->dte["otros_documentos"]);
    }


    public function pagos()
    {
        if (isset($this->dte["metodos_pago"])) {
            $metodos_pago = [];
            foreach ($this->dte["metodos_pago"] as $pago) {
                if ($this->dte["type"] !== "15") {
                    $metodos_pago[] = [
                        "codigo" => $pago["forma_pago"],
                        "montoPago" => round((float) $pago["monto"], 2),
                        "referencia" => $pago["numero_documento"],
                        "plazo" => $pago["plazo"] ?? null,
                        "periodo" => $pago["periodo"] ?? null,
                    ];
                } else {
                    $metodos_pago[] = [
                        "codigo" => $pago["forma_pago"],
                        "montoPago" => round((float) $pago["monto"], 2),
                        "referencia" => $pago["numero_documento"]
                    ];
                }
            }
            return $metodos_pago;
        } else {
            return null;
        }
    }

    public function extension($request)
    {
        if ($request->documento_emitir !== "" && $request->nombre_emitir !== "" && $request->documento_recibir !== "" && $request->nombre_recibir !== "") {
            $extension = [
                "docuEntrega" => $request->documento_emitir,
                "nombEntrega" => $request->nombre_emitir,
                "docuRecibe" => $request->documento_recibir,
                "nombRecibe" => $request->nombre_recibir
            ];
            $this->dte["extension"] = $extension;
        }

        if ($request->observaciones !== "") {
            $extension["observaciones"] = $request->observaciones;
            $this->dte["extension"]["observaciones"] = $request->observaciones;
        }

        session(["dte" => $this->dte]);
        return $extension ?? null;
    }

    public function ventaTerceros($request)
    {
        if ($request->nit_terceros !== "" && $request->nombre_terceros) {
            $dte["ventaTercero"] = [
                "nit" => $request->nit_terceros,
                "nombre" => $request->nombre_terceros
            ];
            $this->dte["venta_tercero"] = $dte["ventaTercero"];
            session(["dte" => $this->dte]);
            return $dte["ventaTercero"];
        }
    }

    public function getTributos()
    {
        $tributos_dte = [];

        if (isset($this->dte["turismo_por_alojamiento"]) && $this->dte["turismo_por_alojamiento"] > 0) {
            $tributos_dte[] = [
                "codigo" => "59",
                "descripcion" => Tributes::where("codigo", "59")->first()->descripcion,
                "valor" => round($this->dte["turismo_por_alojamiento"], 2)
            ];
        }

        if ($this->dte["turismo_salida_pais_via_aerea"] > 0) {
            $tributos_dte[] = [
                "codigo" => "71",
                "descripcion" => Tributes::where("codigo", "71")->first()->descripcion,
                "valor" => round($this->dte["turismo_salida_pais_via_aerea"], 2)
            ];
        }

        if ($this->dte["fovial"] > 0) {
            $tributos_dte[] = [
                "codigo" => "D1",
                "descripcion" => Tributes::where("codigo", "D1")->first()->descripcion,
                "valor" => round($this->dte["fovial"], 2)
            ];
        }

        if ($this->dte["contrans"] > 0) {
            $tributos_dte[] = [
                "codigo" => "C8",
                "descripcion" => Tributes::where("codigo", "C8")->first()->descripcion,
                "valor" => round($this->dte["contrans"], 2)
            ];
        }

        if ($this->dte["bebidas_alcoholicas"] > 0) {
            $tributos_dte[] = [
                "codigo" => "C5",
                "descripcion" => Tributes::where("codigo", "C5")->first()->descripcion,
                "valor" => round($this->dte["bebidas_alcoholicas"], 2)
            ];
        }

        if ($this->dte["tabaco_cigarillos"] > 0) {
            $tributos_dte[] = [
                "codigo" => "C6",
                "descripcion" => Tributes::where("codigo", "C6")->first()->descripcion,
                "valor" => round($this->dte["tabaco_cigarillos"], 2)
            ];
        }

        if ($this->dte["tabaco_cigarros"] > 0) {
            $tributos_dte[] = [
                "codigo" => "C7",
                "descripcion" => Tributes::where("codigo", "C7")->first()->descripcion,
                "valor" => round($this->dte["tabaco_cigarros"], 2)
            ];
        }

        return $tributos_dte != [] ? $tributos_dte : null;
    }

    /**
     * Actualiza stocks de productos usando el sistema de inventario por sucursales
     * 
     * @param string $codGeneracion Código del DTE
     * @param array $productsDTE Lista de productos del DTE
     * @param int $business_id ID del negocio
     * @param string $tipo 'salida' o 'entrada'
     * @param int|null $sucursalId ID de la sucursal (si no se provee, busca la primera)
     * @param string|null $tipoDte Tipo de DTE para manejos especiales (ej: '14' para sujeto excluido)
     */
    public function updateStocks($codGeneracion, $productsDTE, $business_id, $tipo = "salida", $sucursalId = null, $tipoDte = null, $posId = null)
    {
        Log::info("=== INICIO updateStocks ===", [
            'codGeneracion' => $codGeneracion,
            'tipo' => $tipo,
            'tipoDte' => $tipoDte,
            'sucursalId' => $sucursalId,
            'posId' => $posId,
            'productos_count' => count($productsDTE)
        ]);

        // Determinar si debemos actualizar stock en POS independiente
        $usePosInventory = false;
        $pos = null;

        if ($posId) {
            $pos = PuntoVenta::find($posId);
            if ($pos && $pos->has_independent_inventory && $pos->sucursal->business->pos_inventory_enabled) {
                $usePosInventory = true;
                Log::info("Usando inventario de POS independiente", ['pos_id' => $posId, 'pos_nombre' => $pos->nombre]);
            }
        }

        // Si no usamos POS, necesitamos sucursal
        if (!$usePosInventory) {
            // Si no se provee sucursal, usar la primera del negocio
            if (!$sucursalId) {
                $sucursal = Sucursal::where('business_id', $business_id)->first();
                $sucursalId = $sucursal?->id;
            }

            if (!$sucursalId) {
                Log::warning("No se encontró sucursal para actualizar stocks. Business ID: {$business_id}");
                return;
            }
            Log::info("Usando inventario de sucursal", ['sucursal_id' => $sucursalId]);
        }

        foreach ($productsDTE as $product) {
            $searchProduct = null;
            $cantidad = 0;
            $precioUnitario = null;
            $priceVariantId = null;
            $priceVariantName = null;

            if (is_array($product)) {
                // Caso 1: Producto viene del formato interno (al generar DTE)
                if (isset($product["product"]) && is_array($product["product"])) {
                    $searchProduct = BusinessProduct::find($product["product"]["id"]);
                    $cantidad = $product["cantidad"];
                }
                $precioUnitario = $product['precio_unitario'] ?? $product['precio'] ?? null;
                $priceVariantId = $product['price_variant_id'] ?? null;
                $priceVariantName = $product['price_variant_name'] ?? null;
            } else {
                // Caso 2: Producto viene del JSON de Hacienda (al anular)
                // Buscar por business_id + código + descripción para mayor precisión
                $codigo = $product->codigo ?? null;
                $descripcion = $product->descripcion ?? null;

                if ($codigo && $descripcion) {
                    // Búsqueda exacta: mismo negocio, mismo código y misma descripción
                    $searchProduct = BusinessProduct::where('business_id', $business_id)
                        ->where('codigo', $codigo)
                        ->where('descripcion', $descripcion)
                        ->first();
                } elseif ($codigo) {
                    // Fallback: solo por código si no hay descripción
                    $searchProduct = BusinessProduct::where('business_id', $business_id)
                        ->where('codigo', $codigo)
                        ->first();
                } elseif ($descripcion) {
                    // Último recurso: solo por descripción
                    $searchProduct = BusinessProduct::where('business_id', $business_id)
                        ->where('descripcion', $descripcion)
                        ->first();
                }

                $cantidad = $product->cantidad ?? 0;
            }

            if ($searchProduct && $cantidad > 0) {
                Log::info("Procesando producto", [
                    'producto_id' => $searchProduct->id,
                    'codigo' => $searchProduct->codigo,
                    'descripcion' => $searchProduct->descripcion,
                    'cantidad' => $cantidad,
                    'tipo_movimiento' => $tipo
                ]);

                // Para facturas de sujeto excluido (compras), siempre incrementar inventario
                if ($tipoDte === "14") {
                    $descripcion = "Compra de producto (Factura Sujeto Excluido)";
                    try {
                        if ($usePosInventory) {
                            $searchProduct->increaseStockInPos($posId, (float) $cantidad, $codGeneracion, $descripcion, $precioUnitario, $priceVariantId, $priceVariantName);
                            Log::info("Stock incrementado en POS", ['pos_id' => $posId]);
                        } else {
                            $searchProduct->increaseStockInBranch($sucursalId, (float) $cantidad, $codGeneracion, $descripcion, $precioUnitario, $priceVariantId, $priceVariantName);
                            Log::info("Stock incrementado en sucursal", ['sucursal_id' => $sucursalId]);
                        }
                    } catch (\Exception $e) {
                        $location = $usePosInventory ? "punto de venta {$posId}" : "sucursal {$sucursalId}";
                        Log::error("Error incrementando stock para producto {$searchProduct->id} en {$location}: " . $e->getMessage());
                        continue;
                    }
                } else {
                    // Lógica normal para otros tipos de DTE
                    $descripcion = $tipo === "salida" ? "Venta de producto" : "Anulación de documento";
                    try {
                        if ($tipo === "salida") {
                            if ($usePosInventory) {
                                $searchProduct->reduceStockInPos($posId, (float) $cantidad, $codGeneracion, $descripcion, $precioUnitario, $priceVariantId, $priceVariantName);
                                Log::info("Stock reducido en POS", ['pos_id' => $posId]);
                            } else {
                                $searchProduct->reduceStockInBranch($sucursalId, (float) $cantidad, $codGeneracion, $descripcion, $precioUnitario, $priceVariantId, $priceVariantName);
                                Log::info("Stock reducido en sucursal", ['sucursal_id' => $sucursalId]);
                            }
                        } elseif ($tipo === "entrada") {
                            if ($usePosInventory) {
                                $searchProduct->increaseStockInPos($posId, (float) $cantidad, $codGeneracion, $descripcion, $precioUnitario, $priceVariantId, $priceVariantName);
                                Log::info("Stock devuelto a POS (anulación)", ['pos_id' => $posId]);
                            } else {
                                $searchProduct->increaseStockInBranch($sucursalId, (float) $cantidad, $codGeneracion, $descripcion, $precioUnitario, $priceVariantId, $priceVariantName);
                                Log::info("Stock devuelto a sucursal (anulación)", ['sucursal_id' => $sucursalId]);
                            }
                        }
                    } catch (\Exception $e) {
                        $location = $usePosInventory ? "punto de venta {$posId}" : "sucursal {$sucursalId}";
                        Log::error("Error actualizando stock para producto {$searchProduct->id} en {$location}: " . $e->getMessage());
                        continue;
                    }
                }
            } else {
                if (!$searchProduct) {
                    $codigo = is_object($product) ? ($product->codigo ?? 'N/A') : ($product['product']['codigo'] ?? 'N/A');
                    $descripcion = is_object($product) ? ($product->descripcion ?? 'N/A') : ($product['descripcion'] ?? 'N/A');
                    Log::warning("Producto no encontrado en BD", [
                        'business_id' => $business_id,
                        'codigo' => $codigo,
                        'descripcion' => $descripcion,
                        'criterio' => 'business_id + codigo + descripcion'
                    ]);
                } elseif ($cantidad <= 0) {
                    Log::warning("Cantidad inválida para producto", ['producto_id' => $searchProduct->id, 'cantidad' => $cantidad]);
                }
            }
        }

        Log::info("=== FIN updateStocks ===");
    }

    public function getMunicipios($departamento)
    {
        $municipios = $this->octopus_service->getCatalog("CAT-012", $departamento);
        return $municipios;
    }

    public function delete(string $id)
    {
        try {
            DB::beginTransaction();
            DTE::where("id", $id)->delete();
            DB::commit();
            return redirect()->route("business.index")
                ->with("success", "Exito")
                ->with("success_message", "Documento eliminado correctamente");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route("business.index")
                ->with("error", "Error")
                ->with("error_message", "Ha ocurrido un error al eliminar el documento");
        }
    }

    public function delete_all()
    {
        try {
            $business_id = Session::get('business') ?? null;
            if ($business_id) {
                DB::beginTransaction();
                DTE::where("business_id", $business_id)->delete();
                DB::commit();
                return redirect()->route("business.index")
                    ->with("success", "Exito")
                    ->with("success_message", "Documentos eliminados correctamente");
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route("business.index")
                ->with("error", "Error")
                ->with("error_message", "Ha ocurrido un error al eliminar los documentos");
        }
    }

    public function anular(Request $request)
    {
        try {
            $codGeneracion = $request->codGeneracion;
            $motivo = $request->input("motivo");
            $business_id = Session::get('business') ?? null;

            $dte = Http::get(env("OCTOPUS_API_URL") . "/dtes/" . $codGeneracion)->json();
            $documento = json_decode($dte["documento"]);
            $business = Business::find($business_id);

            $response = Http::post(env("OCTOPUS_API_URL") . '/anulacion/', [
                "nit" => $business->nit,
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
                    "numDocResponsable" => $business->nit,
                    "nombreSolicita" => auth()->user()->name,
                    "tipoDocSolicita" => "36",
                    "numDocSolicita" => $business->nit,
                ]
            ]);
            $data = $response->json();
            if ($response->status() == 201) {
                if (!in_array($dte["tipo_dte"], ["04", "07", "14"])) {
                    $products_dte = $documento->cuerpoDocumento;

                    // Para anulación, extraer sucursal y POS del documento original
                    // Probar diferentes variantes del campo de sucursal en el JSON
                    $sucursalCode = $documento->emisor->codEstable
                        ?? $documento->emisor->codEstablecimiento
                        ?? $documento->emisor->codEstableMH
                        ?? $dte["codSucursal"] // Del JSON externo
                        ?? null;

                    $sucursalId = null;
                    $posId = null;

                    Log::info("Extrayendo datos para anulación", [
                        'sucursalCode' => $sucursalCode,
                        'posCode_emisor' => $documento->emisor->codPuntoVenta ?? null,
                        'posCode_externo' => $dte["codPuntoVenta"] ?? null
                    ]);

                    if ($sucursalCode) {
                        $sucursal = Sucursal::where('business_id', $business_id)
                            ->where('codSucursal', $sucursalCode)
                            ->first();
                        $sucursalId = $sucursal?->id;
                    }

                    // Intentar obtener el punto de venta del documento original
                    // Probar diferentes variantes del campo de POS
                    $posCode = $documento->emisor->codPuntoVenta
                        ?? $documento->emisor->codPuntoVentaMH
                        ?? $dte["codPuntoVenta"] // Del JSON externo
                        ?? null;

                    if ($posCode && $sucursalId) {
                        // El campo en la tabla se llama 'codPuntoVenta', no 'codigo'
                        $pos = PuntoVenta::where('sucursal_id', $sucursalId)
                            ->where('codPuntoVenta', $posCode)
                            ->first();
                        $posId = $pos?->id;

                        if ($pos) {
                            Log::info("Punto de venta encontrado para anulación", [
                                'posCode' => $posCode,
                                'posId' => $posId,
                                'pos_nombre' => $pos->nombre,
                                'has_independent_inventory' => $pos->has_independent_inventory
                            ]);
                        } else {
                            Log::warning("Punto de venta NO encontrado en BD", [
                                'posCode' => $posCode,
                                'sucursalId' => $sucursalId,
                                'query' => "SELECT * FROM punto_ventas WHERE sucursal_id = {$sucursalId} AND codPuntoVenta = '{$posCode}'"
                            ]);
                        }
                    } else {
                        Log::warning("No se pudo determinar el punto de venta para anulación", [
                            'posCode' => $posCode,
                            'sucursalId' => $sucursalId,
                            'motivo' => !$posCode ? 'posCode es null' : 'sucursalId es null'
                        ]);
                    }

                    // Determinar el tipo de movimiento según el documento anulado
                    // Si se anula una Nota de Crédito (tipo 05), el inventario debe salir nuevamente (revertir la devolución)
                    // Si se anula una venta (tipos 01, 03, 11), el inventario debe regresar (entrada)
                    $tipoMovimiento = ($dte["tipo_dte"] === "05") ? "salida" : "entrada";

                    $this->updateStocks($codGeneracion, $products_dte, $business_id, $tipoMovimiento, $sucursalId, $dte["tipo_dte"], $posId);
                }
                return redirect()->route('business.documents.index')
                    ->with('success', "Documento anulado correctamente")
                    ->with("success_message", $data["descripcionMsg"]);
            } else {
                return redirect()->route('business.documents.index')
                    ->with('error', "Error al anular el documento")
                    ->with("error_message", $data["detail"]["descripcionMsg"]);
            }
        } catch (\Exception $e) {
            return redirect()->route('business.documents.index')
                ->with([
                    'error' => "Error",
                    'error_message' => "Ha ocurrido un error al anular el documento: " . $e->getMessage() . " " . $e->getLine()
                ]);
        }
    }

    /**
     * Importa un Excel de clientes y devuelve la lista normalizada.
     */
    public function importCustomersExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $import = new CustomerListImport();
            Excel::import($import, $request->file('file'));
            $items = $import->getItems();

            return response()->json([
                'success' => true,
                'count' => count($items),
                'items' => $items,
            ]);
        } catch (\Throwable $e) {
            Log::error("Excel import error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'No se pudo procesar el archivo. Verifique el formato de columnas.',
            ], 422);
        }
    }

    /**
     * Recibe un JSON de plantilla (estructura de dte_template_example.json),
     * lo carga en session('dte') y envía el DTE al servicio manteniendo la lógica.
     * Retorna el JSON del resultado sin redirecciones.
     */
    public function submitFromJson(Request $request)
    {
        $request->validate([
            'dte' => 'required|array',
        ]);

        $payload = $request->input('dte');

        // 1) Cargar JSON en sesión
        $this->dte = $payload;
        // Asegurar claves mínimas
        $this->dte['type'] = $payload['type'] ?? $payload['tipo'] ?? ($this->dte['type'] ?? null);
        if (!isset($this->dte['products'])) {
            $this->dte['products'] = [];
        }
        if (!isset($this->dte['customer'])) {
            $this->dte['customer'] = [];
        }
        // Garantizar llaves de tributos / descuentos / retenciones
        $this->ensureDteDefaults();
        session(['dte' => $this->dte]);

        // 2) Endpoint por tipo
        $type = $this->dte['type'];
        $endpointByType = [
            '01' => '/factura/',
            '03' => '/credito_fiscal/',
            '04' => '/nota_remision/',
            '05' => '/nota_credito/',
            '06' => '/nota_debito/',
            '07' => '/comprobante_retencion/',
            '11' => '/factura_exportacion/',
            '14' => '/sujeto_excluido/',
            '15' => '/comprobante_donacion/',
        ];
        if (!$type || !isset($endpointByType[$type])) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de DTE no soportado o ausente.'
            ], 422);
        }

        // 3) POS por defecto
        $businessId = session('business');
        $business_user = null;
        if (auth()->check()) {
            $business_user = BusinessUser::where('business_id', $businessId)
                ->where('user_id', auth()->id())
                ->first();
        }
        $pos_id = $business_user?->default_pos_id;
        if (!$pos_id) {
            $pos_id = $this->resolveDefaultPosId($businessId);
        }
        if (!$pos_id) {
            return response()->json(['success' => false, 'message' => 'No hay Punto de Venta configurado.'], 422);
        }

        // 4) Construir Request mínimo desde JSON
        $c = $this->dte['customer'] ?? [];
        $req = new Request();
        $req->merge([
            'pos_id' => $pos_id,
            'tipo_documento' => $c['tipoDocumento'] ?? null,
            'numero_documento' => $c['numDocumento'] ?? null,
            'nrc_customer' => $c['nrc'] ?? null,
            'nombre_customer' => $c['nombre'] ?? null,
            'nombre_receptor' => $c['nombre'] ?? null,
            'nombre_comercial' => $c['nombreComercial'] ?? null,
            'actividad_economica' => $c['codActividad'] ?? null,
            'departamento' => $c['departamento'] ?? null,
            'municipio' => $c['municipio'] ?? null,
            'complemento' => $c['complemento'] ?? null,
            'telefono' => $c['telefono'] ?? null,
            'correo' => $c['correo'] ?? null,
            'tipo_persona' => $c['tipoPersona'] ?? null,
            'codigo_pais' => $c['pais'] ?? null,
            'bienTitulo' => $this->dte['bienTitulo'] ?? null,
            'condicion_operacion' => $this->dte['condicion_operacion'] ?? ($this->dte['resumen']['condicionOperacion'] ?? '1'),
            'action' => 'send',
            'json_mode' => true,
        ]);

        // Exportación: parámetros del emisor si vinieran en JSON
        if ($type === '11') {
            $req->merge([
                'regimen_exportacion' => Arr::get($this->dte, 'emisor.regimen'),
                'recinto_fiscal' => Arr::get($this->dte, 'emisor.recintoFiscal'),
                'tipo_item_exportar' => Arr::get($this->dte, 'emisor.tipoItemExpor'),
                'incoterms' => Arr::get($this->dte, 'resumen.codIncoterms') ?? Arr::get($this->dte, 'incoterms'),
            ]);
        }

        try {
            $data = $this->processDTE($req, $type, $endpointByType[$type]);
            // Si la validación interna retornó un RedirectResponse (errores), traducir a JSON 422
            if ($data instanceof \Illuminate\Http\RedirectResponse) {
                $msg = session('error_message') ?? 'Error de validación';
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            // En algunos flujos internos se usa ->send() en el RedirectResponse, lo cual produce null aquí.
            if ($data === null && (session()->has('error') || session()->has('error_message'))) {
                $msg = session('error_message') ?? 'Error de validación';
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return response()->json($data);
        } catch (\Throwable $e) {
            Log::error('submitFromJson error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el DTE desde JSON.'
            ], 500);
        }
    }

    /**
     * Inicializa todas las llaves esperadas en $this->dte para evitar warnings/errores
     * cuando se consumen estructuras construidas externamente (por ejemplo importación masiva JSON/Excel).
     */
    private function ensureDteDefaults(): void
    {
        if (!is_array($this->dte)) {
            $this->dte = [];
        }

        // Listado de llaves numéricas usadas en cálculos / getTributos()
        $numericKeys = [
            'turismo_por_alojamiento',
            'turismo_salida_pais_via_aerea',
            'fovial',
            'contrans',
            'bebidas_alcoholicas',
            'tabaco_cigarillos',
            'tabaco_cigarros',
            'total_ventas_gravadas',
            'total_ventas_exentas',
            'total_ventas_no_sujetas',
            'descuento_venta_gravada',
            'descuento_venta_exenta',
            'descuento_venta_no_sujeta',
            'total_descuentos',
            'iva',
            'total_taxes',
            'subtotal',
            'total',
            'total_pagar',
            'monto_abonado',
            'monto_pendiente',
            'total_iva_retenido',
            'isr',
            'flete',
            'seguro'
        ];
        foreach ($numericKeys as $k) {
            if (!array_key_exists($k, $this->dte) || $this->dte[$k] === null || $this->dte[$k] === '') {
                $this->dte[$k] = 0;
            }
        }

        // Flags / switches
        $flagKeys = [
            'retener_iva',
            'retener_renta',
            'percibir_iva',
            'remove_discounts'
        ];
        foreach ($flagKeys as $k) {
            if (!array_key_exists($k, $this->dte)) {
                $this->dte[$k] = null; // mantener null para distinguir de 'active'
            }
        }

        // Arreglos estructurales
        $arrayKeys = [
            'products',
            'metodos_pago',
            'documentos_relacionados',
            'otros_documentos'
        ];
        foreach ($arrayKeys as $k) {
            if (!isset($this->dte[$k]) || !is_array($this->dte[$k])) {
                $this->dte[$k] = [];
            }
        }

        // Cliente base (evitar accesos indefinidos en buildDTE)
        if (!isset($this->dte['customer']) || !is_array($this->dte['customer'])) {
            $this->dte['customer'] = [];
        }
        // Asegurar subclaves mínimas del customer usadas en la vista
        $customerDefaultKeys = [
            'tipoDocumento',
            'numDocumento',
            'nrc',
            'nombre',
            'nombreComercial',
            'codActividad',
            'descActividad',
            'departamento',
            'municipio',
            'complemento',
            'telefono',
            'correo',
            'tipoPersona',
        ];
        foreach ($customerDefaultKeys as $ck) {
            if (!array_key_exists($ck, $this->dte['customer'])) {
                $this->dte['customer'][$ck] = null;
            }
        }

        // Claves de meta del DTE usadas en vistas parciales
        if (!array_key_exists('status', $this->dte)) {
            $this->dte['status'] = null;
        }
        if (!array_key_exists('name', $this->dte)) {
            $this->dte['name'] = null;
        }
    }

    private function resolveDefaultPosId(?int $businessId): ?int
    {
        if (!$businessId) {
            return null;
        }

        return PuntoVenta::whereHas('sucursal', function ($query) use ($businessId) {
            $query->where('business_id', $businessId);
        })
            ->orderBy('id')
            ->value('id');
    }

    /**
     * Importa Excel que contiene en cada fila datos de cliente + un producto.
     * Agrupa por cliente generando estructuras de DTE listas para ser enviadas luego
     * mediante submitFromJson. Soporta tipos 01 (Factura Consumidor Final), 03 (Crédito Fiscal) y 14 (Factura Sujeto Excluido).
     * Request params:
     *  - file: archivo excel
     *  - dte_type: '01' | '03'
     */
    public function importCustomersProductsExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:20480',
            'dte_type' => 'required|in:01,03,14',
            // modo de agrupación: group (por cliente) | row (cada fila un DTE)
            'group_mode' => 'sometimes|in:group,row'
        ]);

        $type = $request->input('dte_type');
        $groupMode = $request->input('group_mode', 'group'); // default comportamiento anterior
        $business_id = Session::get('business');
        if (!$business_id) {
            return response()->json(['success' => false, 'message' => 'Sesión de empresa no encontrada'], 401);
        }

        try {
            $import = new \App\Imports\CustomerProductsImport($type);
            Excel::import($import, $request->file('file'));
            $rows = $import->getRows();
            $discarded = $import->getDiscarded();

            if (empty($rows)) {
                return response()->json(['success' => false, 'message' => 'El archivo no contiene filas válidas'], 422);
            }

            // Catálogo de unidades para mapear texto -> código índice
            $unidades = $this->unidades_medidas ?? [];
            $mapUnidad = function ($txt) use ($unidades) {
                if ($txt === null)
                    return '59'; // default
                $lower = mb_strtolower(trim((string) $txt));
                $idx = array_search($lower, array_map(fn($v) => mb_strtolower($v), $unidades), true);
                return $idx === false ? '59' : (string) $idx;
            };

            // Mapeo tipo item texto
            $mapTipoItem = function ($txt) {
                $t = mb_strtolower(trim((string) ($txt ?? '')));
                return match ($t) {
                    'bienes' => 1,
                    'servicios' => 2,
                    'ambos (bienes y servicios)', 'ambos' => 3,
                    default => 1,
                };
            };

            // Mapeo tipo venta -> claves internas (Gravada, Exenta, No sujeta) (ignorado para tipo 14: se tratará como Exenta)
            $mapTipoVenta = function ($txt) {
                $t = mb_strtolower(trim((string) ($txt ?? '')));
                return match ($t) {
                    'gravada', 'gravado' => 'Gravada',
                    'exenta', 'exento' => 'Exenta',
                    'no sujeta', 'nosujeta', 'no_sujeta' => 'No sujeta',
                    default => 'Gravada',
                };
            };

            // Colección de grupos (cada grupo = un DTE a generar). En modo 'group' agrupamos por receptor,
            // en modo 'row' cada línea constituye su propio grupo con un único producto.
            $groups = [];
            foreach ($rows as $idx => $r) {
                $doc = $this->onlyDigits($r['numDocumento'] ?? null) ?? '';
                $docType = $this->normalizeDocType($r['tipoDocumento'] ?? null);
                $nombre = trim($r['nombre'] ?? $r['nombreComercial'] ?? '');
                // Clave de agrupación según modo
                $groupKey = $groupMode === 'group'
                    ? (($docType ?: 'XX') . '|' . ($doc ?: $nombre))
                    : ('ROW_' . $idx); // fuerza cada fila a ser independiente

                $cantidad = (float) ($r['cantidad'] ?? 0);
                $precioSinIVA = (float) ($r['precio_unitario_sin_iva'] ?? 0);
                if ($cantidad <= 0 || $precioSinIVA <= 0) {
                    continue;
                }

                $tipoVenta = $mapTipoVenta($r['tipo_venta_txt'] ?? null);
                if ($type === '14') { // sujeto excluido sin IVA
                    $tipoVenta = 'Exenta';
                }
                $tipoItem = $mapTipoItem($r['tipo_item_txt'] ?? null);
                $uniMedida = $mapUnidad($r['unidad_medida_txt'] ?? null);

                $totalBase = $cantidad * $precioSinIVA;
                $precioConIVA = ($tipoVenta === 'Gravada')
                    ? $precioSinIVA * ($type === '03' ? 1 : 1.13)
                    : $precioSinIVA;
                $iva = 0;
                if ($tipoVenta === 'Gravada' && $type !== '14') {
                    $iva = $type === '03' ? $totalBase * 0.13 : ($totalBase * 0.13);
                }
                $ventaGravada = ($tipoVenta === 'Gravada' && $type !== '14') ? ($type === '03' ? $totalBase : ($totalBase * 1.13)) : 0;
                $ventaExenta = ($tipoVenta === 'Exenta' || $type === '14') ? $totalBase : 0;
                $ventaNoSuj = ($tipoVenta === 'No sujeta') ? $totalBase : 0;

                // Retención de renta por línea (solo tipo 14 y servicios/ambos)
                $retencionLinea = 0;
                if ($type === '14' && in_array($tipoItem, [2, 3], true)) {
                    $retencionLinea = round($totalBase * 0.10, 8);
                }

                $productArray = [
                    'id' => rand(1, 100000),
                    'product' => null,
                    'product_id' => null,
                    'codigo' => null,
                    'unidad_medida' => $uniMedida,
                    'descripcion' => $r['descripcion'] ?? 'Producto',
                    'cantidad' => $cantidad,
                    'tipo' => $tipoVenta,
                    'precio' => $tipoVenta === 'Gravada' && $type !== '14' ? ($type === '03' ? $precioSinIVA : $precioConIVA) : $precioSinIVA,
                    'precio_sin_tributos' => $precioSinIVA,
                    'descuento' => 0,
                    'ventas_gravadas' => $ventaGravada,
                    'ventas_exentas' => $ventaExenta,
                    'ventas_no_sujetas' => $ventaNoSuj,
                    'total' => $ventaGravada + $ventaExenta + $ventaNoSuj,
                    'iva' => $iva,
                    'tipo_item' => $tipoItem,
                    'tributos' => json_encode(['20']), // por defecto
                    'retencion_renta' => $retencionLinea,
                ];

                if (!isset($groups[$groupKey])) {
                    $groups[$groupKey] = [
                        'type' => $type,
                        'customer' => [
                            'tipoDocumento' => $docType,
                            'numDocumento' => $this->normalizeNumeroDocumento($doc, $docType, $type),
                            'nrc' => $this->onlyDigits($r['nrc'] ?? null),
                            'nombre' => $nombre,
                            'nombreComercial' => $r['nombreComercial'] ?? null,
                            'codActividad' => $r['codActividad'] ?? null,
                            'departamento' => $r['departamento'] ?? null,
                            'municipio' => $r['municipio'] ?? null,
                            'complemento' => $r['complemento'] ?? null,
                            'telefono' => $r['telefono'] ?? null,
                            'correo' => $r['correo'] ?? null,
                            'pais' => $r['pais'] ?? null,
                            'tipoPersona' => $r['tipoPersona'] ?? null,
                        ],
                        'products' => [],
                        'condicion_operacion' => 1,
                    ];
                }

                $groups[$groupKey]['products'][] = $productArray;
            }

            // Totales por cada DTE (simplificados reutilizando lógica parcial de totals()).
            $result = [];
            foreach ($groups as $g) {
                $grav = array_sum(array_map(fn($p) => $p['ventas_gravadas'], $g['products']));
                $exe = array_sum(array_map(fn($p) => $p['ventas_exentas'], $g['products']));
                $nos = array_sum(array_map(fn($p) => $p['ventas_no_sujetas'], $g['products']));
                $g['total_ventas_gravadas'] = round($grav, 8);
                $g['total_ventas_exentas'] = round($exe, 8);
                $g['total_ventas_no_sujetas'] = round($nos, 8);
                // Para CCF (03) el subtotal base es sin IVA (grav representa base), IVA separado
                $iva_calculado = 0;
                if ($g['type'] === '03') {
                    // Reconstruir IVA a partir de productos gravados base
                    $baseGravada = array_sum(array_map(fn($p) => $p['tipo'] === 'Gravada' ? ($p['cantidad'] * $p['precio_sin_tributos']) : 0, $g['products']));
                    $iva_calculado = round($baseGravada * 0.13, 8);
                    $g['subtotal'] = $baseGravada + $exe + $nos; // base sin IVA
                } else if ($g['type'] === '14') {
                    // En sujeto excluido, subtotal es la suma de las bases (sin IVA, todo tratado como exento/servicio)
                    $g['subtotal'] = $grav + $exe + $nos; // grav debería ser 0 siempre aquí
                } else {
                    $g['subtotal'] = $grav + $exe + $nos; // ya incluye IVA en gravadas (flujo consumidor final)
                }
                // Inicializaciones alineadas con total_init()
                $g['descuento_venta_gravada'] = 0;
                $g['descuento_venta_exenta'] = 0;
                $g['descuento_venta_no_sujeta'] = 0;
                $g['percentaje_descuento_venta_gravada'] = 0;
                $g['percentaje_descuento_venta_exenta'] = 0;
                $g['percentaje_descuento_venta_no_sujeta'] = 0;
                $g['retener_iva'] = 'inactive';
                $g['retener_renta'] = 'inactive';
                $g['percibir_iva'] = 'inactive';
                $g['monto_abonado'] = 0;
                $g['flete'] = 0;
                $g['seguro'] = 0;
                $g['total_descuentos'] = 0;
                // Para CCF exponer IVA separado; para CF queda embebido
                $g['total_taxes'] = $g['type'] === '03' ? $iva_calculado : 0; // tipo 14 sin IVA
                $g['total_iva_retenido'] = 0;
                $g['isr'] = 0;
                if ($g['type'] !== '11' && $g['type'] !== '14' && $g['type'] !== '07' && $g['type'] !== '01') {
                    $g['total'] = $g['subtotal'] + $g['total_taxes'];
                } else {
                    $g['total'] = $g['subtotal'];
                }
                // En CCF el total a pagar es subtotal + IVA
                if ($g['type'] === '03') {
                    $g['total_pagar'] = round(($g['subtotal'] + $iva_calculado) - $g['total_descuentos'], 8);
                } else {
                    $g['total_pagar'] = round($g['total'] - $g['total_descuentos'], 8);
                }
                // Retención 10% sólo en tipo 14 para líneas servicio o ambos (aplica igual en modo row)
                if ($g['type'] === '14') {
                    $retencionBase = 0;
                    foreach ($g['products'] as $p) {
                        if (isset($p['tipo_item']) && in_array((int) $p['tipo_item'], [2, 3], true)) {
                            $retencionBase += (float) ($p['total'] ?? 0);
                        }
                    }
                    if ($retencionBase > 0) {
                        $isr = round($retencionBase * 0.10, 8);
                        $g['retener_renta'] = 'active';
                        $g['isr'] = $isr;
                        $g['total_pagar'] = max(0, round($g['total_pagar'] - $isr, 8));
                        $g['monto_abonado'] = $g['total_pagar'];
                    }
                }
                // Método de pago por defecto (forma 99) cubre el total
                $g['metodos_pago'] = [
                    [
                        'id' => rand(1, 100000),
                        'forma_pago' => '99', // Otros
                        'monto' => (string) $g['total_pagar'], // debe reflejar total con IVA si CCF
                        'numero_documento' => '0',
                        'plazo' => null,
                        'periodo' => null,
                    ]
                ];
                $g['monto_abonado'] = $g['total_pagar'];
                $g['monto_pendiente'] = 0;

                // Etiqueta documento legible (sólo para vista previa)
                $docMap = ['36' => 'NIT', '13' => 'DUI', '02' => 'Pasaporte', '03' => 'Carnet Residente', '37' => 'Otros'];
                $docCode = $g['customer']['tipoDocumento'] ?? '';
                $g['customer']['tipoDocumentoLabel'] = $docMap[$docCode] ?? $docCode;

                // Totales de preview uniformes según tipo
                if ($g['type'] === '03') { // Crédito Fiscal: base + IVA + total
                    $g['preview_totals'] = [
                        'base' => round($g['subtotal'], 2),
                        'iva' => round($iva_calculado, 2),
                        'renta' => 0.00,
                        'total' => round($g['total_pagar'], 2)
                    ];
                } elseif ($g['type'] === '14') { // Sujeto Excluido: base, renta (retención) y total neto
                    $base14 = round($g['subtotal'], 2);
                    $renta14 = round($g['isr'] ?? 0, 2);
                    $total14 = round($g['total_pagar'], 2); // ya viene con renta descontada
                    $g['preview_totals'] = [
                        'base' => $base14,
                        'iva' => 0.00,
                        'renta' => $renta14,
                        'total' => $total14,
                    ];
                }

                // Resumen compacto de items para modal/desplegable (incluye desglose por tipo de venta e IVA)
                $g['items_preview'] = array_map(function ($p) {
                    $cantidad = (float) ($p['cantidad'] ?? 0);
                    $iva = (float) ($p['iva'] ?? 0);
                    $total = (float) ($p['total'] ?? 0);
                    // Si existe precio_sin_tributos úsalo como base; sino calcula base a partir del total - iva
                    $baseTotal = $p['ventas_gravadas'] ?? ($p['tipo'] === 'Gravada' ? ($total - $iva) : ($total - $iva));
                    $precioUnitarioBase = $p['precio_sin_tributos'] ?? ($cantidad > 0 ? $baseTotal / $cantidad : ($p['precio'] ?? 0));
                    $subtotal = $cantidad * $precioUnitarioBase;
                    $retencionLinea = (float) ($p['retencion_renta'] ?? 0);
                    return [
                        'descripcion' => $p['descripcion'] ?? '',
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precioUnitarioBase, // sin IVA
                        'tipo' => $p['tipo'] ?? null,
                        'iva' => $iva,
                        'gravada' => $p['ventas_gravadas'] ?? ($p['tipo'] === 'Gravada' ? ($total - $iva) : 0),
                        'exenta' => $p['ventas_exentas'] ?? ($p['tipo'] === 'Exenta' ? $total : 0),
                        'no_suj' => $p['ventas_no_sujetas'] ?? ($p['tipo'] === 'No sujeta' ? $total : 0),
                        'total' => $total,
                        'tipo_item' => $p['tipo_item'] ?? null,
                        'retencion_renta' => $retencionLinea,
                        'subtotal' => $subtotal,
                        'total_neto' => max(0, $subtotal - $retencionLinea),
                    ];
                }, $g['products']);
                $result[] = $g;
            }

            return response()->json([
                'success' => true,
                'count' => count($result),
                'items' => $result,
                'discarded' => $discarded,
                'group_mode' => $groupMode,
            ]);
        } catch (\Throwable $e) {
            Log::error('importCustomersProductsExcel error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el archivo combinado.'
            ], 422);
        }
    }

    /** Limpia la sesión dte (para finalizar flujo masivo) */
    public function clearSessionAfterBulk(Request $request)
    {
        session()->forget('dte');
        return response()->json(['success' => true]);
    }

    // Helpers privados de normalización
    private function onlyDigits($value)
    {
        if ($value === null)
            return null;
        $digits = preg_replace('/\D+/', '', (string) $value);
        return $digits !== '' ? $digits : null;
    }

    private function normalizeDocType($value)
    {
        if ($value === null)
            return null;
        $val = mb_strtolower(trim((string) $value), 'UTF-8');
        $map = [
            'nit' => '36',
            'dui' => '13',
            'pasaporte' => '02',
            'pasport' => '02',
            'passport' => '02',
            'carnet de residente' => '03',
            'carnet residencia' => '03',
            'carnet de residencia' => '03',
            'residente' => '03',
            'otro' => '37',
            'otros' => '37',
        ];
        if (isset($map[$val]))
            return $map[$val];
        // si ya viene código conocido
        $allowed = ['02', '03', '13', '36', '37'];
        $v = strtoupper((string) $value);
        return in_array($v, $allowed, true) ? $v : null;
    }

    private function normalizeNumeroDocumento($numero, $docCode, $type)
    {
        if ($numero === null)
            return null;
        $raw = trim((string) $numero);
        $digits = preg_replace('/\D+/', '', $raw);

        if ($docCode === '36') { // NIT
            return $digits ?: null;
        }

        if ($docCode === '13') { // DUI
            if (in_array($type, ['03', '05', '06'], true)) {
                return $digits ?: null; // sin guion en CCF/NC/ND por validación MH
            }
            if (strlen($digits) === 9) {
                return substr($digits, 0, 8) . '-' . substr($digits, 8, 1);
            }
            return $digits ?: null; // si trae otros símbolos, los quitamos
        }

        // Otros documentos pueden incluir letras; devolver tal cual, recortado
        return $raw !== '' ? $raw : null;
    }
}
