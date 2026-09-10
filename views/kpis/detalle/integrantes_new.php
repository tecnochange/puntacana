<script>
    $(document).ready(function() {
        $('#menuKpis').collapse();
        $('#bt_mis_kpis').addClass('active');
    });
</script>

<?php
$hoy = date("Y-m-d H:i:s");

$id = $_GET["id"];

if ($_GET["id"]) {
    $_SESSION["id_kpi_edit"] = $_GET["id"];
}

include("app/models/kpis/KpisCrud.php");
$KpisCRUD = new KpisCrud();
include("app/models/kpis/KpisServicios.php");
$ClassKpisServicios = new KpisServicios($user_log["id_empresa"]);

if (isset($_POST["guardar_integrantes"])) {
    //ASOCIAR INTEGRANTES AL KPI
    $KpisCRUD->Guardar_Integrantes($user_log["id_empresa"], $user_log["id"], $_SESSION["anio_ciclo"], $_POST);
    $respuesta = '
    <div class="alert alert-success" role="alert">
        El integrante ha sido relacionado con éxito.
    </div>
    ';
}

$data = $KpisCRUD->obtener_data_kpis($_SESSION["id_kpi_edit"]);

//print_r($data);
$integrantes_asociados = $ClassKpisServicios->obtener_integrantes($user_log["id_empresa"], $_SESSION["id_kpi_edit"]);

//Mensajes y alertas
$mensajes_kpis = $ClassKpisServicios->mensajes_kpis($user_log["id_empresa"]);

?>

<style>
    #tablaEmpleados th {
        white-space: nowrap !important;
    }
</style>

