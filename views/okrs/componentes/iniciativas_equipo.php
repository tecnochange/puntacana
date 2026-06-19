<?php

?>

<!-- CONSOLIDADO -->
<?php include("views/okrs/componentes/consolidado_iniciativas.php"); ?>

<!-- LISTADO DE INICIATIVAS -->
<div class="table-responsive mt-4">
    <table class="table" id="tabla_iniciativas">
        <thead>
                    <tr>
                        <th>Iniciativa</th>
                        <th class="text-center">Mes</th>
                        <th class="text-center">Fecha Entrega</th>
                        <th class="text-center">Meta</th>
                        <th class="text-center">Seguimiento</th>
                        <th class="text-center">Porcentaje</th>
                        <th>Acciones</th>
                    </tr>
        </thead>
        <tbody>
                    <?php foreach ($okrs_iniciativas as $iniciativa): ?>
                        <?php //dd($iniciativa); ?>
                        <tr>
                            <td><?= $iniciativa["descripcion"]; ?></td>
                            <td class="text-center"><?= $ClassOkrsServicios->nombreMes($iniciativa["mes"]); ?></td>
                            <td class="text-center"><?= $iniciativa["fecha_entrega"]; ?></td>
                            <td class="text-center"><?= $iniciativa["meta"]; ?></td>
                            <td class="text-center">
                                <input type="text" class="form-control form-control-sm decimales" onkeyup="okrsClass.EditarSeguimiento('Okrs_Iniciativas',<?= $iniciativa['id'] ?>,this.value)" value="<?= $iniciativa["avance"]; ?>">
                            </td>
                            <td class="text-center">
                                <div class="progress">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= empty($iniciativa["porcentaje_avance"]) || (float)$iniciativa["porcentaje_avance"] == 0 ? '100' : $iniciativa["porcentaje_avance"]; ?>%; background-color: <?= $iniciativa["bg_color"]; ?> !important;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                        <b><?= number_format($iniciativa["porcentaje_avance"], 0); ?>%</b>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <button class="btn btn-sm" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bx bx-dots-horizontal-rounded"></i>
                                </button>
                                <ul class="dropdown-menu" id="menu_acciones">
                                    <li>
                                        <a href="?pg=okrs/okr/iniciativas&id_okr=<?= $iniciativa["id_okrs"]; ?>&id_resultado=<?= $iniciativa["id_resultado"]; ?>&id_iniciativa=<?= $iniciativa["id"]; ?>" class=" dropdown-item">Editar Iniciativa</a>
                                    </li>
                                    <li>
                                        <a class=" dropdown-item">Duplicar Iniciativa</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item">Comentarios</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item">Documentos</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item">Planes de Acción</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item">Gestionar Iniciativas</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item">Eliminar Iniciativas **</a>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                    <?php endforeach; ?>
        </tbody>
    </table>
</div>


<script>
    $(document).ready(function() {

        $('#tabla_iniciativas').DataTable(
            {
                pageLength: 50
            }
        );
    });
</script>

