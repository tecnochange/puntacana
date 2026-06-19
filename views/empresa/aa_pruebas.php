<script>
$(document).ready(function() {
    $('#menuEmpresa').collapse();
    $('#bt_empresa_areas').addClass('active');
});
</script>

<?php
$hoy = date("Y-m-d H:i:s");

$array_registros = [];
$qryRegistros = mysqli_query($connect_okrs, "SELECT id, avance FROM Okrs_Iniciativas_punto_restauracion  "); //
while($dataValidar = mysqli_fetch_array($qryRegistros)){
    $array_registros[$dataValidar["id"]] = $dataValidar;
}


$array_colaboradores = [];
$qryCol = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id_empresa = 1 "); //
while($dataCol = mysqli_fetch_array($qryCol)){
    $array_colaboradores[$dataCol["id"]] = $dataCol;
}

//VICEPRESIDENCIAS
$array_vicepresidencias = [];
$qryVices = mysqli_query($connect_admin, "SELECT * FROM Vicepresidencia ");
while($dataVice = mysqli_fetch_array($qryVices)){ 
    $array_vicepresidencias[$dataVice["id"]] = $dataVice;
}

//AREAS
$array_areas = [];
$qryAreas = mysqli_query($connect_admin, "SELECT * FROM Areas ");
while($dataAreas = mysqli_fetch_array($qryAreas)){ 
    $array_areas[$dataAreas["id"]] = $dataAreas;
}




//CARGOS PARA CAMBIAR
/*
$array_cargos = [];
$qryCambiosCargos = mysqli_query($connect_admin, "SELECT * FROM aa_planta_marzo_2026_cargos ");
while($dataCambiosCargos = mysqli_fetch_array($qryCambiosCargos)){ 

    $qryCargos = mysqli_query($connect_admin, "SELECT * FROM Cargos WHERE nombre = '".$dataCambiosCargos["Cargo"]."' ");
    $dataCargos = mysqli_fetch_array($qryCargos); 

    $dataCambiosCargos["id_cargo"] = $dataCargos["id"];

    $array_cargos[$dataCambiosCargos["Documento"]] = $dataCambiosCargos;
}


//CARGOS PARA CAMBIAR
$array_cargos_plataforma = array();
$qryCargosPlataforma = mysqli_query($connect_admin, "SELECT * FROM Cargos ");
while($dataCargosPlataforma = mysqli_fetch_array($qryCargosPlataforma)){ 
    array_push($array_cargos_plataforma, $dataCargosPlataforma);
}
*/

/*
//CARGOS PARA CAMBIAR
$array_areas_plataforma = array();
$qryCargosPlataforma = mysqli_query($connect_admin, "SELECT * FROM aa_planta_marzo_2026_cargos ");
while($dataCargosPlataforma = mysqli_fetch_array($qryCargosPlataforma)){ 
    array_push($array_cargos_plataforma, $dataCargosPlataforma);
}
*/

?>



