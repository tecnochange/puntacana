<?php
//FUNCTION CREADA POR EL WILLIAM SOSA EL 10 DE MAYO
function ValidarRuta($ruta, $role) {
    global $VALIDAR_ROOT;
    global $connect_admin;

    $query = mysqli_query($connect_admin,"SELECT * FROM Rutas WHERE ruta = '".$ruta."' ");
    $data = mysqli_fetch_array($query);

    $roles_usuario = explode(",", $data["roles"]);
    $editar = explode(",", $data["editar"]);
    $crear = explode(",", $data["crear"]);
    $eliminar = explode(",", $data["eliminar"]);
    $exportar = explode(",", $data["exportar"]);

    $validar_ruta = $VALIDAR_ROOT["ruta"];
    foreach($roles_usuario as $r){
        if($r == $role ){
            $validar_ruta = true;
        }    
    }

    if( $ruta == 'logout' ){
        $validar_ruta = true;
    }

    $validar_editar = $VALIDAR_ROOT["editar"];
    foreach($editar as $r){
        if($r == $role ){
            $validar_editar = true;
        }    
    }

    $validar_crear = $VALIDAR_ROOT["crear"];
    foreach($crear as $r){
        if($r == $role ){
            $validar_crear = true;
        }    
    }

    $validar_eliminar = $VALIDAR_ROOT["eliminar"];
    foreach($eliminar as $r){
        if($r == $role ){
            $validar_eliminar = true;
        }    
    }

    $validar_exportar = $VALIDAR_ROOT["exportar"];
    foreach($exportar as $r){
        if($r == $role ){
            $validar_exportar = true;
        }    
    }


    return array(
        "ruta" => $validar_ruta,
        "editar" => $validar_editar,
        "crear" => $validar_crear,
        "eliminar" => $validar_eliminar,
        "exportar" => $validar_exportar
    );
}

function ValidarMenu($role){
    global $VALIDAR_MENU;
    global $connect_admin;

    $queryMenus = mysqli_query($connect_admin, "SELECT * FROM Menus");
    while($dataMenus = mysqli_fetch_array($queryMenus)){
        $roles_usuario_menu = explode(",", $dataMenus["roles"]);
        foreach($roles_usuario_menu as $rol){
            if($rol == $role ){
                $VALIDAR_MENU[$dataMenus["menu"]] = true;
            }
        }
    }
}

function ValidarModulosLic($mod_id){

    global $dtEmpresa;
    $permitir = false;

    //DESEMPEÑO
    if( $mod_id == 'mod_desempenio'){
        if($dtEmpresa["mod_desempenio"] == 'on'){
            $permitir = true;
        }
    }

    //REPORTES
    if( $mod_id == 'mod_okrs_equipo'){
        if($dtEmpresa["mod_okrs_equipo"] == 'on'){
            $permitir = true;
        }
    }

    //OKRS
    if( $mod_id == 'mod_okrs'){
        if($dtEmpresa["mod_okrs"] == 'on'){
            $permitir = true;
        }
    }

    //VALORACION
    if( $mod_id == 'mod_valoracion'){
        if($dtEmpresa["mod_valoracion"] == 'on'){
            $permitir = true;
        }
    }

    //KPIS
    if( $mod_id == 'mod_kpis'){
        if($dtEmpresa["mod_kpis"] == 'on'){
            $permitir = true;
        }
    }

    //ENDOMARKETING
    if( $mod_id == 'mod_endomarketing'){
        if($dtEmpresa["mod_endomarketing"] == 'on'){
            $permitir = true;
        }
    }

    //ADMINISTRAR
    if( $mod_id == 'mod_admin'){
        if($dtEmpresa["mod_admin"] == 'on'){
            $permitir = true;
        }
    }

    //CONFIGURACION ESTRATÉTICA
    if( $mod_id == 'mod_estrategica'){
        if($dtEmpresa["mod_estrategica"] == 'on'){
            $permitir = true;
        }
    }

    //ACADEMIA
    if( $mod_id == 'mod_academia'){
        if($dtEmpresa["mod_academia"] == 'on'){
            $permitir = true;
        }
    }

    /*
    $permitir = false;
    global $mod_disponibles; // Viene del middleware
    foreach( $mod_disponibles as $mod ){
        if($mod == $mod_id ){
            $permitir = true;  
        }
    }
        */
    return $permitir;
    
}

