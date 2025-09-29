import { showAlert } from "./alert";

$(document).ready(function () {
    let intervalId;

    function startClock() {
        intervalId = setInterval(() => {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, "0");
            const minutes = String(now.getMinutes()).padStart(2, "0");
            const seconds = String(now.getSeconds()).padStart(2, "0");
            $("#time-in-real-time").val(`${hours}:${minutes}:${seconds}`);
        }, 1000)
    }

    startClock();

    $(document).on("change", "#dte-otra-fecha", function () {
        if (this.checked) {
            Swal.fire({
                title: "¿Desea cambiar la fecha y hora del DTE?",
                text: "Tome en cuenta que enviar una fecha distinta a la actual puede ser observado por el Ministerio de Hacienda, ¿Desea continuar?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, Cambiar",
                cancelButtonText: "No"
            }).then((result) => {
                if (result.isConfirmed) {
                    clearInterval(intervalId); // Detener el intervalo
                } else {
                    $('#dte-otra-fecha').prop('checked', false);
                }
            });
        } else {
            startClock(); // Reiniciar el intervalo
        }
    });

    //Drawer new product
    const count = $("#count_product");
    const price = $("#price");

    $("#count_product, #price").on("input", function () {
        const countValue = parseFloat(count.val());
        const priceValue = parseFloat(price.val());
        if (count.val() === "" || price.val() === "") {
            $("#iva").text("$0.00");
            $("#turismo").text("$0.00");
            $("#add-valorem-bebidas-alcoholicas").text("$0.00");
            $("#add-valorem-tabaco-cigarrillos").text("$0.00");
            $("#add-valorem-tabaco-cigarros").text("$0.00");
            $("#total_product").val(0);
            return;
        }
        var total = countValue * priceValue;
        const descuento = parseFloat($("#descuento_product").val()) || 0;
        total = total - descuento;

        if (total < 0) {
            $("#descuento_product").val(0);
            $("#total_product").val(
                redondear(parseFloat(countValue * priceValue), 8)
            );
            showAlert(
                "error",
                "Error",
                "El descuento no puede ser mayor al total"
            );
            return;
        }

        $("#total_product").val(redondear(total, 8));
        $("#descuento_product").prop("max", redondear(total, 8));
        updatePrices(priceValue, countValue);
    });

    $("#descuento_product").on("input", function () {
        const descuento = parseFloat($(this).val()) || 0;
        const total = parseFloat($(this).prop("max")) || 0;

        if (total === 0) {
            showAlert("error", "Error", "Debes ingresar un precio y cantidad");
            $(this).val("");
            return;
        }

        if (descuento > total) {
            showAlert(
                "error",
                "Error",
                "El descuento no puede ser mayor al total"
            );
            $(this).val("");
            return;
        }

        const total_descuento = total - descuento;
        $("#total_product").val(redondear(total_descuento, 8));
    });

    // Drawer new donación
    $("#count_product, #valor_unitario, #depreciacion").on("input", function () {
        const count = parseFloat($("#count_product").val()) || 0;
        const valorUnitario = parseFloat($("#valor_unitario").val()) || 0;
        const depreciacion = parseFloat($("#depreciacion").val()) || 0;

        const total = (count * valorUnitario) - depreciacion;
        $("#valor_donado").val(redondear(total, 8));
    });

    function updatePrices(price, count) {
        const iva = count * (price / 1.13) * 0.13;
        const turismo = count * (price / 1.13) * 0.05;
        const add_valorem_bebidas_alcoholicas = count * (price / 1.13) * 0.08;
        const add_valorem_tabaco_cigarrillos = count * (price / 1.13) * 0.39;
        const add_valorem_tabaco_cigarros = count * (price / 1.13) * 1;
        $("#iva").text("$" + redondear(iva, 2));
        $("#turismo").text("$" + redondear(turismo, 2));
        $("#add-valorem-bebidas-alcoholicas").text(
            "$" + redondear(add_valorem_bebidas_alcoholicas, 2)
        );
        $("#add-valorem-tabaco-cigarrillos").text(
            "$" + redondear(add_valorem_tabaco_cigarrillos, 2)
        );
        $("#add-valorem-tabaco-cigarros").text(
            "$" + redondear(add_valorem_tabaco_cigarros, 2)
        );
    }

    //Toogle show tax
    $(".tax-toggle").on("change", function () {
        const list = $($(this).data("list"));
        if ($(this).is(":checked")) {
            list.removeClass("hidden");
        } else {
            list.addClass("hidden");
        }
    });

    //Obtener el cliente seleccionado
    $(document).on("click", ".selected-customer", function () {
        const url = $(this).data("url");
        $("#selected-customer").addClass("hidden").removeClass("flex");
        $("#loader").removeClass("hidden");
        $("body").addClass("overflow-hidden");

        axios
            .get(url)
            .then((response) => {
                const data = response.data;

                $("#numero_documento_customer").val(
                    data.customer.numDocumento ?? ""
                );

                //Search DTes automatic
                $("#nit-receptor")
                    .val(data.customer.numDocumento ?? "")
                    .trigger("input");
                Livewire.dispatch('refreshNumeroDocumento', {nuevoNumeroDocumento: data.customer.numDocumento ?? ""});

                $("#nombre_customer").val(data.customer.nombre);
                $("#nombre_comercial_customer").val(
                    data.customer.nombreComercial
                );

                $("#actividad_economica_customer").val(
                    data.customer.actividadEconomica
                );
                $("#complemento_customer").val(data.customer.complemento);
                $("#correo_customer").val(data.customer.correo);
                $("#telefono_customer").val(data.customer.telefono);
                $("#nrc_customer").val(data.customer.nrc);

                // Actualizar selects dinámicos
                $("#select-tipos-documentos").html(
                    data.select_tipos_documentos
                );
                $("#select-actividad-economica").html(
                    data.select_actividad_economica
                );
                $("#select-departamentos").html(data.select_departamentos);
                $("#select-municipio").html(data.select_municipios);
                $("#select-pais").html(data.select_countries);
                $("#select-tipo-persona").html(data.select_tipo_persona);
            })
            .catch((error) => {
                console.error("Error al obtener el cliente:", error);
            })
            .finally(() => {
                // Ocultar loader
                $("#loader").addClass("hidden");
                $("body").removeClass("overflow-hidden");
            });
    });

    //Obtener el producto seleccionado
    $(document).on("click", ".btn-selected-product", function () {
        const form = $(this).closest("form");
        const url = form.attr("action");
        const method = form.attr("method");
        const formData = form.serialize();

        $("#loader").removeClass("hidden");
        $("body").addClass("overflow-hidden");
        $("#form-add-product").trigger("reset");

        axios({
            method: method,
            url: url,
            data: formData,
        })
            .then((response) => {
                const data = response.data;

                if (data.success === false) {
                    showAlert("error", "Error", "Producto no encontrado");
                    return;
                }

                $("#product_id").val(data.product.id);
                $("#product_description").val(data.product.descripcion);
                if (data.product.has_stock) {
                    $("#count").prop("max", data.product.stockActual);
                } else {
                    $("#count").removeAttr("max");
                }

                let precioSinTributos = data.product.precioSinTributos;
                let precioUni = data.product.precioUni;

                if (data.customer && data.customer.special_price) {
                    precioSinTributos = data.product.special_price;
                    precioUni = data.product.special_price_with_iva;
                }

                const productPrice =
                    data.dte != "01"
                        ? precioSinTributos
                        : precioUni;
                $("#product_price").val(productPrice);

                $("#container-data-product").removeClass("hidden");
            })
            .catch((error) => {
                console.error("Error al obtener el producto:", error);
            })
            .finally(() => {
                $("#loader").addClass("hidden");
                $("body").removeClass("overflow-hidden");
            });
    });

    //Calcular total en el modal de seleccionar producto
    $("#count").on("input", function () {
        const count = $(this).val();
        const price = $("#product_price").val();
        const max = parseFloat($(this).prop("max"));
        const descuento = parseFloat($("#descuento_total").val()) || 0;
        const total = redondear(
            parseFloat(count) * parseFloat(price) -
            descuento
            , 8);
        if (count > max) {
            showAlert(
                "error",
                "Error",
                "La cantidad no puede ser mayor al stock disponible"
            );
            $(this).val("");
            $("#total_item").val("");
            return;
        }

        if (total < 0) {
            $("#descuento_total").val(0);
            $("#total_item").val(
                parseFloat(count) * redondear(parseFloat(price), 8)
            );
            showAlert(
                "error",
                "Error",
                "El descuento no puede ser mayor al total"
            );
            return;
        }

        $("#total_item").val(total);
        $("#descuento_total").prop("max", total);
    });

    $("#descuento_total").on("input", function () {
        const value = parseFloat($(this).val()) || 0;
        const total = parseFloat($("#descuento_total").prop("max")) || 0;

        if (total === 0) {
            showAlert("error", "Error", "Debes ingresar una cantidad y precio");
            $(this).val("");
            return;
        }

        if (value === "" || value === 0) {
            $("#total_item").val($("#product_price").val() * $("#count").val());
        }

        if (value > total) {
            showAlert(
                "error",
                "Error",
                "El descuento no puede ser mayor al total"
            );
            $(this).val("");
            $("#total_item").val(redondear(total, 8));
            return;
        }

        const total_descuento = total - value;

        if (total_descuento < 0) {
            $("#total_item").val(0);
            showAlert(
                "error",
                "Error",
                "El descuento no puede ser mayor al total"
            );
            return;
        }

        $("#total_item").val(redondear(total_descuento, 8));
    });

    //Add product
    $("#btn-add-product").click(function () {
        const form = $(this).closest("form");
        let hasError = false;

        const count = $("#count");
        const error_count = $("#error-" + count.attr("id"));

        const type_sale = $("#type-sale");
        const error_type_sale = $("#error-" + type_sale.attr("id"));

        const total_item = $("#total_item");
        const error_total_item = $("#error-" + total_item.attr("id"));

        if (count.val().trim() === "") {
            count.focus().addClass("is-invalid");
            error_count.css("display", "flex");
            hasError = true;
        } else {
            count.removeClass("is-invalid");
            error_count.css("display", "none");
        }

        if (type_sale.length > 0) {
            if (type_sale.val().trim() === "") {
                type_sale.next().find(".selected").addClass("is-invalid");
                error_type_sale.css("display", "flex");
                hasError = true;
            } else {
                type_sale.next().find(".selected").removeClass("is-invalid");
                error_type_sale.css("display", "none");
            }
        }

        if (total_item.val().trim() === "") {
            total_item.focus().addClass("is-invalid");
            error_total_item.css("display", "flex");
            hasError = true;
        } else {
            total_item.removeClass("is-invalid");
            error_total_item.css("display", "none");
        }

        if (hasError) {
            showAlert("error", "Error", "Rellena los campos requeridos");
            return;
        }

        $("#loader").removeClass("hidden");
        $("body").addClass("overflow-hidden");

        axios({
            method: form.attr("method"),
            url: form.attr("action"),
            data: form.serialize(),
        })
            .then((response) => {
                const data = response.data;
                console.log(data);

                if (data.success) {
                    $("#table-products-dte").html(data.table_products);
                    $("#table-exportacion").html(data.table_exportacion);
                    $("#container-data-product").addClass("hidden");
                    form.trigger("reset");

                    showAlert(
                        "success",
                        "Agregado",
                        "Producto agregado correctamente"
                    );

                    if (data.monto_pendiente !== undefined) {
                        $("#monto_total").val(redondear(data.monto_pendiente, 2));
                    }
                } else {
                    showAlert("error", "Error", data.message);
                }
            })
            .catch((error) => {
                console.error("Error al agregar producto:", error);
            })
            .finally(() => {
                $("#loader").addClass("hidden");
                $("body").removeClass("overflow-hidden");
            });
    });

    //Checks de retener IVA y renta
    $(document).on("change", ".retener", function () {
        const action = $(this).data("action");
        var retener = "inactive";
        const type = $(this).data("type");

        if ($(this).is(":checked")) {
            retener = "active";
        } else {
            retener = "inactive";
        }

        axios
            .get(action, {
                params: { value: retener, type: type },
            })
            .then(function (response) {
                $("#table-products-dte").html(response.data.table_products);
                if (response.data.monto_pendiente !== undefined) {
                    $("#monto_total").val(
                        redondear(response.data.monto_pendiente, 2)
                    );
                }
            })
            .catch(function (error) {
                console.log(error);
            })
            .finally(function () {
                $("#loader").addClass("hidden");
                $("body").removeClass("overflow-hidden");
            });

        $("#loader").removeClass("hidden");
        $("body").addClass("overflow-hidden");
    });

    //Sección documentos asociados
    $("#documento_asociado").on("Changed", function () {
        const value = $(this).val();
        const container = $("#container-data-documento-asociado");
        const container_transporte = $("#container-data-transporte");
        const container_medico = $("#container-data-medico");
        switch (value) {
            case "1":
            case "2":
                container.removeClass("hidden");
                container.find("input").attr("required", true);
                container_transporte.addClass("hidden");
                container_transporte.find("input").removeAttr("required");
                container_medico.addClass("hidden");
                container_medico.find("input").removeAttr("required");
                break;
            case "3": // Médico
                container.addClass("hidden");
                container.find("input").removeAttr("required");
                container_medico.removeClass("hidden");
                container_medico.find("input").attr("required", true);
                container_transporte.addClass("hidden");
                container_transporte.find("input").removeAttr("required");
                break;
            case "4": // Transporte
                container.addClass("hidden");
                container.find("input").removeAttr("required");
                container_transporte.removeClass("hidden");
                container_transporte.find("input").attr("required", true);
                container_medico.addClass("hidden");
                container_medico.find("input").removeAttr("required");
                break;
        }

    });

    //Sección condición operación
    $("#condicion_operacion").on("Changed", function () {
        const value = $(this).val();
        console.log(value);
        if (value === "1") {
            $("#input-plazo").addClass("hidden");
            $("#input-periodo").addClass("hidden");
        }

        if (value === "2") {
            $("#input-plazo").removeClass("hidden");
            $("#input-periodo").removeClass("hidden");
        }

        if (value === "3") {
            $("#input-plazo").removeClass("hidden");
            $("#input-periodo").removeClass("hidden");
        }
    });

    $("#tipo_item_exportar").on("Changed", function () {
        const value = $(this).val();
        const container = $("#container-data-exportacion");
        if (value === "1" || value === "3") {
            container.removeClass("hidden");
        } else {
            container.addClass("hidden");
            container.find("input").val("");
        }
    });

    function validateField(field, value) {
        value = value || field.val();
        if (value === "") {
            field.addClass("is-invalid");
            return false;
        } else {
            field.removeClass("is-invalid");
            return true;
        }
    }

    //Sección forma de pago
    $("#btn-add-forma-pago").on("click", function () {
        const action = $(this).data("action");
        const condicion_operacion = $("#condicion_operacion").val();

        const forma_pago = $("#forma_pago");
        const monto = $("#monto_total");
        const numero_documento = $("#numero_documento");
        const plazo = $("#plazo");
        const periodo = $("#periodo");
        var isValid = true;

        if (condicion_operacion === "1") {
            if (
                !validateField(
                    forma_pago.next().find(".selected"),
                    forma_pago.val()
                )
            )
                isValid = false;
            if (!validateField(monto)) isValid = false;
            if (!validateField(numero_documento)) isValid = false;
        }

        if (condicion_operacion === "2") {
            if (!validateField(monto)) isValid = false;
            if (!validateField(numero_documento)) isValid = false;
            if (!validateField(plazo.next().find(".selected"), plazo.val()))
                isValid = false;
            if (!validateField(periodo)) isValid = false;
        }

        if (condicion_operacion === "3") {
            if (
                !validateField(
                    forma_pago.next().find(".selected"),
                    forma_pago.val()
                )
            )
                isValid = false;
            if (!validateField(monto)) isValid = false;
            if (!validateField(numero_documento)) isValid = false;
            if (!validateField(plazo.next().find(".selected"), plazo.val()))
                isValid = false;
            if (!validateField(periodo)) isValid = false;
        }

        if (!isValid) {
            showAlert("error", "Error", "Rellena los campos requeridos");
            return;
        }

        if (isValid) {
            // Realizamos la solicitud con Axios
            axios
                .post(action, {
                    forma_pago: forma_pago.val(),
                    monto: monto.val(),
                    numero_documento: numero_documento.val(),
                    plazo: plazo.val(),
                    periodo: periodo.val(),
                    _token: $("input[name='_token']").val(),
                })
                .then(function (response) {
                    if (response.data.success) {
                        $("#table-formas-pago").html(response.data.table_data);
                        $("#monto_total").val(response.data.monto_pendiente);
                        showAlert(
                            "success",
                            "Exito",
                            "Forma de pago agregada correctamente"
                        );
                    } else {
                        showAlert("error", "Error", response.data.message);
                    }
                })
                .catch(function (error) {
                    // Error: mostramos el error en la consola
                    console.log(error);
                })
                .finally(function () {
                    // Al finalizar, ocultamos el loader y restauramos el overflow
                    $("#loader").addClass("hidden");
                    $("body").removeClass("overflow-hidden");
                });

            // Mostramos el loader antes de la solicitud
            $("#loader").removeClass("hidden");
            $("body").addClass("overflow-hidden");
        }
    });

    //Documento fisico - Comprobante de retención
    $("#monto_sujeto_retencion").on("input", function () {
        const value = $(this).val();
        const codigo_tributo = $("#codigo_tributo").val();
        if (codigo_tributo === "22") {
            $("#iva_retenido").val(redondear((value * 0.01), 2));
        }
    });

    $("#codigo_tributo").on("Changed", function () {
        const value = $(this).val();
        if (value === "Retención IVA") {
            var monto = $("#monto_sujeto_retencion").val();
            $("#iva_retenido").val(redondear((monto * 0.01), 2));
        }
    });

    //Validación para nota de débido y nota de crédito
    $(".new-item").on("click", function () {
        const action = $(this).data("action");
        const type = $(this).data("type");
        axios
            .get(action)
            .then(function (response) {
                if (!response.data.success) {
                    showAlert(
                        "warning",
                        "Advertencia",
                        "Para agregar items, primero debe ingresar los documentos relacionados"
                    );
                    return;
                } else {
                    if (type === "new-product") {
                        $("#drawer-new-product").removeClass(
                            "-translate-x-full"
                        );
                        $("#overlay").removeClass("hidden");
                        $("body").addClass("overflow-hidden");
                    } else if (type === "selected-product") {
                        $("#selected-product")
                            .removeClass("hidden")
                            .addClass("flex");
                        $("body").addClass("overflow-hidden");
                    } else if (type === "other-contribution") {
                        $("#taxes-iva").removeClass("hidden").addClass("flex");
                        $("body").addClass("overflow-hidden");
                    }
                }
            })
            .catch(function (error) {
                console.log(error);
            });
    });

    //Submit forms
    $(document).on("click", ".submit-form", function () {
        const form = $(this).closest("form");
        // console.log(form);

        let isValid = true;

        form.find("[required]").each(function () {
            const input = $(this);
            const error = $("#error-" + input.attr("id"));
            if (!input.val().trim()) {
                input.addClass("is-invalid");
                error.css("display", "flex");
                isValid = false;
            } else {
                input.removeClass("is-invalid");
                error.css("display", "none");
                isValid = true;
            }

            if (input.is("[select]") && !input.val().trim()) {
                input.next().find(".selected").addClass("is-invalid");
            }
        });

        if (!isValid) {
            showAlert("error", "Error", "Rellena los campos requeridos");
            return;
        }

        axios({
            method: form.attr("method"),
            url: form.attr("action"),
            data: form.serialize(),
        })
            .then(function (response) {
                const data = response.data;

                if (data.success) {
                    showAlert("success", "Exito", data.message);
                    form.trigger("reset");
                    $("#table-" + data.table).html(data.table_data);

                    if (data.modal !== undefined) {
                        $("#" + data.modal)
                            .addClass("hidden")
                            .removeClass("flex");
                    } else if (data.drawer !== undefined) {
                        $("#" + data.drawer).addClass("-translate-x-full");
                        $("#overlay").addClass("hidden");
                    }

                    if (data.monto_pendiente !== undefined) {
                        $("#monto_total").val(redondear(data.monto_pendiente, 2));
                    }

                    if (data.table_selected_product !== undefined) {
                        $("#table-selected-product").html(
                            data.table_selected_product
                        );
                    }

                    if (data.table_exportacion !== undefined) {
                        $("#table-exportacion").html(data.table_exportacion);
                    }

                    if (data.table_sujeto_excluido !== undefined) {
                        $("#table-sujeto-excluido").html(
                            data.table_sujeto_excluido
                        );
                    }

                    if (data.select_data) {
                        $("." + data.select).html(data.select_data);
                    }
                    if (data.select_data_new) {
                        $("." + data.select_new).html(data.select_data_new);
                    }

                    if (data.total_iva_retenido_texto !== undefined) {
                        $("#iva-retenido-letters").text(
                            data.total_iva_retenido_texto
                        );
                    }

                    if (data.total_discounts !== undefined) {
                        $("#container-total-discount").html(
                            data.total_discounts
                        );
                    }

                    if (data.comprobante_retencion !== undefined) {
                        if (data.success) {
                            $("#container-data-hacienda").removeClass("hidden");
                            $("#cod-generacion-hacienda").val(data.data.codGeneracion);
                            $("#tipo-dte-hacienda").val(data.data.tipoDte);
                            $("#fecha-emision-hacienda").val(data.data.fechaEmision);
                            $("#monto-documento-hacienda").val(data.data.sujetoRetencion);
                            $("#iva-retenido-documento-hacienda").val(data.data.ivaRetenido);
                            $("#descripcion-retencion-hacienda").val(data.data.descripcion);
                        } else {
                            showAlert("error", "Error", data.message);
                        }
                    }
                } else {
                    showAlert("error", "Error", data.message);
                }
            })
            .catch(function (error) {
                console.log(error);
            })
            .finally(function () {
                $("#loader").addClass("hidden");
                $("body").removeClass("overflow-hidden");
            });

        $("#loader").removeClass("hidden");
        $("body").addClass("overflow-hidden");
    });

    $(document).on("click", ".delete-discounts", function () {
        const action = $(this).data("action");
        axios
            .get(action)
            .then(function (response) {
                const data = response.data;

                if (data.success) {
                    $("#table-" + data.table).html(data.table_data);

                    if (data.monto_pendiente !== undefined) {
                        $("#monto_total").val(redondear(data.monto_pendiente, 2));
                    }

                    if (data.total_discounts !== undefined) {
                        $("#container-total-discount").html(
                            data.total_discounts
                        );
                    }

                    if (data.table_sujeto_excluido !== undefined) {
                        $("#table-sujeto-excluido").html(
                            data.table_sujeto_excluido
                        );
                    }
                } else {
                    showAlert("error", "Error", data.message);
                }
            })
            .catch(function (error) {
                console.log(error);
            })
            .finally(function () {
                $("#loader").addClass("hidden");
                $("body").removeClass("overflow-hidden");
            });

        $("#loader").removeClass("hidden");
        $("body").addClass("overflow-hidden");
    });

    //Delete items
    $(document).on("click", ".btn-delete", function () {
        const url = $(this).data("action");
        axios
            .get(url)
            .then(function (response) {
                const data = response.data;

                if (data.success) {
                    showAlert("success", "Exito", data.message);

                    if (data.table_data) {
                        $("#table-" + data.table).html(data.table_data);
                    }

                    if (data.table_data_2) {
                        $("#table-" + data.table_2).html(data.table_data_2);
                    }

                    if (data.total_iva_retenido_texto !== undefined) {
                        $("#iva-retenido-letters").text(
                            data.total_iva_retenido_texto
                        );
                    }

                    if (data.table_exportacion !== undefined) {
                        $("#table-exportacion").html(data.table_exportacion);
                    }

                    if (data.table_sujeto_excluido !== undefined) {
                        $("#table-sujeto-excluido").html(
                            data.table_sujeto_excluido
                        );
                    }

                    if (data.select_data) {
                        $("." + data.select).html(data.select_data);
                    }

                    if (data.select_data_new) {
                        $("." + data.select_new).html(data.select_data_new);
                    }

                    if (data.monto_pendiente !== undefined) {
                        $("#monto_total").val(redondear(data.monto_pendiente, 2));
                    }
                } else {
                    showAlert("error", "Error", data.message);
                }
            })
            .catch(function (error) {
                console.log(error);
            })
            .finally(function () {
                $("#loader").addClass("hidden");
                $("body").removeClass("overflow-hidden");
            });

        $("#loader").removeClass("hidden");
        $("body").addClass("overflow-hidden");
    });

    // Omitir Datos Emisor
    $("#omitir_datos_receptor, #save_as_template").on("change", function () {
        if ($("#save_as_template").is(":checked")) {
            $("#omitir-datos-receptor-container").addClass("hidden");
            $("#generate-button").addClass("hidden");
            $("#draft-button").addClass("hidden");
            $("#template-button").removeClass("hidden");
            $("#template_name").removeClass("hidden");
        } else {
            $("#omitir-datos-receptor-container").removeClass("hidden");
            $("#generate-button").removeClass("hidden");
            $("#draft-button").removeClass("hidden");
            $("#template-button").addClass("hidden");
            $("#template_name").addClass("hidden");
        }

        if ($(this).is(":checked")) {
            // Clear all the inputs within the div #datos-receptor and make them readonly and not required
            $(
                "#datos-receptor input, #datos-receptor select, #datos-receptor textarea"
            ).val("");
            $(
                "#datos-receptor input, #datos-receptor select, #datos-receptor textarea"
            ).prop("readonly", true);
            $("#datos-receptor select, #datos-receptor input").prop(
                "required",
                false
            );
            $("#datos-receptor").addClass("hidden");
        } else {
            // Make all the inputs within the div #datos-receptor required
            $("#datos-receptor input, #datos-receptor select").prop(
                "required",
                true
            );
            $(
                "#datos-receptor input, #datos-receptor select, #datos-receptor textarea"
            ).prop("readonly", false);
            $("#datos-receptor").removeClass("hidden");
        }
    });

    //Inpust discounts
    $("#container-total-discount input").on("input", function () {
        const total_discounts = $("#container-total-discount input");
        // El valor de cada input no puede ser mayor a 100
        total_discounts.each(function () {
            if (parseFloat($(this).val()) > 100) {
                $(this).val(100);
                showAlert(
                    "error",
                    "Error",
                    "El descuento no puede ser mayor al 100%"
                );
            }
        });
    });

    //Exportacion
    $(document).on("change", "#seguro, #flete", function () {
        const url = $(this).data("url");
        const type = $(this).data("type");
        const value = $(this).val();
        axios
            .get(url, {
                params: { value: value, type: type },
            })
            .then((response) => {
                const data = response.data;
                if (data.success) {
                    showAlert("success", "Éxito", data.message);
                    if (data.monto_pendiente !== undefined) {
                        $("#monto_total").val(redondear(data.monto_pendiente, 2));
                    }
                    $("#table-exportacion").html(data.table_exportacion);
                } else {
                    $("#flete").val("");
                    $("#seguro").val("");
                    showAlert("error", "Error", data.message);
                }
            })
            .catch((error) => {
                console.error(error);
            });
    });

    //Seleccionar documento electronico
    $(document).on("click", ".btn-selected-document-electric", function () {
        const url = $(this).data("url");
        const codGeneracion = $(this).data("cod");
        axios
            .get(url, {
                params: { codGeneracion: codGeneracion },
            })
            .then((response) => {
                const data = response.data;
                if (data.success) {
                    console.log("Documento seleccionado:", data);
                    $("#table-" + data.table).html(data.table_data);
                    $("#" + data.modal)
                        .addClass("hidden")
                        .removeClass("flex");
                    $("." + data.select).html(data.select_data);
                    $("." + data.select_new).html(data.select_data_new);
                    $("#overlay").addClass("hidden");
                    $("body").removeClass("overflow-hidden");
                    showAlert("success", "Exito", data.message);
                } else {
                    showAlert("error", "Error", data.message);
                }
            })
            .catch((error) => {
                console.error(error);
            })
            .finally(function () {
                $("#loader").addClass("hidden");
                $("body").removeClass("overflow-hidden");
            });

        $("#loader").removeClass("hidden");
        $("body").addClass("overflow-hidden");
    });

    $(document).on("click", ".btn-add-document-electric", function () {
        const numero_documento = $("#numero_documento_customer").val();
        if (numero_documento === "") {
            showAlert(
                "error",
                "Error",
                "Debes seleccionar un cliente antes de agregar un documento electrónico"
            );
            return;
        }

        $("#add-documento-electronico").removeClass("hidden").addClass("flex");
        $("body").addClass("overflow-hidden");
    });

    $(document).on("click", ".btn-selected-document", function () {
        const codGeneracion = $(this).data("cod");
        const url = $(this).data("url");
        axios
            .get(url, {
                params: { codGeneracion: codGeneracion },
            })
            .then((response) => {
                const data = response.data;
                if (data.success) {
                    $("#container-data-document").removeClass("hidden");
                    $("#monto-documento").val(data.monto);
                    $("#cod-generacion").val(codGeneracion);
                    if ($("#codigo-tributo-2").val() === "22") {
                        $("#iva-retenido-documento").val(
                            redondear((data.monto * 0.01), 2)
                        );
                    }
                } else {
                    showAlert("error", "Error", data.message);
                }
            })
            .catch((error) => {
                console.error(error);
            })
            .finally(function () {
                $("#loader").addClass("hidden");
                $("body").removeClass("overflow-hidden");
            });

        $("#loader").removeClass("hidden");
        $("body").addClass("overflow-hidden");
    });

    $(document).on("submit", "#form-query-dte", function (e) {
        e.preventDefault();
        const form = $(this);
        const url = form.attr("action");
        const method = form.attr("method");
        const formData = form.serialize();

        $("#loader").removeClass("hidden");
        $("body").addClass("overflow-hidden");

        axios({
            method: method,
            url: url,
            data: formData,
        })
            .then((response) => {
                const data = response.data;

                if (data.success) {
                    $("#container-data-hacienda").removeClass("hidden");
                    $("#cod-generacion-hacienda").val(data.codGeneracion);
                    $("#monto-documento-hacienda").val(data.sujetoRetencion);
                    $("#iva-retenido-documento-hacienda").val(data.ivaRetenido);
                    $("#descripcion-retencion-hacienda").val(data.descripcion);
                    showAlert("success", "Éxito", "Consulta realizada correctamente");
                } else {
                    showAlert("error", "Error", data.message);
                }
            })
            .catch((error) => {
                console.error("Error al realizar la consulta:", error);
            })
            .finally(() => {
                $("#loader").addClass("hidden");
                $("body").removeClass("overflow-hidden");
            });
    });


    // Handle cambio de sucursal
    const $sucursal = $("#sucursal_select");

    $sucursal.on("Changed", function () {
        const action = $(this).data("action");
        const sucursalId = $(this).val();
        const business_id = $(this).data("business-id");
        $("#datos-sucursal").addClass("hidden");
        $.ajax({
            url: action,
            type: "GET",
            data: { sucursal_id: sucursalId, business_id: business_id },
            success: function (response) {
                $("#punto_venta_select").html(response.html)
                $("#datos-sucursal #complemento_emisor").val(response.sucursal.complemento);
                $("#datos-sucursal #correo_emisor").val(response.sucursal.correo);
                $("#datos-sucursal #telefono_emisor").val(response.sucursal.telefono);
                $("#datos-sucursal").removeClass("hidden");
            },
            error: function () {
                console.error("Error al cambiar la sucursal");
            },
        });
    });

    function redondear(num, decimales = 2) {
        const factor = Math.pow(10, decimales);
        return Math.round((num + Number.EPSILON) * factor) / factor;
    }
});
