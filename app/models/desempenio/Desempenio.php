<?php
class Desempenio {

    //PARA OBTENER LOS RESULTADOS DE OKRS
    //PARA OBTENER LOS RESULTADOS DE OKRS
    //PARA OBTENER LOS RESULTADOS DE OKRS
    public function ResultadoDesempenio($okrs, $kpis, $competencias, $nivel){ 

        global $connect_admin;
        //OBTENEMOS LAS PONDERACIONES
        $queryPonderaciones = mysqli_query( $connect_admin , "SELECT * FROM Ponderar_Desempenio WHERE id_empresa = '".$_SESSION["id_empresa"]."' AND anio = '".$_SESSION["anio_fill"]."' AND nivel = '".$nivel."' " );
        $dataPonderaciones = mysqli_fetch_array($queryPonderaciones);

        //print_r($dataPonderaciones);

        $okrs_ponderado = 0;
        if($okrs > 0){ 
            $okrs_ponderado = $okrs*($dataPonderaciones["mod_okrs"]/100); 
        }

        $competencias_ponderado = 0;
        if($competencias > 0){ $competencias_ponderado = $competencias*($dataPonderaciones["mod_competencias"]/100); }

        $kpis_ponderado = 0;
        if( $kpis > 0 ){ $kpis_ponderado = $kpis*($dataPonderaciones["mod_kpis"]/100); }

        $total_ponderado = $okrs_ponderado+$competencias_ponderado+$kpis_ponderado;
        if($total_ponderado > 0){
            $total_ponderado = round($total_ponderado,2);
        }

        return $total_ponderado;
    }
  
}