function EscalaColor($numero){

    global $connect_admin;
    $query = mysqli_query( $connect_admin, " SELECT * FROM Escala_Medicion WHERE id_empresa = '".$_SESSION["id_empresa"]."' ");
    $data = mysqli_fetch_array($query);

    $color = '';

    if($numero >= $data["porcentaje_uno"] ){ $color = '#FF0000'; }
    if($numero >= $data["porcentaje_tres"] ){ $color = '#FFF200'; }
    if($numero >= $data["porcentaje_cinco"] ){ $color = '#95FA03'; }
    if($numero >= $data["porcentaje_siete"] ){ $color = '#14F209'; }
    if($numero > $data["porcentaje_ocho"] ){ $color = '#00D30A'; }

    return $color;

}

function Skin(){	
    //$skinArray = json_decode($_SESSION['skin']);
    global $connect_admin;
	$queryLogoEmpr = mysqli_query($connect_admin,"SELECT * FROM Empresas WHERE id = '".$_SESSION['id_empresa']."' ");
	$dataLogoEmpr = mysqli_fetch_array($queryLogoEmpr);
	$skinArray = json_decode($dataLogoEmpr['skin']);
	
	$cfondo = $skinArray->fondo;
	$cfuente = $skinArray->fuente;
	$cmenu = $skinArray->menu;
	$citem = $skinArray->item;
	$cseparador = $skinArray->separador;
	$ctitulos = $skinArray->titulo;
}

	
function Format_Number($number){
    $val = number_format($number,2,".",",");
    return $val;
}

function FechaAmigable($fecha){
	global $Array_Meses;
	$partes = explode("-", $fecha);
	
	$txt_mes = "";
	foreach($Array_Meses as $mes){
		if( $mes[0] == $partes[1] ){
			$txt_mes = $mes[1];
		}
	}
	
	return $txt_mes." ".$partes[2]." de ".$partes[0];

}

