import { Autocomplete } from './autocomplete';

let tablaProds = null;
let tablaClientes = null;
let itemSeleccionado = null;
let itemNuevo = null;
let paises = null;
let actividades = null;

let tributos_dte = [];
let total_tributos = 0;

let descuentosTotal = 0;

let items = [];
let reteIva1 = 0;
let reteRenta = 0;
let perciIva1 = 0;


if (localStorage.getItem("items_fex")) {
    items = JSON.parse(localStorage.getItem("items_fex"));
    cargar_items();
    calcular_totales();
}

$(function () {

    // Cargar actividades económicas
    $.ajax({
        url: '/catalogo/cat_020',
        success: function (response) {
            const field_pais = document.getElementById('codPais');
            const ac_pais = new Autocomplete(field_pais, {
                data: response,
                maximumItems: 5,
                threshold: 1,
                fullWidth: true,
            });
            paises = response;
        }
    })

    // Cargar actividades económicas
    $.ajax({
        url: '/catalogo/cat_019',
        success: function (response) {
            const field_actividad = document.getElementById('codActividad');
            const ac_actividad = new Autocomplete(field_actividad, {
                data: response,
                maximumItems: 5,
                threshold: 1,
                fullWidth: true,
            });
            actividades = response;
        }
    }) 

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
        itemNuevo.ventaGravada = subtotal;
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
        itemNuevo.ventaGravada = itemNuevo.precioUni * cantidad;

        items.push(itemNuevo);
        reiniciar_item();
        cargar_items();
        // Guardar items en localStorage
        localStorage.setItem('items_fex', JSON.stringify(items));

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
        localStorage.setItem('items_fex', JSON.stringify(items));
    });

    // Generar DTE y enviarlo
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
                        data: 'precioSinTributos',
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
                        itemSeleccionado.precioUni = itemSeleccionado.precioSinTributos;
                        $("#prodDesc").text(itemSeleccionado.descripcion);
                    }
                });
            });
        }
    })

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
                        if(!response.codPais){
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'El cliente seleccionado no tiene información de exportación en su perfil',
                                showConfirmButton: false,
                                timer: 2000
                            })
                        } else {
                            $("#tipoDoc").val(response.tipoDocumento);
                            $("#nitContribuyente").val(response.numDocumento)
                            $("#nombre").val(response.nombre)
                            $("#tipoPersona").val(response.tipoPersona)
                            $("#complementoContribuyente").val(response.complemento)
                            $("#correoContribuyente").val(response.correo)
                            $("#telefonoContribuyente").val(response.telefono)
                            $("#cerrarModalCliente").trigger("click")

                            const pais = paises.find(act => act.value === response.codPais);
                            if (pais) {
                                $('#codPais').val(pais.label);
                            }

                            const actividad = actividades.find(act => act.value === response.codActividad);
                            if (actividad) {
                                $('#codActividad').val(actividad.label);
                            }
                        }
                    }
                });
            });
        }
    })

    $("#aggitem").on("show.bs.modal", function (e) {
        reiniciar_item();
    });

    $("#aggitem .form-check-input").on("change", function () {
        calcular_tributos_item();
    });

    $("#cantidadExistente, #descuentoExistente").on("change", function () {
        let cantidad = $("#cantidadExistente").val() || 0;
        let descuento = $("#descuentoExistente").val() || 0;
        let subtotal = calcular_subtotal_item(cantidad, itemSeleccionado.precioUni, descuento);

        $("#totalExistente").val(parseFloat(subtotal).toFixed(4));
    });

    $("#btnAgregarProd").on("click", function () {
        let cantidad = $("#cantidadExistente").val() || 0;
        let descuento = $("#descuentoExistente").val() || 0;
        itemSeleccionado.id = items.length + 1;
        itemSeleccionado.cantidad = cantidad;
        itemSeleccionado.montoDescu = descuento;
        itemSeleccionado.ventaGravada = $("#totalExistente").val();

        items.push(itemSeleccionado);
        cargar_items();
        // Guardar items en localStorage
        localStorage.setItem('items_fex', JSON.stringify(items));

        $("#cantidadExistente").val("");
        $("#descuentoExistente").val("");
        $("#totalExistente").val("");
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
                localStorage.removeItem("items_fex")
                window.location = "/business/dashboard"
            }
        });
    })

    $("#seguro, #flete").on("change", function () {
        calcular_totales();
    });

});

