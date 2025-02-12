let tablaProds = null;
let tablaClientes = null;
let itemNuevo = null;

let descuentosTotal = 0;

let items = [];
let reteIva1 = 0;
let reteRenta = 0;

if (localStorage.getItem("items_fse")) {
    items = JSON.parse(localStorage.getItem("items_fse"));
    cargar_items();
    calcular_totales();
}

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
        itemNuevo.compra = subtotal;
    });

    // Agregar Item que no está en BD
    $("#agregar_item").on("click", function () {
        let unidad_id = $("#unidad").val();
        let cantidad = $("#cantidad").val();
        let descuento = $("#descuento").val() || 0;
        let descripcion = $("#producto").val();

        itemNuevo.id = items.length + 1;
        itemNuevo.tipoItem = $("#tipoItem").val();
        itemNuevo.uniMedida = unidad_id;
        itemNuevo.montoDescu = descuento;
        itemNuevo.descripcion = descripcion;
        itemNuevo.cantidad = cantidad;
        itemNuevo.compra = $("#total").val();

        items.push(itemNuevo);
        reiniciar_item();
        cargar_items();
        // Guardar items en localStorage
        localStorage.setItem('items_fse', JSON.stringify(items));

        $("#cantidad").val("");
        $("#precio").val("");
        $("#descuento").val("");
        $("#total").val("");
        $("#producto").val("");
        calcular_totales();
    });

    // Eliminar Items que se hayan agregado al carrito
    $("#items").on("click", ".eliminar", function () {
        let id = $(this).data("id");
        items = items.filter(item => item.id !== id);
        cargar_items();
        calcular_totales();
        // Guardar items en localStorage
        localStorage.setItem('items_fse', JSON.stringify(items));
    });

    // Guardar descuentos globales
    $("#guardarDescuento").on("click", function () {
        let descuento = $("#descVentasGravadas").val();
        descuentosTotal = parseFloat(descuento);
        calcular_totales();
    });

    // Generar DTE y enviarlo
    $("#generarDocumento").on("click", function () {
        generar_documento();
    });

    // Al abrir modal de clientes
    $("#clienteExistenteModal").on("show.bs.modal", function (e) {
        $.ajaxSetup({
            Headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        if (tablaClientes) {
            tablaClientes.ajax.reload();
        } else {
            tablaClientes = $("#tablaClientes").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/business/obtener_clientes',
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
                        data: 'numDocumento',
                        render: function (data, type, row) {
                            if (row.tipoDocumento == "13") {
                                return row.numDocumento.slice(0, -1) + '-' + row.numDocumento.slice(-1)
                            } else {
                                return row.numDocumento
                            }
                        }
                    },
                    {
                        data: 'nombre',
                    },
                    {
                        data: 'id',
                        width: "20%",
                        render: function (data, type, row) {
                            return `
                                <button type="button" class="btn btn-primary btn-sm btnSeleccionarCliente" data-id="${row.id}">Seleccionar</button>
                            `;
                        }
                    }
                ],
                buttons: [],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/2.1.5/i18n/es-ES.json',
                }
            });

            tablaClientes.on('click', '.btnSeleccionarCliente', function () {
                const id = $(this).data('id');
                $.ajax({
                    url: `/business/obtener_cliente/${id}`,
                    success: function (response) {
                        $("#tipoDoc").val(response.tipoDocumento);
                        if (response.tipoDocumento == "13") {
                            $("#nitContribuyente").val(response.numDocumento.slice(0, -1) + '-' + response.numDocumento.slice(-1))
                        } else {
                            $("#nitContribuyente").val(response.numDocumento)
                        }
                        $("#nombreContribuyente").val(response.nombre)
                        $("#departamentoContribuyente").val(response.departamento)
                        $("#departamentoContribuyente").trigger("change")
                        $("#municipioContribuyente").val(response.municipio)
                        $("#complementoContribuyente").val(response.complemento)
                        $("#correoContribuyente").val(response.correo)
                        $("#telefonoContribuyente").val(response.telefono)

                        $("#cerrarModalCliente").trigger("click")
                    }
                });
            });
        }
    })

    $("#aggitem").on("show.bs.modal", function (e) {
        reiniciar_item();
    });

    $("#checkIvaRete1").on("change", function () {
        if ($(this).prop("checked")) {
            let sumaGravada = 0;
            items.forEach(item => {
                sumaGravada += parseFloat(item.ventaGravada);
            });
            reteIva1 = (sumaGravada / 1.13) * 0.01;
            localStorage.setItem('reteIva1', reteIva1);
        } else {
            reteIva1 = 0;
            localStorage.removeItem("reteIva1_fse")
        }
        // Guardar items en localStorage
        calcular_totales();
    });

    $("#cancelarDTE").on("click", function () {
        Swal.fire({
            title: "¿Cancelar generación de DTE?",
            text: "Se perderá toda la información ingresada",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sí, Cancelar",
            cancelButtonText: "No"
        }).then((result) => {
            if (result.isConfirmed) {
                localStorage.removeItem("items_fse")
                localStorage.removeItem("reteIva1_fse")
                localStorage.removeItem("reteRenta_fse")
                window.location = "/business/dashboard"
            }
        });
    })
});

