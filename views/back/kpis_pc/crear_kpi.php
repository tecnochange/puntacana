<style>
    .card,
    .card-header,
    .card-body {
        background-color: white !important;
    }

    .circleSpan {
        display: inline-block;
        /* Para que el span tenga un comportamiento adecuado como bloque */
        width: 20px;
        /* Ancho del círculo */
        height: 20px;
        /* Alto del círculo */
        background-color: #ffc107;
        /* Color de fondo */
        text-align: center;
        /* Centra el texto horizontalmente */
        line-height: 20px;
        /* Centra el texto verticalmente */
        border-radius: 50%;
        /* Hace que el span sea redondo */
        font-size: 12px;
        /* Tamaño del texto */

    }
</style>
<script>
    $(".menu_section").addClass("active");
    jQuery("#menu_kpi").css("display", "none");
    $("#bt_kpi_crear_kpi").addClass("current-page");
</script>

<?php
include("views/kpis_pc/etiquetas.php");
include("views/kpis_pc/detalle/functions.php");

$hoy = date("Y-m-d H:i:s");
$query = mysqli_query($connect_kpis, "SELECT * FROM Kpis WHERE id = '" . $_GET["id_kpir"] . "' ");
$data = mysqli_fetch_array($query);

$queryEmpresa = mysqli_query($connect_valentina, "SELECT * FROM Empresas WHERE id = '" . $_SESSION["id_empresa"] . "'");
$dataEmpresa = mysqli_fetch_array($queryEmpresa);
$mesInicio = (int)$dataEmpresa["mes_inicio"];

$queryMA6 = mysqli_query($connect_valentina, "SELECT * FROM Menu_Empresa WHERE id_empresa = " . $_POST["id_empresa"] . " AND estado = 1 AND id_menu = 6");
$dataMA6 = mysqli_fetch_array($queryMA6);

