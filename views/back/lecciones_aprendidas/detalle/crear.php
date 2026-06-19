<script>
    $(".menu_section").addClass("active");
    // $("#nav_lecciones_aprendidas").addClass("active");
    jQuery("#menu_lecciones_aprendidas").css("display", "none");
    $("#bt_la_detalle").addClass("current-page");
</script>

<?php
$hoy = date("Y-m-d H:i:s");
if ($_GET["id"]) {
    $_SESSION["id_leccion"] = $_GET["id"];
}

if ($_GET["id"] == "new") {
    $_SESSION["id_leccion"] = "";
}

if ($_POST["descripcion"] != "") {
    // print_r($_POST);

    $estrategico = $organizacional = $area = $equipo = $vp = 'NULL';
    $celula = $ruta = "";

    $id_owner = $_POST['id_owner_leccion'];
    if ($_POST["id_objetivo_estrategico"] != "") {
        $estrategico = $_POST["id_objetivo_estrategico"];
        $queryEstrategico = mysqli_query($connect_okrs, "SELECT * FROM Objetivos_estrategicos WHERE id = $estrategico");
        $dataEstrategico = mysqli_fetch_array($queryEstrategico);
        $id_owner = $dataEstrategico["id_responsable"];
        $ruta = "&id_obj_est=" . $_POST["id_objetivo_estrategico"];
    }

    if ($_POST["id_objetivo_estrategico_e"] != "") {
        $estrategico = $_POST["id_objetivo_estrategico_e"];
        $queryEstrategico = mysqli_query($connect_okrs, "SELECT * FROM Objetivos_estrategicos WHERE id = $estrategico");
        $dataEstrategico = mysqli_fetch_array($queryEstrategico);
        $id_owner = $dataEstrategico["id_responsable"];
    }

    if ($_POST["id_okr_organizacional"] != "") {
        $organizacional = $_POST["id_okr_organizacional"];
        $queryOrganizacional = mysqli_query($connect_okrs, "SELECT * FROM Okrs WHERE id = $organizacional");
        $dataOrganizacional = mysqli_fetch_array($queryOrganizacional);
        $id_owner = $dataOrganizacional["id_empleado"];
    }

    if ($_POST["id_okr_equipo"] != "") {
        $equipo = $_POST["id_okr_equipo"];
        $queryEquipo = mysqli_query($connect_okrs, "SELECT * FROM Okrs WHERE id = $equipo");
        $dataEquipo = mysqli_fetch_array($queryEquipo);
        $id_owner = $dataEquipo["id_empleado"];
    }

    if ($_POST["area_sel"] != "") {
        $area = $_POST["area_sel"];
        $id_owner = $_POST["id_owner_leccion"];
        $ruta = "&id_area=" . $_POST["area_sel"];
    }

    if ($_POST["vp_sel"] != "") {
        $vp = $_POST["vp_sel"];
        $ruta = "&id_vp=" . $_POST["vp_sel"];
        $id_owner = $_POST["id_owner_leccion1"];
    }

    if ($_POST["id_registro"] != "") {
        $sentencia = "
			UPDATE Lecciones_Aprendidas SET 
            descripcion = '" . $_POST["descripcion"] . "',
            anio = '" . $_POST["anio"] . "',
            fecha_inicia = '" . $_POST["fecha_inicia"] . "', 
			fecha_termina = '" . $_POST["fecha_termina"] . "',
            periodo = '" . $_POST["periodo"] . "',
            id_vp = '$vp',
            area = '$area',
            id_empleado = " . $_POST['id_owner_leccion']  . ",
            updated_at = '$hoy'          
			WHERE id = '" . $_POST["id_registro"] . "'
			";

        mysqli_query($connect_clima, $sentencia);

        $respuesta = '
			<div class="alert alert-success" role="alert">
			 	Los datos han sido actualizados
			</div>
			';
    } else {
        $sentencia = "
			INSERT INTO Lecciones_Aprendidas ( id_empresa , id_empleado, descripcion, tipo_leccion, anio, fecha_inicia, fecha_termina, periodo, id_okr_estrategico, id_okr_organizacional, id_okr_equipo, id_vp, area, estado, fecha_publicacion, created_at ) 
			VALUES 
			( '" . $_SESSION['id_empresa'] . "', $id_owner, '" . $_POST["descripcion"] . "','" . $_POST["tipo_sel"] . "', '" . $_POST["anio"] . "', '" . $_POST["fecha_inicia"] . "', '" . $_POST["fecha_termina"] . "', '" . $_POST["periodo"] . "',  $estrategico, $organizacional, $equipo, $vp ,$area , 1, '" . $hoy . "', '" . $hoy . "' )
			";
        // echo $sentencia;
        mysqli_query($connect_clima, $sentencia);
        $id_tmp = mysqli_insert_id($connect_clima);
        // $_SESSION["id_leccion"] = $id_tmp;

        if ($vp > 0) {
            $queryVPLA = mysqli_query($connect_clima, "SELECT * FROM Celula_Integrantes WHERE id_vp = $vp AND id_empresa = " . $_SESSION['id_empresa'] . "");
            if (mysqli_num_rows($queryVPLA) > 0) {
                $dataVPLA = mysqli_fetch_array($queryVPLA);
                $celula = $dataVPLA["responsables"];
            } else {
                $queryVPCI = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND unidad_corporativa = '$vp' AND role = 2 AND estado = 1 ORDER BY nombre ASC");
                while ($dataVPCI = mysqli_fetch_array($queryVPCI)) {
                    $celula .= $dataVPCI["id"] . ',';
                }

                $celula = substr($celula, 0, -1);

                $sentencia = "
			INSERT INTO Celula_Integrantes ( id_empresa , id_vp, responsables, estado, created_at ) 
			VALUES 
			( '" . $_SESSION['id_empresa'] . "', $vp, '$celula', 1, '" . $hoy . "' )
			";
                // echo $sentencia;
                mysqli_query($connect_clima, $sentencia);
            }

            $sentenciaCelula = "
        UPDATE Lecciones_Aprendidas SET 
        celula = '$celula'
        WHERE id = $id_tmp
        ";
            // echo $sentenciaCelula;
            mysqli_query($connect_clima, $sentenciaCelula);
        }
        if ($area > 0) {
            $queryVPLA = mysqli_query($connect_clima, "SELECT * FROM Celula_Integrantes WHERE id_area = $area AND id_empresa = " . $_SESSION['id_empresa'] . "");
            if (mysqli_num_rows($queryVPLA) > 0) {
                $dataVPLA = mysqli_fetch_array($queryVPLA);
                $celula = $dataVPLA["responsables"];
            } else {
                $queryAreaCI = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND area = $area AND estado = 1 ORDER BY nombre ASC");
                while ($dataAreaCI = mysqli_fetch_array($queryAreaCI)) {
                    $celula .= $dataAreaCI["id"] . ',';
                }
                $celula = substr($celula, 0, -1);
                $sentencia = "
			INSERT INTO Celula_Integrantes ( id_empresa , id_area, responsables, estado, created_at ) 
			VALUES 
			( '" . $_SESSION['id_empresa'] . "', $area, '$celula', 1, '" . $hoy . "' )
			";
                // echo $sentencia;
                mysqli_query($connect_clima, $sentencia);
            }

            $sentenciaCelula = "
        UPDATE Lecciones_Aprendidas SET 
        celula = '$celula'
        WHERE id = $id_tmp
        ";
            // echo $sentenciaCelula;
            mysqli_query($connect_clima, $sentenciaCelula);
        }
        if ($estrategico > 0) {
            $queryVPLA = mysqli_query($connect_clima, "SELECT * FROM Celula_Integrantes WHERE id_obj_estrategico = $estrategico AND id_empresa = " . $_SESSION['id_empresa'] . "");
            if (mysqli_num_rows($queryVPLA) > 0) {
                $dataVPLA = mysqli_fetch_array($queryVPLA);
                $celula = $dataVPLA["responsables"];
            } else {
                $queryOE = mysqli_query($connect_okrs, "SELECT * FROM Objetivos_estrategicos WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND id = $estrategico AND estado = 1 ORDER BY objetivo ASC");
                $dataOE = mysqli_fetch_array($queryOE);
                $id_lider = $dataOE["id_responsable"];
                $queryEMP = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id = $id_lider");
                $dataEMP = mysqli_fetch_array($queryEMP);
                $queryAreaCI = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND unidad_corporativa = ".$dataEMP["unidad_corporativa"]." AND role = 2 AND estado = 1 ORDER BY nombre ASC");
                while ($dataAreaCI = mysqli_fetch_array($queryAreaCI)) {
                    $celula .= $dataAreaCI["id"] . ',';
                }
                $celula = substr($celula, 0, -1);
                $sentencia = "
			INSERT INTO Celula_Integrantes ( id_empresa , id_obj_estrategico, responsables, estado, created_at ) 
			VALUES 
			( '" . $_SESSION['id_empresa'] . "', $estrategico, '$celula', 1, '" . $hoy . "' )
			";
                // echo $sentencia;
                mysqli_query($connect_clima, $sentencia);
            }

            $sentenciaCelula = "
        UPDATE Lecciones_Aprendidas SET 
        celula = '$celula'
        WHERE id = $id_tmp
        ";
            // echo $sentenciaCelula;
            mysqli_query($connect_clima, $sentenciaCelula);
        }

        $array_lista_la = explode(",", $celula);
        foreach ($array_lista_la as $id_resp) {
            $sentenciaCL = "
			INSERT INTO Celula_Lecciones ( id_leccion, id_empresa , id_empleado, estado, created_at ) 
			VALUES 
			( $id_tmp, '" . $_SESSION['id_empresa'] . "', $id_resp,  1, '" . $hoy . "' )
			";
            // echo $sentenciaCL;
            mysqli_query($connect_clima, $sentenciaCL);
        }

        echo '<script> window.location.href = "?pg=lecciones_aprendidas/detalle/participantes&id=' . $id_tmp . '' . $ruta . '";</script>';
    }
}



