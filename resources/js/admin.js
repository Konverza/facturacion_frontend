import { Autocomplete } from './autocomplete';

$(function () {
    $.ajax({
        url: '/catalogo/cat_019',
        success: function (response) {
            const field_actividad_economica = document.getElementById('actividad_economica');
            const ac_actividad_economica = new Autocomplete(field_actividad_economica, {
                data: response,
                maximumItems: 5,
                threshold: 1
            });
        }
    })


    $.ajax({
        url: '/departamentos/all',
        success: function (apiResponse) {
            // Llenar el select de departamentos
            const departamentoSelect = $('#departamentoSelect');
            departamentoSelect.empty();
            apiResponse.forEach(departamento => {
                departamentoSelect.append(new Option(departamento.valores, departamento.codigo));
            });

            // Manejar el cambio del select de departamentos
            departamentoSelect.on('change', function () {
                const selectedDepartamento = $(this).val();
                const municipioSelect = $('#municipioSelect');

                // Limpiar el select de municipios
                municipioSelect.empty();

                if (selectedDepartamento) {
                    // Obtener los municipios del departamento seleccionado
                    const municipios = apiResponse.find(dept => dept.codigo === selectedDepartamento).municipios;

                    // Llenar el select de municipios
                    municipios.forEach(municipio => {
                        municipioSelect.append(new Option(municipio.valores, municipio.codigo));
                    });
                }
            });
        }
    })
});