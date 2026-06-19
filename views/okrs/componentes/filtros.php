<?php

$border_color = 'style="border: 2px solid #8BC34A;"';
$seleccionados = array(
    "vicepresidencias_fill" => "",
    "area_proceso" => "",
    "area_subproceso" => "" 
);

if($_SESSION["vicepresidencias_fill"] > 0){ $seleccionados["vicepresidencias_fill"] = $border_color; }
if($_SESSION["area_proceso_fill"] > 0){ $seleccionados["area_proceso"] = $border_color; } 
if($_SESSION["subproceso_fill"] > 0){ $seleccionados["area_subproceso"] = $border_color; }

?>

<form id="form_filtros" action="" method="POST">
    <div class="card mb-3">
        <div class="card-body">

            <div class="row">

                <div class="col-md-4 mb-2">
                    <select class="form-control form-control-sm" name="anio_fill">
                        <option value="-1" style="color: blue;">Por Año...</option>
                        <?php
                        foreach ($Array_Anio as $periodo) {
                            if ($_SESSION["anio_fill"] ==  $periodo[0]) {
                                echo '<option value="' . $periodo[0] . '" selected>' . $periodo[1] . '</option>';
                            } else {
                                echo '<option value="' . $periodo[0] . '">' . $periodo[1] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <select class="select_2_search form-control form-control-sm" name="vicepresidencias_fill" <?= $seleccionados["vicepresidencias_fill"]; ?>>
                        <option value="-1">Por Vicepresidencia...</option>
                        <?php
                        $queryVicepresidencias = mysqli_query($connect_admin, "SELECT * FROM Vicepresidencia WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND estado = 1 ORDER BY nombre ASC");
                        while ($dataVicepresidencia = mysqli_fetch_array($queryVicepresidencias)) {
                            if ($_SESSION["vicepresidencias_fill"] == $dataVicepresidencia["id"]) {
                                echo '<option value="' . $dataVicepresidencia["id"] . '" selected>' . $dataVicepresidencia["nombre"] . '</option>';
                            } else {
                                echo '<option value="' . $dataVicepresidencia["id"] . '">' . $dataVicepresidencia["nombre"] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <select class="select_2_search form-control form-control-sm" name="objetivos_fill">
                        <option value="-1">Por Objetivos Estratégicos...</option>
                        <?php
                        $queryObjEstrategicos = mysqli_query($connect_okrs, "SELECT * FROM Objetivos_estrategicos WHERE id_empresa = '" . $user_log["id_empresa"] . "'  AND anio = " . $_SESSION["anio_fill"] . " ");
                        while ($dataObjEstrategicos = mysqli_fetch_array($queryObjEstrategicos)) {
                            if ($_SESSION["objetivos_fill"] ==  $dataObjEstrategicos["id"]) {
                                echo '<option value="' . $dataObjEstrategicos["id"] . '" selected>' . $dataObjEstrategicos["objetivo"] . '</option>';
                            } else {
                                echo '<option value="' . $dataObjEstrategicos["id"] . '">' . $dataObjEstrategicos["objetivo"] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <select class="select_2_search form-control form-control-sm" name="okrs_fill">
                        <option value="-1">Por OKrs</option>
                        <?php
                        foreach ($okrs_filtro as $filtro) {
                            if ($_SESSION["okrs_fill"] == $filtro["id_okrs"]) {
                                echo '<option value="' . $filtro["id_okrs"] . '" selected>' . $filtro["objetivo"] . '</option>';
                            } else {
                                echo '<option value="' . $filtro["id_okrs"] . '">' . $filtro["objetivo"] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <!-- LISTADO DE RESPONSABLES -->
                <div class="col-md-4">
                    <?php $colaboradores_filtro = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id_empresa = '" . $user_log["id_empresa"] . "' AND estado = 1 ORDER BY nombre ASC"); ?>
                    <select class="select_2_search form-control form-control-sm" name="responsables_fill">
                        <option value="-1">Por Responsable..</option>
                        <?php foreach ($colaboradores_filtro as $colaborador): ?>
                             <option value="<?= $colaborador["id"]; ?>" <?= $colaborador["id"] == $_SESSION["responsables_fill"] ? 'selected' : '' ?>><?= $colaborador["nombre"]; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <select class="form-control form-control-sm" name="tipo_okrs_fill">
                        <option value="-1">Por Tipo...</option>
                        <?php
                        foreach ($Array_Tipo_OKR as $periodo) {
                            if ($_SESSION["tipo_okrs_fill"] ==  $periodo[0]) {
                                echo '<option value="' . $periodo[0] . '" selected>' . $periodo[1] . '</option>';
                            } else {
                                echo '<option value="' . $periodo[0] . '">' . $periodo[1] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-5">
                    <div class="row" style="align-items: center;">
                        <div class="col-md-2">
                            <label for="" style="color: black;">Periodo:</label>
                        </div>
                        <?php
                        $selectedOptions = isset($_SESSION['periodo_fill']) ? $_SESSION['periodo_fill'] : [];
                        ?>
                        <div class="col-md-2">
                            <input type="checkbox" name="periodo_fill[]" value="Q1" <?php echo in_array('Q1', $_SESSION['periodo_fill']) ? 'checked' : ''; ?> class="form-check-input"> Q1
                        </div>
                        <div class="col-md-2">
                            <input type="checkbox" name="periodo_fill[]" value="Q2" <?php echo in_array('Q2', $selectedOptions) ? 'checked' : ''; ?> class="form-check-input"> Q2
                        </div>
                        <div class="col-md-2">
                            <input type="checkbox" name="periodo_fill[]" value="Q3" <?php echo in_array('Q3', $selectedOptions) ? 'checked' : ''; ?> class="form-check-input"> Q3
                        </div>
                        <div class="col-md-2">
                            <input type="checkbox" name="periodo_fill[]" value="Q4" <?php echo in_array('Q4', $selectedOptions) ? 'checked' : ''; ?> class="form-check-input"> Q4
                        </div>
                        <div class="col-md-2">
                            <input type="checkbox" name="periodo_fill[]" value="Anual" <?php echo in_array('Anual', $selectedOptions) ? 'checked' : ''; ?> class="form-check-input"> Anual
                        </div>
                    </div>
                </div>

                <div class="col-md-12" style="font-size: 13px;">
                    Recuerde que para hacer uso de los filtros de esta visualización, se debe seleccionar o hacer uso de los campos de arriba, y despues hacer clic en el botón de filtrar
                </div>

                <div class="col-md-12 text-end mt-3">
                    <button type="submit" class="btn btn-success ">Filtrar</button>
                    <button type="button" class="btn btn-danger" onclick="ResetFiltros();">Resetear Filtros</button>
                </div>






            </div>

        </div>
    </div>
</form>

<form action="" method="POST" id="formulario_filtros">
    <input type="hidden" name="resetear_filtros" value="true">
</form>

<script>
    function ResetFiltros() {
        $("#formulario_filtros").submit();
    }
</script>

<script>
    //Reinicia la paginación para copnservar la integridad de la búsqueda 
    $('#form_filtros').on('submit', function() {
        let url = new URL(window.location.href);
        url.searchParams.delete('p');
        let finalUrl = decodeURIComponent(url.toString());
        window.history.replaceState({}, '', finalUrl);
    });
</script>

<script>
$(document).ready(function() {
    $('.select_2_search').select2();
});
</script>