function ObjetivosArea($id_area, $connect_admin, $connect_desempenio, $id_select){
    
    $lista_objetivos = '';
    
    $query1 = mysqli_query($connect_admin,"SELECT * FROM Areas WHERE id = '".$id_area."' ");
    $data1 = mysqli_fetch_array($query1);
    
    //objetivos 
    $queryObj = mysqli_query($connect_desempenio,"SELECT * FROM Objetivos_Area WHERE id_area = '".$data1["id"]."' AND anio = '".$_SESSION['anio']."' ");
    while($dataObj = mysqli_fetch_array($queryObj)){
        if($id_select == $dataObj["id"]){ $lista_objetivos .= '<option value="'.$dataObj["id"].'" selected>'.$dataObj["objetivo_area"].'</option>'; }
        else{ $lista_objetivos .= '<option value="'.$dataObj["id"].'">'.$dataObj["objetivo_area"].'</option>'; }  
    }
    
    $query2 = mysqli_query($connect_admin,"SELECT * FROM Areas WHERE id = '".$data1["padre"]."' ");
    while($data2 = mysqli_fetch_array($query2)){
        
        
        //objetivos 1
        $queryObj1 = mysqli_query($connect_desempenio,"SELECT * FROM Objetivos_Area WHERE id_area = '".$data2["id"]."' AND anio = '".$_SESSION['anio']."' ");
        while($dataObj1 = mysqli_fetch_array($queryObj1)){
            if($id_select == $dataObj1["id"]){ $lista_objetivos .= '<option value="'.$dataObj1["id"].'" selected>'.$dataObj1["objetivo_area"].'</option>'; }
            else{ $lista_objetivos .= '<option value="'.$dataObj1["id"].'">'.$dataObj1["objetivo_area"].'</option>'; }  
        }
        
        
        $query3 = mysqli_query($connect_admin,"SELECT * FROM Areas WHERE id = '".$data2["padre"]."' ");
        while($data3 = mysqli_fetch_array($query3)){
            
            //objetivos 2
            $queryObj2 = mysqli_query($connect_desempenio,"SELECT * FROM Objetivos_Area WHERE id_area = '".$data3["id"]."' AND anio = '".$_SESSION['anio']."' ");
            while($dataObj2 = mysqli_fetch_array($queryObj2)){
                if($id_select == $dataObj2["id"]){ $lista_objetivos .= '<option value="'.$dataObj2["id"].'" selected>'.$dataObj2["objetivo_area"].'</option>'; }
                else{ $lista_objetivos .= '<option value="'.$dataObj2["id"].'">'.$dataObj2["objetivo_area"].'</option>'; }  
            }
            
            $query4 = mysqli_query($connect_admin,"SELECT * FROM Areas WHERE id = '".$data3["padre"]."' ");
            while($data4 = mysqli_fetch_array($query4)){
                
                //objetivos 3
                $queryObj3 = mysqli_query($connect_desempenio,"SELECT * FROM Objetivos_Area WHERE id_area = '".$data4["id"]."' AND anio = '".$_SESSION['anio']."' ");
                while($dataObj3 = mysqli_fetch_array($queryObj3)){
                    if($id_select == $dataObj3["id"]){ $lista_objetivos .= '<option value="'.$dataObj3["id"].'" selected>'.$dataObj3["objetivo_area"].'</option>'; }
                    else{ $lista_objetivos .= '<option value="'.$dataObj3["id"].'">'.$dataObj3["objetivo_area"].'</option>'; } 
                }

                $query5 = mysqli_query($connect_admin,"SELECT * FROM Areas WHERE id = '".$data4["padre"]."' ");
                while($data5 = mysqli_fetch_array($query5)){
                    
                    //objetivos 4
                    $queryObj4 = mysqli_query($connect_desempenio,"SELECT * FROM Objetivos_Area WHERE id_area = '".$data5["id"]."' AND anio = '".$_SESSION['anio']."' ");
                    while($dataObj4 = mysqli_fetch_array($queryObj4)){
                        if($id_select == $dataObj4["id"]){ $lista_objetivos .= '<option value="'.$dataObj4["id"].'" selected>'.$dataObj4["objetivo_area"].'</option>'; }
                        else{ $lista_objetivos .= '<option value="'.$dataObj4["id"].'">'.$dataObj4["objetivo_area"].'</option>'; } 
                    }

                    $query6 = mysqli_query($connect_admin,"SELECT * FROM Areas WHERE id = '".$data6["padre"]."' ");
                    while($data6 = mysqli_fetch_array($query6)){
                        
                        //objetivos 5
                        $queryObj5 = mysqli_query($connect_desempenio,"SELECT * FROM Objetivos_Area WHERE id_area = '".$data6["id"]."' AND anio = '".$_SESSION['anio']."' ");
                        while($dataObj5 = mysqli_fetch_array($queryObj5)){
                            if($id_select == $dataObj5["id"]){ $lista_objetivos .= '<option value="'.$dataObj5["id"].'" selected>'.$dataObj5["objetivo_area"].'</option>'; }
                            else{ $lista_objetivos .= '<option value="'.$dataObj5["id"].'">'.$dataObj5["objetivo_area"].'</option>'; } 
                        }
                        

                    }
                    
                }
                
            }
            
        }
        
    }
    
    return $lista_objetivos;
}
	

