let tablaProds = null;

let items = [];
let itemSeleccionado = null;
let itemNuevo = null;

let tributos_dte = [];
let total_tributos = 0;

let descuentosTotal = 0;

$(function () {

    // Cargar Departamentos en Select
    $.ajax({
        url: '/departamentos/all',
        success: function (apiResponse) {
            // Llenar el select de departamentos
            const departamentoSelect = $('#departamentoContribuyente');
            departamentoSelect.empty();
            apiResponse.forEach(departamento => {
                departamentoSelect.append(new Option(departamento.valores, departamento.codigo));
            });

            // Manejar el cambio del select de departamentos
            departamentoSelect.on('change', function () {
                const selectedDepartamento = $(this).val();
                const municipioSelect = $('#municipioContribuyente');

                // Limpiar el select de municipios
                municipioSelect.empty();

                if (selectedDepartamento) {
                    // Obtener los municipios del departamento seleccionado
                    const municipios = apiResponse.find(dept => dept.codigo === selectedDepartamento).municipios;

                    // Llenar el select de municipios
                    municipios.forEach(municipio => {
                        municipioSelect.append(new Option(municipio.valores, municipio.codigo));
                    });
                }
            });
            // Trigger the change event on page load to populate the municipios for the selected departamento
            $('#departamentoContribuyente').trigger('change');
        }
    })

    // Set a timeout to update #horaDTE every second
    setInterval(() => {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');

        const timeString = `${hours}:${minutes}:${seconds}`;

        $('#horaDTE').val(timeString);
    }, 1000);


    // Set the current date on #fechaDTE
    $('#fechaDTE').val(new Date().toISOString().split('T')[0]);

    // Cálculo de descuentos y subtotales
    $("#descuento, #cantidad, #precio").on("change", function () {
        let cantidad = $("#cantidad").val();
        let precio = $("#precio").val();
        let descuento = $("#descuento").val();
        let subtotal = calcular_subtotal_item(cantidad, precio, descuento);
        $("#total").val(subtotal);
        itemNuevo.cantidad = cantidad;
        itemNuevo.precioUni = precio;
        itemNuevo.montoDescu = descuento;
        let tipoVenta = $("#tipoVenta").val();
        if (tipoVenta == "gravada"){
            itemNuevo.ventaGravada = subtotal;
        } else if (tipoVenta == "exenta"){
            itemNuevo.ventaExenta = subtotal;
        } else if (tipoVenta == "noSujeta"){
            itemNuevo.ventaNoSuj = subtotal;
        }
        calcular_tributos_item();
    });

    // Manejar el cambio de la casilla de verificación de contribuyente
    $("#checkContribuyente").on("change", function () {
        if ($(this).prop("checked")) {
            $("#nombreContribuyente").prop("disabled", true);
            $("#telefonoContribuyente").prop("disabled", true);
            $("#correoContribuyente").prop("disabled", true);
            $("#departamentoContribuyente").prop("disabled", true);
            $("#municipioContribuyente").prop("disabled", true);
            $("#complementoContribuyente").prop("disabled", true);
            $("#tipoDoc").prop("disabled", true);
            $("#nitContribuyente").prop("disabled", true);
        } else {
            $("#nombreContribuyente").prop("disabled", false);
            $("#telefonoContribuyente").prop("disabled", false);
            $("#correoContribuyente").prop("disabled", false);
            $("#departamentoContribuyente").prop("disabled", false);
            $("#municipioContribuyente").prop("disabled", false);
            $("#complementoContribuyente").prop("disabled", false);
            $("#tipoDoc").prop("disabled", false);
            $("#nitContribuyente").prop("disabled", false);
        }
    });

    // Agregar Item que no está en BD
    $("#agregar_item").on("click", function () {
        let unidad_id = $("#unidad").val();
        // let unidad = $("#unidad option:selected").text();
        let cantidad = $("#cantidad").val();
        // let precio = $("#precio").val();
        let descuento = $("#descuento").val() || 0;
        // let total = $("#total").val();
        let descripcion = $("#producto").val();
        
        itemNuevo.tipoItem = $("#tipoItem").val();
        itemNuevo.uniMedida = unidad_id;
        itemNuevo.montoDescu = descuento;
        itemNuevo.descripcion = descripcion;
        itemNuevo.cantidad = cantidad;

        items.push(itemNuevo);
        cargar_items(items);

        $("#cantidad").val("");
        $("#precio").val("");
        $("#descuento").val("");
        $("#total").val("");
        $("#producto").val("");
        calcular_totales();
    });

    $("#items").on("click", ".eliminar", function () {
        let id = $(this).data("id");
        items = items.filter(item => item.id !== id);
        $(this).closest("tr").remove();
        calcular_totales();
    });

    $("#guardarDescuento").on("click", function () {
        let descuento = $("#descVentasGravadas").val();
        descuentosTotal = parseFloat(descuento);
        calcular_totales();
        $("#descuentosTotal").text("$" + descuentosTotal.toFixed(2));
    });

    $("#generarDocumento").on("click", function () {
        generar_documento();
    });

    $("#prodExistenteModal").on("show.bs.modal", function (e) {
        itemSeleccionado = null;
        $("#prodSeleccionado").addClass("d-none");

        $.ajaxSetup({
            Headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        
        if (tablaProds) {
            tablaProds.ajax.reload();
        } else {
            tablaProds = $("#tablaProds").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/business/obtener_productos',
                    type: 'POST',
                    data: function (data) {
                        data._token = $('meta[name="csrf-token"]').attr('content');
                        data.search = $('input[type="search"]').val();
                    }
                },
                order: ['1', 'DESC'],
                pageLength: 10,
                searching: true,
                aoColumns: [
                    {
                        data: 'codigo',
                    },
                    {
                        data: 'descripcion',
                    },
                    {
                        data: 'precioUni',
                        render: function (data, type, row) {
                            return `$${parseFloat(data).toFixed(2)}`;
                        }
                    },
                    {
                        data: 'id',
                        width: "20%",
                        render: function (data, type, row) {
                            return `
                                <button type="button" class="btn btn-primary btn-sm btnSeleccionarProd" data-id="${row.id}">Seleccionar</button>
                            `;
                        }
                    }
                ],
                buttons: [],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/2.1.5/i18n/es-ES.json',
                }
            });

            tablaProds.on('click', '.btnSeleccionarProd', function () {
                const id = $(this).data('id');
                $.ajax({
                    url: `/business/obtener_producto/${id}`,
                    success: function (response) {
                        $("#prodSeleccionado").removeClass("d-none");
                        itemSeleccionado = response;
                        console.log(itemSeleccionado);
                        $("#prodDesc").text(itemSeleccionado.descripcion);
                    }
                });
            });
        }
    })

    $("#aggitem").on("show.bs.modal", function (e) {
        itemNuevo = {
            id: items.length + 1,
            tipoItem: null,
            cantidad: null,
            codigo: null,
            uniMedida: null,
            descripcion: null,
            precioUni: 0,
            montoDescu: 0,
            ventaNoSuj: 0,
            ventaExenta: 0,
            ventaGravada: 0,
            tributos: [],
            psv: 0,
            ivaItem: 0,
        };
    });

    $("#aggitem .form-check-input").on("change", function () {
        calcular_tributos_item();
    });

    $("#cantidadExistente, #descuentoExistente").on("change", function () {
        let cantidad = $(this).val() || 0;
        let precio = itemSeleccionado.precioUni;
        let descuento = $("#descuentoExistente").val() || 0;
        let subtotal = calcular_subtotal_item(cantidad, precio, descuento);

        $("#totalExistente").val(parseFloat(subtotal).toFixed(4));
    });

    $("#btnAgregarProd").on("click", function () {
        let cantidad = $("#cantidadExistente").val() || 0;
        let descuento = $("#descuentoExistente").val() || 0;
        itemSeleccionado.cantidad = cantidad;
        itemSeleccionado.montoDescu = descuento;
        itemSeleccionado.ventaGravada = 0;
        itemSeleccionado.ventaExenta = 0;
        itemSeleccionado.ventaNoSuj = 0;
        itemSeleccionado.tributos.forEach(trib => {
            if(trib.codigo == "20"){
                trib.calculado = ((itemSeleccionado.precioUni / 1.13) * trib.valor) * itemSeleccionado.cantidad;
            } else {
                if (trib.es_porcentaje) {
                    trib.calculado = (itemSeleccionado.precioUni * trib.valor) * itemSeleccionado.cantidad;
                } else {
                    trib.calculado = trib.valor * itemSeleccionado.cantidad;
                }
            }
        });

        switch ($("#tipoVentaExistente").val()) {
            case "gravada":
                itemSeleccionado.ventaGravada = $("#totalExistente").val();
                break;
            case "exenta":
                itemSeleccionado.ventaExenta = $("#totalExistente").val();
                break;
            case "noSujeta":
                itemSeleccionado.ventaNoSuj = $("#totalExistente").val();
                break;
        }

        itemSeleccionado.ivaItem = (($("#totalExistente").val() / 1.13) * 0.13).toFixed(4);

        items.push(itemSeleccionado);
        cargar_items(items);

        $("#cantidadExistente").val("");
        $("#descuentoExistente").val("");
        $("#totalExistente").val("");
        calcular_totales();
    });
});

