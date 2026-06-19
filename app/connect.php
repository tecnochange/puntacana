<?php
    header('X-Powered-By: GofoAgile 2025');
    session_start();
	date_default_timezone_set('America/Santo_Domingo');
    $_SESSION['token'] = md5(uniqid(mt_rand(), true));

    $user_db = "User_Gofor2025";
    $pass_db = "P433G0fr26!!!";
    $host_db = "localhost";

    //ESTRUCTURA
    $connect_admin = mysqli_connect( $host_db, $user_db, $pass_db, "goforagile_admin") or die("sin conexion .");
	mysqli_set_charset($connect_admin,"utf8");
	
    //ENDOMARKETING
	$connect_clima = mysqli_connect( $host_db, $user_db, $pass_db, "goforagile_endomarketing") or die("sin conexion ..");
	mysqli_set_charset($connect_clima,"utf8");

    //OKRS
	$connect_okrs = mysqli_connect( $host_db, $user_db, $pass_db, "goforagile_okrs") or die("sin conexion ...");
	mysqli_set_charset($connect_okrs,"utf8");

    //KPIS
	$connect_kpis = mysqli_connect( $host_db, $user_db, $pass_db, "goforagile_kpis") or die("sin conexion ....");
	mysqli_set_charset($connect_kpis,"utf8");

    //COMPETENCIAS
    $connect_valoracion = mysqli_connect( $host_db, $user_db, $pass_db, "goforagile_competencias") or die("sin conexion ......");
	mysqli_set_charset($connect_valoracion,"utf8");

    /*
	$connect_kpis_pc = mysqli_connect("localhost", "User_Gofor2025", "P433G0fr26!!!", "goforagile_kpis") or die("sin conexion .....");
	mysqli_set_charset($connect_kpis,"utf8");
    */

    /*
	$connect_desempenio_individual = mysqli_connect("localhost", "User_Gofor2023", "PaGofor!!!", "smartV3_desempenio_individual") or die("sin conexion");
	mysqli_set_charset($conneconnect_desempenio_individualct_okrs,"utf8");
    */


	




    //ACADEMIA
    // $connect_academia = mysqli_connect("localhost", "User_Gofor2025", "P4sGof0r25!!!", "goforagile_academia") or die("sin conexion");
	// mysqli_set_charset($connect_academia,"utf8");

    /*
	$connect_competencias_pc = mysqli_connect("localhost", "User_Gofor2025", "P433G0fr26!!!", "goforagile_competencias") or die("sin conexion ......");
	mysqli_set_charset($connect_competencias_pc,"utf8");
    */

	//$connect_pdi = mysqli_connect("localhost", "User_Gofor2023", "PaGofor!!!", "goforagile_pdi") or die("sin conexion");
	//mysqli_set_charset($connect_pdi,"utf8");

	setlocale(LC_TIME,"es_ES");
	$url = 'https://'.$_SERVER['SERVER_NAME']."/";
    $recursos_clima = 'https://'.$_SERVER['SERVER_NAME'].'/recursos_clima';
    $recursos_virtual = 'https://'.$_SERVER['SERVER_NAME'].'/recursos_virtual';
    $recursos_desarrollo = 'https://'.$_SERVER['SERVER_NAME'].'/recursos_desarrollo';
    //$api = $url.'api/';





//$url = 'https://'.$_SERVER['SERVER_NAME'];
	$uri_ruta = $url.$_SERVER['REQUEST_URI'];

	$recursos_local = $_SERVER['DOCUMENT_ROOT'].'/recursos/';
	//$recursos_plubico = $url.'/recursos';
    $recursos_publico = 'https://goforagile.com/recursos/';

?>

