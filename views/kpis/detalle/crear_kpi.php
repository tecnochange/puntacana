<script>
    $(document).ready(function() {
        $('#menuKpis').collapse();
        $('#bt_crear').addClass('active');
    });
</script>

<?php
    $id = $_GET["id"];
    $hoy = date("Y-m-d H:i:s");

    if($_POST["guardar_formulario"]){
        //1. CREACION DEL KPIS
        $sentencia = "
            INSERT INTO Kpis(
                id_empresa,
                id_empleado,
                tipo_kpi, 
                ponderado, 
                anio,
                area_macro,
                area_proceso,
                subproceso,
                objetivo_sg,
                indicador,
                objetivo_indicador,
                formula,
                resultado_anterior,
                unidad_medida,
                tipo_calculo,
                meta,
                frecuencia,
                tipo_resultado,
                obj_meses, 
                created_at,
                updated_at
            )
            VALUES(
                '".$user_log["id_empresa"]."',
                '".$user_log["id"]."',
                '".$_POST["tipo_kpi"]."', 
                '".$_POST["ponderado"]."',
                '".$_POST["anio"]."',
                '".$_POST["area_macro"]."',
                '".$_POST["area_proceso"]."',
                '".$_POST["subproceso"]."',
                '".$_POST["objetivo_sg"]."',
                '".$_POST["indicador"]."',
                '".$_POST["objetivo_indicador"]."',
                '".$_POST["formula"]."',
                '".$_POST["resultado_anterior"]."',
                '".$_POST["unidad_medida"]."',
                '".$_POST["tipo_calculo"]."',
                '".$_POST["meta"]."',
                '".$_POST["frecuencia"]."',
                '".$_POST["tipo_resultado"]."', 
                0,
                '".$hoy."',
                '".$hoy."' 
            )
        ";

        mysqli_query($connect_kpis, $sentencia);
        $id_tmp = mysqli_insert_id($connect_kpis);

        //2. CREACION DE LA FRECUENCIA
        if($_POST["frecuencia"] == 1){
                $enero = $_POST["enero"];
                $febrero = $_POST["febrero"];
                $marzo = $_POST["marzo"];
                $abril = $_POST["abril"];
                $mayo = $_POST["mayo"];
                $junio = $_POST["junio"];
                $julio = $_POST["julio"];
                $agosto = $_POST["agosto"];
                $septiembre = $_POST["septiembre"];
                $octubre = $_POST["octubre"];
                $noviembre = $_POST["noviembre"];
                $diciembre = $_POST["diciembre"];

                $enero_min = $_POST["enero_min"]; 
                $febrero_min = $_POST["febrero_min"]; 
                $marzo_min = $_POST["marzo_min"]; 
                $abril_min = $_POST["abril_min"]; 
                $mayo_min = $_POST["mayo_min"]; 
                $junio_min = $_POST["junio_min"];
                $julio_min = $_POST["julio_min"]; 
                $agosto_min = $_POST["agosto_min"]; 
                $septiembre_min = $_POST["septiembre_min"]; 
                $octubre_min = $_POST["octubre_min"]; 
                $noviembre_min = $_POST["noviembre_min"]; 
                $diciembre_min = $_POST["diciembre_min"];
        }

        if($_POST["frecuencia"] == 2){
                $enero = $_POST["enero"]; $febrero = $_POST["enero"];
                $marzo = $_POST["marzo"]; $abril = $_POST["marzo"];
                $mayo = $_POST["mayo"]; $junio = $_POST["mayo"];
                $julio = $_POST["julio"]; $agosto = $_POST["julio"];
                $septiembre = $_POST["septiembre"]; $octubre = $_POST["septiembre"];
                $noviembre = $_POST["noviembre"]; $diciembre = $_POST["noviembre"];

                $enero_min = $_POST["enero_min"]; $febrero_min = $_POST["enero_min"]; 
                $marzo_min = $_POST["marzo_min"]; $abril_min = $_POST["marzo_min"]; 
                $mayo_min = $_POST["mayo_min"]; $junio_min = $_POST["mayo_min"];
                $julio_min = $_POST["julio_min"]; $agosto_min = $_POST["julio_min"]; 
                $septiembre_min = $_POST["septiembre_min"]; $octubre_min = $_POST["octubre_min"]; 
                $noviembre_min = $_POST["noviembre_min"]; $diciembre_min = $_POST["noviembre_min"];
        }
        if($_POST["frecuencia"] == 3){
                $enero = $_POST["enero"]; $febrero = $_POST["enero"]; $marzo = $_POST["enero"];
                $abril = $_POST["abril"]; $mayo = $_POST["abril"]; $junio = $_POST["abril"];
                $julio = $_POST["julio"]; $agosto = $_POST["julio"]; $septiembre = $_POST["julio"];
                $octubre = $_POST["octubre"]; $noviembre = $_POST["octubre"]; $diciembre = $_POST["octubre"]; 

                $enero_min = $_POST["enero_min"]; $febrero_min = $_POST["enero_min"]; $marzo_min = $_POST["enero_min"];
                $abril_min = $_POST["abril_min"]; $mayo_min = $_POST["mayo_min"]; $junio_min = $_POST["junio_min"];
                $julio_min = $_POST["julio_min"]; $agosto_min = $_POST["julio_min"]; $septiembre_min = $_POST["julio_min"];
                $octubre_min = $_POST["octubre_min"]; $noviembre_min = $_POST["octubre_min"]; $diciembre_min = $_POST["octubre_min"];  
        }
        if($_POST["frecuencia"] == 6){
                $marzo = $_POST["marzo"]; $abril = $_POST["marzo"]; $mayo = $_POST["marzo"]; $junio = $_POST["marzo"];
                $julio = $_POST["julio"]; $agosto = $_POST["julio"]; $septiembre = $_POST["julio"]; $octubre = $_POST["julio"];
                $noviembre = $_POST["noviembre"]; $diciembre = $_POST["noviembre"]; $enero = $_POST["noviembre"]; $febrero = $_POST["noviembre"];

                $marzo_min = $_POST["marzo_min"]; $abril_min = $_POST["marzo_min"]; $mayo_min = $_POST["marzo_min"]; $junio_min = $_POST["marzo_min"];
                $julio_min = $_POST["julio_min"]; $agosto_min = $_POST["julio_min"]; $septiembre_min = $_POST["julio_min"]; $octubre_min = $_POST["julio_min"]; 
                $noviembre_min = $_POST["noviembre_min"]; $diciembre_min = $_POST["noviembre_min"]; $enero_min = $_POST["noviembre_min"]; $febrero_min = $_POST["noviembre_min"]; 
                
        }
        if($_POST["frecuencia"] == 4){
                $julio = $_POST["julio"]; $agosto = $_POST["julio"]; $septiembre = $_POST["julio"]; $octubre = $_POST["julio"]; $noviembre = $_POST["julio"]; $diciembre = $_POST["julio"];
                $enero = $_POST["enero"]; $febrero = $_POST["enero"]; $marzo = $_POST["enero"]; $abril = $_POST["enero"]; $mayo = $_POST["enero"]; $junio = $_POST["enero"];

                $julio_min = $_POST["julio_min"]; $agosto_min = $_POST["julio_min"]; $septiembre_min = $_POST["julio_min"]; $octubre_min = $_POST["julio_min"]; $noviembre_min = $_POST["julio_min"]; $diciembre_min = $_POST["julio_min"];
                $enero_min = $_POST["enero_min"]; $febrero_min = $_POST["enero_min"]; $marzo_min = $_POST["enero_min"]; $abril_min = $_POST["enero_min"]; $mayo_min = $_POST["enero_min"]; $junio_min = $_POST["enero_min"];
        }
        if($_POST["frecuencia"] == 5){
            $julio = $_POST["julio"]; $agosto = $_POST["julio"]; $septiembre = $_POST["julio"]; $octubre = $_POST["julio"]; $noviembre = $_POST["julio"]; $diciembre = $_POST["julio"];
            $enero = $_POST["julio"]; $febrero = $_POST["julio"]; $marzo = $_POST["julio"]; $abril = $_POST["julio"]; $mayo = $_POST["julio"]; $junio = $_POST["julio"];

            $julio_min = $_POST["julio_min"]; $agosto_min = $_POST["julio_min"]; $septiembre_min = $_POST["julio_min"]; $octubre_min = $_POST["julio_min"]; $noviembre_min = $_POST["julio_min"]; $diciembre_min = $_POST["julio_min"];
            $enero_min = $_POST["julio_min"]; $febrero_min = $_POST["julio_min"]; $marzo_min = $_POST["julio_min"]; $abril_min = $_POST["julio_min"]; $mayo_min = $_POST["julio_min"]; $junio_min = $_POST["julio_min"];

            
        }

        $sentencia_frecuencia = "
            INSERT INTO Frecuencia_Kpis(
                id_kpi,
                id_empresa,
                tipo,
                enero,
                febrero,
                marzo,
                abril,
                mayo,
                junio,
                julio,
                agosto,
                septiembre,
                octubre,
                noviembre,
                diciembre, 

                enero_min,
                febrero_min,
                marzo_min,
                abril_min,
                mayo_min,
                junio_min,
                julio_min,
                agosto_min,
                septiembre_min,
                octubre_min,
                noviembre_min,
                diciembre_min,

                created_at,
                updated_at
            )
            VALUES(
                '".$id_tmp."',
                '".$user_log["id_empresa"]."',
                '".$_POST["frecuencia"]."',
                '".$enero."', 
                '".$febrero."',
                '".$marzo."',
                '".$abril."',
                '".$mayo."',
                '".$junio."',
                '".$julio."',
                '".$agosto."',
                '".$septiembre."',
                '".$octubre."',
                '".$noviembre."',
                '".$diciembre."', 

                '".$enero_min."',
                '".$febrero_min."',
                '".$marzo_min."',
                '".$abril_min."',
                '".$mayo_min."',
                '".$junio_min."',
                '".$julio_min."',
                '".$agosto_min."',
                '".$septiembre_min."',
                '".$octubre_min."',
                '".$noviembre_min."',
                '".$diciembre_min."',

                '".$hoy."',
                '".$hoy."'
            )
        ";

        mysqli_query($connect_kpis, $sentencia_frecuencia);

        $id_tmp_frecuencia = mysqli_insert_id($connect_kpis);

        //3. ACTUALIZAMOS EN 
        mysqli_query($connect_kpis, "UPDATE Kpis SET obj_meses = '".$id_tmp_frecuencia."' WHERE id = '".$id_tmp."' " );
        //de debe actualizar el obj_meses

            
        //4. CREACION DE REGISTRO DEL COLABORADOR
        $sentencia_kpis_colaborador = "
            INSERT INTO Kpis_Colaborador(
                id_empresa,
                id_empleado,
                id_kpi,
                anio,
                area_macro,
                area_proceso,
                id_colaborador,
                tipo,
                created_at,
                updated_at
            )
            VALUES(
                '".$user_log["id_empresa"]."',
                '".$user_log["id"]."',
                '".$id_tmp."', 
                '".$_POST["anio"]."',
                '".$_POST["area_macro"]."',
                '".$_POST["area_proceso"]."',
                '".$user_log["id"]."',
                1,
                '".$hoy."',
                '".$hoy."'
            )
        ";

        //echo $sentencia_kpis_colaborador;

        //mysqli_query($connect_kpis, $sentencia_kpis_colaborador); 
        //LOS KPIS CREADOS NO DEBEN SER RELACIONADOS A LA PERSONA QUE LOS CREA

        //BLOQUE PARA GUARDAR LA BITACORA
        //BLOQUE PARA GUARDAR LA BITACORA
        //BLOQUE PARA GUARDAR LA BITACORA
        $accion = 'CREACIÓN';
        $descripcion = 'Creación de Kpis ' . $_POST["objetivo_indicador"];
        $id_kpi = $id_tmp;
        $tipo_kpi = $_POST["tipo_kpi"];
        GuardarAuditoriaKpis( $user_log["id_empresa"], $user_log["id"], $accion, $descripcion, $id_kpi, $tipo_kpi ); 
        //BLOQUE PARA GUARDAR LA BITACORA
        //BLOQUE PARA GUARDAR LA BITACORA
        //BLOQUE PARA GUARDAR LA BITACORA
            
        echo '<script> window.location.href = "?pg=kpis/detalle/detalle_kpi&id='.$id_tmp.'";</script>';
        
    }

    include("app/models/kpis/Kpis.php");
    $ClassKpis = new Kpis();
    include("app/models/kpis/KpisCrud.php");
    $ClassKpisCrud = new KpisCrud();

    /* AREAS MACRO / VICEPRESIDENCIAS */
    $vicepresidencias = $ClassKpis->Vicepresidencias($user_log["id_empresa"]);
    $areas = $ClassKpis->Areas($user_log["id_empresa"]);
    $unidades_organizativas = $ClassKpis->Unidades_Organizativas($user_log["id_empresa"]);
    $Kpis_Objetivos_SG = $ClassKpis->Kpis_Objetivos_SG($user_log["id_empresa"]);

    //KPI DATA
    if (isset($_GET["id_kpi"]) && !empty($_GET["id_kpi"])) {
        $data = $ClassKpis->Obtener_kpi($id);
    }

