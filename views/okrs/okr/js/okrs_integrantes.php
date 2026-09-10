<script>
    const baseUrl = "<?php echo $url; ?>";
    const idEmpresa = <?php echo $user_log["id_empresa"]; ?>;

    var api = baseUrl + 'api/okrs/';
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
                    id_empresa: idEmpresa
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
                    <td><img src="https://puntacana.goforagile.com/recursos/${empleado.foto}" width="40" height="40" class="rounded-circle"></td>
                    <td>${empleado.nombre}</td>
                    <td>${empleado.nombre_cargo}</td>
                    <td align="center"><input type="checkbox" class="chk-rol chk-rol-admin" name="empleados[${empleado.id}][admin_kr]" value="1"></td>
                    <td align="center"><input type="checkbox" class="chk-rol chk-rol-contri" name="empleados[${empleado.id}][contrib_dir]" value="1"></td>
                    <td align="center"><input type="checkbox" class="chk-rol chk-rol-apoyo" name="empleados[${empleado.id}][contrib_ap]" value="1"></td>
                </tr>
            `);
            });
        }

        $("#tablaEmpleados").DataTable({
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            }
        });

        // 🔹 Permitir solo un checkbox activo por empleado
        $("#tablaEmpleados").off("change", ".chk-rol").on("change", ".chk-rol", function() {
            const $row = $(this).closest("tr");
            if ($(this).is(":checked")) {
                $row.find(".chk-rol").not(this).prop("checked", false);
            }
        });
    }


    // 🔹 Filtro por área
    $(document).on("change", "#area", function() {
        const idAreaSeleccionada = $(this).val();
        if (!idAreaSeleccionada) {
            renderTablaEmpleados(listaEmpleados);
            return;
        }
        const filtrados = listaEmpleados.filter(e => e.id_area == idAreaSeleccionada);
        renderTablaEmpleados(filtrados);
    });

    // 🔹 Guardar estado de los checkboxes (persistencia por paginación)
    $(document).on("change", "#tablaEmpleados input[type='checkbox']", function() {
        const $checkbox = $(this);
        const empleadoId = $checkbox.attr("name").match(/\[(\d+)\]/)[1];
        const campo = $checkbox.attr("name").match(/\[(.*?)\]/g)[1].replace(/\[|\]/g, "");

        if (!seleccionados[empleadoId]) {
            seleccionados[empleadoId] = {
                admin_kr: 0,
                contrib_dir: 0,
                contrib_ap: 0
            };
        }

        seleccionados[empleadoId][campo] = $checkbox.is(":checked") ? 1 : 0;
        actualizarBoton();
    });

    // 🔹 Restaura selección tras redibujar DataTable
    $('#tablaEmpleados').on('draw.dt', function() {
        restaurarSeleccion();
    });

    function actualizarBoton() {
        const algunCheckMarcado = Object.values(seleccionados).some(emp =>
            emp.admin_kr || emp.contrib_dir || emp.contrib_ap
        );
        $("#btnAsociarIntegrantes").prop("disabled", !algunCheckMarcado);
    }

    function restaurarSeleccion() {
        $("#tablaEmpleados tbody tr").each(function() {
            const $row = $(this);
            const idMatch = $row.find("input[type='checkbox']").first().attr("name").match(/\[(\d+)\]/);
            if (!idMatch) return;
            const empleadoId = idMatch[1];

            if (seleccionados[empleadoId]) {
                for (const campo in seleccionados[empleadoId]) {
                    if (seleccionados[empleadoId][campo]) {
                        $row.find(`input[name='empleados[${empleadoId}][${campo}]']`).prop("checked", true);
                    }
                }
            }
        });
    }

    // 🔹 Antes de enviar el formulario, agregar los seleccionados
    $("form").on("submit", function() {
        for (const id in seleccionados) {
            for (const campo in seleccionados[id]) {
                if (seleccionados[id][campo]) {
                    $(this).append(`<input type="hidden" name="empleados[${id}][${campo}]" value="1">`);
                }
            }
        }
    });
</script>