function TrazaArea($id_area){

    global $connect_admin;
    
    $query1 = mysqli_query($connect_admin,"SELECT * FROM Areas WHERE id = '".$id_area."' ");
    $data1 = mysqli_fetch_array($query1);
    
    $query2 = mysqli_query($connect_admin,"SELECT * FROM Areas WHERE id = '".$data1["padre"]."' ");
    while($data2 = mysqli_fetch_array($query2)){
        
        
        $query3 = mysqli_query($connect_admin,"SELECT * FROM Areas WHERE id = '".$data2["padre"]."' ");
        while($data3 = mysqli_fetch_array($query3)){
            
            $query4 = mysqli_query($connect_admin,"SELECT * FROM Areas WHERE id = '".$data3["padre"]."' ");
            while($data4 = mysqli_fetch_array($query4)){

                $query5 = mysqli_query($connect_admin,"SELECT * FROM Areas WHERE id = '".$data4["padre"]."' ");
                while($data5 = mysqli_fetch_array($query5)){

                    $query6 = mysqli_query($connect_admin,"SELECT * FROM Areas WHERE id = '".$data6["padre"]."' ");
                    while($data6 = mysqli_fetch_array($query6)){
                        

                    }
                    
                }
                
            }
            
        }
        
    }

}

function EscaparInyeccion($string){
	$cadenalimpia = str_replace('"', '', $string);
	$cadenalimpia = str_replace("'", '', $cadenalimpia);
	
	return $cadenalimpia;
}

//$key = 'g0f0rag1l4'; //LLAVE DE ENCRIPCION
function encrypt_data($data, $key) {
    $method = 'aes-256-cbc';
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
    $encrypted = openssl_encrypt($data, $method, $key, 0, $iv);
    $dato_cifrado = base64_encode($encrypted . '::' . $iv);
    $dato_cifrado_url = urlencode($dato_cifrado);
    return $dato_cifrado_url;

}

function decrypt_data($data, $key) {
    $data = urldecode($data);
    $method = 'aes-256-cbc';
    list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
    return openssl_decrypt($encrypted_data, $method, $key, 0, $iv);
}
	
function dd($objeto){
    echo '<pre>';
    print_r($objeto);
    echo '</pre>';
}

function eliminar_tildes($archivo){

	$cadena = $archivo;
	$cadena = str_replace(
		array('á', 'à', 'ä', 'â', 'ª', 'Ã¡', 'Á', 'À', 'Â', 'Ä', 'Ã¡', 'Ã', 'Ã', 'ÃƒÁ'),
		array('á', 'á', 'á', 'á', 'á', 'á', 'Á', 'Á', 'Á', 'Á', 'Á', 'Á', 'Á', 'Á'),
		$cadena
	);

	$cadena = str_replace(
		array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë', 'Ã©', 'Ã‰'),
		array('e', 'e', 'e', 'e', 'É', 'É', 'É', 'É', 'é', 'É'),
		$cadena
	);

	$cadena = str_replace(
		array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î', 'Ã­', 'Ã'),
		array('i', 'i', 'i', 'i', 'Í', 'Í', 'Í', 'Í', 'Í', 'Í'),
		$cadena
	);

	$cadena = str_replace(
		array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô', 'Ã³', 'Ã“', 'Ã“', 'oÍ', 'ÃƒÁ“'),
		array('ó', 'ó', 'ó', 'ó', 'Ó', 'Ó', 'Ó', 'Ó', 'ó', 'Ó', 'Ó', 'ó', 'Ó'),
		$cadena
	);

	$cadena = str_replace(
		array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü', 'Ãº', 'Ãš'),
		array('u', 'u', 'u', 'u', 'Ú', 'Ú', 'Ú', 'Ú', 'ú', 'Ú'),
		$cadena
	);

	$cadena = str_replace(
		array('ñ', 'Ñ', 'ç', 'Ç', 'Ã±', 'ÃƒÁ±', 'Ã‘'),
		array('n', 'Ñ', 'c', 'C', 'ñ', 'ñ', 'Ñ'),
		$cadena
	);
	return $cadena;
}


