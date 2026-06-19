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
        $query = mysqli_query($connect_valoracion, "SELECT * FROM Perfiles_Cargos WHERE id_ciclo = 17 ");
        while($data = mysqli_fetch_array($query)){ 

            //anterior
            $queryCargos = mysqli_query($connect_admin, "SELECT * FROM Cargos WHERE id = '" .$data["id_cargo"]. "' ");

            if($queryCargos->num_rows > 0){

            



                $comp_niveles = explode(",", $data["perfiles"]); 

                $lista_niveles = '';

                $nuevas_niveles = array();

                foreach( $comp_niveles as $nivel){ 

                    //anterior
                    $queryNivel = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Niveles WHERE id = '" .$nivel. "' ");
                    $dataNivel = mysqli_fetch_array($queryNivel); 

                    $queryN = mysqli_query($connect_valoracion, "SELECT * FROM Niveles WHERE id = '" . $dataNivel["id_nivel"] . "' ");
                    $dataN = mysqli_fetch_array($queryN);

                    $queryCompetencia = mysqli_query($connect_valoracion, "SELECT * FROM Competencias WHERE id = '" . $dataNivel["id_competencia"] . "' ");
                    $dataCompetencia = mysqli_fetch_array($queryCompetencia);






                    //NUEVOS
                    $queryN_New = mysqli_query($connect_valoracion, "SELECT * FROM Niveles WHERE anio = 2026 AND nombre = '" . $dataN["nombre"] . "' ");
                    $dataN_New = mysqli_fetch_array($queryN_New);

                    $queryCompetencia_New = mysqli_query($connect_valoracion, "SELECT * FROM Competencias WHERE anio = 2026 AND nombre = '" . $dataCompetencia["nombre"] . "' ");
                    $dataCompetencia_New = mysqli_fetch_array($queryCompetencia_New);



                    $queryNivel_New = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Niveles WHERE anio = 2026 AND id_nivel = '" . $dataN_New["id"] . "' AND id_competencia = '".$dataCompetencia_New["id"]."' ");
                    $dataNivel_New = mysqli_fetch_array($queryNivel_New);



                    $lista_niveles .= $dataNivel["id"].' - '.$dataNivel_New["id"].'<br>';

                    if($dataNivel_New["id"] > 0){
                        array_push($nuevas_niveles, $dataNivel_New["id"]);
                    }

                    

                }

                
                //NUEVOS PERFILES
                $perfiles_nuevos = implode(",", $nuevas_niveles);

                $sentencia_crear = "
                INSERT INTO Perfiles_Cargos(
                    anio,
                    id_ciclo,
                    id_empresa,
                    id_cargo,
                    perfiles,
                    created_at
                )
                VALUES(
                    2026,
                    21,
                    1,
                    '".$data["id_cargo"]."',
                    '".$perfiles_nuevos."',
                    '".$hoy."'
                )
                ";
                //mysqli_query($connect_valoracion, $sentencia_crear );
                //echo $sentencia_crear; 

                echo '
                <tr>
                        <td>'.$count.'</td>
                        <td>'.$data["id_cargo"].'</td>
                        <td>'.count($comp_niveles).'</td>
                        <td>'.$lista_niveles.'</td>
                        
                        
                    </tr>
                ';

                $count++;

            }
            

           

            



           
        }
        */

        
        //AUTO Y JEFE
        $sentencia = "
        SELECT * FROM Empleados WHERE id_empresa = 1 AND estado = 1 AND fecha_ingreso <= '2025-10-29' 
        ";

        $count = 1;
        $query = mysqli_query($connect_admin, $sentencia );
        while($data = mysqli_fetch_array($query)){ 

            /*
            $sentencia_auto = "
            INSERT INTO Evaluadores(
                id_empresa,
                anio,
                id_ciclo,
                id_empleado,
                id_evaluador,
                tipo,
                created_at
            )
            VALUES(
                1,
                2026,
                21,
                '".$data["id"]."',
                '".$data["id"]."',
                1,
                '".$hoy."'
            )
            ";
            //echo $sentencia_auto;
            mysqli_query( $connect_valoracion, $sentencia_auto );
            */

           
            //JEFE
            /*
            $queryJefe = mysqli_query($connect_admin, "SELECT * FROM Lideres WHERE id_empleado = '" .$data["id"]. "' ORDER BY id DESC   ");
            $dataJefe = mysqli_fetch_array($queryJefe);

            if($queryJefe->num_rows > 0){

                $sentencia_jefe = "
                INSERT INTO Evaluadores(
                    id_empresa,
                    anio,
                    id_ciclo,
                    id_empleado,
                    id_evaluador,
                    tipo,
                    created_at
                )
                VALUES(
                    1,
                    2026,
                    21,
                    '".$data["id"]."',
                    '".$dataJefe["id_jefe"]."',
                    5,
                    '".$hoy."'
                )
                ";
                //echo $sentencia_jefe;
                mysqli_query( $connect_valoracion, $sentencia_jefe );

            }
            */
            /*
            echo '
                <tr>
                        <td>'.$count.'</td>
                        <td>'.$data["nombre"].'</td>
                        <td>'.$tiene_perfil.'</td>
                        
                        
                    </tr>
            ';

            $count++;
            */
           
        }
    









        /*
        //PARES ORIGINALES: LIDERES 
        //PARA LOS QUE VAN CON PAR Y COLABORADOR:   AND nivel_jerarquico IN (1, 5)
        //PARA LOS QUE VAN CON PAR Y COLABORADOR:   AND nivel_jerarquico IN (1, 5)
        //PARA LOS QUE VAN CON PAR Y COLABORADOR:   AND nivel_jerarquico IN (1, 5)
        //PARA LOS QUE VAN CON PAR Y COLABORADOR:   AND nivel_jerarquico IN (1, 5)
        //PARA LOS QUE VAN CON PAR Y COLABORADOR:   AND nivel_jerarquico IN (1, 5)
        $sentencia = "
        SELECT * FROM Empleados WHERE id_empresa = 1 AND estado = 1 AND fecha_ingreso <= '2025-10-29' AND nivel_jerarquico IN (1, 5) 
        ";
        $count = 1;
        $query = mysqli_query($connect_admin, $sentencia );
        while($data = mysqli_fetch_array($query)){ 

            

            $queryJefe = mysqli_query($connect_admin, "SELECT * FROM Lideres WHERE id_empleado = '" .$data["id"]. "' ORDER BY id DESC   ");
            $dataJefe = mysqli_fetch_array($queryJefe);

            //CREAMOS ARREGLOS PARA LAS MATRICES
			$ARRAY_MATRIZ = array();
			$array_pares = array();

            //OBTENEMOS EL EQUIPO
            //OBTENEMOS EL EQUIPO
            //OBTENEMOS EL EQUIPO
            $sentencia_pares = "
                SELECT
                    *
                FROM
                    Lideres    
                WHERE
                    id_jefe = '".$dataJefe["id_jefe"]."' AND id_empleado != '".$data["id"]."'
                    ORDER BY id DESC
            ";
            $queryPares = mysqli_query($connect_admin, $sentencia_pares ); 
            while($dataPares = mysqli_fetch_array($queryPares)){
                array_push($array_pares, $dataPares["id_empleado"]);
            } 

                    

            //INICIAMOS EN EL ARMADO DE LA MATRIZ
            //INICIAMOS EN EL ARMADO DE LA MATRIZ
            //INICIAMOS EN EL ARMADO DE LA MATRIZ
            //INICIAMOS EN EL ARMADO DE LA MATRIZ			
			$contador = 1;
							
			//RECOREMOS EL EQUPO
			foreach($array_pares as $evaluador){

            
									
					//VALIDAMOS QUE ESTE EVALUADOR NO TENGA MAS DE 3 EVALUACIONES ASIGNADAS
					$coun_evaluado = 1;
					foreach($ARRAY_MATRIZ as $matriz){
						if( $matriz["id_evaluado"] == $evaluador ){
							$coun_evaluado++;
						}
					}
									
					///SI ES MENOR O IGUAL AGREGARMOS EL NODO
					if($coun_evaluado <= 1){
									
						if($contador <= 2){

							$nodo = array(
												"id_evaluado" => $data["id"], 
												"id_evaluador" => $evaluador
							);

							array_push($ARRAY_MATRIZ, $nodo);
							$contador++;

						}
					}

				
								
			}
							

		






    
            $lista_pares = "";
            //FINALMENTE RECORREMOS EL NODO
            //FINALMENTE RECORREMOS EL NODO
            //FINALMENTE RECORREMOS EL NODO
            foreach($ARRAY_MATRIZ as $evaluaciones){
                                $ya_existe = '';
                                $queryValExiste = mysqli_query($connect_valoracion,"SELECT * FROM Evaluadores 
                                WHERE id_empleado = '".$evaluaciones["id_evaluado"]."' AND id_evaluador = '".$evaluaciones["id_evaluador"]."' AND anio = '2026' AND id_ciclo =  '21' AND tipo = 2 ");
                                if(
                                    $queryValExiste->num_rows > 0){ $ya_existe = ' - Ya tiene - '; 
                                }
                                else{

                                $sentencia_eval = "INSERT INTO Evaluadores (id_empresa, anio, id_ciclo, id_empleado, id_evaluador, tipo, created_at ) 
                                    VALUES 
                                    ( '1', '2026', '21', '".$evaluaciones["id_evaluado"]."', '".$evaluaciones["id_evaluador"]."', 2, '".$hoy."' ) ";
                                    //echo $sentencia_eval;
                                    
                                    mysqli_query($connect_valoracion, $sentencia_eval);
                                }
                                
                                $lista_pares .= $evaluaciones["id_evaluador"]." - ". $evaluaciones["id_evaluado"].$ya_existe."<br>";
            }
            

            


            echo '
                <tr>
                        <td>'.$count.'</td>
                        <td>'.$data["id"].'</td>
                        <td>'.$data["nombre"].'</td>
                        <td>'.$lista_pares.'</td>
                        
                        
                    </tr>
            ';

            $count++;
           
        }
        */



