<script>
    $(document).ready(function() {
        $('#menuKpis').collapse();
        $('#bt_administradores').addClass('active');
    });
</script>

<?php
include("app/models/kpis/Kpis.php");
$ClassKpis = new Kpis();
include("app/models/kpis/KpisCrud.php");
$ClassKpisCrud = new KpisCrud();

/* VICEPRESIDENCIAS */
$vicepresidencias = $ClassKpis->Vicepresidencias($user_log["id_empresa"]);
/* AREAS */
$areas = $ClassKpis->Areas($user_log["id_empresa"]);
/* EMPLEADOS */
$empleados = $ClassKpis->Empleados($user_log["id_empresa"]);

if ($_POST["guardar_formulario"]) {
    $ClassKpisCrud->crear_Kpis_Administradores($user_log["id_empresa"], $_POST);

    //BLOQUE PARA GUARDAR LA BITACORA
    //BLOQUE PARA GUARDAR LA BITACORA
    //BLOQUE PARA GUARDAR LA BITACORA
    $accion = 'CREACIÓN ADMINITRADOS';
    $descripcion = 'Creación de administrador ';
    $id_kpi = 0;
    $tipo_kpi = 0;
    GuardarAuditoriaKpis( $user_log["id_empresa"], $user_log["id"], $accion, $descripcion, $id_kpi, $tipo_kpi ); 
    //BLOQUE PARA GUARDAR LA BITACORA
    //BLOQUE PARA GUARDAR LA BITACORA
    //BLOQUE PARA GUARDAR LA BITACORA

    echo '<script> window.location.href = "?pg=kpis/administradores";</script>';
}

?>

<div class="container-fluid" style="max-width: 90%; margin: 0 auto;">

    <!-- ALERTAS -->
    <?= $alerta; ?>

    <!-- TITULO -->
    <div class="card mb-3">
        <div class="card-header">
            <h3>Creación Administrador Kpi</h3>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <form action="" method="post" id="formulario_general">
                            <input type="hidden" name="guardar_formulario" value="true">
                            <input type="hidden" name="id_empresa" id="id_empresa" value="<?= $user_log["id_empresa"]; ?>">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="id_vicepresidencia">Área Macro *</label>
                                        <select name="id_vicepresidencia" id="id_vicepresidencia" class="form-control" required>
                                            <option value="">Selecciona...</option>
                                            <?php foreach ($vicepresidencias as $vicepresidencia): ?>
                                                <option value="<?= $vicepresidencia["id"]; ?>"><?= $vicepresidencia["nombre"]; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="id_area">Área Proceso *</label>
                                        <select class="form-control" name="id_area" id="id_area" required>
                                            <option value="">Selecciona...</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="id_area">Colaborador *</label>
                                        <select class="form-control" name="id_colaborador" id="id_colaborador" required>
                                            <option value="">Selecciona...</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12" style="margin-top: 15px ">
                                <button type="submit" class="btn btn-success w-100">
                                    Guardar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    $(document).ready(function() {
        // Convertimos el array PHP a JSON para usarlo en JS
        const todasLasAreas = <?php echo json_encode($areas); ?>;
        const todosLosEmpleados = <?php echo json_encode($empleados); ?>;

        $('#id_vicepresidencia').on('change', function() {
            const idVicepresidenciaSel = $(this).val();
            const $selectArea = $('#id_area');

            // Limpiar el select de áreas y el de colaborador
            $selectArea.empty().append('<option value="">Selecciona...</option>');
            $('#id_colaborador').empty().append('<option value="">Selecciona...</option>');

            if (idVicepresidenciaSel !== "") {
                // Filtramos las áreas que coincidan con la vicepresidencia seleccionada
                const areasFiltradas = todasLasAreas.filter(function(area) {
                    return area.id_vicepresidencia == idVicepresidenciaSel;
                });

                // Llenamos el select con los resultados
                areasFiltradas.forEach(function(area) {
                    $selectArea.append(`<option value="${area.id}">${area.nombre}</option>`);
                });
            }
        });

        $('#id_area').on('change', function() {
            const idAreaSel = $(this).val();
            const $selectColaborador = $('#id_colaborador');

            // Limpiamos el select de colaboradores
            $selectColaborador.empty().append('<option value="">Selecciona...</option>');

            if (idAreaSel !== "") {
                // Filtramos los empleados por la propiedad "area"
                // Nota: Si en tu base de datos el campo se llama "id_area", cámbialo aquí:
                const empleadosFiltrados = todosLosEmpleados.filter(function(empleado) {
                    return empleado.area == idAreaSel;
                });

                // Llenamos el select con los colaboradores filtrados
                empleadosFiltrados.forEach(function(empleado) {
                    // Asumimos que el empleado tiene 'id' y 'nombre' (o 'nombres')
                    $selectColaborador.append(`<option value="${empleado.id}">${empleado.nombre}</option>`);
                });
            }
        });
    });
</script>