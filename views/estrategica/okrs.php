<script>
$(document).ready(function() {
    $('#menuEstrategia').collapse();
    $('#bt_estrategia_configuracion').addClass('active');
});
</script>


<?php

//CONSULTA PARA NUEVO CLIENTE
//CONSULTA PARA NUEVO CLIENTE

?>

<div class="container">

    <ul class="nav nav-tabs justify-content-center">
        <li class="nav-item">
            <a class="nav-link" href="?pg=estrategica/configurar">General</a>
        </li>
        <li class="nav-item">
            <a class="nav-link " href="?pg=estrategica/desempenio">Desempeño</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="?pg=estrategica/competencias">Competencias</a>
        </li>
        <li class="nav-item">
            <a class="nav-link " href="?pg=estrategica/kpis">Kpis</a>
        </li>
        <li class="nav-item">
            <a class="nav-link actives" href="?pg=estrategica/okrs">Okrs</a>
        </li>
    </ul>


    <div class="card mb-3">
        <div class="card-header">
            <h3>Okrs</h3>
        </div>

        <div class="card-body">
                <form action="" method="post">
                    <div class="row">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <h2>Seleccionar Ciclo</h2>
                                    <input type="hidden" name="guardar_configruar_ciclo" value="true">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-2">
                                    <lable>Año:</lable>
                                    <select class="form-control form-control-sm" name="anio_ciclo" id="anio_ciclo">
                                        <option value="">Por Año...</option>
                                        <?php
                                        $query = mysqli_query($connect_valoracion, "SELECT * FROM Ciclos WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' ");
                                        while ($dataCiclos = mysqli_fetch_array($query)) {
                                            if ($_SESSION["ciclo"] ==  $dataCiclos["id"]) {
                                                echo '<option value="' . $dataCiclos["anio"] . '" selected>' . $dataCiclos["anio"] . '</option>';
                                            } else {
                                                echo '<option value="' . $dataCiclos["anio"] . '">' . $dataCiclos["anio"] . '</option>';
                                            }
                                        }

                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <lable>Ciclo:</lable>
                                    <select class="form-control form-control-sm" name="ciclo" id="ciclo">
                                        <option value="">Por Ciclo...</option>
                                        <?php
                                        $query = mysqli_query($connect_valoracion, "SELECT * FROM Ciclos WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' ");
                                        while ($dataCiclos = mysqli_fetch_array($query)) {
                                            if ($_SESSION["ciclo"] ==  $dataCiclos["id"]) {
                                                echo '<option value="' . $dataCiclos["id"] . '" selected>' . $dataCiclos["nombre"] . '</option>';
                                            } else {
                                                echo '<option value="' . $dataCiclos["id"] . '">' . $dataCiclos["nombre"] . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="margin-bottom: 10px">
                            <button type="submit" class="btn btn-success btn-block btn-sm">
                                <i class="fas fa-check"></i> Guardar
                            </button>
                        </div>
                    </div>
                </form>
        </div>
      
    </div>


</div>

<script>
    var api_kpi = '<?php echo $url; ?>api/kpis/';
    function actualizarEstadoMesKPI(mes, id) {
        var estado = document.getElementById('mes_kpi_' + mes + '_' + id).checked ? 1 : 0;
        var xhr = new XMLHttpRequest();
        xhr.open('POST', api_kpi + 'habilitar_mes_kpi.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                console.log(xhr.responseText);
                toastr.success('Acción actualizada con éxito', '¡Éxito!');
            }
        };
        xhr.send('id=' + id + '&estado=' + estado + '&mes=' + mes);
    }
</script>





