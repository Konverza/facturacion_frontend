$(function () {
    $("#dteGenerar").on("change", function () {
        if($(this).val() != ""){
            $("#btnGenerar").prop("disabled", false);
        } else {
            $("#btnGenerar").prop("disabled", true);
        }
    });

    $("#btnGenerar").on("click", function () {
        let dte = $("#dteGenerar").val();
        let url = `/business/dte/?dte=${dte}`;
        window.location.href = url;
    });
});