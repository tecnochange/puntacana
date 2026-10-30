<script>
    $(document).ready(function() {
        $('#menuOkrs').collapse();
        $('#bt_okrs_crear').addClass('active');
    });
</script>

<?php
/*
 * TEST PAGE of the AI assistant (agent-engine) on the key result (KR) form.
 * Copy of resultados.php that never saves nor deletes: no POST handling, no "Guardar",
 * no delete button. Administrators only (Rutas row of sql/005 and the check below).
 * Delete it once the assistant lives in resultados.php.
 */
if (!in_array('1', array_map('trim', explode(',', (string) $user_log["role"])), true)) {
    include("views/sin_permisos.php");
    return;
}

if ($_GET["id"]) {
    $_SESSION["id_okrs_edit"] = $_GET["id"];
}

include("app/models/okrs/OkrsServicios.php");
$OkrsServicios = new OkrsServicios();

$data = $OkrsServicios->obtener_okrs($_SESSION["id_okrs_edit"]); //OBTENER OKRS
$integrantes_asociados = $OkrsServicios->obtener_integrantes_asociados($user_log["id_empresa"], $_SESSION["id_okrs_edit"]); //OBTENER INTEGRANTES ASOCIADOS AL OKRS
$listado_resultados_claves_asignados = $OkrsServicios->listado_resultados_claves_asignados($user_log["id_empresa"], $data["id_empleado"], $_SESSION["id_okrs_edit"]); //OBTENER LISTADO DE RESULTADOS CLAVES ASIGNADOS AL OKRS

if (isset($_GET["id_resultado"]) && !empty($_GET["id_resultado"])) {
    $data_resultado_clave = $OkrsServicios->obtener_resultado_clave($_GET["id_resultado"]);
    $responsables_seleccionados = explode(",", $data_resultado_clave["responsables"]);
}

