<?php


class Okrs {

    //PARA OBTENER LOS RESULTADOS DE OKRS
    //PARA OBTENER LOS RESULTADOS DE OKRS
    //PARA OBTENER LOS RESULTADOS DE OKRS
    public function ResultadoOkrs($id_empleado, $id_empresa, $anio){ 
        
        global $connect_okrs;
        $promedio = 0;
        $cantidad = 0;
        $okrs = 0;

        $sentencia = "SELECT o.id as idOkrs FROM Okrs_Equipos oe INNER JOIN Okrs o on o.id = oe.id_okrs WHERE oe.id_empleado =  $id_empleado and o.anio='$anio' and o.id_empresa = $id_empresa GROUP BY o.id "; 
        $query = mysqli_query( $connect_okrs , $sentencia);
		while($data = mysqli_fetch_array($query)){ 

            $resultados =  $this->OkrsReporteUsuarioDesempenio($data["idOkrs"],$_SESSION["id_empresa"]);
            
            $cantidad++;
            $promedio += round($resultados["promedio"]);

        }

        if( $cantidad == 0 ){
            $okrs = 0;
        }
        else{
            $okrs = round( ($promedio / $cantidad), 2);
        }

        return $okrs;
    }

    public function OkrsReporteUsuarioDesempenio($okr,$empresa_id){ 

        global $connect_okrs;
        
        $suma_resultado = 0;
        $conteo_resultado = 0;
        $resultado_prom_okr = 0;
        $porcentaje = 0;

        $sentencia = "SELECT id as idRk, avance as avanceRK, tendencia as Tendencia, meta as metaRK FROM Okrs_Resultados WHERE id_okrs = $okr"; 
        $query = mysqli_query( $connect_okrs , $sentencia);
		while($data = mysqli_fetch_array($query)){ 

            $avance_raw = $this->normalizarNumero($data["avanceRK"]);
            $meta_raw   = $this->normalizarNumero($data["metaRK"]); 

            $avance_raw = abs($avance_raw);
            $meta_raw = abs($meta_raw);

            $avance = isset($avance_raw) && is_numeric($avance_raw) && $avance_raw > 0 ? floatval($avance_raw) : 0;
            $meta   = isset($meta_raw ) && is_numeric($meta_raw ) && $meta_raw  > 0 ? floatval($meta_raw ) : 0;

            // Si alguno es 0, salta el cálculo (sigue con el siguiente KR)
            if ($avance == 0 || $meta == 0) {
                $porcentaje = 0;
                $conteo_resultado++;
                // echo "Conteo de resultados: ".$conteo_resultado. "\n";
                //continue;
            }

            $porcentaje = ($avance  * 100) / $meta;

   

            if ($data["Tendencia"] == 2) {
                //$porcentaje = ($meta / $avance  * 100);
                $porcentaje = ($meta *100) / $avance ;

                
            }

            if (is_infinite($porcentaje) || is_nan($porcentaje)) {
                $porcentaje = 0;
            }

            if ($porcentaje > 100) {
                $porcentaje = 100;
            }

            $suma_resultado = $suma_resultado + $porcentaje;
            // echo "Suma resultado: ".$suma_resultado . "\n";
            $conteo_resultado++;
            // echo "Conteo de resultados: ".$conteo_resultado. "\n";
        }


        
        /*
        foreach ($result as $key => $value) {
            //Normalizar valores
            $avance_raw = $this->normalizarNumero($value["avanceRK"]);
            $meta_raw   = $this->normalizarNumero($value["metaRK"]);
            // echo "Valor Inicial: Avance ".$avance_raw." Meta ".$meta_raw."\n";

            $avance = isset($avance_raw) && is_numeric($avance_raw) && $avance_raw > 0 ? floatval($avance_raw) : 0;
            $meta   = isset($meta_raw ) && is_numeric($meta_raw ) && $meta_raw  > 0 ? floatval($meta_raw ) : 0;

            // Si alguno es 0, salta el cálculo (sigue con el siguiente KR)
            if ($avance == 0 || $meta == 0) {
                $porcentaje = 0;
                $conteo_resultado++;
                // echo "Conteo de resultados: ".$conteo_resultado. "\n";
                continue;
            }

            $porcentaje = ($avance  * 100) / $meta;

            if ($value["Tendencia"] == 2) {
                $porcentaje = ($meta / $avance  * 100);
            }

            if (is_infinite($porcentaje) || is_nan($porcentaje)) {
                $porcentaje = 0;
            }

            if ($porcentaje > 100) {
                $porcentaje = 100;
            }

            $suma_resultado = $suma_resultado + $porcentaje;
            // echo "Suma resultado: ".$suma_resultado . "\n";
            $conteo_resultado++;
            // echo "Conteo de resultados: ".$conteo_resultado. "\n";
        }
        */


        // USAMOS EL CONTADOR MANUAL, NO rowCount()
        $totalResultados = $conteo_resultado;
        $resultado_prom_okr = ($totalResultados > 0) ? ($suma_resultado / $totalResultados) : 0;
        // echo "Resultado Promedio OKR: ".$resultado_prom_okr. "\n";
        if (is_infinite($resultado_prom_okr) || is_nan($resultado_prom_okr)) {
            $resultado_prom_okr = 0;
        }

        return [
            "promedio" => $resultado_prom_okr,
            "no_resultados" => $totalResultados
        ];
    }



