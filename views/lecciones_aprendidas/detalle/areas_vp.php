<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table border="1" id="areas_vp<?php echo $dataVP["id"] ?>" class="display table" style="width:100%">
                        <thead>
                            <th>Nombre Área</th>
                            <th>Líder</th>
                            <th>Lecciones Asignadas</th>
                            <th>Acciones</th>
                        </thead>
                        <tbody>
                            <?php

                            foreach ($equipo as $value) {

                                echo '
																<tr style="vertical-align: middle;">
                                                                <td>' . $value["nombre"] . '</td>
																<td>' . $value["nombre_lider"] . '</td>
																<td align="center">' . $value["contador"] . '</td>
																<td align="end"><button type="button" class="btn btn-primary btn-sm bt_editar" onClick="VerCelula(' . $value["id"] . ','.$_SESSION["id_user"].')" data-bs-toggle="tooltip" title="Vista rápida célula">
                                                                    <i class="fa fa-users"></i>
                                                                </button>
                                                                <a class="btn btn-black btn-sm bt_editar" href="' . $url . '?pg=lecciones_aprendidas/detalle/info_area&id=' . $value["id"] . '" title="Vista rápida del área" id="vistaAreas" style="float: right;">
																<i class="fa fa-eye" style="font-size: 1.3rem;"></i>
																</a></td>
																</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                    <script type="text/javascript">
                        $(document).ready(function() {
                            $('#areas_vp<?php echo $dataVP["id"] ?>').DataTable({
                                columnDefs: [{
                                        responsivePriority: 1,
                                        targets: 0
                                    },
                                    {
                                        responsivePriority: 2,
                                        targets: -1
                                    }
                                ],
                                order: [
                                    [3, 'desc']
                                ],
                                responsive: true,
                                // pageLength: 50,
                                language: {
                                    processing: "Procesando...",
                                    search: "Buscar:",
                                    lengthMenu: "Mostrar _MENU_ registros.",
                                    info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                                    infoEmpty: "Mostrando registros del 0 al 0 de 0 registros",
                                    infoFiltered: "(filtrado de un total de _MAX_ registros)",
                                    infoPostFix: "",
                                    loadingRecords: "Cargando...",
                                    zeroRecords: "No se encontraron resultados",
                                    emptyTable: "Ningún dato disponible en esta tabla",
                                    row: "Registro",
                                    export: "Exportar",
                                    paginate: {
                                        first: "Primero",
                                        previous: "Anterior",
                                        next: "Siguiente",
                                        last: "Ultimo"
                                    },
                                    aria: {
                                        sortAscending: ": Activar para ordenar la columna de manera ascendente",
                                        sortDescending: ": Activar para ordenar la columna de manera descendente"
                                    },
                                    select: {
                                        row: "registro",
                                        selected: "seleccionado"
                                    }
                                }
                            });
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>