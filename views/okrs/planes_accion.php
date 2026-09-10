<script>
    $(document).ready(function() {
        $('#menuOkrs').collapse();
        $('#bt_okrs_planes_accion').addClass('active');
    });
</script>

<?php
include("app/models/okrs/OkrsServicios.php");
$ClassOkrsServicios = new OkrsServicios();
$okrs_planes_accion = $ClassOkrsServicios->okrs_area_planes_accion($user_log["id_empresa"], $user_log["id"], $_SESSION["anio_fill"]);

//CONSOLIDADO
$datos_consolidado_planes = $ClassOkrsServicios->datos_consolidado_planes_accion($user_log["id_empresa"], $okrs_planes_accion);

//MODALES PARA RESPONSABLES
$modales_responsables = [];

$okrs_objetivos_asociados = $ClassOkrsServicios->objetivos_asociados($user_log["id_empresa"], $user_log["id"], $user_log["id_area"], $_SESSION["anio_fill"]);
$okrs_planes = $ClassOkrsServicios->planes_accion_responsable($user_log["id_empresa"], $user_log["id"], $okrs_objetivos_asociados);
?>

<div class="container-fluid pb-4" style="max-width: 90%; margin: 0 auto;">

    <!-- TITULO -->
    <div class="card mb-3">
        <div class="card-header">
            <h3>Gestionar Mis Planes de Acción</h3>
        </div>
        <div class="card-body">
            <div>
                Aquí podrá gestionar los objetivos, resultados, iniciativas y planes de acción de su área o equipo, recuerde que puede utilizar los filtros para acceder de forma rápida a la información requerida.
            </div>
        </div>
    </div>

    <!-- FILTROS -->
    <?php include("views/okrs/componentes/filtros.php"); ?>

    <?php if (count($okrs_planes_accion) == 0): ?>
        <div class="card">
            <div class="card-body text-center">
                <h5 class="text-secondary">Actualmente no hay Planes de Acción asociados</h5>
            </div>
        </div>
    <?php exit;
    endif; ?>

    <!-- AVANCE GENERAL -->
    <div class="card mb-3">

        <div class="card-body">

            <h5>AVANCE GENERAL DE MIS PLANES DE ACCIÓN</h5>
            <div class="progress mb-4" style="height: auto;">
                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $datos_consolidado_planes["promedio_general"]; ?>%; background-color: <?php echo $datos_consolidado_planes["color_general"]; ?> !important;" aria-valuenow="<?php echo $resultado_general; ?>" aria-valuemin="0" aria-valuemax="100">
                    <div style="font-size: 40px"><b><?php echo number_format($datos_consolidado_planes["promedio_general"], 0); ?>%</b></div>
                </div>
            </div>

            <!-- CONSOLIDADO -->
            <?php include("views/okrs/componentes/consolidado_planes_accion.php"); ?>

        </div>
    </div>

    <!-- AVANCE GENERAL -->
    <div class="card mb-3">

        <div class="card-body">

            <!-- PESTAÑAS -->
            <ul class="nav nav-tabs text-center" style="width:100%;" id="okrsTabs" role="tablist">
                <li class="nav-item" style="width:33%;" role="presentation">
                    <button class="nav-link active w-100" id="tab-listado" data-bs-toggle="tab" data-bs-target="#content-listado" type="button" role="tab">
                        Listado
                    </button>
                </li>

                <li class="nav-item" style="width:33%;" role="presentation">
                    <button class="nav-link w-100" id="tab-kanban" data-bs-toggle="tab" data-bs-target="#content-kanban" type="button" role="tab">
                        Tablero Kanban
                    </button>
                </li>

                <li class="nav-item" style="width:33%;" role="presentation">
                    <button class="nav-link w-100" id="tab-timeline" data-bs-toggle="tab" data-bs-target="#content-timeline" type="button" role="tab">
                        Time line
                    </button>
                </li>
            </ul>

            <div class="tab-content mt-3" id="okrsTabsContent">

                <!-- LISTADO -->
                <div class="tab-pane fade show active" id="content-listado" role="tabpanel" aria-labelledby="tab-listado">
                    <div class="text-end mt-3 mb-3">
                        <a href="?pg=okrs/okr/planes_accion_crear" class="btn btn-primary">+ Crear Plan de Acción</a>
                    </div>

                    <div class="alert alert-warning" role="alert">
                        <h3>¡Atención! Formato numérico en el sistema</h3>

                        Digita los números sin puntos ni comas como separadores de miles. El sistema aplicará el formato correcto (americano o europeo) según tu configuración.<br><br>

                        Ejemplos:<br>
                        escribe 1500000.25 y se mostrará como 1,500,000.25 o 1.500.000,25<br><br>
                        <b>Evita errores comunes:</b><br>
                        ✖ No uses puntos para separar miles manualmente<br>
                        ✖ No mezcles símbolos como en 1.000,45 o 1,000,45<br>
                        ✔ Escribe los números de forma continua y deja que el sistema los formatee<br>
                    </div>

                    <div class="table-responsive mt-4" style="min-height: 400px !important;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Iniciativa</th>
                                    <th class="text-center">Ciclo</th>
                                    <th class="text-center">Publicado por</th>
                                    <th>Planes de acción</th>
                                    <th class="text-center">Prioridad</th>
                                    <th class="text-center">Fecha Inicio</th>
                                    <th class="text-center">Fecha Entrega</th>
                                    <th class="text-center">Estado Backlog</th>
                                    <th class="text-center">Meta</th>
                                    <th class="text-center">Seguimiento</th>
                                    <th class="text-center">Progreso</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($okrs_planes_accion as $plan_de_accion): ?>
                                    <tr>
                                        <td><?= $plan_de_accion["descripcion_iniciativa"]; ?></td>
                                        <td class="text-center"><?= $plan_de_accion["ciclo"]; ?></td>
                                        <td class="text-center">
                                            <?php
                                            $responsableData = $ClassOkrsServicios->Empleado($plan_de_accion["publicado_por"]["id"]); //RESPONSABLE DEL RESULTADO CLAVE
                                            $responsable_foto = !empty($responsableData["foto"]) ? $responsableData["foto"] : "img_default.jpg"; //FOTO DEL RESPONSABLE
                                            $modalId = "modal_responsable_" . $responsableData["id"];
                                            ?>
                                            <img src="<?= $recursos_local . $responsable_foto; ?>"
                                                class="foto_miniaturas mb-1"
                                                title="<?= $responsableData["nombre"]; ?>"
                                                style="cursor:pointer;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#<?= $modalId ?>">
                                            <?php $modales_responsables[$responsableData["id"]] = $responsableData; ?>
                                        </td>
                                        <td><?= $plan_de_accion["descripcion"]; ?></td>
                                        <td class="text-center">
                                            <div class="text-center">
                                                <span class="prioridad-texto" onclick="cambiarPrioridad(<?= $plan_de_accion['id']; ?>)">
                                                    <?= $plan_de_accion["prioridad_txt"]; ?>
                                                </span> <br>
                                                <select id="select_prioridad_<?= $plan_de_accion['id']; ?>" class="form-select form-select-sm d-none" aria-label="Default select example">
                                                    <option selected>...</option>
                                                    <option value="1">Bajo</option>
                                                    <option value="2">Medio</option>
                                                    <option value="3">Alto</option>
                                                    <option value="4">Urgente</option>
                                                </select>
                                            </div>
                                        </td>
                                        <td class="text-center"><?= $plan_de_accion["fecha_inicia"]; ?></td>
                                        <td class="text-center"><?= $plan_de_accion["fecha_entrega"]; ?></td>
                                        <td class="text-center">
                                            <div class="text-center">
                                                <span class="backlog-texto" onclick="cambiarBacklog(<?= $plan_de_accion['id']; ?>)"><?= $plan_de_accion["estado_backlog_txt"]; ?></span> <br>
                                                <select id="select_backlog_<?= $plan_de_accion['id']; ?>" class="form-select form-select-sm d-none" aria-label="Default select example">
                                                    <option selected>...</option>
                                                    <option value="1">Planificado</option>
                                                    <option value="2">En Progreso</option>
                                                    <option value="3">En Revisión</option>
                                                    <option value="4">Completado</option>
                                                </select>
                                            </div>
                                        </td>
                                        <td class="text-center"><?= $plan_de_accion["meta"]; ?></td>
                                        <td class="text-center">
                                            <input
                                                type="text"
                                                class="form-control form-control-sm decimales"
                                                style="max-width: 70%; display: inline-block;"
                                                onkeyup="okrsClass.EditarSeguimiento('Okrs_Actividades',<?= $plan_de_accion['id'] ?>,this.value)" value="<?= $plan_de_accion["progreso"]; ?>">
                                            <a href="" class="btn btn-sm btn-warning float-end"><i class="bi bi-arrow-clockwise" title="Actualizar"></i></a>
                                        </td>
                                        <td class="text-center">
                                            <div class="progress">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= empty($plan_de_accion["porcentaje_avance"]) || (float)$plan_de_accion["porcentaje_avance"] == 0 ? '100' : $plan_de_accion["porcentaje_avance"]; ?>%; background-color:<?= $plan_de_accion["bg_color"] ?> !important; " aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <b><?= number_format($plan_de_accion["porcentaje_avance"], 0) ?>%</b>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="bx bx-dots-horizontal-rounded"></i>
                                            </button>
                                            <ul class="dropdown-menu" id="menu_acciones">
                                                <li>
                                                    <a href="?pg=okrs/okr/planes_accion&id_plan_accion=<?= $plan_de_accion["id"]; ?>" class="dropdown-item">Editar Planes de Acción</a>
                                                </li>
                                                <li>
                                                    <?php
                                                    $queryComentarios = mysqli_query($connect_okrs, "SELECT * FROM Comentarios_Plan_Accion WHERE id_plan = '".$plan_de_accion["id"]."' ");
                                                    $totalComentarios = mysqli_num_rows($queryComentarios);
                                                    ?>
                                                    <a href="#"
                                                        class="dropdown-item btn-comentarios"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modal_comentarios_plan_accion"
                                                        data-id_empresa="<?= $user_log["id_empresa"]; ?>"
                                                        data-id_okr="<?= $okrs["id_okrs"]; ?>"
                                                        data-id_resultado="<?= $resultado["id"]; ?>"
                                                        data-id_empleado="<?= $user_log["id"]; ?>"
                                                        data-descripcion="<?= $iniciativa["descripcion"]; ?>"
                                                        data-id_plan_accion="<?= $plan_de_accion["id"]; ?>">
                                                        Comentarios <?= $totalComentarios ?? 0;?>
                                                    </a>
                                                </li>
                                                <li>
                                                    <?php
                                                    $queryDocumentos = mysqli_query($connect_okrs, "SELECT * FROM Documentos_Plan_Accion WHERE id_plan = '".$plan_de_accion["id"]."' ");
                                                    $totalDocumentos = mysqli_num_rows($queryDocumentos);
                                                    ?>
                                                    <a href="#"
                                                        class="dropdown-item btn-documentos"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modal_documentos_plan_accion"
                                                        data-id_empresa="<?= $user_log["id_empresa"]; ?>"
                                                        data-id_empleado="<?= $user_log["id"]; ?>"
                                                        data-id_plan_accion="<?= $plan_de_accion["id"]; ?>"
                                                        data-plan_descripcion="<?= $plan_de_accion["descripcion"]; ?>">
                                                        Documentos <?= $totalDocumentos ?? 0;?>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class=" dropdown-item text-secondary">Duplicar Planes de Acción</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-secondary">Eliminar Planes de Acción</a>
                                                </li>

                                            </ul>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                </div>

                <!-- INICIATIVAS -->
                <div class="tab-pane fade" id="content-kanban" role="tabpanel" aria-labelledby="tab-kanban">
                    <?php include("views/okrs/componentes/kanban.php"); ?>
                </div>

                <!-- PLANES DE ACCIÓN -->
                <div class="tab-pane fade" id="content-timeline" role="tabpanel" aria-labelledby="tab-timeline">
                    <?php include("views/okrs/componentes/timeline.php"); ?>
                </div>

            </div>



        </div>
    </div>
