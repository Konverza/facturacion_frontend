import { Autocomplete } from './autocomplete';

$(function () {
    $.ajax({
        url: '/catalogo/cat_014',
        success: function (response) {
            const field_uniMedida = document.getElementById('uniMedida');
            const ac_uniMedida = new Autocomplete(field_uniMedida, {
                data: response,
                maximumItems: 5,
                threshold: 1
            });
        }
    })

    $("#precioSinTributos").on("change", function () {
        const precioSinTributos = $(this).val();
        const precioConTributos = precioSinTributos * 1.13;
        $("#precioUni").val(precioConTributos.toFixed(8));
    });

    $("#precioUni").on("change", function () {
        const precioConTributos = $(this).val();
        const precioSinTributos = precioConTributos / 1.13;
        $("#precioSinTributos").val(precioSinTributos.toFixed(8));
    });

    $("#formProducto").on("submit", function (e) {
        e.preventDefault();
        const form = $(this);
        $("#20").prop('disabled', false);
        const formData = form.serializeArray();
        console.log(formData);
        $("#20").prop('disabled', true);
        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            data: formData,
            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Producto guardado',
                        text: 'El producto se guardó exitosamente',
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    }).then((result) => {
                        window.location.reload();
                    })
                } else {
                    Swal.fire({
                        title: 'Error al guardar el producto',
                        text: 'Hubo un error al guardar el producto',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    })
                }
            },
            error: function (xhr, status, error) {
                Swal.fire({
                    title: 'Error al guardar el producto',
                    text: 'Hubo un error al guardar el producto',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                })
            }
        });
    });


    $('#productosTable tfoot th').each(function () {
        //Apply the search except to the last column
        if ($(this).index() < $('#productosTable tfoot th').length - 1) {
            var title = $(this).text();
            $(this).html('<input class="form-control form-control-sm" type="text" placeholder="Buscar ' + title + '" />');
        }else{
            $(this).html('');
        }
    });

    var productosTable = $('#productosTable').DataTable({
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
                title: 'Reporte de Productos',
            },
        ],
        "order": [[ 0, "desc" ]],
    });
});