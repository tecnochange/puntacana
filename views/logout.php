<?php
	include("app/models/usuario/LogSession.php");	
	$ClassLog = new LogSession();
	$sesion = $ClassLog->logout();
	echo '<script> window.location = "'.$url.'log.php"; </script>';
	
?>