$(document).ready(function () {
    var container = $(document);

    // Mostrar/Ocultar las opciones
    container.on("click", ".selected", function () {
        const svg = $(this).find("svg");
        closeSelects(this, svg);

        if (svg.hasClass("is-rotated")) {
            svg.removeClass("is-rotated").addClass("not-rotated");
        } else {
            svg.removeClass("not-rotated").addClass("is-rotated");
        }

        var selectedItems = $(this).next();

        if (selectedItems) {
            selectedItems.toggleClass("hidden");
            $(this).toggleClass("active");
        }
    });

    // Seleccionar una opción
    container.on("click", ".selectOptions .itemOption", function () {
        let item = $(this).html();
        let value = $(this).data("value");
        let input = $(this).data("input");
        $(this)
            .closest(".selectOptions")
            .prev(".selected")
            .find(".itemSelected")
            .html(item);
        $(input).val(value).trigger("Changed");
        $(this).parent().addClass("hidden");
        $(".arrow-down-select")
            .removeClass("is-rotated")
            .addClass("not-rotated");
    });

    // Filtrar opciones y ajustar height
    container.on("input", ".search-input", function () {
        const query = $(this).val().toLowerCase();
        const options = $(this)
            .closest(".selectOptions")
            .find(".itemOption")
            .not(".search-input");

        let visibleCount = 0;
        options.each(function () {
            const text = $(this).text().toLowerCase();
            if (text.includes(query)) {
                $(this).show();
                visibleCount++;
            } else {
                $(this).hide();
            }
        });

        // Ajustar el height dinámicamente
        const optionsContainer = $(this).closest(".selectOptions");
        const optionHeight = options.first().outerHeight(true) || 0; // Altura de una opción
        const maxVisible = 5; // Número máximo de opciones visibles

        if (visibleCount > 6) {
            optionsContainer
                .removeClass("h-auto")
                .addClass("h-64 overflow-auto");
        } else {
            optionsContainer
                .removeClass("h-64 overflow-auto")
                .addClass("h-auto");
        }

        if (visibleCount === 0) {
            optionsContainer.append(
                ` <li
                    class="itemOption pointer-events-none rounded-lg px-4 py-2.5 text-sm text-gray-900 dark:text-white dark:hover:bg-gray-900">
                    No hay opciones disponibles
                </li>`
            );
        }

        console.log("visibleCount", visibleCount);
    });

    function closeSelects(thisSelect, svg) {
        $(".selectOptions").not($(thisSelect).next()).addClass("hidden");
        $(".arrow-down-select")
            .not(svg)
            .removeClass("is-rotated")
            .addClass("not-rotated");
    }

    // Modificar el evento global para no cerrar si se hace clic en .search-input
    $(document).on("click", function (e) {
        if (
            !$(e.target).closest(".selected").length &&
            !$(e.target).hasClass("search-input")
        ) {
            $(".selectOptions").addClass("hidden");
            $(".selected").removeClass("active");
            $(".arrow-down-select")
                .removeClass("is-rotated")
                .addClass("not-rotated");
        }
    });
});
