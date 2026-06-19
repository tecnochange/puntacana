<style>
    /* Evitar salto de línea en los encabezados */
    #comentarios th {
        white-space: nowrap;
    }
</style>
<div class="col-md-12">
    <div class="table-responsive">
        <table id="comentarios" class="table">
            <thead class="table-dark">
                <tr>
                    <th>Publicado por</th>
                    <th>Comentario</th>
                    <th class="text-center" style="min-width: 150px;">Fecha</th>
                    <th class="text-center" style="min-width: 100px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $queryComentarios = mysqli_query($connect_okrs, "SELECT * FROM Comentarios_Gestion_Iniciativas WHERE id_iniciativa = '$id_iniciativa' ");
                ?>
                <?php while ($dataComentarios = mysqli_fetch_assoc($queryComentarios)): ?>

                    <?php
                    $queryPub = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id = '" . $dataComentarios["id_empleado"] . "' ");
                    $dataPub = mysqli_fetch_array($queryPub);

                    if (!$dataPub["foto"]) {
                        $dataPub["foto"] = "img_default.jpg";
                    }

                    $publicadoPor = '<img loading="lazy" src="' . $recursos_publico .  $dataPub["foto"] . '" class="foto_miniaturas" title="' . $dataPub["nombre"] . '" style="width: 40px !important;height: 40px !important;" onclick="FichaEmpleado(' . $dataComentarios["id_empleado"] . ')">';
                    $fechaInicio = isset($dataComentarios["created_at"]) ? date("d-m-Y", strtotime($dataComentarios["created_at"])) : "";
                    ?>

                    <tr>
                        <td class="text-center"><?= $publicadoPor; ?></td>
                        <td><?= $dataComentarios["comentario"]; ?></td>
                        <td class="text-center"><?= $fechaInicio; ?></td>
                        <td class="text-center">
                            <?php if ($dataEmpleado["role"] == 1): ?>
                                <button type="button" class="btn btn-primary btn-sm bt_editar" title="Editar Comentario" onclick="EditarComentario('<?= $dataComentarios['id']; ?>','<?= $dataEmpleado['id_empresa']; ?>','<?= $dataEmpleado['id']; ?>','<?= $dataEmpleado['area']; ?>','<?= $kpis['frecuencia']; ?>')">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm bt_editar" title="Eliminar Comentario" onclick="EliminarComentario(this,'<?= $dataComentarios['id']; ?>','<?= $dataEmpleado['id_empresa']; ?>','<?= $dataEmpleado['id']; ?>')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>

                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>