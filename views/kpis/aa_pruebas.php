<?php

$hoy = date("Y-m-d H:i:s");

//CARGAMOS LOS NIVELES
$arrayTipos = array();
$queryT = mysqli_query($connect_valoracion, "SELECT * FROM Tipos WHERE id_empresa = '" . $user_log["id_empresa"] . "' ORDER BY id DESC ");
while ($dataT = mysqli_fetch_array($queryT)) {
	array_push($arrayTipos, array($dataT["id"], $dataT["nombre"]));
}

//CARGAMOS LOS NIVELES
$arrayNiveles = array();
$queryN = mysqli_query($connect_valoracion, "SELECT * FROM Niveles WHERE id_empresa = '" . $user_log["id_empresa"] . "' ORDER BY id DESC ");
while ($dataN = mysqli_fetch_array($queryN)) {
	array_push($arrayNiveles, array($dataN["id"], $dataN["nombre"]));
}

//CARGAMOS LOS NIVELES
$arrayNivelesCargados = array();
$queryNiveles = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Niveles WHERE anio = 2026 and id_empresa = 1 ");
while ( $dataNivelesCargados = mysqli_fetch_array($queryNiveles)) { 
    array_push($arrayNivelesCargados, $dataNivelesCargados );
}

?>

<div class="container">
    <table class="table">
        <?php
        //CARGOS PARA CAMBIAR

        /*
        $count = 1;
        $array_cargos = [];
        $query = mysqli_query($connect_kpis, "SELECT * FROM Kpis WHERE anio = 2026  ");
        while($data = mysqli_fetch_array($query)){ 

            $sentencia_inst = "
                INSERT INTO Kpis(
                    id_empresa,
                    id_empleado,
                    tipo_kpi,
                    anio,
                    area_macro,
                    area_proceso,
                    subproceso,
                    objetivo_sg,
                    indicador,
                    objetivo_indicador,
                    formula,
                    resultado_anterior,
                    unidad_medida,
                    tipo_calculo,
                    meta,
                    frecuencia,
                    tipo_resultado,
                    obj_meses,
                    created_at,
                    updated_at, 
                    id_borrar
                )
                VALUES(
                    '".$data["id_empresa"]."',
                    '".$data["id_empleado"]."',
                    '".$data["tipo_kpi"]."',
                    '2027',
                    '".$data["area_macro"]."',
                    '".$data["area_proceso"]."',
                    '".$data["subproceso"]."',
                    '".$data["objetivo_sg"]."',
                    '".$data["indicador"]."',
                    '".$data["objetivo_indicador"]."',
                    '".$data["formula"]."',
                    '".$data["resultado_anterior"]."',
                    '".$data["unidad_medida"]."',
                    '".$data["tipo_calculo"]."',
                    '".$data["meta"]."',
                    '".$data["frecuencia"]."',
                    '".$data["tipo_resultado"]."',
                    '".$data["obj_meses"]."',
                    '".$hoy."',
                    '".$hoy."', 
                    '".$data["id"]."'
                )
            ";
            //mysqli_query($connect_kpis, $sentencia_inst );

            echo '
            <tr>
                <td>'.$count.'</td>
                <td>'.$data["indicador"].'</td>
            </tr>
            ';

            $count++;
     
        }
        */

        /*
        $count = 1;
        $array_cargos = [];
        $query = mysqli_query($connect_kpis, "SELECT * FROM Kpis WHERE anio = 2027  ");
        while($data = mysqli_fetch_array($query)){ 

            $queryFrecuencia = mysqli_query($connect_kpis, "SELECT * FROM Frecuencia_Kpis WHERE id_kpi = '".$data["id_borrar"]."' ");
            $dataFrecuencia = mysqli_fetch_array($queryFrecuencia);

            if($queryFrecuencia->num_rows > 0){

                $sentencia_frec = "
                    INSERT INTO Frecuencia_Kpis(
                        id_kpi,
                        id_empresa,
                        tipo,
                        enero,
                        avance_1,
                        febrero,
                        avance_2,
                        marzo,
                        avance_3,
                        abril,
                        avance_4,
                        mayo,
                        avance_5,
                        junio,
                        avance_6,
                        julio,
                        avance_7,
                        agosto,
                        avance_8,
                        septiembre,
                        avance_9,
                        octubre,
                        avance_10,
                        noviembre,
                        avance_11,
                        diciembre,
                        avance_12, 
                        created_at,
                        updated_at, 
                        anio_new
                    )
                    VALUES(
                        '".$data["id"]."',
                        '".$dataFrecuencia["id_empresa"]."',
                        '".$dataFrecuencia["tipo"]."',
                        '".$dataFrecuencia["enero"]."',
                        '".$dataFrecuencia["avance_1"]."',
                        '".$dataFrecuencia["febrero"]."',
                        '".$dataFrecuencia["avance_2"]."',
                        '".$dataFrecuencia["marzo"]."',
                        '".$dataFrecuencia["avance_3"]."',
                        '".$dataFrecuencia["abril"]."',
                        '".$dataFrecuencia["avance_4"]."',
                        '".$dataFrecuencia["mayo"]."',
                        '".$dataFrecuencia["avance_5"]."',
                        '".$dataFrecuencia["junio"]."',
                        '".$dataFrecuencia["avance_6"]."',
                        '".$dataFrecuencia["julio"]."',
                        '".$dataFrecuencia["avance_7"]."',
                        '".$dataFrecuencia["agosto"]."',
                        '".$dataFrecuencia["avance_8"]."',
                        '".$dataFrecuencia["septiembre"]."',
                        '".$dataFrecuencia["avance_9"]."',
                        '".$dataFrecuencia["octubre"]."',
                        '".$dataFrecuencia["avance_10"]."',
                        '".$dataFrecuencia["noviembre"]."',
                        '".$dataFrecuencia["avance_11"]."',
                        '".$dataFrecuencia["diciembre"]."',
                        '".$dataFrecuencia["avance_12"]."',
                        '".$hoy."',
                        '".$hoy."',
                        '2027'
                    )
                ";

                //echo $sentencia_frec;

                //mysqli_query($connect_kpis, $sentencia_frec );  
            }
            
            echo '
            <tr>
                <td>'.$count.'</td>
                <td>'.$data["indicador"].'</td>
            </tr>
            ';

            $count++;
        }
        */

        $count = 1;
        $array_cargos = [];
        $query = mysqli_query($connect_kpis, "SELECT * FROM Kpis WHERE anio = 2027 AND id_empresa = 1   ");
        while($data = mysqli_fetch_array($query)){ 

            $queryCol = mysqli_query($connect_kpis, "SELECT * FROM Kpis_Colaborador WHERE id_kpi = '".$data["id_borrar"]."' ");
            while($dataCol = mysqli_fetch_array($queryCol)){ 

                
                $sentencia_kpis_colaborador = "
                INSERT INTO Kpis_Colaborador(
                    id_empresa,
                    id_empleado,
                    id_kpi,
                    anio,
                    area_macro,
                    area_proceso,
                    id_colaborador,
                    tipo,
                    created_at,
                    updated_at
                )
                VALUES(
                    '".$dataCol["id_empresa"]."',
                    '".$dataCol["id_empleado"]."',
                    '".$data["id"]."',
                    '2027',
                    '".$dataCol["area_macro"]."',
                    '".$dataCol["area_proceso"]."',
                    '".$dataCol["id_colaborador"]."',
                    '".$dataCol["tipo"]."',
                    '".$hoy."',
                    '".$hoy."'
                )
                ";

                //mysqli_query($connect_kpis, $sentencia_kpis_colaborador ); 
                

            }


            

        
            

           
            
            echo '
            <tr>
                <td>'.$count.'</td>
                <td>'.$data["indicador"].'</td>
            </tr>
            ';

            $count++;
     
        }
        


        ?>  
        
    </table>
</div>