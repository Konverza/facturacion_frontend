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
                    alert('Producto guardado exitosamente');
                    form[0].reset();
                    console.log(response);
                } else {
                    alert('Hubo un error al guardar el producto');
                    console.log(response);
                }
            },
            error: function (xhr, status, error) {
                alert('Hubo un error al guardar el producto');
                console.log(error);
            }
        });
    });
});