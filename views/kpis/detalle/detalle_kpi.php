<script>
    $(document).ready(function() {
        $('#menuKpis').collapse();
    });
</script>

<?php
$id = $_GET["id"];
$hoy = date("Y-m-d H:i:s");

if($_POST["guardar_formulario"]){

    
    if($_POST["id_registro"]){

        $sentencia = "";
        //MENSUAL
        if($_POST["frecuencia"] == 1){
            $sentencia = "
            UPDATE
                Frecuencia_Kpis
            SET
                enero = '".$_POST["enero"]."',
                febrero = '".$_POST["febrero"]."',
                marzo = '".$_POST["marzo"]."',
                abril = '".$_POST["abril"]."',
                mayo = '".$_POST["mayo"]."',
                junio = '".$_POST["junio"]."',
                julio = '".$_POST["julio"]."',
                agosto = '".$_POST["agosto"]."',
                septiembre = '".$_POST["septiembre"]."',
                octubre = '".$_POST["octubre"]."',
                noviembre = '".$_POST["noviembre"]."',
                diciembre = '".$_POST["diciembre"]."',
                updated_at = '".$hoy."'
            WHERE
                id_kpi = '".$_POST["id_registro"]."'
            ";
        }
        //BIMESTRAL
        if($_POST["frecuencia"] == 2){
            $sentencia = "
            UPDATE
                Frecuencia_Kpis
            SET
                enero = '".$_POST["enero"]."',
                febrero = '".$_POST["febrero"]."',
                marzo = '".$_POST["marzo"]."',
                abril = '".$_POST["abril"]."',
                mayo = '".$_POST["mayo"]."',
                junio = '".$_POST["junio"]."',
                julio = '".$_POST["julio"]."',
                agosto = '".$_POST["agosto"]."',
                septiembre = '".$_POST["septiembre"]."',
                octubre = '".$_POST["octubre"]."',
                noviembre = '".$_POST["noviembre"]."',
                diciembre = '".$_POST["diciembre"]."',
                updated_at = '".$hoy."'
            WHERE
                id_kpi = '".$_POST["id_registro"]."'
            ";
        }
        //TRIMESTRAL
        if($_POST["frecuencia"] == 3){
            $sentencia = "
            UPDATE
                Frecuencia_Kpis
            SET
                enero = '".$_POST["enero"]."',
                febrero = '".$_POST["enero"]."',
                marzo = '".$_POST["enero"]."',
                abril = '".$_POST["abril"]."',
                mayo = '".$_POST["abril"]."',
                junio = '".$_POST["abril"]."',
                julio = '".$_POST["julio"]."',
                agosto = '".$_POST["julio"]."',
                septiembre = '".$_POST["julio"]."',
                octubre = '".$_POST["octubre"]."',
                noviembre = '".$_POST["octubre"]."',
                diciembre = '".$_POST["octubre"]."',
                updated_at = '".$hoy."'
            WHERE
                id_kpi = '".$_POST["id_registro"]."'
            ";
        }
        //CUATRIMESTRAL
        if($_POST["frecuencia"] == 6){
            $sentencia = "
            UPDATE
                Frecuencia_Kpis
            SET
                enero = '".$_POST["noviembre"]."',
                febrero = '".$_POST["noviembre"]."',
                marzo = '".$_POST["marzo"]."',
                abril = '".$_POST["marzo"]."',
                mayo = '".$_POST["marzo"]."',
                junio = '".$_POST["marzo"]."',
                julio = '".$_POST["julio"]."',
                agosto = '".$_POST["julio"]."',
                septiembre = '".$_POST["julio"]."',
                octubre = '".$_POST["julio"]."',
                noviembre = '".$_POST["noviembre"]."',
                diciembre = '".$_POST["noviembre"]."',
                updated_at = '".$hoy."'
            WHERE
                id_kpi = '".$_POST["id_registro"]."'
            ";
        }
        //SEMESTRAL
        if($_POST["frecuencia"] == 4){ 

            $sentencia = "
            UPDATE
                Frecuencia_Kpis
            SET
                enero = '".$_POST["enero"]."',
                febrero = '".$_POST["enero"]."',
                marzo = '".$_POST["enero"]."',
                abril = '".$_POST["enero"]."',
                mayo = '".$_POST["enero"]."',
                junio = '".$_POST["enero"]."',
                julio = '".$_POST["julio"]."',
                agosto = '".$_POST["julio"]."',
                septiembre = '".$_POST["julio"]."',
                octubre = '".$_POST["julio"]."',
                noviembre = '".$_POST["julio"]."',
                diciembre = '".$_POST["julio"]."',
                updated_at = '".$hoy."'
            WHERE
                id_kpi = '".$_POST["id_registro"]."'
            ";
        }
        //ANUAL
        if($_POST["frecuencia"] == 5){

            $sentencia = "
            UPDATE
                Frecuencia_Kpis
            SET
                enero = '".$_POST["julio"]."',
                febrero = '".$_POST["julio"]."',
                marzo = '".$_POST["julio"]."',
                abril = '".$_POST["julio"]."',
                mayo = '".$_POST["julio"]."',
                junio = '".$_POST["julio"]."',
                julio = '".$_POST["julio"]."',
                agosto = '".$_POST["julio"]."',
                septiembre = '".$_POST["julio"]."',
                octubre = '".$_POST["julio"]."',
                noviembre = '".$_POST["julio"]."',
                diciembre = '".$_POST["julio"]."',
                updated_at = '".$hoy."'
            WHERE
                id_kpi = '".$_POST["id_registro"]."'
            ";
        }

        mysqli_query($connect_kpis, $sentencia);

        //PARA ACTUALIZAR EL KPI
        $sentencia_upd_kpi = "
        UPDATE Kpis SET 
            tipo_kpi = '".$_POST["tipo_kpi"]."',
            anio = '".$_POST["anio"]."',
            area_macro = '".$_POST["area_macro"]."',
            area_proceso = '".$_POST["area_proceso"]."',
            subproceso = '".$_POST["subproceso"]."',
            objetivo_sg = '".$_POST["objetivo_sg"]."',
            indicador = '".$_POST["indicador"]."',
            objetivo_indicador = '".$_POST["objetivo_indicador"]."',
            formula = '".$_POST["formula"]."',
            resultado_anterior = '".$_POST["resultado_anterior"]."',
            unidad_medida = '".$_POST["unidad_medida"]."',
            tipo_calculo = '".$_POST["tipo_calculo"]."',
            meta = '".$_POST["meta"]."',
            frecuencia = '".$_POST["frecuencia"]."',
            tipo_resultado = '".$_POST["tipo_resultado"]."',
            updated_at = '".$hoy."' 
        WHERE id = '".$_POST["id_registro"]."' 
        ";

        mysqli_query($connect_kpis, $sentencia_upd_kpi);
    }

    //BLOQUE PARA GUARDAR LA BITACORA
    //BLOQUE PARA GUARDAR LA BITACORA
    //BLOQUE PARA GUARDAR LA BITACORA
    $accion = 'ACTUALIZAR';
    $descripcion = 'Actualización de Kpis ' . $_POST["objetivo_indicador"];
    $id_kpi = $_POST["id_registro"];
    $tipo_kpi = $_POST["tipo_kpi"];
    GuardarAuditoriaKpis( $user_log["id_empresa"], $user_log["id"], $accion, $descripcion, $id_kpi, $tipo_kpi ); 
    //BLOQUE PARA GUARDAR LA BITACORA
    //BLOQUE PARA GUARDAR LA BITACORA
    //BLOQUE PARA GUARDAR LA BITACORA
}