$queryOkrs = mysqli_query($connect_okrs, "SELECT * FROM Okrs WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND anio = " . $_SESSION["anio_fill"] . "");
$dataOkrs = mysqli_fetch_array($queryOkrs);

$query = mysqli_query($connect_clima, "SELECT * FROM Lecciones_Aprendidas WHERE id = '" . $_GET["id"] . "' ");
$data = mysqli_fetch_array($query);

$querySM71 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 7 AND id_submenu = 41");
$dataSM71 = mysqli_fetch_array($querySM71);

?>

<style>
    #TipoSel,
    #ObjetivoEstrategico,
    #ObjetivoEstrategicoE,
    #AreaSel,
    #VPSel,
    #VPSel1,
    #dependiente,
    #dependiente1,
    #OkrOrganizacional,
    #OkrEquipo {
        display: none;
    }

    .check_dimensiones {
        width: 150px;
        display: inline-table;
        text-align: center;
    }

    .check_box {
        width: 25px;
        height: 25px;
    }

    .borrar {
        background-color: #F30000;
        color: #ffffff;
        padding: 6px;
        border-radius: 30px;
        margin-right: 10px;
        margin-bottom: 6px;
    }

    .card-footer {
        background-color: #FFFFFF !important;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header" style="background-color: #FFFFFF !important;">
                <div class="row">
                    <div class="col-md-12">
                        <h4><i class="fas fa-book" id="iconCabecera"></i>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo $dataSM71["nombre"]; ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
<?php
if ($_GET["id_area1"] != "" || $_GET["id_vp1"] != "") {
?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12" style="text-align: end;">
                            <?php if ($_GET["id_area1"] > 0) {
                                $queryA = mysqli_query($connect_valentina, "SELECT * FROM Areas WHERE id = '" . $_GET["id_area1"] . "' ");
                                $dataA = mysqli_fetch_array($queryA);
                                $pagina = "info_area&id=" . $_GET["id_area1"];
                                $texto = "del área ".$dataA["nombre"];
                            }
                            if ($_GET["id_vp1"] > 0) {
                                $queryV = mysqli_query($connect_valentina, "SELECT * FROM Vicepresidencia WHERE id = '" . $_GET["id_vp1"] . "' ");
                                $dataV = mysqli_fetch_array($queryV);
                                $pagina = "info_vp&id=" . $_GET["id_vp1"];
                                $texto = "de la alta dirección ".$dataV["nombre"];
                            } ?>
                            <a href="<?php echo $url ?>?pg=lecciones_aprendidas/detalle/<?php echo $pagina; ?>" class="btn btn-primary" id="btnAccion">Volver a listado de lecciones <?php echo $texto; ?> </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12" align="center">

            <?php echo $respuesta; ?>

            <ul class="nav nav-tabs justify-content-center">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="<?php echo $url; ?>?pg=lecciones_aprendidas/detalle/crear" style="color:white !important;">
                        Lección Aprendida
                    </a>
                </li>
                <?php if ($_SESSION["id_leccion"]) { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $url; ?>?pg=lecciones_aprendidas/detalle/participantes&id=<?php echo $_SESSION["id_leccion"]; ?>" style="color:black !important;">
                            Célula
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12" align="center">

            <div class="card" style="margin-bottom: 15px">

                <div class="card-body">

                    <h3>Creación para publicación en el muro de Lecciones Aprendidas</h3>

                    <form action="" method="post">
                        <input type="hidden" name="id_empresa" id="id_empresa" value="<?php echo $_SESSION["id_empresa"]; ?>">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-1">
                                    <label>* Año</label>
                                    <select class="form-control" name="anio" id="anio" required onchange="anio_select(this);">
                                        <option value="">Selecciona...</option>
                                        <?php
                                        foreach ($Array_Anio as $periodo) {
                                            if ($data["anio"] ==  $periodo[0]) {
                                                echo '<option value="' . $periodo[0] . '" selected>' . $periodo[1] . '</option>';
                                            } else {
                                                echo '<option value="' . $periodo[0] . '">' . $periodo[1] . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>



                                <div class="col-md-2">
                                    <label>* Periodo Inicia</label>
                                    <input type="date" class="form-control" name="fecha_inicia" value="<?php echo $data["fecha_inicia"]; ?>" required>
                                </div>

                                <div class="col-md-2">
                                    <label>* Periodo Termina</label>
                                    <input type="date" class="form-control" name="fecha_termina" value="<?php echo $data["fecha_termina"]; ?>" required>
                                </div>
                                <div class="col-md-1">
                                    <label>* Periodo</label>
                                    <select class="form-control" name="periodo" required>
                                        <option value="">Selecciona...</option>
                                        <?php
                                        foreach ($Array_Periodos_Q as $periodo) {
                                            if ($data["periodo"] ==  $periodo[0] || $data_equipo["periodo"] ==  $periodo[0]) {
                                                echo '<option value="' . $periodo[0] . '" selected>' . $periodo[1] . '</option>';
                                            } else {
                                                echo '<option value="' . $periodo[0] . '">' . $periodo[1] . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-md-2" id="TipoSel">
                                    <label>* Tipo</label>
                                    <select class="form-control" name="tipo_sel" id="tipo_sel" onchange="select_leccion(this);">
                                        <option value="">Selecciona...</option>
                                        <?php
                                        foreach ($Array_Leccion as $tipo) {
                                            if ($data["tipo_leccion"] ==  $tipo[0]) {
                                                echo '<option value="' . $tipo[0] . '" selected>' . $tipo[1] . '</option>';
                                            } else {
                                                echo '<option value="' . $tipo[0] . '">' . $tipo[1] . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-md-2" id="ObjetivoEstrategico">
                                    <label for="">* Objetivo Estratégico</label>
                                    <select class="form-control" name="id_objetivo_estrategico" id="id_objetivo_estrategico" onchange="select_organizacional(this);">

                                    </select>
                                </div>

                                <div class="col-md-2" id="ObjetivoEstrategicoE">
                                    <label for="">* Objetivo Estratégico</label>
                                    <select class="form-control" name="id_objetivo_estrategico_e" id="id_objetivo_estrategico_e" onchange="select_equipos(this);">

                                    </select>
                                </div>

                                <div class="col-md-4" id="OkrOrganizacional">
                                    <label>* OKR Organizacional</label>
                                    <select class="form-control" id="id_okr_organizacional" name="id_okr_organizacional">
                                        <?php if ($data["id_okr_organizacional"] > 0) {

                                            $queryResultado = mysqli_query($connect_okrs, "SELECT * FROM Okrs WHERE id_empresa = '" . $data["id_empresa"] . "' AND objetivos_estrategicos LIKE '%" . $data["id_okr_estrategico"] . "%' AND tipo = 1 AND estado = 1 ORDER BY objetivo_okr ASC");

                                            while ($dataResultado = mysqli_fetch_array($queryResultado)) {

                                                if ($data["id_okr_organizacional"] == $dataResultado["id"]) {
                                                    echo '<option value="' . $dataResultado["id"] . '" selected> ' . $dataResultado["objetivo_okr"] . '</option>';
                                                } else {
                                                    echo '<option value="' . $dataResultado["id"] . '">' . $dataResultado["objetivo_okr"] . '</option>';
                                                }
                                            }
                                        } ?>
                                    </select>
                                </div>

                                <div class="col-md-4" id="OkrEquipo">
                                    <label>* OKR Equipo</label>
                                    <select class="form-control" id="id_okr_equipo" name="id_okr_equipo">
                                        <?php if ($data["id_okr_equipo"] > 0) {

                                            $queryResultadoE = mysqli_query($connect_okrs, "SELECT * FROM Okrs WHERE id_empresa = '" . $data["id_empresa"] . "' AND objetivos_estrategicos LIKE '%" . $data["id_okr_estrategico"] . "%' AND tipo = 2 AND estado = 1 ORDER BY objetivo_okr ASC");

                                            while ($dataResultadoE = mysqli_fetch_array($queryResultadoE)) {

                                                if ($data["id_okr_equipo"] == $dataResultadoE["id"]) {
                                                    echo '<option value="' . $dataResultadoE["id"] . '" selected> ' . $dataResultadoE["objetivo_okr"] . '</option>';
                                                } else {
                                                    echo '<option value="' . $dataResultadoE["id"] . '">' . $dataResultadoE["objetivo_okr"] . '</option>';
                                                }
                                            }
                                        } ?>
                                    </select>
                                </div>

                                <div class="col-md-3" id="VPSel1">
                                    <label>* Alta Dirección</label>
                                    <select class="form-control" id="vicepresidencia" name="vicepresidencia" onchange="select_vicepresidencia(this);">
                                        <option value="">Seleccione alta dirección..</option>
                                        <?php
                                        $queryVP = mysqli_query($connect_valentina, "SELECT * FROM Vicepresidencia WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND estado = 1 ORDER BY nombre");
                                        while ($dataVP = mysqli_fetch_array($queryVP)) {

                                            if ($data['area'] == $dataVP["id"]) {
                                                echo '<option value="' . $dataVP["id"] . '" selected>' . $dataVP["nombre"] . '</option>';
                                            } else {
                                                echo '<option value="' . $dataVP["id"] . '">' . $dataVP["nombre"] . '</option>';
                                            }
                                        }

                                        ?>
                                    </select>

                                </div>

                                <div class="col-md-3" id="AreaSel">
                                    <label>* Area</label>
                                    <select class="form-control" id="area_sel" name="area_sel" onchange="select_owner(this);">
                                        <option value="">Seleccione area..</option>
                                        <?php
                                        // $queryAreas = mysqli_query($connect_valentina, "SELECT * FROM Areas WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND estado = 1 ORDER BY nombre");
                                        // while ($dataAreas = mysqli_fetch_array($queryAreas)) {

                                        //     if ($data['area'] == $dataAreas["id"]) {
                                        //         echo '<option value="' . $dataAreas["id"] . '" selected>' . $dataAreas["nombre"] . '</option>';
                                        //     } else {
                                        //         echo '<option value="' . $dataAreas["id"] . '">' . $dataAreas["nombre"] . '</option>';
                                        //     }
                                        // }

                                        ?>
                                    </select>

                                </div>
                                <div class="col-md-3" id="VPSel">
                                    <label>* Alta Dirección</label>
                                    <select class="form-control" id="vp_sel" name="vp_sel" onchange="select_owner1(this);">
                                        <option value="">Seleccione alta dirección..</option>
                                        <?php
                                        $queryVP = mysqli_query($connect_valentina, "SELECT * FROM Vicepresidencia WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND estado = 1 ORDER BY nombre");
                                        while ($dataVP = mysqli_fetch_array($queryVP)) {

                                            if ($data['area'] == $dataVP["id"]) {
                                                echo '<option value="' . $dataVP["id"] . '" selected>' . $dataVP["nombre"] . '</option>';
                                            } else {
                                                echo '<option value="' . $dataVP["id"] . '">' . $dataVP["nombre"] . '</option>';
                                            }
                                        }

                                        ?>
                                    </select>

                                </div>
                                <div class="col-md-3" id="dependiente">
                                    <label>Owner * </label>
                                    <select class="form-control" name="id_owner_leccion" id="id_owner_leccion">
                                        <?php if ($data["id_empleado"] > 0) {

                                            $queryResultado = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id_empresa = '" . $data["id_empresa"] . "' AND area = '" . $data["area"] . "' ORDER BY nombre ASC");

                                            while ($dataResultado = mysqli_fetch_array($queryResultado)) {

                                                if ($id_empleado == $dataResultado["id"]) {
                                                    echo '<option value="' . $dataResultado["id"] . '" selected> ' . $dataResultado["nombre"] . '</option>';
                                                } else {
                                                    echo '<option value="' . $dataResultado["id"] . '">' . $dataResultado["nombre"] . '</option>';
                                                }
                                            }
                                        } ?>
                                    </select>
                                </div>

                                <div class="col-md-3" id="dependiente1">
                                    <label>Owner * </label>
                                    <select class="form-control" name="id_owner_leccion1" id="id_owner_leccion1">
                                        <?php if ($data["id_empleado"] > 0) {

                                            $queryResultado = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id_empresa = '" . $data["id_empresa"] . "' AND unidad_corporativa = '" . $data["id_vp"] . "' AND role = 2 ORDER BY nombre ASC");

                                            while ($dataResultado = mysqli_fetch_array($queryResultado)) {

                                                if ($id_empleado == $dataResultado["id"]) {
                                                    echo '<option value="' . $dataResultado["id"] . '" selected> ' . $dataResultado["nombre"] . '</option>';
                                                } else {
                                                    echo '<option value="' . $dataResultado["id"] . '">' . $dataResultado["nombre"] . '</option>';
                                                }
                                            }
                                        } ?>
                                    </select>
                                </div>

                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <input type="hidden" name="id_registro" value="<?php echo $data["id"]; ?>">
                                <input type="hidden" name="idEmpresa" value="<?php echo $_SESSION["id_empresa"]; ?>" id="idEmpresa">
                                <input type="hidden" name="idEmpleado" value="<?php echo $data["id_empleado"]; ?>" id="idEmpleado">
                                <input type="hidden" name="anioOkr" value="" id="anioOkr">
                                <div class="col-md-12">
                                    <label>* Descripción de la lección aprendida</label>
                                    <textarea rows="3" class="form-control" name="descripcion" required placeholder="Ingrese la descripción de la lección aprendida..."><?php echo $data["descripcion"]; ?></textarea>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12" style="margin-top: 15px">
                                    <button type="submit" class="btn btn-success ">Guardar</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <?php if ($_SESSION["id_leccion"]) { ?>
                    <div class="card-footer" align="center">
                        <a href="<?php echo $url; ?>?pg=lecciones_aprendidas/detalle/participantes&id=<?php echo $_SESSION["id_leccion"]; ?>">
                            <button type="button" class="btn btn-secondary w-25 " style="float: right">Siguiente >> </button>
                        </a>
                    </div>
                <?php } ?>

            </div>


        </div>

    </div>

</div>

<script>
    var api = '<?php echo $url; ?>api/desempenio/';
    var api_admin = '<?php echo $url; ?>api/administrar/';

    var activar = false;

    function Elimimar(id) {

        if (activar == false) {
            $("#modal_general").modal("show");
            $("#modal_body").html('Está a punto de eliminar un objetivo, esta acción es irreversible ¿está seguro?<br><br>');
            $("#modal_body").append('<button type="button" class="btn btn-danger btn-sm" onclick="activar= true; Elimimar(' + id + ')"> Confirmar </button>');
        } else {

            jQuery.ajax({
                    url: api + "eliminar_objetivo.php",
                    type: 'post',
                    data: {
                        id: id,
                        url: "?pg=desempenio/objetivos"
                    },
                }).done(function(resp) {
                    $("#xscript").html(resp);
                })
                .fail(function(resp) {
                    console.log(resp);
                })
                .always(function(resp) {});

        }
    }


    function EliminarOKRs() {
        if (activar == false) {
            $("#modal_general").modal("show");
            $("#modal_body").html('Está a punto de eliminar este OKRS. esto eliminará los responsables, resultados, iniciativas y avances. ESTA ACCIÓN ES IRREVERSIBLE. se perderán los datos. ¿está seguro?<br><br>');
            $("#modal_body").append('<button type="button" class="btn btn-danger btn-sm" onclick="activar= true; EliminarOKRs()"> Confirmar </button>');
        } else {
            $("#eliminar_registro").submit();
        }
    }

    $(function() {
        $('#anio').change(function() {
            var yearOkr = $(this).val();
            $("#anioOkr").val(yearOkr);
        }).change();
    });

    // $(document).ready(function(){
    function select_owner() {

        $("#area_sel option:selected").each(function() {
            var empresa = $("#idEmpresa").val();
            var idEmpleado = $("#idEmpleado").val();
            area = $(this).val();
            console.log(idEmpleado);
            $.post("views/lecciones_aprendidas/detalle/owner_leccion.php", {
                id_area: area,
                id_empleado: idEmpleado,
                id_empresa: empresa
            }, function(data) {
                $("#id_owner_leccion").html(data);

            });
        });

    }

    function select_owner1() {

        $("#vp_sel option:selected").each(function() {
            var empresa = $("#idEmpresa").val();
            var idEmpleado = $("#idEmpleado").val();
            vp = $(this).val();
            console.log(idEmpleado);
            $.post("views/lecciones_aprendidas/detalle/owner_leccion_vp.php", {
                id_vp: vp,
                id_empleado: idEmpleado,
                id_empresa: empresa
            }, function(data) {
                $("#id_owner_leccion1").html(data);
            });
        });

    }

    function anio_select() {
        $("#anio option:selected").each(function() {
            anio = $(this).val();
            console.log(anio);
            if (anio > 0) {
                document.getElementById("TipoSel").style.display = "block";
                document.getElementById("tipo_sel").required = true;
            } else {
                document.getElementById("TipoSel").style.display = "none";
                document.getElementById("tipo_sel").required = false;
            }
        });
    }

    function select_leccion() {

        $("#tipo_sel option:selected").each(function() {
            var empresa = $("#idEmpresa").val();
            var anio = $("#anio").val();
            var objetivoEstrategico = $("#id_objetivo_estrategico").val();
            var objetivoEstrategicoE = $("#id_objetivo_estrategico_e").val();
            var okrOrganizacional = $("#id_okr_organizacional").val();
            var okrEquipo = $("#id_okr_equipo").val();

            tipo = $(this).val();
            console.log(tipo);

            if (tipo != '') {
                switch (tipo) {
                    case '1':
                        document.getElementById("VPSel1").style.display = "block";
                        document.getElementById("AreaSel").style.display = "block";
                        document.getElementById("dependiente").style.display = "block";
                        document.getElementById("area_sel").required = true;
                        document.getElementById("VPSel").style.display = "none";
                        document.getElementById("vp_sel").required = false;
                        document.getElementById("id_owner_leccion").required = true;
                        document.getElementById("dependiente1").style.display = "none";
                        document.getElementById("id_owner_leccion1").required = false;
                        document.getElementById("id_objetivo_estrategico").required = false;
                        document.getElementById("id_objetivo_estrategico_e").required = false;
                        document.getElementById("id_okr_organizacional").required = false;
                        document.getElementById("id_okr_equipo").required = false;
                        document.getElementById("ObjetivoEstrategico").style.display = "none";
                        document.getElementById("ObjetivoEstrategicoE").style.display = "none";
                        document.getElementById("OkrOrganizacional").style.display = "none";
                        document.getElementById("OkrEquipo").style.display = "none";
                        break;
                    case '2':
                        document.getElementById("VPSel1").style.display = "none";
                        document.getElementById("AreaSel").style.display = "none";
                        document.getElementById("dependiente").style.display = "none";
                        document.getElementById("VPSel").style.display = "none";
                        document.getElementById("dependiente").style.display = "none";
                        document.getElementById("vp_sel").required = false;
                        document.getElementById("area_sel").required = false;
                        document.getElementById("id_owner_leccion").required = false;
                        document.getElementById("id_owner_leccion1").required = false;
                        document.getElementById("id_objetivo_estrategico").required = true;
                        document.getElementById("id_objetivo_estrategico_e").required = false;
                        document.getElementById("id_okr_organizacional").required = false;
                        document.getElementById("id_okr_equipo").required = false;
                        document.getElementById("ObjetivoEstrategico").style.display = "block";
                        document.getElementById("ObjetivoEstrategicoE").style.display = "none";
                        document.getElementById("OkrOrganizacional").style.display = "none";
                        document.getElementById("OkrEquipo").style.display = "none";
                        $.post("views/lecciones_aprendidas/detalle/objetivos_estrategicos.php", {
                            anio: anio,
                            id_empresa: empresa
                        }, function(data) {
                            $("#id_objetivo_estrategico").html(data);
                        });
                        break;
                    case '3':
                        document.getElementById("VPSel1").style.display = "none";
                        document.getElementById("AreaSel").style.display = "none";
                        document.getElementById("dependiente").style.display = "none";
                        document.getElementById("VPSel").style.display = "none";
                        document.getElementById("dependiente1").style.display = "none";
                        document.getElementById("area_sel").required = false;
                        document.getElementById("vp_sel").required = false;
                        document.getElementById("id_owner_leccion").required = false;
                        document.getElementById("id_owner_leccion1").required = false;
                        document.getElementById("id_objetivo_estrategico").required = false;
                        document.getElementById("id_objetivo_estrategico_e").required = true;
                        document.getElementById("id_okr_organizacional").required = false;
                        document.getElementById("id_okr_equipo").required = true;
                        document.getElementById("ObjetivoEstrategico").style.display = "none";
                        document.getElementById("ObjetivoEstrategicoE").style.display = "block";
                        document.getElementById("OkrOrganizacional").style.display = "none";
                        document.getElementById("OkrEquipo").style.display = "block";
                        $.post("views/lecciones_aprendidas/detalle/objetivos_estrategicos.php", {
                            anio: anio,
                            id_empresa: empresa
                        }, function(data) {
                            $("#id_objetivo_estrategico_e").html(data);
                        });
                        $.post("views/lecciones_aprendidas/detalle/objetivos_equipo.php", {
                            anio: anio,
                            id_empresa: empresa
                        }, function(data) {
                            $("#id_okr_equipo").html(data);
                        });
                        break;
                    case '4':
                        document.getElementById("VPSel1").style.display = "none";
                        document.getElementById("AreaSel").style.display = "none";
                        document.getElementById("dependiente").style.display = "none";
                        document.getElementById("VPSel").style.display = "none";
                        document.getElementById("dependiente1").style.display = "none";
                        document.getElementById("area_sel").required = false;
                        document.getElementById("vp_sel").required = false;
                        document.getElementById("id_owner_leccion").required = false;
                        document.getElementById("id_owner_leccion1").required = false;
                        document.getElementById("id_objetivo_estrategico").required = true;
                        document.getElementById("id_objetivo_estrategico_e").required = false;
                        document.getElementById("id_okr_organizacional").required = true;
                        document.getElementById("id_okr_equipo").required = false;
                        document.getElementById("ObjetivoEstrategico").style.display = "block";
                        document.getElementById("ObjetivoEstrategicoE").style.display = "none";
                        document.getElementById("OkrOrganizacional").style.display = "block";
                        document.getElementById("OkrEquipo").style.display = "none";
                        $.post("views/lecciones_aprendidas/detalle/objetivos_estrategicos.php", {
                            anio: anio,
                            id_empresa: empresa
                        }, function(data) {
                            $("#id_objetivo_estrategico").html(data);
                        });
                        $.post("views/lecciones_aprendidas/detalle/objetivos_organizacionales.php", {
                            anio: anio,
                            id_empresa: empresa
                        }, function(data) {
                            $("#id_okr_organizacional").html(data);
                        });
                        break;
                    case '5':
                        document.getElementById("VPSel").style.display = "block";
                        document.getElementById("dependiente1").style.display = "block";
                        document.getElementById("vp_sel").required = true;
                        document.getElementById("id_owner_leccion1").required = true;
                        document.getElementById("area_sel").required = false;
                        document.getElementById("VPSel1").style.display = "none";
                        document.getElementById("AreaSel").style.display = "none";
                        document.getElementById("dependiente").style.display = "none";
                        document.getElementById("id_owner_leccion").required = false;
                        document.getElementById("id_objetivo_estrategico").required = false;
                        document.getElementById("id_objetivo_estrategico_e").required = false;
                        document.getElementById("id_okr_organizacional").required = false;
                        document.getElementById("id_okr_equipo").required = false;
                        document.getElementById("ObjetivoEstrategico").style.display = "none";
                        document.getElementById("ObjetivoEstrategicoE").style.display = "none";
                        document.getElementById("OkrOrganizacional").style.display = "none";
                        document.getElementById("OkrEquipo").style.display = "none";
                        break;
                }
            }


        });

    }

    function select_organizacional() {

        $("#id_objetivo_estrategico option:selected").each(function() {
            var empresa = $("#idEmpresa").val();
            var idEmpleado = $("#idEmpleado").val();
            var objetivoOrganizacional = $("#idOrganizacional").val();
            var anio = $("#anio").val();
            obj_estrategico = $(this).val();
            console.log(obj_estrategico);
            $.post("views/lecciones_aprendidas/detalle/objetivos_organizacionales.php", {
                obj_estrategico: obj_estrategico,
                id_empleado: idEmpleado,
                id_empresa: empresa,
                objetivoOrganizacional: objetivoOrganizacional,
                anio: anio
            }, function(data) {
                $("#id_okr_organizacional").html(data);

            });
        });

    }

    function select_equipos() {

        $("#id_objetivo_estrategico_e option:selected").each(function() {
            var empresa = $("#idEmpresa").val();
            var idEmpleado = $("#idEmpleado").val();
            var objetivoEquipo = $("#idEquipo").val();
            var anio = $("#anio").val();
            obj_estrategico = $(this).val();
            console.log(obj_estrategico);
            $.post("views/lecciones_aprendidas/detalle/objetivos_equipo.php", {
                obj_estrategico: obj_estrategico,
                id_empleado: idEmpleado,
                id_empresa: empresa,
                objetivoEquipo: objetivoEquipo,
                anio: anio
            }, function(data) {
                $("#id_okr_equipo").html(data);

            });
        });

    }

    function select_vicepresidencia() {

        $("#vicepresidencia option:selected").each(function() {
            var empresa = $("#id_empresa").val();
            var area = $("#id_area").val();
            id = $(this).val();
            // console.log(id);
            $.post(api_admin + "cargar_vicepresidencias.php", {
                id: id,
                id_empresa: empresa,
                area: area
            }, function(data) {
                $("#area_sel").html(data);
                var foption = $('#area_sel option:first');
                var soptions = $('#area_sel option:not(:first)').sort(function(a, b) {
                    return a.text == b.text ? 0 : a.text < b.text ? -1 : 1
                });
                $('#area_sel').html(soptions).prepend(foption);
            });
        });

    }
</script>