</div>

<!-- MODALES PARA CADA RESPONSABLE -->
<?php foreach ($modales_responsables as $responsableData): ?>
    <?php include "componentes/modal_responsable.php"; ?>
<?php endforeach; ?>
<?php include("app/models/okrs/OkrsScripts.php"); ?>
<?php include("views/okrs/okr/js/comentarios_plan_accion.php"); ?>
<?php include("views/okrs/okr/js/documentos_plan_accion.php"); ?>
<script>
    // Instancia global accesible por los inputs
    const okrsClass = new OkrsScripts();
</script>

<script>
    const cambiarPrioridad = (id) => {

        const select = $("#select_prioridad_" + id);
        const container = select.closest("div");
        const texto = container.find(".prioridad-texto");

        // Mostrar select
        select.removeClass("d-none");

        // Evitar múltiples eventos
        select.off("change");

        select.on("change", function() {
            const prioridad = $(this).val();

            const prioridades = {
                1: '<span class="btn btn-sm btn-info bg-white">Bajo</span>',
                2: '<span class="btn btn-sm btn-success bg-success">Medio</span>',
                3: '<span class="btn btn-sm btn-warning">Alto</span>',
                4: '<span class="btn btn-sm btn-danger">Urgente</span>'
            };

            // 👇 AQUÍ EL CAMBIO
            texto.html(prioridades[prioridad]);

            $(this).addClass("d-none");

            editarPrioridad(id, prioridad);
        });
    };
    const editarPrioridad = (id, prioridad) => {
        $.ajax({
            url: "api/okrs/editar_prioridad.php",
            method: "POST",
            data: {
                id: id,
                prioridad: prioridad
            },
            success: function(res) {
                window.location.reload();
                //console.log(res);
            },
            error: function() {
                alert("Error al actualizar prioridad");
            }
        });
    };

    const cambiarBacklog = (id) => {

        const select = $("#select_backlog_" + id);
        const container = select.closest("div");
        const texto = container.find(".backlog-texto");

        // Mostrar select
        select.removeClass("d-none");

        // Evitar múltiples eventos
        select.off("change");

        select.on("change", function() {
            const backlog = $(this).val();

            const backlogs = {
                1: '<span class="btn btn-sm btn-info bg-white">Planificado</span>',
                2: '<span class="btn btn-sm btn-success bg-success">En Progreso</span>',
                3: '<span class="btn btn-sm btn-warning">En Revisión</span>',
                4: '<span class="btn btn-sm btn-success bg-success">Completado</span>'
            };

            // 👇 AQUÍ EL CAMBIO
            texto.html(backlogs[backlog]);

            $(this).addClass("d-none");

            editarBacklog(id, backlog);
        });
    };
    const editarBacklog = (id, backlog) => {
        const select = $("#select_backlog_" + id);
        const container = select.closest("div");
        const texto = container.find(".backlog-texto");
        const backlogs = {
            1: '<span class="btn btn-sm btn-info bg-white">Planificado</span>',
            2: '<span class="btn btn-sm btn-success">En Progreso</span>',
            3: '<span class="btn btn-sm btn-warning">En Revisión</span>',
            4: '<span class="btn btn-sm btn-success bg-success">Completado</span>'
        };
        texto.html(backlogs[backlog]);

        jQuery.ajax({
            url: "api/okrs/editar_estado_backlog.php",
            type: 'post',
            data: {
                id_plan_accion: id,
                estado: backlog
            },
            success: function(res) {
                window.location.reload();
            }
        });
    };
</script>