function calcular_subtotal_item(cantidad = 0, precio = 0, descuento = 0) {
    return ((cantidad * precio) - descuento).toFixed(4);
}

function calcular_totales() {
    mostrar_tributos();
    let reteIva = 0;
    let reteRenta = 0;
    let subTotalGeneral = 0;
    let montoTotalOperacion = 0;
    let totalPagar = 0;
    items.forEach(item => {
        subTotalGeneral += parseFloat(item.ventaGravada) + parseFloat(item.ventaExenta) + parseFloat(item.ventaNoSuj);
    });
    montoTotalOperacion = subTotalGeneral - descuentosTotal + total_tributos;
    totalPagar = montoTotalOperacion + reteIva + reteRenta;

    $("#reteIVA").text("$" + reteIva.toFixed(2));
    $("#reteRenta").text("$" + reteRenta.toFixed(2));
    $("#subTotalGeneral").text("$" + subTotalGeneral.toFixed(2));
    $("#montoTotalOperacion").text("$" + montoTotalOperacion.toFixed(2));
    $("#totalPagar").text("$" + totalPagar.toFixed(2));
    $("#monto").val(totalPagar);
}

function generar_documento() {
    $("#loadingOverlay").removeClass("d-none")
    let receptor = {};
    if ($("#checkContribuyente").prop("checked")) {
        receptor = {
            "nombre": "Consumidor Final",
            "telefono": null,
            "correo": null,
            "direccion": null,
            "tipoDocumento": null,
            "numDocumento": null
        }
    } else {
        receptor = {
            "nombre": $("#nombreContribuyente").val(),
            "telefono": $("#telefonoContribuyente").val(),
            "correo": $("#correoContribuyente").val(),
            "direccion": {
                "departamento": $("#departamentoContribuyente").val(),
                "municipio": $("#municipioContribuyente").val(),
                "complemento": $("#complementoContribuyente").val()
            },
            "tipoDocumento": $("#tipoDoc").val(),
            "numDocumento": $("#nitContribuyente").val()
        }
    }

    let dte = {
        "nit": $("#nit").val(),
        "receptor": receptor,
        "cuerpoDocumento": [],
        "documentoRelacionado": null,
        "ventaTercero": null,
        "resumen": {
            "descuNoSuj": 0,
            "descuExtenta": 0,
            "descuGravada": 0,
            "porcentajeDescuento": 0,
            "ivaRete1": 0,
            "reteRenta": 0,
            "saldoFavor": 0,
            "condicionOperacion": 1
        },
        "extension": null,
        "apendice": null,
        "pagos": null,
        "numPagoElectronico": null,
    }

    items.forEach(item => {
        let tributos_item = [];
        item.tributos.forEach(trib => {
            if(trib.codigo !== "20"){
                tributos_item.push(trib.codigo);
            }
        });

        if(tributos_item.length == 0){
            tributos_item = null;
        }

        dte.cuerpoDocumento.push({
            "tipoItem": item.tipoItem,
            "numeroDocumento": null,
            "cantidad": item.cantidad,
            "codigo": item.codigo || null,
            "codTributo": null,
            "uniMedida": item.uniMedida,
            "descripcion": item.descripcion,
            "precioUni": item.precioUni,
            "montoDescu": item.montoDescu,
            "ventaNoSuj": item.ventaNoSuj,
            "ventaExenta": item.ventaExenta,
            "ventaGravada": item.ventaGravada,
            "tributos": tributos_item,
            "psv": item.precioUni,
            "ivaItem": item.ivaItem,
            "noGravado": 0,
        });
    });

    dte.resumen.tributos = tributos_dte;
    console.log(dte);

    $.ajax({
        url: "/business/factura",
        method: "POST",
        data: JSON.stringify(dte),
        contentType: "application/json",
        success: function (response) {
            if (response.status == 201) {
                Swal.fire({
                    icon: 'success',
                    title: 'Factura generada',
                    text: 'La factura ha sido generada exitosamente',
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    $("#loadingOverlay").addClass("d-none")
                    window.location.href = "/business/dtes";
                })
            }
        },
        error: function (error) {
            console.error(error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ha ocurrido un error al generar la factura',
                showConfirmButton: false,
                timer: 2000
            })
        }
    });
}

