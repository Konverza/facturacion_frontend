$(document).ready(function () {
    const $departamento = $("#departamento");

    /*     if ($departamento.length > 0) {
        const action = $departamento.data("action");
        const codigo = $departamento.val();
        handleAjax(action, codigo);
    } */

    $departamento.on("Changed", function () {
        const action = $(this).data("action");
        const codigo = $(this).val();
        handleAjax(action, codigo);
    });

    function handleAjax(action, codigo) {
        $.ajax({
            url: action,
            type: "GET",
            data: {
                codigo: codigo,
                municipio: $("#municipio").val() ? $("#municipio").val() : null,
            },
            success: function (response) {
                $("#select-municipio").html(response.html);
                $("#edit-municipio").html(response.html);
            },
            error: function () {
                console.log("Error");
            },
        });
    }

    //Actions
    $(".btn-edit").on("click", function () {
        const url = $(this).data("url");
        const action = $(this).data("action");
        const type = $(this).data("type");
        $.ajax({
            url: url,
            type: "GET",
            success: function (response) {
                if (type === "plans") {
                    $("#edit-plan").removeClass("hidden").addClass("flex");
                    $("body").addClass("overflow-hidden");
                    $("#form-edit-plan").attr("action", action);
                    $("#form-edit-plan #name").val(response.nombre);
                    $("#form-edit-plan #limit").val(response.limite);
                    $("#form-edit-plan #price").val(response.precio);
                    $("#form-edit-plan #price_aditional").val(
                        response.precio_adicional
                    );
                } else if (type === "users") {
                    $("#edit-user").removeClass("hidden").addClass("flex");
                    $("body").addClass("overflow-hidden");
                    $("#form-edit-user").attr("action", action);
                    $("#form-edit-user #name").val(response.name);
                    $("#form-edit-user #email").val(response.email);
                    $("#" + response.role).prop("checked", true);
                } else if (type === "sucursales") {
                    $("#edit-sucursal").removeClass("hidden").addClass("flex");
                    $("body").addClass("overflow-hidden");
                    $("#form-edit-sucursal").attr("action", action);
                    $("#form-edit-sucursal #nombre").val(response.nombre);
                    $("#form-edit-sucursal #departamento").val(
                        response.departamento
                    );
                    $("#form-edit-sucursal #municipio").val(response.municipio);
                    $("#form-edit-sucursal #complemento").val(response.complemento);
                    $("#form-edit-sucursal #telefono").val(response.telefono);
                    $("#form-edit-sucursal #correo").val(response.correo);
                    $("#form-edit-sucursal #codSucursal").val(response.codSucursal);
                } else if (type === "puntos_venta") {
                    $("#edit-punto-venta").removeClass("hidden").addClass("flex");
                    $("body").addClass("overflow-hidden");
                    $("#form-edit-punto-venta").attr("action", action);
                    $("#form-edit-punto-venta #nombre").val(response.nombre);
                    $("#form-edit-punto-venta #codPuntoVenta").val(
                        response.codPuntoVenta
                    );
                    // Manejar el checkbox de inventario independiente
                    if (response.has_independent_inventory) {
                        $("#form-edit-punto-venta #has_independent_inventory").prop("checked", true);
                    } else {
                        $("#form-edit-punto-venta #has_independent_inventory").prop("checked", false);
                    }
                }
            },
        });
    });

    function validatePasswordMatch(
        passwordSelector,
        confirmPasswordSelector,
        errorSelector
    ) {
        $(confirmPasswordSelector).on("input", function () {
            const password = $(passwordSelector).val();
            const confirmPassword = $(this).val();
            const result = checkPasswordMatch(password, confirmPassword);

            $(this).toggleClass("is-invalid", !result);
            $(errorSelector).toggleClass("hidden", result);
        });
    }

    validatePasswordMatch(
        "#password-new",
        "#confirm-password-new",
        "#error-password-new"
    );
    validatePasswordMatch("#password", "#confirm-password", "#error-password");

    function checkPasswordMatch(value, confirmValue) {
        if (value !== confirmValue) {
            return false;
        }
        return true;
    }

    $("#change-password").on("click", function () {
        if ($(this).is(":checked")) {
            $("#change-password-container").removeClass("hidden");
        } else {
            $("#change-password-container").addClass("hidden");
        }
    });

    //Cuentas por cobrar
    $(document).on("click", ".btn-add-pay", function () {
        const id = $(this).data("id");
        const monto = $(this).data("amount");
        $("#cuenta-id").val(id);
        $("#monto").attr("max", monto).val(monto);
    });

    $("#tipo").on("Changed", function () {
        const value = $(this).val();
        if (value === "ajuste") {
            $("#numero-factura-container").addClass("hidden");
        } else {
            $("#numero-factura-container").removeClass("hidden");
        }
    });

    $(document).on("click", ".btn-show-history", function () {
        const url = $(this).data("url");
        $.ajax({
            url: url,
            type: "GET",
            beforeSend: function () {
                $("#loader").removeClass("hidden");
                $("body").addClass("overflow-hidden");
            },
            success: function (response) {
                $("#drawer-history").removeClass("translate-x-full");
                $("#overlay").removeClass("hidden");
                $("#history").html(response.html);
            },
            complete: function () {
                $("#loader").addClass("hidden");
            },
            error: function () {
                console.log("Error");
            },
        });
    });

    // Products
    $("#has_stock").on("change", function () {
        if ($(this).is(":checked")) {
            $("#stock_inicial").prop("disabled", false);
            $("#stock_minimo").prop("disabled", false);
            $("#stock_inicial").val(0);
            $("#stock_minimo").val(0);
            $("#stocks").removeClass("hidden");
        } else {
            $("#stock_inicial").prop("disabled", true);
            $("#stock_minimo").prop("disabled", true);
            $("#stock_inicial").val(0);
            $("#stock_minimo").val(0);
            $("#stocks").addClass("hidden");
        }
    });

    // Función para calcular todos los valores basados en costo, margen y descuento
    function calculateFromCostMarginDiscount() {
        let cost = parseFloat($("#cost").val()) || 0;
        let margin = parseFloat($("#margin").val()) || 0;
        let discount = parseFloat($("#discount").val()) || 0;

        let price = cost + (cost * margin / 100);
        let specialPrice = price - (price * discount / 100);

        $("#price-not-iva").val(price.toFixed(8));
        $("#price-with-iva").val((price * 1.13).toFixed(8));
        $("#special_price").val(specialPrice.toFixed(8));
        $("#special_price_with_iva").val((specialPrice * 1.13).toFixed(8));
    }

    // Función para calcular el descuento basado en el precio especial
    function calculateDiscountFromSpecialPrice() {
        let priceNotIva = parseFloat($("#price-not-iva").val()) || 0;
        let specialPrice = parseFloat($("#special_price").val()) || 0;

        if (priceNotIva > 0) {
            let discount = ((priceNotIva - specialPrice) / priceNotIva) * 100;
            $("#discount").val(discount.toFixed(8));
        }
    }

    // Función para calcular el margen basado en el precio normal sin IVA
    function calculateMarginFromNormalPrice() {
        let cost = parseFloat($("#cost").val()) || 0;
        let priceNotIva = parseFloat($("#price-not-iva").val()) || 0;

        if (cost > 0) {
            let margin = ((priceNotIva - cost) / cost) * 100;
            $("#margin").val(margin.toFixed(8));
        }
    }

    // Conversión entre precio sin IVA y con IVA (normal)
    $("#price-not-iva").on("input", function () {
        let priceNotIva = parseFloat($(this).val()) || 0;
        $("#price-with-iva").val((priceNotIva * 1.13).toFixed(8));
        calculateMarginFromNormalPrice(); // Nueva línea para calcular margen
        calculateDiscountFromSpecialPrice();
    });

    $("#price-with-iva").on("input", function () {
        let priceWithIva = parseFloat($(this).val()) || 0;
        let priceNotIva = priceWithIva / 1.13;
        $("#price-not-iva").val(priceNotIva.toFixed(8));
        calculateMarginFromNormalPrice(); // Nueva línea para calcular margen
        calculateDiscountFromSpecialPrice();
    });

    // Conversión entre precio especial sin IVA y con IVA
    $("#special_price").on("input", function () {
        let specialPrice = parseFloat($(this).val()) || 0;
        $("#special_price_with_iva").val((specialPrice * 1.13).toFixed(8));
        calculateDiscountFromSpecialPrice();
    });

    $("#special_price_with_iva").on("input", function () {
        let specialPriceWithIva = parseFloat($(this).val()) || 0;
        let specialPrice = specialPriceWithIva / 1.13;
        $("#special_price").val(specialPrice.toFixed(8));
        calculateDiscountFromSpecialPrice();
    });

    function syncVariantPrice($input, mode) {
        if ($input.data("syncing")) {
            return;
        }

        const name = $input.attr("name") || "";
        const match = name.match(/price_variants\[(\d+)\]\[(price_without_iva|price_with_iva)\]/);
        if (!match) {
            return;
        }

        const variantId = match[1];
        const withoutSelector = `input[name="price_variants[${variantId}][price_without_iva]"]`;
        const withSelector = `input[name="price_variants[${variantId}][price_with_iva]"]`;

        let value = parseFloat($input.val());
        if (isNaN(value)) {
            return;
        }

        const $target = mode === "without" ? $(withSelector) : $(withoutSelector);
        if ($target.length === 0) {
            return;
        }

        $target.data("syncing", true);
        if (mode === "without") {
            $target.val((value * 1.13).toFixed(8));
        } else {
            $target.val((value / 1.13).toFixed(8));
        }
        $target.data("syncing", false);
    }

    $(document).on("input", "input[name^='price_variants'][name$='[price_without_iva]']", function () {
        syncVariantPrice($(this), "without");
    });

    $(document).on("input", "input[name^='price_variants'][name$='[price_with_iva]']", function () {
        syncVariantPrice($(this), "with");
    });

    // Cálculos cuando cambian costo, margen o descuento
    $("#cost, #margin, #discount").on("input", function () {
        calculateFromCostMarginDiscount();
    });

    // Cálculos iniciales
    if ($("#price-not-iva").length > 0 && $("#price-not-iva").val()) {
        let priceNotIva = parseFloat($("#price-not-iva").val());
        $("#price-with-iva").val((priceNotIva * 1.13).toFixed(8));
        calculateMarginFromNormalPrice(); // Nueva línea para cálculo inicial
    }

    if ($("#special_price").length > 0 && $("#special_price").val()) {
        $("#special_price_with_iva").val((parseFloat($("#special_price").val()) * 1.13).toFixed(8));
    }

    // Si se modifica el precio normal (sin IVA o con IVA), actualizar margen y descuento si hay precio especial
    $("#price-not-iva, #price-with-iva").on("input", function () {
        calculateMarginFromNormalPrice();
        if ($("#special_price").val() || $("#special_price_with_iva").val()) {
            calculateDiscountFromSpecialPrice();
        }
    });

    //Profile
    $("#logo").on("change", function () {
        const file = $(this)[0].files[0];
        const reader = new FileReader();
        reader.onload = function (e) {
            $("#logo-preview").attr("src", e.target.result);
        };
        reader.readAsDataURL(file);
    });

    $(document).on("click", ".btn-add-stock", function () {
        const id = $(this).data("id");
        $("#product-id").val(id);
    });

    $(document).on("click", ".btn-remove-stock", function () {
        const id = $(this).data("id");
        $("#product-remove-id").val(id);
    });
});