include("app/models/kpis/Kpis.php");
$ClassKpis = new Kpis();
include("app/models/kpis/KpisCrud.php");
$ClassKpisCrud = new KpisCrud();

/* AREAS MACRO / VICEPRESIDENCIAS */
$vicepresidencias = $ClassKpis->Vicepresidencias($user_log["id_empresa"]);
$areas = $ClassKpis->Areas($user_log["id_empresa"]);
$unidades_organizativas = $ClassKpis->Unidades_Organizativas($user_log["id_empresa"]);

/* OBJETIVOS SG */
$Kpis_Objetivos_SG = $ClassKpis->Kpis_Objetivos_SG($user_log["id_empresa"]);

$data = $ClassKpisCrud->obtener_data_kpis($id);
$kpis = $data["array_frecuencias"];
//dd($data);
?>

<div class="container-fluid" style="max-width: 90%; margin: 0 auto;">

    <!-- TITULO -->
    <div class="card mb-3">
        <div class="card-header">
            <h3>Detalle KPI</h3>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-body">

                    <form action="" method="post" id="formulario_general">
                    <div class="row">
                       
                        <input type="hidden" name="guardar_formulario" value="true">
                        <input type="hidden" name="id_registro" value="<?= $data["id"]; ?>">
                        <input type="hidden" name="id_area_kpi" id="id_area_kpi" value="<?= $user_log["id_area"]; ?>">

                        <div class="col-md-2">
                                        <label for="tipo_kpi">Tipo de KPI *</label>
                                        <select name="tipo_kpi" id="tipo_kpi" class="form-control form-control-sm" required>
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
                                        <select class="form-control form-control-sm" name="anio" id="anio" required>
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
                                        <label for="vicepresidencia">Área Macro *</label>
                                        <select class="select_2_search form-control" name="area_macro" id="area_macro" required onchange="ListaFiltroProceso(this.value)" >
                                            <option value="">Seleccionar..</option>
                                            <?php foreach ($vicepresidencias as $vicepresidencia): ?>
                                                <option value="<?= $vicepresidencia["id"]; ?>" <?= isset($data["area_macro"]) && $vicepresidencia["id"] == $data["area_macro"] ? 'selected' : ''; ?>><?= $vicepresidencia["nombre"]; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                        </div>
                        <div class="col-md-3">
                                        <label for="area">Área Proceso *</label>
                                        <select class="select_2_search form-control" name="area_proceso" id="area_proceso" onchange="ListaFiltroSubProceso()">
                                            <option value="">Seleccionar..</option>
                                            <?php foreach($areas as $area): ?>
                                                <?php if($area["id_vicepresidencia"] == $data["area_macro"] ){ ?>

                                                <option value="<?= $area["id"]; ?>" <?= isset($data["area_proceso"]) && $area["id"] == $data["area_proceso"] ? 'selected' : ''; ?>><?= $area["nombre"]; ?></option>

                                                <?php } ?>

                                            <?php endforeach; ?>
                                        </select>
                        </div>
                        <div class="col-md-3">
                                        <label for="unidad_organizativa">Subproceso</label>
                                        <select class="select_2_search form-control" name="subproceso" id="subproceso" onchange="ListaFiltroObjetivosSG()" >
                                            <option value="">Seleccionar..</option>
                                            <?php foreach($unidades_organizativas as $unidad): ?> 

                                                

                                                <option value="<?= $unidad["id"]; ?>" <?= isset($data["subproceso"]) && $unidad["id"] == $data["subproceso"] ? 'selected' : ''; ?>><?= $unidad["unidad_organizativa"]; ?></option>

                                              

                                            <?php endforeach; ?>        
                                        </select>
                                    
                        </div>

                        <div class="col-md-12">
                                        <label for="objetivo_sg">Objetivo SG</label>
                                        <select class="select_2_search form-control" id="objetivo_sg" name="objetivo_sg">
                                            <option value="">Seleccionar..</option>
                                            <?php foreach($Kpis_Objetivos_SG as $objetivo): ?>
                                                <option value="<?= $objetivo["id"]; ?>" <?= isset($data["objetivo_sg"]) && $objetivo["id"] == $data["objetivo_sg"] ? 'selected' : ''; ?>><?= $objetivo["objetivo"]; ?></option>
                                            <?php endforeach; ?>    
                                        </select>
                        </div>

                        <div class="col-md-12">
                            <label for="indicador">Indicador *</label>
                            <textarea name="indicador" id="indicador" rows="2" class="form-control" required><?= isset($data["indicador"]) ? $data["indicador"] : ''; ?></textarea>
                        </div>


                        <div class="col-md-12">
                                        <label for="objetivo_indicador">Objetivo del Indicador *</label>
                                        <textarea name="objetivo_indicador" id="objetivo_indicador" rows="2" class="form-control" required><?= isset($data["objetivo_indicador"]) ? $data["objetivo_indicador"] : ''; ?></textarea>
                        </div>
                                
                        <div class="col-md-12">
                                        <label for="formula">Fórmula de Cálculo *</label>
                                        <textarea name="formula" id="formula" rows="2" class="form-control" required><?= isset($data["formula"]) ? $data["formula"] : ''; ?></textarea>
                        </div>
                               
                        <div class="col-md-2">
                                        <label for="resultado_anterior">Resultado Año Anterior</label>
                                        <input type="text" class="form-control numeros" name="resultado_anterior" id="resultado_anterior" value="<?= isset($data["resultado_anterior"]) ? $data["resultado_anterior"] : ''; ?>">
                        </div>
                        <div class="col-md-2">
                                        <label for="unidad_medida">Unidad de Medida *</label>
                                        <select class="form-control" name="unidad_medida" id="unidad_medida" required>
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
                                        <label for="tipo_resultado">Tipo de Resultado * &nbsp;<span id="spanResultado" class="circleSpan" data-bs-toggle="tooltip" style="color:#365189 !important;font-size: 11px !important;" title="Ver información de los tipos de resultado"><i class="fa fa-question"></i></span></label>
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
                                        <label for="meta">Meta *</label>
                                        <div class="row" id="metaNormal" style="display:block;">
                                            <div class="col-md-12">
                                                <input type="text" class="form-control" name="meta" id="meta" value="<?php echo $data["meta"]; ?>" onkeyup="return NumerosDecimales(this)" readonly >
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
                                        <label for="tipo_calculo">Tipo de Cálculo * &nbsp;<span id="spanCalculo" data-bs-toggle="tooltip" class="circleSpan" style="color:#365189 !important;font-size: 11px !important;" title="Ver información de los tipos de cálculo"><i class="fa fa-question"></i></span></label>
                                        <select class="form-control" name="tipo_calculo" id="tipo_calculo" required onchange="select_meta(this);">
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
                                        <label for="frecuencia">Frecuencia * *</label>
                                        <select class="form-control" name="frecuencia" id="frecuencia" required onchange="CargarTabla(this.value);">
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

                               

                        <div class="col-md-12" id="alerta"></div>

                        <div class="col-md-12" id="table_meses">
                                <?php 
                                    if( $data["frecuencia"] == 1 ){ include("views/kpis/detalle/tabla_mensual.php");  }
                                    if( $data["frecuencia"] == 2 ){ include("views/kpis/detalle/tabla_bimestral.php");  }
                                    if( $data["frecuencia"] == 3 ){ include("views/kpis/detalle/tabla_trimestral.php");  }
                                    if( $data["frecuencia"] == 6 ){ include("views/kpis/detalle/tabla_cuatrimestral.php");  }
                                    if( $data["frecuencia"] == 4 ){ include("views/kpis/detalle/tabla_semestral.php");  }
                                    if( $data["frecuencia"] == 5 ){ include("views/kpis/detalle/tabla_anual.php");  }
                                ?>
                        </div>

                            

                        <div class="col-md-12">
                                <div class="dinamico" id="lista_mes_padre" style="display: none">
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
                                                        <input type="text" class="form-control numeros mes_per campoMensual" name="mes_' . $indice . '" value="' . $meta_mes . '" id="mensual_' . $indice . '"  onkeypress="return NumerosDecimalesPuntos(this)">

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
                        </div>

                        <div class="col-md-6" style="margin-top: 15px ">
                                <button type="submit" class="btn btn-success w-100">
                                    Guardar
                                </button>
                        </div>

                        <div class="col-md-6" style="margin-top: 15px ">
                                <a href="?pg=kpis/detalle/integrantes&id=<?php echo $id; ?>">
                                <button type="button" class="btn btn-warning w-100">
                                    Siguiente >>
                                </button>
                                </a>
                        </div>

                        
                    </div>
                    </form>
                </div>
            </div>

            <?php 
            //SOLO
            if ( $VALIDAR_ROOT["editar"] || $user_log["permiso_administrador_kpis"] ) {  
            ?>

            <button type="button" class="btn btn-danger btn-sm" onclick="EliminarKPI()">
                Eliminar KPI
            </button>
            <?php } ?>
        </div>
    </div>

