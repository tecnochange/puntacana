<script>
    $(document).ready(function() {
        $('#menuOkrs').collapse();
        $('#bt_okrs_crear').addClass('active');
    });
</script>

<?php
/*
 * TEST PAGE of the AI assistant (agent-engine) on the OKR objective form.
 * Copy of detalle.php that never saves nor deletes: no POST handling, no "Guardar",
 * no "Eliminar". Administrators only (Rutas row of sql/005 and the check below).
 * Delete it once the assistant lives in detalle.php.
 */
if (!in_array('1', array_map('trim', explode(',', (string) $user_log["role"])), true)) {
    include("views/sin_permisos.php");
    return;
}

if($_GET["new"]){
    $_SESSION["id_okrs_edit"] = "";
}

include("app/models/okrs/OkrsServicios.php");

$OkrsServicios = new OkrsServicios();

$disabled = "";
if($_GET["id"]){
    $_SESSION["id_okrs_edit"] = $_GET["id"];
    $disabled = "disabled";
}

$data = $OkrsServicios->obtener_okrs($_SESSION["id_okrs_edit"]); //OBTENER OKRS
$objetivos_estrategicos = $OkrsServicios->Obtener_Objetivos_Estrategicos($user_log["id_empresa"], $_SESSION["anio_fill"]); //OBTENER OBJETIVOS ESTRATEGICOS
?>

