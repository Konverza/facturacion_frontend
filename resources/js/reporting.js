document.addEventListener("DOMContentLoaded", function () {
    const dropZone = document.getElementById("drop-zone");
    const fileInput = document.getElementById("file-input");
    const browseBtn = document.getElementById("browse-files");
    const dropContent = document.getElementById("drop-content");
    const filesList = document.getElementById("files-list");
    const filesContainer = document.getElementById("files-container");
    const inputSelectedDocuments =
        document.getElementById("selected-documents");

    let selectedFiles = [];
    let dragCounter = 0;

    ["dragenter", "dragover", "dragleave", "drop"].forEach((eventName) => {
        dropZone.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    // Manejar entrada del drag
    dropZone.addEventListener("dragenter", function (e) {
        dragCounter++;
        if (dragCounter === 1) {
            document.getElementById("drop-title").textContent =
                "Soltar archivos";
            dropZone.classList.add("drag-zone-active");
        }
    });

    // Manejar salida del drag
    dropZone.addEventListener("dragleave", function (e) {
        dragCounter--;
        if (dragCounter === 0) {
            document.getElementById("drop-title").textContent =
                "Arrastra y suelta tus archivos aquí";
            dropZone.classList.remove("drag-zone-active");
        }
    });

    // Manejar dragover
    dropZone.addEventListener("dragover", function (e) {
        // Solo para prevenir default
    });

    dropZone.addEventListener("drop", handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        document.getElementById("drop-title").textContent =
            "Arrastra y suelta tus archivos aquí";
        dragCounter = 0;
        handleFiles(files);
    }

    browseBtn.addEventListener("click", () => {
        fileInput.click();
    });

    dropZone.addEventListener("click", function () {
        fileInput.click();
    });

    fileInput.addEventListener("change", (e) => {
        handleFiles(e.target.files);
    });

    function handleFiles(files) {
        selectedFiles = [...files];
        displayFiles();
    }

    function displayFiles() {
        if (selectedFiles.length === 0) {
            filesList.classList.add("hidden");
            fileInput.value = "";
            inputSelectedDocuments.classList.add("hidden");
            return;
        }

        filesList.classList.remove("hidden");
        inputSelectedDocuments.classList.remove("hidden");
        filesContainer.innerHTML = "";

        selectedFiles.forEach((file, index) => {
            const fileItem = createFileItem(file, index);
            filesContainer.appendChild(fileItem);
        });
    }

    function createFileItem(file, index) {
        const div = document.createElement("div");
        div.className =
            "flex items-center justify-between p-3 bg-secondary-50 dark:bg-secondary-900/50 rounded-lg border border-secondary-200 dark:border-secondary-700";

        const fileExtension = file.name.split(".").pop().toLowerCase();
        const fileSize = (file.size / 1024).toFixed(1) + " KB";

        div.innerHTML = `
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-white dark:bg-secondary-800">
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="size-5 dark:text-white text-secondary-800 icon icon-tabler icons-tabler-outline icon-tabler-file-code"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M10 13l-1 2l1 2" /><path d="M14 13l1 2l-1 2" /></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-secondary-900 dark:text-white">${file.name}</p>
                            <p class="text-xs text-secondary-500 dark:text-secondary-400">${fileSize}</p>
                        </div>
                    </div>
                    <button type="button" onclick="removeFile(${index})" class="p-1 rounded-lg text-red-500 hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="size-4 icon icon-tabler icons-tabler-outline icon-tabler-x"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>
                    </button>
                `;

        return div;
    }

    window.removeFile = function (index) {
        selectedFiles.splice(index, 1);
        fileInput.value = "";
        displayFiles();
    };

    window.getSelectedFiles = function () {
        return selectedFiles;
    };

    $("#form-generate-book").submit(function (e) {
        // Primero agregar los archivos al formulario
        addFilesToForm();

        $("#text-loader").text("Generando libro de ventas...");
        setTimeout(() => {
            $("#loader").addClass("hidden");
        }, 2000);
    });

    function addFilesToForm() {
        // Remover inputs de archivos previos
        $('input[name="uploaded_files[]"]').remove();

        if (selectedFiles.length > 0) {
            const form = document.getElementById("form-generate-book");

            selectedFiles.forEach((file, index) => {
                // Crear un input file temporal para cada archivo
                const fileInput = document.createElement("input");
                fileInput.type = "file";
                fileInput.name = "uploaded_files[]";
                fileInput.style.display = "none";

                // Crear un DataTransfer para asignar el archivo al input
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;

                form.appendChild(fileInput);
            });
        }
    }

    $("#book-type").on("Changed", function () {
        const value = $(this).val();
        $(".container-books").addClass("hidden");

        if (value === "contribuyentes") {
            $("#container-ventas-contribuyentes").removeClass("hidden");
        } else if (value === "consumidores") {
            $("#container-ventas-consumidor-final").removeClass("hidden");
        } else if (value === "retencion_iva") {
            $("#container-retencion-iva").removeClass("hidden");
        } else if (value === "compras") {
            $("#container-compras").removeClass("hidden");
        } else if (value === "percepcion_iva") {
            $("#container-percepcion-iva").removeClass("hidden");
        }
    });

    $("#only-selected").on("change", function () {
        $("#only-mix").prop("checked", false);
    });

    $("#only-mix").on("change", function () {
        $("#only-selected").prop("checked", false);
    });
});