?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<style>
    .select2-selection--single .select2-selection__rendered{
        color: #006dbd !important;
    }
.cursor-pointer {
    cursor: pointer;
}
#alerta{
    color: #f90202;
    font-weight: bold;
    margin-top: 10px;
}
.advertencia{
    color: #F44336;
    font-weight: bold;
}
</style>

<div class="container-fluid" style="max-width: 90%; margin: 0 auto;">

    <!-- TITULO -->
    <div class="card mb-3">
        <div class="card-header">
            <h3>Crear KPI</h3>
        </div>
    </div>

    <!-- FICHA -->
    <div class="card mb-3">
        <div class="card-body">

            <div class="row">
                <form action="" method="post" id="formulario_general">
                    
                    <input type="hidden" name="guardar_formulario" value="true">
                    <input type="hidden" name="id_registro" value="<?= $data["id"]; ?>">
                            
                    <input type="hidden" name="id_empresa" id="id_empresa" value="<?= $user_log["id_empresa"]; ?>">
                    <input type="hidden" name="id_area_kpi" id="id_area_kpi" value="<?= $user_log["id_area"]; ?>">

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="tipo_kpi">Tipo de KPI *</label>
                                        <select name="tipo_kpi" id="tipo_kpi" class="form-control form-control-sm" required onclick="ValidarTipo(this.value)" >
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

                                    <div class="col-md-3" id="cont_tipo" style="display:none">
                                        <label >Ponderado Estratégicos</label>
                                        <input type="text" class="form-control form-control-sm" name="ponderado" id="ponderado" >
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
                                                <option value="<?= $area["id"]; ?>" <?= isset($data["area_proceso"]) && $area["id"] == $data["area_proceso"] ? 'selected' : ''; ?>><?= $area["nombre"]; ?></option>
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
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="objetivo_sg">Objetivo SG</label>
                                        <select class="select_2_search form-control" id="objetivo_sg" name="objetivo_sg">
                                            <option value="">Seleccionar..</option>
                                            <?php foreach($Kpis_Objetivos_SG as $objetivo): ?>
                                                <option value="<?= $objetivo["id"]; ?>" <?= isset($data["objetivo_sg"]) && $objetivo["id"] == $data["objetivo_sg"] ? 'selected' : ''; ?>><?= $objetivo["objetivo"]; ?></option>
                                            <?php endforeach; ?>    
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="indicador">Indicador *</label>
                                        <textarea name="indicador" id="indicador" rows="2" class="form-control" required><?= isset($data["indicador"]) ? $data["indicador"] : ''; ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="objetivo_indicador">Objetivo del Indicador *</label>
                                        <textarea name="objetivo_indicador" id="objetivo_indicador" rows="2" class="form-control" required><?= isset($data["objetivo_indicador"]) ? $data["objetivo_indicador"] : ''; ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="formula_calculo">Fórmula de Cálculo *</label>
                                        <textarea name="formula" id="formula" rows="2" class="form-control" required><?= isset($data["formula"]) ? $data["formula"] : ''; ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="resultado_anterior">Resultado Año Anterior</label>
                                        <input type="text" class="form-control numeros" name="resultado_anterior" id="resultado_anterior" value="<?= isset($data["resultado_anterior"]) ? $data["resultado_anterior"] : ''; ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="unidad_medida">Unidad de Medida *</label>
                                        <select class="form-control" name="unidad_medida" id="unidad_medida" required onchange="Validar_Unidad_Medida()">
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
                                        <?php include("views/kpis/componentes/modal_tipo_ayuda.php"); ?>
                                        <label for="tipo_resultado">Tipo de Resultado * <span class="text-warning cursor-pointer" data-bs-toggle="modal" data-bs-target="#tipo_ayuda_modal"><i class="bi bi-question-circle"></i></span></label>
                                        <select class="form-control" name="tipo_resultado" id="tipo_resultado" required onchange="Validar_Tipo_Resultado()">
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
                                                <input type="text" class="form-control" name="meta" id="meta" value="<?php echo $meta; ?>" onkeyup="return NumerosDecimales(this)" readonly >
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
                                        <?php include("views/kpis/componentes/modal_calculo_ayuda.php"); ?>
                                        <label for="tipo_calculo">Tipo de Cálculo * <span class="text-warning cursor-pointer" data-bs-toggle="modal" data-bs-target="#calculo_ayuda_modal"><i class="bi bi-question-circle"></i></span></label>
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
                                        <label for="frecuencia">Frecuencia *</label>
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

                                </div>
                            </div>

                            <div id="alerta"></div>

                            <div id="table_meses">
                                <?php 
                                    if( $data["tipo_kpi"] == 1 ){ include("views/kpis/detalle/tabla_mensual.php");  }
                                    if( $data["tipo_kpi"] == 2 ){ include("views/kpis/detalle/tabla_bimestral.php");  }
                                    if( $data["tipo_kpi"] == 3 ){ include("views/kpis/detalle/tabla_trimestral.php");  }
                                    if( $data["tipo_kpi"] == 6 ){ include("views/kpis/detalle/tabla_cuatrimestral.php");  }
                                    if( $data["tipo_kpi"] == 4 ){ include("views/kpis/detalle/tabla_semestral.php");  }
                                    if( $data["tipo_kpi"] == 5 ){ include("views/kpis/detalle/tabla_anual.php");  }
                                ?>
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
    //VALIDAMOS LA UNIDAD DE MEDIDA
    //VALIDAMOS LA UNIDAD DE MEDIDA}
    //VALIDAMOS LA UNIDAD DE MEDIDA
    function Validar_Unidad_Medida(){
        unidad_medida = $("#unidad_medida").val();
        if(unidad_medida == 1){
            $("#meta").attr("placeholder", "Número entero...");
            $(".mes_valor").attr("placeholder", "...");
        }
        if(unidad_medida == 2){
            $("#meta").attr("placeholder", "Número o decimal...");
            $(".mes_valor").attr("placeholder", "...");
        }
        if(unidad_medida == 3){
            $("#meta").attr("placeholder", "Número entero...");
            $(".mes_valor").attr("placeholder", "...");
        }
        if(unidad_medida == 4){
            $("#meta").attr("placeholder", "hh:mm:ss...");
            $(".mes_valor").attr("placeholder", "hh:mm:ss");
        }

        ValidarMeta();
        
    }

    function Validar_Tipo_Resultado(){
        tipo_resultado = $("#tipo_resultado").val();
        ValidarMeta();
    }

    //PARA VALIDAR EL CARGUE DE LA TABLA FRECUENCIA
    //PARA VALIDAR EL CARGUE DE LA TABLA FRECUENCIA
    //PARA VALIDAR EL CARGUE DE LA TABLA FRECUENCIA
    function CargarTabla(){

        permitir_carga = false;
        tipo = $("#frecuencia").val(); 

        if( $("#unidad_medida").val() && $("#tipo_resultado").val() && $("#tipo_calculo").val() ){
            permitir_carga = true;
        }

        if(permitir_carga){

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
            $("#alerta").html("");
        }
        else{
            $("#alerta").html("Recuerda que debes seleccionar: Unidad de Medida, Tipo de resultado, Tipo de Calculo para poder continuar");
            $("#frecuencia").val(""); 
        }
    }

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
                        ultimo_valor = hora;
                    }
                    /////NUEVO CODIGO









                    /*
                    hora = hora.split(":");

                    //console.log(hora);

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

            //console.log(ultimo_valor);

            //SOLO HORAS
            //part_1 = 0;
            //part_2 = 0;
            //part_3 = 0;

            //console.log(total_seguimientos);

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
                    */
                    valor_meta = resultado;
                    //console.log(valor_meta); 

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

    /*
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
    */




    function ValidarUnidadMedida(element){
        let unidad_medida = $("#unidad_medida").val();
        let valor = $(element).val();
        let formato = "";

        if (unidad_medida == 1 || unidad_medida == 2 || unidad_medida == 3) {

            // Permitir solo números, punto y signo -
            valor = valor.replace(/[^0-9.-]/g, '');

            // Solo permitir un signo - al inicio
            valor = valor.replace(/(?!^)-/g, '');

            // Solo permitir un punto decimal
            let partes = valor.split('.');
            if (partes.length > 2) {
                valor = partes.shift() + '.' + partes.join('');
            }

            formato = valor;
        }

        if (unidad_medida == 4) {
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
        //BUSCADORES
        $('.select_2_search').select2({
            // Si usas Bootstrap 5:
            theme: 'bootstrap-5'
        });
    });

    function ValidarTipo(tipo){
        if(tipo == 1){
            $("#cont_tipo").show();
        }
        if(tipo == 2){
            $("#cont_tipo").hide();
            $("#ponderado").val("");
        }
    }

</script>