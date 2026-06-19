<div class="row">

    <div class="col-md-12 pt-2" style="background-color: #007bff !important; border-radius:5px;">
        <h6 class="text-center">

            <div class="row">
                <div class="col-md-4">
                    <!-- TIMELINE -->
                    <a class="btn btn-sm btn-warning float-start mx-1" data-bs-toggle="modal" data-bs-target="#timelineModal_<?= $iniciativa['id'] ?>">Time Line</a>
                    <!-- Modal -->
                    <div class="modal fade" id="timelineModal_<?= $iniciativa['id'] ?>" tabindex="-1" aria-labelledby="timelineModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <?php include "timeline.php"; ?>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- KANBAN -->
                    <a class="btn btn-sm btn-warning float-start mx-1" data-bs-toggle="modal" data-bs-target="#kambasModal_<?= $iniciativa['id'] ?>">Tablero Kanban</a>
                    <!-- Modal -->
                    <div class="modal fade" id="kambasModal_<?= $iniciativa['id'] ?>" tabindex="-1" aria-labelledby="kambasModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <?php include "kanban.php"; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 pt-1">
                    <span class="text-light fs-5" style="font-weight: bold;">Planes de Acción</span>
                </div>
                <div class="col-md-4">
                    <!-- CREAR -->
            <a href="?pg=okrs/okr/planes_accion&id_okrs=<?= $okrs["id_okrs"]; ?>&id_resultado=<?= $resultado["id"]; ?>&id_iniciativa=<?= $iniciativa["id"]; ?>" class="btn btn-sm btn-warning float-end mx-1">Agregar</a>
                </div>
            </div>
        </h6>
    </div>

    <div class="col-md-12 col-lg-12">
        <div class="table-responsive mt-4">
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
                    <?php foreach ($planes_accion as $plan_de_accion): ?>
                        <tr>
                            <td><?= $plan_de_accion["descripcion_iniciativa"]; ?></td>
                            <td class="text-center"><?= $plan_de_accion["ciclo"]; ?></td>
                            <td class="text-center">
                                <?php
                                $responsableData = $ClassOkrsServicios->Empleado($plan_de_accion["publicado_por"]["id"]); //RESPONSABLE DEL RESULTADO CLAVE
                                $responsable_foto = !empty($responsableData["foto"]) ? $responsableData["foto"] : "img_default.jpg"; //FOTO DEL RESPONSABLE
                                $modalId = "modal_responsable_" . $responsableData["id"];
                                ?>
                                <img src="https://goforagile.com/recursos/<?= $responsable_foto; ?>"
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
                                    <span class="prioridad-texto" onclick="cambiarPrioridad(<?= $plan_de_accion['id']; ?>)"><?= $plan_de_accion["prioridad_txt"]; ?></span> <br>
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
                                    style="max-width: 65%; display: inline-block;"
                                    onkeyup="okrsClass.EditarSeguimiento('Okrs_Actividades',<?= $plan_de_accion['id'] ?>,this.value)" value="<?= $plan_de_accion["progreso"]; ?>">
                                <a href="" class="btn btn-sm btn-warning float-end"><i class="bi bi-arrow-clockwise" title="Actualizar"></i></a>
                            </td>
                            <td class="text-center">
                                <div class="progress">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= empty($plan_de_accion["porcentaje_avance"]) || (float)$plan_de_accion["porcentaje_avance"] == 0 ? '100' : $plan_de_accion["porcentaje_avance"]; ?>%; background-color: <?= $plan_de_accion["bg_color"]; ?> !important;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                        <b><?= number_format($plan_de_accion["porcentaje_avance"], 0); ?>%</b>
                                    </div>
                                </div>
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
                                            data-plan_descripcion="<?= $plan_de_accion["descrdescripcion_iniciativaipcion"]; ?>">
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

</div>