function cargar_items(items){
    let tbody = $("#items");
    tbody.empty();
    items.forEach(item => {
        tbody.append(`
            <tr>
                <td>${item.uniMedida}</td>
                <td>${item.descripcion}</td>
                <td>${item.cantidad}</td>
                <td>$${parseFloat(item.precioUni).toFixed(2)}</td>
                <td>$${parseFloat(item.montoDescu).toFixed(2)}</td>
                <td>
                    $${parseFloat(item.ventaGravada).toFixed(4)} (Gravada) <br>
                    $${parseFloat(item.ventaExenta).toFixed(4)} (Exenta) <br>
                    $${parseFloat(item.ventaNoSuj).toFixed(4)} (No Sujeta)
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm eliminar" data-id="${item.id}">Eliminar</button>
                </td>
            </tr>
        `);
    });
    console.log(items);
}

function calcular_tributos_item(){
    $(".form-check-input").each(function () {
        if ($(this).prop("checked")) {
            // Append the value to the tributos array if it's not already there
            let tributo = {
                codigo: $(this).val(),
                descripcion: $(this).next('label').text().trim(),
                valor: $(this).data('valor'),
                es_porcentaje: $(this).data('porcentaje'),
                calculado: 0
            };

            if(!itemNuevo.tributos.some(trib => trib.codigo === tributo.codigo)){
                itemNuevo.tributos.push(tributo);
            }
        } else {
            // Remove the value from the tributos array if it's there
            itemNuevo.tributos = itemNuevo.tributos.filter(trib => trib.codigo !== $(this).val());
        }
    });

    let alerts = "";
    itemNuevo.tributos.forEach(trib => {
        let valorTributo = 0;
        if(trib.codigo == "20"){
            valorTributo = ((itemNuevo.precioUni / 1.13) * trib.valor) * itemNuevo.cantidad;
            alerts += `
                <div class="alert alert-info" role="alert">
                    ${trib.descripcion}: $${valorTributo.toFixed(4)}
                </div>
            `;
            trib.calculado = valorTributo;
        } else {
            if (trib.es_porcentaje) {
                valorTributo = (itemNuevo.precioUni * trib.valor) * itemNuevo.cantidad;
            } else {
                valorTributo = trib.valor * itemNuevo.cantidad;
            }
            alerts += `
                <div class="alert alert-info" role="alert">
                    ${trib.descripcion}: $${valorTributo.toFixed(4)}
                </div>
            `;
            trib.calculado = valorTributo;
        }
    });
    $("#tributosAplicados").html(alerts);
    // console.log(itemNuevo.tributos);
}

function calcular_tributos_dte(){
    total_tributos = 0;
    items.forEach(item => {
        item.tributos.forEach(trib => {
            if(trib.codigo !== "20"){
                if(!tributos_dte.some(tributo => tributo.codigo === trib.codigo)){
                    tributos_dte.push({
                        "codigo": trib.codigo,
                        "descripcion": trib.descripcion,
                        "valor": trib.calculado,
                    });
                } else {
                    tributos_dte.find(tributo => tributo.codigo === trib.codigo).valor += trib.calculado;
                }
                total_tributos += trib.calculado;
            }
        });
    });
}

function mostrar_tributos(){
    calcular_tributos_dte();
    let tbody = $("#tributos");
    tbody.empty();
    tributos_dte.forEach(trib => {
        tbody.append(`
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-end fw-bold">${trib.descripcion}</td>
                <td>$${trib.valor.toFixed(2)}</td>
            </tr>
        `);
    })
}