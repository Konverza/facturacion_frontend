import { showAlert } from "./alert";

$(document).ready(function () {
    const regLowercase = /[a-z]/;
    const regUppercase = /[A-Z]/;
    const regNumber = /[0-9]/;
    const regSpecialChar = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/;

    function validatePasswordStrength(value) {
        let strength = 0;
        let requirements = [];
        let isValid = true;

        $("#password-requirements").empty();

        if (value.length >= 8) {
            requirements.push("Longitud mínima de 8 caracteres");
            strength += 1;
        } else {
            $("#password-requirements").append(
                "<li class='text-red-500 font-dine-r'>Longitud mínima de 8 caracteres</li>"
            );
        }

        if (regLowercase.test(value)) {
            requirements.push("Contiene letras minúsculas");
            strength += 1;
        } else {
            $("#password-requirements").append(
                "<li class='text-red-500 font-dine-r'>Contiene letras minúsculas</li>"
            );
        }

        if (regUppercase.test(value)) {
            requirements.push("Contiene letras mayúsculas");
            strength += 1;
        } else {
            $("#password-requirements").append(
                "<li class='text-red-500 font-dine-r'>Contiene letras mayúsculas</li>"
            );
        }

        if (regNumber.test(value)) {
            requirements.push("Contiene números");
            strength += 1;
        } else {
            $("#password-requirements").append(
                "<li class='text-red-500 font-dine-r'>Contiene números</li>"
            );
        }

        if (regSpecialChar.test(value)) {
            requirements.push("Contiene caracteres especiales");
            strength += 1;
        } else {
            $("#password-requirements").append(
                "<li class='text-red-500 font-dine-r'>Contiene caracteres especiales</li>"
            );
        }

        requirements.forEach(function (requirement) {
            $("li:contains('" + requirement + "')")
                .removeClass("text-red-500")
                .addClass("text-green-500");
        });

        if (requirements.length === 5) {
            $("#password-requirements").append(
                '<li class="text-green-500">Contraseña segura</li>'
            );
        }

        let strengthPercentage = 0;
        let strengthColor = "bg-red-500";

        switch (strength) {
            case 0:
            case 1:
                strengthPercentage = 20;
                strengthColor = "bg-red-500";
                break;
            case 2:
                strengthPercentage = 40;
                strengthColor = "bg-orange-500";
                break;
            case 3:
                strengthPercentage = 60;
                strengthColor = "bg-yellow-500";
                break;
            case 4:
                strengthPercentage = 80;
                strengthColor = "bg-blue-500";
                break;
            case 5:
                strengthPercentage = 100;
                strengthColor = "bg-green-500";
                break;
        }

        $("#password-strength-bar")
            .css("width", `${strengthPercentage}%`)
            .removeClass()
            .addClass(`h-full rounded ${strengthColor}`);

        if (strengthPercentage === 100) {
            isValid = true;
        } else {
            isValid = false;
        }

        return isValid;
    }

    $("#new-password").on("keyup", function () {
        validatePasswordStrength($(this).val());
    });

    $("#confirm-password").on("keyup", function () {
        const value = $(this).val();
        const newPassword = $("#new-password").val();
        if (newPassword === value) {
            $("#confirm-password")
                .removeClass("is-invalid")
                .addClass("is-valid");
            $("#password-match-message").text("");
        } else {
            $("#confirm-password")
                .addClass("is-invalid")
                .removeClass("is-valid");

            $("#password-match-message").text("Las contraseñas no coinciden");
        }
    });

    $("#password").on("keyup", function () {
        if ($("#password").val() !== "") {
            $("#password").removeClass("is-invalid");
            $("#password-error-message").text("");
        }
    });

    $("#resetPasswordButton").on("click", function () {
        const form = $(this).closest("form");
        let isValid = validatePasswordStrength($("#new-password").val());

        if ($("#confirm-password").val() !== $("#new-password").val()) {
            isValid = false;
        } else {
            $("#confirm-password").removeClass("is-invalid");
            $("#password-match-message").text("");
        }

        if (isValid) {
            $.ajax({
                url: form.attr("action"),
                method: form.attr("method"),
                data: form.serialize(),
                success: function (response) {
                    if (response.success) {
                        $(".confirmPasswordModal")
                            .removeClass("hidden")
                            .addClass("flex");
                    } else {
                        showAlert("error", "Error", response.error);
                    }
                },
                error: function (error) {
                    console.log(error);
                    showAlert("error", "Error", error.responseJSON.error);
                },
            });
        } else {
            showAlert(
                "error",
                "Error",
                "Ingresa una contraseña válida y asegúrate de que las contraseñas coincidan"
            );
        }
    });
});
