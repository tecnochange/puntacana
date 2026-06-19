<?php
$queryEmpresa = mysqli_query($connect_admin, "SELECT * FROM Empresas WHERE id = '" . $user_log["id_empresa"] . "'");
$dataEmpresa = mysqli_fetch_array($queryEmpresa);
// $mesInicio = (int)$dataEmpresa["mes_inicio"];
$mesInicio = $dataEmpresa["mes_inicio"];
$mesesFiltro = obtenerListadoDesdeMes($mesInicio);

$border_color = 'style="border: 2px solid #8BC34A;"';
$seleccionados = array(
    "tipo" => "",
    "frecuencia" => "",
    "resultado" => "",
    "inicia" => "",
    "termina" => "",
    "calculo" => "",
    "unidad" => "",
    "area_macro" => "",
    "area_proceso" => "",
    "area_subproceso" => "",
    "objetivo" => ""
);

if (isset($_SESSION["tipo_kpi_fill"]) && $_SESSION["tipo_kpi_fill"] > 0) { $seleccionados["tipo"] = $border_color; }
if (isset($_SESSION["frecuencia_fill"]) && $_SESSION["frecuencia_fill"] > 0) { $seleccionados["frecuencia"] = $border_color; }
if (isset($_SESSION["tipo_resultado_fill"]) && $_SESSION["tipo_resultado_fill"] > 0) { $seleccionados["resultado"] = $border_color; }
if (isset($_SESSION["periodo_inicio_fill"]) && $_SESSION["periodo_inicio_fill"] > 0) { $seleccionados["inicia"] = $border_color; }
if (isset($_SESSION["periodo_fin_fill"]) && $_SESSION["periodo_fin_fill"] > 0) { $seleccionados["termina"] = $border_color; }
if (isset($_SESSION["tipo_calculo_fill"]) && $_SESSION["tipo_calculo_fill"] > 0) { $seleccionados["calculo"] = $border_color; }
if (isset($_SESSION["unidad_medida_fill"]) && $_SESSION["unidad_medida_fill"] > 0) { $seleccionados["unidad"] = $border_color; }
if (isset($_SESSION["area_macro_fill"] )&& $_SESSION["area_macro_fill"] > 0) { $seleccionados["area_macro"] = $border_color; }
if (isset($_SESSION["area_proceso_fill"]) && $_SESSION["area_proceso_fill"] > 0) { $seleccionados["area_proceso"] = $border_color; }
if (isset($_SESSION["subproceso_fill"]) && $_SESSION["subproceso_fill"] > 0) { $seleccionados["area_subproceso"] = $border_color; }
if (isset($_SESSION["objetivo_sg_fill"]) && $_SESSION["objetivo_sg_fill"] > 0) { $seleccionados["objetivo"] = $border_color; }
?>