?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="container pb-4">

    <div class="alert alert-warning mb-3">
        <strong>Página de prueba del asistente de IA.</strong> Solo la ven los administradores y <strong>no guarda nada</strong>:
        sirve para probar «Sugerir KRs» y «Mejorar este KR» con datos reales. «Usar» llena el formulario, pero no hay botón para guardarlo.
        Para crear o cambiar un KR usa la <a href="<?php echo $url; ?>?pg=okrs/okr/resultados">pantalla normal</a>.
    </div>

    <div class="card mb-3">
        <div class="card-body" style="font-size: 19px; font-weight: bold; color: #007bff;">
            <?php echo $data["objetivo_okr"] ?>
        </div>
    </div>

    <ul class="nav nav-tabs justify-content-center">
        <li class="nav-item">
            <a class="nav-link " href="<?php echo $url; ?>?pg=okrs/okr/detalle_ia">Paso 1. OKRS del Equipo (prueba IA)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="<?php echo $url; ?>?pg=okrs/okr/resultados_ia">PASO 4. Resultados Claves (KR) (prueba IA)</a>
        </li>
    </ul>

    <div class="card mb-3">
        <div class="card-header">
            <h3>Resultados Claves (KR)</h3>
        </div>
        <div class="card-body">

            <!-- AI assistant: suggests KRs or improves the one in the form; "Usar" fills the form. -->
            <div class="mb-3" data-agent="okr-mentor" data-tasks="suggest-key-results,improve-key-result"
                 data-param-objective="<?= htmlspecialchars((string) $data["objetivo_okr"], ENT_QUOTES, 'UTF-8') ?>"
                 data-param-okr_id="<?= (int) $_SESSION["id_okrs_edit"] ?>"
                 data-param-kr_text-from="textarea[name=descripcion_del_resultado]"
                 data-param-measurement-from="select[name=medicion]" data-param-trend-from="select[name=tendencia]"
                 data-param-target-from="input[name=meta]" data-param-period-from="select[name=periodo]"
                 data-fill-map='{"description":"textarea[name=descripcion_del_resultado]","measurement":"select[name=medicion]","trend":"select[name=tendencia]","target":"input[name=meta]","period":"select[name=periodo]"}'></div>

            <!-- FORMULARIO (test page: never submitted) -->
            <form action="" method="POST" onsubmit="return false;">
                <div class="row">

                    <div class="col-md-3 mb-2">
                        <label><strong>* Fecha de Inicio</strong></label>
                        <input type="date" class="form-control" name="fecha_inicia" value="<?php echo $data_resultado_clave["fecha_inicia"]; ?>" required>
                    </div>

                    <div class="col-md-3 mb-2">
                        <label><strong>* Fecha de entrega</strong></label>
                        <input type="date" class="form-control" name="fecha_termina" value="<?php echo $data_resultado_clave["fecha_entrega"]; ?>" required>
                    </div>

                    <div class="col-md-3 mb-2">
                        <label><strong>* Medición</strong></label>
                        <select class="form-control" name="medicion" required>
                            <option value="">Seleccione...</option>
                            <option value="1" <?= isset($data_resultado_clave["medicion"]) && $data_resultado_clave["medicion"] == 1 ? "selected" : "" ?>>Cantidad</option>
                            <option value="2" <?= isset($data_resultado_clave["medicion"]) && $data_resultado_clave["medicion"] == 2 ? "selected" : "" ?>>Horas</option>
                            <option value="3" <?= isset($data_resultado_clave["medicion"]) && $data_resultado_clave["medicion"] == 3 ? "selected" : "" ?>>Moneda</option>
                            <option value="4" <?= isset($data_resultado_clave["medicion"]) && $data_resultado_clave["medicion"] == 4 ? "selected" : "" ?>>Porcentaje</option>
                            <option value="5" <?= isset($data_resultado_clave["medicion"]) && $data_resultado_clave["medicion"] == 5 ? "selected" : "" ?>>Documento</option>
                            <option value="6" <?= isset($data_resultado_clave["medicion"]) && $data_resultado_clave["medicion"] == 6 ? "selected" : "" ?>>Hito</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-2">
                        <label><strong>* Tendencia</strong></label>
                        <select class="form-control" name="tendencia" required>
                            <option value="">Seleccione...</option>
                            <option value="1" <?= isset($data_resultado_clave["tendencia"]) && $data_resultado_clave["tendencia"] == 1 ? "selected" : "" ?>>Ascendente</option>
                            <option value="2" <?= isset($data_resultado_clave["tendencia"]) && $data_resultado_clave["tendencia"] == 2 ? "selected" : "" ?>>Descendente</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-2">
                        <label><strong>* Meta</strong></label>
                        <input type="text" class="form-control" name="meta" value="<?= $data_resultado_clave["meta"]; ?>" required>
                    </div>

                    <div class="col-md-3 mb-2">
                        <label><strong>* Periodo</strong></label>
                        <select class="form-control" name="periodo" required>
                            <option value="">Seleccione...</option>
                            <option value="Q1" <?= isset($data_resultado_clave["periodo"]) && $data_resultado_clave["periodo"] == "Q1" ? "selected" : "" ?>>Q1</option>
                            <option value="Q2" <?= isset($data_resultado_clave["periodo"]) && $data_resultado_clave["periodo"] == "Q2" ? "selected" : "" ?>>Q2</option>
                            <option value="Q3" <?= isset($data_resultado_clave["periodo"]) && $data_resultado_clave["periodo"] == "Q3" ? "selected" : "" ?>>Q3</option>
                            <option value="Q4" <?= isset($data_resultado_clave["periodo"]) && $data_resultado_clave["periodo"] == "Q4" ? "selected" : "" ?>>Q4</option>
                            <option value="Anual" <?= isset($data_resultado_clave["periodo"]) && $data_resultado_clave["periodo"] == "Anual" ? "selected" : "" ?>>Anual</option>
                        </select>
                    </div>

                    <div class="col-md-12 mb-2">
                        <label>* Descripción del Resultado</label>
                        <textarea name="descripcion_del_resultado" class="form-control" oninput="LimpiarCaracteresEspeciales(this)" ><?= $data_resultado_clave["descripcion"]; ?></textarea>
                    </div>

                    <div class="col-md-12 mb-2">
                        <label><strong>* Responsables</strong></label>
                        <select class="multiples_responsables form-control" style="width: 100%" name="id_responsables[]" id="id_responsables" multiple="multiple">
                            <?php foreach ($integrantes_asociados as $integrante): ?>
                                <option value="<?= $integrante["empleado"]["id"]; ?>" <?= in_array($integrante["empleado"]["id"], $responsables_seleccionados) ? "selected" : "" ?>>
                                    <?= $integrante["empleado"]["nombre"]; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-12 mt-2 mb-2">
                        <button type="button" class="btn btn-secondary" disabled title="Página de prueba: no guarda">Guardar (desactivado en la prueba)</button>
                    </div>

                </div>
            </form>

        </div>
    </div>

    <!-- TABLA DE RESULTADOS -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Periodo</th>
                            <th>Publicado por</th>
                            <th>Resultados Clave (KR)</th>
                            <th>Responsables</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha de Entrega</th>
                            <th>Meta</th>
                            <th>Seguimiento</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listado_resultados_claves_asignados as $item): ?>

                            <?php
                            $empleado_owner_foto = !empty($item["empleado_owner"]["foto"]) ? $item["empleado_owner"]["foto"] : "img_default.jpg"; //FOTO DEL EMPELADO OWNER
                            $responsables = explode(',', $item["responsables"]);
                            ?>

                            <tr>
                                <td class="text-center"><?= $item["periodo"]; ?></td>
                                <td class="text-center"><img src="<?= $recursos_local . $empleado_owner_foto; ?>" width="40" height="40" class="rounded-circle" title="<?= $item["empleado_owner"]["nombre"]; ?>"></td>
                                <td><?= $item["descripcion"]; ?></td>
                                <td class="text-center">
                                    <?php for ($i = 0; $i < count($responsables); $i++): ?>
                                        <?php
                                        $id_responsable = $responsables[$i];
                                        $responsableData = $OkrsServicios->Empleado($id_responsable); //OBTENER DATOS DEL RESPONSABLE
                                        $foto_responsable = !empty($responsableData["foto"]) ? $responsableData["foto"] : "img_default.jpg"; //FOTO DEL RESPONSABLE
                                        ?>
                                        <img src="<?= $recursos_local . $foto_responsable; ?>" width="25" height="25" class="rounded-circle" title="<?= $responsableData["nombre"]; ?>">
                                    <?php endfor; ?>
                                </td>
                                <td><?= $item["fecha_inicia"]; ?></td>
                                <td><?= $item["fecha_entrega"]; ?></td>
                                <td class="text-center"><?= $item["meta"]; ?></td>
                                <td class="text-center"><?= $item["avance"]; ?></td>
                                <td style="width:100px;">
                                    <a href="?pg=okrs/okr/resultados_ia&id_resultado=<?= $item["id"]; ?>" title="Cargar en el formulario para probar «Mejorar este KR»">
                                        <button type="button" class="btn btn-outline-dark btn-sm ">
                                            <i class="bx bx-pencil" title="Cargar en el formulario"></i>
                                        </button>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<script>
    $(document).ready(function() {
        $('.multiples_responsables').select2();
    });
</script>

<script src="<?php echo $url; ?>agent-engine/assets/ai-field/ai-field.js" data-endpoint="/agent-engine/public/api" data-accent="#007bff" defer></script>

<script>
function LimpiarCaracteresEspeciales(textarea) {
    
    // Permite letras, números, espacios y algunos signos básicos
    textarea.value = textarea.value.replace(
        /[^a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s.,;:¡!¿?()\-%#\/]/g,
        ''
    );
}
</script>