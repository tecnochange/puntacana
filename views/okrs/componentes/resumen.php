<style>
    .accordion-button .foto_miniaturas {
        width: 60px !important;
        height: 60px !important;
        object-fit: cover;
        border-radius: 50%;
    }

    .foto_miniaturas {
        width: 25px !important;
        height: 25px !important;
        object-fit: cover;
        border-radius: 50%;
    }
</style>

<div class="accordion-item">
    <div class="accordion-header" id="flush-heading_<?php echo $okrs["id_okrs"] ?>">
        <div class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse_<?php echo $okrs["id_okrs"] ?>" aria-expanded="false" aria-controls="flush-collapse_<?php echo $okrs["id_okrs"] ?>">
            <div class="row w-100">

                <!-- Foto del accordion-item -->
                <div class="col-md-1 text-center">
                    <?php $foto = !empty($okrs["empleado"]["foto"]) ? $okrs["empleado"]["foto"] : "img_default.jpg"; ?>
                    <img src="<?= $recursos_local . $foto ?>" class="foto_miniaturas" title="<?= $okrs["empleado"]["nombre"]; ?>">
                </div>

                <div class="col-md-7 d-flex align-items-center">
                    <div>
                        <?php echo $okrs["objetivo"] ?> <br>
                        <b><?php echo $okrs["tipo_okrs"] ?></b>
                    </div>
                    
                </div>

                <!-- Barra de progreso -->
                <div class="col-md-3 pt-4 text-center">
                    <div class="progress">
                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= empty($okrs["porcentaje_avance"]) || (float)$okrs["porcentaje_avance"] == 0 ? '100' : $okrs["porcentaje_avance"]  ?>%; background-color: <?php echo $okrs["color_avance"] ?> !important;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                            
                        </div>
                    </div>
                    <b><?php echo $okrs["porcentaje_avance"] ?>%</b>
                    
                </div>

                <div class="col-md-1 pt-3">
                    <a href="?pg=okrs/okr/detalle&id=<?php echo $okrs["id_okrs"] ?>">
                        <button type="button" class="btn btn-outline-dark btn-sm ">
                            <i class="bx bx-pencil" title="Editar"></i>
                        </button>
                    </a>
                    <a href="?pg=okrs/okr/resultados&id=<?php echo $okrs["id_okrs"] ?>">
                        <button type="button" class="btn btn-outline-dark btn-sm">
                            <i class="bx bx-plus" title="Agregar resultado clave"></i>
                        </button>
                    </a>
                </div>

            </div>
        </div>
    </div>

    <div id="flush-collapse_<?php echo $okrs["id_okrs"] ?>" class="accordion-collapse collapse" aria-labelledby="flush-heading_<?php echo $okrs["id_okrs"] ?>" data-bs-parent="#FichasDesplegablesMaster">

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Periodo</th>
                    <th>Resultado</th>
                    <th>Responsables</th>
                    <th>Fecha Inicia</th>
                    <th>Fecha Entrega</th>
                    <th>Meta</th>
                    <th>Seguimiento</th>
                    <th>Progreso</th>
                    <th>Acciones</th>
                    <th></th>
                </tr>
            </thead>

            <?php
            $okrs_resultados = $okrs['resultados']; //RESULTADOS DEL OKR
            foreach ($okrs_resultados as $resultado): ?>

                <?php
                $responsables = explode(',', $resultado["responsables"]); //REPONSABLES DEL RESULTADO CLAVE
                $progress_value = empty($resultado["porcentaje_avance"]) || (float)$resultado["porcentaje_avance"] == 0 ? '100' : $resultado["porcentaje_avance"];
                $progress = '
                    <div class="progress">
                        <div class="progress-bar bg-success" role="progressbar" style="width: '.$progress_value.'%; background-color: '.$resultado["color"].' !important" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                        <b>'.$resultado["porcentaje_avance"].'%</b>
                        </div>
                    </div>
                ';
                ?>

                <tbody>
                    <tr>
                        <td class="text-center"><?= $resultado["periodo"]; ?></td>
                        <td><?= $resultado["descripcion"]; ?></td>
                        <td class="text-center">
                            <?php
                            $totalResponsables = count($responsables);

                            // Mostrar solo los dos primeros responsables
                            for ($i = 0; $i < min(2, $totalResponsables); $i++):
                                $responsableData = $ClassOkrsServicios->Empleado($responsables[$i]);
                                $responsable_foto = !empty($responsableData["foto"]) ? $responsableData["foto"] : "img_default.jpg";
                                $modalId = "modal_responsable_" . $responsableData["id"];
                            ?>
                                <img src="<?= $recursos_local . $responsable_foto; ?>"
                                    class="foto_miniaturas mb-1"
                                    title="<?= $responsableData["nombre"]; ?>"
                                    style="cursor:pointer;"
                                    data-bs-toggle="modal"
                                    data-bs-target="#<?= $modalId ?>">
                                <!-- Incluye el modal reusable -->
                                <?php include "modal_responsable.php"; ?>
                            <?php endfor; ?>

                            <?php if ($totalResponsables > 2): ?>
                                <!-- Botón +X -->
                                <span class="badge rounded-circle bg-secondary" style="cursor:pointer; width:25px; height:25px; font-size:10px; padding:9px 0px 9px .1px;" data-bs-toggle="modal" data-bs-target="#modal_responsables_<?= $resultado['id'] ?>">
                                    +<?= $totalResponsables - 2 ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center"><?= $resultado["fecha_inicia"]; ?></td>
                        <td class="text-center"><?= $resultado["fecha_entrega"]; ?></td>
                        <td class="text-center"><?= $resultado["meta"]; ?></td>
                        <td class="text-center">
                            <input
                                type="text"
                                class="form-control form-control-sm decimales"
                                style="max-width: 80%; display: inline-block;"
                                onkeyup="okrsClass.EditarSeguimiento('Okrs_Resultados',<?= $resultado['id'] ?>,this.value)" value="<?= $resultado["avance"]; ?>"
                            >
                            <a href="" class="btn btn-sm btn-warning float-end"><i class="bi bi-arrow-clockwise" title="Actualizar"></i></a>
                        </td>
                        <td class="text-center"><?= $progress; ?></td>
                        <td class="text-center">
                            <button class="btn btn-sm" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bx bx-dots-horizontal-rounded"></i>
                            </button>
                            <ul class="dropdown-menu" id="menu_acciones" style="">
                                <li>
                                    <a href="?pg=okrs/okr/resultados&id=<?= $okrs["id_okrs"]; ?>&id_resultado=<?= $resultado["id"]; ?>" class="dropdown-item">Editar Resultado Clave</a>
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
                                    <a class="dropdown-item text-secondary">Mover a otro OKR</a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-secondary">Duplicar</a>
                                </li>
                            </ul>
                        </td>
                        <td class="text-center">
                            <?php $iniciativas = $ClassOkrsServicios->okrs_iniciativas_resultados($okrs["id_okrs"], $resultado["id"]); ?>

                            <?php if(count($iniciativas) > 0): ?>
                                <button class="btn btn-sm p-0 me-1"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#accordion_res_<?= $resultado['id'] ?>">
                                    <i class="bx bx-chevron-down fs-3"></i>
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <!-- ================= INICIATIVAS ================= -->
                    <tr class="p-0">
                        <td colspan="9" class="p-0 border-0">
                            <div id="accordion_res_<?= $resultado['id'] ?>" class="collapse">
                                <div class="p-3 bg-light shadow-sm">
                                    <?php include("iniciativas.php"); ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>

                <!-- MODAL CON RESPONSABLES -->
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
                                        $respData = $ClassOkrsServicios->Empleado($responsables[$j]);
                                        $fotoResp = !empty($respData["foto"]) ? $respData["foto"] : "img_default.jpg";
                                        ?>
                                        <div class="d-flex align-items-center mb-2">
                                            <img src="https://goforagile.com/recursos/<?= $fotoResp ?>" class="foto_miniaturas me-2">
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

            <?php endforeach; ?>
        </table>

    </div>
</div>