<?php

namespace App\Http\Controllers\Business;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BusinessProduct;
use Illuminate\Support\Facades\DB;


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
        $customer = session("dte")["customer"] ?? null;
        // Normalizar: convertir strings vacíos en null
        $sucursalId = $request->sucursal_id && $request->sucursal_id !== '' ? $request->sucursal_id : null;
        $posId = $request->pos_id && $request->pos_id !== '' ? $request->pos_id : null;

        if (!$product) {
            return response()->json([
                "success" => false,
                "message" => "Producto no encontrado"
            ]);
        }

        // Obtener usuario actual para determinar restricciones de inventario
        $business_user = \App\Models\BusinessUser::where('business_id', session('business'))
            ->where('user_id', auth()->id())
            ->first();

        // Determinar stock según configuración del usuario y tipo de inventario
        $stockDisponible = null;
        $estadoStock = null;
        $inventorySource = null; // 'global', 'branch', 'pos'
        
        if ($product->has_stock) {
            // Caso 1: Usuario con only_default_pos y default_pos_id donde el POS tiene inventario independiente
            if ($business_user && $business_user->only_default_pos && $business_user->default_pos_id) {
                $defaultPos = $business_user->defaultPos;
                if ($defaultPos && $defaultPos->has_independent_inventory) {
                    // Stock del POS únicamente
                    $posStock = $product->getStockForPos($defaultPos->id);
                    if ($posStock) {
                        $stockDisponible = $posStock->stockActual;
                        $estadoStock = $posStock->estado_stock;
                        $inventorySource = 'pos';
                        $posId = $defaultPos->id;
                    } else {
                        $stockDisponible = 0;
                        $estadoStock = 'agotado';
                        $inventorySource = 'pos';
                    }
                } else {
                    // POS sin inventario independiente, usar sucursal del POS
                    if ($defaultPos && $defaultPos->sucursal_id) {
                        $branchStock = $product->getStockForBranch($defaultPos->sucursal_id);
                        if ($branchStock) {
                            $stockDisponible = $branchStock->stockActual;
                            $estadoStock = $branchStock->estado_stock;
                            $inventorySource = 'branch';
                            $sucursalId = $defaultPos->sucursal_id;
                        } else {
                            $stockDisponible = 0;
                            $estadoStock = 'agotado';
                            $inventorySource = 'branch';
                        }
                    }
                }
            }
            // Caso 2: Usuarios sin default_pos_id o con branch_selector - selección manual
            elseif ($posId || $sucursalId) {
                // Si se proporciona POS ID, verificar si tiene inventario independiente
                if ($posId) {
                    $pos = \App\Models\PuntoVenta::find($posId);
                    if ($pos && $pos->has_independent_inventory) {
                        $posStock = $product->getStockForPos($posId);
                        if ($posStock) {
                            $stockDisponible = $posStock->stockActual;
                            $estadoStock = $posStock->estado_stock;
                            $inventorySource = 'pos';
                        } else {
                            $stockDisponible = 0;
                            $estadoStock = 'agotado';
                            $inventorySource = 'pos';
                        }
                    } else {
                        // POS sin inventario independiente, usar sucursal
                        $sucursalId = $pos ? $pos->sucursal_id : $sucursalId;
                        if ($sucursalId) {
                            $branchStock = $product->getStockForBranch($sucursalId);
                            if ($branchStock) {
                                $stockDisponible = $branchStock->stockActual;
                                $estadoStock = $branchStock->estado_stock;
                                $inventorySource = 'branch';
                            } else {
                                $stockDisponible = 0;
                                $estadoStock = 'agotado';
                                $inventorySource = 'branch';
                            }
                        }
                    }
                }
                // Solo sucursal seleccionada
                elseif ($sucursalId) {
                    $branchStock = $product->getStockForBranch($sucursalId);
                    if ($branchStock) {
                        $stockDisponible = $branchStock->stockActual;
                        $estadoStock = $branchStock->estado_stock;
                        $inventorySource = 'branch';
                    } else {
                        $stockDisponible = 0;
                        $estadoStock = 'agotado';
                        $inventorySource = 'branch';
                    }
                }
            }
            // Producto global (fallback para casos especiales)
            elseif ($product->is_global) {
                $stockDisponible = $product->stockActual;
                $estadoStock = $product->estado_stock;
                $inventorySource = 'global';
            }
            // Fallback: Si no hay POS/sucursal proporcionados pero hay default_pos_id, usar su sucursal
            elseif ($business_user && $business_user->default_pos_id) {
                $defaultPos = $business_user->defaultPos;
                if ($defaultPos && $defaultPos->sucursal_id) {
                    $branchStock = $product->getStockForBranch($defaultPos->sucursal_id);
                    if ($branchStock) {
                        $stockDisponible = $branchStock->stockActual;
                        $estadoStock = $branchStock->estado_stock;
                        $inventorySource = 'branch';
                        $sucursalId = $defaultPos->sucursal_id;
                        $posId = $defaultPos->id;
                    } else {
                        $stockDisponible = 0;
                        $estadoStock = 'agotado';
                        $inventorySource = 'branch';
                        $sucursalId = $defaultPos->sucursal_id;
                    }
                } else {
                    $stockDisponible = 0;
                    $estadoStock = 'agotado';
                    $inventorySource = 'none';
                }
            }
            // Sin contexto de inventario
            else {
                $stockDisponible = 0;
                $estadoStock = 'agotado';
                $inventorySource = 'none';
            }
        }

        // Agregar datos de stock al producto
        $productData = $product->toArray();
        $productData['stockDisponible'] = $stockDisponible;
        $productData['estadoStock'] = $estadoStock;
        $productData['sucursal_id'] = $sucursalId;
        $productData['pos_id'] = $posId;
        $productData['inventory_source'] = $inventorySource;

        // Log temporal para debugging
        \Log::info('DTEProductController::select - Stock Debug', [
            'product_id' => $product->id,
            'has_stock' => $product->has_stock,
            'is_global' => $product->is_global,
            'request_sucursal_id' => $request->sucursal_id,
            'request_pos_id' => $request->pos_id,
            'normalized_sucursal_id' => $sucursalId,
            'normalized_pos_id' => $posId,
            'business_user_id' => $business_user?->id,
            'default_pos_id' => $business_user?->default_pos_id,
            'only_default_pos' => $business_user?->only_default_pos,
            'stockDisponible' => $stockDisponible,
            'estadoStock' => $estadoStock,
            'inventory_source' => $inventorySource
        ]);

        return response()->json([
            "success" => true,
            "product" => $productData,
            "dte" => $dte,
            "customer" => $customer
        ]);
    }

    public function store(Request $request)
    {
        try {
            $this->dte = session("dte", ["products" => []]);
            $customer = session("dte")["customer"] ?? null;

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

            // Obtener usuario para determinar origen de stock
            $business_user = \App\Models\BusinessUser::where('business_id', session('business'))
                ->where('user_id', auth()->id())
                ->first();

            // Determinar stock disponible según contexto del usuario
            $stock = 0;
            $sucursalId = $request->sucursal_id;
            $posId = $request->pos_id;

            if ($business_product->has_stock) {
                // Caso 1: Usuario con only_default_pos y POS con inventario independiente
                if ($business_user && $business_user->only_default_pos && $business_user->default_pos_id) {
                    $defaultPos = $business_user->defaultPos;
                    if ($defaultPos && $defaultPos->has_independent_inventory) {
                        $posStock = $business_product->getStockForPos($defaultPos->id);
                        $stock = $posStock ? (float) $posStock->stockActual : 0;
                        $posId = $defaultPos->id;
                    } else if ($defaultPos && $defaultPos->sucursal_id) {
                        $branchStock = $business_product->getStockForBranch($defaultPos->sucursal_id);
                        $stock = $branchStock ? (float) $branchStock->stockActual : 0;
                        $sucursalId = $defaultPos->sucursal_id;
                    }
                }
                // Caso 2: Usuario con selección manual
                elseif ($posId) {
                    $pos = \App\Models\PuntoVenta::find($posId);
                    if ($pos && $pos->has_independent_inventory) {
                        $posStock = $business_product->getStockForPos($posId);
                        $stock = $posStock ? (float) $posStock->stockActual : 0;
                    } else if ($pos && $pos->sucursal_id) {
                        $branchStock = $business_product->getStockForBranch($pos->sucursal_id);
                        $stock = $branchStock ? (float) $branchStock->stockActual : 0;
                        $sucursalId = $pos->sucursal_id;
                    }
                } elseif ($sucursalId) {
                    $branchStock = $business_product->getStockForBranch($sucursalId);
                    $stock = $branchStock ? (float) $branchStock->stockActual : 0;
                } elseif ($business_product->is_global) {
                    $stock = (float) $business_product->stockActual;
                } else {
                    $stock = 0;
                }
            }

            $found = false;
            $incoming_doc = $request->documento_relacionado ?? '';

            foreach ($this->dte["products"] as &$product) {
                $current_doc = $product["documento_relacionado"] ?? '';

                if (
                    $product["product_id"] == $business_product->id &&
                    $product["tipo"] === $request->tipo &&
                    $current_doc === $incoming_doc
                ) {
                    $found = true;

                    $cantidadActual = (float) $product["cantidad"];
                    $cantidadNueva = (float) $request->cantidad;

                    // No validar stock en facturas de sujeto excluido (tipo 14) ya que son compras
                    if ($business_product->has_stock && $this->dte["type"] !== "14") {
                        if ($cantidadActual + $cantidadNueva > $stock) {
                            return response()->json([
                                "success" => false,
                                "message" => "No hay suficiente stock para el producto, ya se agregó la cantidad máxima."
                            ]);
                        }
                    }

                    $product["cantidad"] += $cantidadNueva;
                    $product["descuento"] += (float) ($request->descuento ?? 0);
                    $product["total"] += (float) $request->total;
                    $product["documento_relacionado"] = $incoming_doc;
                    $product["ventas_gravadas"] += $request->tipo === "Gravada" ? (float) $request->total : 0;
                    $product["ventas_exentas"] += $request->tipo === "Exenta" ? (float) $request->total : 0;
                    $product["ventas_no_sujetas"] += $request->tipo === "No sujeta" ? (float) $request->total : 0;

                    $iva = 0;
                    if ($request->tipo === "Gravada") {
                        if (in_array($this->dte["type"], ["03", "05", "06"])) {
                            $iva = $this->precise_round($product["total"] * 0.13, 8);
                        } else {
                            $iva = $this->precise_round(($product["total"] / 1.13) * 0.13, 8);
                        }
                    }

                    $product["iva"] = $iva;

                    break;
                }
            }

            $iva = 0;
            if ($request->tipo === "Gravada") {
                if ($this->dte["type"] === "03" || $this->dte["type"] === "05" || $this->dte["type"] === "06") {
                    $iva = $this->precise_round((float) $request->total * 0.13, 8);
                } else {
                    $iva = $this->precise_round(((float) $request->total / 1.13) * 0.13, 8);
                }
            }

            $product_tributes = json_decode($business_product->tributos, true) ?? [];

            if (!$found) {
                // Validar stock antes de agregar producto nuevo (no es sujeto excluido)
                if ($business_product->has_stock && $this->dte["type"] !== "14") {
                    $cantidad = (float) $request->cantidad;
                    if ($cantidad > $stock) {
                        return response()->json([
                            "success" => false,
                            "message" => "No hay suficiente stock disponible. Stock actual: " . $stock
                        ]);
                    }
                }

                if ($customer && isset($customer["special_price"]) && $customer["special_price"]) {
                    $precio = (float) $business_product->special_price_with_iva;
                    $precio_sin_tributos = (float) $business_product->special_price;
                } else {
                    $precio = (float) $business_product->precioUni;
                    $precio_sin_tributos = (float) $business_product->precioSinTributos;
                }
                $cantidad = (float) $request->cantidad;
                $total = (float) $request->total;
                $descuento = (float) ($request->descuento ?? 0);

                $this->dte["remove_discounts"] = in_array("59", $product_tributes) || in_array("71", $product_tributes) || in_array("D1", $product_tributes) || in_array("C8", $product_tributes) || in_array("C5", $product_tributes) || in_array("C6", $product_tributes) || in_array("C7", $product_tributes);

                $this->dte["products"][] = [
                    "id" => rand(1, 1000),
                    "product" => $business_product->toArray(),
                    "product_id" => $business_product->id,
                    "codigo" => $business_product->codigo,
                    "unidad_medida" => $business_product->uniMedida,
                    "descripcion" => $business_product->descripcion,
                    "cantidad" => $cantidad,
                    "tipo" => $request->tipo,
                    "precio" => ($this->dte["type"] === "14") ? $precio_sin_tributos : ($request->tipo == "Gravada" ? $precio : $precio_sin_tributos),
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
                    "tipo_item" => $business_product->tipoItem,
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
                // "table_products" => "",
                "table_products" => $this->dte["type"] != "11" && $this->dte["type"] != "14" ? view("layouts.partials.ajax.business.table-products-dte", [
                    "dte" => $this->dte
                ])->render() : null,
                "total_pagar" => $this->dte["total_pagar"],
                "monto_pendiente" => $this->dte["monto_pendiente"],
                // "table_exportacion" => "",
                "table_exportacion" => $this->dte["type"] == "11" ? view("layouts.partials.ajax.business.table-exportacion", [
                    "dte" => $this->dte
                ])->render() : null,
                "table_sujeto_excluido" => $this->dte["type"] == "14" ? view("layouts.partials.ajax.business.table-sujeto-excluido", [
                    "dte" => $this->dte
                ])->render() : null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Ha ocurrido un error al guardar el producto: " . $e->getMessage() . " ". $e->getLine(),
            ]);
        }
    }

    public function store_new(Request $request)
    {
        try {
            if (in_array($this->dte["type"], ["05", "06"])) {
                if ($request->documento_relacionado === "" || $request->documento_relacionado === null) {
                    return response()->json([
                        "success" => false,
                        "message" => "Debe seleccionar un documento relacionado"
                    ]);
                }
            }
            $product_tributes = $request->tributos ?? [];
            $total = floatval($request->total);
            $iva = 0;
            if ($request->tipo === "Gravada") {
                $iva = ($this->dte["type"] === "03")
                    ? $this->precise_round($total * 0.13, 8)
                    : $this->precise_round(($total / 1.13) * 0.13, 8);
            }

            $this->dte["products"][] = [
                "id" => rand(1, 1000),
                "product" => null,
                "product_id" => null,
                "codigo" => null,
                "unidad_medida" => $request->unidad_medida,
                "descripcion" => $request->descripcion,
                "cantidad" => $request->cantidad,
                "tipo" => $request->tipo,
                "precio" => $request->precio_unitario,
                "precio_sin_tributos" => $request->precio_unitario,
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
                "tipo_item" => $request->tipo_item,
                "iva" => $iva,
                "documento_relacionado" => $request->documento_relacionado ?? null,
                "tributos" => json_encode($product_tributes)
            ];
            $this->dte["remove_discounts"] = in_array("59", $product_tributes) || in_array("71", $product_tributes) || in_array("D1", $product_tributes) || in_array("C8", $product_tributes) || in_array("C5", $product_tributes) || in_array("C6", $product_tributes) || in_array("C7", $product_tributes);
            session(["dte" => $this->dte]);
            $this->totals();

            return response()->json([
                "success" => true,
                "message" => "Producto guardado correctamente",
                "table_data" => $this->dte["type"] != "11" && $this->dte["type"] != "14" ? view("layouts.partials.ajax.business.table-products-dte", [
                    "dte" => $this->dte
                ])->render() : null,
                "table" => "products-dte",
                "drawer" => "drawer-new-product",
                "total_pagar" => $this->dte["total_pagar"],
                "monto_pendiente" => $this->dte["monto_pendiente"],
                "table_exportacion" => $this->dte["type"] == "11" ? view("layouts.partials.ajax.business.table-exportacion", [
                    "dte" => $this->dte
                ])->render() : null,
                "table_sujeto_excluido" => $this->dte["type"] == "14" ? view("layouts.partials.ajax.business.table-sujeto-excluido", [
                    "dte" => $this->dte
                ])->render() : null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Ha ocurrido un error al guardar el producto: " . $e->getMessage(),
            ]);
        }
    }

    public function store_from_pos(Request $request)
    {
        try {
            $this->dte = session("dte", ["products" => []]);
            $total = 0;

            if (!isset($this->dte["products"]) || !is_array($this->dte["products"])) {
                $this->dte["products"] = [];
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
                if ($product["product_id"] == $business_product->id) {
                    $found = true;

                    $cantidadActual = (float) $product["cantidad"];
                    $cantidadNueva = (float) $request->cantidad;

                    if ($business_product->has_stock) {
                        if ($cantidadActual + $cantidadNueva > $stock) {
                            return response()->json([
                                "success" => false,
                                "message" => "No hay suficiente stock para el producto, ya se agregó la cantidad máxima."
                            ]);
                        }
                    }

                    $product["cantidad"] += $cantidadNueva;
                    $product["descuento"] += 0;
                    $product["total"] = $business_product->precioUni * $product["cantidad"];
                    $product["documento_relacionado"] = null;
                    $product["ventas_gravadas"] = $product["total"];
                    $product["ventas_exentas"] = 0;
                    $product["ventas_no_sujetas"] = 0;
                    $product["iva"] = $this->precise_round(($product["total"] / 1.13) * 0.13, 8);
                    break;
                }
            }

            $product_tributes = json_decode($business_product->tributos, true);
            if (!$found) {
                $precio = (float) $business_product->precioUni;
                $precio_sin_tributos = (float) $business_product->precioSinTributos;
                $cantidad = (float) $request->cantidad;
                $total = $business_product->precioUni * $cantidad;
                $iva = $this->precise_round(((float) $total / 1.13) * 0.13, 8);
                $descuento = 0;


                $this->dte["remove_discounts"] = in_array("59", $product_tributes) || in_array("71", $product_tributes) || in_array("D1", $product_tributes) || in_array("C8", $product_tributes) || in_array("C5", $product_tributes) || in_array("C6", $product_tributes) || in_array("C7", $product_tributes);

                $this->dte["products"][] = [
                    "id" => rand(1, 1000),
                    "product" => $business_product->toArray(),
                    "product_id" => $business_product->id,
                    "codigo" => $business_product->codigo,
                    "unidad_medida" => $business_product->uniMedida,
                    "descripcion" => $business_product->descripcion,
                    "cantidad" => $cantidad,
                    "tipo" => "Gravada",
                    "precio" => $precio,
                    "precio_sin_tributos" => $precio_sin_tributos,
                    "descuento" => $descuento,
                    "ventas_gravadas" => $total,
                    "ventas_exentas" => 0,
                    "ventas_no_sujetas" => 0,
                    "total" => $total,
                    "turismo_por_alojamiento" => in_array("59", $product_tributes) ? "active" : "inactive",
                    "turismo_salida_pais_via_aerea" => in_array("71", $product_tributes) ? "active" : "inactive",
                    "fovial" => in_array("D1", $product_tributes) ? "active" : "inactive",
                    "contrans" => in_array("C8", $product_tributes) ? "active" : "inactive",
                    "bebidas_alcoholicas" => in_array("C5", $product_tributes) ? "active" : "inactive",
                    "tabaco_cigarillos" => in_array("C6", $product_tributes) ? "active" : "inactive",
                    "tabaco_cigarros" => in_array("C7", $product_tributes) ? "active" : "inactive",
                    "iva" => $iva,
                    "documento_relacionado" => null
                ];
            }

            $this->totals();

            $this->dte["metodos_pago"][0] = [
                "id" => rand(1, 1000),
                "forma_pago" => "01",
                "monto" => array_sum(array_column($this->dte["products"], "total")),
                "numero_documento" => null,
                "plazo" => null,
                "periodo" => null,
            ];

            $this->dte["monto_abonado"] = array_sum(array_column($this->dte["products"], "total"));
            $this->dte["monto_pendiente"] = $this->dte["total_pagar"] - $this->dte["monto_abonado"];
            $this->totals();


            // Guardar en sesión
            session(["dte" => $this->dte]);
            // Retornar respuesta
            return redirect()->back()->with([
                "success" => true
            ]);
        } catch (\Exception $e) {
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
                "product_id" => null,
                "codigo" => null,
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
                "product_id" => null,
                "codigo" => null,
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

    public function delete(string $id, Request $request)
    {
        $from_pos = $request->input("from_pos", false);
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

            if ($from_pos) {
                if (isset($this->dte["metodos_pago"]) && count($this->dte["metodos_pago"]) > 0) {
                    unset($this->dte["metodos_pago"]);
                    $this->dte["metodos_pago"][] = [
                        "id" => rand(1, 1000),
                        "forma_pago" => "01",
                        "monto" => array_sum(array_column($this->dte["products"], "total")),
                        "numero_documento" => null,
                        "plazo" => null,
                        "periodo" => null,
                    ];
                }

                $this->dte["monto_abonado"] = array_sum(array_column($this->dte["products"], "total"));
                $this->dte["monto_pendiente"] = $this->dte["total_pagar"] - $this->dte["monto_abonado"];
                $this->totals();
            }

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
                "table_sujeto_excluido" => $this->dte["type"] == "14" ? view("layouts.partials.ajax.business.table-sujeto-excluido", [
                    "dte" => $this->dte
                ])->render() : null,
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

        if ($this->dte["type"] !== "14" && $this->dte["type"] !== "11" && $this->dte["type"] !== "07" && $this->dte["type"] !== "01") {
            $this->dte["total"] = $this->dte["subtotal"] + $this->dte["total_taxes"];
        } else {
            $this->dte["total"] = $this->dte["subtotal"];
        }

        $this->dte["total_pagar"] = $this->precise_round($this->dte["total"] - $this->dte["total_descuentos"], 8);

        $this->total_retenciones();

        if ($this->dte["type"] === "14") {
            $this->dte["total_pagar"] -= $this->dte["isr"];
        }

        if ($this->dte["type"] === "11") {
            $this->dte["total_pagar"] = $this->precise_round((float) $this->dte["total_ventas_gravadas"] + $this->dte["flete"] + $this->dte["seguro"], 8);
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
        $this->dte["total_ventas_gravadas"] = $this->precise_round((float) array_sum(array_map(fn($product) => $product["ventas_gravadas"] ?? 0, $this->dte["products"] ?? [])), 8);

        $this->dte["total_ventas_exentas"] = $this->precise_round((float) array_sum(array_map(fn($product) => $product["ventas_exentas"] ?? 0, $this->dte["products"] ?? [])), 8);

        $this->dte["total_ventas_no_sujetas"] = $this->precise_round((float) array_sum(array_map(fn($product) => $product["ventas_no_sujetas"] ?? 0, $this->dte["products"] ?? [])), 8);
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
        $this->dte["total_ventas_gravadas_descuento"] = $this->precise_round($this->dte["total_ventas_gravadas"] - $this->dte["descuento_venta_gravada"], 8);
        $this->dte["iva"] = $this->precise_round($this->dte["total_ventas_gravadas_descuento"] * 0.13, 8);

        if (isset($this->dte["products"]) && count($this->dte["products"]) > 0) {
            foreach ($this->dte["products"] as $product) {
                $total = $this->precise_round(($product["total"] ?? 0), 8);
                $cantidad = $this->precise_round(($product["cantidad"] ?? 0), 8);

                if (!empty($product["turismo_por_alojamiento"]) && $product["turismo_por_alojamiento"] === "active") {
                    $this->dte["turismo_por_alojamiento"] += $this->precise_round(($total / $cantidad) * 0.05, 8) * $cantidad;
                }

                if (!empty($product["turismo_salida_pais_via_aerea"]) && $product["turismo_salida_pais_via_aerea"] === "active") {
                    $this->dte["turismo_salida_pais_via_aerea"] += $this->precise_round($cantidad * 7, 8);
                }

                if (!empty($product["fovial"]) && $product["fovial"] === "active") {
                    $this->dte["fovial"] += $this->precise_round($cantidad * 0.20, 8);
                }

                if (!empty($product["contrans"]) && $product["contrans"] === "active") {
                    $this->dte["contrans"] += $this->precise_round($cantidad * 0.10, 8);
                }

                if (!empty($product["bebidas_alcoholicas"]) && $product["bebidas_alcoholicas"] === "active") {
                    $this->dte["bebidas_alcoholicas"] += $this->precise_round(($total / $cantidad) * 0.08, 8) * $cantidad;
                }

                if (!empty($product["tabaco_cigarillos"]) && $product["tabaco_cigarillos"] === "active") {
                    $this->dte["tabaco_cigarillos"] += $this->precise_round(($total / $cantidad) * 0.39, 8) * $cantidad;
                }

                if (!empty($product["tabaco_cigarros"]) && $product["tabaco_cigarros"] === "active") {
                    $this->dte["tabaco_cigarros"] += $this->precise_round(($total / $cantidad) * 1, 8) * $cantidad;
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
            $this->dte["tabaco_cigarros"] +
            $this->dte["iva"];

        $this->dte["total_taxes"] = $this->precise_round((float) $this->dte["total_taxes"], 8);
    }

    public function total_descuentos()
    {
        if ($this->dte["remove_discounts"]) {
            $this->dte["descuento_venta_gravada"] = 0;
            $this->dte["descuento_venta_exenta"] = 0;
            $this->dte["descuento_venta_no_sujeta"] = 0;
            $this->dte["total_descuentos"] = 0;
        } else {
            $this->dte["descuento_venta_gravada"] = $this->precise_round($this->dte["total_ventas_gravadas"] * ($this->dte["percentaje_descuento_venta_gravada"] / 100), 8);

            $this->dte["descuento_venta_exenta"] = $this->precise_round($this->dte["total_ventas_exentas"] * ($this->dte["percentaje_descuento_venta_exenta"] / 100), 8);

            $this->dte["descuento_venta_no_sujeta"] = $this->precise_round($this->dte["total_ventas_no_sujetas"] * ($this->dte["percentaje_descuento_venta_no_sujeta"] / 100), 8);

            $this->dte["total_descuentos"] = $this->precise_round(
                ($this->dte["descuento_venta_gravada"]) +
                ($this->dte["descuento_venta_exenta"]) +
                ($this->dte["descuento_venta_no_sujeta"]),
                8
            );
        }
    }

    public function total_retenciones()
    {

        $this->dte["total_pagar"] = $this->precise_round($this->dte["total"] - $this->dte["total_descuentos"], 8);

        if ($this->dte["type"] !== "14") {
            if ($this->dte["type"] == "01") {
                $this->dte["total_iva_retenido"] = $this->precise_round((($this->dte["total_ventas_gravadas"] - ($this->dte["descuento_venta_gravada"] ?? 0)) / 1.13 ?? 0) * 0.01, 8);
                $total_servicios = array_sum(
                    array_map(
                        function ($product) {
                            $tipo_item = ($product["product"] && is_array($product["product"])) ? $product["product"]["tipoItem"] ?? null : $product["tipo_item"] ?? null;
                            if ($tipo_item == 2) {
                                return $product["ventas_gravadas"] ?? 0;
                            }
                            return 0;
                        },
                        $this->dte["products"] ?? []
                    )
                );
                $this->dte["isr"] = $this->precise_round(($total_servicios / 1.13) * 0.10, 8);
            } else {
                $this->dte["total_iva_retenido"] = $this->precise_round((($this->dte["total_ventas_gravadas"] - ($this->dte["descuento_venta_gravada"] ?? 0)) ?? 0) * 0.01, 8);
                $total_servicios = array_sum(
                    array_map(
                        function ($product) {
                            $tipo_item = ($product["product"] && is_array($product["product"])) ? $product["product"]["tipoItem"] ?? null : $product["tipo_item"] ?? null;
                            if ($tipo_item == 2) {
                                return $product["ventas_gravadas"] ?? 0;
                            }
                            return 0;
                        },
                        $this->dte["products"] ?? []
                    )
                );
                $this->dte["isr"] = $this->precise_round($total_servicios * 0.10, 8);
            }

        } else if ($this->dte["type"] === "14") {
            $total_servicios = array_sum(
                array_map(
                    function ($product) {
                        $tipo_item = ($product["product"] && is_array($product["product"])) ? $product["product"]["tipoItem"] ?? null : $product["tipo_item"] ?? null;
                        if ($tipo_item == 2) {
                            return $product["total"] ?? 0;
                        }
                        return 0;
                    },
                    $this->dte["products"] ?? []
                )
            );
            $total_bienes = array_sum(
                array_map(
                    function ($product) {
                        $tipo_item = ($product["product"] && is_array($product["product"])) ? $product["product"]["tipoItem"] ?? null : $product["tipo_item"] ?? null;
                        if ($tipo_item == 1) {
                            return $product["total"] ?? 0;
                        }
                        return 0;
                    },
                    $this->dte["products"] ?? []
                )
            );
            $this->dte["total_iva_retenido"] = $this->precise_round(($total_bienes ?? 0) * 0.01, 8);
            $this->dte["isr"] = $this->precise_round(($total_servicios ?? 0) * 0.10, 8);
        } else {
            $this->dte["total_iva_retenido"] = $this->precise_round((($this->dte["total_ventas_gravadas"] ?? 0) - ($this->dte["descuento_venta_gravada"] ?? 0)) * 0.01, 8);

            $total_servicios = array_sum(
                array_map(
                    function ($product) {
                        $tipo_item = ($product["product"] && is_array($product["product"])) ? $product["product"]["tipoItem"] ?? null : $product["tipo_item"] ?? null;
                        if ($tipo_item == 2) {
                            return $product["ventas_gravadas"] ?? 0;
                        }
                        return 0;
                    },
                    $this->dte["products"] ?? []
                )
            );
            $this->dte["isr"] = $this->precise_round($total_servicios * 0.10, 8);
        }


        $this->dte["total_pagar"] = ($this->dte["retener_iva"] ?? "inactive") === "active"
            ? $this->precise_round($this->dte["total_pagar"] - $this->dte["total_iva_retenido"], 8)
            : $this->precise_round($this->dte["total_pagar"], 8);

        $this->dte["total_pagar"] = ($this->dte["retener_renta"] ?? "inactive") === "active"
            ? $this->precise_round($this->dte["total_pagar"] - $this->dte["isr"], 8)
            : $this->precise_round($this->dte["total_pagar"], 8);

        $this->dte["total_pagar"] = ($this->dte["percibir_iva"] ?? "inactive") === "active"
            ? $this->precise_round($this->dte["total_pagar"] + $this->dte["total_iva_retenido"], 8)
            : $this->precise_round($this->dte["total_pagar"], 8);
    }

    private function precise_round($value, $precision = 2)
    {
        $factor = pow(10, $precision);
        return round(($value + PHP_FLOAT_EPSILON) * $factor) / $factor;
    }
}
