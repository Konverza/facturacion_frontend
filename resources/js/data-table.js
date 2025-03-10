import DataTable from "datatables.net-dt";

$(document).ready(function () {
    const defaultTableOptions = {
        paging: true,
        pagingType: "full_numbers",
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 75, 100],
        info: true,
        language: {
            emptyTable: "No se encontraron registros",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            paginate: {
                first: '<svg class="size-3"  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-chevrons-left"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M11 7l-5 5l5 5" /><path d="M17 7l-5 5l5 5" /></svg>',
                previous:
                    '<svg  class="size-3" xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-left"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>',
                next: '<svg  class="size-3 xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-right"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>',
                last: '<svg class="size-3" xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-chevrons-right"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7l5 5l-5 5" /><path d="M13 7l5 5l-5 5" /></svg>',
            },
        },
    };

    const tables = [
        {
            id: "#table-special",
            searchInput: "#input-search-special",
            order: [[0, "desc"]],
            pageLength: 10,
            pagingType: "full_numbers",
        },
        {
            id: "#table-products",
            searchInput: "#input-search-products",
            pageLength: 5,
            pagingType: "simple",
        },
        {
            id: "#table-customers",
            searchInput: "#input-search-customers",
            pageLength: 5,
            pagingType: "simple",
        },
        {
            id: "#table-data",
            searchInput: "#input-search-data",
            pageLength: 10,
            pagingType: "full_numbers",
        },
        {
            id: "#table-dtes",
            searchInput: "#nit-receptor",
            pageLength: 5,
            pagingType: "simple",
            order: [[0, "desc"]],
        },
    ];

    const initializedTables = {};

    tables.forEach(({ id, searchInput, order, pageLength, pagingType }) => {
        const tableOptions = { ...defaultTableOptions };
        if (order) {
            tableOptions.order = order; // Aplica el orden descendente si está definido
        }

        if (pagingType) {
            tableOptions.pagingType = pagingType; // Aplica el tipo de paginación si está definido
        } 
        if (pageLength) {
            tableOptions.pageLength = pageLength; // Aplica la cantidad de registros por página si está definido
        }

        const table = new DataTable(id, tableOptions);
        initializedTables[id] = table;

        if (searchInput) {
            $(searchInput).on("keyup", function () {
                table.search($(this).val()).draw();
            });
        }
    });

    const filters = [
        {
            tableId: "#tableProduct",
            filterInput: "input[name='filter-status']",
        },
    ];

    filters.forEach(({ tableId, filterInput }) => {
        $(filterInput).on("change", function () {
            const value = $(this).val();
            initializedTables[tableId].search(value).draw();
        });
    });

    filters.forEach(({ tableId, filterInput }) => {
        $(filterInput).on("Changed", function () {
            const value = $(this).val();
            initializedTables[tableId].search(value).draw();
        });
    });

    $("#emitido-desde").on("change", function () {
        const value = $(this).val();
        const [year, month, day] = value.split("-");
        const formattedDate = `${day}/${month}/${year}`;
        initializedTables["#table-dtes"].column(2).search(formattedDate).draw();
    });

    $(document).on("change", "#emitido-desde, #emitido-hasta", function () {
        const startDate = $("#emitido-desde").val();
        const endDate = $("#emitido-hasta").val();

        if (startDate && endDate) {
            const [startYear, startMonth, startDay] = startDate.split("-");
            const [endYear, endMonth, endDay] = endDate.split("-");
            const formattedStartDate = `${startYear}-${startMonth}-${startDay}`;
            const formattedEndDate = `${endYear}-${endMonth}-${endDay}`;
            initializedTables["#table-dtes"]
                .column(2)
                .search(function (settings, data, dataIndex) {
                    const tableDate = data[2];
                    const tableDateFormatted = moment(
                        tableDate,
                        "DD/MM/YYYY"
                    ).format("YYYY-MM-DD");
                    return moment(tableDateFormatted).isBetween(
                        formattedStartDate,
                        formattedEndDate,
                        undefined,
                        "[]"
                    );
                })
                .draw();
        } else {
            initializedTables["#table-dtes"].column(2).search("").draw();
        }
    });

    $("#tipo_documento").on("Changed", function () {
        const value = $(this).val();
        initializedTables["#table-dtes"].column(0).search(value).draw();
    });

    $("#nit-receptor").on("input", function () {
        const value = $(this).val();
        initializedTables["#table-dtes"].column(1).search(value).draw();
    });

    if ($("#nit-receptor").val() != "") {
        const value = $("#nit-receptor").val();
        initializedTables["#table-dtes"].column(1).search(value).draw();
    }
});
