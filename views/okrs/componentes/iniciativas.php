<div class="row">

    <div class="col-md-12">
        <h5 class="text-center">Despliegue de Iniciativas</h5>
    </div>

    <div class="col-md-12">
        <div class="table-responsive mt-4">
            <table class="table">
                <thead>
                    <tr>
                        <th>Mes</th>
                        <th>Iniciativas</th>
                        <th class="text-center">Responsables</th>
                        <th class="text-center">Entrega</th>
                        <th class="text-center">Meta</th>
                        <th class="text-center">Seguimiento</th>
                        <th>% Iniciativas</th>
                        <th>%</th>
                        <th class="text-center">Acciones</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($iniciativas as $iniciativa): ?>

                        <tr>
                            <td><?= $ClassOkrsServicios->nombreMes($iniciativa["mes"]); ?></td>

                            <td><?= $iniciativa["descripcion"]; ?></td>

                            <!-- ========== RESPONSABLES ========== -->
                            <td class="text-center" style="width: 150px;">

                                <?php
                                $resp_iniciativa = explode(',', $iniciativa["responsables"]);
                                $totalRespIni = count($resp_iniciativa);

                                for ($i = 0; $i < min(2, $totalRespIni); $i++):
                                    $respDataIni = $ClassOkrsServicios->Empleado($resp_iniciativa[$i]);
                                    $fotoRespIni = !empty($respDataIni["foto"]) ? $respDataIni["foto"] : "img_default.jpg";
                                    $modalId = "modal_responsable_" . $respDataIni["id"];
                                ?>
                                    <img src="https://goforagile.com/recursos/<?= $fotoRespIni; ?>"
                                        width="32" height="32"
                                        class="rounded-circle mb-1"
                                        title="<?= $respDataIni["nombre"]; ?>"
                                        style="cursor:pointer;"
                                        data-bs-toggle="modal"
                                        data-bs-target="#<?= $modalId ?>">
                                    <?php $modales_responsables[$respDataIni["id"]] = $respDataIni; ?>
                                <?php endfor; ?>

                                <?php if ($totalRespIni > 2): ?>
                                    <span class="badge rounded-circle bg-dark d-inline-flex justify-content-center align-items-center"
                                        style="cursor:pointer; width:32px; height:32px; font-size:13px;"
                                        data-bs-toggle="modal" data-bs-target="#modal_resp_ini_<?= $iniciativa['id'] ?>">
                                        +<?= $totalRespIni - 2 ?>
                                    </span>
                                <?php endif; ?>

                            </td>

                            <td class="text-center"><?= $iniciativa["fecha_entrega"]; ?></td>
                            <td class="text-center"><?= $iniciativa["meta"]; ?></td>

                            <td class="text-center">
                                <input
                                    type="text"
                                    class="form-control form-control-sm decimales"
                                    style="max-width: 80%; display: inline-block;"
                                    onkeyup="okrsClass.EditarSeguimiento('Okrs_Iniciativas',<?= $iniciativa['id'] ?>,this.value)" value="<?= $iniciativa["avance"]; ?>"
                                >
                                <a href="" class="btn btn-sm btn-warning float-end"><i class="bi bi-arrow-clockwise" title="Actualizar"></i></a>
                            </td>

                            <td class="text-center">
                                <div class="progress">
                                    <?php
                                    $progress = $iniciativa["porcentaje_avance"];
                                    $exceso = 0;
                                    $total_progress = $progress;

                                    if ($progress > 100) {
                                        $total_progress = 100;
                                        $exceso = $progress - 100;
                                    }
                                    ?>
                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= empty($total_progress) || (float)$total_progress == 0 ? '100' : $total_progress; ?>%; background-color: <?= $iniciativa["color"]; ?> !important;" aria-valuenow="<?= $total_progress; ?>" aria-valuemin="0" aria-valuemax="100">
                                        <b><?= number_format($total_progress, 0); ?>%</b>
                                    </div>
                                </div>

                                <?php if ($exceso > 0): ?>
                                    <div>
                                        <span style="background-color: #0dcaf0; padding:.5px 2px; border-radius: 5px; font-size:0.7rem;">
                                            +<?= abs($exceso); ?>%
                                        </span>
                                        <?php $tooltipHTML =
                                            "<b>Felicidades has alcanzado un resultado extraordinario</b> <br><br>
                                            Tu avance ha superado el 100%, lo cual representa un sobrecumplimiento sobresaliente dentro de la metodología OKR. <br><br>
                                            Este logro será visibilizado y resaltado gráficamente como un valor adicional (ej. +20%,), destacando tu esfuerzo más allá del objetivo. <br><br>
                                            <i>Importante:</i> Para fines de cálculo y ponderación general, el sistema considerará 100% como el valor máximo al promediar con los demás resultados clave del objetivo. <br><br>
                                            ¡Gracias por impulsar el rendimiento estratégico!";
                                        ?>
                                        <i
                                            class="bx bx-info-circle"
                                            style="font-size: 1.2em; position: relative; top:2px;"
                                            data-bs-toggle="tooltip" data-bs-html="true" data-bs-title="<?= $tooltipHTML; ?>"></i>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <td></td>

                            <!-- ======== ACCIONES ========= -->
                            <td class="text-center">
                                <button class="btn btn-sm" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-horizontal-rounded"></i>
                                </button>
                                <ul class="dropdown-menu" id="menu_acciones">
                                    <li><a href="?pg=okrs/okr/iniciativas&id_okr=<?= $iniciativa["id_okrs"]; ?>&id_resultado=<?= $iniciativa["id_resultado"]; ?>&id_iniciativa=<?= $iniciativa["id"]; ?>" class="dropdown-item">Editar Iniciativa</a></li>
                                    <li>
                                        <?php
                                        $queryComentarios = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Comentarios_Iniciativas WHERE id_iniciativa = '".$iniciativa["id"]."' ");
                                        $totalComentarios = mysqli_num_rows($queryComentarios);
                                        ?>
                                        <a href="#"
                                            class="dropdown-item btn-comentarios"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modal_comentarios_iniciativas"
                                            data-id_okr="<?= $okrs["id_okrs"]; ?>"
                                            data-id_resultado="<?= $iniciativa["id_resultado"]; ?>"
                                            data-id_empleado="<?= $user_log["id"]; ?>"
                                            data-id_iniciativa="<?= $iniciativa["id"]; ?>"
                                            data-descripcion="<?= $iniciativa["descripcion"]; ?>">
                                            Comentarios <?= $totalComentarios ?? 0;?>
                                        </a>
                                    </li>
                                    <li>
                                        <?php
                                        $queryDocumentos = mysqli_query($connect_okrs, 'SELECT * FROM Okrs_Documentos WHERE id_iniciativa = "'.$iniciativa['id'].'" ');
                                        $totalDocumentos = mysqli_num_rows($queryDocumentos);
                                        ?>
                                        <a target="_blank" href="?pg=okrs/gestionar_iniciativa&id_okrs=<?= $iniciativa["id_okrs"]; ?>&id_resultado=<?= $iniciativa["id_resultado"]; ?>&id_iniciativa=<?= $iniciativa["id"]; ?>" class="dropdown-item">Documentos <?= $totalDocumentos ?? 0; ?></a>
                                    </li>
                                    <li><a href="?pg=okrs/okr/planes_accion&id_okrs=<?= $iniciativa["id_okrs"]; ?>&id_resultado=<?= $iniciativa["id_resultado"]; ?>&id_iniciativa=<?= $iniciativa["id"]; ?>" class="dropdown-item">Planes de Acción</a></li>
                                    <li><a target="_blank" href="?pg=okrs/gestionar_iniciativa&id_okrs=<?= $iniciativa["id_okrs"]; ?>&id_resultado=<?= $iniciativa["id_resultado"]; ?>&id_iniciativa=<?= $iniciativa["id"]; ?>" class="dropdown-item">Gestionar Iniciativas</a></li>
                                    <li><a class="dropdown-item " onclick="EliminarIniciativa(<?= $iniciativa['id']; ?>)">Eliminar Iniciativas</a></li>
                                </ul>
                            </td>
                            <td class="text-center">
                                <!-- Flecha -->
                                <?php $planes_accion = $ClassOkrsServicios->okrs_obtener_plan_accion_iniciativa($iniciativa['id']); ?>
                                <?php if (count($planes_accion) > 0): ?>
                                    <button class="btn btn-sm p-0 me-1"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#accordion_ini_<?= $iniciativa['id'] ?>">
                                        <i class="bx bx-chevron-down fs-3"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>

                        <!-- ================= PLANES DE ACCION ================= -->
                        <tr class="p-0">
                            <td colspan="9" class="p-0 border-0">
                                <div id="accordion_ini_<?= $iniciativa['id'] ?>" class="collapse">
                                    <div class="p-3 bg-light shadow-sm">
                                        <?php if (count($planes_accion) > 0): ?>
                                            <?php include("planes_accion.php"); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>