</div>


<?php if(!$id){ ?>
            
    <div id="cont_tabla_mensual" style="display: none">
        <?php include("views/kpis/detalle/tabla_mensual.php");  ?>
    </div>

    <div id="cont_tabla_bimestral" style="display: none">
        <?php include("views/kpis/detalle/tabla_bimestral.php");  ?>
    </div>

    <div id="cont_tabla_trimestral" style="display: none">
        <?php include("views/kpis/detalle/tabla_trimestral.php");  ?>
    </div>

    <div id="cont_tabla_cuatrimestral" style="display: none">
        <?php include("views/kpis/detalle/tabla_cuatrimestral.php");  ?>
    </div>

    <div id="cont_tabla_semestral" style="display: none">
        <?php include("views/kpis/detalle/tabla_semestral.php");  ?>
    </div>

    <div id="cont_tabla_anual" style="display: none">
        <?php include("views/kpis/detalle/tabla_anual.php");  ?>
    </div>

<?php } ?>


<script>

    $(document).ready(function() {
        //ValidarMeta()
    });

    function CargarTabla(tipo){

        if( tipo == 1 ){
            html = $("#cont_tabla_mensual").html();
            $("#table_meses").html(html);
        }
        if( tipo == 2 ){
            html = $("#cont_tabla_bimestral").html();
            $("#table_meses").html(html);
        }
        if( tipo == 3 ){
            html = $("#cont_tabla_trimestral").html();
            $("#table_meses").html(html);
        }
        if( tipo == 6 ){
            html = $("#cont_tabla_cuatrimestral").html();
            $("#table_meses").html(html);
        }
        if( tipo == 4 ){
            html = $("#cont_tabla_semestral").html();
            $("#table_meses").html(html);
        }
        if( tipo == 5 ){
            html = $("#cont_tabla_anual").html();
            $("#table_meses").html(html);
        }
    }

    /*
    function ValidarMeta(){
        frecuencia = $("#frecuencia").val();
        unidad_medida = $("#unidad_medida").val();
        tipo_resultado = $("#tipo_resultado").val();
        tipo_calculo = $("#tipo_calculo").val();

        if(frecuencia && tipo_resultado){

            ultimo_valor = 0;
            total_seguimientos = 0;
            cantidad_seguimiento = 0;
            valor_meta = 0;
            //PARA LOS NO ACUMULATIVOS
            $('#table_meses .mes_valor').each(function(index) {
                valor = parseFloat( $(this).val() );
                
                if(!Number.isNaN(valor)){
                    if(valor){
                        total_seguimientos += valor;
                        ultimo_valor = valor;
                    }
                
                    cantidad_seguimiento++;
                }

            });

            if(total_seguimientos != 0){
                valor_meta = total_seguimientos/cantidad_seguimiento
            }

            //SOLO APLICA PARA LOS ACUMULATIVOS
            if(tipo_resultado == 3){
                valor_meta = ultimo_valor;
            }

            valor_meta = valor_meta.toFixed(2);

            
            $("#alerta").html("");
            $("#meta").val(valor_meta);

            //console.log(valor_meta);
        }
        else{
            $("#alerta").html("Recuerda que debes seleccionar una frecuencia y un tipo de resultado para poder continuar");
            return false;
        }
                                                

    }
    */

    function ValidarMeta(){
        frecuencia = $("#frecuencia").val();
        unidad_medida = $("#unidad_medida").val();
        tipo_resultado = $("#tipo_resultado").val();
        tipo_calculo = $("#tipo_calculo").val();

        if(frecuencia && tipo_resultado){

            ultimo_valor = 0;
            total_seguimientos = 0;
            cantidad_seguimiento = 0;
            valor_meta = 0;

            //SOLO PARA CUANDO SON HORAS
            horas_valor = 0;
            minutos_valor = 0;
            segundos_valor = 0;

            /////NUEVO CODIGO
            totalSegundos = 0;
            totalSeguimientos = 0;
            /////NUEVO CODIGO

            //PARA LOS NO ACUMULATIVOS
            $('#table_meses .mes_valor').each(function(index) {

                if(unidad_medida == 4){
                    hora = $(this).val();

                    /////NUEVO CODIGO
                    let partes = hora.split(":");

                    let horas = parseInt(partes[0]) || 0;
                    let minutos = parseInt(partes[1]) || 0;
                    let segundos = parseInt(partes[2]) || 0;

                    let segundosTotales = (horas * 3600) + (minutos * 60) + segundos;
                    totalSegundos += segundosTotales;
                    if($(this).val()){
                        cantidad_seguimiento++;
                    }
                    /////NUEVO CODIGO

                    /*
                    hora = hora.split(":");

                    if(hora[0]){
                        horas_valor += parseInt(hora[0]);
                    }
                    if(hora[1]){
                        minutos_valor += parseInt(hora[1]);
                    }
                    if(hora[2]){
                        segundos_valor += parseInt(hora[2]);
                    }

                    if($(this).val()){
                        total_seguimientos += horas_valor+minutos_valor+segundos_valor;
                        ultimo_valor = $(this).val(); 

                        cantidad_seguimiento++;
                    }
                    */
                    
                }
                else{
                    valor = parseFloat( $(this).val() );
                    if(valor){
                        total_seguimientos += valor;
                        ultimo_valor = valor; 
                        cantidad_seguimiento++;
                    }
                    
                    
                }
            });

            console.log(ultimo_valor);

            //SOLO HORAS
            part_1 = 0;
            part_2 = 0;
            part_3 = 0;

            console.log(total_seguimientos);

            if(cantidad_seguimiento != 0){
                if(unidad_medida == 4){

                    /////NUEVO CODIGO
                    let promedioSegundos = Math.floor(totalSegundos / cantidad_seguimiento); 
                    // Convertir nuevamente a HH:MM:SS
                    let horasPromedio = Math.floor(promedioSegundos / 3600);
                    let minutosPromedio = Math.floor((promedioSegundos % 3600) / 60);
                    let segundosPromedio = promedioSegundos % 60; 

                    // Formatear
                    let resultado = 
                        String(horasPromedio).padStart(2, '0') + ":" +
                        String(minutosPromedio).padStart(2, '0') + ":" +
                        String(segundosPromedio).padStart(2, '0');

                    console.log(resultado);

                    ////NUEVO CODIGO

                    /*
                    part_1 = Math.round(horas_valor/cantidad_seguimiento);
                    part_2 = Math.round(minutos_valor/cantidad_seguimiento);
                    part_3 = Math.round(segundos_valor/cantidad_seguimiento);

                    if(part_1 <= 9){ part_1 = '0'+part_1; }
                    if(part_2 <= 9){ part_2 = '0'+part_2; }
                    if(part_3 <= 9){ part_3 = '0'+part_3; }

                    valor_meta = part_1+":"+part_2+":"+part_3;
                    //console.log(valor_meta); 
                    */

                    valor_meta = resultado;

                }
                else{
                    valor_meta = total_seguimientos/cantidad_seguimiento;
                }
                
            }

            //SOLO APLICA PARA LOS ACUMULATIVOS
            if(tipo_resultado == 3){
                valor_meta = ultimo_valor;
            }

            
            if(unidad_medida != 4){
                valor_meta = valor_meta.toFixed(2);
            }

            console.log(valor_meta);

            
            $("#alerta").html("");
            $("#meta").val(valor_meta);

            //console.log(total_seguimientos);
        }
        else{
            $("#alerta").html("Recuerda que debes seleccionar una frecuencia y un tipo de resultado para poder continuar");
            return false;
        }
                                                

    }

    function NumerosDecimales(value){
        ValidarMeta();
    }

    //FUNCION PARA VALIDAR LAS UNIDADES DE MEDIDA VERSUS LOS FORMATOS
    //FUNCION PARA VALIDAR LAS UNIDADES DE MEDIDA VERSUS LOS FORMATOS
    //FUNCION PARA VALIDAR LAS UNIDADES DE MEDIDA VERSUS LOS FORMATOS
    function ValidarUnidadMedida(element){
        let unidad_medida = $("#unidad_medida").val();
        let valor = $(element).val();
        let formato;

        if( unidad_medida == 1 || unidad_medida == 2 || unidad_medida == 3 ){
            valor = valor.replace(/[^0-9.]/g, '');

            // Evitar múltiples puntos decimales
            let partes = valor.split('.');
            if (partes.length > 2) {
                valor = partes[0] + '.' + partes[1];
            }
            formato = valor;
        }

        if(unidad_medida == 4){
            // Solo números
            valor = valor.replace(/\D/g, ''); 

            // Formatear a hh:mm:ss
            if (valor.length >= 2) {
                valor = valor.substring(0,2) + ':' + valor.substring(2);
            }
            if (valor.length >= 5) {
                valor = valor.substring(0,5) + ':' + valor.substring(5,7);
            }

            // Limitar a 8 caracteres (hh:mm:ss)
            valor = valor.substring(0, 8);

            formato = valor;
        }

        $(element).val(formato);

        //return formato;
    }

    var api = '<?php echo $url; ?>api/kpis/';

    
    var permitir = false;
    function EliminarKPI(){

        if(permitir == false){
            $("#modal_general").modal("show");
            $("#modal_body").html("Estas a punto de eliminar un KPI, esto eliminará todos los datos relacionados con el mismo incluyendo: seguimientos, integrantes, comentarios, documentos, etc. esta acción  es irreversible. ¿Está seguro? <br><br> ");
            $("#modal_body").append('<button type="button" class="btn btn-danger btn-sm" onclick="permitir = true;EliminarKPI()">Eliminar KPI</button> <br> Nota: este esta acción será registrada en la auditoría con su nombre.');
            
        }
        else{

            data = {
                id_empresa: <?php echo $user_log["id_empresa"]; ?>, 
                id_user: <?php echo $user_log["id"]; ?>,
                id_kpi: <?php echo $id; ?>, 
                url: '?pg=kpis/mis_kpis'
            };
            jQuery.ajax({
                url: api + "eliminar_kpi.php",
                type: 'post',
                data: data,
                })
                .done(function(resp) {
                    $("#xscript").html(resp);
                })
                .fail(function(resp) {
                    console.log(resp);
                })
                .always(function(resp) {}
            );

        }

    }