<div class="container">

    <table class="table table-bordered">

        <tr>
            <td>Id</td>
            <td>Año</td>
            <td>Kpis</td>
            <td>Indicador</td>

            <td>Vicepresidencia</td>
            <td>Área</td>

            <td>Equipo</td>
            <td>Pertencen</td>
            <td>No Pertenecen</td>
            <td>Para Validar</td>
        </tr>
        
        <?php 

        
        
        //CARGOS
        /*
        $qry = mysqli_query($connect_admin, "SELECT * FROM aa_planta_marzo_2026  ");
        while($data = mysqli_fetch_array($qry)){

            $qryEmpleado = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE documento = '".$data["Documento"]."' ");
            $dataEmpleado = mysqli_fetch_array($qryEmpleado);

            $cambio_cargo = "";
            $nombre_cargo = "";
            $crear_cargo = "";

            $sentencia_crear_cargo = "";
            
            
            //CAMBIO CARGO
            if( $array_cargos[$data["Documento"]]["Documento"] == $data["Documento"] ){
                $cambio_cargo = "Cambia_Cargo";
                $nombre_cargo = $array_cargos[$data["Documento"]]["id_cargo"]; 

                if($nombre_cargo == ""){
                    $crear_cargo = "Crear_Cargo_Nuevo";

                    $sentencia_crear_cargo = "
                    INSERT INTO Cargos(
                        id_empresa,
                        nivel_jerarquico,
                        nombre,
                        padre,
                        estado,
                        created_at,
                        updated_at
                    )
                    VALUES(
                        1,
                        '".$data["Nivel_Jerarquico"]."',
                        '".$data["Cargo"]."',
                        0,
                       
                        1,
                        '".$hoy."',
                        '".$hoy."'
                    )
                    ";

                    //mysqli_query($connect_admin, $sentencia_crear_cargo );
                }
            }

            if($cambio_cargo == 'Cambia_Cargo' && $qryEmpleado->num_rows > 0){
                //mysqli_query($connect_admin, "UPDATE Empleados SET id_cargo = '".$nombre_cargo."', cargo = '".$data["Cargo"]."'  WHERE id = '".$dataEmpleado["id"]."'  " );
            }
            

            echo '
            <tr>
                <td>'.$data["Documento"].'</td> 
                <td>'.$dataEmpleado["documento"].'</td>
                <td>'.$cambio_cargo.'</td>
                <td>'.$nombre_cargo.'</td> 

                <td>'.$data["Cargo"].'</td>
                <td>'.$crear_cargo.'</td>
            </tr>
            ';
        }
        */

        /*
        //validar cargo
        $qry = mysqli_query($connect_admin, "SELECT * FROM aa_planta_marzo_2026  ");
        while($data = mysqli_fetch_array($qry)){

            $qryEmpleado = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE documento = '".$data["Documento"]."' ");
            $dataEmpleado = mysqli_fetch_array($qryEmpleado);

            $id_cargo = 0;
            foreach( $array_cargos_plataforma as $cargo ){
                if($cargo["nombre"] == $data["Cargo"] ){
                    $id_cargo = $cargo["id"];
                }
            }

            $cambio_cargo = "";
            if($dataEmpleado["cargo"] !=  $data["Cargo"] ){
                $cambio_cargo = "Cambia_Cargo";
            }

            if( $cambio_cargo == 'Cambia_Cargo' && $qryEmpleado->num_rows > 0 ){
                //mysqli_query($connect_admin, "UPDATE Empleados SET id_cargo = '".$id_cargo."', cargo = '".$data["Cargo"]."'  WHERE id = '".$dataEmpleado["id"]."'  " );
            }
            

            echo '
            <tr>
                <td>'.$data["Documento"].'</td> 
                <td>'.$dataEmpleado["cargo"].'</td>
                <td>'.$data["Cargo"].'</td>
                <td>'.$cambio_cargo.'</td>
                <td>'.$id_cargo.'</td>
                

            </tr>
            ';
        }
        */
        
        /*
        //AREAS
        $qry = mysqli_query($connect_admin, "SELECT * FROM aa_planta_marzo_2026_areas  ");
        while($data = mysqli_fetch_array($qry)){

            $qryEmpleado = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE documento = '".$data["Documento"]."' ");
            $dataEmpleado = mysqli_fetch_array($qryEmpleado);

            $qryArea = mysqli_query($connect_admin, "SELECT * FROM Areas WHERE nombre = '".$data["Area"]."' AND id_empresa = 1 ");
            $dataArea = mysqli_fetch_array($qryArea);

            $existe_area = "No Existe";
            if($qryArea->num_rows > 0){
                $existe_area = "";
            } 

            if($existe_area == ""){
                //echo "UPDATE Empleados SET area = '".$dataArea["id"]."' WHERE id = '".$dataEmpleado["id"]."' ";

                mysqli_query($connect_admin, "UPDATE Empleados SET area = '".$dataArea["id"]."' WHERE id = '".$dataEmpleado["id"]."' ");
            }

            echo '
            <tr>
                <td>'.$data["Documento"].'</td> 
                <td>'.$dataEmpleado["documento"].'</td>
                <td>'.$data["Area"].'</td>
                <td>'.$existe_area.'</td>
                <td>'.$dataArea["id"].'</td>
                
            </tr>
            ';

        }
        */

        /*
        //UNIDAD
        $qry = mysqli_query($connect_admin, "SELECT * FROM aa_planta_marzo_2026_unidad_org WHERE Unidad_Organizativa != '' ");
        while($data = mysqli_fetch_array($qry)){

            $qryEmpleado = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE documento = '".$data["Documento"]."' ");
            $dataEmpleado = mysqli_fetch_array($qryEmpleado);

            $qryUnidad = mysqli_query($connect_admin, "SELECT * FROM Estructura_Empresa WHERE unidad_organizativa = '".$data["Unidad_Organizativa"]."' AND id_empresa = 1 ");
            $dataUnidad = mysqli_fetch_array($qryUnidad);

            $existe_area = "No Existe";
            if($qryUnidad->num_rows > 0){
                $existe_area = "";
            } 

            if($existe_area == ""){
                //echo "UPDATE Empleados SET unidad_organizativa = '".$dataUnidad["id"]."' WHERE id = '".$dataEmpleado["id"]."' ";

                mysqli_query($connect_admin, " UPDATE Empleados SET unidad_organizativa = '".$dataUnidad["id"]."' WHERE id = '".$dataEmpleado["id"]."' ");
            }

            echo '
            <tr>
                <td>'.$data["Documento"].'</td> 
                <td>'.$dataEmpleado["documento"].'</td>
                <td>'.$data["Unidad_Organizativa"].'</td>
                <td>'.$existe_area.'</td>
                <td>'.$dataUnidad["id"].'</td>
                
            </tr>
            ';

        }
        */

        /*
        //JERARQUICO
        $qry = mysqli_query($connect_admin, "SELECT * FROM aa_planta_marzo_2026_nivel_je ");
        while($data = mysqli_fetch_array($qry)){

            $qryEmpleado = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE documento = '".$data["Documento"]."' ");
            $dataEmpleado = mysqli_fetch_array($qryEmpleado);

            $qryNivel = mysqli_query($connect_admin, "SELECT * FROM Nivel_Jerarquico WHERE nombre = '".$data["Nivel_Jerarquico"]."' AND id_empresa = 1 ");
            $dataNivel = mysqli_fetch_array($qryNivel);

            $existe_area = "No Existe";
            if($qryNivel->num_rows > 0){
                $existe_area = "";
            } 

            if($existe_area == ""){
                //echo "UPDATE Empleados SET nivel_jerarquico = '".$dataNivel["id"]."' WHERE id = '".$dataEmpleado["id"]."' ";

                mysqli_query($connect_admin, " UPDATE Empleados SET nivel_jerarquico = '".$dataNivel["id"]."' WHERE id = '".$dataEmpleado["id"]."' ");
            }

            echo '
            <tr>
                <td>'.$data["Documento"].'</td> 
                <td>'.$dataEmpleado["documento"].'</td>
                <td>'.$data["Nivel_Jerarquico"].'</td>
                <td>'.$existe_area.'</td>
                <td>'.$dataNivel["id"].'</td>
                
            </tr>
            ';

        }
        */

        /*
        //JERARQUICO
        $qry = mysqli_query($connect_admin, "SELECT * FROM aa_planta_marzo_2026_nivel_gen ");
        while($data = mysqli_fetch_array($qry)){

            $qryEmpleado = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE documento = '".$data["Documento"]."' ");
            $dataEmpleado = mysqli_fetch_array($qryEmpleado);

            
            //echo "UPDATE Empleados SET nivel_general = '".$data["Nivel_General"]."' WHERE id = '".$dataEmpleado["id"]."' ";

            mysqli_query($connect_admin, " UPDATE Empleados SET nivel_general = '".$data["Nivel_General"]."' WHERE id = '".$dataEmpleado["id"]."' ");
            

            echo '
            <tr>
                <td>'.$data["Documento"].'</td> 
                <td>'.$dataEmpleado["documento"].'</td>
                <td>'.$data["Nivel_General"].'</td>
                <td>'.$existe_area.'</td>
                <td>'.$dataNivel["id"].'</td>
                
            </tr>
            ';

        }
        */


        /*
        //JEFE
        $qry = mysqli_query($connect_admin, "SELECT * FROM aa_planta_marzo_2026_jefes ");
        while($data = mysqli_fetch_array($qry)){

            $qryEmpleado = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE documento = '".$data["Documento"]."' ");
            $dataEmpleado = mysqli_fetch_array($qryEmpleado);

            $qryJefe = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE documento = '".$data["Cod_Jefe"]."' ");
            $dataJefe = mysqli_fetch_array($qryJefe);

            $existe_jefe = "No Existe";
            if($qryJefe->num_rows > 0){
                $existe_jefe = "";
            } 


            $qryLider = mysqli_query($connect_admin, "SELECT * FROM Lideres WHERE id_empleado = '".$dataEmpleado["id"]."' AND id_jefe = '".$dataJefe["id"]."' ");
            $dataLider = mysqli_fetch_array($qryLider);

            $exite_reg = "";
            if($qryLider->num_rows > 0){
                $exite_reg = "Ya_existe";
            }
            
            
                
            if($exite_reg == ""){
                $sentencia_lider = "
                INSERT INTO Lideres(
                    id_empresa,
                    id_empleado,
                    id_jefe,
                    created_at
                )
                VALUES(
                    1,
                    '".$dataEmpleado["id"]."',
                    '".$dataJefe["id"]."',
                    '".$hoy."'
                )
                ";

                //mysqli_query($connect_admin, $sentencia_lider );
            }


            
            //echo "UPDATE Empleados SET nivel_general = '".$data["Nivel_General"]."' WHERE id = '".$dataEmpleado["id"]."' ";

            //mysqli_query($connect_admin, " UPDATE Empleados SET nivel_general = '".$data["Nivel_General"]."' WHERE id = '".$dataEmpleado["id"]."' ");
            

            echo '
            <tr>
                <td>'.$data["Documento"].'</td> 
                <td>'.$dataEmpleado["documento"].'</td>
                <td>'.$dataJefe["documento"].'</td>
                <td>'.$existe_jefe.'</td>
                <td>'.$exite_reg.'</td>
                
            </tr>
            ';

        }
        */

        /*
        //NUEVOS
        $qry = mysqli_query($connect_admin, "SELECT * FROM aa_planta_marzo_2026_nuevos ");
        while($data = mysqli_fetch_array($qry)){ 

            $qryValidar = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE documento = '".$data["Documento"]."' ");

            if($qryValidar->num_rows > 0){

            }
            else{

                $qryEmpleado = mysqli_query($connect_admin, "SELECT * FROM aa_planta_marzo_2026 WHERE Documento = '".$data["Documento"]."' ");
                $dataEmpleado = mysqli_fetch_array($qryEmpleado);

                $fecha_ingreso = explode("/", $dataEmpleado["Fecha_Ingreso"]);

                $new_fecha_ingreso = $fecha_ingreso[2]."-".$fecha_ingreso[1]."-".$fecha_ingreso[0];

                $qryCargo = mysqli_query($connect_admin, "SELECT * FROM Cargos WHERE nombre = '".$dataEmpleado["Cargo"]."' ");
                $dataCargo = mysqli_fetch_array($qryCargo);

                $qryNivel = mysqli_query($connect_admin, "SELECT * FROM Nivel_Jerarquico WHERE nombre = '".$dataEmpleado["Nivel_Jerarquico"]."' AND id_empresa = 1 ");
                $dataNivel = mysqli_fetch_array($qryNivel); 

                $qryVicePresidencia = mysqli_query($connect_admin, "SELECT * FROM Vicepresidencia WHERE nombre = '".$dataEmpleado["Vicepresidencia_GFA"]."' AND id_empresa = 1 ");
                $dataVicePresidencia = mysqli_fetch_array($qryVicePresidencia);

                $qryArea = mysqli_query($connect_admin, "SELECT * FROM Areas WHERE nombre = '".$dataEmpleado["Area"]."' AND id_empresa = 1 ");
                $dataArea = mysqli_fetch_array($qryArea);

                $qryUnidad = mysqli_query($connect_admin, "SELECT * FROM Estructura_Empresa WHERE unidad_organizativa = '".$dataEmpleado["Unidad_Organizativa"]."' AND id_empresa = 1 ");
                $dataUnidad = mysqli_fetch_array($qryUnidad);

                $qryJefe = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE documento = '".$dataEmpleado["Cod_Jefe_GFA"]."' ");
                $dataJefe = mysqli_fetch_array($qryJefe);

                $role = 0;
                if($dataEmpleado["Rol"] == 'Colaborador'){ $role = 3; }
                if($dataEmpleado["Rol"] == 'Líder de Equipo'){ $role = 2; }
                if($dataEmpleado["Rol"] == 'Administrador'){ $role = 1; }

                $sentencia_nuevos = "
                INSERT INTO Empleados(
                    id_empresa,
                    documento,
                    nombre,
                    genero,
                    fecha_ingreso,
                    antiguedad_anios,
                    antiguedad_meses,
                    antiguedad_dias,
                    id_cargo,
                    cargo,
                    correo,
                    correo_personal,
                    telefono_movil,
                    telefono_fijo,
                    nivel_jerarquico,
                    nivel_general,
                    compania,
                    sucursal,
                    unidad_corporativa,
                    gerencia,
                    unidad_estrategica,
                    area,
                    unidad_organizativa,
                    nombre_jefe,
                    cargo_jefe,
                    role,
                    estado,
                    password,
                    contrasena,
                    foto,
                    foto_webp,
                    id_posicion,
                    verificar,
                    force_password_reset,
                    created_at,
                    updated_at,
                    actualizado_en
                )
                VALUES(
                    1,
                    '".$dataEmpleado["Documento"]."',
                    '".$dataEmpleado["Nombres_Apellidos"]."',
                    '".$dataEmpleado["Genero"]."',
                    '".$new_fecha_ingreso."',
                    '',
                    '',
                    '',
                    '".$dataCargo["id"]."',
                    '".$dataEmpleado["Cargo"]."',
                    '".$dataEmpleado["Correo"]."',
                    '',
                    '',
                    '',
                    '".$dataNivel["id"]."',
                    '".$dataEmpleado["Nivel_General"]."',
                    '".$dataEmpleado["Compania"]."',
                    '',
                    '".$dataVicePresidencia["id"]."',
                    '',
                    '',
                    '".$dataArea["id"]."',
                    '".$dataUnidad["id"]."',
                    '".$dataJefe["nombre"]."',
                    '',
                    '".$role."',
                    1,
                    '".$dataEmpleado["Documento"]."',
                    '',
                    '',
                    '',
                    '',
                    3,
                    1,
                    '".$hoy."',
                    '".$hoy."',
                    '".$hoy."'
                )
                ";

                //echo $sentencia_nuevos;

                //mysqli_query( $connect_admin, $sentencia_nuevos  );

                echo '
                <tr> 
                    <td>'.$dataEmpleado["Documento"].'</td>
                    <td>'.$dataEmpleado["Nombres_Apellidos"].'</td>
                    <td>'.$dataJefe["documento"].'</td>
                </tr>
                ';

            }


            

        }
        */

       /*
        $count = 1;
        //VALIDADOR GENERAL
        $qry = mysqli_query($connect_admin, "SELECT * FROM aa_planta_marzo_2026 ");
        while($data = mysqli_fetch_array($qry)){ 

                $qryEmpleado = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE documento = '".$data["Documento"]."' ");
                $dataEmpleado = mysqli_fetch_array($qryEmpleado);

                $fecha_ingreso = explode("/", $data["Fecha_Ingreso"]);

                $new_fecha_ingreso = $fecha_ingreso[2]."-".$fecha_ingreso[1]."-".$fecha_ingreso[0];

                $qryCargo = mysqli_query($connect_admin, "SELECT * FROM Cargos WHERE nombre = '".$data["Cargo"]."' ");
                $dataCargo = mysqli_fetch_array($qryCargo);

                $qryNivel = mysqli_query($connect_admin, "SELECT * FROM Nivel_Jerarquico WHERE nombre = '".$data["Nivel_Jerarquico"]."' AND id_empresa = 1 ");
                $dataNivel = mysqli_fetch_array($qryNivel); 

                $qryVicePresidencia = mysqli_query($connect_admin, "SELECT * FROM Vicepresidencia WHERE nombre = '".$data["Vicepresidencia_GFA"]."' AND id_empresa = 1 ");
                $dataVicePresidencia = mysqli_fetch_array($qryVicePresidencia);

                $qryArea = mysqli_query($connect_admin, "SELECT * FROM Areas WHERE nombre = '".$data["Area_GFA_Proceso"]."' AND id_empresa = 1 ");
                $dataArea = mysqli_fetch_array($qryArea);

                $qryUnidad = mysqli_query($connect_admin, "SELECT * FROM Estructura_Empresa WHERE unidad_organizativa = '".$data["Unidad_Organizativa_GFA_Subproceso"]."' AND id_empresa = 1 ");
                $dataUnidad = mysqli_fetch_array($qryUnidad);

                $qryJefe = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE documento = '".$data["Cod_Jefe_GFA"]."' ");
                $dataJefe = mysqli_fetch_array($qryJefe);

                $role = 0;
                if($data["Rol"] == 'Colaborador'){ $role = 3; }
                if($data["Rol"] == 'Líder de Equipo'){ $role = 2; }
                if($data["Rol"] == 'Administrador'){ $role = 1; }

               
                $sentencia_nuevos = "
                INSERT INTO Empleados(
                    id_empresa,
                    documento,
                    nombre,
                    genero,
                    fecha_ingreso,
                    antiguedad_anios,
                    antiguedad_meses,
                    antiguedad_dias,
                    id_cargo,
                    cargo,
                    correo,
                    correo_personal,
                    telefono_movil,
                    telefono_fijo,
                    nivel_jerarquico,
                    nivel_general,
                    compania,
                    sucursal,
                    unidad_corporativa,
                    gerencia,
                    unidad_estrategica,
                    area,
                    unidad_organizativa,
                    nombre_jefe,
                    cargo_jefe,
                    role,
                    estado,
                    password,
                    contrasena,
                    foto,
                    foto_webp,
                    id_posicion,
                    verificar,
                    force_password_reset,
                    created_at,
                    updated_at,
                    actualizado_en
                )
                VALUES(
                    1,
                    '".$dataEmpleado["Documento"]."',
                    '".$dataEmpleado["Nombres_Apellidos"]."',
                    '".$dataEmpleado["Genero"]."',
                    '".$new_fecha_ingreso."',
                    '',
                    '',
                    '',
                    '".$dataCargo["id"]."',
                    '".$dataEmpleado["Cargo"]."',
                    '".$dataEmpleado["Correo"]."',
                    '',
                    '',
                    '',
                    '".$dataNivel["id"]."',
                    '".$dataEmpleado["Nivel_General"]."',
                    '".$dataEmpleado["Compania"]."',
                    '',
                    '".$dataVicePresidencia["id"]."',
                    '',
                    '',
                    '".$dataArea["id"]."',
                    '".$dataUnidad["id"]."',
                    '".$dataJefe["nombre"]."',
                    '',
                    '".$role."',
                    1,
                    '".$dataEmpleado["Documento"]."',
                    '',
                    '',
                    '',
                    '',
                    3,
                    1,
                    '".$hoy."',
                    '".$hoy."',
                    '".$hoy."'
                )
                ";

                //echo $sentencia_nuevos;

                //mysqli_query( $connect_admin, $sentencia_nuevos  );
             
                $no_exite_cargo = "";
                if(!$dataCargo["id"]){

                    $no_exite_cargo = "No_existe_Cargo - ".$data["Cargo"];
                    
                   
                    $sentencia_crear_cargo = "
                    INSERT INTO Cargos(
                        id_empresa,
                        nivel_jerarquico,
                        nombre,
                        padre,
                        estado,
                        created_at,
                        updated_at
                    )
                    VALUES(
                        1,
                        '".$data["Nivel_Jerarquico"]."',
                        '".$data["Cargo"]."',
                        0,
                       
                        1,
                        '".$hoy."',
                        '".$hoy."'
                    )
                    ";
                    mysqli_query( $connect_admin, $sentencia_crear_cargo );
                   
                }

                $no_exite_vice = "";
                if(!$dataVicePresidencia["id"]){
                    $no_exite_vice = "NO_existe_Vicepresidencia";
                }

                $no_exite_nivel_ = "";
                if(!$dataNivel["id"]){
                    $no_exite_nivel = "NO_existe_Nivel";
                }

                $no_existe_area = '';
                if(!$dataArea["id"]){
                    $no_existe_area = "NO_existe_Area";
                    
                    $sentencia_area = "
                    INSERT INTO Areas(
                        jerarquia,
                        id_empresa,
                        nombre,
                        padre,
                        estado,
                        created_at,
                        updated_at
                    )
                    VALUES(
                        '".$dataNivel["id"]."',
                        1,
                        '".$data["Area_GFA_Proceso"]."',
                        '',
                        1,
                        '".$hoy."',
                        '".$hoy."'
                    )
                    ";
                    
                    echo $sentencia_area;
                    mysqli_query( $connect_admin, $sentencia_area );
                   
                }

                $no_existe_unidad = '';
                if(!$dataUnidad["id"]){
                    $no_existe_unidad = "NO_existe_Unidad";

                   
                    $sentencia_unidad = "
                    INSERT INTO Estructura_Empresa(
                        id_empresa,
                        compania,
                        vicepresidencia,
                        area,
                        unidad_organizativa,
                        estado,
                        created_at,
                        updated_at
                    )
                    VALUES(
                        1,
                        '".$data["Compania"]."',
                        '".$dataVicePresidencia["id"]."',
                        '".$dataArea["id"]."',
                        '".$data["Unidad_Organizativa_GFA_Subproceso"]."', 
                        1,
                        '".$hoy."',
                        '".$hoy."'
                    )
                    ";
                    //echo $sentencia_unidad;
                    mysqli_query( $connect_admin, $sentencia_unidad );
                    
                }
                
                $cambio_area = "";
                //VALIDAR DIFERENCIA
                if($dataArea["id"] != $dataEmpleado["area"] ){
                    $cambio_area = "Cambio_Area";
                    //mysqli_query( $connect_admin, " UPDATE Empleados SET area = '".$dataArea["id"]."' WHERE id = '".$dataEmpleado["id"]."' " );
                }

                $cambio_unidad = "";
                //VALIDAR DIFERENCIA
                if($dataUnidad["id"] != $dataEmpleado["unidad_organizativa"] && $data["Unidad_Organizativa_GFA_Subproceso"] != ""  ){
                    $cambio_unidad = "Cambio_Unidad";
                    //mysqli_query( $connect_admin, " UPDATE Empleados SET unidad_organizativa = '".$dataUnidad["id"]."' WHERE id = '".$dataEmpleado["id"]."' " );
                }

                $cambio_vicepresidencia = "";
                //VALIDAR DIFERENCIA
                if($dataVicePresidencia["id"] != $dataEmpleado["unidad_corporativa"]  ){
                    $cambio_vicepresidencia = "Cambio_Vicepresidencia";
                    //mysqli_query( $connect_admin, " UPDATE Empleados SET unidad_corporativa = '".$dataVicePresidencia["id"]."' WHERE id = '".$dataEmpleado["id"]."' " );
                }


                

                

                echo '
                <tr> 
                    <td>'.$count.'</td>
                    <td>'.$dataEmpleado["documento"].'</td>
                    <td>'.$dataEmpleado["nombre"].'</td>
                    <td>'.$no_exite_cargo.'</td>
                    <td>'.$no_exite_vice.' - '.$cambio_vicepresidencia.'</td>
                    <td>'.$no_exite_nivel.'</td>
                    <td>'.$no_existe_area.' - '.$cambio_area.'</td>
                    <td>'.$no_existe_unidad.' || '.$dataEmpleado["unidad_organizativa"].' - '.$cambio_unidad.'</td>
                    
                </tr>
                ';

                $count++;

            


            

        }
        */

        /*
        //JEFE
        $qry = mysqli_query($connect_admin, "SELECT * FROM aa_planta_marzo_2026  ");
        while($data = mysqli_fetch_array($qry)){

            $qryEmpleado = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE documento = '".$data["Documento"]."' ");
            $dataEmpleado = mysqli_fetch_array($qryEmpleado);

            $qryJefe = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE documento = '".$data["Cod_Jefe_GFA"]."' ");
            $dataJefe = mysqli_fetch_array($qryJefe);

            
            $qryLider = mysqli_query($connect_admin, "SELECT * FROM Lideres WHERE id_empleado = '".$dataEmpleado["id"]."' AND id_jefe = '".$dataJefe["id"]."' AND id_empresa = 1 ");
            $dataLider = mysqli_fetch_array($qryLider);

            $exite_reg = "";
            if($qryLider->num_rows == 0){
                $exite_reg = "No Existe";

                $sentencia_lider = "
                INSERT INTO Lideres(
                    id_empresa,
                    id_empleado,
                    id_jefe, 
                    borrar, 
                    created_at
                )
                VALUES(
                    1,
                    '".$dataEmpleado["id"]."',
                    '".$dataJefe["id"]."', 
                    1,
                    '".$hoy."'
                )
                ";
                //mysqli_query($connect_admin, $sentencia_lider );
            }
            else{
                //mysqli_query($connect_admin, " UPDATE Lideres SET borrar = 1 WHERE id = '".$dataLider["id"]."'  " );
            }
            
            
              
            


            echo '
            <tr>
                <td>'.$dataEmpleado["documento"].'</td>
                <td>'.$dataEmpleado["nombre"].'</td>
                <td>'.$dataJefe["documento"].'</td>
                <td>'.$dataJefe["nombre"].'</td>
                <td>'.$exite_reg.'</td>
                
            </tr>
            ';

        }
        */

        /*
        $qryEmpleado = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id_empresa = '1'  "); //aa_ajustar_accesos
        while($data = mysqli_fetch_array($qryEmpleado)){ 

            $qryValidar = mysqli_query($connect_admin, "SELECT * FROM aa_planta_marzo_2026 WHERE Documento = '".$data["documento"]."'  ");
            $dataValidar = mysqli_fetch_array($qryValidar);

            if( $qryValidar->num_rows > 0 ){ 

                echo "ingreso";
                echo "<br>";

                //mysqli_query($connect_admin, " UPDATE Empleados SET estado = 1 WHERE id = '".$data["id"]."' AND id_empresa = 1 ");
            }
            else{
                echo "para_borrar ".$data["nombre"];
                echo "<br>";
            }

        }
        */

        /*
        //PARA AJUSTAR LOS ACCESOS
        $qryEmpleado = mysqli_query($connect_admin, "SELECT * FROM aa_ajustar_accesos "); //
        while($data = mysqli_fetch_array($qryEmpleado)){ 

            

            $qryValidar = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE documento = '".$data["doc"]."' AND id_empresa = '1'  ");
            $dataValidar = mysqli_fetch_array($qryValidar);

            if( $qryValidar->num_rows > 0 ){ 
                $texto = $data["correo"];
                if (strpos($texto, '@') !== false) {
                   
                    echo '
                    <tr>
                        <td>se_mantiene</td>
                        <td>'.$data["correo"].'</td>
                        <td>'.$dataValidar["correo"].'</td>
                    </tr>
                    ';
                    
                }
                else{

                    $cambia_pass = "";
                    if( $data["doc"] != $dataValidar["password"] ){
                        $cambia_pass = "Cambia_Password";
                    }
                    echo '
                    <tr>
                        <td>para_cambiar</td>
                        <td>'.$data["correo"].'</td>
                        <td>'.$dataValidar["correo"].'</td>
                        <td>'.$data["doc"].'</td>
                        <td>'.$dataValidar["password"].'</td>
                        <td>'.$cambia_pass.'</td>
                    </tr>
                    ';

                    //echo " UPDATE Empleados SET correo = '".$data["correo"]."', password = '".$data["doc"]."' WHERE id = '".$dataValidar["id"]."' AND id_empresa = 1 ";

                    //mysqli_query($connect_admin, " UPDATE Empleados SET correo = '".$data["correo"]."', password = '".$data["doc"]."' WHERE id = '".$dataValidar["id"]."' AND id_empresa = 1 ");
                    //mysqli_query($connect_admin, " UPDATE Empleados SET password = '".$data["doc"]."' WHERE id = '".$dataValidar["id"]."' AND id_empresa = 1 ");
                }

                
            }

        }    
        */

        /*
        //PARA AJUSTAR LOS ACCESOS
        $qry = mysqli_query($connect_admin, "SELECT * FROM Lideres WHERE id_empresa = 1 GROUP BY id_empleado  "); //
        while($data = mysqli_fetch_array($qry)){ 

            $qryTotal = mysqli_query($connect_admin, "SELECT * FROM Lideres WHERE id_empleado = '".$data["id_empleado"]."' "); //
            while($dataTotal = mysqli_fetch_array($qryTotal)){ 

                if($dataTotal["id_empresa"] != 1){
                    echo 'Para_borrar_'.$dataTotal["borrar"];
                    echo '<br>';

                    //mysqli_query($connect_admin, "DELETE FROM Lideres WHERE id = '".$dataTotal["id"]."' "); 
                }
            }

            

            

        }
        */

        /*
        $contador_cambios = 0;
        $qry = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Iniciativas WHERE avance = 1 "); //
        while($data = mysqli_fetch_array($qry)){ 

            $dataValidar = $array_registros[$data["id"]];

            //$qryValidar = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Iniciativas_punto_restauracion WHERE id = '".$data["id"]."' "); //
            //$dataValidar = mysqli_fetch_array($qryValidar);

            if($dataValidar["avance"] != $data["avance"] ){

                ///mysqli_query($connect_okrs, "UPDATE Okrs_Iniciativas SET avance = '".$dataValidar["avance"]."' WHERE id = '".$data["id"]."' "); //

                //$contador_cambios++;
                //echo $dataValidar["id"].' cambio '.$dataValidar["avance"];
                //echo '<br>';
            }

        }


        echo $contador_cambios;
        */


        
        $contador = 0;
        $qry = mysqli_query($connect_kpis, "SELECT * FROM Kpis WHERE id_empresa = 1 AND anio = 2026 "); //
        while($data = mysqli_fetch_array($qry)){ 

            
            $dataVice = $array_vicepresidencias[$data["area_macro"]];

            $dataArea = $array_areas[$data["area_proceso"]];

            $contador_equipo = 0;
            $pertenecen = 0;
            $no_pertenece = 0;
            $qryEquipo = mysqli_query($connect_kpis, "SELECT * FROM Kpis_Colaborador WHERE id_empresa = 1 AND id_kpi = '".$data["id"]."'  "); //
            while($dataEquipo = mysqli_fetch_array($qryEquipo)){ 

                $dataEmpleado = $array_colaboradores[$dataEquipo["id_colaborador"]];
                if($dataEmpleado["area"] == $data["area_proceso"] ){
                    $pertenecen ++;
                }
                else{
                    $no_pertenece++;
                }

                $contador_equipo++;
            }

            $para_valida = '';
            if($no_pertenece == 0){
                $para_validar = "";
            }
            if($no_pertenece > 0){
                $para_validar = "Se_debe_validar";
            }

            echo '
            <tr>
                <td>'.$data["id"].'</td>
                <td>'.$data["anio"].'</td>
                <td>'.$data["objetivo_indicador"].'</td>
                <td>'.$data["indicador"].'</td>

                <td>'.$dataVice["nombre"].'</td>
                <td>'.$dataArea["nombre"].'</td>

                <td>'.$contador_equipo.'</td>
                <td>'.$pertenecen.'</td>
                <td>'.$no_pertenece.'</td>
                <td>'.$para_validar.'</td>
            </tr>
            
            ';

            $contador++;

        }


        echo $contador;
        


        /*
        //PARA ELIMINAR LOS REPETIDOS
        $contador = 0;
        $sentencia_general = "
        SELECT COUNT(id) AS cantidad,id_colaborador, id_kpi  FROM Kpis_Colaborador WHERE id_empresa = 1 AND anio = 2026 GROUP BY id_colaborador, id_kpi 
        ";
        $qry = mysqli_query($connect_kpis, $sentencia_general ); //
        while($data = mysqli_fetch_array($qry)){ 

            if($data["cantidad"] > 1){

                $repetido = false; 
                $txt_repetido = "n";
                $qryTodos = mysqli_query($connect_kpis, "SELECT id FROM Kpis_Colaborador WHERE id_empresa = 1 AND id_colaborador = '".$data["id_colaborador"]."' AND  id_kpi = '".$data["id_kpi"]."' ORDER BY id ASC"); //
                while($dataTodos = mysqli_fetch_array($qryTodos)){ 
                    if($repetido == false){
                        $repetido = true;
                    }
                    else{

                        $txt_repetido = "Esta_repetido";
                        echo "eliminar ".$data["id_colaborador"]." - ".$data["id_kpi"]." - ".$dataTodos["id"]." || ";


                        $sentencia_eliminar = "DELETE FROM Kpis_Colaborador WHERE id_empresa = 1 AND id = '".$dataTodos["id"]."' "; 
                        //mysqli_query($connect_kpis, $sentencia_eliminar);
                    }

                }

                $contador++;
            }

        }

        echo $contador;
        */

        /*
        ///CASOS ESPECIALES
        $contador = 0;
        $qry = mysqli_query($connect_kpis, "SELECT * FROM Kpis WHERE id IN (2310,2322,2159,1986,1974,1871,1873,1875,1929,1975,1932,2102,2104,2105,3001,3002,3005) AND  id_empresa = 1 AND anio = 2026 "); //
        while($data = mysqli_fetch_array($qry)){ 

            
            $dataVice = $array_vicepresidencias[$data["area_macro"]];

            $dataArea = $array_areas[$data["area_proceso"]];

            $contador_equipo = 0;
            $pertenecen = 0;
            $no_pertenece = 0;
            $qryEquipo = mysqli_query($connect_kpis, "SELECT * FROM Kpis_Colaborador WHERE id_empresa = 1 AND id_kpi = '".$data["id"]."'  "); //
            while($dataEquipo = mysqli_fetch_array($qryEquipo)){ 

                $dataEmpleado = $array_colaboradores[$dataEquipo["id_colaborador"]];
                if($dataEmpleado["area"] == $data["area_proceso"] ){
                    $pertenecen ++;
                }
                else{
                    $no_pertenece++;
                   

                    //mysqli_query($connect_kpis, "DELETE FROM Kpis_Colaborador WHERE id_empresa = 1 AND id_kpi = '".$data["id"]."' AND id = '".$dataEquipo["id"]."'  "); 
                }

                $contador_equipo++;
            }

            $para_valida = '';
            if($no_pertenece == 0){
                $para_validar = "";
            }
            if($no_pertenece > 0){
                $para_validar = "Se_debe_validar";
            }

            echo '
            <tr>
                <td>'.$data["id"].'</td>
                <td>'.$data["anio"].'</td>
                <td>'.$data["objetivo_indicador"].'</td>
                <td>'.$data["indicador"].'</td>

                <td>'.$dataVice["nombre"].'</td>
                <td>'.$dataArea["nombre"].'</td>

                <td>'.$contador_equipo.'</td>
                <td>'.$pertenecen.'</td>
                <td>'.$no_pertenece.'</td>
                <td>'.$para_validar.'</td>
            </tr>
            
            ';

            $contador++;

        }


        echo $contador;
        */

        
        ?>
    </table>

    


</div>