<div class="container-fluid pb-4" style="max-width: 90%; margin: 0 auto;">

    <div class="card mb-3">
        <div class="card-body" style="font-size: 19px; font-weight: bold; color: #007bff;">
            <?php echo $data["objetivo_indicador"] ?>
        </div>
    </div>

    <?php echo $respuesta; ?>

    <div class="card mb-3">

        <div class="card-body">

            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="accordion accordion-flush" id="accordionFlushOne">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" style="background-color: #ffc107;" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                                    <?php
                                    foreach($mensajes_kpis["roles_kpis"] as $mensaje){
                                        if($mensaje["id_rol"] == 1){
                                            echo '<b>'.$mensaje["nombre_rol"].'</b>';
                                        }
                                    }
                                    ?>  
                                </button>
                            </h2>
                            <div id="flush-collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionFlushOne">
                                <div class="accordion-body" style="font-size: 0.8em;">
                                    <?php
                                    foreach($mensajes_kpis["roles_kpis"] as $mensaje){
                                        if($mensaje["id_rol"] == 1){
                                            echo $mensaje["descripcion"];
                                        }
                                    }
                                    ?>
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
                                    <?php
                                    foreach($mensajes_kpis["roles_kpis"] as $mensaje){
                                        if($mensaje["id_rol"] == 2){
                                            echo '<b>'.$mensaje["nombre_rol"].'</b>';
                                        }
                                    }
                                    ?>    
                                </button>
                            </h2>
                            <div id="flush-collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionFlushTwo">
                                <div class="accordion-body" style="font-size: 0.8em;">
                                    <?php
                                    foreach($mensajes_kpis["roles_kpis"] as $mensaje){
                                        if($mensaje["id_rol"] == 2){
                                            echo $mensaje["descripcion"];
                                        }
                                    }
                                    ?>
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
                                    <?php
                                    foreach($mensajes_kpis["roles_kpis"] as $mensaje){
                                        if($mensaje["id_rol"] == 3){
                                            echo '<b>'.$mensaje["nombre_rol"].'</b>';
                                        }
                                    }
                                    ?>  
                                </button>
                            </h2>
                            <div id="flush-collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionFlushThree">
                                <div class="accordion-body" style="font-size: 0.8em;">
                                    <?php
                                    foreach($mensajes_kpis["roles_kpis"] as $mensaje){
                                        if($mensaje["id_rol"] == 3){
                                            echo $mensaje["descripcion"];
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form method="POST">
                <input type="hidden" name="id_registro" value="<?php echo $data["id"]; ?>">
                <input type="hidden" name="id_area_macro" value="<?php echo $data["area_macro"]; ?>">
                <input type="hidden" name="id_area_proceso" value="<?php echo $data["area_proceso"]; ?>">
                <input type="hidden" name="guardar_integrantes" value="true">

                <div class="row">
                    <div class="col-md-12 mt-4 mb-4">
                        Para asignar 1 o varios colaboradores al KPI, seleccione el o los colaboradores de la tabla, despues haga clic en Asociar Integrantes para validar que hallan quedado seleccionado el colaborador con su respectivo rol. después de esta validación de clic en guardar.
                    </div>

                    <div class="col-md-4 mb-2">
                        <label>Contribución al KPI *</label>
                        <select class="form-control" id="contribucion_kpi" name="contribucion_kpi" onchange="ValidarContribucion(this.value)">
                            <option value="">Seleccione...</option>
                            <?php

                            foreach ($Array_Tipo_Contribucion as $tipo) {
                                echo '<option value="' . $tipo[0] . '">' . $tipo[1] . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div id="altadireccionSelect" class="col-md-4 mb-2 " style="display:none">
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

                    <div id="areaSelect" class="col-md-4 mb-2" style="display:none">
                        <label><strong>Área *</strong></label>
                        <select class="multiples_responsables form-control" id="area" name="area" disabled onchange="ObtenerColaboradores()">
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
                                <th class="text-center">
                                    Seleccionar todos <input type="checkbox" onclick="SeleccionarTodos(this);">
                                </th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <button id="btnAsociarIntegrantes" class="btn btn-primary float-end mt-4" disabled>Asociar Integrantes al Kpi</button>
                </div>

            </form>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h5 class="mb-0">Listado de Integrantes del Kpi</h5>
            <p class="mb-0">Asignación de roles y responsabilidades (Lider / Colaborador) al KPI</p>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Nombre</th>
                            <th>Cargo</th>
                            <th>Vicepresidencia</th>
                            <th>Área</th>
                            <th>Rol Kpi</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($integrantes_asociados as $integrante): ?>

                            <?php
                            //dd($integrante);
                            $integrante_foto = !empty($integrante["foto"]) ? $integrante["foto"] : "img_default.jpg"; //FOTO DEL INTEGRANTE
                            $tipo = $integrante["tipo"] == "1" ? "Administrador KPI" : ($integrante["tipo"] == "2" ? "Lider KPI" : ($integrante["tipo"] == "3" ? "Colaborador KPI" : "Sin Tipo Asignado"));
                            ?>

                            <tr>
                                <td><img src="<?= $recursos_local . $integrante_foto; ?>" width="40" height="40" class="foto_miniaturas" title="<?= $integrante["nombre"]; ?>" onclick="FichaEmpleado('<?= $integrante['id_colaborador']; ?>')"></td>
                                <td><?= $integrante["nombre"]; ?></td>
                                <td><?= $integrante["cargo"]; ?></td>
                                <td><?= $integrante["vicepresidencia"]; ?></td>
                                <td><?= $integrante["area"]; ?></td>
                                <td><?= $tipo; ?></td>
                                <td>

                                    <div class="btn-group dropstart">
                                        <a class="btn btn-sm btn-outline-dark dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bx bx-pencil" title="Editar Tipo"></i>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" onclick="kpisClass.EditarTipoIntegrante(<?= $integrante['id'] ?>,2)">Lider KPI</a></li>
                                            <li><a class="dropdown-item" onclick="kpisClass.EditarTipoIntegrante(<?= $integrante['id'] ?>,3)">Colaborador KPI</a></li>
                                        </ul>
                                    </div>

                                    <button 
                                        data-id_kpi="<?= $data["id"]; ?>" 
                                        data-tipo_kpi="<?= $data["tipo_kpi"]; ?>"
                                        data-nombre="<?php echo $integrante["nombre"]; ?>"
                                        data-nombre_kpi="<?php echo $data["objetivo_indicador"]; ?>"
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

            <div class="row">
                <div class="col-md-6 mb-2">
                    <a href="<?php echo $url; ?>?pg=kpis/detalle/detalle_kpi&id=<?php echo $_SESSION["id_kpi_edit"]; ?>">
                        <button type="button" class="btn btn-success w-100">
                            << Anterior
                                </button>
                    </a>

                </div>

                <div class="col-md-6 mb-2">
                    <a href="?pg=kpis/kpis_empresa">
                        <button type="button" class="btn btn-warning w-100">
                            Finalizar
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </div>                        
</div>

<script>
    var api = '<?php echo $url; ?>api/okrs/';

    function SeleccionarTodos(element){ 

        if( $(element).prop("checked") ) {
            //console.log("selecionado");
            $(".chk-rol").prop("checked", true);
            $("#btnAsociarIntegrantes").removeAttr("disabled");
            //$("#btnAsociarIntegrantes").prop("disabled");

            var table = $('#tablaEmpleados');
            var rows = table.rows({ search: 'applied' }).nodes();
            $('input[type="checkbox"]', rows).prop('checked', this.checked);

        }else{
            $(".chk-rol").prop("checked", false);
            $("#btnAsociarIntegrantes").prop("disabled", true);
        }

    }

    function FichaEmpleado(id_empleado) {

        var id_empresa = <?php echo $user_log["id_empresa"]; ?>

        $("#modal_empleado").modal("show");
        $("#body_empleado").html("Cargando...");

        jQuery.ajax({
                url: api + "ficha_empleado.php",
                type: 'post',
                data: {
                    id_empresa: id_empresa,
                    id_empleado: id_empleado,
                },
            })
            .done(function(resp) {
                $("#body_empleado").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});
    }
</script>






<script>
    var api = '<?php echo $url; ?>api/kpis/';
    var permitir = false;
    var adicionales;
    function EliminarIntegrante(id, element) {
        if(element){
            adicionales = element;
        }
        
        if(permitir == false){
            $("#modal_general").modal("show");
            $("#modal_body").html("Estas a punto de borrar un integrante relacionado a este KPI. Esta acción es irreversible. Se guardará un registro de la acción con tu nombre. ¿Estas seguro? <br><br> ");
            $("#modal_body").append('<button class="btn btn-danger" onclick="permitir = true; EliminarIntegrante('+id+')" >Eliminar Integrante</button>');
            
        }
        else{

            descripcion = 'Eliminar integrante ' + $(adicionales).data("nombre") + ' al KPI ' + $(adicionales).data("nombre_kpi");

            $.ajax({
                url: api + "eliminar_integrante.php",
                type: "POST",
                data: {
                    id_empresa: <?php echo $user_log["id_empresa"]; ?>,
                    id: id, 
                    url: '<?php echo $url; ?>?pg=kpis/detalle/integrantes', 
                    id_user: <?php echo $user_log["id"]; ?>,
                    descripcion: descripcion,
                    tipo_kpi: $(adicionales).data("tipo_kpi") ,
                    id_kpi: $(adicionales).data("id_kpi")
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

    function ValidarContribucion(tipo){
        $("#altadireccionSelect").hide();
        $("#areaSelect").hide();
        if(tipo == 1 ){
            $("#altadireccionSelect").show();
            $("#areaSelect").show();
        }
        if(tipo == 2 ){

        }
    }

    function ObtenerAreas(id_vicepresidencia){
        $.ajax({
            url: api + "lista_areas_vicepresidencia.php",
            type: "POST",
            data: {
                id_empresa: <?php echo $user_log["id_empresa"]; ?>,
                id_viceprecidencia: id_vicepresidencia,
            },
            success: function (data) {
                $("#area").prop("disabled", false);
                $("#area").html(data);
                ObtenerColaboradores(id_vicepresidencia);
            },
            error: function (xhr, status, error) {
                console.log("Error :", error);
            }
        });
    }

    // 🔹 Obtener colaboradores
    function ObtenerColaboradores(){
        id_vicepresidencia = $("#vicepresidencia").val();
        id_area = $("#area").val();
        $.ajax({
            url: api + "lista_integrantes.php",
            type: "POST",
            dataType: "json",
            data: {
                id_empresa: <?php echo $user_log["id_empresa"]; ?>,
                id_vicepresidencia: id_vicepresidencia,
                id_area: id_area,
            },
            success: function(resp) {
                $("#tablaEmpleados_contenedor").removeClass("d-none");
                listaEmpleados = resp;
                renderTablaEmpleados(listaEmpleados);
            },
            error: function(xhr, status, error) {
                console.log("Error al obtener colaboradores:", error);
            }
        });
    }


    // 🔹 Renderizar tabla de empleados
    function renderTablaEmpleados(lista) {
        if ($.fn.DataTable.isDataTable("#tablaEmpleados")) {
            $("#tablaEmpleados").DataTable().destroy();
        }

        const $tbody = $("#tablaEmpleados tbody");
        $tbody.empty();

        if (lista.length === 0) {
            $tbody.append(`
            <tr>
                <td colspan="6" class="text-center text-muted">No hay colaboradores.</td>
            </tr>
        `);
        } else {
            lista.forEach(function(empleado) {
                empleado.foto = empleado.foto ? empleado.foto : "/img_default.jpg";
                $tbody.append(`
                <tr data-id="${empleado.id}">
                    <td><img src="https://puntacana.goforagile.com/recursos/${empleado.foto}" width="40" height="40" class="foto_miniaturas" onclick="FichaEmpleado('${empleado.id}')"></td>
                    <td>${empleado.nombre}</td>
                    <td>${empleado.nombre_cargo}</td>
                    <td align="center"><input type="checkbox" class="chk-rol" name="empleados[${empleado.id}]" value="1"></td>
                </tr>
            `);
            });
        }

        $("#tablaEmpleados").DataTable({
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "Mostrar Todos"]
            ]
        });

        // 🔹 Permitir solo un checkbox activo por empleado
        $("#tablaEmpleados").off("change", ".chk-rol").on("change", ".chk-rol", function() {
            const $row = $(this).closest("tr");
            if ($(this).is(":checked")) {
                $row.find(".chk-rol").not(this).prop("checked", false);
            }
        });
    }
</script>




































<?php include("app/models/kpis/KpisScripts.php"); ?>
<?php //include("views/kpis/kpi/js/kpis_integrantes.php"); ?>
<script>
    // Instancia global accesible por los inputs
    const kpisClass = new KpisScripts();

    const contribucion_kpi = document.getElementById("contribucion_kpi");
    const altadireccionSelect = document.getElementById("altadireccionSelect");
    const areaSelect = document.getElementById("areaSelect");

    const ValidarContribucion__ = (contribucion) => {
        if (!contribucion) {
            altadireccionSelect.classList.add("d-none");
            areaSelect.classList.add("d-none");
            $("#tablaEmpleados_contenedor").addClass("d-none");

            if ($.fn.DataTable.isDataTable("#tablaEmpleados")) {
                $("#tablaEmpleados").DataTable().destroy();
            }
        } else {
            $("#tablaEmpleados_contenedor").addClass("d-none");
            if (contribucion == 1) {
                $("#tablaEmpleados_contenedor").remove("d-none");
                altadireccionSelect.classList.remove("d-none");
                areaSelect.classList.remove("d-none");
            }
            if (contribucion == 2) {
                $("#tablaEmpleados_contenedor").remove("d-none");
                altadireccionSelect.classList.add("d-none");
                areaSelect.classList.add("d-none");
            }
        }
    }
</script>