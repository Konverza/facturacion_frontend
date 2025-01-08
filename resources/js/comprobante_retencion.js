import { Autocomplete } from './autocomplete';

let tablaClientes = null;
let actividades = null;
let documentosRelacionados = [];
let montoSujetoGrav = 0;
let montoIVA = 0;


let tiposDoc = [
    { numero: "01", descripcion: "Factura" },
    { numero: "03", descripcion: "Comprobante de Crédito Fiscal" },
]

let tiposGeneracion = [
    { numero: 1, descripcion: "Físico" },
    { numero: 2, descripcion: "Electrónico" },
]

let codigosRetencion = [
    { numero: "22", descripcion: "Retención de IVA 1%" },
    { numero : "C9", descripcion: "Otras Retenciones IVA casos especiales" },
]

let dtesResultado = []

$(function () {

    // Cargar actividades económicas
    $.ajax({
        url: '/catalogo/cat_019',
        success: function (response) {
            const field_actividad_economica = document.getElementById('codActividad');
            const ac_actividad_economica = new Autocomplete(field_actividad_economica, {
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

                        $("#tipoDoc").val(response.tipoDocumento);
                        if (response.tipoDocumento == "13") {
                            $("#nitContribuyente").val(response.numDocumento.slice(0, -1) + '-' + response.numDocumento.slice(-1))
                        } else {
                            $("#nitContribuyente").val(response.numDocumento)
                        }
                        $("#nrcContribuyente").val(response.nrc.replace(/-/g, ''))
                        $("#nombre").val(response.nombre)
                        $("#nombreComercial").val(response.nombreComercial)
                        $("#departamentoContribuyente").val(response.departamento)
                        $("#departamentoContribuyente").trigger("change")
                        $("#municipioContribuyente").val(response.municipio)
                        $("#complementoContribuyente").val(response.complemento)
                        $("#correoContribuyente").val(response.correo)
                        $("#telefonoContribuyente").val(response.telefono)
                        $("#cerrarModalCliente").trigger("click")

                        // Search actividades económicas, given that the structure is {value: '', label: ''}
                        const actividad = actividades.find(act => act.value === response.codActividad);
                        if (actividad) {
                            $('#codActividad').val(actividad.label);
                        }
                    }
                });
            });
        }
    })

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
                localStorage.removeItem("items_nc")
                localStorage.removeItem("reteIva1_nc")
                localStorage.removeItem("reteRenta_nc")
                window.location = "/business/dashboard"
            }
        });
    })

    // Documentos relacionados
    $("#guardarDocFisico").on("click", function () {
        documentosRelacionados.push({
            "tipoDte": $("#tipoDocumentoFisico").val(),
            "tipoDoc": 1,
            "numDocumento": $("#numeroDocumentoFisico").val(),
            "fechaEmision": $("#fechaEmisionFisico").val(),
            "montoSujetoGrav": $("#montoRetencionFisico").val(),
            "codigoRetencionMH": $("#tipoRetencionFisico").val(),
            "ivaRetenido": $("#montoIVAFisico").val(),
            "descripcion": $("#descripcionDocumentoFisico").val()
        });
        montoSujetoGrav += parseFloat($("#montoRetencionFisico").val());
        montoIVA += parseFloat($("#montoIVAFisico").val());
        mostrar_documentos_relacionados();
    });

    $("#docElectronico").on("show.bs.modal", function (e) {
        if($("#nitContribuyente").val() == ""){
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Debe ingresar el NIT del contribuyente antes de continuar',
            })
            e.preventDefault()
        } else {
            $("#nitBusqueda").val($("#nitContribuyente").val());
        }
    });

    $("#buscarDTE").on("click", function () {
        $("#loadingOverlay").removeClass("d-none")
        $.ajax({
            url: "/business/buscar_dte",
            method: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                nitBusqueda: $("#nitBusqueda").val(),
                tipoDocumentoElectronico: $("#tipoDocumentoElectronico").val(),
                desdeBusqueda: $("#desdeBusqueda").val(),
                hastaBusqueda: $("#hastaBusqueda").val()
            },
            success: function(response){
                dtesResultado = response;
                $("#loadingOverlay").addClass("d-none")
                if(dtesResultado.length > 0){
                    $("#resultadosDte").html("");
                    dtesResultado.forEach(dte => {
                        $("#resultadosDte").append(`
                            <tr>
                                <td>${dte.fecha_emision}</td>
                                <td>${dte.codigo_generacion}</td>
                                <td>$${dte.monto.toFixed(2)}</td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm btnSeleccionarDTE" data-id="${dte.codigo_generacion}">Seleccionar</button>
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    $("#resultadosDte").html(`
                        <tr>
                            <td colspan="4" class="text-center">No se encontraron resultados</td>
                        </tr>
                    `);
                }
            }
        });
    });

    $(document).on("click", ".btnSeleccionarDTE", function () {
        let dteSeleccionado = dtesResultado.find(dte => dte.codigo_generacion == $(this).data("id"));
        documentosRelacionados.push({
            "tipoDocumento": dteSeleccionado.tipo_dte,
            "tipoGeneracion": 2,
            "numeroDocumento": dteSeleccionado.codigo_generacion,
            "fechaEmision": dteSeleccionado.fecha_emision
        });
        mostrar_documentos_relacionados();
    });
});

function generar_documento() {
    $("#loadingOverlay").removeClass("d-none")

    const codActividad = actividades.find(act => act.label === $('#codActividad').val())?.value;
    const descActividad = $('#codActividad').val().split('-').pop().trim();

    let receptor = {
        "nombre": $("#nombre").val(),
        "nombreComercial": $("#nombreComercial").val(),
        "codActividad": codActividad,
        "descActividad": descActividad,
        "telefono": $("#telefonoContribuyente").val(),
        "correo": $("#correoContribuyente").val(),
        "direccion": {
            "departamento": $("#departamentoContribuyente").val(),
            "municipio": $("#municipioContribuyente").val(),
            "complemento": $("#complementoContribuyente").val()
        },
        "tipoDocumento": $("#tipoDoc").val(),
        "numDocumento": $("#nitContribuyente").val(),
        "nrc": $("#nrcContribuyente").val(),
    }

    let dte = {
        "nit": $("#nit").val(),
        "receptor": receptor,
        "cuerpoDocumento": documentosRelacionados,
        "resumen": {
            "totalSujetoRetencion": montoSujetoGrav,
            "totalIVAretenido": montoIVA
        },
        "extension": null,
        "apendice": null,
    }

    $.ajax({
        url: "/business/factura?dte=comprobante_retencion",
        method: "POST",
        data: JSON.stringify(dte),
        contentType: "application/json",
        success: function (response) {
            if (response.status == 201) {
                if (response.message.estado == "PROCESADO") {
                    Swal.fire({
                        icon: 'success',
                        title: 'Comprobante de Retención generado',
                        text: 'El Comprobante de Retención ha sido generado exitosamente',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        $("#loadingOverlay").addClass("d-none")
                        window.location.href = "/business/dtes";
                    })
                } else if (response.message.estado == "CONTINGENCIA") {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Comprobante de Retención generado en CONTINGENCIA',
                        text: 'Se generó el Comprobante de Retención, pero no se envió a MH',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        $("#loadingOverlay").addClass("d-none")
                        window.location.href = "/business/dtes";
                    })
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Comprobante de Retención Rechazado',
                        text: `Motivo: ${response.message.observaciones}`,
                    }).then(() => {
                        $("#loadingOverlay").addClass("d-none")
                    })
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'El Comprobante de Retención no se envió',
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
                text: 'Ha ocurrido un error al generar el Comprobante de Retención',
                showConfirmButton: false,
                timer: 2000
            })
        }
    });
}

function mostrar_documentos_relacionados(){
    let tbody = $("#documentosRelacionados");
    let selectNuevo = $("#documentoRelacionado");
    let selectExistente = $("#documentoRelacionadoExistente");
    tbody.empty();
    selectNuevo.empty();
    documentosRelacionados.forEach(doc => {
        tbody.append(`
            <tr>
                <td>${
                    tiposGeneracion.find(tipo => tipo.numero === doc.tipoDoc).descripcion
                }</td>
                <td>${
                    tiposDoc.find(tipo => tipo.numero === doc.tipoDte).descripcion
                }</td>
                <td>${doc.numDocumento}</td>
                <td>${
                    codigosRetencion.find(tipo => tipo.numero === doc.codigoRetencionMH).descripcion
                }</td>
                <td>${doc.descripcion}</td>
                <td>${doc.fechaEmision}</td>
                <td>$${doc.montoSujetoGrav}</td>
                <td>$${doc.ivaRetenido}</td>
            </tr>
        `);
    });

}