<script>
    $(document).ready(function() {
        $('#menuOkrs').collapse();
    });
</script>

<?php
include("app/models/okrs/OkrsCrud.php");
include("app/models/okrs/OkrsServicios.php");
$OkrsCRUD = new OkrsCrud();
$OkrsServicios = new OkrsServicios();

$hoy = date("Y-m-d H:i:s");

//PARA GUARDAR O EDITAR CONTENIDO
if ($_POST["guardar_formulario"]) {

    if ($_POST["id_registro"]) {
        //ACTUALIZAR RESULTADOS CLAVES
        $OkrsCRUD->Actualizar_Plan_Accion($_POST);
    } else {
        //CREAR RESULTADOS CLAVES
        $id_reg = $OkrsCRUD->Crear_plan_accion($user_log["id"], $user_log["id_empresa"], $_POST);
        echo '<script> window.location.href = "?pg=okrs/okr/planes_accion&id_plan_accion='.$id_reg.'"</script>';
    }
}

//O - OBJETIVOS
if (isset($_GET["id_okr"]) && !empty($_GET["id_okr"])) {
    $_SESSION["id_okrs_edit"] = $_GET["id_okr"];
    $okrs_objetivos = $OkrsServicios->objetivo_asociado($user_log["id_empresa"], $user_log["id"], $_GET["id_okr"]);
}else{
    $okrs_objetivos = $OkrsServicios->objetivos_asociados($user_log["id_empresa"], $user_log["id"], $user_log["id_area"], $_SESSION["anio_fill"]);
}

//dd($okrs_objetivos);

// RESULTADOS CLAVE (LISTA PLANA)
$resultados_claves = [];
foreach ($okrs_objetivos as $objetivo_clave) {
    foreach ($objetivo_clave["resultados"] as $resultados) {
        $resultados_claves[] = $resultados;
    }
}

// INICIATIVAS
if (isset($_GET["id_iniciativa"]) && !empty($_GET["id_iniciativa"])) {
    
    $okrs_iniciativas = $OkrsServicios->iniciativa($_GET["id_iniciativa"]);
    //dd($okrs_iniciativas);
}else{
    $okrs_iniciativas = $OkrsServicios->okrs_area_iniciativas($user_log["id"], $user_log["id_empresa"], $user_log["id_area"], $_SESSION["anio_fill"]); 

    
}

if (isset($_GET["id_plan_accion"]) && !empty($_GET["id_plan_accion"])) {
    $data_plan_accion = $OkrsServicios->okrs_obtener_plan_accion($_GET["id_plan_accion"]);
}

$array_responsables = explode(",", $data_plan_accion["id_asignado"]);

$lista_responsables = "";
foreach($array_responsables as $responsable){
    $queryResp = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id = '" . $responsable . "' ");
    $dataResp = mysqli_fetch_array($queryResp);

    $tipoColaborador = '';
    if ($dataResp["area"] == $id_area) {
        $tipoColaborador = 'Interno';
    } else {
        $tipoColaborador = 'Externo';
    }

    if($dataResp["id"] > 0){

    
        $lista_responsables .= '
        <div class="responsables"> 
            <button class="btn btn-sm btn-danger">
                <i class="bx bx-trash borrar boton-rojo" onClick="Eliminar_Responsable(' . $dataResp["id"] . ')"></i>
            </button>
            <b>' . $dataResp["nombre"] . '(' . $tipoColaborador . ')</b> 
        </div>';
    }

}

?>

<style>
    .responsables{
        margin: 6px 0px;
    }
    .tipos_apoyo{
        display:none;
    }
</style>



