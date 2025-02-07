import { Autocomplete } from './autocomplete';
import { MaskInput, Mask } from 'maska';

$(function () {

    const numDocumento = $('#numDocumento');
    let mask = null;
    let actividades = null;
    let paises = null;

    const nrc = $('#nrc');
    new MaskInput(nrc, { mask: '######-#' });

    $.ajax({
        url: '/catalogo/cat_019',
        success: function (response) {
            const field_actividad_economica = document.getElementById('codActividad');
            const ac_actividad_economica = new Autocomplete(field_actividad_economica, {
                data: response,
                maximumItems: 5,
                threshold: 1
            });
            actividades = response;
        }
    })

    $.ajax({
        url: '/catalogo/cat_020',
        success: function (response) {
            const field_pais = document.getElementById('codPais');
            const ac_cod_pais = new Autocomplete(field_pais, {
                data: response,
                maximumItems: 5,
                threshold: 1
            });
            paises = response;
        }
    })


    $.ajax({
        url: '/departamentos/all',
        success: function (apiResponse) {
            // Llenar el select de departamentos
            const departamentoSelect = $('#departamento');
            departamentoSelect.empty();
            apiResponse.forEach(departamento => {
                departamentoSelect.append(new Option(departamento.valores, departamento.codigo));
            });

            // Manejar el cambio del select de departamentos
            departamentoSelect.on('change', function () {
                const selectedDepartamento = $(this).val();
                const municipioSelect = $('#municipio');

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

            departamentoSelect.trigger('change');
        }
    })

    $('#customerTable tfoot th').each(function () {
        //Apply the search except to the last column
        if ($(this).index() < $('#customerTable tfoot th').length - 1) {
            var title = $(this).text();
            $(this).html('<input class="form-control form-control-sm" type="text" placeholder="Buscar ' + title + '" />');
        } else {
            $(this).html('');
        }
    });

    var customerTable = $('#customerTable').DataTable({
        initComplete: function () {
            this.api().columns().every(function () {
                var that = this;
                $('input', this.footer()).on('keyup change clear', function () {
                    if (that.search() !== this.value) {
                        that.search(this.value).draw();
                    }
                });
            });
        },
        "language": {
            "lengthMenu": "Mostrar _MENU_ registros por página",
            "zeroRecords": "No se encontraron resultados",
            "info": "Mostrando página _PAGE_ de _PAGES_",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "search": "Buscar:",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        },
        dom: '<"container-fluid"<"row"<"col"l><"col"B><"col"f>>>rtip',
        buttons: [
            {
                extend: 'print',
                text: 'Imprimir esta tabla',
                title: 'Reporte de Clientes',
            },
        ],
        "order": [[0, "desc"]],
    });

    $("#tipoDocumento").on('change', function () {
        let tipoDoc = $(this).val();
        if (tipoDoc == "36") {
            mask = new MaskInput(numDocumento, { mask: '####-######-###-#' });
        } else if (tipoDoc == "13") {
            mask = new MaskInput(numDocumento, { mask: '########-#' });
        } else {
            mask.destroy()
            mask = null
        }
    });

    $("#clienteExportacion").on('change', function () {
        if ($(this).is(':checked')) {
            $("#codPais").prop('disabled', false);
            $("#tipoPersona").prop('disabled', false);
        } else {
            $("#codPais").prop('disabled', true);
            $("#tipoPersona").prop('disabled', true);
        }
    });

    $("#clienteExportacion").trigger('change');


    $("#formCliente").on('submit', function (e) {
        e.preventDefault();
        let data = $(this).serializeArray();
        if (data.tipoDocumento == "36" || data.tipoDocumento == "13") {
            data.numDocumento = data.numDocumento.replace(/-/g, '');
        }
        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: data,
            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Cliente guardado',
                        text: response.message,
                        confirmButtonText: 'Aceptar'
                    }).then((result) => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al guardar',
                        text: response.message,
                        confirmButtonText: 'Aceptar'
                    });
                }
            }
        });
    });

    $(".frm-delete").on('submit', function (e) {
        e.preventDefault();
        Swal.fire({
            title: '¿Está seguro de eliminar este cliente?',
            text: "¡No podrá revertir esto!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                let data = $(this).serializeArray();
                $.ajax({
                    url: $(this).attr('action'),
                    type: $(this).attr('method'),
                    data: data,
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Cliente eliminado',
                                text: response.message,
                                confirmButtonText: 'Aceptar'
                            }).then((result) => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error al eliminar',
                                text: response.message,
                                confirmButtonText: 'Aceptar'
                            });
                        }
                    }
                });
            }
        })
    });

    $(document).on('show.bs.modal', '#aggCliente', function (event) {
        let button = $(event.relatedTarget);
        let id = button.data('id');
        if(id){
            $.ajax({
                url: '/business/clientes/' + id,
                success: function (response) {
                    $('#formCliente').attr('action', '/business/clientes/' + id);
                    $('#formCliente').attr('method', 'PUT');
                    $('#formCliente').find('#tipoDocumento').val(response.tipoDocumento);
                    $('#formCliente').find('#numDocumento').val(response.numDocumento);
                    $('#formCliente').find('#nombre').val(response.nombre);
                    $('#formCliente').find('#nrc').val(response.nrc);
                    $('#formCliente').find('#nombreComercial').val(response.nombreComercial);

                    const actividad = actividades.find(act => act.value === response.codActividad);
                    if (actividad) {
                        $('#codActividad').val(actividad.label);
                    }
                    $('#formCliente').find('#departamento').val(response.departamento);
                    $('#formCliente').find('#departamento').trigger('change');
                    $('#formCliente').find('#municipio').val(response.municipio);
                    $('#formCliente').find('#complemento').val(response.complemento);
                    $('#formCliente').find('#telefono').val(response.telefono);
                    $('#formCliente').find('#correo').val(response.correo);

                    if(response.codPais){
                        $("#clienteExportacion").prop('checked', true);
                        $("#codPais").prop('disabled', false);
                        $("#tipoPersona").prop('disabled', false);
                        $('#formCliente').find('#tipoPersona').val(parseInt(response.tipoPersona));
                        const pais = paises.find(act => act.value === response.codPais);
                        if (pais) {
                            $('#codPais').val(pais.label);
                        }
                    }
                }
            });
        }
    });
});