<?php

$array_vicepresidencia = [];
$queryVicepresidencias = mysqli_query($connect_admin, "SELECT id, nombre FROM Vicepresidencia WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND estado = 1 ORDER BY nombre ASC");
while ($dataVicepresidencia = mysqli_fetch_assoc($queryVicepresidencias)) {
    $array_vicepresidencia[$dataVicepresidencia["id"]] = $dataVicepresidencia;
}

$array_areas = [];
$queryAreas = mysqli_query($connect_admin, "SELECT id, nombre FROM Areas WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' ORDER BY nombre ASC");
while ($dataAreas = mysqli_fetch_assoc($queryAreas)) {
    $array_areas[$dataAreas["id"]] = $dataAreas;
}

include("app/models/kpis/KpisServicios.php");
$ClassKpisServicios = new KpisServicios($user_log["id_empresa"]);

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

            <h5>Reporte general del KPIs <?php echo $_SESSION["anio_fill"]; ?></h5>
        </div>

        <div class="barra_superior">
            <div class="div_content"></div>
        </div>

        <div class="table-responsive">
        <table class="table table-bordered" id="TablaExcel">
            <tr>
                <th>#</th>
                <th>Tipo Kpi</th>
                <th>Área macro</th>
                <th>Área Proceso</th>
                <th>Año</th>
                <th style="width: 480px;">Indicador</th>
                <th>Objetivo del Indicador</th>
                <th>Fórmula de Cálculo</th>
                <th>Tipo de Resultado</th>
                <th>Tipo de Cálculo</th>
                <th>Unidad de Medida</th>
                <th>Frecuencia</th>

                <th>Meta</th>
                <th>Seguimiento</th>

                <th>% Avance</th>

                <th>Enero</th>
                <th>Febrero</th>
                <th>Marzo</th>
                <th>Abril</th>
                <th>Mayo</th>
                <th>Junio</th>
                <th>Julio</th>
                <th>Agosto</th>
                <th>Septiembre</th>
                <th>Octubre</th>
                <th>Noviembre</th>
                <th>Diciembre</th>

                <th>Enero Seguimiento</th>
                <th>Febrero Seguimiento</th>
                <th>Marzo Seguimiento</th>
                <th>Abril Seguimiento</th>
                <th>Mayo Seguimiento</th>
                <th>Junio Seguimiento</th>
                <th>Julio Seguimiento</th>
                <th>Agosto Seguimiento</th>
                <th>Septiembre Seguimiento</th>
                <th>Octubre Seguimiento</th>
                <th>Noviembre Seguimiento</th>
                <th>Diciembre Seguimiento</th>

            </tr>
            <?php 
            $count = 1;
            $sentencia = "
            SELECT DISTINCT
                Kpis.id, 
                Kpis.tipo_kpi, 
                Kpis.area_macro, 
                Kpis.area_proceso, 
                Kpis.anio, 
                Kpis.objetivo_indicador, 
                Kpis.indicador, 
                Kpis.formula, 
                Kpis.tipo_resultado, 
                Kpis.tipo_calculo, 
                Kpis.frecuencia, 
                Kpis.unidad_medida, 
                Kpis.meta, 

                Frecuencia_Kpis.enero, 
                Frecuencia_Kpis.febrero, 
                Frecuencia_Kpis.marzo, 
                Frecuencia_Kpis.abril, 
                Frecuencia_Kpis.mayo, 
                Frecuencia_Kpis.junio, 
                Frecuencia_Kpis.julio, 
                Frecuencia_Kpis.agosto, 
                Frecuencia_Kpis.septiembre, 
                Frecuencia_Kpis.octubre, 
                Frecuencia_Kpis.noviembre, 
                Frecuencia_Kpis.diciembre, 

                Frecuencia_Kpis.avance_1, 
                Frecuencia_Kpis.avance_2, 
                Frecuencia_Kpis.avance_3, 
                Frecuencia_Kpis.avance_4, 
                Frecuencia_Kpis.avance_5, 
                Frecuencia_Kpis.avance_6, 
                Frecuencia_Kpis.avance_7, 
                Frecuencia_Kpis.avance_8, 
                Frecuencia_Kpis.avance_9, 
                Frecuencia_Kpis.avance_10, 
                Frecuencia_Kpis.avance_11, 
                Frecuencia_Kpis.avance_12  

            FROM
                Kpis 
                LEFT JOIN Frecuencia_Kpis ON Frecuencia_Kpis.id_kpi = Kpis.id
            WHERE
                Kpis.id_empresa = '".$_SESSION["id_empresa"]."' AND Kpis.anio = '".$_SESSION["anio_fill"]."'  
            ";
            $query = mysqli_query( $connect_kpis, $sentencia );
            while($data = mysqli_fetch_array($query)){

                //AREA MACRO
                $vicepresidencia = $array_vicepresidencia[$data["area_macro"]];
                $area = $array_areas[$data["area_proceso"]];

                

                $txt_tipo_kpi = "";
                foreach ($Array_tipo_kpi_PC1 as $nodo) {
                    if($data["tipo_kpi"] ==  $nodo[0]) {
                        $txt_tipo_kpi = $nodo[1];
                    }
                }

                $txt_tipo_resultado = "";
                foreach ($Array_Acumulativo_PC as $objetivo) {
                    if ($objetivo[0] == $data["tipo_resultado"]) {
                         $txt_tipo_resultado = $objetivo[1];
                    } 
                }

                $txt_tipo_calculo = "";
                foreach ($Array_Tendencia_KPIS as $objetivo) {
                    if ($objetivo[0] == $data["tipo_calculo"]) {
                        $txt_tipo_calculo = $objetivo[1] ;
                    } 
                }

                $txt_frecuencia = "";
                foreach ($Array_Frecuencia_PC as $frecuencia) {
                    if ($frecuencia[0] == $data["frecuencia"]) {
                        $txt_frecuencia = $frecuencia[1];
                    }
                }

                $txt_unidad_medida = "";
                foreach ($Array_Medicion_PC as $unidad) {
                    if ($unidad[0] == $data["unidad_medida"]) {
                        $txt_unidad_medida = $unidad[1];
                    } 
                }

                $avance = 0;
                $avance = $ClassKpisServicios->AvanceKPI_2($data);

                


                

                echo '
                <tr>
                    <td>'.$count.'</td>
                    <td title="'.$data["id"].'">'.$txt_tipo_kpi.'</td>
                    <td>'.$vicepresidencia["nombre"].'</td>
                    <td>'.$area["nombre"].'</td>
                    <td>'.$data["anio"].'</td>
                    <td>'.$data["objetivo_indicador"].'</td>
                    <td>'.$data["indicador"].'</td>
                    <td>'.$data["formula"].'</td>


                    <td>'.$txt_tipo_resultado.'</td>
                    <td>'.$txt_tipo_calculo.'</td>
                    <td>'.$txt_unidad_medida.'</td>
                    <td>'.$txt_frecuencia.'</td>

                    <td>'.$data["meta"].'</td>
                    <td>'.$avance["avance_numero"].'</td>
                    <td>'.$avance["avance_porcentaje"].'%</td>

                    <td>'.$data["enero"].'</td>
                    <td>'.$data["febrero"].'</td>
                    <td>'.$data["marzo"].'</td>
                    <td>'.$data["abril"].'</td>
                    <td>'.$data["mayo"].'</td>
                    <td>'.$data["junio"].'</td>
                    <td>'.$data["julio"].'</td>
                    <td>'.$data["agosto"].'</td>
                    <td>'.$data["septiembre"].'</td>
                    <td>'.$data["octubre"].'</td>
                    <td>'.$data["noviembre"].'</td>
                    <td>'.$data["diciembre"].'</td>


                    <td>'.$data["avance_1"].'</td>
                    <td>'.$data["avance_2"].'</td>
                    <td>'.$data["avance_3"].'</td>
                    <td>'.$data["avance_4"].'</td>
                    <td>'.$data["avance_5"].'</td>
                    <td>'.$data["avance_6"].'</td>
                    <td>'.$data["avance_7"].'</td>
                    <td>'.$data["avance_8"].'</td>
                    <td>'.$data["avance_9"].'</td>
                    <td>'.$data["avance_10"].'</td>
                    <td>'.$data["avance_11"].'</td>
                    <td>'.$data["avance_12"].'</td>
                    
                </tr>
                ';

                $count++;
            }
            ?>
        </table>
        </div>

    </div>



</div>

<style>
    .barra_superior {
        overflow-x: scroll;
        overflow-y: hidden;
        width: 100%;
    }
    .div_content {
        height: 10px;
    }
</style>

<script>
    // Exportar datos filtrados
    function exportarReporte() {
        var tablaFiltrada = $("<div>").append($("#TablaExcel").clone()).html();
        $("#datos_a_enviar").val(tablaFiltrada);
        $("#FormularioExportacion").submit();
    }

    $(document).ready(function () {
        $('.div_content').width($('#TablaExcel').width());
    });

    $(function () {
        $(".barra_superior").scroll(function () {
            $(".table-responsive").scrollLeft($(".barra_superior").scrollLeft());
        });
        $(".table-responsive").scroll(function () {
            $(".barra_superior").scrollLeft($(".table-responsive").scrollLeft());
        });
    }); 
</script>