</script>






















<script>
    $(document).ready(function() {

        const AREAS = <?= json_encode($areas); ?>;
        const UNIDADES_ORGANIZATIVAS = <?= json_encode($unidades_organizativas); ?>;
        const OBJETIVOS_SG = <?= json_encode($Kpis_Objetivos_SG); ?>;

        /* Cargar AREAS */
        $('#id_vicepresidencia').on('change', function() {

            const idVicepresidencia = $(this).val();
            const $selectArea = $('#id_area');
            const $selectUnidad = $('#id_unidad_organizativa');

            $selectArea.empty().append('<option value="">Seleccionar..</option>');
            $selectUnidad.empty().append('<option value="">Seleccionar..</option>');

            if (!idVicepresidencia) return;

            const areasFiltradas = AREAS.filter(area =>
                area.id_vicepresidencia == idVicepresidencia
            );

            areasFiltradas.forEach(area => {
                $selectArea.append(
                    `<option value="${area.id}">${area.nombre}</option>`
                );
            });
        });

        /* Cargar UNIDADES ORGANIZATIVAS */
        $('#id_area').on('change', function() {

            const idArea = $(this).val();
            const idVicepresidencia = $('#id_vicepresidencia').val();

            const $selectUnidad = $('#id_unidad_organizativa');
            const $selectObjetivo = $('#objetivo_sg');

            /* ===============================
               RESET SELECTS DEPENDIENTES
            ================================*/
            $selectUnidad.empty().append('<option value="">Seleccionar..</option>');
            $selectObjetivo.empty().append('<option value="">Seleccionar..</option>');

            if (!idArea || !idVicepresidencia) return;

            /* ===============================
               UNIDADES ORGANIZATIVAS
            ================================*/
            const unidadesFiltradas = UNIDADES_ORGANIZATIVAS.filter(unidad =>
                unidad.vicepresidencia == idVicepresidencia &&
                unidad.area == idArea
            );

            if (unidadesFiltradas.length === 0) {
                $selectUnidad.append(
                    '<option value="" disabled>No hay subprocesos asociados</option>'
                );
            } else {
                unidadesFiltradas.forEach(unidad => {
                    if (unidad.id && unidad.unidad_organizativa) {
                        $selectUnidad.append(
                            `<option value="${unidad.id}">${unidad.unidad_organizativa}</option>`
                        );
                    } else {
                        $selectUnidad.append(
                            '<option value="" disabled>No hay subprocesos asociados</option>'
                        );
                    }
                });
            }

            /* ===============================
               OBJETIVOS SG
            ================================*/
            const objetivosFiltrados = OBJETIVOS_SG.filter(obj =>
                obj.id_vp == idVicepresidencia &&
                obj.id_area == idArea
            );

            if (objetivosFiltrados.length === 0) {
                $selectObjetivo.append(
                    '<option value="" disabled>No hay objetivos SG asociados</option>'
                );
                return;
            }

            objetivosFiltrados.forEach(obj => {
                if (obj.id && obj.objetivo) {
                    $selectObjetivo.append(
                        `<option value="${obj.id}">${obj.objetivo}</option>`
                    );
                }
            });

        });

    });