<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="container pb-4">
    <div class="card mb-3">

        <div class="card-header">
            <h3>Planes de Acción</h3>
        </div>

        <div class="card-body">
            <div class="alert alert-warning" role="alert">

                Para garantizar la correcta interpretación de los valores ingresados, por favor ten en cuenta lo siguiente:<br>
                Separador de Miles "." (Punto)<br>
                Ejemplo: $500.000 (quinientos mil)<br>
                Separador de Decimales "," (Coma)<br>
                Ejemplo: 0,7 (siete décimos) | -0,5 (menos cinco décimos)<br>
            </div>

            
            <!-- FORMULARIO -->
            <form action="" method="POST">
                <input type="hidden" name="id_registro" value="<?= isset($_GET["id_plan_accion"]) ? $_GET["id_plan_accion"] : ""; ?>">
                <input type="hidden" name="id_empleado" value="<?= $user_log["id"]; ?>">
                <input type="hidden" name="guardar_formulario" value="true">

                <input type="hidden" name="id_okrs" value="<?php echo $_SESSION["id_okrs_edit"]; ?>">
                <input type="hidden" name="id_resultado" value="true">
                <input type="hidden" name="id_iniciativa" value="true">

                

                <div class="row">

                    <div class="col-md-12 mb-2">
                        <label><strong>* Objetivo</strong></label>
                        <select class="form-control" name="id_okrs" id="select_objetivo" required>
                            <option value="" >Seleccione...</option>
                            <?php foreach ($okrs_objetivos as $objetivo): ?>
                                <option value="<?= $objetivo["id"]; ?>" <?= ((isset($data_plan_accion["id_okrs"]) || isset($_GET["id_objetivo"])) && ($data_plan_accion["id_okrs"] == $objetivo["id"] || $_GET["id_objetivo"] == $objetivo["id"])) || $_GET["id_okr"] == $objetivo["id"] ? "selected" : "" ?>><?= $objetivo["objetivo"]; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-12 mb-2">
                        <label><strong>* Resultados Clave</strong></label> <br>
                        <select class="form-control" name="id_resultado"  id="select_resultado"  required>
                            <option value="" >Seleccione...</option>
                            <?php foreach ($resultados_claves as $resultado): ?>
                                <option value="<?= $resultado["id"]; ?>" data-idokr="<?= $resultado["id_okrs"]; ?>" <?= (isset($data_plan_accion["id_resultado"]) || isset($_GET["id_resultado"])) && ($data_plan_accion["id_resultado"] == $resultado["id"] || $_GET["id_resultado"] == $resultado["id"]) ? "selected" : "" ?>><?= $resultado["descripcion"]; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-12 mb-2">
                        <label><strong>* Iniciativa</strong></label><br>
                        <select class="form-control" name="id_iniciativa" id="select_iniciativa" required>
                            <option value="" >Seleccione...</option>
                            <?php foreach ($okrs_iniciativas as $iniciativa): ?>
                                <option value="<?= $iniciativa["id"]; ?>" data-idresultado="<?= $iniciativa["id_resultado"]; ?>" <?= (isset($data_plan_accion["id_iniciativa"]) || isset($_GET["id_iniciativa"])) && ($data_plan_accion["id_iniciativa"] == $iniciativa["id"] || $_GET["id_iniciativa"] == $iniciativa["id"]) ? "selected" : "" ?>><?= $iniciativa["descripcion"]; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>




                    

                  

                    <div class="col-md-12 mb-2">
                        <label>* Plan de Acción</label>
                        <textarea name="descripcion" class="form-control"><?= isset($data_plan_accion["descripcion"]) ? $data_plan_accion["descripcion"] : ""; ?></textarea>
                    </div>

                    <div class="col-md-3 mb-2">
                        <label><strong>* Prioridad</strong></label>
                        <select class="form-control" name="prioridad" required>
                            <option value="">Seleccione...</option>
                            <option value="1" <?= isset($data_plan_accion["prioridad"]) && $data_plan_accion["prioridad"] == "1" ? "selected" : "" ?>>Bajo</option>
                            <option value="2" <?= isset($data_plan_accion["prioridad"]) && $data_plan_accion["prioridad"] == "2" ? "selected" : "" ?>>Medio</option>
                            <option value="3" <?= isset($data_plan_accion["prioridad"]) && $data_plan_accion["prioridad"] == "3" ? "selected" : "" ?>>Alto</option>
                            <option value="4" <?= isset($data_plan_accion["prioridad"]) && $data_plan_accion["prioridad"] == "4" ? "selected" : "" ?>>Urgente</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-2">
                        <label><strong>* Meta</strong></label>
                        <input type="text" class="form-control" name="meta" value="<?= isset($data_plan_accion["meta"]) ? $data_plan_accion["meta"] : ""; ?>" required>
                    </div>

                    <div class="col-md-3 mb-2">
                        <label><strong>* Fecha de inicio</strong></label>
                        <input type="date" class="form-control" name="fecha_inicia" value="<?= isset($data_plan_accion["fecha_inicia"]) ? $data_plan_accion["fecha_inicia"] : ""; ?>" required>
                    </div>

                    <div class="col-md-3 mb-2">
                        <label><strong>* Fecha de finalización</strong></label>
                        <input type="date" class="form-control" name="fecha_entrega" value="<?= isset($data_plan_accion["fecha_entrega"]) ? $data_plan_accion["fecha_entrega"] : ""; ?>" required>
                    </div>

                    <div class="col-md-3 mb-2">
                        <label><strong>Ciclo</strong></label>
                        <input type="text" class="form-control" name="ciclo" value="<?= isset($data_plan_accion["ciclo"]) ? $data_plan_accion["ciclo"] : ""; ?>">
                    </div>

                    <div class="col-md-3 mb-2" style="display:none">
                        <label><strong>* Tipo de Apoyo:</strong></label>
                        <select class="form-control" name="tipo_contribucion" >
                            <option value="">Seleccione...</option>
                            <option value="1" <?= isset($data_plan_accion["prioridad"]) && $data_plan_accion["prioridad"] == "1" ? "selected" : "" ?>>Interno</option>
                            <option value="2">Externo</option>
                        </select>
                    </div>
                </div>

                

                <div class="row">
                    <div class="col-md-12 mt-2 mb-2">
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </div>
                </div>




               <!-- NUEVO BLOQUE --> 
                <div class="row"  style="display:none" >
                    <div class="col-md-3" >
                        <label for="">Tipo de Apoyo:</label>
                        <select class="form-control form-control-sm" onchange="MostrarTipoApoyo(this.value)">
                            <option value="">Seleccione..</option>
                            <option value="1">Interno</option>
                            <option value="2">Externo</option>
                        </select>
                    </div>

                    <div class="col-md-3 tipos_apoyo interno_apoyo">
                        <label for="">Asignado a:</label>
                        <select id="id_empleado_interno" class="form-control multiples_responsables" style="width: 100%;" >
                            <option value="">Seleccione..</option>
                            <?php
                                $queryEmpleados = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id_empresa = '" . $user_log["id_empresa"] . "' AND area = '" . $user_log["id_area"] . "' ORDER BY nombre ASC");
                                while ($dataEmp = mysqli_fetch_array($queryEmpleados)) {
                                    echo '<option value="' . $dataEmp["id"] . '">' . $dataEmp["nombre"] . '</option>';
                                }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-3 tipos_apoyo interno_apoyo "> 
                        <label >.</label> <br>
                        <button type="button" class="btn btn-success btn-sm " onclick="AgregarColaborador()">
                            Agregar Responsable
                        </button>
                    </div>




                    <div class="col-md-4 tipos_apoyo externos_apoyo" >
                        <label for="">Alta Dirección Externa:</label>
                        <select id="id_vicepresidencia" class="form-control multiples_responsables" style="width: 100%;" onchange="Areas_Vicepresidencia(this.value);">
                                <option value="" selected>Seleccione..</option>
                                <?php
                                $queryvicepresidencias = mysqli_query($connect_admin, "SELECT * FROM Vicepresidencia WHERE id_empresa = '" . $user_log["id_empresa"] . "' ORDER BY nombre ASC ");
                                while ($datavicepresidencia = mysqli_fetch_array($queryvicepresidencias)) {
                                    echo '<option value="' . $datavicepresidencia["id"] . '">' . $datavicepresidencia["nombre"] . '</option>';
                                }
                                ?>

                        </select>
                    </div>

                        <div class="col-md-4 tipos_apoyo externos_apoyo" >
                            <label for="">Área Externa:</label>
                            <select id="id_areas" class="form-control multiples_responsables" style="width: 100%;" onchange="VerEmpleadosArea(this.value);">
                                <option value="" selected>Seleccione..</option>
                            </select>
                        </div>
                        <div class="col-md-3 tipos_apoyo externos_apoyo">
                            <label for="">Asignado a:</label>
                            <select id="id_empleado_externo" class="form-control multiples_responsables" style="width: 100%;" onchange="asignar_empleado_ext_edit(this);">

                            </select>
                        </div>



                        
                        
                        <div class="col-md-3 tipos_apoyo externos_apoyo" >
                            <button type="button" class="btn btn-success btn-block " onclick="AgregarColaboradorExterno()">
                                Agregar Responsable
                            </button>
                        </div>
                    </div>

                </div>

                <script>
                    $(document).ready(function() {
                        $('.multiples_responsables').select2();
                    });
                </script>




                <div style="margin: 20px;">
                    <?php 
                    echo $lista_responsables;
                    ?>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    var api_planes = '<?php echo $url; ?>api/okrs/';

    function MostrarTipoApoyo(tipo){ 
        $(".tipos_apoyo").hide();
        if(tipo == 1){
            $(".interno_apoyo").show();
        }
        if(tipo == 2){
            $(".externos_apoyo").show();
        }
    }

    function AgregarColaborador(){

        data = {
            id_empresa: <?php echo $user_log["id_empresa"]; ?>,
            id_colaborador: $("#id_empleado_interno").val(),
            url: '<?php echo $url; ?>?pg=okrs/okr/planes_accion&id_plan_accion=<?php echo $_GET["id_plan_accion"]; ?>' 
        };
        jQuery.ajax({
            url: api_planes + "insertar_responsable_plan_accion.php",
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

    function AgregarColaboradorExterno(){

        data = {
            id_empresa: <?php echo $user_log["id_empresa"]; ?>,
            id_colaborador: $("#id_empleado_externo").val(),
            url: '<?php echo $url; ?>?pg=okrs/okr/planes_accion&id_plan_accion=<?php echo $_GET["id_plan_accion"]; ?>' 
        };
        jQuery.ajax({
            url: api_planes + "insertar_responsable_plan_accion.php",
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

    function Areas_Vicepresidencia(id_vicepresidencia){
        data = {
            id_empresa: <?php echo $user_log["id_empresa"]; ?>,
            id_viceprecidencia: $("#id_vicepresidencia").val()  
        };
        jQuery.ajax({
            url: api_planes + "lista_areas_vicepresidencia.php",
            type: 'post',
            data: data,
            })
            .done(function(resp) {
                $("#id_areas").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {}
        );
    }

    function VerEmpleadosArea(){
        data = {
            id_empresa: <?php echo $user_log["id_empresa"]; ?>,
            id_viceprecidencia: $("#id_vicepresidencia").val(), 
            id_area: $("#id_areas").val()  
        };
        jQuery.ajax({
            url: api_planes + "lista_colaboradores_areas.php",
            type: 'post',
            data: data,
            })
            .done(function(resp) {
                $("#id_empleado_externo").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {}
        );
    }

</script>

<?php include("views/okrs/okr/js/okrs_planes_accion.php"); ?>