<form id="form_filtros" action="" method="POST">
    <div class="card mb-3">
        <div class="card-body">

            <div class="row">

                <div class="col-md-3 mb-2">
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

                <div class="col-md-3 mb-2">
                    <select class="form-control form-control-sm" name="tipo_kpi_fill" <?= $seleccionados["tipo"]; ?>>
                        <option value="-1">Tipo de Kpi...</option>
                        <?php
                        foreach ($Array_tipo_kpi_PC1 as $tipo) {
                            if ($_SESSION["tipo_kpi_fill"] ==  $tipo[0]) {
                                echo '<option value="' . $tipo[0] . '" selected>' . $tipo[1] . '</option>';
                            } else {
                                echo '<option value="' . $tipo[0] . '">' . $tipo[1] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-3 mb-2">
                    <select class="form-control form-control-sm" name="frecuencia_fill" <?= $seleccionados["frecuencia"]; ?>>
                        <option value="-1">Frecuencia...</option>
                        <?php
                        foreach ($Array_Frecuencia_PC as $frecuencia) {
                            if ($_SESSION["frecuencia_fill"] ==  $frecuencia[0]) {
                                echo '<option value="' . $frecuencia[0] . '" selected>' . $frecuencia[1] . '</option>';
                            } else {
                                echo '<option value="' . $frecuencia[0] . '">' . $frecuencia[1] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-3 mb-2">
                    <select class="form-control form-control-sm" name="tipo_resultado_fill" <?= $seleccionados["resultado"]; ?>>
                        <option value="-1">Tipo de Resultado...</option>
                        <?php
                        foreach ($Array_Acumulativo_PC as $resultado) {
                            if ($_SESSION["tipo_resultado_fill"] ==  $resultado[0]) {
                                echo '<option value="' . $resultado[0] . '" selected>' . $resultado[1] . '</option>';
                            } else {
                                echo '<option value="' . $resultado[0] . '">' . $resultado[1] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-3 mb-2 mb-2">
                    <select class="form-control form-control-sm" name="periodo_inicio_fill" <?= $seleccionados["inicia"]; ?>>
                        <option value="-1">Periodo Inicio...</option>
                        <?php
                        foreach ($mesesFiltro as $key => $value) {
                            if ($_SESSION["periodo_inicio_fill"] ==  $key) {
                                echo '<option value="' . $key . '" selected>' . $value . '</option>';
                            } else {
                                echo '<option value="' . $key . '">' . $value . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-3 mb-2">
                    <select class="form-control form-control-sm" name="periodo_fin_fill" <?= $seleccionados["termina"]; ?>>
                        <option value="-1">Periodo Fin...</option>
                        <?php
                        foreach ($mesesFiltro as $key => $value) {
                            if ($_SESSION["periodo_fin_fill"] ==  $key) {
                                echo '<option value="' . $key . '" selected>' . $value . '</option>';
                            } else {
                                echo '<option value="' . $key . '">' . $value . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-3 mb-2">
                    <select class="form-control form-control-sm" name="tipo_calculo_fill" <?= $seleccionados["calculo"]; ?>>
                        <option value="-1">Tipo de Cálculo...</option>
                        <?php
                        foreach ($Array_Tendencia_KPIS as $calculo) {
                            if ($_SESSION["tipo_calculo_fill"] ==  $calculo[0]) {
                                echo '<option value="' . $calculo[0] . '" selected>' . $calculo[1] . '</option>';
                            } else {
                                echo '<option value="' . $calculo[0] . '">' . $calculo[1] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-3 mb-2">
                    <select class="form-control form-control-sm" name="unidad_medida_fill" <?= $seleccionados["unidad"]; ?>>
                        <option value="-1">Unidad de Medida...</option>
                        <?php
                        foreach ($Array_Medicion_PC as $unidad) {
                            if ($_SESSION["unidad_medida_fill"] ==  $unidad[0]) {
                                echo '<option value="' . $unidad[0] . '" selected>' . $unidad[1] . '</option>';
                            } else {
                                echo '<option value="' . $unidad[0] . '">' . $unidad[1] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-3 mb-2">
                    <select class="form-control form-control-sm select_2_search" name="area_macro_fill" id="area_macro_fill" onchange="ListaFiltroProceso(this.value)" <?= $seleccionados["area_macro"]; ?>>
                        <option value="-1">Área Macro...</option>
                        <?php
                        $queryVicepresidencias = mysqli_query($connect_admin, "SELECT * FROM Vicepresidencia WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND estado = 1 ORDER BY nombre ASC");
                        while ($dataVicepresidencia = mysqli_fetch_array($queryVicepresidencias)) {
                            if ($_SESSION["area_macro_fill"] == $dataVicepresidencia["id"]) {
                                echo '<option value="' . $dataVicepresidencia["id"] . '" selected>' . $dataVicepresidencia["nombre"] . '</option>';
                            } else {
                                echo '<option value="' . $dataVicepresidencia["id"] . '">' . $dataVicepresidencia["nombre"] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>


                <div class="col-md-3 mb-2">
                    <select class="form-control form-control-sm select_2_search" name="area_proceso_fill" id="area_proceso_fill" onchange="ListaFiltroSubProceso()" <?= $seleccionados["area_proceso"]; ?>>
                        <option value="-1">Área Proceso...</option>
                        <?php
                        $queryVicepresidencias = mysqli_query($connect_admin, "SELECT * FROM Areas WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND estado = 1 ORDER BY nombre ASC");
                        while ($dataVicepresidencia = mysqli_fetch_array($queryVicepresidencias)) {
                            if ($_SESSION["area_proceso_fill"] == $dataVicepresidencia["id"]) {
                                echo '<option value="' . $dataVicepresidencia["id"] . '" selected>' . $dataVicepresidencia["nombre"] . '</option>';
                            } else {
                                echo '<option value="' . $dataVicepresidencia["id"] . '">' . $dataVicepresidencia["nombre"] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-3 mb-2">
                    <select class="form-control form-control-sm select_2_search" name="subproceso_fill" id="subproceso_fill" onchange="ListaFiltroObjetivosSG()" <?= $seleccionados["area_subproceso"]; ?>>
                        <option value="-1">Subproceso...</option>
                        <?php
                        $sentencia_sub = "
                SELECT
                    Estructura_Empresa.id, Estructura_Empresa.unidad_organizativa, Vicepresidencia.nombre AS vicepresidencia, Areas.nombre AS area
                FROM
                    Estructura_Empresa 
                    LEFT JOIN Vicepresidencia ON Vicepresidencia.id = Estructura_Empresa.vicepresidencia
                    LEFT JOIN Areas ON Areas.id = Estructura_Empresa.area   
                WHERE
                    Estructura_Empresa.id_empresa = '" . $_SESSION["id_empresa"] . "' AND Estructura_Empresa.estado = 1 AND Estructura_Empresa.unidad_organizativa != ''
                ORDER BY
                    Estructura_Empresa.unidad_organizativa;
                ";
                        $queryVicepresidencias = mysqli_query($connect_admin, $sentencia_sub);
                        while ($dataVicepresidencia = mysqli_fetch_array($queryVicepresidencias)) {
                            if ($_SESSION["subproceso_fill"] == $dataVicepresidencia["id"]) {
                                echo '<option value="' . $dataVicepresidencia["id"] . '" selected>' . $dataVicepresidencia["vicepresidencia"] . ' / ' . $dataVicepresidencia["area"] . ' / ' . $dataVicepresidencia["unidad_organizativa"] . '</option>';
                            } else {
                                echo '<option value="' . $dataVicepresidencia["id"] . '" >' . $dataVicepresidencia["vicepresidencia"] . ' / ' . $dataVicepresidencia["area"] . ' / ' . $dataVicepresidencia["unidad_organizativa"] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-3 mb-2">
                    <select class="form-control form-control-sm" name="objetivo_sg_fill" id="objetivo_sg_fill">
                        <option value="-1">Seleccione objetivo SG...</option>
                        <?php
                        $sentencia_obj_sg = "
                SELECT
                    *
                FROM
                    Objetivo_Sg 
                   
                WHERE
                    id_empresa = '" . $user_log["id_empresa"] . "' AND estado = 1 
                ";
                        $queryObj_SG = mysqli_query($connect_kpis, $sentencia_obj_sg);
                        while ($dataObj_SG = mysqli_fetch_array($queryObj_SG)) {
                            if ($_SESSION["objetivo_sg_fill"] == $dataObj_SG["id"]) {
                                echo '<option value="' . $dataObj_SG["id"] . '" selected>' . $dataObj_SG["objetivo"] . '</option>';
                            } else {
                                echo '<option value="' . $dataObj_SG["id"] . '" >' . $dataObj_SG["objetivo"] . ' </option>';
                            }
                        }
                        ?>
                    </select>
                </div>






                <div class="col-md-12 mb-2" style="font-size: 13px;">
                    Recuerde que para hacer uso de los filtros de esta visualización, se debe seleccionar o hacer uso de los campos de arriba, y despues hacer clic en el botón de filtrar
                </div>

                <div class="col-md-12 text-end mt-3">
                    <button type="submit" class="btn btn-success ">Filtrar</button>
                    <button type="button" class="btn btn-danger" onclick="ResetFiltrosKpis();">Resetear Filtros</button>
                </div>

            </div>

        </div>
    </div>
</form>

<form action="" method="POST" id="formulario_filtros_kpis">
    <input type="hidden" name="resetear_filtros_kpis" value="true">
</form>

<script>
    var api = '<?php echo $url; ?>api/kpis/';

    function ListaFiltroProceso() {

        data = {
            id_empresa: <?php echo $user_log["id_empresa"]; ?>,
            id_vicepresidencia: $("#area_macro_fill").val(),
        };
        jQuery.ajax({
                url: api + "filtro_lista_proceso.php",
                type: 'post',
                data: data,
            })
            .done(function(resp) {
                $("#area_proceso_fill").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});

        ListaFiltroObjetivosSG();
    }

    function ListaFiltroSubProceso() {

        data = {
            id_empresa: <?php echo $user_log["id_empresa"]; ?>,
            id_vicepresidencia: $("#area_macro_fill").val(),
            id_area: $("#area_proceso_fill").val()
        };
        jQuery.ajax({
                url: api + "filtro_lista_subproceso.php",
                type: 'post',
                data: data,
            })
            .done(function(resp) {
                $("#subproceso_fill").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});
        ListaFiltroObjetivosSG();
    }

    function ListaFiltroObjetivosSG() {

        data = {
            id_empresa: <?php echo $user_log["id_empresa"]; ?>,
            id_vicepresidencia: $("#area_macro_fill").val(),
            id_area: $("#area_proceso_fill").val(),
            id_subproceso: $("#subproceso_fill").val()
        };
        jQuery.ajax({
                url: api + "filtro_lista_objetivos_sg.php",
                type: 'post',
                data: data,
            })
            .done(function(resp) {
                $("#objetivo_sg_fill").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});
    }

    function ResetFiltrosKpis() {
        $("#formulario_filtros_kpis").submit();
    }
</script>

<script>
    $(document).ready(function() {
        $('.select_2_search').select2();
    });
</script>

<?php
//ESTA SOLICITUD VIENE DE RESETEAR LOS FILTROS 
if (isset($_POST["resetear_filtros"])) {
    $_SESSION["tipo_kpi_fill"] = "";
    $_SESSION["frecuencia_fill"] = "";
    $_SESSION["tipo_resultado_fill"] = "";
    $_SESSION["periodo_inicio_fill"] = "";
    $_SESSION["periodo_fin_fill"] = "";

    $_SESSION["tipo_calculo_fill"] = "";
    $_SESSION["unidad_medida_fill"] = "";

    $_SESSION["area_macro_fill"] = "";
    $_SESSION["area_proceso_fill"] = "";
    $_SESSION["subproceso_fill"] = "";
    $_SESSION["objetivo_sg_fill"] = "";
}

?>

<script>
    //Reinicia la paginación para copnservar la integridad de la búsqueda 
    $('#form_filtros').on('submit', function() {
        let url = new URL(window.location.href);
        url.searchParams.delete('p');
        let finalUrl = decodeURIComponent(url.toString());
        window.history.replaceState({}, '', finalUrl);
    });
</script>