if ($_POST["indicador"] != "") {

    $mesb_1 = $mesb_2 = $mesb_3 = $mesb_4 = $mesb_5 = $mesb_6 = $mest_1 = $mest_2 = $mest_3 = $mest_4 = $mesS_1 = $mesS_2 = $mesc_1 = $mesc_2 = $mesc_3 = "";
    $mes_1 = $mes_2 = $mes_3 = $mes_4 = $mes_5 = $mes_6 = $mes_7 = $mes_8 = $mes_9 = $mes_10 = $mes_11 = $mes_12 = false;
    $meses = array();

    include("views/kpis_pc/meta_mes.php");

    if ($_POST["unidad_medida"] == 4) {
        $valueMeta = $_POST["meta_h"] . ':' . $_POST["meta_m"] . ':' . $_POST["meta_s"];
    } else {
        $valueMeta = round($_POST["meta"], 2);
    }

    // print_r($_POST);echo "<br>";

    $insertKpi = "INSERT INTO Kpis (id_empresa, id_empleado, tipo_kpi,anio, area_macro, area_proceso, subproceso, objetivo_sg, indicador, objetivo_indicador, formula, resultado_anterior, unidad_medida, tipo_calculo, meta, frecuencia, tipo_resultado, created_at) VALUES
    (" . $_SESSION["id_empresa"] . "," . $_SESSION["id_user"] . "," . $_POST["tipo_kpi"] . "," . $_POST["anio"] . "," . $_POST["vicepresidencia"] . "," . $_POST["area"] . ",'" . $_POST["unidad_organizativa"] . "','" . $_POST["objetivo_sg"] . "','" . htmlspecialchars($_POST['indicador'], ENT_QUOTES, 'UTF-8') . "'
    ,'" . htmlspecialchars($_POST['objetivo_indicador'], ENT_QUOTES, 'UTF-8') . "','" . htmlspecialchars($_POST['formula_calculo'], ENT_QUOTES, 'UTF-8') . "','" . $_POST["resultado_anterior"] . "'," . $_POST["unidad_medida"] . "," . $_POST["tipo_calculo"] . ",'" . $valueMeta . "'," . $_POST["frecuencia"] . "," . $_POST["tipo_resultado"] . ",'$hoy')";

    // echo "<br>$insertKpi";

    // echo "<br>".$julio;

    // echo "<br>INSERT INTO Frecuencia_Kpis (id_kpi, id_empresa, tipo, enero, febrero, marzo, abril, mayo, junio, julio, agosto, septiembre, octubre, noviembre, diciembre, created_at)
    // VALUES ($id_kpi," . $_SESSION["id_empresa"] . "," . $_POST["frecuencia"] . ",'$enero','$febrero','$marzo','$abril','$mayo','$junio','$julio','$agosto','$septiembre','$octubre','$noviembre','$diciembre','$hoy')";

    mysqli_query($connect_kpis, $insertKpi);
    $id_kpi = mysqli_insert_id($connect_kpis);

    foreach ($Array_tipo_kpi_PC1 as $tipoKpi) {
        if ($_POST["tipo_kpi"] ==  $tipoKpi[0]) {
            $tipoKpi =  $tipoKpi[1];
        }
    }

    $accion = 'CREACIÓN';
    $descripcion = 'Creación de ' . $dataMA6["nombre"] . ' ' . $tipoKpi . ': ' . htmlspecialchars($_POST['indicador'], ENT_QUOTES, 'UTF-8');

    $auditoria = "INSERT INTO Auditoria_Kpi (id_empresa,id_empleado,accion,descripcion,id_kpi,tipo_kpi,created_at)
	    VALUES (" . $_POST["id_empresa"] . ", " . $_POST["id_user"] . ",'$accion','$descripcion',$id_kpi," . $_POST["tipo_kpi"] . ",'$hoy')";
    // echo $auditoria;
    mysqli_query($connect_kpis, $auditoria);

    if ($id_kpi > 0) {

        $insertFrecuencia = "INSERT INTO Frecuencia_Kpis (id_kpi, id_empresa, tipo, enero, febrero, marzo, abril, mayo, junio, julio, agosto, septiembre, octubre, noviembre, diciembre, created_at)
    VALUES ($id_kpi," . $_SESSION["id_empresa"] . "," . $_POST["frecuencia"] . ",'$enero','$febrero','$marzo','$abril','$mayo','$junio','$julio','$agosto','$septiembre','$octubre','$noviembre','$diciembre','$hoy')";


        // echo "<br>$insertFrecuencia";

        mysqli_query($connect_kpis, $insertFrecuencia);
        $id_frecuencia = mysqli_insert_id($connect_kpis);
        if ($id_frecuencia > 0) {
            $updateKpi = "UPDATE Kpis SET obj_meses = $id_frecuencia WHERE id = $id_kpi";
            mysqli_query($connect_kpis, $updateKpi);

            echo '<script> window.location.href = "?pg=kpis_pc/detalle/asignar_colaborador&id_kpi=' . $id_kpi . '&id_frecuencia=' . $id_frecuencia . '";</script>';
        } else {
            echo '<script> alert("Algo Salio mal, intentelo nuevamente");</script>';
        }
    } else {
        echo '<script> alert("Algo Salio mal, intentelo nuevamente");</script>';
    }
}
include("views/kpis_pc/detalle/modal_tipo.php");
$Array_tipo_kpi_PC1 = array(
    array("1", $etiquetaKpiE),
    array("2", $etiquetaKpiT),
);
$querySM64 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 6 AND id_submenu = 36");
$dataSM64 = mysqli_fetch_array($querySM64);
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header" style="background-color: #FFFFFF !important;">
                <div class="row">
                    <div class="col-md-12" style="text-align: start !important;">
                        <h4><i class="fas fa-chart-line" id="iconCabecera"></i>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo $dataSM64["nombre"]; ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
