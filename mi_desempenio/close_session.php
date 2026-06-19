<?php
session_start();
$_SESSION['id_super_user_valentina'] = "";
$_SESSION['nombre_super_valentina'] = "";
$_SESSION['id_empresa_valentina'] = "";
$_SESSION["role_plataforma_valentina"] = "";
$_SESSION['ciclo'] =  "";
$_SESSION["anio_ciclo"] = "";
header('Location: log.php');
