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

    $("#price-not-iva").on("input", function () {
        $("#price-with-iva").val(
            (
                parseFloat($(this).val()) +
                parseFloat($(this).val()) * 0.13
            ).toFixed(8)
        );
    });

    $("#price-with-iva").on("input", function () {
        $("#price-not-iva").val(
            (
                parseFloat($(this).val())/(1.13)
            ).toFixed(8)
        );
    });

    if($("#price-not-iva").length > 0){
          $("#price-with-iva").val(
              (
                  parseFloat($("#price-not-iva").val()) +
                  parseFloat($("#price-not-iva").val()) * 0.13
              ).toFixed(8)
          );
    }

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