/*

//AND Empleados.id =  374 

        //PARES POR UNIDADA CORPORATIVA
        //PARES POR UNIDADA CORPORATIVA
        //PARA LOS QUE VAN CON PAR Y COLABORADOR:   AND nivel_jerarquico IN (1, 5)
        //PARA LOS QUE VAN CON PAR Y COLABORADOR:   AND nivel_jerarquico IN (1, 5)
        //PARA LOS QUE VAN CON PAR Y COLABORADOR:   AND nivel_jerarquico IN (1, 5)
        //PARA LOS QUE VAN CON PAR Y COLABORADOR:   AND nivel_jerarquico IN (1, 5)
        //PARA LOS QUE VAN CON PAR Y COLABORADOR:   AND nivel_jerarquico IN (1, 5)
        //AND id = 105 LIMIT 10 
        $sentencia = "
        SELECT * FROM Empleados WHERE id_empresa = 1 AND estado = 1 AND fecha_ingreso <= '2025-11-04' AND nivel_jerarquico IN (1, 5) 
        ";
        $count = 1;
        $query = mysqli_query($connect_admin, $sentencia );
        while($data = mysqli_fetch_array($query)){ 

            //CREAMOS ARREGLOS PARA LAS MATRICES
			$ARRAY_MATRIZ = array();
			$array_pares = array();

            //1.
            $sentencia_pares = "
            SELECT * FROM Empleados WHERE id_empresa = 1 AND estado = 1 AND fecha_ingreso <= '2025-11-04'  
            AND unidad_corporativa = '".$data["unidad_corporativa"]."' AND nivel_jerarquico = '".$data["nivel_jerarquico"]."' AND id != '".$data["id"]."'
            ";
            $count = 1;
            $queryPares = mysqli_query($connect_admin, $sentencia_pares );
            while($dataPares = mysqli_fetch_array($queryPares)){  
                array_push($array_pares, $dataPares["id"]);
            }

            
            //INICIAMOS EN EL ARMADO DE LA MATRIZ
            //INICIAMOS EN EL ARMADO DE LA MATRIZ
            //INICIAMOS EN EL ARMADO DE LA MATRIZ
            //INICIAMOS EN EL ARMADO DE LA MATRIZ			
			$contador = 1;
			//1.				
			//RECOREMOS EL EQUPO
			foreach($array_pares as $evaluador){

					//VALIDAMOS QUE ESTE EVALUADOR NO TENGA MAS DE 3 EVALUACIONES ASIGNADAS
					$coun_evaluado = 1;
					foreach($ARRAY_MATRIZ as $matriz){
						if( $matriz["id_evaluado"] == $evaluador ){
							$coun_evaluado++;
						}
					}
									
					///SI ES MENOR O IGUAL AGREGARMOS EL NODO
					if($coun_evaluado <= 1){
						if($contador <= 2){
							$nodo = array(
								"id_evaluado" => $data["id"], 
								"id_evaluador" => $evaluador
							);

							array_push($ARRAY_MATRIZ, $nodo);
							$contador++;
						}
					}
			}
							

		






            $lista_pares = "";
            //FINALMENTE RECORREMOS EL NODO
            //FINALMENTE RECORREMOS EL NODO
            //FINALMENTE RECORREMOS EL NODO
            foreach($ARRAY_MATRIZ as $evaluaciones){
                $ya_existe = '';
                $queryValExiste = mysqli_query($connect_valoracion,"SELECT * FROM Evaluadores_PRUEBAS 
                WHERE  id_evaluador = '".$evaluaciones["id_evaluador"]."' AND anio = '2026' AND id_ciclo =  '21' AND tipo = 2 ");
                if($queryValExiste->num_rows <= 1){ 

                    //$ya_existe = ' - NO_tiene - '; 

                    $sentencia_eval = "INSERT INTO Evaluadores_PRUEBAS (id_empresa, anio, id_ciclo, id_empleado, id_evaluador, tipo, created_at ) 
                    VALUES 
                    ( '1', '2026', '21', '".$evaluaciones["id_evaluado"]."', '".$evaluaciones["id_evaluador"]."', 2, '".$hoy."' ) ";
                    //echo $sentencia_eval;
                                    
                    mysqli_query($connect_valoracion, $sentencia_eval);

                    
                }
                else{
                    $ya_existe = ' - Ya_tiene - '; 
                }
                                
                $lista_pares .= $evaluaciones["id_evaluador"]." - ". $evaluaciones["id_evaluado"].$ya_existe."<br>";
            }
            

            


            echo '
                <tr>
                        <td>'.$count.'</td>
                        <td>'.$data["id"].'</td>
                        <td>'.$data["nombre"].'</td>
                        <td>'.$lista_pares.'</td>
                        
                        
                    </tr>
            ';

            $count++;
           
        }
*/


        /*
        //FUNCIONA
        //PARES POR UNIDADA CORPORATIVA
        //PARES POR UNIDADA CORPORATIVA
        //PARA LOS QUE VAN CON PAR Y COLABORADOR:   AND nivel_jerarquico IN (1, 5)
        //PARA LOS QUE VAN CON PAR Y COLABORADOR:   AND nivel_jerarquico IN (1, 5)
        //PARA LOS QUE VAN CON PAR Y COLABORADOR:   AND nivel_jerarquico IN (1, 5)
        //PARA LOS QUE VAN CON PAR Y COLABORADOR:   AND nivel_jerarquico IN (1, 5)
        //PARA LOS QUE VAN CON PAR Y COLABORADOR:   AND nivel_jerarquico IN (1, 5)
        //AND id = 105 LIMIT 10 
        $sentencia = "
        SELECT * FROM Empleados WHERE id_empresa = 1 AND estado = 1 AND fecha_ingreso <= '2025-11-04' AND nivel_jerarquico IN (1, 5)   AND id = 128
        ";
        $count = 1;
        $query = mysqli_query($connect_admin, $sentencia );
        while($data = mysqli_fetch_array($query)){ 

            //CREAMOS ARREGLOS PARA LAS MATRICES
			$ARRAY_MATRIZ = array();
			$array_pares = array();

            //1.
            $sentencia_pares = "
            SELECT * FROM Empleados WHERE id_empresa = 1 AND estado = 1 AND fecha_ingreso <= '2025-11-04'  
            AND unidad_corporativa = '".$data["unidad_corporativa"]."' AND nivel_jerarquico = '".$data["nivel_jerarquico"]."' AND id != '".$data["id"]."'
            ";
            $queryPares = mysqli_query($connect_admin, $sentencia_pares );

            if($queryPares->num_rows >= 2){ 

                while($dataPares = mysqli_fetch_array($queryPares)){  

                    //VALIDAR QUE EL EVALUADO NO TENGA MAS DE 2 PARES
                    $queryValPares = mysqli_query($connect_valoracion,"SELECT * FROM Evaluadores_PRUEBAS 
                    WHERE  id_empleado = '".$data["id"]."' AND anio = '2026' AND id_ciclo =  '21' AND tipo = 2 ");
                    if($queryValPares->num_rows <= 1){ 

                        //VALIDAMOS QUE EL PAR NO TENGA MAS DE 2 EVALUACIONES
                        $queryValExiste = mysqli_query($connect_valoracion,"SELECT * FROM Evaluadores_PRUEBAS 
                        WHERE  id_evaluador = '".$dataPares["id"]."' AND anio = '2026' AND id_ciclo =  '21' AND tipo = 2 ");
                        if($queryValExiste->num_rows <= 1){ 

                            $sentencia_eval = "INSERT INTO Evaluadores_PRUEBAS (id_empresa, anio, id_ciclo, id_empleado, id_evaluador, tipo, created_at ) 
                            VALUES 
                            ( '1', '2026', '21', '".$data["id"]."', '".$dataPares["id"]."', 2, '".$hoy."' ) ";
                            //echo $sentencia_eval;
                                            
                            //mysqli_query($connect_valoracion, $sentencia_eval);
                        }
                        
                    }

                    
                    
                }


                echo '
                    <tr>
                            <td>'.$count.'</td>
                            <td>'.$data["id"].'</td>
                            <td>'.$data["nombre"].'</td>
                            <td>'.$lista_pares.'</td>
                            
                            
                        </tr>
                ';

                $count++;

            }


            

            
           
        }
            */
















        //SOLO PARA VER PARES
        //SOLO PARA VER PARES
        //SOLO PARA VER PARES
        //SOLO PARA VER PARES
        //SOLO PARA VER PARES
        //SOLO PARA VER PARES
        //SOLO PARA VER PARES
        $sentencia = "
        SELECT * FROM Empleados WHERE id_empresa = 1 AND estado = 1 AND fecha_ingreso <= '2025-11-04' AND nivel_jerarquico IN (1, 5)   AND id = 18
        ";
        $count = 1;
        $query = mysqli_query($connect_admin, $sentencia );
        while($data = mysqli_fetch_array($query)){ 

            //CREAMOS ARREGLOS PARA LAS MATRICES
			$ARRAY_MATRIZ = array();
			$array_pares = array();

            //1.
            $sentencia_pares = "
            SELECT * FROM Empleados WHERE id_empresa = 1 AND estado = 1 AND fecha_ingreso <= '2025-11-04'  
            AND unidad_corporativa = '".$data["unidad_corporativa"]."' AND nivel_jerarquico = '".$data["nivel_jerarquico"]."' AND id != '".$data["id"]."'
            ";
            $queryPares = mysqli_query($connect_admin, $sentencia_pares );

   
            while($dataPares = mysqli_fetch_array($queryPares)){  


                    echo '
                        <tr>
                                <td>'.$count.'</td>
                                <td>'.$data["id"].'</td>
                                <td>'.$dataPares["nombre"].'</td>
                                <td>'.$dataPares["id"].'</td>
                                <td>'.$lista_pares.'</td>
                                
                                
                            </tr>
                    ';

                    $count++;


                    
                    
                }

                
            


            

            
           
        }



















            


        /*
        //PARES POR UNIDADA CORPORATIVA
        //PARES POR UNIDADA CORPORATIVA
        //PARA LOS QUE VAN CON PAR Y COLABORADOR:   AND nivel_jerarquico IN (1, 5)
        //PARA LOS QUE VAN CON PAR Y COLABORADOR:   AND nivel_jerarquico IN (1, 5)
        //PARA LOS QUE VAN CON PAR Y COLABORADOR:   AND nivel_jerarquico IN (1, 5)
        //PARA LOS QUE VAN CON PAR Y COLABORADOR:   AND nivel_jerarquico IN (1, 5)
        //PARA LOS QUE VAN CON PAR Y COLABORADOR:   AND nivel_jerarquico IN (1, 5)
        //AND id = 105 LIMIT 10 
        $sentencia = "
        SELECT * FROM Empleados WHERE id_empresa = 1 AND estado = 1 AND fecha_ingreso <= '2025-11-04' AND nivel_jerarquico IN (1, 5)  AND id = 2
        ";
        $count = 1;
        $query = mysqli_query($connect_admin, $sentencia );
        while($data = mysqli_fetch_array($query)){ 

            //CREAMOS ARREGLOS PARA LAS MATRICES
			$ARRAY_MATRIZ = array();
			$array_pares = array();

            //1.
            $sentencia_pares = "
            SELECT * FROM Empleados WHERE id_empresa = 1 AND estado = 1 AND fecha_ingreso <= '2025-11-04'  
            AND unidad_corporativa = '".$data["unidad_corporativa"]."' AND nivel_jerarquico = '".$data["nivel_jerarquico"]."' AND id != '".$data["id"]."'
            ";
            $queryPares = mysqli_query($connect_admin, $sentencia_pares );
            while($dataPares = mysqli_fetch_array($queryPares)){  

                echo '
                    <tr>
                            <td>'.$count.'</td>
                            <td>'.$data["nombre"].'</td>
                            <td>'.$data["id"].'</td>
                            
                            <td>'.$dataPares["nombre"].'</td>
                            <td>'.$dataPares["id"].'</td>
                            
                            
                            
                        </tr>
                ';

            $count++;
                

                
                
            }


            
           
        }
            */
        
        


        
        /*
        //SOLO COLABORADORES
        //SOLO COLABORADORES
        //SOLO COLABORADORES
        //PARA LOS QUE VAN CON PAR Y COLABORADOR:   AND nivel_jerarquico IN (1, 5)
        //PARA LOS QUE VAN CON PAR Y COLABORADOR:   AND nivel_jerarquico IN (1, 5)
        //PARA LOS QUE VAN CON PAR Y COLABORADOR:   AND nivel_jerarquico IN (1, 5)
        //PARA LOS QUE VAN CON PAR Y COLABORADOR:   AND nivel_jerarquico IN (1, 5)
        //PARA LOS QUE VAN CON PAR Y COLABORADOR:   AND nivel_jerarquico IN (1, 5)
        $sentencia = "
        SELECT * FROM Empleados WHERE id_empresa = 1 AND estado = 1 AND fecha_ingreso <= '2025-11-04' AND nivel_jerarquico IN (1, 5) 
        ";

        $count = 1;
        $query = mysqli_query($connect_admin, $sentencia );
        while($data = mysqli_fetch_array($query)){ 

            $sentencia_colaboradores = "
            SELECT * FROM Lideres 
            LEFT JOIN Empleados ON Empleados.id = Lideres.id_empleado
            WHERE Lideres.id_jefe = '" .$data["id"]. "' AND Empleados.fecha_ingreso <= '2025-11-04'
            ORDER BY RAND()
            LIMIT 2;
            ";
            $queryColaboradores = mysqli_query( $connect_admin, $sentencia_colaboradores );
            while($dataCol = mysqli_fetch_array($queryColaboradores)){
                
                $sentencia_col = "
                INSERT INTO Evaluadores(
                    id_empresa,
                    anio,
                    id_ciclo,
                    id_empleado,
                    id_evaluador,
                    tipo,
                    created_at
                )
                VALUES(
                    1,
                    2026,
                    21,
                    '".$data["id"]."',
                    '".$dataCol["id_empleado"]."',
                    3,
                    '".$hoy."'
                )
                ";
                //echo $sentencia_col;
                //mysqli_query( $connect_valoracion, $sentencia_col );
                
            }

            echo '
                <tr>
                        <td>'.$count.'</td>
                        <td>'.$data["id"].'</td>
                        <td>'.$data["nombre"].'</td>
                        <td>'.$lista_pares.'</td>
                        
                        
                    </tr>
            ';

            $count++;
           
        }
        */





      
        
        /*
        $count = 1;
        $query = mysqli_query($connect_valoracion, "SELECT * FROM aa_auto_eliminar_2026 " );
        while($data = mysqli_fetch_array($query)){ 

            $queryColaboradores = mysqli_query( $connect_admin, "SELECT * FROM Empleados WHERE documento = '".$data["COL1"]."'" );
            $dataColaborador = mysqli_fetch_array($queryColaboradores);


            if($queryColaboradores->num_rows > 0 ){

                $sentencia_col = " 
                DELETE FROM Evaluadores WHERE id_ciclo = 21 AND id_empleado = '".$dataColaborador["id"]."' AND tipo = 1
                ";
                //echo $sentencia_col;
                //mysqli_query( $connect_valoracion, $sentencia_col );
            }



            echo '
                <tr>
                        <td>'.$count.'</td>
                        <td>'.$dataColaborador["nombre"].'</td>
                        <td>'.$dataColaborador["id"].'</td>
                        <td>'.$lista_pares.'</td>
                        
                        
                    </tr>
            ';

            $count++;
           
        }
        */




        
        

        ?>  

        
    </table>
</div>