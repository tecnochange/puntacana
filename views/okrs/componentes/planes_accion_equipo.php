<?php
//$okrs_planes_accion = $ClassOkrsServicios->okrs_area_planes_accion($user_log["id_empresa"], $user_log["id"], $_SESSION["anio_fill"]);

$resultado_general = 25;
?>

<h1>Planes acción equipos</h1>

<!-- CONSOLIDADO -->
<?php include("views/okrs/componentes/consolidado_planes_accion.php"); ?>



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
                    <?php foreach ($okrs_planes as $plan_de_accion): ?>
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
                            <td class="text-center"><?= $plan_de_accion["prioridad"]; ?></td>
                            <td class="text-center"><?= $plan_de_accion["fecha_inicia"]; ?></td>
                            <td class="text-center"><?= $plan_de_accion["fecha_entrega"]; ?></td>
                            <td class="text-center"><?= $plan_de_accion["estado_backlog"]; ?></td>
                            <td class="text-center"><?= $plan_de_accion["meta"]; ?></td>
                            <td class="text-center">
                                <input type="text" class="form-control form-control-sm decimales" onkeyup="okrsClass.EditarSeguimiento('Okrs_Actividades',<?= $plan_de_accion['id'] ?>,this.value)" value="<?= $plan_de_accion["progreso"]; ?>">
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
                                        <a class=" dropdown-item">Duplicar Planes de Acción</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item">Comentarios</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item">Documentos</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item">Eliminar Planes de Acción</a>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
</div>