function calcular_subtotal_item(cantidad = 0, precio = 0, descuento = 0) {
    return ((cantidad * precio) - descuento).toFixed(4);
}

function calcular_totales() {
    let subTotalGeneral = 0;
    let montoTotalOperacion = 0;
    let totalPagar = 0;
    let seguro = $("#seguro").val() || 0;
    let flete = $("#flete").val() || 0;
    items.forEach(item => {
        subTotalGeneral += parseFloat(item.ventaGravada);
    });
    montoTotalOperacion = subTotalGeneral - descuentosTotal;
    totalPagar = montoTotalOperacion + parseFloat(seguro) + parseFloat(flete);

    $("#subTotalGeneral").text("$" + subTotalGeneral.toFixed(2));
    $("#montoTotalOperacion").text("$" + montoTotalOperacion.toFixed(2));
    $("#totalPagar").text("$" + totalPagar.toFixed(2));
    $("#monto").val(totalPagar);
}

function generar_documento() {
    $("#loadingOverlay").removeClass("d-none")

    const codActividad = paises.find(act => act.label === $('#codActividad').val())?.value;
    const descActividad = $('#codActividad').val().split('-').pop().trim();

    const codPais = paises.find(act => act.label === $('#codPais').val())?.value;
    const nombrePais = $('#codPais').val().split('-').pop().trim();

    const codIncoterms = $('#incoterms').val();
    const descIncoterms = $('#incoterms option:selected').text();

    let receptor = {
        "tipoDocumento": $("#tipoDoc").val(),
        "numDocumento": $("#nitContribuyente").val(),
        "nombre": $("#nombre").val(),
        "nombreComercial": $("#nombre").val(),
        "descActividad": descActividad,
        "codPais": codPais,
        "nombrePais": nombrePais,
        "complemento": $("#complementoContribuyente").val(),
        "telefono": $("#telefonoContribuyente").val(),
        "correo": $("#correoContribuyente").val(),
        "tipoPersona": $("#tipoPersona").val(),
    }

    let dte = {
        "nit": $("#nit").val(),
        "emisor": {
            "regimen": $("#regimen").val(),
            "recintoFiscal": $("#recinto").val(),
            "tipoItemExpor": $("#tipoItemExpor").val(),
        },
        "receptor": receptor,
        "cuerpoDocumento": [],
        "otrosDocumentos": null,
        "resumen": {
            "porcentajeDescuento": 0,
            "condicionOperacion": 1,
            "codIncoterms": codIncoterms,
            "descIncoterms": descIncoterms,
            "flete": $("#flete").val(),
            "seguro": $("#seguro").val(),
            "descuento": 0
        },
        "apendice": null
    }

    items.forEach(item => {
        dte.cuerpoDocumento.push({
            "cantidad": item.cantidad,
            "codigo": item.codigo || null,
            "uniMedida": item.uniMedida,
            "descripcion": item.descripcion,
            "precioUni": item.precioUni,
            "montoDescu": item.montoDescu,
            "ventaGravada": item.ventaGravada,
            "noGravado": 0
        });
    });

    $.ajax({
        url: "/business/factura?dte=factura_exportacion",
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
                        localStorage.removeItem("items_fex")
                        localStorage.removeItem("reteIva1")
                        localStorage.removeItem("reteRenta")
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
                    $${parseFloat(item.ventaGravada).toFixed(4)}
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
        codigo: null,
        uniMedida: null,
        descripcion: null,
        precioUni: 0,
        montoDescu: 0,
        ventaGravada: 0
    };
}