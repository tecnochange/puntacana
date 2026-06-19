<!-- jQuery & Bootstrap JS deben estar cargados antes de este bloque -->
<script>
    $(document).ready(function() {
        $('#menuEstructura').collapse();
        $("#bt_rutas").addClass("active");

        $("#buscador").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $(".tabla_lista tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>

<div class="container-fluid">
    <form method="post">
        <input type="hidden" name="guardar_permisos" value="true">
        <div class="card">
            <div class="card-body">
                <h2 class="mb-md-0">GESTIONAR RUTAS</h2>
                <div class="d-flex gap-2 w-100 w-md-auto mb-5 mt-5">
                    <input class="form-control form-control-sm" type="text" placeholder="Búsqueda rápida..." id="buscador" style="max-width: 250px;">

                    
                        <a href="<?= $url ?>?pg=empresa/ruta/detalle">
                            <button type="button" class="btn btn-primary btn-sm">Nuevo</button>
                        </a>
              
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Ruta</th>
                                <th>Módulo</th>
                                <th>Editar Ruta</th>
                            </tr>
                        </thead>

                        <tbody class="tabla_lista">
                            <?php
                            $queryModulo = mysqli_query($connect_admin, "SELECT * FROM Modulos");

                            while ($dataModulo = mysqli_fetch_array($queryModulo)) {
                                echo '
                                    <tr class="table-secondary">
                                        <td colspan="3">
                                            <strong>' . htmlspecialchars($dataModulo["nombre"]) . '</strong>
                                        </td>
                                    </tr>
                                ';

                                $queryRuta = mysqli_query($connect_admin, "SELECT * FROM Rutas WHERE modulo = '" . $dataModulo["id"] . "'");

                                while ($dataRuta = mysqli_fetch_array($queryRuta)) {
                                    $bt_edit = '';
                                    //if ($VALIDAR_ROOT["editar"]) {
                                        $bt_edit = '
                                            <a href="' . $url . '?pg=empresa/ruta/detalle&id=' . $dataRuta["id"] . '">
                                                <button type="button" class="btn btn-success btn-sm">
                                                    <i class="bx bx-edit"></i>
                                                </button>
                                            </a>';
                                   // }

                                    echo '
                                        <tr>
                                            <td>' . htmlspecialchars($dataRuta["ruta"]) . '</td>
                                            <td>' . htmlspecialchars($dataModulo["nombre"]) . '</td>
                                            <td>' . $bt_edit . '</td>
                                        </tr>
                                    ';
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>

