<?php

namespace App\Http\Controllers\Business;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BusinessProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Expr\FuncCall;

class DTEProductController extends Controller
{
    public $dte;

    public function __construct()
    {
        $this->dte = session("dte", ["products" => []]);
    }

    public function select(Request $request)
    {
        $product = BusinessProduct::find($request->product_id);
        $dte = session("dte")["type"];

        if (!$product) {
            return response()->json([
                "success" => false,
                "message" => "Producto no encontrado"
            ]);
        }

        return response()->json([
            "success" => true,
            "product" => $product,
            "dte" => $dte
        ]);
    }

    public function store(Request $request)
    {
        try {
            $this->dte = session("dte", ["products" => []]);

            if (!isset($this->dte["products"]) || !is_array($this->dte["products"])) {
                $this->dte["products"] = [];
            }

            if ($this->dte["type"] === "05" || $this->dte["type"] === "06") {
                if ($request->documento_relacionado === "" || $request->documento_relacionado === null) {
                    return response()->json([
                        "success" => false,
                        "message" => "Debe seleccionar un documento relacionado"
                    ]);
                }
            }

            $business_product = BusinessProduct::find($request->product_id);
            if (!$business_product) {
                return response()->json([
                    "success" => false,
                    "message" => "Producto no encontrado"
                ]);
            }

            $stock = (float) $business_product->stockActual;
            $found = false;

            foreach ($this->dte["products"] as &$product) {
                if (
                    $product["product_id"] == $business_product->id &&
                    $product["tipo"] == $request->tipo
                ) {
                    $found = true;

                    $cantidadActual = (float) $product["cantidad"];
                    $cantidadNueva = (float) $request->cantidad;

                    if ($cantidadActual + $cantidadNueva > $stock) {
                        return response()->json([
                            "success" => false,
                            "message" => "No hay suficiente stock para el producto, ya se agregó la cantidad máxima."
                        ]);
                    }

                    $product["cantidad"] += $cantidadNueva;
                    $product["descuento"] += (float) ($request->descuento ?? 0);
                    $product["total"] += (float) $request->total;
                    $product["documento_relacionado"] = $request->documento_relacionado ?? null;
                    $product["ventas_gravadas"] += $request->tipo === "Gravada" ? $request->total : 0;
                    $product["ventas_exentas"] += $request->tipo === "Exenta" ? $request->total : 0;
                    $product["ventas_no_sujetas"] = $request->tipo === "No sujeta" ? $request->total : 0;

                    $iva = 0;
                    if ($request->tipo === "Gravada") {
                        if ($this->dte["type"] === "03") {
                            $iva = round($product["total"] * 0.13, 2);
                        } else {
                            $iva = round(($product["total"] / 1.13) * 0.13, 2);
                        }
                        $product["iva"] = $iva; // Asignar el nuevo IVA calculado
                    }

                    break;
                }
            }

            $iva = 0;
            if ($request->tipo === "Gravada") {
                if ($this->dte["type"] === "03") {
                    $iva = round((float) $request->total * 0.13, 2);
                } else {
                    $iva = round(((float) $request->total / 1.13) * 0.13, 2);
                }
            }

            $product_tributes = json_decode($business_product->tributos, true);

            if (!$found) {
                $precio = (float) $business_product->precioUni;
                $precio_sin_tributos = (float) $business_product->precioSinTributos;
                $cantidad = (float) $request->cantidad;
                $total = (float) $request->total;
                $descuento = (float) ($request->descuento ?? 0);


                $this->dte["remove_discounts"] = in_array("59", $product_tributes) || in_array("71", $product_tributes) || in_array("D1", $product_tributes) || in_array("C8", $product_tributes) || in_array("C5", $product_tributes) || in_array("C6", $product_tributes) || in_array("C7", $product_tributes);

                $this->dte["products"][] = [
                    "id" => rand(1, 1000),
                    "product" => $business_product,
                    "product_id" => $business_product->id,
                    "unidad_medida" => $business_product->uniMedida,
                    "descripcion" => $business_product->descripcion,
                    "cantidad" => $cantidad,
                    "tipo" => $request->tipo,
                    "precio" => $precio,
                    "precio_sin_tributos" => $precio_sin_tributos,
                    "descuento" => $descuento,
                    "ventas_gravadas" => $request->tipo === "Gravada" ? $total : 0,
                    "ventas_exentas" => $request->tipo === "Exenta" ? $total : 0,
                    "ventas_no_sujetas" => $request->tipo === "No sujeta" ? $total : 0,
                    "total" => $total,
                    "turismo_por_alojamiento" => in_array("59", $product_tributes) ? "active" : "inactive",
                    "turismo_salida_pais_via_aerea" => in_array("71", $product_tributes) ? "active" : "inactive",
                    "fovial" => in_array("D1", $product_tributes) ? "active" : "inactive",
                    "contrans" => in_array("C8", $product_tributes) ? "active" : "inactive",
                    "bebidas_alcoholicas" => in_array("C5", $product_tributes) ? "active" : "inactive",
                    "tabaco_cigarillos" => in_array("C6", $product_tributes) ? "active" : "inactive",
                    "tabaco_cigarros" => in_array("C7", $product_tributes) ? "active" : "inactive",
                    "iva" => $iva,
                    "documento_relacionado" => $request->documento_relacionado ?? null
                ];
            }

            $this->totals();

            // Guardar en sesión
            session(["dte" => $this->dte]);

            // Retornar respuesta
            return response()->json([
                "success" => true,
                "product" => $this->dte,
                "table_products" => view("layouts.partials.ajax.business.table-products-dte", [
                    "dte" => $this->dte
                ])->render(),
                "total_pagar" => $this->dte["total_pagar"],
                "monto_pendiente" => $this->dte["monto_pendiente"],
                "table_exportacion" => view("layouts.partials.ajax.business.table-exportacion", [
                    "dte" => $this->dte
                ])->render(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Ha ocurrido un error al guardar el producto: " . $e->getMessage(),
            ]);
        }
    }

    public function store_new(Request $request)
    {
        try {
            DB::beginTransaction();
            if ($this->dte["type"] === "05" || $this->dte["type"] === "06") {
                if ($request->documento_relacionado === "" || $request->documento_relacionado === null) {
                    return response()->json([
                        "success" => false,
                        "message" => "Debe seleccionar un documento relacionado"
                    ]);
                }
            }

            if ($this->dte["type"] !== "14") {
                $precio_unitario = floatval($request->precio_unitario);
                $stock_inicial = floatval($request->stock_inicial);

                $business_product = BusinessProduct::create([
                    "business_id" => session("business"),
                    "tipoItem" => $request->tipo_item,
                    "codigo" => $request->codigo ?? "---",
                    "uniMedida" => $request->unidad_medida,
                    "descripcion" => $request->descripcion ?? "Sin descripción",
                    "precioUni" => $precio_unitario,
                    "precioSinTributos" => $precio_unitario / 1.13,
                    "tributos" => json_encode($request->tributos),
                    "stockInicial" => $stock_inicial,
                    "stockActual" => $stock_inicial,
                ]);

                $product_tributes = json_decode($business_product->tributos, true) ?? [];

                $total = floatval($request->total);
                if ($this->dte["type"] === "03") {
                    $total /= 1.13;
                }

                $iva = 0;
                if ($request->tipo === "Gravada") {
                    $iva = ($this->dte["type"] === "03")
                        ? round($total * 0.13, 2)
                        : round(($total / 1.13) * 0.13, 2);
                }

                $cantidad = floatval($request->cantidad);

                $this->dte["remove_discounts"] = in_array("59", $product_tributes) || in_array("71", $product_tributes) || in_array("D1", $product_tributes) || in_array("C8", $product_tributes) || in_array("C5", $product_tributes) || in_array("C6", $product_tributes) || in_array("C7", $product_tributes);

                $this->dte["products"][] = [
                    "id" => rand(1, 1000),
                    "product" => $business_product,
                    "product_id" => $business_product->id,
                    "unidad_medida" => $business_product->uniMedida,
                    "descripcion" => $business_product->descripcion,
                    "cantidad" => $cantidad,
                    "tipo" => $request->tipo,
                    "precio" => $precio_unitario,
                    "precio_sin_tributos" => $business_product->precioSinTributos,
                    "descuento" => floatval($request->descuento ?? 0),
                    "ventas_gravadas" => $request->tipo === "Gravada" ? $total : 0,
                    "ventas_exentas" => $request->tipo === "Exenta" ? $total : 0,
                    "ventas_no_sujetas" => $request->tipo === "No sujeta" ? $total : 0,
                    "total" => $total,
                    "turismo_por_alojamiento" => in_array("59", $product_tributes) ? "active" : "inactive",
                    "turismo_salida_pais_via_aerea" => in_array("71", $product_tributes) ? "active" : "inactive",
                    "fovial" => in_array("D1", $product_tributes) ? "active" : "inactive",
                    "contrans" => in_array("C8", $product_tributes) ? "active" : "inactive",
                    "bebidas_alcoholicas" => in_array("C5", $product_tributes) ? "active" : "inactive",
                    "tabaco_cigarillos" => in_array("C6", $product_tributes) ? "active" : "inactive",
                    "tabaco_cigarros" => in_array("C7", $product_tributes) ? "active" : "inactive",
                    "iva" => $iva,
                    "documento_relacionado" => $request->documento_relacionado ?? null
                ];
            } else {
                $this->dte["products"][] = [
                    "id" => rand(1, 1000),
                    "product" => null,
                    "product_id" => null,
                    "unidad_medida" => $request->unidad_medida,
                    "descripcion" => $request->descripcion,
                    "cantidad" => $request->cantidad,
                    "tipo" => $request->tipo,
                    "precio" => $request->precio_unitario,
                    "precio_sin_tributos" => $request->precio,
                    "descuento" => floatval($request->descuento ?? 0),
                    "ventas_gravadas" =>  $request->tipo === "Gravada" ? $request->total : 0,
                    "ventas_exentas" =>  0,
                    "ventas_no_sujetas" => 0,
                    "total" => $request->total,
                    "turismo_por_alojamiento" => 0,
                    "turismo_salida_pais_via_aerea" =>  0,
                    "fovial" =>  0,
                    "contrans" =>  0,
                    "bebidas_alcoholicas" => 0,
                    "tabaco_cigarillos" =>  0,
                    "tabaco_cigarros" =>  0,
                    "iva" => 0,
                    "tipo_item" => $request->tipo_item,
                    "documento_relacionado" => null
                ];
            }

            DB::commit();
            session(["dte" => $this->dte]);
            $this->totals();

            $business_products = BusinessProduct::where("business_id", session("business"))->get();

            return response()->json([
                "success" => true,
                "message" => "Producto guardado correctamente",
                "table_data" => view("layouts.partials.ajax.business.table-products-dte", [
                    "dte" => $this->dte
                ])->render(),
                "table" => "products-dte",
                "drawer" => "drawer-new-product",
                "table_selected_product" => view("layouts.partials.ajax.business.table-selected-product", [
                    "business_products" => $business_products,
                    "number" => $this->dte["type"]
                ])->render(),
                "total_pagar" => $this->dte["total_pagar"],
                "monto_pendiente" => $this->dte["monto_pendiente"],
                "table_exportacion" => view("layouts.partials.ajax.business.table-exportacion", [
                    "dte" => $this->dte
                ])->render(),
                "table_sujeto_excluido" => view("layouts.partials.ajax.business.table-sujeto-excluido", [
                    "dte" => $this->dte
                ])->render(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "success" => false,
                "message" => "Ha ocurrido un error al guardar el producto: " . $e->getMessage(),
            ]);
        }
    }

    public function unaffected_amounts(Request $request)
    {
        try {
            $this->dte["products"][] = [
                "id" => rand(1, 1000),
                "product_id" => "",
                "unidad_medida" => "Otra",
                "descripcion" => $request->descripcion,
                "cantidad" => 1,
                "tipo" => "Gravada",
                "precio" => $request->monto,
                "precio_sin_tributos" => $request->monto,
                "descuento" => 0,
                "ventas_gravadas" => $request->monto,
                "ventas_exentas" => 0,
                "ventas_no_sujetas" => 0,
                "total" => (float) $request->monto,
                "turismo_por_alojamiento" => 0,
                "turismo_salida_pais_via_aerea" => 0,
                "fovial" => 0,
                "contrans" => 0,
                "bebidas_alcoholicas" => 0,
                "tabaco_cigarillos" => 0,
                "tabaco_cigarros" => 0,
            ];

            $this->totals();
            session(["dte" => $this->dte]);
            return response()->json([
                "success" => true,
                "message" => "Item guardado correctamente",
                "table_data" => view("layouts.partials.ajax.business.table-products-dte", [
                    "dte" => $this->dte
                ])->render(),
                "table" => "products-dte",
                "modal" => "unaffected-amounts",
                "monto_pendiente" => $this->dte["monto_pendiente"]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Ha ocurrido un error al guardar el producto",
            ]);
        }
    }

    public function taxes_iva(Request $request)
    {
        try {
            $this->dte["products"][] = [
                "id" => rand(1, 1000),
                "product_id" => "",
                "unidad_medida" => "Otra",
                "descripcion" => $request->descripcion,
                "cantidad" => 1,
                "tipo" => $request->tipo,
                "precio" => (float) $request->monto,
                "precio_sin_tributos" => $request->monto,
                "descuento" => 0,
                "ventas_gravadas" => $request->tipo === "Gravada" ? (float) $request->monto : 0,
                "ventas_exentas" => $request->tipo === "Exenta" ? (float) $request->monto : 0,
                "ventas_no_sujetas" => $request->tipo === "No sujeta" ? (float) $request->monto : 0,
                "total" => (float) $request->monto,
                "turismo_por_alojamiento" => 0,
                "turismo_salida_pais_via_aerea" => 0,
                "fovial" => 0,
                "contrans" => 0,
                "bebidas_alcoholicas" => 0,
                "tabaco_cigarillos" => 0,
                "tabaco_cigarros" => 0,
            ];

            $this->totals();
            session(["dte" => $this->dte]);
            return response()->json([
                "success" => true,
                "message" => "Item guardado correctamente",
                "table_data" => view("layouts.partials.ajax.business.table-products-dte", [
                    "dte" => $this->dte
                ])->render(),
                "table" => "products-dte",
                "modal" => "taxes-iva",
                "monto_pendiente" => $this->dte["monto_pendiente"]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Ha ocurrido un error al guardar el producto",
            ]);
        }
    }

    public function delete(string $id)
    {
        try {
            foreach ($this->dte["products"] as $key => $product) {
                if ($product["id"] == $id) {
                    unset($this->dte["products"][$key]);
                }
            }

            if (count($this->dte["products"]) === 0) {
                $this->dte["total_descuentos"] = 0;
                $this->dte["total_pagar"] = 0;
                $this->dte["descuento_venta_gravada"] = 0;
                $this->dte["descuento_venta_exenta"] = 0;
                $this->dte["descuento_venta_no_sujeta"] = 0;
                $this->dte["monto_pendiente"] = 0;
                $this->dte["monto_abonado"] = 0;
                $this->dte["total_ventas_gravadas"] = 0;
                $this->dte["total_ventas_exentas"] = 0;
                $this->dte["total_ventas_no_sujetas"] = 0;
                $this->dte["total_taxes"] = 0;
                $this->dte["total"] = 0;
                $this->dte["subtotal"] = 0;
                $this->dte["total_iva_retenido"] = 0;
                $this->dte["isr"] = 0;
                $this->dte["iva"] = 0;
                $this->dte["retener_iva"] = "inactive";
                $this->dte["retener_renta"] = "inactive";
                $this->dte["percibir_iva"] = "inactive";
                $this->dte["percentaje_descuento_venta_gravada"] = 0;
                $this->dte["percentaje_descuento_venta_exenta"] = 0;
                $this->dte["percentaje_descuento_venta_no_sujeta"] = 0;
                $this->dte["turismo_por_alojamiento"] = 0;
                $this->dte["turismo_salida_pais_via_aerea"] = 0;
                $this->dte["fovial"] = 0;
                $this->dte["contrans"] = 0;
                $this->dte["bebidas_alcoholicas"] = 0;
                $this->dte["tabaco_cigarillos"] = 0;
                $this->dte["tabaco_cigarros"] = 0;
            }

            $this->totals();
            session(["dte" => $this->dte]);
            return response()->json([
                "success" => true,
                "message" => "Producto eliminado correctamente",
                "table_data" => view("layouts.partials.ajax.business.table-products-dte", [
                    "dte" => $this->dte
                ])->render(),
                "table" => "products-dte",
                "total_pagar" => $this->dte["total_pagar"],
                "monto_pendiente" => $this->dte["monto_pendiente"],
                "table_exportacion" => view("layouts.partials.ajax.business.table-exportacion", [
                    "dte" => $this->dte
                ])->render(),
                "table_sujeto_excluido" => view("layouts.partials.ajax.business.table-sujeto-excluido", [
                    "dte" => $this->dte
                ])->render()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Ha ocurrido un error al eliminar el producto"
            ]);
        }
    }

    public function withhold(Request $request)
    {
        try {
            if ($request->input("type") === "iva") {
                $this->dte["retener_iva"] = $request->input("value");
            } elseif ($request->input("type") === "percibir_iva") {
                $this->dte["percibir_iva"] = $request->input("value");
            } else {
                $this->dte["retener_renta"] = $request->input("value");
            }
            $this->totals();
            session(["dte" => $this->dte]);
            return response()->json([
                "success" => true,
                "table_products" => view("layouts.partials.ajax.business.table-products-dte", [
                    "dte" => $this->dte
                ])->render(),
                "total_pagar" => $this->dte["total_pagar"],
                "monto_pendiente" => $this->dte["monto_pendiente"]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Ha ocurrido un error al guardar la retención"
            ]);
        }
    }

    public function add_discounts(Request $request)
    {
        try {

            if (isset($this->dte["metodos_pago"]) && count($this->dte["metodos_pago"]) > 0) {
                return response()->json([
                    "success" => false,
                    "message" => "No se pueden aplicar descuentos si ya se han ingresado formas de pago"
                ]);
            }

            if ($this->dte["total_pagar"] == 0) {
                return response()->json([
                    "success" => false,
                    "message" => "Ingrese productos para aplicar descuentos"
                ]);
            }

            if ($this->dte["total_pagar"] < $request->descuento_venta_gravada + $request->descuento_venta_exenta + $request->descuento_venta_no_sujeta) {
                return response()->json([
                    "success" => false,
                    "message" => "El monto de los descuentos no puede ser mayor al total a pagar"
                ]);
            }

            $tiposVentas = [
                'gravadas' => 'descuento_venta_gravada',
                'exentas' => 'descuento_venta_exenta',
                'no_sujetas' => 'descuento_venta_no_sujeta',
            ];

            foreach ($tiposVentas as $tipo => $descuentoKey) {
                $totalVentaKey = "total_ventas_" . $tipo;
                $requestDescuentoKey = "descuento_venta_" . $tipo;
                $percentajeDescuentoKey = "percentaje_" . $descuentoKey;

                $totalVenta = floatval($this->dte[$totalVentaKey] ?? 0);
                $porcentajeDescuento = floatval($request->$requestDescuentoKey ?? 0);

                if (!$totalVenta > 0 && !$porcentajeDescuento > 0) {
                    $this->dte[$descuentoKey] = 0;
                }

                $this->dte[$percentajeDescuentoKey] = $porcentajeDescuento;
            }

            session(["dte" => $this->dte]);
            $this->totals();

            return response()->json([
                "success" => true,
                "message" => "Descuentos aplicados correctamente",
                "table_data" => view("layouts.partials.ajax.business.table-products-dte", [
                    "dte" => $this->dte
                ])->render(),
                "table" => "products-dte",
                "modal" => "add-discount",
                "total_pagar" => $this->dte["total_pagar"],
                "total_discounts" => view("layouts.partials.ajax.business.totals-discounts", [
                    "dte" => $this->dte
                ])->render(),
                "monto_pendiente" => $this->dte["monto_pendiente"],
                "table_sujeto_excluido" => view("layouts.partials.ajax.business.table-sujeto-excluido", [
                    "dte" => $this->dte
                ])->render()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Ha ocurrido un error al aplicar los descuentos: " . $e->getMessage()
            ]);
        }
    }

    public function exportacion(Request $request)
    {
        try {
            $type = $request->type;
            if ($this->dte["total"] === 0) {
                return response()->json([
                    "success" => false,
                    "message" => "Ingrese productos para aplicar exportación"
                ]);
            }

            if ($type === "flete") {
                $this->dte["flete"] = $request->value;
            } else {
                $this->dte["seguro"] = $request->value;
            }
            $this->totals();
            session(["dte" => $this->dte]);
            return response()->json([
                "success" => true,
                "message" => ucfirst($type) . " aplicado correctamente",
                "table_exportacion" => view("layouts.partials.ajax.business.table-exportacion", [
                    "dte" => $this->dte
                ])->render(),
                "monto_pendiente" => $this->dte["monto_pendiente"]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Ha ocurrido un error al aplicar el seguro"
            ]);
        }
    }

    public function remove_discounts()
    {
        try {
            $this->dte["descuento_venta_gravada"] = 0;
            $this->dte["descuento_venta_exenta"] = 0;
            $this->dte["descuento_venta_no_sujeta"] = 0;

            $this->dte["percentaje_descuento_venta_gravada"] = 0;
            $this->dte["percentaje_descuento_venta_exenta"] = 0;
            $this->dte["percentaje_descuento_venta_no_sujeta"] = 0;

            session(["dte" => $this->dte]);
            $this->totals();
            return response()->json([
                "success" => true,
                "message" => "Descuentos eliminados correctamente",
                "table_data" => view("layouts.partials.ajax.business.table-products-dte", [
                    "dte" => $this->dte
                ])->render(),
                "table" => "products-dte",
                "total_pagar" => $this->dte["total_pagar"],
                "total_discounts" => view("layouts.partials.ajax.business.totals-discounts", [
                    "dte" => $this->dte
                ])->render(),
                "monto_pendiente" => $this->dte["monto_pendiente"],
                "table_sujeto_excluido" => view("layouts.partials.ajax.business.table-sujeto-excluido", [
                    "dte" => $this->dte
                ])->render()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Ha ocurrido un error al eliminar los descuentos"
            ]);
        }
    }

    public function totals()
    {
        $this->total_init();
        $this->total_ventas();
        $this->total_descuentos();
        $this->total_taxes();

        $this->dte["subtotal"] =
            $this->dte["total_ventas_gravadas"]
            + $this->dte["total_ventas_exentas"]
            + $this->dte["total_ventas_no_sujetas"];

        if ($this->dte["type"] !== "14" && $this->dte["type"] !== "11" && $this->dte["type"] !== "07") {
            $this->dte["total"] = $this->dte["subtotal"] + $this->dte["total_taxes"];
        } else {
            $this->dte["total"] = $this->dte["subtotal"];
        }

        $this->dte["total_pagar"] -= $this->dte["total_descuentos"];

        $this->total_retenciones();

        if ($this->dte["type"] === "14") {
            $this->dte["total_pagar"] -= $this->dte["isr"];
        }

        if ($this->dte["type"] === "11") {
            $this->dte["total_pagar"] = round((float)$this->dte["total_ventas_gravadas"] + $this->dte["flete"] + $this->dte["seguro"], 2);
        }

        $this->dte["monto_pendiente"] = $this->dte["total_pagar"] - $this->dte["monto_abonado"] ?? 0;
        session(["dte" => $this->dte]);
    }

    public function total_init()
    {
        if (!isset($this->dte["total_ventas_gravadas"])) {
            $this->dte["total_ventas_gravadas"] = 0;
        }

        if (!isset($this->dte["total_ventas_exentas"])) {
            $this->dte["total_ventas_exentas"] = 0;
        }

        if (!isset($this->dte["total_ventas_no_sujetas"])) {
            $this->dte["total_ventas_no_sujetas"] = 0;
        }

        if (!isset($this->dte["seguro"])) {
            $this->dte["seguro"] = 0;
        }

        if (!isset($this->dte["flete"])) {
            $this->dte["flete"] = 0;
        }

        if (!isset($this->dte["total_taxes"])) {
            $this->dte["total_taxes"] = 0;
        }

        if (!isset($this->dte["total_descuentos"])) {
            $this->dte["total_descuentos"] = 0;
        }

        if (!isset($this->dte["total"])) {
            $this->dte["total"] = 0;
        }

        if (!isset($this->dte["subtotal"])) {
            $this->dte["subtotal"] = 0;
        }

        if (!isset($this->dte["total_iva_retenido"])) {
            $this->dte["total_iva_retenido"] = 0;
        }

        if (!isset($this->dte["isr"])) {
            $this->dte["isr"] = 0;
        }

        if (!isset($this->dte["iva"])) {
            $this->dte["iva"] = 0;
        }

        if (!isset($this->dte["total_pagar"])) {
            $this->dte["total_pagar"] = 0;
        }

        if (!isset($this->dte["descuento_venta_gravada"])) {
            $this->dte["descuento_venta_gravada"] = 0;
        }

        if (!isset($this->dte["descuento_venta_exenta"])) {
            $this->dte["descuento_venta_exenta"] = 0;
        }

        if (!isset($this->dte["descuento_venta_no_sujeta"])) {
            $this->dte["descuento_venta_no_sujeta"] = 0;
        }

        if (!isset($this->dte["percentaje_descuento_venta_gravada"])) {
            $this->dte["percentaje_descuento_venta_gravada"] = 0;
        }

        if (!isset($this->dte["percentaje_descuento_venta_exenta"])) {
            $this->dte["percentaje_descuento_venta_exenta"] = 0;
        }

        if (!isset($this->dte["percentaje_descuento_venta_no_sujeta"])) {
            $this->dte["percentaje_descuento_venta_no_sujeta"] = 0;
        }

        if (!isset($this->dte["retener_iva"]) && !isset($this->dte["retener_renta"]) && !isset($this->dte["percibir_iva"])) {
            $this->dte["retener_iva"] = "inactive";
            $this->dte["retener_renta"] = "inactive";
            $this->dte["percibir_iva"] = "inactive";
        }

        if (!isset($this->dte["monto_abonado"])) {
            $this->dte["monto_abonado"] = 0;
        }

        if (!isset($this->dte["turismo_por_alojamiento"])) {
            $this->dte["turismo_por_alojamiento"] = 0;
        }

        if (!isset($this->dte["turismo_salida_pais_via_aerea"])) {
            $this->dte["turismo_salida_pais_via_aerea"] = 0;
        }

        if (!isset($this->dte["fovial"])) {
            $this->dte["fovial"] = 0;
        }

        if (!isset($this->dte["contrans"])) {
            $this->dte["contrans"] = 0;
        }

        if (!isset($this->dte["bebidas_alcoholicas"])) {
            $this->dte["bebidas_alcoholicas"] = 0;
        }

        if (!isset($this->dte["tabaco_cigarillos"])) {
            $this->dte["tabaco_cigarillos"] = 0;
        }

        if (!isset($this->dte["tabaco_cigarros"])) {
            $this->dte["tabaco_cigarros"] = 0;
        }

        if (!isset($this->dte["total_ventas_gravadas_descuento"])) {
            $this->dte["total_ventas_gravadas_descuento"] = 0;
        }

        if (!isset($this->dte["remove_discounts"])) {
            $this->dte["remove_discounts"] = false;
        }
    }

    public function total_ventas()
    {
        $this->dte["total_ventas_gravadas"] = round((float)array_sum(array_map(fn($product) => $product["ventas_gravadas"] ?? 0, $this->dte["products"] ?? [])), 2);

        $this->dte["total_ventas_exentas"] = round((float)array_sum(array_map(fn($product) => $product["ventas_exentas"] ?? 0, $this->dte["products"] ?? [])), 2);

        $this->dte["total_ventas_no_sujetas"] = round((float)array_sum(array_map(fn($product) => $product["ventas_no_sujetas"] ?? 0, $this->dte["products"] ?? [])), 2);
    }

    public function total_taxes()
    {
        $this->dte["turismo_por_alojamiento"] = 0;
        $this->dte["turismo_salida_pais_via_aerea"] = 0;
        $this->dte["fovial"] = 0;
        $this->dte["contrans"] = 0;
        $this->dte["bebidas_alcoholicas"] = 0;
        $this->dte["tabaco_cigarillos"] = 0;
        $this->dte["tabaco_cigarros"] = 0;

        if (isset($this->dte["products"]) && count($this->dte["products"]) > 0) {
            foreach ($this->dte["products"] as $product) {
                $total = round(($product["total"] ?? 0), 2);
                $cantidad = round(($product["cantidad"] ?? 0), 2);

                if (!empty($product["turismo_por_alojamiento"]) && $product["turismo_por_alojamiento"] === "active") {
                    $this->dte["turismo_por_alojamiento"] += round(($total / $cantidad) * 0.05, 2) * $cantidad;
                }

                if (!empty($product["turismo_salida_pais_via_aerea"]) && $product["turismo_salida_pais_via_aerea"] === "active") {
                    $this->dte["turismo_salida_pais_via_aerea"] += round($cantidad * 7, 2);
                }

                if (!empty($product["fovial"]) && $product["fovial"] === "active") {
                    $this->dte["fovial"] += round($cantidad * 0.20, 2);
                }

                if (!empty($product["contrans"]) && $product["contrans"] === "active") {
                    $this->dte["contrans"] += round($cantidad * 0.10, 2);
                }

                if (!empty($product["bebidas_alcoholicas"]) && $product["bebidas_alcoholicas"] === "active") {
                    $this->dte["bebidas_alcoholicas"] += round(($total / $cantidad) * 0.08, 2) * $cantidad;
                }

                if (!empty($product["tabaco_cigarillos"]) && $product["tabaco_cigarillos"] === "active") {
                    $this->dte["tabaco_cigarillos"] += round(($total / $cantidad) * 0.39, 2) * $cantidad;
                }

                if (!empty($product["tabaco_cigarros"]) && $product["tabaco_cigarros"] === "active") {
                    $this->dte["tabaco_cigarros"] += round(($total / $cantidad) * 1, 2) * $cantidad;
                }
            }
        }

        if ($this->dte["turismo_por_alojamiento"] === 0 && $this->dte["turismo_salida_pais_via_aerea"] === 0 && $this->dte["fovial"] === 0 && $this->dte["contrans"] === 0 && $this->dte["bebidas_alcoholicas"] === 0 && $this->dte["tabaco_cigarillos"] === 0 && $this->dte["tabaco_cigarros"] === 0) {
            $this->dte["remove_discounts"] = false;
        } else {
            $this->dte["remove_discounts"] = true;
        }

        $this->dte["total_taxes"] =
            $this->dte["turismo_por_alojamiento"] +
            $this->dte["turismo_salida_pais_via_aerea"] +
            $this->dte["fovial"] +
            $this->dte["contrans"] +
            $this->dte["bebidas_alcoholicas"] +
            $this->dte["tabaco_cigarillos"] +
            $this->dte["tabaco_cigarros"];

        $this->dte["total_taxes"] = round((float) $this->dte["total_taxes"], 2);
    }

    public function total_descuentos()
    {
        if ($this->dte["remove_discounts"]) {
            $this->dte["descuento_venta_gravada"] = 0;
            $this->dte["descuento_venta_exenta"] = 0;
            $this->dte["descuento_venta_no_sujeta"] = 0;
            $this->dte["total_descuentos"] = 0;
        } else {
            $this->dte["descuento_venta_gravada"] = round($this->dte["total_ventas_gravadas"] * ($this->dte["percentaje_descuento_venta_gravada"] / 100), 2);

            $this->dte["descuento_venta_exenta"] = round($this->dte["total_ventas_exentas"] * ($this->dte["percentaje_descuento_venta_exenta"]  / 100), 2);

            $this->dte["descuento_venta_no_sujeta"] = round($this->dte["total_ventas_no_sujetas"] * ($this->dte["percentaje_descuento_venta_no_sujeta"]  / 100), 2);

            $this->dte["total_descuentos"] = round(
                ($this->dte["descuento_venta_gravada"]) +
                    ($this->dte["descuento_venta_exenta"]) +
                    ($this->dte["descuento_venta_no_sujeta"]),
                2
            );
        }
    }

    public function total_retenciones()
    {
        //Total del IVA CCF
        $this->dte["total_ventas_gravadas_descuento"] = round($this->dte["total_ventas_gravadas"] - $this->dte["descuento_venta_gravada"], 2);
        $this->dte["iva"] = round($this->dte["total_ventas_gravadas_descuento"]  * 0.13, 2);

        if ($this->dte["type"] === "03" && $this->dte["total_ventas_gravadas"] > 0) {
            $this->dte["total_pagar"] = round($this->dte["total"] - $this->dte["total_descuentos"] + $this->dte["iva"], 2);
        } else {
            $this->dte["total_pagar"] = round($this->dte["total"] - $this->dte["total_descuentos"], 2);
        }

        if ($this->dte["type"] === "01") {
            $this->dte["total_iva_retenido"] = round(((($this->dte["total_ventas_gravadas"] - ($this->dte["descuento_venta_gravada"] ?? 0)) / 1.13 ?? 0)) * 0.01, 2);
            $this->dte["isr"] = round(((($this->dte["total_ventas_gravadas"] - $this->dte["descuento_venta_gravada"]) / 1.13 ?? 0) + (($this->dte["total_ventas_exentas"] - $this->dte["descuento_venta_exenta"] ?? 0) / 1.13 ?? 0)) * 0.10, 2);
        } else {
            $this->dte["total_iva_retenido"] = round((($this->dte["total_ventas_gravadas"] ?? 0) - ($this->dte["descuento_venta_gravada"] ?? 0)) * 0.01, 2);
            $this->dte["isr"] = round(((($this->dte["total_ventas_gravadas"] ?? 0) - ($this->dte["descuento_venta_gravada"] ?? 0)) + (($this->dte["total_ventas_exentas"] ?? 0) - $this->dte["descuento_venta_exenta"] ?? 0)) * 0.10, 2);
        }

        $this->dte["total_pagar"] = ($this->dte["retener_iva"] ?? "inactive") === "active"
            ? round($this->dte["total_pagar"] - $this->dte["total_iva_retenido"], 2)
            : round($this->dte["total_pagar"], 2);

        $this->dte["total_pagar"] = ($this->dte["retener_renta"] ?? "inactive") === "active"
            ? round($this->dte["total_pagar"] - $this->dte["isr"], 2)
            : round($this->dte["total_pagar"], 2);

        $this->dte["total_pagar"] = ($this->dte["percibir_iva"] ?? "inactive") === "active"
            ? round($this->dte["total_pagar"] + $this->dte["total_iva_retenido"], 2)
            : round($this->dte["total_pagar"], 2);
    }
}
