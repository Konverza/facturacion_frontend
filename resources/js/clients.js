import { Autocomplete } from './autocomplete';

$(function () {
    $.ajax({
        url: "/admin/negocios/json",
        success: function(response) {
            const field_negocios = document.getElementById("nombreNegocio");
            const ac_negocios = new Autocomplete(field_negocios, {
                data: response,
                maximumItems: 5,
                threshold: 1,
                onSelectItem: ({ label, value }) => {
                    console.log("user selected:", label, value);
                    let val = $("#nombreNegocio").val();
                }
            });
        }
    })

    $("#usuarioNegocio, #usuarioKonverza").on("change", function() {
        const tipoUsuario = $("input[name='tipoUsuario']:checked").val();
        if (tipoUsuario == "2") {
            $("#negocioLocal").removeClass("d-none");
        } else {
            $("#negocioLocal").addClass("d-none");
        }
    });
});