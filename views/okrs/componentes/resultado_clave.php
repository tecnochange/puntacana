<?php
$OkrsServicios = new OkrsServicios();
$iniciativas = $OkrsServicios->okrs_iniciativas_resultados($resultado["id_okrs"], $resultado["id"]);

$progress_value = empty($resultado["porcentaje_avance"]) || (float)$resultado["porcentaje_avance"] == 0 ? '100' : $resultado["porcentaje_avance"];
$progress = '
        <div class="progress">
            <div class="progress-bar bg-success" role="progressbar" style="width: ' . $progress_value . '%; background-color: ' . $resultado["color"] . ' !important;" aria-valuenow="' . $resultado["porcentaje_avance"] . '" aria-valuemin="0" aria-valuemax="100">
                 
            </div>
        </div>
        <b>' . number_format($resultado["porcentaje_avance"], 0) . '%</b>
    ';
?>

<style>
    .pointer-off {
        pointer-events: none;
        /* evita que el contenedor dispare el collapse */
    }

    .pointer-off * {
        pointer-events: auto;
        /* pero permite que los elementos internos funcionen */
    }

    .accordion-button::after {
        display: none !important;
    }

    #menu_acciones {
        margin: -15px !important;
        margin-right: 5px !important;
    }
</style>
<div class="accordion-item mb-2" style="border:0 !important;">
    <div class="card py-2">
        <div class="accordion-header d-flex align-items-center" id="header_<?php echo $resultado["id"] ?>">
            <div class="accordion-button collapsed pointer-off w-100">

                <div class="row w-100">
                    <div class="col-md-1 text-center">
                        <?php echo $resultado["periodo"]; ?>
                    </div>
                    <div class="col-md-4">
                        <b><?php echo $resultado["descripcion"]; ?></b>
                    </div>
                    <div class="col-md-2 text-center">
                        <?php
                        $responsables = explode(',', $resultado["responsables"]);
                        $totalResponsables = count($responsables);

                        // Mostrar solo los dos primeros responsables
                        for ($i = 0; $i < min(2, $totalResponsables); $i++):
                            $id_responsable = $responsables[$i];
                            $responsableData = $OkrsServicios->Empleado($id_responsable);
                            $foto_responsable = !empty($responsableData["foto"]) ? $responsableData["foto"] : "img_default.jpg";
                            $modalId = "modal_responsable_" . $responsableData["id"];
                        ?>
                            <img src="https://goforagile.com/recursos/<?= $foto_responsable; ?>"
                                class="foto_miniaturas mb-1"
                                title="<?= $responsableData["nombre"]; ?>"
                                style="cursor:pointer; width:32px !important; height:32px !important;"
                                data-bs-toggle="modal"
                                data-bs-target="#<?= $modalId ?>">
                            <?php $modales_responsables[$responsableData["id"]] = $responsableData; ?>
                        <?php endfor; ?>

                        <?php if ($totalResponsables > 2): ?>
                            <!-- Círculo +X -->
                            <span class="badge rounded-circle bg-dark d-inline-flex justify-content-center align-items-center" style="cursor:pointer; width:35px; height:35px; font-size:14px;" data-bs-toggle="modal" data-bs-target="#modal_responsables_<?= $resultado['id'] ?>">
                                +<?= $totalResponsables - 2 ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-1">
                        <b>Meta</b><br>
                        <?php echo $resultado["meta"]; ?>
                    </div>
                    <div class="col-md-2">
                        <b>Seguimiento</b><br>
                        <input type="text" class="form-control form-control-sm decimales" style="max-width: 80%; display: inline-block;" onkeyup="okrsClass.EditarSeguimiento('Okrs_Resultados',<?= $resultado['id'] ?>,this.value)" value="<?php echo $resultado["avance"]; ?>">
                        <a href="" class="btn btn-sm btn-warning float-end"><i class="bi bi-arrow-clockwise" title="Actualizar"></i></a>
                    </div>
                    <div class="col-md-1 text-center">
                        <b>Progreso</b><br>
                        <?php echo $progress; ?>
                    </div>
                    <div class="col-md-1 text-center" style="font-size: 0.8em;">

                        <?php if (count($iniciativas) > 0): ?>
                            Iniciativas
                            <sup>
                                <span style="
                                display:inline-flex;
                                align-items:center;
                                justify-content:center;
                                background-color:#26b99a;
                                color:#fff;
                                border-radius:50%;
                                font-size:1.2em;
                                width:22px;
                                height:22px;
                                line-height:22px;
                                text-align:center;
                            ">
                                    <b><?= count($iniciativas); ?></b>
                                </span>
                            </sup>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <button class="btn btn-sm ms-2 toggle-arrow fs-4" data-bs-toggle="collapse" data-bs-target="#target_<?php echo $resultado["id"] ?>" aria-expanded="false" aria-controls="target_<?php echo $resultado["id"] ?>">
                <i class="bx bx-chevron-down"></i>
            </button>

        </div>

        <!-- MODAL RESPONSABLES DE RESULTADOS -->
        <?php if ($totalResponsables > 2): ?>
            <div class="modal fade" id="modal_responsables_<?= $resultado['id'] ?>" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">Responsables</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">

                            <?php for ($j = 0; $j < $totalResponsables; $j++): ?>
                                <?php
                                $respData = $OkrsServicios->Empleado($responsables[$j]);
                                $fotoResp = !empty($respData["foto"]) ? $respData["foto"] : "img_default.jpg";
                                ?>
                                <div class="d-flex align-items-center mb-2">
                                    <img src="https://goforagile.com/recursos/<?= $fotoResp ?>"
                                        class="rounded-circle me-2"
                                        width="35" height="35">
                                    <span><?= $respData["nombre"] ?></span>
                                </div>
                            <?php endfor; ?>

                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>

                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div id="target_<?php echo $resultado["id"] ?>" class="accordion-collapse collapse" aria-labelledby="header_<?php echo $resultado["id"] ?>" data-bs-parent="#desplegable_<?php echo $okrs["id_okrs"]; ?>">

            <div class="accordion-body">

                <div class="row">
                    <div class="col-md-3">
                        <label>Responsable</label>
                        <?= $responsableData["nombre"]; ?>
                    </div>
                    <div class="col-md-3">
                        <label>Inicia</label>
                        <?php echo $resultado["fecha_inicia"]; ?>
                    </div>
                    <div class="col-md-3">
                        <label>Entrega</label>
                        <?php echo $resultado["fecha_entrega"]; ?>
                    </div>                
                    <div class="col-md-3 text-end">
                        <!-- ACCIONES PARA EL RESULTADO -->
                        <div class="btn-group dropstart">
                            <button class="btn btn-sm dropup" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bx bx-dots-horizontal-rounded"></i>
                            </button>
                            <ul class="dropdown-menu pb-2" id="menu_acciones">
                                <li>
                                    <a href="?pg=okrs/okr/iniciativas&id_okr=<?= $okrs["id_okrs"]; ?>&id_resultado=<?= $resultado["id"]; ?>" class="dropdown-item">Agregar Iniciativa</a>
                                </li>
                                <li>
                                    <?php
                                        $queryComentarios = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Comentarios WHERE id_resultado = '".$resultado["id"]."' ");
                                        $totalComentarios = mysqli_num_rows($queryComentarios);
                                    ?>
                                    <a href="#"
                                        class="dropdown-item btn-comentarios"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modal_comentarios"
                                        data-id_okr="<?= $okrs["id_okrs"]; ?>"
                                        data-id_resultado="<?= $resultado["id"]; ?>"
                                        data-id_empleado="<?= $user_log["id"]; ?>"
                                        data-okr_descripcion="<?= $resultado["descripcion"]; ?>">
                                        Comentarios <?= $totalComentarios ?? 0;?>
                                    </a>
                                </li>
                                <li>
                                    <?php
                                        $queryDocumentos = mysqli_query($connect_okrs, "SELECT * FROM Documentos_Resultados WHERE id_resultado = '".$resultado["id"]."' ");
                                        $totalDocumentos = mysqli_num_rows($queryDocumentos);
                                    ?>
                                    <a href="#"
                                        class="dropdown-item btn-documentos"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modal_documentos"
                                        data-id_okr="<?= $okrs["id_okrs"]; ?>"
                                        data-id_resultado="<?= $resultado["id"]; ?>"
                                        data-id_empleado="<?= $user_log["id"]; ?>"
                                        data-id_empresa="<?= $user_log["id_empresa"]; ?>"
                                        data-resultado_descripcion="<?= $resultado["descripcion"]; ?>">
                                        Documentos <?= $totalDocumentos ?? 0;?>
                                    </a>
                                </li>
                                <li>
                                    <a href="?pg=okrs/okr/resultados&id=<?= $resultado[" id_okrs"]; ?>&id_resultado=<?= $resultado["id"]; ?>" class="dropdown-item">Editar Resultado Clave</a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-secondary">Mover a otro OKR</a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-secondary">Duplicar</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" onclick="EliminarResultadoClave(<?= $resultado["id"]; ?>)">Eliminar</a>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>

                <div class="card-header text-center text-light" style="background-color: #b0b0b0 !important;">
                    <h5 style="margin-bottom: .1rem;">Despliegue de Iniciativas</h5>
                </div>

                <style>
                    .table {
                        border-color: transparent;
                    }

                    .table>tbody {
                        vertical-align: middle;
                    }
                </style>
                <div class="table-responsive" style="min-height: 250px !important;">
                    <table class="table table-bordered">
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

                        <?php foreach ($iniciativas as $iniciativa): ?>

                            <!-- ===================== FILA PRINCIPAL ===================== -->
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
                                        style="max-width: 75%; display: inline-block;"
                                        onkeyup="okrsClass.EditarSeguimiento('Okrs_Iniciativas',<?= $iniciativa['id']; ?>,this.value)" value="<?= $iniciativa["avance"]; ?>">
                                    <a href="" class="btn btn-sm btn-warning float-end"><i class="bi bi-arrow-clockwise" title="Actualizar"></i></a>
                                </td>

                                <td class="text-center">
                                    <div class="progress">
                                        <?php
                                        $progress_value = empty($iniciativa['porcentaje_avance']) || (float)$iniciativa['porcentaje_avance'] == 0 ? '100' : $iniciativa['porcentaje_avance'];
                                        $progress =  $iniciativa['porcentaje_avance'];
                                        $exceso = 0;
                                        $total_progress = $progress;

                                        if ($progress > 100) {
                                            $total_progress = 100;
                                            $exceso = $progress - 100;
                                        }
                                        ?>
                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= $progress_value; ?>%; background-color:<?= $iniciativa['color']; ?> !important; " aria-valuenow="<?= $total_progress; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <b><?= number_format($total_progress, 0); ?>%</b>

                                    <?php if ($exceso > 0): ?>
                                        <div>
                                            <span style="background-color: #0dcaf0; padding:.5px 2px; border-radius: 5px; font-size:0.7rem;">
                                                +<?= abs($exceso); ?>%
                                            </span>
                                            <?php $tooltipHTML =
                                                "<b>¡Felicidades! has alcanzado un resultado extraordinario</b> <br><br>
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

                                <!-- ======== ACCIONES PARA LA INICIATIVA========= -->
                                <style>
                                    #menu_acciones_iniciativas {
                                        margin: -15px !important;
                                        margin-right: 5px !important;
                                    }
                                </style>
                                <td class="text-center">
                                    <div class="btn-group dropstart">
                                        <button class="btn btn-sm dropup" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-horizontal-rounded"></i>
                                        </button>
                                        <ul class="dropdown-menu" id="menu_acciones_iniciativas">
                                            <li><a href="?pg=okrs/okr/iniciativas&id_okr=<?= $iniciativa["id_okrs"]; ?>&id_resultado=<?= $iniciativa["id_resultado"]; ?>&id_iniciativa=<?= $iniciativa["id"]; ?>" class="dropdown-item">Editar Iniciativa</a></li>
                                            
                                            <li>
                                                <?php
                                                $queryComentarios = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Comentarios_Iniciativas WHERE id_iniciativa = '".$iniciativa["id"]."' ");
                                                $totalComentarios = mysqli_num_rows($queryComentarios);
                                                ?>
                                                <a target="_blank" href="?pg=okrs/gestionar_iniciativa&id_okrs=<?= $okrs["id_okrs"]; ?>&id_resultado=<?= $resultado["id"]; ?>&id_iniciativa=<?= $iniciativa["id"]; ?>&tab=comentarios" class="dropdown-item">Comentarios <?= $totalComentarios ?? 0;?></a>
                                            </li>
                                            <li>
                                                <?php
                                                $queryDocumentos = mysqli_query($connect_okrs, 'SELECT * FROM Okrs_Documentos WHERE id_iniciativa = "'.$iniciativa['id'].'" ');
                                                $totalDocumentos = mysqli_num_rows($queryDocumentos);
                                                ?>
                                                <a target="_blank" href="?pg=okrs/gestionar_iniciativa&id_okrs=<?= $okrs["id_okrs"]; ?>&id_resultado=<?= $resultado["id"]; ?>&id_iniciativa=<?= $iniciativa["id"]; ?>&tab=documentos" class="dropdown-item">Documentos <?= $totalDocumentos ?? 0; ?></a>
                                            </li>
                                            <li><a href="?pg=okrs/okr/planes_accion_crear&id_objetivo=<?= $okrs["id_okrs"]; ?>&id_resultado=<?= $resultado["id"]; ?>&id_iniciativa=<?= $iniciativa["id"]; ?>" class="dropdown-item">Crear Planes de Acción</a></li>
                                            <li><a target="_blank" href="?pg=okrs/gestionar_iniciativa&id_okrs=<?= $okrs["id_okrs"]; ?>&id_resultado=<?= $resultado["id"]; ?>&id_iniciativa=<?= $iniciativa["id"]; ?>" class="dropdown-item">Gestionar Iniciativas</a></li>
                                            <li><a class="dropdown-item text-secondary">Duplicar Iniciativa</a></li>
                                            <li><a class="dropdown-item " onclick="EliminarIniciativa(<?= $iniciativa['id']; ?>)">Eliminar Iniciativas</a></li>
                                        </ul>
                                    </div>
                                </td>
                                <td>
                                    <!-- Flecha -->
                                    <?php $planes_accion = $OkrsServicios->okrs_obtener_plan_accion_iniciativa($iniciativa['id']); ?>
                                    <?php if (count($planes_accion) > 0): ?>
                                        <button class="btn btn-sm p-0 me-1 fs-4"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#accordion_ini_<?= $iniciativa['id'] ?>">
                                            <i class="bi bi-plus-circle-fill text-primary" title="Ver Planes de Acción"></i>
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

                            <!-- ========== MODAL RESPONSABLES ========= -->
                            <?php if ($totalRespIni > 2): ?>
                                <div class="modal fade" id="modal_resp_ini_<?= $iniciativa['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">

                                            <div class="modal-header">
                                                <h5 class="modal-title">Responsables de la Iniciativa</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <?php for ($j = 0; $j < $totalRespIni; $j++):
                                                    $respIniData = $ClassOkrsServicios->Empleado($resp_iniciativa[$j]);
                                                    $fotoIniFull = !empty($respIniData["foto"]) ? $respIniData["foto"] : "img_default.jpg";
                                                ?>
                                                    <div class="d-flex align-items-center mb-2">
                                                        <img src="https://goforagile.com/recursos/<?= $fotoIniFull ?>"
                                                            class="rounded-circle me-2"
                                                            width="35" height="35">
                                                        <span><?= $respIniData["nombre"] ?></span>
                                                    </div>
                                                <?php endfor; ?>
                                            </div>

                                            <div class="modal-footer">
                                                <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>