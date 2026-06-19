<script>
    $(document).ready(function() {
        $('#menuOkrs').collapse();
        $('#bt_okrs_crear').addClass('active');
    });
</script>

<?php
if($_GET["new"]){
    $_SESSION["id_okrs_edit"] = "";
}

include("app/models/okrs/OkrsCrud.php");
include("app/models/okrs/OkrsServicios.php");

$OkrsCRUD = new OkrsCrud();
$OkrsServicios = new OkrsServicios();

$disabled = "";
if($_GET["id"]){
    $_SESSION["id_okrs_edit"] = $_GET["id"];
    $disabled = "disabled";
}

$hoy = date("Y-m-d H:i:s");

//PARA GUARDAR O EDITAR CONTENIDO
if ($_POST["guardar_formulario"]) {

    if($_POST["id_registro"]) {
        //ACTUALIZAR OKRS
        $OkrsCRUD->Actualizar_Okrs($_POST);
    } 
    else {
        //CREAR OKRS
        $OkrsCRUD->Crear_Okrs($user_log["id_empresa"], $_POST);
    }
}

$data = $OkrsServicios->obtener_okrs($_SESSION["id_okrs_edit"]); //OBTENER OKRS
$objetivos_estrategicos = $OkrsServicios->Obtener_Objetivos_Estrategicos($user_log["id_empresa"], $_SESSION["anio_fill"]); //OBTENER OBJETIVOS ESTRATEGICOS
?>

<div class="container">

    <?php echo $respuesta; ?>

    <?php if($_SESSION["id_okrs_edit"]){ ?>
        <div class="card mb-3">
            <div class="card-body" style="font-size: 19px; font-weight: bold; color: #007bff;">
                <?php echo $data["objetivo_okr"] ?>
            </div>
        </div>

        <ul class="nav nav-tabs justify-content-center">
            <li class="nav-item">
                <a class="nav-link active" href="<?php echo $url; ?>?pg=okrs/okr/detalle">PASO 1. OKRS del Equipo</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo $url; ?>?pg=okrs/okr/areas" >PASO 2. Áreas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo $url; ?>?pg=okrs/okr/integrantes" >PASO 3. Integrantes</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo $url; ?>?pg=okrs/okr/resultados" >PASO 4. Resultados Claves (KR)</a>
            </li>
        </ul>
    <?php  } ?>

    <div class="card mb-3">
        <div class="card-header">
            <h3>Ficha del OKRs</h3>
        </div>
        <div class="card-body">

            <form action="" method="POST">
                <input type="hidden" name="id_registro" value="<?= $data["id"]; ?>">
                <input type="hidden" name="guardar_formulario" value="true">
                <input type="hidden" name="tipo" value="<?= $data['tipo'] ?>">
                <div class="row">

                    <div class="col-md-3 mb-2">
                        <label><strong>Tipo de OKRs *</strong></label>
                        <select class="form-control" name="tipo" onchange="ObjetivosAltaDireccion()" required <?= $disabled; ?>>
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
                        <select class="form-control" name="anio" id="anio" onchange="ObjetivosAltaDireccion()" required>
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
                        <textarea rows="3" class="form-control" name="objetivo_okr" required placeholder="Ingrese su Objetivo en este espacio..."><?php echo $data["objetivo_okr"] ?></textarea>
                    </div>



                    <div class="col-md-2 mb-2">
                        <label><strong>* Periodo Inicia</strong></label>
                        <input type="date" class="form-control" name="fecha_inicia" value="<?php echo $data["fecha_inicia"]; ?>" required>
                    </div>

                    <div class="col-md-2 mb-2">
                        <label><strong>* Periodo Termina</strong></label>
                        <input type="date" class="form-control" name="fecha_termina" value="<?php echo $data["fecha_termina"]; ?>" required>
                    </div>

                    <div class="col-md-2 mb-2">
                        <label><strong>Periodo *</strong></label>
                        <select class="form-control" name="periodo" required>
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
                        <button type="submit" class="btn btn-success ">Guardar</button>
                    </div>

                </div>
            </form>

        </div>
    </div>

    <?php if ($_SESSION["id_okrs_edit"] > 0) { ?>
        <div align="right" style="margin-top: 15px">
            <button type="button" class="btn btn-danger btn-sm " onClick="EliminarOKRs()" >Eliminar</button>
        </div>
    <?php } ?>

</div>

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
   

    
    var permitir = false;
    function EliminarOKRs(){

        if(permitir == false){
            $("#modal_general").modal("show");
            $("#modal_body").html("Estas a punto de eliminar un OKR, esto eliminará todos los datos relacionados con el mismo incluyendo: Resultados Claves, integrantes, iniciativas, planes de acción, comentarios, documentos, etc. esta acción  es irreversible. ¿Está seguro? <br><br> ");
            $("#modal_body").append('<button type="button" class="btn btn-danger btn-sm" onclick="permitir = true;EliminarOKRs()">Eliminar Okrs</button> <br> Nota: este esta acción será registrada en la auditoría con su nombre.');
            
        }
        else{

            data = {
                id_empresa: <?php echo $user_log["id_empresa"]; ?>, 
                id_user: <?php echo $user_log["id"]; ?>,
                id_okr: <?php echo $_SESSION["id_okrs_edit"]; ?>, 
                url: '?pg=okrs/objetivos_asociados'
            };
            jQuery.ajax({
                url: api + "eliminar_okr.php",
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