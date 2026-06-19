<script>
    $(document).ready(function() {
        $('#menuKpis').collapse();
        $('#bt_objetivos_sg').addClass('active');
    });
</script>

<?php
include("app/models/kpis/Kpis.php");
$ClassKpis = new Kpis();
include("app/models/kpis/KpisCrud.php");
$ClassKpisCrud = new KpisCrud();

//PARA CRER Y EDITAR
if ($_POST["guardar_formulario"]) {

    if ($_POST["id_registro"]) {
        //ACTUALIZAR OBJETIVO
        $alerta = $ClassKpisCrud->Acutualizar_Objetivo_SG($_POST["id_registro"], $_POST);
        //BLOQUE PARA GUARDAR LA BITACORA
        //BLOQUE PARA GUARDAR LA BITACORA
        //BLOQUE PARA GUARDAR LA BITACORA
        $accion = 'ACTUALIZAR OBJETIVO';
        $descripcion = 'Actualización de Objetivos SG ' . $_POST["objetivo"];
        $id_kpi = 0;
        $tipo_kpi = 0;
        GuardarAuditoriaKpis( $user_log["id_empresa"], $user_log["id"], $accion, $descripcion, $id_kpi, $tipo_kpi ); 
        //BLOQUE PARA GUARDAR LA BITACORA
        //BLOQUE PARA GUARDAR LA BITACORA
        //BLOQUE PARA GUARDAR LA BITACORA
    } 
    else {
        //CREAR OBJETIVO
        $id_tmp = $ClassKpisCrud->Crear_Objetivo_SG($user_log["id_empresa"], $_POST); 

        //BLOQUE PARA GUARDAR LA BITACORA
        //BLOQUE PARA GUARDAR LA BITACORA
        //BLOQUE PARA GUARDAR LA BITACORA
        $accion = 'CREACIÓN OBJETIVO';
        $descripcion = 'Creación de Objetivos SG ' . $_POST["objetivo"];
        $id_kpi = 0;
        $tipo_kpi = 0;
        GuardarAuditoriaKpis( $user_log["id_empresa"], $user_log["id"], $accion, $descripcion, $id_kpi, $tipo_kpi ); 
        //BLOQUE PARA GUARDAR LA BITACORA
        //BLOQUE PARA GUARDAR LA BITACORA
        //BLOQUE PARA GUARDAR LA BITACORA

        echo '<script> window.location.href = "?pg=kpis/objetivos_sg";</script>';

    }
}




/* SI TIENE ID EL GET */
$id = null;
if (isset($_GET["id"]) && !empty($_GET["id"])) {
    //ID
    $id = $_GET["id"];

    /* OBTENER INFORMACION DEL OBJETIVO */
    $objetivo_sg = $ClassKpis->Kpis_Objetivo_SG($user_log["id_empresa"], $id);
    $_SESSION["anio_fill"] = $objetivo_sg["anio"];
}



/* VICEPRESIDENCIAS */
$vicepresidencias = $ClassKpis->Vicepresidencias($user_log["id_empresa"]);
/* AREAS */
$areas = $ClassKpis->Areas($user_log["id_empresa"]);


?>

<div class="container-fluid" style="max-width: 90%; margin: 0 auto;">

    <!-- ALERTAS -->
    <?= $alerta; ?>

    <!-- TITULO -->
    <div class="card mb-3">
        <div class="card-header">
            <h3>Ficha de Objetivo SG</h3>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <form action="" method="post" id="formulario_general">
                            <input type="hidden" name="guardar_formulario" value="true"">
                            <input type="hidden" name="id_registro" value="<?= $objetivo_sg["id"]; ?>">
                            
                            <input type="hidden" name="id_empresa" id="id_empresa" value="<?= $user_log["id_empresa"]; ?>">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="id_area">Año *</label>
                                        <select class="form-control form-control-sm" name="anio">
                                            <option value="-1" style="color: blue;">Selecciona...</option>
                                            <?php
                                            foreach ($Array_Anio as $periodo) {
                                                if ($objetivo_sg["anio"] ==  $periodo[0]) {
                                                    echo '<option value="' . $periodo[0] . '" selected>' . $periodo[1] . '</option>';
                                                } else {
                                                    echo '<option value="' . $periodo[0] . '">' . $periodo[1] . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="id_vicepresidencia">Área Macro *</label>
                                        <select name="id_vicepresidencia" id="id_vicepresidencia" class="form-control" required>
                                            <option value="">Selecciona...</option>
                                            <?php foreach ($vicepresidencias as $vicepresidencia): ?>
                                                <option value="<?= $vicepresidencia["id"]; ?>" <?= isset($objetivo_sg["id_vp"]) && $objetivo_sg["id_vp"] == $vicepresidencia["id"] ? " selected" : ""; ?>><?= $vicepresidencia["nombre"]; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="id_area">Área Proceso *</label>
                                        <select class="form-control" name="id_area" id="id_area" required>
                                            <option value="">Selecciona...</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="descripcion">Descripción del Objetivo *</label>
                                        <textarea
                                            class="form-control"
                                            name="objetivo"
                                            id="objetivo"
                                            rows="2"
                                            placeholder="Describe el objetivo SG..."
                                            required><?= isset($objetivo_sg["objetivo"]) && !empty($objetivo_sg["objetivo"]) ? $objetivo_sg["objetivo"] : "" ?></textarea>
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

        const objetivoEdit = <?= isset($objetivo_sg) ? json_encode([
                                "id_vp"   => $objetivo_sg["id_vp"],
                                "id_area" => $objetivo_sg["id_area"]
                            ]) : 'null'; ?>;

        const todasLasAreas = <?php echo json_encode($areas); ?>;
        const $selectVP = $('#id_vicepresidencia');
        const $selectArea = $('#id_area');

        function cargarAreas(idVP, idAreaSeleccionada = null) {
            $selectArea.empty().append('<option value="">Selecciona...</option>');

            const areasFiltradas = todasLasAreas.filter(area =>
                area.id_vicepresidencia == idVP
            );

            areasFiltradas.forEach(area => {
                const selected = idAreaSeleccionada == area.id ? 'selected' : '';
                $selectArea.append(
                    `<option value="${area.id}" ${selected}>${area.nombre}</option>`
                );
            });
        }

        // Evento change normal
        $selectVP.on('change', function() {
            const idVP = $(this).val();
            if (idVP !== "") {
                cargarAreas(idVP);
            } else {
                $selectArea.empty().append('<option value="">Selecciona...</option>');
            }
        });

        // 🔹 SI VIENE EN MODO EDICIÓN
        if (objetivoEdit) {
            // Forzamos carga de áreas
            cargarAreas(objetivoEdit.id_vp, objetivoEdit.id_area);
        }

    });
</script>