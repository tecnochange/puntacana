<script>
    $(document).ready(function() {
        $('#menuCompetencias').collapse();
        $('#bt_competencias_programacion').addClass('active');
    });
</script>

<?php
$hoy = date("Y-m-d H:i:s");
$id = $_GET["id"];
$ahora = date("Y-m-d");

if ($_POST["nombre"] != "") {

    if ($_POST["id_registro"] != "") {
        mysqli_query($connect_valoracion, "UPDATE Ciclos SET nombre = '" . $_POST["nombre"] . "', fecha_inicia = '" . $_POST["fecha_inicia"] . "',  
			fecha_termina = '" . $_POST["fecha_termina"] . "' WHERE id = '" . $_POST["id_registro"] . "'  ");
    } else {
        mysqli_query($connect_valoracion, "INSERT INTO Ciclos ( id_empresa, anio, nombre, fecha_inicia, fecha_termina, created_at) 
			VALUES 
			( '" . $_SESSION['id_empresa'] . "', '" . $_POST["anio_ciclo"] . "', '" . $_POST["nombre"] . "', '" . $_POST["fecha_inicia"] . "', '" . $_POST["fecha_termina"] . "', '" . $hoy . "' ) ");
        $id_temp = mysqli_insert_id($connect_valoracion);

        $queryUltimoCiclo = mysqli_query($connect_valoracion, "SELECT * FROM Ciclos WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND id NOT IN($id_temp) ORDER BY id DESC LIMIT 1");
        if (mysqli_num_rows($queryUltimoCiclo) > 0) {
            $dataUltimoCiclo = mysqli_fetch_array($queryUltimoCiclo);
            $anioCiclo = $dataUltimoCiclo["anio"];
            $idCiclo = $dataUltimoCiclo["id"];
        }
        if ($anioCiclo > 0) {
            // echo "<br>";
            $queryTipos = mysqli_query($connect_valoracion, "SELECT * FROM Tipos WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '" . $_POST["anio_ciclo"] . "'");
            if (mysqli_num_rows($queryPE) == 0) {
                $queryTipos1 = mysqli_query($connect_valoracion, "SELECT * FROM Tipos WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '$anioCiclo'");
                while ($dataTipos1 = mysqli_fetch_array($queryTipos1)) {
                    // echo "INSERT INTO Tipos (id_empresa, anio, nombre, created_at, update_at)
                    // VALUES ('" . $_SESSION['id_empresa'] . "','" . $_POST["anio_ciclo"] . "','" . eliminar_tildes($dataTipos1["nombre"]) . "','$hoy','$hoy')>br>";
                    mysqli_query($connect_valoracion, "INSERT INTO Tipos (id_empresa, anio, nombre, created_at, update_at)
                    VALUES ('" . $_SESSION['id_empresa'] . "','" . $_POST["anio_ciclo"] . "','" . eliminar_tildes($dataTipos1["nombre"]) . "','$hoy','$hoy')");
                }
            }
            // echo "<br>";
            $queryCompetencias = mysqli_query($connect_valoracion, "SELECT * FROM Competencias WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '" . $_POST["anio_ciclo"] . "' AND id_ciclo = $id_temp");
            if (mysqli_num_rows($queryCompetencias) == 0) {
                $queryCompetenciasUpdate = mysqli_query($connect_valoracion, "SELECT * FROM Competencias WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '$anioCiclo' AND id_ciclo = $idCiclo");
                while ($dataCompetencia = mysqli_fetch_array($queryCompetenciasUpdate)) {
                    $queryTipos1 = mysqli_query($connect_valoracion, "SELECT * FROM Tipos WHERE id = " . $dataCompetencia["id_tipo"]);
                    $dataTipo1 = mysqli_fetch_array($queryTipos1);
                    // echo "SELECT * FROM Tipos WHERE nombre = '".$dataTipo1["nombre"]."' AND definicion = '".$dataTipo1["definicion"]."'  AND anio = '" . $_POST["anio_ciclo"] . "' AND id_ciclo = $id_temp <br>";
                    $queryTipos2 = mysqli_query($connect_valoracion, "SELECT * FROM Tipos WHERE nombre = '" . $dataTipo1["nombre"] . "'  AND anio = '" . $_POST["anio_ciclo"] . "'");
                    $dataTipo2 = mysqli_fetch_array($queryTipos2);
                    //     echo "INSERT INTO Competencias (id_empresa, anio, id_ciclo, nombre, definicion, id_tipo, pregunta_abierta, created_at, update_at)
                    // VALUES ('" . $_SESSION['id_empresa'] . "','" . $_POST["anio_ciclo"] . "','$id_temp','" . eliminar_tildes($dataCompetencia["nombre"]) . "','" . eliminar_tildes($dataCompetencia["definicion"]) . "',
                    // '" . $dataCompetencia["id_tipo"] . "','" . eliminar_tildes($dataCompetencia["pregunta_abierta"]) . "','$hoy','$hoy')<br>";
                    mysqli_query($connect_valoracion, "INSERT INTO Competencias (id_empresa, anio, id_ciclo, nombre, definicion, id_tipo, pregunta_abierta, created_at, update_at)
                    VALUES ('" . $_SESSION['id_empresa'] . "','" . $_POST["anio_ciclo"] . "','$id_temp','" . $dataCompetencia["nombre"] . "','" . $dataCompetencia["definicion"] . "',
                    '" . $dataTipo2["id"] . "','" . $dataCompetencia["pregunta_abierta"] . "','$hoy','$hoy')");
                }
            }
            // echo "<br>";
            $queryNiveles = mysqli_query($connect_valoracion, "SELECT * FROM Niveles WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '" . $_POST["anio_ciclo"] . "'");
            if (mysqli_num_rows($queryNiveles) == 0) {
                $queryNiveles1 = mysqli_query($connect_valoracion, "SELECT * FROM Niveles WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '$anioCiclo'");
                while ($dataNiveles1 = mysqli_fetch_array($queryNiveles1)) {
                    // echo "INSERT INTO Niveles (id_empresa, anio, nombre, created_at, update_at)
                    // VALUES('" . $_SESSION['id_empresa'] . "','" . $_POST["anio_ciclo"] . "','" . eliminar_tildes($dataNiveles1["nombre"]) . "','$hoy','$hoy')<br>";
                    mysqli_query($connect_valoracion, "INSERT INTO Niveles (id_empresa, anio, nombre, created_at, update_at)
                    VALUES('" . $_SESSION['id_empresa'] . "','" . $_POST["anio_ciclo"] . "','" . eliminar_tildes($dataNiveles1["nombre"]) . "','$hoy','$hoy')");
                }
            }
            // echo "<br>";
            $queryCompetenciasNiveles = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Niveles WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '" . $_POST["anio_ciclo"] . "'");
            if (mysqli_num_rows($queryCompetenciasNiveles) == 0) {
                $queryCompetenciasNiveles1 = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Niveles WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '$anioCiclo'");
                while ($dataCompetenciaNivel = mysqli_fetch_array($queryCompetenciasNiveles1)) {
                    $queryCompetencias1 = mysqli_query($connect_valoracion, "SELECT * FROM Competencias WHERE id = " . $dataCompetenciaNivel["id_competencia"]);
                    $dataCompetencia1 = mysqli_fetch_array($queryCompetencias1);
                    // echo "SELECT * FROM Competencias WHERE nombre = '".$dataCompetencia1["nombre"]."' AND definicion = '".$dataCompetencia1["definicion"]."'  AND anio = '" . $_POST["anio_ciclo"] . "' AND id_ciclo = $id_temp <br>";
                    $queryCompetencias2 = mysqli_query($connect_valoracion, "SELECT * FROM Competencias WHERE nombre = '" . $dataCompetencia1["nombre"] . "' AND definicion = '" . $dataCompetencia1["definicion"] . "'  AND anio = '" . $_POST["anio_ciclo"] . "' AND id_ciclo = $id_temp");
                    $dataCompetencia2 = mysqli_fetch_array($queryCompetencias2);
                    $queryNiveles1 = mysqli_query($connect_valoracion, "SELECT * FROM Niveles WHERE id = " . $dataCompetenciaNivel["id_nivel"]);
                    $dataNivel1 = mysqli_fetch_array($queryNiveles1);
                    $queryNiveles2 = mysqli_query($connect_valoracion, "SELECT * FROM Niveles WHERE nombre = '" . $dataNivel1["nombre"] . "' AND anio = '" . $_POST["anio_ciclo"] . "'");
                    $dataNivel2 = mysqli_fetch_array($queryNiveles2);
                    // echo "INSERT INTO Competencias_Niveles (id_empresa, anio,id_competencia,id_nivel,created_at, update_at)
                    // VALUES ('" . $_SESSION['id_empresa'] . "','" . $_POST["anio_ciclo"] . "','" . $dataCompetencia2["id"] . "','" . $dataCompetenciaNivel["id_nivel"] . "','$hoy','$hoy')<br>";
                    mysqli_query($connect_valoracion, "INSERT INTO Competencias_Niveles (id_empresa, anio,id_competencia,id_nivel,created_at, update_at)
                    VALUES ('" . $_SESSION['id_empresa'] . "','" . $_POST["anio_ciclo"] . "','" . $dataCompetencia2["id"] . "','" . $dataNivel2["id"] . "','$hoy','$hoy')");
                }
            }
            // echo "<br>";
            $queryCPreguntas = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Preguntas WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '" . $_POST["anio_ciclo"] . "'");
            if (mysqli_num_rows($queryCPreguntas) == 0) {
                $queryCN1 = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Niveles WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '" . $_POST["anio_ciclo"] . "'");
                $cont = mysqli_num_rows($queryCN1);
                $queryCPreguntas1 = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Preguntas WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '$anioCiclo'");
                while ($dataCPreguntas1 = mysqli_fetch_array($queryCPreguntas1)) {
                    $queryCompetencias1 = mysqli_query($connect_valoracion, "SELECT * FROM Competencias WHERE id = " . $dataCPreguntas1["id_competencia"]);
                    $dataCompetencia1 = mysqli_fetch_array($queryCompetencias1);
                    // echo "SELECT * FROM Competencias WHERE nombre = '".$dataCompetencia1["nombre"]."' AND definicion = '".$dataCompetencia1["definicion"]."'  AND anio = '" . $_POST["anio_ciclo"] . "' AND id_ciclo = $id_temp <br>";
                    $queryCompetencias2 = mysqli_query($connect_valoracion, "SELECT * FROM Competencias WHERE nombre = '" . $dataCompetencia1["nombre"] . "' AND definicion = '" . $dataCompetencia1["definicion"] . "'  AND anio = '" . $_POST["anio_ciclo"] . "' AND id_ciclo = $id_temp");
                    $dataCompetencia2 = mysqli_fetch_array($queryCompetencias2);
                    // $queryCN1 = mysqli_query();
                    // echo "INSERT INTO Competencias_Preguntas (id_empresa, anio, id_competencia, id_nivel_competencia, indicador, pregunta, contra_pregunta, fortaleza, oportunidad, created_at, update_at)
                    // VALUES ('" . $_SESSION['id_empresa'] . "','" . $_POST["anio_ciclo"] . "','" . $dataCPreguntas1["id_competencia"] . "','" . $dataCPreguntas1["id_nivel_competencia"] . "','" . eliminar_tildes($dataCPreguntas1["indicador"]) . "','" . eliminar_tildes($dataCPreguntas1["pregunta"]) . "',
                    // '" . eliminar_tildes($dataCPreguntas1["contra_pregunta"]) . "','" . eliminar_tildes($dataCPreguntas1["fortaleza"]) . "','" . eliminar_tildes($dataCPreguntas1["oportunidad"]) . "','$hoy','$hoy')<br>";
                    mysqli_query($connect_valoracion, "INSERT INTO Competencias_Preguntas (id_empresa, anio, id_competencia, id_nivel_competencia, indicador, pregunta, contra_pregunta, fortaleza, oportunidad, created_at, update_at)
                    VALUES ('" . $_SESSION['id_empresa'] . "','" . $_POST["anio_ciclo"] . "','" . $dataCompetencia2["id"] . "','" . ($dataCPreguntas1["id_nivel_competencia"] + $cont) . "','" . eliminar_tildes($dataCPreguntas1["indicador"]) . "','" . eliminar_tildes($dataCPreguntas1["pregunta"]) . "',
                    '" . eliminar_tildes($dataCPreguntas1["contra_pregunta"]) . "','" . eliminar_tildes($dataCPreguntas1["fortaleza"]) . "','" . eliminar_tildes($dataCPreguntas1["oportunidad"]) . "','$hoy','$hoy')");
                }
            }

            // echo "<br>";
            $queryCTipoE = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Tipo_Evaluador WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '" . $_POST["anio_ciclo"] . "'");
            if (mysqli_num_rows($queryCTipoE) == 0) {
                $queryCTipoE1 = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Tipo_Evaluador WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '$anioCiclo' AND estado = 1");
                while ($dataCTipoE1 = mysqli_fetch_array($queryCTipoE1)) {
                    $queryTipos1 = mysqli_query($connect_valoracion, "SELECT * FROM Tipos WHERE id = " . $dataCTipoE1["id_tipo_competencia"]);
                    $dataTipo1 = mysqli_fetch_array($queryTipos1);
                    // echo "SELECT * FROM Tipos WHERE nombre = '".$dataTipo1["nombre"]."' AND definicion = '".$dataTipo1["definicion"]."'  AND anio = '" . $_POST["anio_ciclo"] . "' AND id_ciclo = $id_temp <br>";
                    $queryTipos2 = mysqli_query($connect_valoracion, "SELECT * FROM Tipos WHERE nombre = '" . $dataTipo1["nombre"] . "'  AND anio = '" . $_POST["anio_ciclo"] . "'");
                    $dataTipo2 = mysqli_fetch_array($queryTipos2);
                    // echo "INSERT INTO Competencias_Tipo_Evaluador (id_empresa, anio, id_tipo_competencia, auto, jefe, par, subalterno, cliente, estado, created_at)
                    // VALUES ('" . $_SESSION['id_empresa'] . "','" . $_POST["anio_ciclo"] . "','" . $dataCTipoE1["id_tipo_competencia"] . "','" . $dataCTipoE1["auto"] . "','" . $dataCTipoE1["jefe"] . "','" . $dataCTipoE1["par"] . "',
                    // '" . $dataCTipoE1["subalterno"] . "','" . $dataCTipoE1["cliente"] . "','" . $dataCTipoE1["estado"] . "','$hoy')<br>";
                    mysqli_query($connect_valoracion, "INSERT INTO Competencias_Tipo_Evaluador (id_empresa, anio, id_tipo_competencia, auto, jefe, par, subalterno, cliente, estado, created_at)
                    VALUES ('" . $_SESSION['id_empresa'] . "','" . $_POST["anio_ciclo"] . "','" . $dataTipo2["id"] . "','" . $dataCTipoE1["auto"] . "','" . $dataCTipoE1["jefe"] . "','" . $dataCTipoE1["par"] . "',
                    '" . $dataCTipoE1["subalterno"] . "','" . $dataCTipoE1["cliente"] . "','" . $dataCTipoE1["estado"] . "','$hoy')");
                }
            }
            // echo "<br>";
            $queryEscala = mysqli_query($connect_valoracion, "SELECT * FROM Escalas WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '" . $_POST["anio_ciclo"] . "'");
            if (mysqli_num_rows($queryEscala) == 0) {
                $queryEscala1 = mysqli_query($connect_valoracion, "SELECT * FROM Escalas WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '$anioCiclo'");
                while ($dataEscala1 = mysqli_fetch_array($queryEscala1)) {
                    // echo "INSERT INTO Escalas (id_empresa, anio, nombre_n_1, descripcion_n_1, nombre_n_2, descripcion_n_2, nombre_n_3, descripcion_n_3, nombre_n_4, descripcion_n_4, nombre_n_5, descripcion_n_5, nombre_n_6, descripcion_n_6, created_at)
                    // VALUES ('" . $_SESSION['id_empresa'] . "','" . $_POST["anio_ciclo"] . "','" . $dataEscala1["nombre_n_1"] . "','" . $dataEscala1["descripcion_n_1"] . "','" . $dataEscala1["nombre_n_2"] . "','" . $dataEscala1["descripcion_n_2"] . "',
                    // '" . $dataEscala1["nombre_n_3"] . "','" . $dataEscala1["descripcion_n_3"] . "','" . $dataEscala1["nombre_n_4"] . "','" . $dataEscala1["descripcion_n_4"] . "','" . $dataEscala1["nombre_n_5"] . "','" . $dataEscala1["descripcion_n_5"] . "','" . $dataEscala1["nombre_n_6"] . "','" . $dataEscala1["descripcion_n_6"] . "','$hoy')<br>";
                    mysqli_query($connect_valoracion, "INSERT INTO Escalas (id_empresa, anio, nombre_n_1, descripcion_n_1, nombre_n_2, descripcion_n_2, nombre_n_3, descripcion_n_3, nombre_n_4, descripcion_n_4, nombre_n_5, descripcion_n_5, nombre_n_6, descripcion_n_6, created_at)
                    VALUES ('" . $_SESSION['id_empresa'] . "','" . $_POST["anio_ciclo"] . "','" . $dataEscala1["nombre_n_1"] . "','" . $dataEscala1["descripcion_n_1"] . "','" . $dataEscala1["nombre_n_2"] . "','" . $dataEscala1["descripcion_n_2"] . "',
                    '" . $dataEscala1["nombre_n_3"] . "','" . $dataEscala1["descripcion_n_3"] . "','" . $dataEscala1["nombre_n_4"] . "','" . $dataEscala1["descripcion_n_4"] . "','" . $dataEscala1["nombre_n_5"] . "','" . $dataEscala1["descripcion_n_5"] . "','" . $dataEscala1["nombre_n_6"] . "','" . $dataEscala1["descripcion_n_6"] . "','$hoy')");
                }
            }
            // echo "<br>";
            $queryEscalaI = mysqli_query($connect_valoracion, "SELECT * FROM Escalas_Interpretacion WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '" . $_POST["anio_ciclo"] . "'");
            if (mysqli_num_rows($queryEscalaI) == 0) {
                $queryEscalaI1 = mysqli_query($connect_valoracion, "SELECT * FROM Escalas_Interpretacion WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '$anioCiclo'");
                while ($dataEscalaI1 = mysqli_fetch_array($queryEscalaI1)) {
                    // echo "INSERT INTO Escalas_Interpretacion (id_empresa, anio, nombre_n_1, descripcion_n_1, rango_1, nombre_n_2, descripcion_n_2, rango_2, nombre_n_3, descripcion_n_3, rango_3, nombre_n_4, descripcion_n_4, rango_4, created_at)
                    // VALUES ('" . $_SESSION['id_empresa'] . "','" . $_POST["anio_ciclo"] . "','" . $dataEscalaI1["nombre_n_1"] . "','" . $dataEscalaI1["descripcion_n_1"] . "','" . $dataEscalaI1["rango_1"] . "','" . $dataEscalaI1["nombre_n_2"] . "','" . $dataEscalaI1["descripcion_n_2"] . "','" . $dataEscalaI1["rango_2"] . "',
                    // '" . $dataEscalaI1["nombre_n_3"] . "','" . $dataEscalaI1["descripcion_n_3"] . "','" . $dataEscalaI1["rango_3"] . "','" . $dataEscalaI1["nombre_n_4"] . "','" . $dataEscalaI1["descripcion_n_4"] . "','" . $dataEscalaI1["rango_4"] . "','$hoy')<br>";
                    mysqli_query($connect_valoracion, "INSERT INTO Escalas_Interpretacion (id_empresa, anio, nombre_n_1, descripcion_n_1, rango_1, nombre_n_2, descripcion_n_2, rango_2, nombre_n_3, descripcion_n_3, rango_3, nombre_n_4, descripcion_n_4, rango_4, created_at)
                    VALUES ('" . $_SESSION['id_empresa'] . "','" . $_POST["anio_ciclo"] . "','" . $dataEscalaI1["nombre_n_1"] . "','" . $dataEscalaI1["descripcion_n_1"] . "','" . $dataEscalaI1["rango_1"] . "','" . $dataEscalaI1["nombre_n_2"] . "','" . $dataEscalaI1["descripcion_n_2"] . "','" . $dataEscalaI1["rango_2"] . "',
                    '" . $dataEscalaI1["nombre_n_3"] . "','" . $dataEscalaI1["descripcion_n_3"] . "','" . $dataEscalaI1["rango_3"] . "','" . $dataEscalaI1["nombre_n_4"] . "','" . $dataEscalaI1["descripcion_n_4"] . "','" . $dataEscalaI1["rango_4"] . "','$hoy')");
                }
            }
            // echo "<br>";
            $queryEmpleados = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND estado = 1 AND fecha_ingreso <= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)");
            while ($dataEmpleados = mysqli_fetch_array($queryEmpleados)) {
                $queryLideres = mysqli_query($connect_admin, "SELECT * FROM Lideres WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND id_empleado = '" . $dataEmpleados["id"] . "'");
                if (mysqli_num_rows($queryLideres) > 0) {
                    while ($dataLideres = mysqli_fetch_array($queryLideres)) {
                        // echo "INSERT INTO Evaluadores (id_empresa, anio, id_ciclo, id_empleado, id_evaluador, tipo, created_at)
                        // VALUES ('" . $_SESSION['id_empresa'] . "','" . $_POST["anio_ciclo"] . "','$id_temp','" . $dataLideres["id_empleado"] . "','" . $dataLideres["id_jefe"] . "',5,'$hoy')<br>";
                        mysqli_query($connect_valoracion, "INSERT INTO Evaluadores (id_empresa, anio, id_ciclo, id_empleado, id_evaluador, tipo, created_at)
                        VALUES ('" . $_SESSION['id_empresa'] . "','" . $_POST["anio_ciclo"] . "','$id_temp','" . $dataLideres["id_empleado"] . "','" . $dataLideres["id_jefe"] . "',5,'$hoy')");
                    }
                }
            }

            // echo "<br>";
            $queryPCargo = mysqli_query($connect_valoracion, "SELECT * FROM Perfiles_Cargos WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '" . $_POST["anio_ciclo"] . "' AND id_ciclo = $id_temp");
            $queryCN1 = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Niveles WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '" . $_POST["anio_ciclo"] . "'");
            $cont = mysqli_num_rows($queryCN1);
            if (mysqli_num_rows($queryPCargo) == 0) {
                $queryPCargo1 = mysqli_query($connect_valoracion, "SELECT * FROM Perfiles_Cargos WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '$anioCiclo' AND id_ciclo = $idCiclo");
                while ($dataPCargo1 = mysqli_fetch_array($queryPCargo1)) {
                    $perfiles = $dataPCargo1['perfiles'];
                    $perfilesArray = explode(',', $perfiles);

                    $perfilesSumados = array_map(function ($perfil) use ($cont) {
                        return $perfil + $cont;
                    }, $perfilesArray);

                    $perfilesNuevos = implode(',', $perfilesSumados);
                    // echo "INSERT INTO Perfiles_Cargos (id_empresa, anio, id_ciclo, id_cargo, perfiles, created_at)
                    // VALUES ('" . $_SESSION['id_empresa'] . "','" . $_POST["anio_ciclo"] . "','$id_temp','" . $dataPCargo1["id_cargo"] . "','" . $dataPCargo1["perfiles"] . "','$hoy')<br>";
                    mysqli_query($connect_valoracion, "INSERT INTO Perfiles_Cargos (id_empresa, anio, id_ciclo, id_cargo, perfiles, created_at)
                    VALUES ('" . $_SESSION['id_empresa'] . "','" . $_POST["anio_ciclo"] . "','$id_temp','" . $dataPCargo1["id_cargo"] . "','" . $perfilesNuevos . "','$hoy')");
                }
            }
            // echo "<br>";
            $queryPE = mysqli_query($connect_valoracion, "SELECT * FROM Ponderar_Evaluaciones WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '" . $_POST["anio_ciclo"] . "'");
            if (mysqli_num_rows($queryPE) == 0) {
                $queryPE1 = mysqli_query($connect_valoracion, "SELECT * FROM Ponderar_Evaluaciones WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '$anioCiclo'");
                while ($dataPE1 = mysqli_fetch_array($queryPE1)) {
                    // echo "INSERT INTO Ponderar_Evaluaciones(id_empresa, anio, id_ciclo, id_tipo_competencia, auto, jefe, par, subalterno, cliente, estado, created_at)
                    // VALUES ('" . $_SESSION['id_empresa'] . "','" . $_POST["anio_ciclo"] . "','$id_temp','" . $dataPE1["id_tipo_competencia"] . "','" . $dataPE1["auto"] . "','" . $dataPE1["jefe"] . "',
                    // '" . $dataPE1["par"] . "','" . $dataPE1["subalterno"] . "','" . $dataPE1["cliente"] . "',1,'$hoy')<br>";
                    mysqli_query($connect_valoracion, "INSERT INTO Ponderar_Evaluaciones(id_empresa, anio, id_ciclo, id_tipo_competencia, auto, jefe, par, subalterno, cliente, estado, created_at)
                    VALUES ('" . $_SESSION['id_empresa'] . "','" . $_POST["anio_ciclo"] . "','$id_temp','" . $dataPE1["id_tipo_competencia"] . "','" . $dataPE1["auto"] . "','" . $dataPE1["jefe"] . "',
                    '" . $dataPE1["par"] . "','" . $dataPE1["subalterno"] . "','" . $dataPE1["cliente"] . "',1,'$hoy')");
                }
            }
        }
    }

    echo '
        <script>
            window.location = "?pg=competencias/programacion";
        </script>
        ';

    $respuesta = '
			<div class="alert alert-success" role="alert" style="margin-top:8px">
			  Información Guardada.
			</div>
		';
}

$mostar = true;

$queryUltimoCiclo = mysqli_query($connect_valoracion, "SELECT * FROM Ciclos WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '" . $_SESSION["anio_ciclo"] . "' ORDER BY id DESC ");
$dataUltimoCiclo = mysqli_fetch_array($queryUltimoCiclo);

if ($dataUltimoCiclo["fecha_termina"] > $ahora) {
    $mostar = false;
}

$query = mysqli_query($connect_valoracion, "SELECT * FROM Ciclos WHERE id = '" . $id . "' ");
$data = mysqli_fetch_array($query);

if ($id != "") {
    $mostar = true;
}

$queryVal = mysqli_query($connect_valoracion, "SELECT * FROM Ciclos WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '" . $_SESSION["anio_ciclo"] . "' AND fecha_termina = '" . $data["fecha_termina"] . "' ");

$querySM14 = mysqli_query($connect_admin, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 1 AND id_submenu = 4");
$dataSM14 = mysqli_fetch_array($querySM14);
?>

<?php include("views/layouts/ficha_competencia.php"); ?>

<style>
    .checkbox_list {
        width: 18px;
        height: 18px;
    }
    .card,
    .card-body,
    .card-footer {
        background-color: #FFFFFF !important;
    }
    /* Margen debajo de la barra de herramientas (botones) */
    .dt-buttons {
        margin-bottom: 15px !important;
    }
    /* Margen debajo de la tabla (paginación) */
	.dataTables_paginate, .dataTables_info{
		margin-top: 15px !important;
	}
</style>

<div class="container-fluid" style="max-width: 90%; margin: 0 auto;">

    <?php echo $respuesta; ?>

    <!-- TITULO -->
    <div class="card mb-3">
        <div class="card-header">
            <h3>Programación</h3>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-pills justify-content-center">
                        <li class="nav-item">
                            <a class="nav-link active" href="?pg=competencias/programacion">Ciclo Evaluación</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?pg=competencias/escala">Administrar Escala</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?pg=competencias/escala_interpretacion">Interpretación</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="?pg=competencias/evaluados">Competencias Tipo Evaluador</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?pg=competencias/ponderar">Ponderar Evaluación</a>
                        </li>
                    </ul>
                </div>
                <?php if ($_SESSION["anio_ciclo"]) { ?>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h3>Crear Ciclos</h3>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <?php if ($mostar == true) { ?>
                                    <form action="" method="post">
                                        <div class="row">
                                            <input type="hidden" value="<?php echo $id; ?>" name="id_registro">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label>Año Ciclo</label>
                                                    <select class="form-control" name="anio_ciclo" id="anio_ciclo" required>
                                                        <option value="">Selecciona...</option>
                                                        <?php
                                                        foreach ($Array_Anio as $periodo) {
                                                            if ($data["anio"] == $periodo[0]) {
                                                                echo '<option value="' . $periodo[0] . '" selected>' . $periodo[1] . '</option>';
                                                            } else {
                                                                echo '<option value="' . $periodo[0] . '">' . $periodo[1] . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label>Nombre Ciclo</label>
                                                    <input type="text" class="form-control" name="nombre" value="<?php echo $data["nombre"]; ?>">
                                                </div>

                                                <div class="col-md-3">
                                                    <label>Fecha Inicio</label>
                                                    <input type="date" class="form-control" name="fecha_inicia" value="<?php echo $data["fecha_inicia"]; ?>">
                                                </div>

                                                <div class="col-md-3">
                                                    <label>Fecha Fin</label>
                                                    <input type="date" class="form-control" name="fecha_termina" value="<?php echo $data["fecha_termina"]; ?>">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12" style="margin-top: 20px">
                                                    <button type="submit" class="btn btn-primary float-end">
                                                        Guardar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <div class="card-footer">
                    <div class="table-responsive">
                        <table border="1" id="programacion" class="display table" style="width:100%;">
                            <thead>
                                <tr>
                                    <th scope="col" style="width:50px">#</th>
                                    <th scope="col">Año</th>
                                    <th scope="col">Nombre</th>
                                    <th scope="col">Fecha Inicio</th>
                                    <th scope="col">Fecha Fin</th>
                                    <th scope="col" style="width: 90px; text-align:center">
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $count = 1;
                                $query = mysqli_query(
                                    $connect_valoracion,
                                    "SELECT *
                                    FROM Ciclos 
			                        WHERE id_empresa = '" . $_SESSION['id_empresa'] . "'
                                "
                                );
                                while ($data = mysqli_fetch_array($query)) {
                                    echo '
                                    <tr>
                                        <td scope="col" style="width:50px">' . $count . '</td>
                                        <td scope="col">' . $data["anio"] . '</td>
                                        <td scope="col">' . $data["nombre"] . '</td>
                                        <td scope="col">' . $data["fecha_inicia"] . '</td>
                                        <td scope="col">' . $data["fecha_termina"] . '</td>
                                        <td scope="col" style="width: 90px; text-align:center">
                                            <a href="?pg=competencias/programacion&id='.$data["id"].'" type="button" class="btn btn-success btn-sm" title="Editar">
                                                <i class="bx bx-pencil" title="Editar"></i>
                                            </a>
                                        </th>
                                    </tr>
                                    ';
                                    $count++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS de DataTables + Botones -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<!-- JS de DataTables + Botones -->
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script>
    $(document).ready(function() {
        $('#programacion').DataTable({
            pageLength: 50,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            dom: 'Bfrtip',
            buttons: [{
                extend: 'excelHtml5',
                text: 'Descargar Excel'
            }]
        });
    });
</script>

<!-- <script>
    $(document).ready(function() {

        $("#myInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();

            $(".myTable").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                $(this).next(".insumos").toggle($(this).text().toLowerCase().indexOf(value) > -1);
                //$(this).next(".insumos").toggle();
                //$(this).parent().next().hide();
            });

            $(".name_fil_off").parent().show();

        });

    });

    var api = 'https://wandtalent.com/seleccion/superadmin/api/';

    function Ficha_Competencia(id) {
        $('#lista_niveles').html('');
        jQuery.ajax({
                url: api + "ficha_competencia.php",
                type: 'post',
                data: {
                    id: id,
                    url: "?pg=competencias"
                },
            }).done(function(resp) {
                $("#xscript").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});
    }

    function Seter_Ficha() {

        $('[name="nombre"]').val("");
        $('[name="definicion"]').val("");
        $('[name="id_tipo"]').val("");

        $('[name="id_competencia"]').val("");

    }
</script>