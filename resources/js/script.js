$(document).ready(function () {
    $("#toggle-sidebar").on("click", function () {
        $("#sidebar").toggleClass("collapsed");
        $("#only-icon").toggleClass("hidden");
        $("#icon-complete").toggleClass("hidden");
        $(".business").toggleClass("collapsed-sidebar");
        $("#navbar").toggleClass(
            "lg:w-calc-full-minus-64 lg:w-calc-full-minus-16"
        );
        $(".tooltip").toggleClass("hidden");
        if ($("#sidebar").hasClass("collapsed")) {
            localStorage.setItem("sidebarCollapsed", "true");
        } else {
            localStorage.setItem("sidebarCollapsed", "false");
        }
    });

    window.addEventListener("resize", function () {
        if (window.innerWidth < 1024) {
            $("#sidebar").removeClass("collapsed");
            $("#only-icon").addClass("hidden");
            $("#icon-complete").removeClass("hidden");
            $(".business").removeClass("collapsed-sidebar");
            $(".tooltip").addClass("hidden");
        }
    });

    if (localStorage.getItem("sidebarCollapsed") === "true") {
        $("#sidebar").addClass("collapsed");
        $("#only-icon").removeClass("hidden");
        $("#icon-complete").addClass("hidden");
        $(".business").addClass("collapsed-sidebar");
        $("#navbar")
            .removeClass("lg:w-calc-full-minus-64")
            .addClass("lg:w-calc-full-minus-16");
        $(".tooltip").removeClass("hidden");
    }

    setTimeout(() => {
        $("#sidebar").css({
            opacity: "1",
            visibility: "visible",
        });

        $(".business").css({
            opacity: "1",
            visibility: "visible",
        });

        $("#navbar").css({
            opacity: "1",
            visibility: "visible",
        });
    }, 50);

    const $alert = $(".alert");
    $(".alert-close").click(function () {
        $alert.hide();
    });

    $(document).on("click", ".toggle-password", function () {
        const input = $(this).prev();
        const eye = $(this).find("#eye-icon");
        const eyeClosed = $(this).find("#eye-closed-icon");

        if (input.attr("type") === "password") {
            input.attr("type", "text");
            eye.addClass("hidden");
            eyeClosed.removeClass("hidden");
        } else {
            input.attr("type", "password");
            eye.removeClass("hidden");
            eyeClosed.addClass("hidden");
        }
    });

    $(document).on("change", ".input-doc", function () {
        const $file = $(this);
        const file = $file[0].files[0];

        console.log($file.data("name"), $file.data("button-remove"));

        const $fileName = $($file.data("name"));
        const $btnRemoveFile = $($file.data("button-remove"));
        const maxMB = $file.data("max-mb");

        if (file) {
            $fileName.text(file.name);
            if (file.size > maxMB * 1024) {
                $file.val("");
                $fileName.text(
                    "No se permiten archivos mayores a " + maxMB / 1024 + "MB"
                );
                return;
            }
            $btnRemoveFile.removeClass("hidden");
        }
    });

    $(document).on("click", ".remove-file", function () {
        const $btnRemoveFile = $(this);
        const $file = $($btnRemoveFile.data("input"));
        const $fileName = $($file.data("name"));
        const accept = $file.attr("accept");

        $file.val("");
        $fileName.text("Formatos permitidos: " + accept);
        $btnRemoveFile.addClass("hidden");
    });

    $(".show-options").on("click", function (event) {
        event.stopPropagation();
        var target = $(this).data("target");
        var options = $(target);
        $(".options").not(options).addClass("hidden");
        $(".show-options")
            .not(this)
            .find("svg")
            .removeClass("is-rotated")
            .addClass("not-rotated");

        const align = $(this).data("align")?.toLowerCase() || "left";

        if (!options.parent().is("body")) {
            $("body").append(options);
        }

        const svg = $(this).find("svg");
        if (svg.hasClass("is-rotated")) {
            svg.removeClass("is-rotated").addClass("not-rotated");
        } else {
            svg.removeClass("not-rotated").addClass("is-rotated");
        }

        var buttonOffset = $(this).offset();

        if (align === "left") {
            options.css({
                position: "absolute",
                top: buttonOffset.top + $(this).outerHeight(),
                left:
                    buttonOffset.left -
                    options.outerWidth() +
                    $(this).outerWidth(),
                zIndex: 9999,
            });
        } else {
            options.css({
                position: "absolute",
                top: buttonOffset.top + $(this).outerHeight(),
                left: buttonOffset.left,
                zIndex: 9999,
            });
        }

        options.toggleClass("hidden animate-fade-down animate-duration-300");
    });

    $(document).on("click", function (event) {
        if (!$(event.target).closest(".show-options").length) {
            $(".options").addClass("hidden");
            $(".show-options svg")
                .removeClass("is-rotated")
                .addClass("not-rotated");
        }
    });

    $(document).on("click", ".buttonDelete", function () {
        $(".deleteModal").removeClass("hidden").addClass("flex");
        let formId = $(this).data("form");
        $(".deleteModal .confirmDelete").attr("data-form", formId);
        $("body").addClass("overflow-hidden");
    });

    $(document).on("click", ".closeModal", function () {
        $(".deleteModal").removeClass("flex").addClass("hidden");
        $("body").removeClass("overflow-hidden");
    });

    $(document).on("click", ".confirmDelete", function () {
        let formId = $(this).data("form");
        $("#" + formId).submit();
        $("body").removeClass("overflow-hidden");
    });

    //Customer create JS

    if ($("#export-data").is(":checked")) {
        $("#export-data-container").removeClass("hidden");
    } else {
        $("#export-data-container").addClass("hidden");
    }

    $("#export-data").on("click", function () {
        if ($(this).is(":checked")) {
            $("#export-data-container").removeClass("hidden");
        } else {
            $("#export-data-container").addClass("hidden");
        }
    });

    // Show modals
    $(document).on("click", ".show-modal", function () {
        const target = $(this).data("target");
        $(target).removeClass("hidden").addClass("flex");
        $("body").addClass("overflow-hidden");
    });

    $(document).on("click", ".hide-modal", function () {
        const target = $(this).data("target");
        $(target).removeClass("flex").addClass("hidden");
        $("body").removeClass("overflow-hidden");
        const form = $(this).closest("form");
        form.trigger("reset");
    });

    // Drawers
    $(document).on("click", ".show-drawer", function () {
        const target = $(this).data("target");
        $(target).removeClass("-translate-x-full");
        $("#overlay").removeClass("hidden");
        $("body").addClass("overflow-hidden");
    });

    $(document).on("click", ".show-drawer-left", function () {
        const target = $(this).data("target");
        $(target).removeClass("translate-x-full");
        $("#overlay").removeClass("hidden");
        $("body").addClass("overflow-hidden");
    });

    $(document).on("click", ".hide-drawer", function () {
        const target = $(this).data("target");
        $(target).addClass("-translate-x-full");
        $("#overlay").addClass("hidden");
        $("body").removeClass("overflow-hidden");
    });

    $(document).on("click", ".hide-drawer-left", function () {
        const target = $(this).data("target");
        $(target).addClass("translate-x-full");
        $("#overlay").addClass("hidden");
        $("body").removeClass("overflow-hidden");
    });

    // Botones dinámicos para anular, enviar email y WhatsApp
    $(document).on(
        "click",
        ".btn-anular-dte, .btn-send-email, .btn-send-whatsapp",
        function () {
            const id = $(this).data("id");
            $("#cod-generacion-anular").val(id);
            $("#cod-generacion-email").val(id);
            $("#cod-generacion-whatsapp").val(id);
        }
    );
});
