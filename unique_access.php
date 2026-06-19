<?php
session_start();
include("app/connect.php");
include("app/functions.php");

$datos_url = $_SERVER['REQUEST_URI'];
$partes = explode("e=", $datos_url);
$hash = $partes[1];
//echo $hash;

$dato_encript = $_GET["e"];
//echo $dato_encript;

$key = 'g0f0rag1l4';
$desifrado = decrypt_data($hash , $key);
//print_r($desifrado);


$queryValidar = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id = '".$desifrado."' ");
if($queryValidar->num_rows > 0){


        $data = mysqli_fetch_array($queryValidar);

        //LUEGO DE PASAR LAS VALIDACIONES Y PERMITIR EL ACCESO
        //LUEGO DE PASAR LAS VALIDACIONES Y PERMITIR EL ACCESO
        //LUEGO DE PASAR LAS VALIDACIONES Y PERMITIR EL ACCESO


            $queryCiclos = mysqli_query($connect_competencias_pc, "SELECT * FROM Ciclos WHERE id_empresa = '" . $data["id_empresa"] . "'");

            if (mysqli_num_rows($queryCiclos) > 0) {
                    $dataCiclos = mysqli_fetch_array($queryCiclos);
                    // $_SESSION['ciclo'] =  $dataCiclos["id"];
                    $_SESSION["anio_ciclo"] = $dataCiclos["anio"];
                    $_SESSION["ciclo"] = $dataCiclos["id"];
            } 
            else {
                    $_SESSION['ciclo'] =  "";
                    $_SESSION["anio_ciclo"] = "";
                    // $_SESSION["ciclo"] = $_POST["ciclo"];

            }
            
            $queryEmpresa = mysqli_query($connect_admin, "SELECT * FROM Empresas WHERE id = '" . $data["id_empresa"] . "'");

            if ($queryEmpresa->num_rows > 0) {
                $dataEmpresa = mysqli_fetch_array($queryEmpresa);
                $_SESSION['nombre_empresa'] =  $dataEmpresa["nombre"];
                $_SESSION['anio_fill'] =  $_SESSION["periodo_desempenio_fill"] = $dataEmpresa["anio_curso"];
                $_SESSION["anio_ciclo"] = $dataEmpresa["anio_ciclo"];
                $_SESSION["ciclo"] = $dataEmpresa["id_ciclo"];
            } 
            else {
                $_SESSION['nombre_empresa'] =  "";
                $_SESSION['anio_fill'] =  $_SESSION["periodo_desempenio_fill"] = $_SESSION["anio_ciclo"] = $_SESSION["ciclo"] = "";
            }

            $_SESSION['id_user_valentina'] = $data["id"];
            $_SESSION['id_user'] = $data["id"];
            $_SESSION['nombre_valentina'] = $data["nombre"];
            $_SESSION['id_empresa'] = $data["id_empresa"];
            $_SESSION['area'] = $data["area"];
            $_SESSION['unidad_corporativa'] = $data["unidad_corporativa"];
            $_SESSION['role_plataforma'] =  $data["role"];

            $_SESSION['equipo_fill'] =  "";
            // echo '<script> window.location = "'.$url.'?pg=okrs_equipos/reportes/consolidados_equipo"; </script>';
            echo '<script>  window.location = "' . $url . '"; </script>';
            
        

}

?>