function calcular_subtotal_item(cantidad = 0, precio = 0, descuento = 0) {
    return ((cantidad * precio) - descuento).toFixed(4);
}

function calcular_totales() {
    let subTotalGeneral = 0;
    let montoTotalOperacion = 0;
    let totalPagar = 0;
    items.forEach(item => {
        subTotalGeneral += parseFloat(item.compra);
    });
    reteRenta = subTotalGeneral * 0.1;
    montoTotalOperacion = subTotalGeneral - descuentosTotal;
    totalPagar = montoTotalOperacion - reteIva1 - reteRenta;

    $("#reteIVA").text("$" + reteIva1.toFixed(2));
    $("#reteRenta").text("$" + reteRenta.toFixed(2));
    $("#montoTotalOperacion").text("$" + montoTotalOperacion.toFixed(2));
    $("#totalPagar").text("$" + totalPagar.toFixed(2));
    $("#monto").val(totalPagar);
    $("#descuentosTotal").text("$" + descuentosTotal.toFixed(2));
}

function generar_documento() {
    $("#loadingOverlay").removeClass("d-none")
    let receptor = {
        "nombre": $("#nombreContribuyente").val(),
        "telefono": $("#telefonoContribuyente").val(),
        "correo": $("#correoContribuyente").val(),
        "direccion": {
            "departamento": $("#departamentoContribuyente").val(),
            "municipio": $("#municipioContribuyente").val(),
            "complemento": $("#complementoContribuyente").val()
        },
        "tipoDocumento": $("#tipoDoc").val(),
        "numDocumento": $("#nitContribuyente").val().replace(/-/g, '')
    }

    let dte = {
        "nit": $("#nit").val(),
        "sujetoExcluido": receptor,
        "cuerpoDocumento": [],
        "resumen": {
            "descu": descuentosTotal,
            "totalDescu": 0,
            "ivaRete1": reteIva1,
            "condicionOperacion": $("#condicionOperacion").val(),
            "observaciones": $("#observacionesDoc").val(),
        },
        "apendice": null,
    }

    items.forEach(item => {
        dte.cuerpoDocumento.push({
            "tipoItem": item.tipoItem,
            "cantidad": item.cantidad,
            "uniMedida": item.uniMedida,
            "descripcion": item.descripcion,
            "precioUni": item.precioUni,
            "codigo": item.codigo || null,
            "montoDescu": item.montoDescu,
            "compra": item.compra,
        });
    });

    $.ajax({
        url: "/business/factura?dte=sujeto_excluido",
        method: "POST",
        data: JSON.stringify(dte),
        contentType: "application/json",
        success: function (response) {
            if (response.status == 201) {
                if (response.message.estado == "PROCESADO") {
                    Swal.fire({
                        icon: 'success',
                        title: 'Factura generada',
                        text: 'La factura ha sido generada exitosamente',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        $("#loadingOverlay").addClass("d-none")
                        localStorage.removeItem("items_fse")
                        localStorage.removeItem("reteIva1_fse")
                        localStorage.removeItem("reteRenta_fse")
                        window.location.href = "/business/dtes";
                    })
                } else if (response.message.estado == "CONTINGENCIA") {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Factura generada en CONTINGENCIA',
                        text: 'Se generó la factura, pero no se envió a MH',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        $("#loadingOverlay").addClass("d-none")
                        window.location.href = "/business/dtes";
                    })
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Factura Rechazada',
                        text: `Motivo: ${response.message.observaciones}`,
                    }).then(() => {
                        $("#loadingOverlay").addClass("d-none")
                    })
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'La factura no se envió',
                    text: 'Ha ocurrido un error, verifica los datos e intenta de nuevo',
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    $("#loadingOverlay").addClass("d-none")
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

function cargar_items() {
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
                    $${parseFloat(item.compra).toFixed(4)}
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm eliminar" data-id="${item.id}">Eliminar</button>
                </td>
            </tr>
        `);
    });
}

function reiniciar_item() {
    itemNuevo = {
        id: 1,
        tipoItem: null,
        cantidad: null,
        uniMedida: null,
        descripcion: null,
        precioUni: 0,
        codigo: null,
        montoDescu: 0,
        compra: 0,
    };
}