<script>
    $(document).ready(function() {
        $('#menuOkrs').collapse();
        $('#bt_okrs_crear').addClass('active');
    });
</script>

<?php
include("app/models/okrs/OkrsCrud.php");
include("app/models/okrs/OkrsServicios.php");
$OkrsCRUD = new OkrsCrud();
$OkrsServicios = new OkrsServicios();

$hoy = date("Y-m-d H:i:s");

if ($_GET["id"]) {
    $_SESSION["id_okrs_edit"] = $_GET["id"];
}

if (isset($_POST["guardar_integrantes"])) {
    //ASOCIAR INTEGRANTES AL OKRS
    $OkrsCRUD->Guardar_Integrantes($user_log["id_empresa"], $_POST);
    $_POST = [];
}

$data = $OkrsServicios->obtener_okrs($_SESSION["id_okrs_edit"]); //OBTENER OKRS
$integrantes_asociados = $OkrsServicios->obtener_integrantes_asociados($user_log["id_empresa"], $data["id"]); 
?>

<style>
    #tablaEmpleados th {
        white-space: nowrap !important;
    }
</style>

<div class="container pb-4">

    <div class="card mb-3">
        <div class="card-body" style="font-size: 19px; font-weight: bold; color: #007bff;">
            <?php echo $data["objetivo_okr"] ?>
        </div>
    </div>

    <?php echo $respuesta; ?>

    <ul class="nav nav-tabs justify-content-center">
        <li class="nav-item">
            <a class="nav-link " href="<?php echo $url; ?>?pg=okrs/okr/detalle">Paso 1. OKRS del Equipo</a>
        </li>
        <li class="nav-item">
            <a class="nav-link " href="<?php echo $url; ?>?pg=okrs/okr/areas">PASO 2. Áreas</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="<?php echo $url; ?>?pg=okrs/okr/integrantes">PASO 3. Integrantes</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?php echo $url; ?>?pg=okrs/okr/resultados">PASO 4. Resultados Claves (KR)</a>
        </li>
    </ul>

    <div class="card mb-3">

        <div class="card-body">

            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="accordion accordion-flush" id="accordionFlushOne">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" style="background-color: #ffc107;" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                                    <b>Administrador de los Resultados Clave (KR)</b>
                                </button>
                            </h2>
                            <div id="flush-collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionFlushOne">
                                <div class="accordion-body" style="font-size: 0.8em;">
                                    Accesos y Funcionalidades
                                    <ul>
                                        <li>
                                            <b>Gestión de Resultados Clave (KR)</b>
                                            <ul>
                                                <li>Crear, editar y eliminar KR en cada trimestre (Q).</li>
                                                <li>Asociar KR directamente al Objetivo y validarlos con el Owner.</li>
                                            </ul>
                                        </li>
                                        <li>
                                            <b>Gestión de la Célula de Trabajo</b>
                                            <ul>
                                                <li>Invitar y asignar nuevos integrantes a la célula de trabajo.</li>
                                                <li>Modificar roles dentro de la célula en colaboración con el Owner.</li>
                                            </ul>
                                        </li>
                                        <li>
                                            <b>Gestión de Iniciativas y Planes de Acción</b>
                                            <ul>
                                                <li>Crear, asignar y gestionar Iniciativas Estratégicas relacionadas con los KR.</li>
                                                <li>Diseñar, asignar y gestionar planes de acción específicos para alcanzar KR.</li>
                                            </ul>
                                        </li>
                                        <li>
                                            <b>Colaboración Interdepartamental</b>
                                            <ul>
                                                <li>Llamar a otras áreas para involucrarlas en iniciativas específicas.</li>
                                            </ul>
                                        </li>
                                    </ul>
                                    Restricciones:
                                    <ul>
                                        <li>No puede modificar el Objetivo principal, solo sus KR.</li>
                                        <li>No puede reasignar el rol de Owner del Objetivo.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="accordion accordion-flush" id="accordionFlushTwo">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" style="background-color: #ffc107;" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseOne">
                                    <b>Contribuyente Directo</b>
                                </button>
                            </h2>
                            <div id="flush-collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionFlushTwo">
                                <div class="accordion-body" style="font-size: 0.8em;">
                                    Accesos y Funcionalidades
                                    <ul>
                                        <li>
                                            <b>Gestión de Iniciativas y Planes de Acción</b>
                                            <ul>
                                                <li>Crear Iniciativas Estratégicas relacionadas con los KR asignados.</li>
                                                <li>Alimentar el seguimiento únicamente de las Iniciativas y Planes de Acción donde aparece como responsable.</li>
                                                <li>Proponer y definir planes de acción para cumplir con las iniciativas estratégicas.</li>
                                            </ul>
                                        </li>
                                        <li>
                                            <b>Visualización</b>
                                            <ul>
                                                <li>El Objetivo al que está vinculado.</li>
                                                <li>Los KR asociados al Objetivo en los que participa.</li>
                                                <li>Las Iniciativas Estratégicas donde es contribuyente directo o de apoyo.</li>
                                            </ul>
                                        </li>
                                        <li>
                                            <b>Participación en la Célula</b>
                                            <ul>
                                                <li>Aportar seguimiento en iniciativas específicas asignadas como responsable.</li>
                                            </ul>
                                        </li>
                                    </ul>
                                    Restricciones:
                                    <ul>
                                        <li>Crear KR ni modificar los existentes.</li>
                                        <li>Alimentar el seguimiento de iniciativas, KR o objetivos donde no sea responsable directo.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="accordion accordion-flush" id="accordionFlushThree">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" style="background-color: #ffc107;" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseOne">
                                    <b>Contribuyente de Apoyo</b>
                                </button>
                            </h2>
                            <div id="flush-collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionFlushThree">
                                <div class="accordion-body" style="font-size: 0.8em;">
                                    Accesos y Funcionalidades
                                    <ul>
                                        <li>
                                            <b>Contribución Interdepartamental</b>
                                            <ul>
                                                <li>No puede crear Iniciativas Estratégicas relacionadas con los KR asignados.</li>
                                                <li>Participar como soporte en iniciativas específicas en las que lo hayan delegado.</li>
                                                <li>Alimentar el seguimiento de actividades en las iniciativas asignadas como apoyo.</li>
                                            </ul>
                                        </li>
                                        <li>
                                            <b>Gestión de Planes de Acción</b>
                                            <ul>
                                                <li>Crear, asignar y gestionar Iniciativas Estratégicas relacionadas con los KR.</li>
                                                <li>Diseñar, asignar y gestionar planes de acción específicos para alcanzar KR.</li>
                                            </ul>
                                        </li>
                                        <li>
                                            <b>Visualización</b>
                                            <ul>
                                                <li>Iniciativas donde es un contribuyente de apoyo.</li>
                                                <li>Progreso de KR asociados con dichas iniciativas.</li>
                                                <li>Progreso de los Objetivos asociados conde esta participando.</li>
                                            </ul>
                                        </li>
                                    </ul>
                                    Restricciones:
                                    <ul>
                                        <li>Crear KR, iniciativas ni planes de acción.</li>
                                        <li>Modificar objetivos, KR o iniciativas existentes.</li>
                                        <li>Gestionar o reasignar roles en la célula.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form method="POST">
                <input type="hidden" name="id_registro" value="<?php echo $data["id"]; ?>">
                <input type="hidden" name="guardar_integrantes" value="true">

                <div class="row">
                    <div class="col-md-12 mt-4 mb-4">
                        Para asignar 1 o varios colaboradores al OKRs, seleccione el o los colaboradores de la tabla, despues haga clic en ASOCIAR INTEGRANTES para validar que hallan quedado seleccionado el colaborador con su respectivo rol. después de esta validación de clic en GUARDAR.
                    </div>

                    <div class="col-md-4 mb-2">
                        <label><strong>Alta Dirección *</strong></label>
                        <select class="multiples_responsables form-control" id="vicepresidencia" name="vicepresidencia" onchange="ObtenerAreas(this.value);">
                            <option value="">Seleccione...</option>
                            <?php
                            $queryVicepresidencias = mysqli_query($connect_admin, "SELECT * FROM Vicepresidencia WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND estado = 1 ORDER BY nombre ASC");
                            while ($dataVicepresidencia = mysqli_fetch_array($queryVicepresidencias)) {
                                if ($id_estrategico == $dataVicepresidencia["id"]) {
                                    echo '<option value="' . $dataVicepresidencia["id"] . '" selected>' . $dataVicepresidencia["nombre"] . '</option>';
                                } else {
                                    echo '<option value="' . $dataVicepresidencia["id"] . '">' . $dataVicepresidencia["nombre"] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label><strong>Área *</strong></label>
                        <select class="multiples_responsables form-control" id="area" name="area" disabled>
                            <option value="">Seleccione...</option>
                        </select>
                    </div>
                </div>

                <div id="tablaEmpleados_contenedor" class="table-responsive mt-4 d-none">
                    <table id="tablaEmpleados" class="table">
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th>Nombre</th>
                                <th>Cargo</th>
                                <th>Administrador de Resultados Clave (KR) <input type="checkbox" onclick="SeleccionarTodos(this,1);"></th>
                                <th>Contribuyente Directo <input type="checkbox" onclick="SeleccionarTodos(this,2);"></th>
                                <th>Contribuyente de Apoyo <input type="checkbox" onclick="SeleccionarTodos(this,3);"></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <button id="btnAsociarIntegrantes" class="btn btn-primary" disabled>Asociar Integrantes al OKRs</button>
                </div>

            </form>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h5>Listado de Integrantes del OKRs</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Integrante</th>
                            <th>Cargo</th>
                            <th>Alta Dirección</th>
                            <th>Área</th>
                            <th>Tipo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($integrantes_asociados as $integrante): ?>

                            <?php
                            $integrante_foto = !empty($integrante["empleado"]["foto"]) ? $integrante["empleado"]["foto"] : "img_default.jpg"; //FOTO DEL INTEGRANTE
                            $tipo = $integrante["tipo"] == "1" ? "Administrador de Resultados Clave (KR)" : ($integrante["tipo"] == "2" ? "Contribuyente Directo" : ($integrante["tipo"] == "3" ? "Contribuyente de Apoyo" : "Sin Tipo Asignado"));
                            ?>

                            <tr>
                                <td><img src="<?= $recursos_local . $integrante_foto; ?>" width="40" height="40" class="rounded-circle" title="<?= $integrante["empleado"]["nombre"]; ?>"></td>
                                <td><?= $integrante["empleado"]["nombre"]; ?></td>
                                <td><?= $integrante["empleado"]["nombre_cargo"]; ?></td>
                                <td><?= $integrante["empleado"]["nombre_vicepresidencia"]; ?></td>
                                <td><?= $integrante["empleado"]["nombre_area"]; ?></td>
                                <td><?= $tipo; ?></td>
                                <td>
                                    <div class="btn-group dropstart">
                                        <a class="btn btn-sm btn-outline-dark dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bx bx-pencil" title="Editar Tipo"></i>
                                        </a>                       
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" onclick="okrsClass.EditarTipoIntegrante(<?= $integrante['id'] ?>,1)">Administrador de Resultados Clave (KR)</a></li>
                                            <li><a class="dropdown-item" onclick="okrsClass.EditarTipoIntegrante(<?= $integrante['id'] ?>,2)">Contribuyente Directo</a></li>
                                            <li><a class="dropdown-item" onclick="okrsClass.EditarTipoIntegrante(<?= $integrante['id'] ?>,3)">Contribuyente de Apoyo</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                        </ul>
                                        
                                    </div>

                                    <button 
                                        data-id_okr="<?= $data["id"]; ?>" 
                                        data-tipo_okr="<?= $data["tipo"]; ?>"
                                        data-nombre="<?php echo $integrante["empleado"]["nombre"]; ?>"
                                        data-nombre_okrs="<?php echo $data["objetivo_okr"]; ?>"
                                        class="btn btn-danger btn-sm" 
                                        onclick="EliminarIntegrante( <?= $integrante["id"]; ?> , this )">
                                        X
                                    </button>
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
    function SeleccionarTodos(element,tipo){ 

        if( $(element).prop("checked") ) {
            if(tipo == 1){ $(".chk-rol-admin").prop("checked", true); }
            if(tipo == 2){ $(".chk-rol-contri").prop("checked", true); }
            if(tipo == 3){ $(".chk-rol-apoyo").prop("checked", true); }

        }
        else{
            if(tipo == 1){ $(".chk-rol-admin").prop("checked", false); }
            if(tipo == 2){ $(".chk-rol-contri").prop("checked", false); }
            if(tipo == 3){ $(".chk-rol-apoyo").prop("checked", false); }
        }

        //actualizarBoton();
    }
</script>


<?php include("app/models/okrs/OkrsScripts.php"); ?>
<script>
    // Instancia global accesible por los inputs
    const okrsClass = new OkrsScripts();
</script>



<?php include("views/okrs/okr/js/okrs_integrantes.php"); ?>

<script>
    var api = '<?php echo $url; ?>api/okrs/';
    var permitir = false;
    var adicionales;
    function EliminarIntegrante(id, element) {
        if(element){
            adicionales = element;
        }
        
        if(permitir == false){
            $("#modal_general").modal("show");
            $("#modal_body").html("Estas a punto de borrar un integrante relacionado a este OKRs. esta acción es irreversible. se guardará un registro de la acción con tu nombre. ¿Estas seguro? <br><br> ");
            $("#modal_body").append('<button class="btn btn-danger" onclick="permitir = true; EliminarIntegrante('+id+')" >Eliminar Integrante</button>');
            
        }
        else{

            descripcion = 'Eliminar integrante ' + $(adicionales).data("nombre") + ' al OKR ' + $(adicionales).data("nombre_okrs");

            $.ajax({
                url: api + "eliminar_integrante.php",
                type: "POST",
                data: {
                    id_empresa: <?php echo $user_log["id_empresa"]; ?>,
                    id: id, 
                    url: '<?php echo $url; ?>?pg=okrs/okr/integrantes', 
                    id_user: <?php echo $user_log["id"]; ?>,
                    descripcion: descripcion,
                    tipo_okr: $(adicionales).data("tipo_okr") ,
                    id_okr: $(adicionales).data("id_okr")
                },
                success: function (data) {
                    $("#xscript").html(data);
                },
                error: function (xhr, status, error) {
                    console.log("Error :", error);
                }
            });
            
        }
        
    }
</script>