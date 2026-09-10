<?php
//VALIDACION DE SESION
if ($_SESSION['id_user'] == "") {
    header('Location: log.php');
} 
//SI EL USUARIO ESTÁ LOGUEADO
else {
    $route = 'views/desempenio/mi_desempenio.php';
    $ahora = date("Y-m-d"); 

    include("app/models/Users.php");
	$UserClass = new Users();
	$user_log = $UserClass->user( $_SESSION["id_user"], $connect_admin ); 

    //EN CASO DE REQUERIR RENOVACIÓN DE CODIGO
    if($user_log["renovar_codigo"] == 2){
        include("app/models/usuario/LogSession.php");	
        $ClassLog = new LogSession();
        $sesion = $ClassLog->logout();
        echo '<script> window.location = "'.$url.'log.php"; </script>';
    }

    if($user_log["estado"] != 1){
        include("app/models/usuario/LogSession.php");	
        $ClassLog = new LogSession();
        $sesion = $ClassLog->logout();
        echo '<script> window.location = "'.$url.'log.php"; </script>';
    }

    $skin_empresa = json_decode($user_log["skin_empresa"], true);
   
    //DATOS DE LA EMPRESA
    $qrEmpresa = mysqli_query($connect_admin, "SELECT * FROM Empresas WHERE id = '" . $user_log['id_empresa'] . "' ");
    $dtEmpresa = mysqli_fetch_array($qrEmpresa);

    $qryLic = mysqli_query($connect_admin, "SELECT * FROM Licencias_Empresas WHERE id_empresa = '" . $dtEmpresa['id'] . "' AND fecha_inicia <= '" . $ahora . "' AND fecha_termina >= '" . $ahora . "'  ");
    $dtLic = mysqli_fetch_array($qryLic);

    //VALIDACION DE PERMISOS ESPECIALES
    $queryValidarAdministradorKpis = mysqli_query($connect_kpis, "SELECT * FROM Administradores_Kpi WHERE id_empresa = '" . $user_log["id_empresa"] . "' AND id_empleado = '".$user_log["id"]."' AND estado = 1 ");

    $queryValidarRelacionesLaborales = mysqli_query($connect_admin, "SELECT * FROM Relaciones_Laborales WHERE id_empresa = '" . $user_log["id_empresa"] . "' AND id_empleado = '".$user_log["id"]."' AND mod_kpis = 'on' ");

    

    //ASIGNAMOS EL NUEVO PERMISO A ESTE OBJETO
    $user_log["permiso_administrador_kpis"] = false;
    if($queryValidarAdministradorKpis->num_rows > 0){
        $user_log["permiso_administrador_kpis"] = true;
    }

    $user_log["permiso_relaciones_laboradores"] = false;
    if($queryValidarRelacionesLaborales->num_rows > 0){
        $user_log["permiso_relaciones_laboradores"] = true;
    }

    //PERMISOS RELACIONES LABORALES GLOBAL
    //PERMISOS RELACIONES LABORALES GLOBAL
    //PERMISOS RELACIONES LABORALES GLOBAL
    //PERMISOS RELACIONES LABORALES GLOBAL
    $queryValidarRelacionesLaboralesGlobal = mysqli_query($connect_admin, "SELECT * FROM Relaciones_Laborales WHERE id_empresa = '" . $user_log["id_empresa"] . "' AND id_empleado = '".$user_log["id"]."' AND estado = 1 ");
    $dataValidarRelacionesLaboralesGlobal = mysqli_fetch_array($queryValidarRelacionesLaboralesGlobal);
    
    if( $dataValidarRelacionesLaboralesGlobal["mod_competencias"] == 'on' ){
        $user_log["permiso_relaciones_laboradores_competencias"] = true;
    }
    if( $dataValidarRelacionesLaboralesGlobal["mod_okrs"] == 'on' ){
        $user_log["permiso_relaciones_laboradores_okrs"] = true;
    }
    if( $dataValidarRelacionesLaboralesGlobal["mod_kpis"] == 'on' ){
        $user_log["permiso_relaciones_laboradores_kpis"] = true;
    }
    if( $dataValidarRelacionesLaboralesGlobal["mod_desempenio"] == 'on' ){
        $user_log["permiso_relaciones_laboradores_kpis"] = true;
    }

    /*
    $qryAreaLider = mysqli_query($connect_admin, "SELECT * FROM Lideres_Areas WHERE id_lider = '" . $dtEmpleado['id'] . "' ");
    $dtArealider = mysqli_fetch_array($qryAreaLider);
    $contAreaLider = mysqli_num_rows($qryAreaLider);
    $_SESSION['cont_area_lider'] = $contAreaLider;

    $qryVpLider = mysqli_query($connect_admin, "SELECT * FROM Lideres_Vicepresidencia WHERE id_lider = '" . $dtEmpleado['id'] . "' ");
    $dtVPLider = mysqli_fetch_array($qryVpLider);
    $contVpLider = mysqli_num_rows($qryVpLider);
    $_SESSION['cont_vp_lider'] = $contVpLider;
    */

    /*
    //OKR TEMPORAL
    if ($_POST["equipo_fill"] != "") {
        $_SESSION['equipo_fill'] = $_POST["equipo_fill"];
    }
    if ($_POST["equipo_fill"] == -1) {
        $_SESSION['equipo_fill'] = "";
    }
    */
    //AÑO DEFAULT
    if (!$_SESSION["anio_fill"]) {
        $_SESSION["anio_fill"] = $dtEmpresa["anio_curso"];
    }

    //AÑO PARA COMPETENCIAS
    if (!$_SESSION["anio_ciclo"]) {
        $_SESSION["anio_ciclo"] = $dtEmpresa["anio_ciclo"];
    }

    //CICLO PARA COMPETENCIAS
    if (!$_SESSION["ciclo"]) {
        $_SESSION["ciclo"] = $dtEmpresa["id_ciclo"];
    }


    //PARA APLICAR LOS FILTROS
    //PARA APLICAR LOS FILTROS
    //PARA APLICAR LOS FILTROS
    //PARA APLICAR LOS FILTROS
    //PARA APLICAR LOS FILTROS
    //PARA APLICAR LOS FILTROS
    //PARA APLICAR LOS FILTROS
    if($_POST["anio_fill"] > 0) { $_SESSION["anio_fill"] = $_POST["anio_fill"]; }
    if($_POST["anio_fill"] == -1) { $_SESSION["anio_fill"] = $dtEmpresa["anio_curso"]; }

    if($_POST["vicepresidencias_fill"] > 0) { $_SESSION["vicepresidencias_fill"] = $_POST["vicepresidencias_fill"]; }
    if($_POST["vicepresidencias_fill"] == -1) { $_SESSION["vicepresidencias_fill"] = ""; }

    if($_POST["objetivos_fill"] > 0) { $_SESSION["objetivos_fill"] = $_POST["objetivos_fill"]; }
    if($_POST["objetivos_fill"] == -1) { $_SESSION["objetivos_fill"] = ""; }

    if($_POST["okrs_fill"] > 0) { $_SESSION["okrs_fill"] = $_POST["okrs_fill"]; }
    if($_POST["okrs_fill"] == -1) { $_SESSION["okrs_fill"] = ""; }

    if($_POST["responsables_fill"] > 0) { $_SESSION["responsables_fill"] = $_POST["responsables_fill"]; }
    if($_POST["responsables_fill"] == -1) { $_SESSION["responsables_fill"] = ""; }

    if($_POST["tipo_okrs_fill"] > 0) { $_SESSION["tipo_okrs_fill"] = $_POST["tipo_okrs_fill"]; }
    if($_POST["tipo_okrs_fill"] == -1) { $_SESSION["tipo_okrs_fill"] = ""; }

    if($_POST["periodo_fill"] != '') { $_SESSION["periodo_fill"] = $_POST["periodo_fill"]; }

    //ESTA SOLICITUD VIENE DE RESETEAR LOS FILTROS 
    if($_POST["resetear_filtros"]){
        $_SESSION["anio_fill"] = $dtEmpresa["anio_curso"];
        $_SESSION["vicepresidencias_fill"] = "";
        $_SESSION["objetivos_fill"] = "";
        $_SESSION["okrs_fill"] = "";
        $_SESSION["responsables_fill"] = "";
        $_SESSION["tipo_okrs_fill"] = "";
        $_SESSION["periodo_fill"] = "";
    }

    //PARA APLICAR LOS FILTROS
    //PARA APLICAR LOS FILTROS
    //PARA APLICAR LOS FILTROS
    //PARA APLICAR LOS FILTROS
    //PARA APLICAR LOS FILTROS
    //PARA APLICAR LOS FILTROS
    //PARA APLICAR LOS FILTROS


    //FILTROS PARA KPIS
    //FILTROS PARA LOS KPIS
    if($_POST["tipo_kpi_fill"] != '') { $_SESSION["tipo_kpi_fill"] = $_POST["tipo_kpi_fill"]; }
    if($_POST["tipo_kpi_fill"] == -1) { $_SESSION["tipo_kpi_fill"] = ""; }

    if($_POST["frecuencia_fill"] != '') { $_SESSION["frecuencia_fill"] = $_POST["frecuencia_fill"]; }
    if($_POST["frecuencia_fill"] == -1) { $_SESSION["frecuencia_fill"] = ""; } 

    if($_POST["tipo_resultado_fill"] != '') { $_SESSION["tipo_resultado_fill"] = $_POST["tipo_resultado_fill"]; }
    if($_POST["tipo_resultado_fill"] == -1) { $_SESSION["tipo_resultado_fill"] = ""; }

    if($_POST["periodo_inicio_fill"] != '') { $_SESSION["periodo_inicio_fill"] = $_POST["periodo_inicio_fill"]; }
    if($_POST["periodo_inicio_fill"] == -1) { $_SESSION["periodo_inicio_fill"] = ""; }

    if($_POST["periodo_fin_fill"] != '') { $_SESSION["periodo_fin_fill"] = $_POST["periodo_fin_fill"]; }
    if($_POST["periodo_fin_fill"] == -1) { $_SESSION["periodo_fin_fill"] = ""; }

    

    if($_POST["tipo_calculo_fill"] != '') { $_SESSION["tipo_calculo_fill"] = $_POST["tipo_calculo_fill"]; }
    if($_POST["tipo_calculo_fill"] == -1) { $_SESSION["tipo_calculo_fill"] = ""; }

    if($_POST["unidad_medida_fill"] != '') { $_SESSION["unidad_medida_fill"] = $_POST["unidad_medida_fill"]; }
    if($_POST["unidad_medida_fill"] == -1) { $_SESSION["unidad_medida_fill"] = ""; }



    if($_POST["area_macro_fill"] != '') { $_SESSION["area_macro_fill"] = $_POST["area_macro_fill"]; }
    if($_POST["area_macro_fill"] == -1) { $_SESSION["area_macro_fill"] = ""; }

    if($_POST["area_proceso_fill"] != '') { $_SESSION["area_proceso_fill"] = $_POST["area_proceso_fill"]; }
    if($_POST["area_proceso_fill"] == -1) { $_SESSION["area_proceso_fill"] = ""; }

    if($_POST["subproceso_fill"] != '') { $_SESSION["subproceso_fill"] = $_POST["subproceso_fill"]; }
    if($_POST["subproceso_fill"] == -1) { $_SESSION["subproceso_fill"] = ""; }

    if($_POST["objetivo_sg_fill"] != '') { $_SESSION["objetivo_sg_fill"] = $_POST["objetivo_sg_fill"]; }
    if($_POST["objetivo_sg_fill"] == -1) { $_SESSION["objetivo_sg_fill"] = ""; }

    //ESTA SOLICITUD VIENE DE RESETEAR LOS FILTROS 
    if($_POST["resetear_filtros_kpis"]){
        $_SESSION["tipo_kpi_fill"] = "";
        $_SESSION["frecuencia_fill"] = "";
        $_SESSION["tipo_resultado_fill"] = "";
        $_SESSION["periodo_inicio_fill"] = "";
        $_SESSION["periodo_fin_fill"] = "";
        
        $_SESSION["tipo_calculo_fill"] = "";
        $_SESSION["unidad_medida_fill"] = "";

        $_SESSION["area_macro_fill"] = "";
        $_SESSION["area_proceso_fill"] = "";
        $_SESSION["subproceso_fill"] = "";
        $_SESSION["objetivo_sg_fill"] = "";
    }
    
    //PARA SELECCIONAR AÑO DE COMPETENCIAS
    if ($_POST["guardar_configurar"] != "") {
        $_SESSION["anio_ciclo"] = $_POST["anio_ciclo"];
        $_SESSION["ciclo"] = $_POST["ciclo"]; 
    }
    





    /*
    $queryCicloVal = mysqli_query($connect_competencias_pc, "SELECT * FROM Ciclos WHERE id = '" . $_SESSION['ciclo'] . "' ");
    $dataCicloVal = mysqli_fetch_array($queryCicloVal);

    $ciclo_cerrado = false;
    if ($dataCicloVal["fecha_termina"] < date("Y-m-d")) {
        $ciclo_cerrado = true;
    }
    */

    //$mod_disponibles = explode(",", $dtEmpleado["modulos"]);
	$roles_usr = explode(",", $user_log["role"]);

	$VALIDAR_MENU = array();
	//CARGAMOS TODOS LOS MENUS DEL USUARIO
	foreach($roles_usr as $rl_menu){
		ValidarMenu($rl_menu);
	}

	//CARGAR RUTA
	//CARGAR RUTA
	if($_GET["pg"]){

        $route = 'views/'.$_GET["pg"].'.php';

		//OBJETO GLOBAL
		//OBJETO GLOBAL
		$VALIDAR_ROOT;
		foreach($roles_usr as $rl){
			$VALIDAR_ROOT = ValidarRuta( $_GET["pg"] , $rl );
		}

		if(!$VALIDAR_ROOT["ruta"]){

            $modulo_borrar = explode("/", $_GET["pg"]);
            $route = "views/sin_permisos.php";
		}
	}

    if( $user_log["cambio_pass"] == 1 ){
        $route = "views/perfil/cambiar_pass.php";
    }

}

    //print_r($VALIDAR_ROOT);
?>