<div class="container">

    <div class="alert alert-warning mb-3">
        <strong>Página de prueba del asistente de IA.</strong> Solo la ven los administradores y <strong>no guarda nada</strong>:
        sirve para probar el botón IA del objetivo con datos reales.
        Para crear o cambiar un OKR usa la <a href="<?php echo $url; ?>?pg=okrs/okr/detalle">ficha normal</a>.
    </div>

    <?php if($_SESSION["id_okrs_edit"]){ ?>
        <div class="card mb-3">
            <div class="card-body" style="font-size: 19px; font-weight: bold; color: #007bff;">
                <?php echo $data["objetivo_okr"] ?>
            </div>
        </div>

        <ul class="nav nav-tabs justify-content-center">
            <li class="nav-item">
                <a class="nav-link active" href="<?php echo $url; ?>?pg=okrs/okr/detalle_ia">PASO 1. OKRS del Equipo (prueba IA)</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo $url; ?>?pg=okrs/okr/resultados_ia" >PASO 4. Resultados Claves (KR) (prueba IA)</a>
            </li>
        </ul>
    <?php  } ?>

    <div class="card mb-3">
        <div class="card-header">
            <h3>Ficha del OKRs</h3>
        </div>
        <div class="card-body">

            <!-- Test page: the form is never submitted. -->
            <form action="" method="POST" onsubmit="return false;">
                <input type="hidden" name="tipo" value="<?= $data['tipo'] ?>">
                <div class="row">

                    <div class="col-md-3 mb-2">
                        <label><strong>Tipo de OKRs *</strong></label>
                        <select class="form-control form-control-sm" name="tipo" onchange="ObjetivosAltaDireccion()" required <?= $disabled; ?>>
                            <option value="">Seleccione...</option>
                            <?php
                            foreach ($Array_Tipo_OKR as $nodo) {
                                if ($data["tipo"] ==  $nodo[0]) {
                                    echo '<option value="' . $nodo[0] . '" selected>' . $nodo[1] . '</option>';
                                } else {
                                    echo '<option value="' . $nodo[0] . '">' . $nodo[1] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-2 mb-2">
                        <label><strong>* Año</strong></label>
                        <select class="form-control form-control-sm" name="anio" id="anio" onchange="ObjetivosAltaDireccion()" required>
                            <option value="">Seleccione...</option>
                            <?php
                            foreach ($Array_Anio as $periodo) {
                                if ($data["anio"] ==  $periodo[0]) {
                                    echo '<option value="' . $periodo[0] . '" selected>' . $periodo[1] . '</option>';
                                } else {
                                    echo '<option value="' . $periodo[0] . '">' . $periodo[1] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label><strong>Alta Dirección *</strong></label>
                        <select class="multiples_responsables form-control" id="vicepresidencia" name="vicepresidencia" onchange="ObjetivosAltaDireccion();">
                            <option value="">Seleccione...</option>
                            <?php
                            $queryVicepresidencias = mysqli_query($connect_admin, "SELECT * FROM Vicepresidencia WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND estado = 1 ORDER BY nombre ASC");
                            while ($dataVicepresidencia = mysqli_fetch_assoc($queryVicepresidencias)) {
                                if ($data["id_vicepresidencia"] == $dataVicepresidencia["id"]) {
                                    echo '<option value="' . $dataVicepresidencia["id"] . '" selected>' . $dataVicepresidencia["nombre"] . '</option>';
                                } else {
                                    echo '<option value="' . $dataVicepresidencia["id"] . '">' . $dataVicepresidencia["nombre"] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-3 mb-2">
                        <label><strong>Lista de Objetivos Estratégicos</strong></label>
                        <select class="multiples_responsables form-control" id="objetivos_estrategicos">
                            <option value="">Seleccione..</option>
                            <?php if($data["objetivos_estrategicos"]): ?>
                                <?php foreach($objetivos_estrategicos as $item): ?>
                                    <option value="<?= $item["id"]; ?>" <?= $item["id"] == $data["objetivos_estrategicos"] ? " selected" : "" ?>><?= $item["objetivo"]; ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="col-md-12 mb-2">
                        <label><strong>Descripción del (O) *</strong></label>
                        <textarea rows="3" class="form-control" name="objetivo_okr" oninput="LimpiarCaracteresEspeciales(this)" required placeholder="Ingrese su Objetivo en este espacio..."
                                  data-agent="okr-mentor" data-tasks="draft-objective,improve-objective"
                                  data-param-year-from="#anio" data-param-okr_type-from="select[name=tipo]"
                                  data-param-vp_id-from="#vicepresidencia"><?php echo $data["objetivo_okr"] ?></textarea>
                    </div>



                    <div class="col-md-2 mb-2">
                        <label><strong>* Periodo Inicia</strong></label>
                        <input type="date" class="form-control form-control-sm" name="fecha_inicia" value="<?php echo $data["fecha_inicia"]; ?>" required>
                    </div>

                    <div class="col-md-2 mb-2">
                        <label><strong>* Periodo Termina</strong></label>
                        <input type="date" class="form-control form-control-sm" name="fecha_termina" value="<?php echo $data["fecha_termina"]; ?>" required>
                    </div>

                    <div class="col-md-2 mb-2">
                        <label><strong>Periodo *</strong></label>
                        <select class="form-control form-control-sm" name="periodo" required>
                            <!-- <option value="">Selecciona...</option> -->
                            <option value="Anual">Anual</option>
                            <?php
                            /* foreach ($Array_Periodos_Q as $periodo) {
                                if ($data["periodo"] ==  $periodo[0] || $data_equipo["periodo"] ==  $periodo[0]) {
                                    echo '<option value="' . $periodo[0] . '" selected>' . $periodo[1] . '</option>';
                                } else {
                                    echo '<option value="' . $periodo[0] . '">' . $periodo[1] . '</option>';
                                }
                            } */
                            ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label><strong>Owner *</strong></label>
                        <select class="multiples_responsables form-control" name="owner" required>
                            <option value="">Selecciona...</option>
                            <?php
                            $queryOwner = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND role IN (1,2) AND estado = 1 ORDER BY nombre");
                            while ($dataOwner = mysqli_fetch_array($queryOwner)) {

                                if ($data['id_empleado'] == $dataOwner["id"]) {
                                    echo '<option value="' . $dataOwner["id"] . '" selected>' . $dataOwner["nombre"] . '</option>';
                                } else {
                                    echo '<option value="' . $dataOwner["id"] . '">' . $dataOwner["nombre"] . '</option>';
                                }
                            }

                            ?>
                        </select>
                    </div>

                    <div class="col-md-12 mb-2">
                        <button type="button" class="btn btn-secondary" disabled title="Página de prueba: no guarda">Guardar (desactivado en la prueba)</button>
                    </div>

                </div>
            </form>

        </div>
    </div>

</div>

<script src="<?php echo $url; ?>agent-engine/assets/ai-field/ai-field.js" data-endpoint="/agent-engine/public/api" data-accent="#007bff" defer></script>

<script>
    var api = '<?php echo $url; ?>api/okrs/';

    function ObjetivosAltaDireccion() {
        $("#objetivos_estrategicos").html("");
        jQuery.ajax({
                url: api + "lista_objetivos_vicepresidencias.php",
                type: 'post',
                data: {
                    id_viceprecidencia: $("#id_vicepresidencia").val(),
                    id_empresa: <?php echo $user_log["id_empresa"] ?>,
                    anio: $("#anio").val()
                },
            }).done(function(resp) {
                $("#objetivos_estrategicos").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});
    }
</script>



<script>
   
   $(document).ready(function() {
        $('.multiples_responsables').select2();
    });
</script>


<script>
function LimpiarCaracteresEspeciales(textarea) {
    
    // Permite letras, números, espacios y algunos signos básicos
    textarea.value = textarea.value.replace(
        /[^a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s.,;:¡!¿?()\-%#\/]/g,
        ''
    );
}
</script>