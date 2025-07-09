$(document).ready(function () {
    const $divSucursal = $("#select-sucursal");
    const $divPuntoVenta = $("#select-punto-venta");


    $(document).on("Changed", "#business_id", function () {
        const action = $(this).data("action");
        const businessId = $(this).val();
        handleAjaxRequest(action, businessId, $divSucursal);
    });

    $(document).on("Changed", "#sucursal", function () {
        const action = $(this).data("action");
        const sucursalId = $(this).val();
        handleAjaxRequest(action, sucursalId, $divPuntoVenta);
    });

    function handleAjaxRequest(action, id, select) {
        $.ajax({
            url: action,
            type: "GET",
            data: {
                id: id
            },
            success: function (response) {
                select.html(response.html);
            },
            error: function () {
                console.log("Error");
            }
        });
    }
});