<div class="container-fluid">

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <form action="" method="post" id="formulario_general">
                            <input type="hidden" name="id_empresa" id="id_empresa" value="<?php echo $_SESSION["id_empresa"]; ?>">
                            <input type="hidden" name="id_area_kpi" id="id_area_kpi" value="<?php echo $data["area"]; ?>">
                            <input type="hidden" name="id_vicepresidencia_kpi" id="id_vicepresidencia_kpi" value="<?php echo $data["unidad_corporativa"]; ?>">
                            <input type="hidden" name="id_UnidadOrg" id="id_UnidadOrg" value="<?php echo $data["unidad_organizativa"]; ?>">
                            <input type="hidden" name="editar_frecuencia" id="editar_frecuencia" value="1">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="tipo_kpi"><?php echo $etiquetaKpiTK; ?> *</label>
                                        <select name="tipo_kpi" id="tipo_kpi" class="form-control" required>
                                            <option value="">Selecciona...</option>
                                            <?php
                                            foreach ($Array_tipo_kpi_PC1 as $tipoKpi) {
                                                if ($data["tipo_kpi"] ==  $tipoKpi[0]) {
                                                    echo '<option value="' . $tipoKpi[0] . '" selected>' . $tipoKpi[1] . '</option>';
                                                } else {
                                                    echo '<option value="' . $tipoKpi[0] . '">' . $tipoKpi[1] . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-1">
                                        <label for="anio">Año *</label>
                                        <select class="form-control" name="anio" id="anio" required>
                                            <option value="">Selecciona...</option>
                                            <?php
                                            foreach ($Array_Anio as $anio) {
                                                if ($_SESSION["anio_fill"] == $anio[0]) {
                                                    echo '<option value="' . $anio[0] . '" selected>' . $anio[1] . '</option>';
                                                } else {
                                                    echo '<option value="' . $anio[0] . '">' . $anio[1] . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="vicepresidencia"><?php echo $etiquetaKpiAM; ?> *</label>
                                        <select class="multiples_responsables form-control" name="vicepresidencia" id="vicepresidencia" onchange="select_vicepresidencia(this);" required>
                                            <option value="">Seleccione <?php echo $etiquetaKpiAM; ?>..</option>
                                            <?php
                                            $queryvicepresidencias = mysqli_query($connect_valentina, "SELECT * FROM Vicepresidencia WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND estado = 1 ORDER BY nombre ASC ");
                                            while ($datavicepresidencia = mysqli_fetch_array($queryvicepresidencias)) {

                                                echo '
                                  <option value="' . $datavicepresidencia["id"] . '">' . $datavicepresidencia["nombre"] . '</option>
                                  ';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="area"><?php echo $etiquetaKpiAP; ?> *</label>
                                        <select class="multiples_responsables form-control" name="area" id="area" onchange="select_area(this);">
                                            <option value="">Seleccione <?php echo $etiquetaKpiAP; ?>..</option>

                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="unidad_organizativa"><?php echo $etiquetaKpiSP; ?></label>
                                        <select class="multiples_responsables form-control" name="unidad_organizativa" id="id_unidad_organizativa">
                                            <option value="">Seleccione <?php echo $etiquetaKpiSP; ?>..</option>

                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="objetivo_sg"><?php echo $etiquetaKpiOS; ?></label>
                                        <select class="multiples_responsables form-control" id="objetivo_sg" name="objetivo_sg">
                                            <option value="">Seleccione <?php echo $etiquetaKpiOS; ?>..</option>

                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="indicador"><?php echo $etiquetaKpiI; ?> *</label>
                                        <textarea name="indicador" id="indicador" rows="2" class="form-control" required></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="objetivo_indicador"><?php echo $etiquetaKpiOI; ?> *</label>
                                        <textarea name="objetivo_indicador" id="objetivo_indicador" rows="2" class="form-control" required></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="formula_calculo"><?php echo $etiquetaKpiFC; ?>*</label>
                                        <textarea name="formula_calculo" id="formula_calculo" rows="2" class="form-control" required></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="resultado_anterior"><?php echo $etiquetaKpiRA; ?></label>
                                        <input type="text" class="form-control numeros" name="resultado_anterior" id="resultado_anterior" value="<?php echo $data["resultado_anterior"] ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="unidad_medida"><?php echo $etiquetaKpiUM; ?> *</label>
                                        <select class="form-control" name="unidad_medida" id="unidad_medida" onchange="select_meta(this);" required>
                                            <option value="">Selecciona...</option>
                                            <?php
                                            foreach ($Array_Medicion_PC as $unidad) {
                                                if ($unidad[0] == $data["unidad_medida"]) {
                                                    echo '<option value="' . $unidad[0] . '" selected>' . $unidad[1] . '</option>';
                                                } else {
                                                    echo '<option value="' . $unidad[0] . '">' . $unidad[1] . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="tipo_resultado"><?php echo $etiquetaKpiTR; ?> * &nbsp;<span id="spanResultado" class="circleSpan" data-bs-toggle="tooltip" style="color:#365189 !important;font-size: 11px !important;" title="Ver información de los tipos de resultado"><i class="fa fa-question"></i></span></label>

                                        <select class="form-control" name="tipo_resultado" id="tipo_resultado" required>
                                            <option value="">Selecciona...</option>
                                            <?php
                                            foreach ($Array_Acumulativo_PC as $objetivo) {
                                                if ($objetivo[0] == $data["tipo_resultado"]) {
                                                    echo '<option value="' . $objetivo[0] . '" selected>' . $objetivo[1] . '</option>';
                                                } else {
                                                    echo '<option value="' . $objetivo[0] . '">' . $objetivo[1] . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                        <span id="error_tipo_resultado" style="color: red; display: none;">Por favor, selecciona un tipo de resultado para calcular la meta.</span>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="meta"><?php echo $etiquetaKpiM; ?> *</label>
                                        <div class="row" id="metaNormal" style="display:none;">
                                            <div class="col-md-12">
                                                <input type="text" class="form-control numeros" name="meta" id="meta" value="<?php echo $meta; ?>" onkeyup="return NumerosDecimales(this)">
                                            </div>
                                        </div>

                                        <div class="row" id="metaTiempo" style="display:none;">
                                            <div class="col-md-4">
                                                <input type="text" class="form-control numeros" name="meta_h" id="meta_h" value="" placeholder="HH">Horas
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control numeros" name="meta_m" id="meta_m" value="" placeholder="MM">Min.
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control numeros" name="meta_s" id="meta_s" value="" placeholder="SS">Seg.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="tipo_calculo"><?php echo $etiquetaKpiTC; ?> * &nbsp;<span id="spanCalculo" data-bs-toggle="tooltip" class="circleSpan" style="color:#365189 !important;font-size: 11px !important;" title="Ver información de los tipos de cálculo"><i class="fa fa-question"></i></span></label>
                                        <select class="form-control" name="tipo_calculo" id="tipo_calculo" required>
                                            <option value="">Selecciona...</option>
                                            <?php
                                            foreach ($Array_Tendencia_KPIS as $objetivo) {
                                                if ($objetivo[0] == $data["tipo_calculo"]) {
                                                    echo '<option value="' . $objetivo[0] . '" selected>' . $objetivo[1] . '</option>';
                                                } else {
                                                    echo '<option value="' . $objetivo[0] . '">' . $objetivo[1] . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="frecuencia"><?php echo $etiquetaKpiF; ?> *</label>
                                        <select class="form-control" name="frecuencia" id="frecuencia" required>
                                            <option value="">Selecciona...</option>
                                            <?php
                                            foreach ($Array_Frecuencia_PC as $frecuencia) {
                                                if ($frecuencia[0] == $data["frecuencia"]) {
                                                    echo '<option value="' . $frecuencia[0] . '" selected>' . $frecuencia[1] . '</option>';
                                                } else {
                                                    echo '<option value="' . $frecuencia[0] . '">' . $frecuencia[1] . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>

                                </div>
                            </div>
                            <script>
                                $(document).ready(function() {
                                    $('.multiples_responsables').select2();
                                });
                            </script>
                            <div class="col-md-12 dinamico" id="lista_mes_padre" style="display: none">
                                <label style="color: #F44336;">Ingresa la meta para cada etapa de seguimiento.</label>

                                <table class="table table-bordered" width="100%">
                                    <tr>
                                        <?php
                                        $queryEmpresa = mysqli_query($connect_valentina, "SELECT * FROM Empresas WHERE id = '" . $_SESSION["id_empresa"] . "'");
                                        $dataEmpresa = mysqli_fetch_array($queryEmpresa);
                                        // $mesInicio = (int)$dataEmpresa["mes_inicio"];
                                        $inicio = new DateTime($dataEmpresa["mes_inicio"]);
                                        $mesInicio = (int)$inicio->format('m');
                                        // $listadoMesFiscal = obtenerListadoDesdeMes($mesInicio);
                                        $listadoMesFiscal = obtenerListadoDesdeMes($dataEmpresa["mes_inicio"]);
                                        // print_r($listadoMesFiscal);
                                        $keys = array_keys($listadoMesFiscal);
                                        for ($i = 0; $i < count($keys); $i++) {
                                            $indice = $keys[$i];
                                            $mes = $listadoMesFiscal[$indice];
                                            // echo "Mes $indice: $mes\n";
                                            // foreach ($Array_Meses_PC as $mes) {
                                            $meta_mes = '';
                                            foreach ($obj_meses as $meta) {
                                                if ($meta["mes"] == $indice) {
                                                    $meta_mes = $meta["meta"];
                                                }
                                            }
                                            $desplegable = '<select class="form-control" name="mes_' . $indice . '" ><option value="">...</option>';
                                            for ($x = 5; $x <= 3000; $x += 5) {
                                                if ($meta_mes == $x) {
                                                    $desplegable .= '
										<option value="' . $x . '" selected>' . $x . '%</option>
									';
                                                } else {
                                                    $desplegable .= '
										<option value="' . $x . '">' . $x . '%</option>
									';
                                                }
                                            }
                                            $desplegable .= '</select>';
                                            echo '
								<td align="center">
									' . $mes . '<br>
									<input type="text" name="nombre_mes_' . $indice . '" value="' . $indice . '" hidden >
									<input type="text" class="form-control numeros mes_per campoMensual" name="mes_' . $indice . '" value="' . $meta_mes . '" id="mensual_' . $indice . '"  onkeypress="return NumerosDecimales(this)">

								</td>
							';
                                        }
                                        ?>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-12 dinamico" id="lista_mes_padre_horas" style="display: none">
                                <label style="color: #F44336;">Ingresa la meta para cada etapa de seguimiento.</label>

                                <table class="table table-bordered" width="100%">
                                    <tr>
                                        <?php
                                        $queryEmpresa = mysqli_query($connect_valentina, "SELECT * FROM Empresas WHERE id = '" . $_SESSION["id_empresa"] . "'");
                                        $dataEmpresa = mysqli_fetch_array($queryEmpresa);
                                        // $mesInicio = (int)$dataEmpresa["mes_inicio"];
                                        $inicio = new DateTime($dataEmpresa["mes_inicio"]);
                                        $mesInicio = (int)$inicio->format('m');
                                        // $listadoMesFiscal = obtenerListadoDesdeMes($mesInicio);
                                        $listadoMesFiscal = obtenerListadoDesdeMes($dataEmpresa["mes_inicio"]);
                                        // print_r($listadoMesFiscal);
                                        $keys = array_keys($listadoMesFiscal);
                                        for ($i = 0; $i < count($keys); $i++) {
                                            $indice = $keys[$i];
                                            $mes = $listadoMesFiscal[$indice];
                                            $meta_mes = '';
                                            foreach ($obj_meses as $meta) {
                                                if ($meta["mes"] == $indice) {
                                                    $meta_mes = $meta["meta"];
                                                }
                                            }
                                            $desplegable = '<select class="form-control" name="mes_' . $indice . '" ><option value="">...</option>';
                                            for ($x = 5; $x <= 3000; $x += 5) {
                                                if ($meta_mes == $x) {
                                                    $desplegable .= '
										<option value="' . $x . '" selected>' . $x . '%</option>
									';
                                                } else {
                                                    $desplegable .= '
										<option value="' . $x . '">' . $x . '%</option>
									';
                                                }
                                            }
                                            $desplegable .= '</select>';
                                            echo '
								<td align="center">
									' . $mes . '<br>
									<input type="text" name="nombre_mes_' . $indice . '" value="' . $indice . '" hidden >

									<input type="text" class="form-control numeros mes_per_h campoMensualH" name="mes_h_' . $indice . '" value="" id="mensual_h_' . $indice . '"  placeholder="horas">

									<input type="text" class="form-control numeros mes_per_m campoMensualM" name="mes_m_' . $indice . '" value="" id="mensual_m_' . $indice . '"  placeholder="minutos">

									<input type="text" class="form-control numeros mes_per_s campoMensualS" name="mes_s_' . $indice . '" value="" id="mensual_s_' . $indice . '"  placeholder="segundos">

								</td>
							';
                                        }
                                        ?>
                                    </tr>
                                </table>
                            </div>

                            <br>
                            <?php include("views/kpis_pc/detalle/meses.php"); ?>

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
        <table style="display: none">
            <tbody id="referencia_nuevo">
                <tr>
                    <td>
                        <input type="text" class="form-control" name="descripcion_sub[]" value="" placeholder="Descripción..." required>
                        <input type="hidden" name="id_objetivo[]" value="0">
                    </td>
                    <td>
                        <input type="text" class="form-control meta_subO numeros" name="meta_sub[]" value="" placeholder="Meta..." required onkeypress="return PrevenirDefault(event);">
                    </td>
                    <td>
                        <input type="text" class="form-control numeros mes_ponderado" name="ponderado_sub[]" placeholder="Peso (%) *" value="" required onkeypress="return NumerosDecimales(this)" onChange="AdicionarAPonderado()">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm" title="Nuevo" onClick="Eliminar(this)">
                            x
                        </button>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <table class="table">
                            <tr>
                                <?php
                                foreach ($Array_Meses_PC as $mes) {
                                    $meta_mes = 0;
                                    foreach ($obj_meses as $meta) {
                                        if ($meta["mes"] == $mes[0]) {
                                            $meta_mes = $meta["meta"];
                                        }
                                    }
                                    echo '
                            <td align="center">
                                ' . $mes[1] . '<br>
                                <input type="text" class="form-control mes_subo mes_fila_' . $total_hijos . ' numeros " name="mes_sub_' . $mes[0] . '[]" value="' . $meta_mes . '" required onkeyup="return NumerosDecimales(this)"  onChange="AdicionarAMetas(this)">

                            </td>
                        ';
                                }
                                ?>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<script>
    var api_admin = '<?php echo $url; ?>/api/administrar/';
    var api_kpi = '<?php echo $url; ?>/api/kpis_pc/';

    $(function() {
        $(".numeros").keydown(function(event) {
            //alert(event.keyCode);
            if ((event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105) && event.keyCode !== 190 && event.keyCode !== 110 && event.keyCode !== 8 && event.keyCode !== 9 && event.keyCode !== 109 && event.keyCode !== 189 && event.keyCode !== 46) {
                return false;
            }
        });
    });

    function SoloNumeros(evt) {
        // code is the decimal ASCII representation of the pressed key.
        var code = (evt.which) ? evt.which : evt.keyCode;

        if (code == 8) { // backspace.
            return true;
        } else if (code >= 48 && code <= 57) { // is a number.
            return true;
        } else { // other keys.
            return false;
        }
    }

    function PrevenirDefault(evt) {
        return false;
    }

    // function NumerosDecimales(elemet) {
    //     elemet.value = elemet.value.replace(/[^0-9,.]/g, '').replace(/,/g, '.');
    // }

    function select_meta() {
        $("#unidad_medida option:selected").each(function() {
            unidad = $(this).val();
            switch (unidad) {
                case '1':
                    $('#meta').attr('placeholder', 'Solo numero entero');
                    document.getElementById('meta').removeAttribute('onkeypress');
                    document.getElementById('meta').setAttribute('type', 'number');
                    break;
                case '2':
                    $('#meta').attr('placeholder', 'Porcentaje con el punto como separador de decimales');
                    document.getElementById('meta').setAttribute('onkeypress', 'return filterFloat(event,this);');
                    document.getElementById('meta').setAttribute('type', 'text');
                    break;
                case '3':
                    $('#meta').attr('placeholder', 'Numero entero sin separador');
                    document.getElementById('meta').setAttribute('onkeypress', 'return SoloNumeros(event,this)');
                    document.getElementById('meta').setAttribute('type', 'text');
                    break;
                case '4':
                    $('#meta').attr('placeholder', 'Cantidad de horas en hh:mm:ss');
                    document.getElementById('meta').removeAttribute('onkeypress');
                    document.getElementById('meta').setAttribute('type', 'text');
                    break;
                default:
                    $('#meta').attr('placeholder', '');
                    break;
            }

        });

    }

    function select_vicepresidencia() {

        $("#vicepresidencia option:selected").each(function() {
            var empresa = $("#id_empresa").val();
            var area = $("#id_area_kpi").val();
            id = $(this).val();
            $.post(api_admin + "cargar_vicepresidencias.php", {
                id: id,
                id_empresa: empresa,
                area: area
            }, function(data) {
                if (id != '') {
                    // $('#area').css('display', 'block');
                    $("#area").html(data);
                    var foption = $('#area option:first');
                    var soptions = $('#area option:not(:first)').sort(function(a, b) {
                        return a.text == b.text ? 0 : a.text < b.text ? -1 : 1
                    });
                    $('#area').html(soptions).prepend(foption);
                } else {
                    // $('#area').css('display', 'none');
                    $("#area").html('');
                }

            });
        });
        $("#vicepresidencia option:selected").each(function() {
            var empresa = $("#id_empresa").val();
            var vicepresidencia = $("#vicepresidencia").val();
            var area = $("#area_proceso").val();
            var idVicepresidencia = $("#area_macro").val();
            var idUnidadOrg = $("#subproceso").val();
            var idObjetivo = $("#id_objetivo_sg").val();
            id = $(this).val();
            $.post(api_kpi + "cargar_objetivo_sg.php", {
                id: id,
                id_empresa: empresa,
                area: area,
                vicepresidencia: vicepresidencia,
                idVicepresidencia: idVicepresidencia,
                idObjetivo: idObjetivo
            }, function(data) {
                if (id != '') {
                    $("#objetivo_sg").html(data);
                } else {
                    $("#objetivo_sg").html('');
                }

            });
        });
    }

    function select_area() {
        $("#area option:selected").each(function() {
            var empresa = $("#id_empresa").val();
            var vicepresidencia = $("#vicepresidencia").val();
            var area = $("#id_area_kpi").val();
            var idVicepresidencia = $("#id_vicepresidencia_kpi").val();
            var idUnidadOrg = $("#id_UnidadOrg").val();
            id = $(this).val();
            $.post(api_admin + "cargar_unidad_organizativa.php", {
                id: id,
                id_empresa: empresa,
                area: area,
                vicepresidencia: vicepresidencia,
                idVicepresidencia: idVicepresidencia,
                idUnidadOrg: idUnidadOrg
            }, function(data) {
                if (id != '') {
                    // $('#unidad_organizativa').css('display', 'block');
                    $("#id_unidad_organizativa").html(data);
                    var foption = $('#id_unidad_organizativa option:first');
                    var soptions = $('#id_unidad_organizativa option:not(:first)').sort(function(a, b) {
                        return a.text == b.text ? 0 : a.text < b.text ? -1 : 1
                    });
                    $('#id_unidad_organizativa').html(soptions).prepend(foption);
                } else {
                    $("#id_unidad_organizativa").html('');
                }

            });
        });
        // $("#area option:selected").each(function() {
        //     var empresa = $("#id_empresa").val();
        //     var vicepresidencia = $("#vicepresidencia").val();
        //     var area = $("#id_area_kpi").val();
        //     var idVicepresidencia = $("#id_vicepresidencia_kpi").val();
        //     var idUnidadOrg = $("#id_UnidadOrg").val();
        //     id = $(this).val();
        //     $.post(api_kpi + "cargar_objetivo_sg.php", {
        //         id: id,
        //         id_empresa: empresa,
        //         area: area,
        //         vicepresidencia: vicepresidencia,
        //         idVicepresidencia: idVicepresidencia,
        //         idUnidadOrg: idUnidadOrg
        //     }, function(data) {
        //         if (id != '') {
        //             $("#objetivo_sg").html(data);
        //             var foption = $('#objetivo_sg option:first');
        //             var soptions = $('#objetivo_sg option:not(:first)').sort(function(a, b) {
        //                 return a.text == b.text ? 0 : a.text < b.text ? -1 : 1
        //             });
        //             $('#objetivo_sg').html(soptions).prepend(foption);
        //         } else {
        //             $("#objetivo_sg").html('');
        //         }

        //     });
        // });
    }

    function select_objetivo() {
        $("#unidad_organizativa option:selected").each(function() {
            var empresa = $("#id_empresa").val();
            var vicepresidencia = $("#vicepresidencia").val();
            var area = $("#id_area").val();
            var idVicepresidencia = $("#id_vicepresidencia").val();
            var idUnidadOrg = $("#id_UnidadOrg").val();
            id = $(this).val();
            $.post(api_kpi + "cargar_objetivo_sg.php", {
                id: id,
                id_empresa: empresa,
                area: area,
                vicepresidencia: vicepresidencia,
                idVicepresidencia: idVicepresidencia,
                idUnidadOrg: idUnidadOrg
            }, function(data) {
                if (id != '') {

                    $("#objetivo_sg").html(data);
                    var foption = $('#objetivo_sg option:first');
                    var soptions = $('#objetivo_sg option:not(:first)').sort(function(a, b) {
                        return a.text == b.text ? 0 : a.text < b.text ? -1 : 1
                    });
                    $('#objetivo_sg').html(soptions).prepend(foption);
                } else {
                    // $('#objetivo_sg').css('display', 'none');
                    $("#objetivo_sg").html('');
                }

            });
        });
    }

    <?php
    if ($id) {
        echo 'VerSubObjetivos(' . $data["tipo"] . ')';
    }
    ?>
    var activar_objetivo = false;

    function EliminarObjetivoIndividual(val) {
        if (activar_objetivo == false) {
            $("#cont_modal_general").html('Esta a punto de eliminar este objetivo , esta acción es irreversible ¿Está seguro?<br><br>');
            $("#cont_modal_general").append('<button type="button" class="btn btn-danger" style="margin-right: 10px;" onclick="activar_objetivo = true; EliminarObjetivoIndividual(' + val + ')">Eliminar</button>');
            $("#cont_modal_general").append('<button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close">Cancelar</button>');
            $("#modal_general").modal('show');
        } else {
            jQuery.ajax({
                    url: api + "eliminar_objetivo_individual.php",
                    type: 'post',
                    data: {
                        id: val,
                        url: "?pg=desempenio_pc/mis_objetivos"
                    },
                }).done(function(resp) {
                    $("#xscript").html(resp);
                })
                .fail(function(resp) {
                })
                .always(function(resp) {});
        }

    }


    var activar = false;

    function Borrar_Pregunta(val) {
        if (activar == false) {
            $("#cont_modal_general").html('Estas a punto de eliminar esta actividad, esta acción es irreversible ¿Estás seguro?<br><br>');
            $("#cont_modal_general").append('<button type="button" class="btn btn-success" style="margin-right: 10px;" onclick="activar = true; Borrar_Pregunta(' + val + ')">Aprobar</button>');
            $("#cont_modal_general").append('<button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">Cancelar</button>');
            $("#modal_general").modal('show');
        } else {
            jQuery.ajax({
                    url: api + "borrar_pregunta.php",
                    type: 'post',
                    data: {
                        id: val,
                        url: "?pg=desempenio_pc/objetivos_propios_lista&p=<?php echo $id_periodo; ?>&id=<?php echo $id; ?>"
                    },
                }).done(function(resp) {
                    $("#xscript").html(resp);
                })
                .fail(function(resp) {
                    // console.log(resp);
                })
                .always(function(resp) {});
        }
    }

    var activar_objetivo = false;

    function Borrar_Objetivo_Propio(val) {
        if (activar_objetivo == false) {
            $("#cont_modal_general").html('Estas a punto de eliminar este objetivo propio y sus actividades relacionadas, esta acción es irreversible ¿Estás seguro?<br><br>');
            $("#cont_modal_general").append('<button type="button" class="btn btn-danger" style="margin-right: 10px;" onclick="activar_objetivo = true; Borrar_Objetivo_Propio(' + val + ')">Eliminar</button>');
            $("#cont_modal_general").append('<button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close">Cancelar</button>');
            $("#modal_general").modal('show');
        } else {
            jQuery.ajax({
                    url: api + "borrar_objetivo_propio.php",
                    type: 'post',
                    data: {
                        id: val,
                        url: "?pg=desempenio_pc/objetivos_propios_lista&p=<?php echo $id_periodo; ?>"
                    },
                }).done(function(resp) {
                    $("#xscript").html(resp);
                })
                .fail(function(resp) {
                    // console.log(resp);
                })
                .always(function(resp) {});
        }

    }

    function NumerosDecimales(input) {
        // Permitir números, puntos y comas
        let valor = input.value;

        // Eliminar cualquier carácter que no sea un dígito, un punto o una coma
        valor = valor.replace(/[^0-9.,-]/g, '');

        // Si hay más de un punto o una coma, eliminar extras
        let puntos = (valor.match(/\./g) || []).length;
        let comas = (valor.match(/,/g) || []).length;

        if (puntos > 1) {
            valor = valor.replace(/\.(?=.*\.)/g, ''); // Eliminar puntos adicionales
        }

        if (comas > 1) {
            valor = valor.replace(/,(?=.*,)/g, ''); // Eliminar comas adicionales
        }

        // Actualizar el valor del input
        input.value = valor;
    }

    var spanResultado = document.getElementById("spanResultado");
    var spanCalculo = document.getElementById("spanCalculo");

    spanResultado.onclick = function() {
        jQuery.ajax({
                url: api_kpi + "ver_tresultado.php",
                type: 'post',
                data: {
                    id_empresa: <?php echo $_SESSION["id_empresa"]; ?>
                },
            }).done(function(resp) {
                $("#modal_tipo").modal("show");
                $("#modal_contenido_tipo").html(resp);
            })
            .fail(function(resp) {
                // console.log(resp);
            })
            .always(function(resp) {});
    }

    spanCalculo.onclick = function() {
        jQuery.ajax({
                url: api_kpi + "ver_tcalculo.php",
                type: 'post',
                data: {
                    id_empresa: <?php echo $_SESSION["id_empresa"]; ?>
                },
            }).done(function(resp) {
                $("#modal_tipo").modal("show");
                $("#modal_contenido_tipo").html(resp);
            })
            .fail(function(resp) {
                // console.log(resp);
            })
            .always(function(resp) {});
    }
</script>
<script>
    // Selecciona todos los inputs con la clase 'soloNumeros'
    const horas = document.querySelectorAll('.mes_per_h');
    const minutos = document.querySelectorAll('.mes_per_m');
    const segundos = document.querySelectorAll('.mes_per_s');

    // Recorre todos los elementos seleccionados y les agrega el evento
    horas.forEach(input => {
        input.addEventListener('input', function(event) {
            // Reemplaza cualquier cosa que no sea un número
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });

    minutos.forEach(input => {
        input.addEventListener('input', function(event) {
            // Reemplaza cualquier cosa que no sea un número
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });

    segundos.forEach(input => {
        input.addEventListener('input', function(event) {
            // Reemplaza cualquier cosa que no sea un número
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });
</script>
<script src="<?php echo $url; ?>views/kpis_pc/kpis.js"></script>