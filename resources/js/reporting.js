document.addEventListener("DOMContentLoaded", function () {
    $("#form-generate-book").submit(function () {
        $("#text-loader").text("Generando libro de ventas...");
        setTimeout(() => {
            $("#loader").addClass("hidden");
        }, 2000);
    });

    $("#book-type").on("Changed", function () {
        const value = $(this).val();
        $(".container-books").addClass("hidden");
        $(".container-anexos").addClass("hidden");

        switch (value) {
            case "anexos_f07":
                $("#container-tipo-anexo").removeClass("hidden");
                break;
            case "contribuyentes":
                $("#container-ventas-contribuyentes").removeClass("hidden");
                break;
            case "consumidores":
                $("#container-ventas-consumidor-final").removeClass("hidden");
                break;
            case "retencion_iva":
                $("#container-retencion-iva").removeClass("hidden");
                break;
            case "compras":
                $("#container-compras").removeClass("hidden");
                break;
            case "percepcion_iva":
                $("#container-percepcion-iva").removeClass("hidden");
                break;
        }
    });

    $("#anexo-type").on("Changed", function () {
        const value = $(this).val();
        $(".container-anexos").addClass("hidden");

        switch (value) {
            case "contribuyentes":
            case "consumidores":
                $("#container-tipo-operacion-ingreso").removeClass("hidden");
                break;
            case "compras":
            case "compras_se":
                $("#container-tipo-operacion-egreso").removeClass("hidden");
                break;
        }
    });
});