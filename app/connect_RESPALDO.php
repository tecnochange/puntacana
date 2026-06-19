<?php
    header('X-Powered-By: Smart Hr Suite 2025');
    session_start();
	date_default_timezone_set('America/Bogota');
    $_SESSION['token'] = md5(uniqid(mt_rand(), true));

    $connect_valentina = mysqli_connect("localhost", "User_Gofor2025", "P433G0fr26!!!", "goforagile_admin") or die("sin conexion .");
	mysqli_set_charset($connect_valentina,"utf8");
	/*
    $connect_desempenio = mysqli_connect("localhost", "User_Gofor2023", "PUAgileM222!", "agilemaker_desempenio") or die("sin conexion");
	mysqli_set_charset($connect_desempenio,"utf8");
	*/

	$connect_clima = mysqli_connect("localhost", "User_Gofor2025", "P433G0fr26!!!", "goforagile_endomarketing") or die("sin conexion ..");
	mysqli_set_charset($connect_clima,"utf8");

	$connect_okrs = mysqli_connect("localhost", "User_Gofor2025", "P433G0fr26!!!", "goforagile_okrs") or die("sin conexion ...");
	mysqli_set_charset($connect_okrs,"utf8");

	$connect_kpis = mysqli_connect("localhost", "User_Gofor2025", "P433G0fr26!!!", "goforagile_kpis") or die("sin conexion ....");
	mysqli_set_charset($connect_kpis,"utf8");

	$connect_kpis_pc = mysqli_connect("localhost", "User_Gofor2025", "P433G0fr26!!!", "goforagile_kpis") or die("sin conexion .....");
	mysqli_set_charset($connect_kpis,"utf8");

    /*
	$connect_desempenio_individual = mysqli_connect("localhost", "User_Gofor2023", "PaGofor!!!", "smartV3_desempenio_individual") or die("sin conexion");
	mysqli_set_charset($conneconnect_desempenio_individualct_okrs,"utf8");
    */


	$connect_valoracion = mysqli_connect("localhost", "User_Gofor2025", "P433G0fr26!!!", "goforagile_competencias") or die("sin conexion ......");
	mysqli_set_charset($connect_valoracion,"utf8");




    //ACADEMIA
    // $connect_academia = mysqli_connect("localhost", "User_Gofor2025", "P4sGof0r25!!!", "goforagile_academia") or die("sin conexion");
	// mysqli_set_charset($connect_academia,"utf8");

	$connect_competencias_pc = mysqli_connect("localhost", "User_Gofor2025", "P433G0fr26!!!", "goforagile_competencias") or die("sin conexion ......");
	mysqli_set_charset($connect_competencias_pc,"utf8");

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
	$recursos_plubico = $url.'/recursos';

?>

