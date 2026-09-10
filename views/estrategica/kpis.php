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
            <a class="nav-link active" href="?pg=estrategica/kpis">Kpis</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="?pg=estrategica/okrs">Okrs</a>
        </li>
    </ul>


    <div class="card mb-3">
        <div class="card-header">
            <h3>Habilitación de Meses</h3>
        </div>

        <div class="card-body">

            <table class="table table-bordered">
                <tr>
                    <th>Año</th>
                    <?php 
                    $mesInicio = $dataAFEmpresa["mes_inicio"];
                    $mesFin = $dataAFEmpresa["mes_fin"];
                    $listadoMesFiscal = obtenerListadoDesdeMes($mesInicio);
                    $keys = array_keys($listadoMesFiscal);
                    for ($i = 0; $i < count($keys); $i++) {
                        $indice = $keys[$i];
                        $mes = $listadoMesFiscal[$indice];
                        echo '<th>' . $mes . '</th>';
                    } 
                    ?>  
                </tr>
                <?php 
                    $queryHabilitaciones = mysqli_query($connect_kpis, "SELECT * FROM Habilitar_Mes WHERE id_empresa = " . $user_log["id_empresa"] . "");
                    while ($dataHabilitaciones = mysqli_fetch_array($queryHabilitaciones)) { 
                        $lista_meses = '';

                        $keys = array_keys($listadoMesFiscal);
                        for ($i = 0; $i < count($keys); $i++) {
                            $indice = $keys[$i];
                            $mes = $listadoMesFiscal[$indice]; 
                            $estadoCheckedMes = ($dataHabilitaciones[strtolower($mes)] == 'on') ? "checked" : "";
                            $mes_upercase = strtolower($mes);

                            $lista_meses .= '
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="mes_kpi_'.$mes_upercase.'_'.$dataHabilitaciones["id"].'" '.$estadoCheckedMes.' onclick="actualizarEstadoMesKPI('."'".$mes_upercase."'".', '.$dataHabilitaciones["id"].')">
                                </div>
                            </td>
                            ';
                                                    
                        } 

                        echo '
                        <tr>
                            <td>'.$dataHabilitaciones["anio"].'</td>
                            '.$lista_meses.'
                        </tr>
                        ';

                        
                        
                    }                            
                ?>
            </table>

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





