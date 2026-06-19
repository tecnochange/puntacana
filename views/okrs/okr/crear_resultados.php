<script>
    $(document).ready(function() {
        $('#menuOkrs').collapse();
        $('#bt_okrs_crear').addClass('active');
    });
</script>

<?php
if ($_GET["id"]) {
    $_SESSION["id_okrs_edit"] = $_GET["id"];
}

include("app/models/okrs/OkrsCrud.php");
include("app/models/okrs/OkrsServicios.php");
$OkrsCRUD = new OkrsCrud();
$OkrsServicios = new OkrsServicios();

$hoy = date("Y-m-d H:i:s");

//PARA GUARDAR O EDITAR CONTENIDO
if ($_POST["guardar_formulario"]) {

    if($_POST["id_registro"]) {
        //ACTUALIZAR RESULTADOS CLAVES
        $OkrsCRUD->Actualizar_Resultados_Claves($_POST);
        echo '<script> window.location.href = "?pg=okrs/objetivos_asociados"; </script>';
    } 
    else {
        //CREAR RESULTADOS CLAVES
        $_POST["id_okrs"] = $_SESSION["id_okrs_edit"];
        $OkrsCRUD->Crear_Resultados_Claves($user_log["id_empresa"], $_POST);
        echo '<script> window.location.href = "?pg=okrs/objetivos_asociados"; </script>';
    }
}

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

    <div class="card mb-3">
        <div class="card-body" style="font-size: 19px; font-weight: bold; color: #007bff;">
            <?php echo $data["objetivo_okr"] ?>
        </div>
    </div>

    <?php echo $respuesta; ?>


    <div class="card mb-3">
        <div class="card-header">
            <h3>Resultados Claves (KR)</h3>
        </div>
        <div class="card-body">

            <!-- FORMULARIO -->
            <form action="" method="POST">
                <input type="hidden" name="id_registro" value="<?= isset($data_resultado_clave["id"]) ? $data_resultado_clave["id"] : null ?>">
                <input type="hidden" name="id_empleado" value="<?php echo $data["id_empleado"]; ?>">
                <input type="hidden" name="guardar_formulario" value="true">
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
                        <textarea name="descripcion_del_resultado" class="form-control"><?= $data_resultado_clave["descripcion"]; ?></textarea>
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
                        <button type="submit" class="btn btn-success ">Guardar</button>
                    </div>

                </div>
            </form>

        </div>
    </div>

  
</div>
<script>
    $(document).ready(function() {
        $('.multiples_responsables').select2();
    });
    

    var api = '<?php echo $url; ?>api/okrs/';

    
    var permitir = false;
    function EliminarResultadoClave(id){

        if(permitir == false){
            $("#modal_general").modal("show");
            $("#modal_body").html("Estas a punto de eliminar un Resultado Clave, esto eliminará todos los datos relacionados con el mismo incluyendo: seguimientos, iniciativas, planes de acción, etc. esta acción  es irreversible. ¿Está seguro? <br><br> ");
            $("#modal_body").append('<button type="button" class="btn btn-danger btn-sm" onclick="permitir = true;EliminarResultadoClave('+id+')">Eliminar</button> <br> Nota: este esta acción será registrada en la auditoría con su nombre.');
            
        }
        else{

            data = {
                id_empresa: <?php echo $user_log["id_empresa"]; ?>, 
                id_user: <?php echo $user_log["id"]; ?>,
                id_resultado: id, 
                url: '?pg=okrs/objetivos_asociados'
            };
            jQuery.ajax({
                url: api + "eliminar_resultado.php",
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