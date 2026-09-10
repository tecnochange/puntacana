<?php

$array_vicepresidencia = [];
$queryVicepresidencias = mysqli_query($connect_admin, "SELECT id, nombre FROM Vicepresidencia WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND estado = 1 ORDER BY nombre ASC");
while ($dataVicepresidencia = mysqli_fetch_assoc($queryVicepresidencias)) {
    $array_vicepresidencia[$dataVicepresidencia["id"]] = $dataVicepresidencia;
}

function AvancePorcentajeOKR($data){
    $porcentaje = 0;

    $data["tendencia"] = isset($data["tendencia"]) ? $data["tendencia"] : 1;

    if ($data["meta"]) {
            $avance = $data["avance"];
            $meta  = $data["meta"];

            //PARA LOS CASOS ASCENDENTES
            $porcentaje = ($avance  * 100) / $meta;

            //SOLO PARA LOS CASOS DONDE LA META ES NEGATIVA Y LA TENDENCIA ASCENDENTE
            if ($meta < 0) {
                if ($avance > $meta) {
                    $porcentaje = 100;
                }
            }

            //PARA LOS CASOS DESENTENTES
            if ($data["tendencia"] == 2) {
                //$porcentaje = ($meta / $avance  * 100);
                $porcentaje = ($meta * 100) / $avance;
            }

            if ($porcentaje > 100) {
                $porcentaje = 100;
            }
    } 
    else {
        $porcentaje = 0;
    }

    if(is_nan($porcentaje)) {
        $porcentaje = 0;
    }

    $porcentaje = round($porcentaje,1);
    return $porcentaje;
}


?>

<form action="app/models/exportarExcel.php" method="post" target="_blank" id="FormularioExportacion">
    <input type="hidden" id="datos_a_enviar" name="datos_a_enviar" />
</form>

<div class="container-fluid">

    <div class="card mb-3" style="display:none">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-2">
                    <label>Por Alta Dirección</label>
                </div>
                <div class="col-md-4 mb-2">
                    <label>Por Objetivos Estratégicos</label>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mb-3">
        <h3>DESEMPEÑO Y AVANCES ESTRATÉGICO DE GRUPO PUNTA CANA</h3>
    </div>

    <?php include("views/reportes/layouts/escala.php"); ?>

    <div class="card mb-3">
        <div class="card-body">
            <table class="w-100">
                <tr>
                    <td>Avance<br>Incipiente</td>
                    <td>Cumplimiento<br>Parcial</td>
                    <td>Cumplimiento<br>Sustancial</td>
                    <td>Cumplimiento<br>Esperado</td>
                    <td>Cumplimiento <br>Excepcional</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>0% al 50%</td>
                    <td>51% al 80%</td>
                    <td>81% al 95%</td>
                    <td>96% al 100%</td>
                    <td>> 100%</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="text-center mb-3">
        <h3>DESEMPEÑO DE LA COMPAÑIA POR PERIODO</h3>
    </div>

    <div class="text-center mb-3">
        <div class="progress" style="height: 40px;">
            <div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                <h3>14.35%</h3>  
            </div>
        </div>
        
    </div>

    <div>
        Evalúa el rendimiento de la compañia en períodos específicos del año, generalmente en intervalos de tres meses.
    </div>

    <div class="row">
        <div class="col-md-4">
            <?php include("views/reportes/componentes/estrategicos.php"); ?>
        </div>

        <div class="col-md-4">
            
        </div>

        <div class="col-md-4">
            
        </div>
    </div>

    





</div>

<script>
    // Exportar datos filtrados
    function exportarReporte() {
        var tablaFiltrada = $("<div>").append($("#TablaExcel").clone()).html();
        $("#datos_a_enviar").val(tablaFiltrada);
        $("#FormularioExportacion").submit();
    }
</script>