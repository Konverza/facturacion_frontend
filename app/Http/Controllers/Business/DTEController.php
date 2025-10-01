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
    }

    public function create(Request $request)
    {
        try {
            $business_user = BusinessUser::where("business_id", session("business"))
                ->where("user_id", auth()->user()->id)
                ->first();
            $number = $request->input("document_type");
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
                "municipios" => isset($this->dte["customer"]) ? $this->getMunicipios($this->dte["customer"]["departamento"]) : [],
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

        return $dte;
    }

    public function processDTE(Request $request, $type, $endpoint)
    {
        try {
            if ($this->dte["type"] !== "07") {
                if (!isset($this->dte["products"]) || count($this->dte["products"]) === 0) {
                    return redirect()->back()->with([
                        'error' => "Error",
                        'error_message' => "Debe agregar al menos un producto"
                    ])->send();
                }
            }

            // Validación de stock: sólo para tipos que impactan inventario
            if (!in_array($this->dte['type'], ["07", "14", "04", "15"])) {
                // Agrupar cantidad solicitada por producto de base de datos con stock habilitado
                $cantidadesPorProducto = [];
                $productosCargados = [];
                foreach ($this->dte['products'] as $p) {
                    if (is_array($p) && isset($p['product']) && is_array($p['product']) && isset($p['product']['id'])) {
                        $bp = $productosCargados[$p['product']['id']] ?? BusinessProduct::find($p['product']['id']);
                        if ($bp) {
                            $productosCargados[$bp->id] = $bp; // cache simple
                            if ($bp->has_stock) {
                                $cantidadesPorProducto[$bp->id] = ($cantidadesPorProducto[$bp->id] ?? 0) + (float)($p['cantidad'] ?? 0);
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
                    if ($bp && $bp->has_stock) {
                        $disponible = (float) $bp->stockActual;
                        if ($cantidadSolicitada > $disponible) {
                            $faltantes[] = $bp->descripcion . " (solicitado: " . $cantidadSolicitada . ", disponible: " . $disponible . ")";
                        }
                    }
                }
                if (count($faltantes) > 0) {
                    return redirect()->back()->with([
                        'error' => 'Error',
                        'error_message' => 'Stock insuficiente para: ' . implode('; ', $faltantes)
                    ])->send();
                }
            }

            if ($this->dte["type"] === "15") {

                if (!$this->otrosDocumentos()) {
                    return redirect()->back()->with([
                        'error' => "Error",
                        'error_message' => "Debe agregar al menos un documento asociado a la donación"
                    ])->send();
                }

                $total_pagar = array_sum(array_column(
                    array_filter($this->dte["products"], function ($product) {
                        return $product["tipo_donacion"] == 1;
                    }),
                    "valor_donado"
                ));
                if ($total_pagar > 0 && !$this->pagos()) {
                    return redirect()->back()->with([
                        'error' => "Error",
                        'error_message' => "Debe agregar al menos una forma de pago"
                    ])->send();
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
                        return redirect()->back()->with([
                            'error' => "Error",
                            'error_message' => "Cada documento relacionado debe estar asociado al menos a un producto ingresado."
                        ])->send();
                    }
                }
            }

            if ($this->dte["type"] !== "04") {
                if (isset($this->dte["monto_abonado"]) && isset($this->dte["total_pagar"])) {
                    if (round($this->dte["monto_abonado"], 2) != round($this->dte["total_pagar"], 2)) {
                        return redirect()->back()->with([
                            'error' => "Error",
                            'error_message' => "El monto total pagado no coincide con el total a pagar. Monto abonado: $" . round($this->dte["monto_abonado"], 2) . ", Total a pagar: $" . round($this->dte["total_pagar"], 2)
                        ])->send();
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
                return $data = [
                    "estado" => "BORRADOR",
                    "observaciones" => "Documento guardado como borrador"
                ];
            }

            if ($request->input("action") === "template") {
                if ($request->id_dte !== "" && $request->id_dte !== null) {
                    $this->updateDtePending($request->id_dte, "template", "Documento actualizado como plantilla", $request->template_name);
                } else {
                    $this->createDtePending("Documento guardado como plantilla", "template", $request->template_name);
                }
                session()->forget('dte');
                return $data = [
                    "estado" => "PLANTILLA",
                    "observaciones" => "Documento guardado como plantilla"
                ];
            }

            // dd($dte);
            $response = Http::timeout(30)->post(env("OCTOPUS_API_URL") . $endpoint, $dte);
            $data = json_decode($response->body(), true);
            return $data;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            $this->createDtePending($e->getMessage(), "error");
            return $data = [
                "estado" => "RECHAZADO",
                "observaciones" => $e->getMessage()
            ];
        }
    }

    public function handleResponse($data, $request)
    {
        $business_id = Session::get('business') ?? null;
        if (isset($data["estado"])) {
            if ($data["estado"] === "PROCESADO" || $data["estado"] === "CONTINGENCIA") {
                if ($this->dte["type"] !== "07" && $this->dte["type"] !== "14" && $this->dte["type"] !== "04" && $this->dte["type"] !== "15") {
                    $this->updateStocks($data["codGeneracion"], $this->dte["products"], $business_id, "salida");
                    if ($request->condicion_operacion === 2) {
                        $this->createCXC($data, $request);
                    }
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
            if ($text === null) return null;
            $text = mb_strtolower(trim((string)$text), 'UTF-8');
            $replacements = [
                'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ñ' => 'n',
                'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u', 'Ñ' => 'n',
            ];
            return strtr($text, $replacements);
        };

        $resolveActividad = function ($value) use ($normalize) {
            if ($value === null || $value === '') return [null, null];
            // Acepta valores como "001 - Comercio al por menor" o solo código "001" o solo texto
            $code = null; $desc = null;
            $parts = preg_split('/\s*-\s*/', (string)$value, 2);
            if (count($parts) === 2 && ctype_digit($parts[0])) {
                $code = $parts[0];
                $desc = $parts[1];
            } elseif (ctype_digit((string)$value)) {
                $code = (string)$value;
            } else {
                // buscar por descripción aproximada
                foreach ($this->actividades_economicas as $k => $v) {
                    if ($normalize($v) === $normalize($value)) {
                        $code = (string)$k; $desc = $v; break;
                    }
                }
            }
            if ($code !== null && $desc === null) {
                $desc = $this->actividades_economicas[$code] ?? null;
            }
            // Extraer solo descripción después del guion si aplica
            if ($desc !== null && strpos($desc, '-') !== false) {
                $tmp = explode('-', $desc, 2); $desc = trim($tmp[1]);
            }
            return [$code, $desc];
        };

        $resolveDepartamento = function ($value) use ($normalize) {
            if ($value === null || $value === '') return null;
            // value puede ser código o nombre
            if (isset($this->departamentos[$value])) return $value;
            foreach ($this->departamentos as $code => $dep) {
                $nombre = is_array($dep) ? ($dep['nombre'] ?? (is_string($dep) ? $dep : '')) : (string)$dep;
                if ($normalize($nombre) === $normalize($value)) return (string)$code;
            }
            return null;
        };

        $resolveMunicipio = function ($departamentoCode, $value) use ($normalize) {
            if ($departamentoCode === null || $value === null || $value === '') return null;
            $munis = $this->octopus_service->getCatalog("CAT-012", $departamentoCode);
            if (isset($munis[$value])) return $value;
            foreach ($munis as $code => $mun) {
                $nombre = is_array($mun) ? ($mun['nombre'] ?? (is_string($mun) ? $mun : '')) : (string)$mun;
                if ($normalize($nombre) === $normalize($value)) return (string)$code;
            }
            return null;
        };

        $normalizeTipoPersona = function ($value) use ($normalize) {
            if ($value === null || $value === '') return null;
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
            $numDigits = preg_replace('/\D+/', '', (string)$request->numero_documento);
            $numDoc = $numDigits !== '' ? $numDigits : null;
        }

        // Regla específica para Factura de Sujeto Excluido (type 14):
        // El DUI se escribe sin guion
        if ($type === '14' && $docCode === '13' && $numDoc !== null) {
            $numDoc = preg_replace('/\D+/', '', (string)$numDoc);
        }

        $telefonoStr = ($request->telefono !== null && $request->telefono !== '') ? (string)$request->telefono : null;

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
                "departamento" => $departamentoCode ?? $request->departamento,
                "municipio" => $municipioCode ?? $request->municipio,
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
                            "departamento" => $departamentoCode ?? $request->departamento,
                            "municipio" => $municipioCode ?? $request->municipio,
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
                        "departamento" => $departamentoCode ?? $request->departamento,
                        "municipio" => $municipioCode ?? $request->municipio,
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
                        "departamento" => $departamentoCode ?? $request->departamento,
                        "municipio" => $municipioCode ?? $request->municipio,
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
                        "departamento" => $departamentoCode ?? $request->departamento,
                        "municipio" => $municipioCode ?? $request->municipio,
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
                        "departamento" => $departamentoCode ?? $request->departamento,
                        "municipio" => $municipioCode ?? $request->municipio,
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
                        "departamento" => $departamentoCode ?? $request->departamento,
                        "municipio" => $municipioCode ?? $request->municipio,
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
                        "departamento" => $departamentoCode ?? $request->departamento,
                        "municipio" => $municipioCode ?? $request->municipio,
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
            return [
                "tipoItem" => $product["tipo_item"],
                "cantidad" => $product["cantidad"],
                "codigo" => null,
                "uniMedida" => $product["unidad_medida"],
                "descripcion" => $product["descripcion"],
                "precioUni" => round($product["precio"], 8),
                "montoDescu" => round($product["descuento"], 8),
                "compra" => round($product["ventas_gravadas"], 8),
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

    public function updateStocks($codGeneracion, $productsDTE, $business_id, $tipo = "salida")
    {
        foreach ($productsDTE as $product) {
            $searchProduct = null;
            if (is_array($product)) {
                if (is_array($product["product"])) {
                    $searchProduct = BusinessProduct::find($product["product"]["id"]);
                }
            } else {
                $searchProduct = BusinessProduct::find($product->codigo);
            }

            if ($searchProduct) {
                if ($searchProduct->has_stock) {
                    $stockAnterior = $searchProduct->stockActual;
                    if ($tipo === "salida") {
                        $searchProduct->stockActual -= is_array($product) ? $product["cantidad"] : $product->cantidad;
                    } elseif ($tipo === "entrada") {
                        $searchProduct->stockActual += is_array($product) ? $product["cantidad"] : $product->cantidad;
                    }

                    $stockActual = $searchProduct->stockActual;
                    if ($stockActual <= $searchProduct->stockMinimo) {
                        $searchProduct->estado_stock = "agotado";
                    } elseif (($stockActual - $searchProduct->stockMinimo) <= 2) {
                        $searchProduct->estado_stock = "por_agotarse";
                    } else {
                        $searchProduct->estado_stock = "disponible";
                    }

                    $diferencia = abs($stockAnterior - $stockActual);
                    if ($diferencia >= 1) {
                        BusinessProductMovement::create([
                            "business_product_id" => $searchProduct->id,
                            "numero_factura" => $codGeneracion,
                            "tipo" => $tipo,
                            "cantidad" => is_array($product) ? $product["cantidad"] : $product->cantidad,
                            "precio_unitario" => $searchProduct->precioUni,
                            "producto" => $searchProduct->descripcion,
                            "descripcion" => $tipo === "salida" ? "Venta de producto" : "Anulación de documento",
                        ]);
                    }
                    $searchProduct->save();
                }
            }
        }
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

            $receptor = ($dte['tipo_dte'] === '14') ? $documento->sujetoExcluido : $documento->receptor ?? "";

            $nombre = $receptor->nombre ?? "";
            if (in_array($dte['tipo_dte'], ['03', '05', '06'])) {
                $tipoDoc = "36";
                $numDocumento = $receptor->nit;
            } else {
                $tipoDoc = $receptor->tipoDocumento;
                $numDocumento = $receptor->numDocumento;
            }

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
                    "nombreSolicita" => $nombre ?? auth()->user()->name,
                    "tipoDocSolicita" => $tipoDoc ?? "36",
                    "numDocSolicita" => $numDocumento ?? $business->nit,
                ]
            ]);
            $data = $response->json();
            if ($response->status() == 201) {
                if (!in_array($dte["tipo_dte"], ["04", "07", "14"])) {
                    $products_dte = $documento->cuerpoDocumento;
                    $this->updateStocks($codGeneracion, $products_dte, $business_id, "entrada");
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

    public function anexos(Request $request)
    {
        $business_id = Session::get('business') ?? null;
        $business = Business::find($business_id);
        $desde = "{$request->desde}T00:00:00";
        $hasta = "{$request->hasta}T23:59:59";

        $dtes = Http::get(env("OCTOPUS_API_URL") . "/dtes/?nit=" . $business->nit . "&fechaInicio=" . $desde . "&fechaFin=" . $hasta)->json();
        $dtes = $dtes["items"] ?? [];
        $dte_collection = null;

        switch ($request->tipo) {
            case "1":
                foreach ($dtes as $dte) {
                    if ($dte["estado"] == "PROCESADO" && in_array($dte["tipo_dte"], ["03", "05", "06"])) {
                        $dte["documento"] = json_decode($dte["documento"]);
                        $dte_collection[] = $dte;
                    }
                }
                $dte_collection = collect($dte_collection);
                $fileName = 'anexo-f07-contribuyentes.csv';
                $filePath = "exports/{$fileName}";
                Excel::store(new Contribuyente($dte_collection), $filePath, 'public');
                if (!Storage::disk('public')->exists($filePath)) {
                    return response()->json(['error' => 'Error al generar el archivo'], 500);
                }
                return response()->download(storage_path("app/public/{$filePath}"), $fileName, [
                    'Content-Type' => 'text/csv',
                    'X-Download-Started' => 'true'
                ])->deleteFileAfterSend(true);
            case "2":
                $dte_collection = collect();
                foreach ($dtes as $dte) {
                    if ($dte["estado"] === "PROCESADO" && in_array($dte["tipo_dte"], ["01", "11"])) {
                        $dte["documento"] = json_decode($dte["documento"]);
                        $dte_collection->push($dte);
                    }
                }

                // Agrupar por fecha y ordenar dentro de cada grupo por fecha y hora
                $grouped_dtes = $dte_collection
                    ->sortBy('fhEmision') // Ordenar globalmente antes de agrupar
                    ->groupBy(fn($dte) => \Carbon\Carbon::parse($dte["fhEmision"])->toDateString())
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

                        if ($totalGravado > 0 && $totalNoSuj == 0 && $totalExento == 0) {
                            $tipoOperacion = "01";
                        } elseif (($totalGravado == 0) && ($totalNoSuj > 0 || $totalExento > 0)) {
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

                $fileName = 'anexo-f07-consumidor-final.csv';
                $filePath = "exports/{$fileName}";

                // Guardar el archivo temporalmente
                Excel::store(new ConsumidorFinal($result), $filePath, 'public');

                // Verificar si el archivo se generó
                if (!Storage::disk('public')->exists($filePath)) {
                    return response()->json(['error' => 'Error al generar el archivo'], 500);
                }

                // Devolver la descarga con la cabecera personalizada
                return response()->download(storage_path("app/public/{$filePath}"), $fileName, [
                    'Content-Type' => 'text/csv',
                    'X-Download-Started' => 'true'
                ])->deleteFileAfterSend(true);
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
        if (!isset($this->dte['products'])) { $this->dte['products'] = []; }
        if (!isset($this->dte['customer'])) { $this->dte['customer'] = []; }
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
        $business_user = BusinessUser::where('business_id', session('business'))
            ->where('user_id', auth()->id())
            ->first();
        $pos_id = $business_user?->default_pos_id;
        if (!$pos_id) {
            $pos = PuntoVenta::where('business_id', session('business'))->first();
            $pos_id = $pos?->id;
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

        // Helpers privados de normalización
        private function onlyDigits($value)
        {
            if ($value === null) return null;
            $digits = preg_replace('/\D+/', '', (string)$value);
            return $digits !== '' ? $digits : null;
        }

        private function normalizeDocType($value)
        {
            if ($value === null) return null;
            $val = mb_strtolower(trim((string)$value), 'UTF-8');
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
            if (isset($map[$val])) return $map[$val];
            // si ya viene código conocido
            $allowed = ['02','03','13','36','37'];
            $v = strtoupper((string)$value);
            return in_array($v, $allowed, true) ? $v : null;
        }

        private function normalizeNumeroDocumento($numero, $docCode, $type)
        {
            if ($numero === null) return null;
            $raw = trim((string)$numero);
            $digits = preg_replace('/\D+/', '', $raw);

            if ($docCode === '36') { // NIT
                return $digits ?: null;
            }

            if ($docCode === '13') { // DUI
                if (in_array($type, ['03','05','06'], true)) {
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
