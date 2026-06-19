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

    <div class="card">
        <div class="card-header">
            <button type="button" class="btn btn-danger btn-sm" onclick="exportarReporte()" style="float: right;">
                Descargar
            </button> 

            <h5>Reporte general del OKRs <?php echo $_SESSION["anio_fill"]; ?></h5>
        </div>

        <div class="table-responsive">
        <table class="table table-bordered" id="TablaExcel">
            <tr>
                <th>#</th>
                <th style="width: 480px;">Objetivo</th>
                <th>Año</th>
                <th>Tipo</th>
                <th>Periodo</th>
                <th>Fecha inicia</th>
                <th>Fecha termina</th>
                <th>Alta Dirección</th>
                <th>Objetivo Estratégico</th>


                <th>Resultado</th>
                <th>Meta</th>
                <th>Avance</th>
                <th>Porcentaje Avance</th>
                <th>Tendencia</th>
                <th>Medición</th>
                <th>Periodo</th>
            </tr>
            <?php 
            $count = 1;
            $sentencia = "
            SELECT DISTINCT
                Okrs.id, Okrs.objetivo_okr, Okrs.anio, Okrs.objetivos_estrategicos, Okrs.tipo, Okrs.periodo, Okrs.fecha_inicia, Okrs.fecha_termina, 
                Okrs_Vicepresidencia.id_vicepresidencia AS vicepresidencia, 
                Okrs_Vicepresidencia.id_obj_estrategico AS id_obj_estrategico, 
                Objetivos_estrategicos.objetivo AS nombre_objetivo_estrategico 
            FROM
                Okrs
                LEFT JOIN Okrs_Vicepresidencia ON Okrs_Vicepresidencia.id_okrs = Okrs.id 
                LEFT JOIN Objetivos_estrategicos ON Objetivos_estrategicos.id = Okrs_Vicepresidencia.id_obj_estrategico
            WHERE
                Okrs.id_empresa = '".$_SESSION["id_empresa"]."' AND Okrs.anio = '".$_SESSION["anio_fill"]."' 
            ";
            $query = mysqli_query( $connect_okrs, $sentencia );
            while($data = mysqli_fetch_array($query)){

                $txt_tipo = "";
                foreach ($Array_Tipo_OKR as $nodo) {
                    if($data["tipo"] ==  $nodo[0]) {
                        $txt_tipo = $nodo[1];
                    }
                }

                $lista_objetivos = "";
                $queryResultados = mysqli_query( $connect_okrs, " SELECT * FROM Okrs_Resultados WHERE id_okrs = '".$data["id"]."' " );
                while($dataResultados = mysqli_fetch_array($queryResultados)){

                    $txt_medicion = "";
                    foreach ($Array_Medicion as $nodo) {
                        if($dataResultados["medicion"] ==  $nodo[0]) {
                            $txt_medicion = $nodo[1];
                        }
                    }

                    $txt_tendencia = "";
                    foreach ($Array_Tendencia as $nodo) {
                        if($dataResultados["tendencia"] ==  $nodo[0]) {
                            $txt_tendencia = $nodo[1];
                        }
                    }

                    $porcentaje = AvancePorcentajeOKR($dataResultados);

                    $data_vicepresidencia = $array_vicepresidencia[$data["vicepresidencia"]];

                    $lista_objetivos .= '

                    <tr>
                        <td>'.$count.'</td>
                        <td title="'.$data["id"].'">'.$data["objetivo_okr"].'</td>
                        <td>'.$data["anio"].'</td>
                        <td>'.$txt_tipo.'</td>
                        <td>'.$data["periodo"].'</td>
                        <td>'.$data["fecha_inicia"].'</td>
                        <td>'.$data["fecha_termina"].'</td>
                        <td>'.$data_vicepresidencia["nombre"].'</td>
                        <td>'.$data["nombre_objetivo_estrategico"].'</td>

                        <td>'.$dataResultados["descripcion"].'</td>
                        <td>'.$dataResultados["meta"].'</td>
                        <td>'.$dataResultados["avance"].'</td>
                        <td>'.$porcentaje.'%</td>
                        <td>'.$txt_tendencia.'</td>
                        <td>'.$txt_medicion.'</td>
                        <td>'.$dataResultados["periodo"].'</td>
                        
                    </tr>
                    ';
                }

                echo $lista_objetivos;

                if($queryResultados->num_rows == 0){ 

                    echo '

                    <tr>
                        <td>'.$count.'</td>
                        <td title="'.$data["id"].'">'.$data["objetivo_okr"].'</td>
                        <td>'.$data["anio"].'</td>
                        <td>'.$txt_tipo.'</td>
                        <td>'.$data["periodo"].'</td>
                        <td>'.$data["fecha_inicia"].'</td>
                        <td>'.$data["fecha_termina"].'</td>
                        <td>'.$data_vicepresidencia["nombre"].'</td>
                        <td>'.$data["nombre_objetivo_estrategico"].'</td>

                        <td>sin resultado</td>
                        <td>sin resultado</td>
                        <td>sin resultado</td>
                        <td>sin resultado</td>
                        <td>sin resultado</td>
                        <td>sin resultado</td>
                        <td>sin resultado</td>
                        
                    </tr>
                    ';

                }

                $count++;
                /*
                echo '
                <tr>
                    <td>'.$data["objetivo_okr"].'</td>
                    <td>'.$data["anio"].'</td>
                    <td>'.$txt_tipo.'</td>
                    <td>'.$data["periodo"].'</td>
                    <td>'.$data["fecha_inicia"].'</td>
                    <td>'.$data["fecha_termina"].'</td>
                </tr>
                ';
                */
            }
            ?>
        </table>
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