</script>

<script>
    function Select_Frecuencia(frecuencia) {

        $(".dinamico").hide();

        if (frecuencia == 1) {
            $("#lista_mes_padre").show();
        }
    }

    /*
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
    */
</script>

<script>

    var api = '<?php echo $url; ?>api/kpis/';

    function ResetFiltrosKpis(){
        $("#formulario_filtros_kpis").submit();
    }

    function ListaFiltroProceso(){

        data = {
            id_empresa: <?php echo $user_log["id_empresa"]; ?>,
            id_vicepresidencia: $("#area_macro").val(),
        };
        jQuery.ajax({
            url: api + "filtro_lista_proceso.php",
            type: 'post',
            data: data,
            })
            .done(function(resp) {
                $("#area_proceso").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {}
        );

        ListaFiltroObjetivosSG();
    }

    function ListaFiltroSubProceso(){

        data = {
            id_empresa: <?php echo $user_log["id_empresa"]; ?>,
            id_vicepresidencia: $("#area_macro").val(), 
            id_area: $("#area_proceso").val()  
        };
        jQuery.ajax({
            url: api + "filtro_lista_subproceso.php",
            type: 'post',
            data: data,
            })
            .done(function(resp) {
                $("#subproceso").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {}
        );
        ListaFiltroObjetivosSG();
    }

    function ListaFiltroObjetivosSG(){

        data = {
            id_empresa: <?php echo $user_log["id_empresa"]; ?>,
            id_vicepresidencia: $("#area_macro").val(), 
            id_area: $("#area_proceso").val(), 
            id_subproceso: $("#subproceso").val()  
        };
        jQuery.ajax({
            url: api + "filtro_lista_objetivos_sg.php",
            type: 'post',
            data: data,
            })
            .done(function(resp) {
                $("#objetivo_sg").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {}
        );
    }



    $(document).ready(function() {
        $('.select_2_search').select2();
    });

</script>

