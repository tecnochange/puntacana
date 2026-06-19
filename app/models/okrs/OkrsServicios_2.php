<?php
class OkrsServicios_2{

    public function QueryOKRs(){

    }

    //TODOS LOS OKRS POR OBJETIVOS ESTRATÉGICOS
    //TODOS LOS OKRS POR OBJETIVOS ESTRATÉGICOS
    //TODOS LOS OKRS POR OBJETIVOS ESTRATÉGICOS
    //TODOS LOS OKRS POR OBJETIVOS ESTRATÉGICOS
    public function okrs_objetivos_estrategicos($id_empresa, $id_estrategico, $anio){
        
        global $connect_okrs;
        $array_okrs = array();

        $sentencia = "
        SELECT
            Okrs.id AS id, Okrs.objetivo_okr AS objetivo_okr, 
            Objetivos_estrategicos.objetivo AS nombre_objetivo_estrategico
        FROM
            Okrs 
        LEFT JOIN Objetivos_estrategicos ON Objetivos_estrategicos.id = Okrs.objetivos_estrategicos 
        WHERE
            Okrs.objetivos_estrategicos = '" . $id_estrategico . "' AND Okrs.anio = '".$anio."'
        ";

        $query = mysqli_query($connect_okrs, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) { 

            $avance = $this->resultados_okrs( $data["id"] );

            $nodo = array(
                "id" => $data["id"],
                "objetivo_okr" => $data["objetivo_okr"], 
                "avance" => $avance
            );

            array_push($array_okrs, $nodo);
        }

        return $array_okrs;

    }

    
    //TODOS LOS OKRS POR AREA
    //TODOS LOS OKRS POR AREA
    //TODOS LOS OKRS POR AREA
    //TODOS LOS OKRS POR AREA
    public function okrs_areas($id_empresa, $id_area, $anio){
        
        global $connect_okrs;
        $array_okrs = array();

        $sentencia = "
        SELECT
            Okrs.id AS id, Okrs.objetivo_okr AS objetivo_okr, 
            Okrs_Areas.id_area AS id_area,
            Objetivos_estrategicos.objetivo AS nombre_objetivo_estrategico
        FROM
            Okrs 
            LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = Okrs.id
        LEFT JOIN Objetivos_estrategicos ON Objetivos_estrategicos.id = Okrs.objetivos_estrategicos
        LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs.id
        WHERE
            Okrs.objetivos_estrategicos = '".$id_vicepresidencia."' AND Okrs.anio = '".$anio."'
        ";

        $query = mysqli_query($connect_okrs, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) { 

            $avance = $this->resultados_okrs( $data["id"] );

            $nodo = array(
                "id" => $data["id"],
                "objetivo_okr" => $data["objetivo_okr"], 
                "avance" => $avance
            );

            array_push($array_okrs, $nodo);
        }

        return $array_okrs;

    }
    
/*
    //TODOS LOS OKRS POR VICEPRESIDENCIA
    //TODOS LOS OKRS POR VICEPRESIDENCIA
    //TODOS LOS OKRS POR VICEPRESIDENCIA
    //TODOS LOS OKRS POR VICEPRESIDENCIA
    public function okrs_areas($id_empresa, $id_area, $anio){
        
        global $connect_okrs;
        $array_okrs = array();

        $sentencia = "
        SELECT
            Okrs.id AS id, Okrs.objetivo_okr AS objetivo_okr, 
            Okrs_Areas.id_area AS id_area,
            Objetivos_estrategicos.objetivo AS nombre_objetivo_estrategico
        FROM
            Okrs 
            LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = Okrs.id
        LEFT JOIN Objetivos_estrategicos ON Objetivos_estrategicos.id = Okrs.objetivos_estrategicos
        LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs.id
        WHERE
            Okrs.objetivos_estrategicos = '".$id_vicepresidencia."' AND Okrs.anio = '".$anio."'
        ";

        $query = mysqli_query($connect_okrs, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) { 

            $avance = $this->resultados_okrs( $data["id"] );

            $nodo = array(
                "id" => $data["id"],
                "objetivo_okr" => $data["objetivo_okr"], 
                "avance" => $avance
            );

            array_push($array_okrs, $nodo);
        }

        return $array_okrs;

    }
        */

    //RESULTADO DE UN OKRS
    //RESULTADO DE UN OKRS
    //RESULTADO DE UN OKRS
    //RESULTADO DE UN OKRS
    public function resultados_okrs($id_okrs){

        global $connect_okrs;
        
        $porcentaje_global = 0;
        $conteno_global = 0;

        $sentencia = "
        SELECT 
            avance, 
            tendencia, 
            meta 
        FROM Okrs_Resultados 
            WHERE id_okrs = '".$id_okrs."'
        ";
        $query = mysqli_query( $connect_okrs , $sentencia);
		while($data = mysqli_fetch_array($query)){ 

            $porcentaje = $this->avance_okrs($data);

            $porcentaje_global += $porcentaje;
            $conteno_global++;
        }

        //VALIDAMOS SI EXISTE ALGO
        if($porcentaje_global != 0){
            $porcentaje_global = $porcentaje_global/$conteno_global;
        }

        return round($porcentaje_global);
    }









    //PORCEJATE DE AVANCE DEL OKRS
    //FUNCTION PARA CALCULAR EL PORCENTAJE DE AVANCE DE UN RESULTADO CLAVE - SE PUEDE USAR EN 1 O VARIOS OKRS
    //FUNCTION PARA CALCULAR EL PORCENTAJE DE AVANCE DE UN RESULTADO CLAVE - SE PUEDE USAR EN 1 O VARIOS OKRS
    public function avance_okrs($data){
        $porcentaje = 0;
        if( $data["meta"] ){
            $avance = $data["avance"];
            $meta  = $data["meta"];

            //PARA LOS CASOS ASCENDENTES
            $porcentaje = ($avance *100) / $meta;

            //SOLO PARA LOS CASOS DONDE LA META ES NEGATIVA Y LA TENDENCIA ASCENDENTE
            if( $meta < 0){
                if($avance > $meta){
                    $porcentaje = 100;
                }
            }

            //PARA LOS CASOS DESENTENTES
            if ($data["tendencia"] == 2) {
                $porcentaje = ($meta *100) / $avance ;
            }

            if ($porcentaje > 100) {
                $porcentaje = 100;
            }
        }

        else{
            $porcentaje = 0;
        }

        if(is_nan($porcentaje)){
            $porcentaje = 0;
        }

        return $porcentaje;
    }


    

}