function obtenerListadoDesdeMes($fecha_inicio){
    $meses = [
        1 => 'Enero',
        2 => 'Febrero',
        3 => 'Marzo',
        4 => 'Abril',
        5 => 'Mayo',
        6 => 'Junio',
        7 => 'Julio',
        8 => 'Agosto',
        9 => 'Septiembre',
        10 => 'Octubre',
        11 => 'Noviembre',
        12 => 'Diciembre'
    ];

    $inicio = new DateTime($fecha_inicio);
    $mes_inicial = (int)$inicio->format('m');
    $meses_seleccionados = [];
    for ($i = $mes_inicial; $i <= 12; $i++) {
        $meses_seleccionados[$i] = $meses[$i];
    }
    for ($i = 1; $i < $mes_inicial; $i++) {
        $meses_seleccionados[$i] = $meses[$i];
    }
    return $meses_seleccionados;
}

function GuardarAuditoria($id_empresa, $id_user, $accion, $descripcion, $modulo){
    global $connect_admin;
    $hoy = date("Y-m-d H:i:s");

    $auditoria = "INSERT INTO Auditoria_Admin (id_empresa,id_empleado,accion,descripcion,modulo,created_at)
    VALUES ('".$id_empresa."', '".$id_user."', '".$accion."', '".$descripcion."', '".$modulo."','".$hoy."')";
    mysqli_query($connect_admin, $auditoria);

}

function GuardarAuditoriaKpis($id_empresa, $id_user, $accion, $descripcion, $id_kpi, $tipo_kpi){
    global $connect_kpis;
    $hoy = date("Y-m-d H:i:s");

    $auditoria = "
    INSERT INTO Auditoria_Kpi(
        id_empresa,
        id_empleado,
        accion,
        descripcion,
        id_kpi,
        tipo_kpi,
        created_at
    )
    VALUES(
        '".$id_empresa."',
        '".$id_user."',
        '".$accion."',
        '".$descripcion."',
        '".$id_kpi."',
        '".$tipo_kpi."',
        '".$hoy."'
    )
    ";

    mysqli_query($connect_kpis, $auditoria);

}


function GuardarAuditoriaOkrs( $id_empresa, $id_user, $accion, $descripcion, $id_okr, $tipo_okr, $id_resultado, $id_iniciativa ){
    global $connect_okrs;
    $hoy = date("Y-m-d H:i:s");

    $auditoria = "
    INSERT INTO Auditoria_Okrs(
        id_empresa,
        id_empleado,
        accion,
        descripcion,
        tipo_okr,
        id_okr,
        id_kr,
        id_iniciativa,
        created_at
    )
    VALUES(
        '".$id_empresa."',
        '".$id_user."',
        '".$accion."',
        '".$descripcion."',
        '".$tipo_okr."',
        '".$id_okr."',
        '".$id_resultado."',
        '".$id_iniciativa."',
        '".$hoy."'
    )
    ";
    //echo $auditoria;
    mysqli_query($connect_okrs, $auditoria);

}

//OBTENER LA ACTIGUEDAD DE UNA FECHA
function ObtenerAntiguedad($fecha_ingreso){
    $fecha_hoy = date("Y-m-d");
    $dateDifference = abs(strtotime($fecha_hoy) - strtotime($fecha_ingreso));

    $years = floor($dateDifference / (365 * 60 * 60 * 24));
    $months = floor(($dateDifference - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
    $days = floor(($dateDifference - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));


    $decimal2 = ($months * 30 + $days) / 365;
    $decimal2 = round($decimal2, 1);
    $parte2 = explode(".", $decimal2);

    //$edad =  $years.".".$parte2[1]." aÃ±os";
    $antiguedad = $years . " años " . $months . " meses " . $days . " días";

    if($fecha_ingreso > $fecha_hoy ){
        return "0 años";
    }

    return $antiguedad;
}

?>

