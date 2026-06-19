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
                $queryComentarios = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Comentarios_Iniciativas WHERE id_iniciativa = '$id_iniciativa' ");
                if(mysqli_num_rows($queryComentarios) > 0){
                    while($row = mysqli_fetch_assoc($queryComentarios)){?>
                        <tr>
                            <td class="text-center"><?= $row['id_empleado']; ?></td>
                            <td><?= $row['comentario']; ?></td>
                            <td class="text-center"><?= $row['created_at']; ?></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-primary btn-sm bt_editar" title="Editar Comentario" onclick="">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm bt_editar" title="Eliminar Comentario" onclick="">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>