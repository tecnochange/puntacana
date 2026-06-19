<script>
$(document).ready(function() {
    $('#menuReportes').collapse();
    $('#bt_reportes_lideres').addClass('active');
});
</script>


<div class="container">
    <div class="card mb-3">
        <div class="card-body">
            <h3>Reporte Individual Líderes Equipo AÑO <?php echo $_SESSION["anio_fill"]; ?></h3>
        </div>
    </div>

    <?php include("views/reportes/layouts/filtros.php"); ?>

    <div class="card">
        <div class="card-body">

        <table class="table">
            <tr>
                <th>Nombre</th>
                <th>Líder</th>
                <th>OKRs Asignados</th>
                <th>Progreso</th>
                <th>Acciones</th>
            </tr>
            <?php
            $query = mysqli_query($connect_admin, "SELECT * FROM Vicepresidencia 
            WHERE id_empresa = " . $user_log["id_empresa"] . " AND estado = 1 ORDER BY nombre ASC ");
            while($data = mysqli_fetch_array($query)){

                $progreso = '
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
                </div>
                ';

                $bt_editar = '';
                if($VALIDAR_ROOT["editar"]){
                    $bt_editar = '
                        <a href="' . $url . '?pg=reportes/area/detalle&id='.$data["id"].'">
                            <button type="button" class="btn btn-success btn-sm" title="Editar">
                                <i class="bx bx-edit"></i>
                            </button>
                        </a>
                    ';
                }

                echo '
                <tr>
                    <td>'.$data["nombre"].'</td>
                    <th></td>
                    <td></td>
                    <td>'.$progreso.'</td>
                    <td>'.$bt_editar.'</td>
                </tr>
                ';
            }

            ?>
        </table>

        </div>
    </div>
</div>