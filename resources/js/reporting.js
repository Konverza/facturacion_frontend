document.addEventListener("DOMContentLoaded", function () {
    const form = $("#form-generate-book");
    const previewContainer = $("#anexo-preview-container");
    const previewHead = $("#anexo-preview-head");
    const previewBody = $("#anexo-preview-body");
    const topScroll = $("#anexo-preview-top-scroll");
    const topScrollContent = $("#anexo-preview-top-scroll-content");
    const bottomScroll = $("#anexo-preview-bottom-scroll");
    const previewTable = $("#anexo-preview-table");
    const downloadButton = $("#btn-download-anexo");

    const state = {
        tipoAnexo: null,
        columns: [],
        rows: [],
        editableColumns: {},
    };

    function escapeHtml(value) {
        return String(value ?? "")
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function setLoadingText(text) {
        $("#text-loader").text(text);
        $("#loader").removeClass("hidden");
    }

    function hideLoadingSoon() {
        setTimeout(() => {
            $("#loader").addClass("hidden");
        }, 400);
    }

    function resetPreview() {
        state.tipoAnexo = null;
        state.columns = [];
        state.rows = [];
        state.editableColumns = {};
        previewHead.empty();
        previewBody.empty();
        topScrollContent.width(0);
        topScroll.scrollLeft(0);
        bottomScroll.scrollLeft(0);
        previewContainer.addClass("hidden");
    }

    function syncTopScrollbarWidth() {
        const tableWidth = previewTable.outerWidth() || 0;
        topScrollContent.width(tableWidth);
    }

    function renderPreviewTable() {
        previewHead.empty();
        previewBody.empty();

        const headCells = ['<tr><th class="px-2 py-2 text-left font-semibold text-gray-700 dark:text-gray-200">#</th>'];
        state.columns.forEach((column) => {
            headCells.push(`<th class="px-2 py-2 text-left font-semibold text-gray-700 dark:text-gray-200 whitespace-nowrap">${escapeHtml(column.label)} (${escapeHtml(column.key)})</th>`);
        });
        headCells.push("</tr>");
        previewHead.html(headCells.join(""));

        const bodyRows = [];
        state.rows.forEach((row, rowIndex) => {
            const cells = [`<tr><td class="px-2 py-2 align-top text-gray-600 dark:text-gray-300">${rowIndex + 1}</td>`];

            state.columns.forEach((column) => {
                const key = column.key;
                const value = row[key] ?? "";
                const editableOptions = state.editableColumns[key] || null;

                if (editableOptions) {
                    const optionsHtml = Object.entries(editableOptions)
                        .map(([optionValue, optionLabel]) => {
                            const selected = String(value) === String(optionValue) ? "selected" : "";
                            return `<option value="${escapeHtml(optionValue)}" ${selected}>${escapeHtml(optionLabel)}</option>`;
                        })
                        .join("");

                    cells.push(`<td class="px-2 py-2 align-top min-w-[180px]"><select class="w-full rounded border border-gray-300 bg-white p-1 text-xs dark:border-gray-600 dark:bg-gray-800 js-anexo-editable" data-row-index="${rowIndex}" data-column-key="${escapeHtml(key)}">${optionsHtml}</select></td>`);
                } else {
                    cells.push(`<td class="px-2 py-2 align-top whitespace-nowrap text-gray-700 dark:text-gray-200">${escapeHtml(value)}</td>`);
                }
            });

            cells.push("</tr>");
            bodyRows.push(cells.join(""));
        });

        previewBody.html(bodyRows.join(""));
        syncTopScrollbarWidth();
        previewContainer.removeClass("hidden");
    }

    function toggleContainersByBook() {
        const value = $(this).val();
        $(".container-books").addClass("hidden");
        $(".container-anexos").addClass("hidden");
        resetPreview();

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
    }

    function toggleAnexoOptions() {
        const value = $(this).val();
        $(".container-anexos").addClass("hidden");
        resetPreview();

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
    }

    $("#book-type").on("Changed", toggleContainersByBook);
    $("#book-type").on("change", toggleContainersByBook);

    $("#anexo-type").on("Changed", toggleAnexoOptions);
    $("#anexo-type").on("change", toggleAnexoOptions);

    form.on("submit", function (event) {
        const bookType = $("#book-type").val();
        const previewRoute = form.data("preview-route");

        if (bookType !== "anexos_f07") {
            setLoadingText("Generando libro contable...");
            hideLoadingSoon();
            return;
        }

        event.preventDefault();
        setLoadingText("Generando previsualización del anexo...");

        const payload = new FormData(form[0]);

        axios
            .post(previewRoute, payload)
            .then((response) => {
                const data = response.data;
                state.tipoAnexo = data.tipo_anexo;
                state.columns = data.columns || [];
                state.rows = data.rows || [];
                state.editableColumns = data.editable_columns || {};

                renderPreviewTable();
            })
            .catch((error) => {
                resetPreview();
                const message = error?.response?.data?.message || "No fue posible generar la previsualización del anexo.";
                alert(message);
            })
            .finally(() => {
                hideLoadingSoon();
            });
    });

    previewBody.on("change", ".js-anexo-editable", function () {
        const rowIndex = Number($(this).data("row-index"));
        const columnKey = $(this).data("column-key");
        if (!Number.isNaN(rowIndex) && state.rows[rowIndex] && columnKey) {
            state.rows[rowIndex][columnKey] = $(this).val();
        }
    });

    let syncingScroll = false;
    topScroll.on("scroll", function () {
        if (syncingScroll) {
            return;
        }
        syncingScroll = true;
        bottomScroll.scrollLeft(topScroll.scrollLeft());
        syncingScroll = false;
    });

    bottomScroll.on("scroll", function () {
        if (syncingScroll) {
            return;
        }
        syncingScroll = true;
        topScroll.scrollLeft(bottomScroll.scrollLeft());
        syncingScroll = false;
    });

    $(window).on("resize", function () {
        syncTopScrollbarWidth();
    });

    downloadButton.on("click", function () {
        const downloadRoute = form.data("download-route");

        if (!state.tipoAnexo || state.rows.length === 0) {
            alert("Primero debes generar la previsualización del anexo.");
            return;
        }

        setLoadingText("Generando CSV final...");

        axios
            .post(
                downloadRoute,
                {
                    tipo_anexo: state.tipoAnexo,
                    rows: state.rows,
                },
                {
                    responseType: "blob",
                }
            )
            .then((response) => {
                const contentDisposition = response.headers["content-disposition"] || "";
                const fileNameMatch = contentDisposition.match(/filename="?([^\"]+)"?/i);
                const fileName = fileNameMatch ? fileNameMatch[1] : `anexo_${Date.now()}.csv`;

                const url = window.URL.createObjectURL(new Blob([response.data]));
                const anchor = document.createElement("a");
                anchor.href = url;
                anchor.setAttribute("download", fileName);
                document.body.appendChild(anchor);
                anchor.click();
                anchor.remove();
                window.URL.revokeObjectURL(url);
            })
            .catch((error) => {
                const message = error?.response?.data?.message || "No fue posible descargar el CSV del anexo.";
                alert(message);
            })
            .finally(() => {
                hideLoadingSoon();
            });
    });
});