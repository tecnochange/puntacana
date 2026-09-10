<?php
    header('X-Powered-By: GofoAgile 2025');
    session_start();
	date_default_timezone_set('America/Santo_Domingo');
    $_SESSION['token'] = md5(uniqid(mt_rand(), true));

    $user_db = "user_puntacana";
    $pass_db = "P433G0fr26!!!";
    $host_db = "localhost";

    //ESTRUCTURA
    $connect_admin = mysqli_connect( $host_db, $user_db, $pass_db, "puntacana_admin") or die("sin conexion .");
	mysqli_set_charset($connect_admin,"utf8");
	
    //ENDOMARKETING
	$connect_clima = mysqli_connect( $host_db, $user_db, $pass_db, "puntacana_endomarketing") or die("sin conexion ..");
	mysqli_set_charset($connect_clima,"utf8");

    //OKRS
	$connect_okrs = mysqli_connect( $host_db, $user_db, $pass_db, "puntacana_okrs") or die("sin conexion ...");
	mysqli_set_charset($connect_okrs,"utf8");

    //KPIS
	$connect_kpis = mysqli_connect( $host_db, $user_db, $pass_db, "puntacana_kpis") or die("sin conexion ....");
	mysqli_set_charset($connect_kpis,"utf8");

    //COMPETENCIAS
    $connect_valoracion = mysqli_connect( $host_db, $user_db, $pass_db, "puntacana_competencias") or die("sin conexion ......");
	mysqli_set_charset($connect_valoracion,"utf8");

	setlocale(LC_TIME,"es_ES");
	$url = 'https://'.$_SERVER['SERVER_NAME']."/";
    $recursos_clima = 'https://'.$_SERVER['SERVER_NAME'].'/recursos_clima';
    $recursos_virtual = 'https://'.$_SERVER['SERVER_NAME'].'/recursos_virtual';
    $recursos_desarrollo = 'https://'.$_SERVER['SERVER_NAME'].'/recursos_desarrollo';

	$uri_ruta = $url.$_SERVER['REQUEST_URI'];
	$recursos_local = $_SERVER['DOCUMENT_ROOT'].'/recursos/';

    $recursos_local = 'https://'.$_SERVER['SERVER_NAME']."/recursos/";
    $recursos_publico = 'https://puntacana.goforagile.com/recursos/';
?>