    function normalizarNumero($valor)
    {
        $valor = trim($valor);

        if (is_numeric($valor)) {
            return $valor;
        }

        $valor = str_replace(' ', '', $valor); // eliminar espacios
        $coma = strrpos($valor, ',');
        $punto = strrpos($valor, '.');

        if ($coma !== false && $punto !== false) {
            // Ambos existen: determinar por posición
            if ($coma > $punto) {
                // Formato europeo: 1.234,56
                $valor = str_replace('.', '', $valor);
                $valor = str_replace(',', '.', $valor);
            } else {
                // Formato americano: 1,234.56
                $valor = str_replace(',', '', $valor);
            }
        } elseif ($coma !== false) {
            // Solo coma: ¿es decimal o miles?
            $decimales = substr($valor, $coma + 1);
            if (strlen($decimales) <= 2) {
                // Es decimal → cambiar a punto
                $valor = str_replace(',', '.', $valor);
            } else {
                // Es separador de miles → eliminar
                $valor = str_replace(',', '', $valor);
            }
        } elseif ($punto !== false) {
            // Solo punto: ¿es decimal o miles?
            $decimales = substr($valor, $punto + 1);
            if (strlen($decimales) > 2) {
                // Es separador de miles → eliminar
                $valor = str_replace('.', '', $valor);
            }
            // Si tiene 1 o 2 cifras, se asume decimal y se deja como está
        }

        return $valor;
    }
























































    public function CalcularMes($meta, $avance, $tipo_calculo){

        $porcentaje = 0;

        if($meta != 0){
            if($avance != 0){
                if($tipo_calculo == 1){
                    $porcentaje = $avance*100/$meta;
                }
                if($tipo_calculo == 2){
                    $porcentaje = $meta*100/$avance;
                    
                    
                }
            }
        }

        //ANDREY INDICA QUE SI LA TENDENCIA ES ABSOLUTA Y EL AVANCE  0  ES IGUAL A (100%) SI ES DIFERENTE DE 0 EL PORCENTAJE ES IGUAL A (0%)
        if($tipo_calculo == 3){
            if($avance != 0){
                $porcentaje = 0;
            }  
            if($avance == 0){
                $porcentaje = 100;
            }  
        }

        
        if($porcentaje > 100){
            $porcentaje = 100;
        }

        if($porcentaje < -100){
            $porcentaje = -100;
        }
        


        return $porcentaje;
    }

    public function ConvertirHorasMinutosSegundos($valor){
        $tiempo_partes =  explode( ":", $valor );
        $hor = $tiempo_partes[0]*60*60; 
        $min = $tiempo_partes[1]*60; 
        $seg = $tiempo_partes[2]; 

        $total_seg = $hor+$min+$seg;
        return $total_seg;
    }
    
}
