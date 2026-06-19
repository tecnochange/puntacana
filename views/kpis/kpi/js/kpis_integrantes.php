<script>
    const baseUrl = "<?php echo $url; ?>";
    const idEmpresa = <?php echo $user_log["id_empresa"]; ?>;

    var api = baseUrl + 'api/kpis/';
    var listaEmpleados = [];
    var seleccionados = {};

    // 🔹 Obtener áreas por vicepresidencia
    function ObtenerAreas(id_vicepresidencia) {

        const $area = $("#area");

        if (!id_vicepresidencia) {
            $area.prop("disabled", true);
            $area.html('<option value="">Seleccione...</option>');
            $("#tablaEmpleados_contenedor").addClass("d-none");
            return;
        }

        $area.prop("disabled", false);
        $area.html('<option value="">Cargando...</option>');

        $.ajax({
                url: api + "lista_areas_vicepresidencia.php",
                type: 'post',
                data: {
                    id_viceprecidencia: id_vicepresidencia, 
                    id_empresa: idEmpresa, 

                },
            })
            .done(function(resp) {
                $area.html(resp);
                ObtenerColaboradores(id_vicepresidencia)
            })
            .fail(function(resp) {
                console.log("Error al obtener áreas:", resp);
                $area.html('<option value="">Error al cargar</option>');
            });
    }

    // 🔹 Obtener colaboradores
    function ObtenerColaboradores(id_vicepresidencia) {
        $.ajax({
            url: api + "lista_integrantes.php",
            type: "POST",
            dataType: "json",
            data: {
                id_empresa: idEmpresa,
                id_vicepresidencia: id_vicepresidencia
            },
            success: function(data) {
                $("#tablaEmpleados_contenedor").removeClass("d-none");
                listaEmpleados = data;
                renderTablaEmpleados(listaEmpleados);
            },
            error: function(xhr, status, error) {
                console.log("Error al obtener colaboradores:", error);
            }
        });
    }

    // 🔹 Obtener colaboradores
    function ObtenerColaboradoresArea(id_vicepresidencia, id_area) {
        $.ajax({
            url: api + "lista_integrantes.php",
            type: "POST",
            dataType: "json",
            data: {
                id_empresa: idEmpresa,
                id_vicepresidencia: id_vicepresidencia, 
                id_area: id_area
            },
            success: function(data) {
                $("#tablaEmpleados_contenedor").removeClass("d-none");
                listaEmpleados = data;
                renderTablaEmpleados(listaEmpleados);
            },
            error: function(xhr, status, error) {
                console.log("Error al obtener colaboradores:", error);
            }
        });
    }

    // 🔹 Renderizar tabla de empleados
    function renderTablaEmpleados(lista) {

        if ($.fn.DataTable.isDataTable("#tablaEmpleados")) {
            $("#tablaEmpleados").DataTable().destroy();
        }

        const $tbody = $("#tablaEmpleados tbody");
        $tbody.empty();

        if (lista.length === 0) {
            $tbody.append(`
            <tr>
                <td colspan="6" class="text-center text-muted">No hay colaboradores.</td>
            </tr>
        `);
        } else {
            lista.forEach(function(empleado) {
                empleado.foto = empleado.foto ? empleado.foto : "/img_default.jpg";
                $tbody.append(`
                <tr data-id="${empleado.id}">
                    <td><img src="https://goforagile.com/recursos/${empleado.foto}" width="40" height="40" class="foto_miniaturas" onclick="FichaEmpleado('${empleado.id}')"></td>
                    <td>${empleado.nombre}</td>
                    <td>${empleado.nombre_cargo}</td>
                    <td align="center">
                        <input type="checkbox"
                            class="chk-rol"
                            data-tipo="3"
                            data-id="${empleado.id}">
                    </td>
                    <td align="center">
                        <input type="checkbox"
                            class="chk-rol"
                            data-tipo="2"
                            data-id="${empleado.id}">
                    </td>
                </tr>
            `);
            });
        }

        $("#tablaEmpleados").DataTable({
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "Mostrar Todos"]
            ]
        });

        // 🔹 Permitir solo un checkbox activo por empleado
        $("#tablaEmpleados").off("change", ".chk-rol").on("change", ".chk-rol", function() {

            const empleadoId = $(this).data("id");
            const tipo = $(this).data("tipo");
            const $row = $(this).closest("tr");

            if ($(this).is(":checked")) {

                // desmarcar el otro checkbox
                $row.find(".chk-rol").not(this).prop("checked", false);

                // guardar tipo seleccionado
                seleccionados[empleadoId] = tipo;

            } else {
                delete seleccionados[empleadoId];
            }

            actualizarBoton();
        });
    }

    // 🔹 Filtro por área
    $(document).on("change", "#area", function() {

        const idAreaSeleccionada = $("#area").val();
        const idVicepresidenciaSeleccionada = $("#vicepresidencia").val();

        filtrados = [];
        listaEmpleados.map(empleado => {
            if (empleado.id_area == idAreaSeleccionada) {
                filtrados.push(empleado);
            }
        })

        if (!filtrados || filtrados.length == 0) {
            filtrados = ObtenerColaboradores(idVicepresidenciaSeleccionada)
        }

        renderTablaEmpleados(filtrados);
    });

    // 🔹 Guardar estado de los checkboxes (persistencia por paginación)
    $(document).on("change", "#tablaEmpleados input[type='checkbox']", function() {

        const empleadoId = $(this).data("id");
        const tipo = $(this).data("tipo");

        if (!empleadoId || !tipo) return;

        if ($(this).is(":checked")) {

            // asegurar que solo haya uno por empleado
            $(`input[data-id="${empleadoId}"]`).not(this).prop("checked", false);

            seleccionados[empleadoId] = tipo;

        } else {
            delete seleccionados[empleadoId];
        }

        actualizarBoton();
    });

    // 🔹 Restaura selección tras redibujar DataTable
    $('#tablaEmpleados').on('draw.dt', function() {
        restaurarSeleccion();
    });

    function actualizarBoton() {
        const haySeleccion = Object.keys(seleccionados).length > 0;
        $("#btnAsociarIntegrantes").prop("disabled", !haySeleccion);
    }

    function restaurarSeleccion() {
        $("#tablaEmpleados tbody tr").each(function() {

            const $row = $(this);

            $row.find(".chk-rol").each(function() {

                const empleadoId = $(this).data("id");

                if (seleccionados[empleadoId] !== undefined) {
                    const tipoGuardado = seleccionados[empleadoId];
                    const tipoCheckbox = $(this).data("tipo");

                    if (tipoGuardado == tipoCheckbox) {
                        $(this).prop("checked", true);
                    }
                }
            });
        });
    }

    // 🔹 Antes de enviar el formulario, agregar los seleccionados
    $("form").on("submit", function() {

        for (const id in seleccionados) {

            $(this).append(`
            <input type="hidden"
                   name="empleados[${id}]"
                   value="${seleccionados[id]}">
        `);
        }
    });
</script>