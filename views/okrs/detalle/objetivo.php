<script>
$(document).ready(function() {
    $('#menuOkrs').collapse();
    $('#bt_okrs_crear').addClass('active');
});
</script>

<?php
$hoy = date("Y-m-d H:i:s");
if ($_GET["id"]) {
    $_GET["id"] = $_GET["id"];
}

if ($_GET["id"] == "new") {
    $_GET["id"] = "";
}

if ($_POST["objetivo_okr"] != "") {
    if ($_POST["objetivos_estrategicos"] != "") {
        $objetivos_estrategicos = implode(",", $_POST["objetivos_estrategicos"]);
    } else {
        if ($_POST["estrategico1_pc"] != "") {
            $objetivos_estrategicos = $_POST["estrategico1_pc"];
        }
        if ($_POST["estrategico2_pc"] != "") {
            $objetivos_estrategicos = $_POST["estrategico2_pc"];
        }
    }
    if ($_POST["anio1"] != "") {
        $anio = $_POST["anio1"];
    }
    if ($_POST["anio2"] != "") {
        $anio = $_POST["anio2"];
    }
    if ($_POST["id_registro"] != "") {
        $sentencia = "
			UPDATE Okrs SET id_empleado = '" . $_POST["owner"] . "',id_resultado_padre = '" . $_POST["id_resultado_padre"] . "', tipo = '" . $_POST["tipo"] . "', responsables = '" . implode(",", $_POST["responsables"]) . "', objetivos_estrategicos = '" . $objetivos_estrategicos . "',  objetivo_okr  = '" . $_POST["objetivo_okr"] . "', fecha_inicia = '" . $_POST["fecha_inicia"] . "', 
			fecha_termina = '" . $_POST["fecha_termina"] . "', periodo = '" . $_POST["periodo"] . "', anio = '" . $anio . "', exigible = '0', id_empleado = '" . $_POST['owner'] . "' 
			WHERE id = '" . $_POST["id_registro"] . "'
			";

        // mysqli_query($connect_okrs, $sentencia);        

        $sentencia1 = "SELECT * FROM Okrs_Equipos WHERE id_okrs = '" . $_POST["id_registro"] . "' LIMIT 1";
        $query = mysqli_query($connect_okrs, $sentencia1);
        $dataEquipo = mysqli_fetch_array($query);

        $sentencia3 = "
			UPDATE Okrs_Equipos SET id_empleado = '" . $_POST["owner"] . "' WHERE id = '" . $dataEquipo["id"] . "'
			";

        // mysqli_query($connect_okrs, $sentencia3);

        $accion = 'ACTUALIZAR';
        $descripcion = 'Actualización OKR ' . $_POST["objetivo_okr"];

        $auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
	    VALUES (" . $_SESSION['id_empresa'] . ", " . $_SESSION['id_user'] . ",'$accion','$descripcion'," . $_POST["tipo"] . "," . $_POST["id_registro"] . ",0,0,'$hoy')";
        // echo $auditoria;
        // mysqli_query($connect_okrs, $auditoria);

        $respuesta = '
			<div class="alert alert-success" role="alert">
			 	Los datos han sido actualizados
			</div>
			';
    } else {
        $sentencia = "
			INSERT INTO  Okrs ( id_empresa , id_resultado_padre, tipo, id_empleado, responsables , objetivos_estrategicos ,  objetivo_okr , fecha_inicia, fecha_termina, periodo, anio, exigible,  estado, created_at ) 
			VALUES 
			( '" . $_SESSION['id_empresa'] . "', '" . $_POST["id_resultado_padre"] . "', '" . $_POST["tipo"] . "', '" . $_POST["owner"] . "',  '" . implode(",", $_POST["responsables"]) . "', '" . $objetivos_estrategicos . "', 
			'" . $_POST["objetivo_okr"] . "', '" . $_POST["fecha_inicia"] . "', '" . $_POST["fecha_termina"] . "', '" . $_POST["periodo"] . "', '" . $anio . "', 1, '0', '" . $hoy . "' )
			";

        // echo $sentencia;
        mysqli_query($connect_okrs, $sentencia);
        $id_tmp = mysqli_insert_id($connect_okrs);
        $_GET["id"] = $id_tmp;

        $sentencia_vicepresidencia = "
                INSERT INTO Okrs_Vicepresidencia ( id_empresa , id_vicepresidencia, id_obj_estrategico, id_obj_organizacional,id_okrs, created_at ) 
                VALUES 
                ( '" . $_SESSION['id_empresa'] . "', '" . $_POST["vicepresidencia"] . "','" . $objetivos_estrategicos . "' ,'" . $_POST["id_resultado_padre"] . "' , '" . $id_tmp . "','" . $hoy . "' )
                ";
                // echo $sentencia_vicepresidencia;
        mysqli_query($connect_okrs, $sentencia_vicepresidencia);

        //CREAR EL PRIMER MIEMBRO DE EQUIPO
                   

            $sentencia_equipo = "
					INSERT INTO  Okrs_Equipos ( id_empresa , id_okrs, id_empleado, tipo,  estado ,  created_at ) 
					VALUES 
					( '" . $_SESSION['id_empresa'] . "', '" . $id_tmp . "', '" . $_POST["owner"] . "', '1', 1, '" . $hoy . "' )
					";
                    // echo $sentencia_equipo;
            mysqli_query($connect_okrs, $sentencia_equipo);
        

        $accion = 'CREAR';
        $descripcion = 'Creación OKR ' . $_POST["objetivo_okr"];

        $auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
	    VALUES (" . $_SESSION['id_empresa'] . ", " . $_SESSION['id_user'] . ",'$accion','$descripcion'," . $_POST["tipo"] . ",$id_tmp,0,0,'$hoy')";
        // echo $auditoria;
        mysqli_query($connect_okrs, $auditoria);

        if ($_POST["tipo"] == 1) {
            echo '<script> window.location.href = "?pg=okrs_equipos/detalle/objetivo_organizacional&id=' . $id_tmp . '";</script>';
        }

        if ($_POST["tipo"] == 2) {
            echo '<script> window.location.href = "?pg=okrs_equipos/detalle/objetivo_equipo&id=' . $id_tmp . '";</script>';
        }
    }
}

if ($_POST["delete_registro"]) {
    mysqli_query($connect_okrs, "DELETE FROM Okrs WHERE id = '" . $_POST["id_registro"] . "' ");
    mysqli_query($connect_okrs, "DELETE FROM Okrs_Actividades 
		WHERE id_okrs = '" . $_POST["id_registro"] . "' ");
    mysqli_query($connect_okrs, "DELETE FROM Okrs_Comentarios 
		WHERE id_okrs = '" . $_POST["id_registro"] . "' ");
    mysqli_query($connect_okrs, "DELETE FROM Okrs_Documentos 
		WHERE id_okrs = '" . $_POST["id_registro"] . "' ");
    mysqli_query($connect_okrs, "DELETE FROM Okrs_Equipos WHERE id_okrs = '" . $_POST["id_registro"] . "' ");
    mysqli_query($connect_okrs, "DELETE FROM Okrs_Iniciativas 
		WHERE id_okrs = '" . $_POST["id_registro"] . "' ");
    mysqli_query($connect_okrs, "DELETE FROM Okrs_Resultados 
		WHERE id_okrs = '" . $_POST["id_registro"] . "' ");
    echo '<script> window.location.href = "?pg=okrs_equipos/home_celula";</script>';
}

$query = mysqli_query($connect_okrs, "SELECT * FROM Okrs WHERE id = '" . $_GET["id"] . "' ");
$data = mysqli_fetch_array($query);
// print_r($data);
if ($data["anio"]) {
    $anio = $data["anio"];
} else {
    $anio = $data_equipo["anio"];
}

$querySM81 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 8 AND id_submenu = 45");
$dataSM81 = mysqli_fetch_array($querySM81);

include("views/okrs_equipos/etiquetas.php");
$Array_Tipo_OKR1 = array(
	array("1", "Okrs Organizacional" ), 
    array("2", "Okrs Equipo"),
);
?>



<style>
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

    #dependiente_pc,
    #estrategico1_pc,
    #estrategico2_pc,
    #estrategico_desc_pc,
    #anioOkrSelect1,
    #anioOkrSelect2,
    #vicepresidencia {
        display: none;
    }

    .select2-container {
        width: 100% !important;
    }

    .card-footer,
    .card-body,
     {
        background-color: #FFFFFF !important;
    }
</style>


<div class="container">

    <?php echo $respuesta; ?>

    <div class="card">
        <div class="card-header">
            <h3>Ficha del OKRs</h3>
        </div>
        <div class="card-body">

            <form action="" method="POST">
            <input type="hidden" name="id_registro" value="<?php echo $data["id"]; ?>">

            <div class="row">

                <div class="col-md-3">
                    <label>Tipo de OKRs *</label>
                    <select class="form-control" name="tipo" id="tipo_objetivo" required>
                        <option value="">Selecciona...</option>
                        <?php
                        foreach ($Array_Tipo_OKR1 as $periodo) {
                            if ($data["tipo"] ==  $periodo[0] || $data_equipo["tipo"] ==  $periodo[0]) {
                                echo '<option value="' . $periodo[0] . '" selected>' . $periodo[1] . '</option>';
                            } 
                            else {
                                echo '<option value="' . $periodo[0] . '">' . $periodo[1] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label>* Año</label>
                    <select class="form-control" name="anio1" id="anio1" >
                        <option value="">Selecciona...</option>
                        <?php
                        foreach ($Array_Anio as $periodo) {
                            if ($data["anio"] ==  $periodo[0] || $data_equipo["anio"] ==  $periodo[0]) {
                                echo '<option value="' . $periodo[0] . '" selected>' . $periodo[1] . '</option>';
                            } 
                            else {
                                echo '<option value="' . $periodo[0] . '">' . $periodo[1] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-4" id="vicepresidencia">
                    <label>Alta Dirección</label>
                    <select class="multiples_responsables form-control" id="id_vicepresidencia" name="vicepresidencia" onchange="select_estrategico_vp(this);">
                        <option value="">Seleccione <?php echo $etiquetaOkrAD; ?>..</option>
                        <?php
                        $queryVicepresidencias = mysqli_query($connect_admin, "SELECT * FROM Vicepresidencia WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND estado = 1 ORDER BY nombre ASC");
                        while ($dataVicepresidencia = mysqli_fetch_array($queryVicepresidencias)) {
                            if ($id_estrategico == $dataVicepresidencia["id"]) {
                                echo '<option value="' . $dataVicepresidencia["id"] . '" selected>' . $dataVicepresidencia["nombre"] . '</option>';
                            } 
                            else {
                                echo '<option value="' . $dataVicepresidencia["id"] . '">' . $dataVicepresidencia["nombre"] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

            </div>
            </form>






            <form action="" method="post">
                        <div class="form-group">
                            <div class="row">

                                <input type="hidden" name="id_registro" value="<?php echo $data["id"]; ?>">
                                <input type="hidden" name="resultado_padre" value="<?php echo $data_equipo["id_resultado_padre"]; ?>" id="resultado_padre">
                                <input type="hidden" name="resultado_estrategico" value="<?php if ($data["objetivos_estrategicos"]) {
                                                                                                echo $data["objetivoss_estrategico"];
                                                                                            } else {
                                                                                                echo $data_equipo["objetivos_estrategicos"];
                                                                                            } ?>" id="resultado_estrategico">
                                <input type="hidden" name="idEmpresa" value="<?php echo $_SESSION["id_empresa"]; ?>" id="idEmpresa">
                                <input type="hidden" name="anioOkr" value="<?php echo $anio; ?>" id="anioOkr">
                                <input type="hidden" name="idVicepresidencia" value="<?php echo $anio; ?>" id="idVicepresidencia">
                                
                                
                                <div class="col-md-2" id="anioOkrSelect2">
                                    <label>* Año</label>
                                    <select class="form-control" name="anio2" id="anio2" onchange="select_vicepresidencia(this);">
                                        <option value="">Selecciona...</option>
                                        <?php
                                        foreach ($Array_Anio as $periodo) {
                                            if ($data["anio"] ==  $periodo[0] || $data_equipo["anio"] ==  $periodo[0]) {
                                                echo '<option value="' . $periodo[0] . '" selected>' . $periodo[1] . '</option>';
                                            } else {
                                                echo '<option value="' . $periodo[0] . '">' . $periodo[1] . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-4" id="estrategico1_pc">
                                    <label><?php echo $etiquetaOkrLOE; ?></label>
                                    <select class="multiples_responsables form-control" id="id_objetivo1" name="estrategico1_pc">
                                        <option value="">Seleccione ..</option>
                                        <?php

                                        if ($data || $data_equipo) {
                                            if ($data["anio"]) {
                                                $anio = $data["anio"];
                                            } else {
                                                $anio = $data_equipo["anio"];
                                            }
                                            if ($data["objetivos_estrategicos"]) {
                                                $id_estrategico = $data["objetivos_estrategicos"];
                                            } else {
                                                $id_estrategico = $data_equipo["objetivos_estrategicos"];
                                            }

                                            $queryObjEstrategicos = mysqli_query($connect_okrs, "SELECT id, objetivo FROM Objetivos_estrategicos WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND anio = '" . $anio . "'");

                                            while ($dataObjEstra = mysqli_fetch_array($queryObjEstrategicos)) {

                                                if ($id_estrategico == $dataObjEstra["id"]) {
                                                    echo '<option value="' . $dataObjEstra["id"] . '" selected>' . $dataObjEstra["objetivo"] . '</option>';
                                                } else {
                                                    echo '<option value="' . $dataObjEstra["id"] . '">' . $dataObjEstra["objetivo"] . '</option>';
                                                }
                                            }
                                        }
                                        ?>
                                    </select>

                                </div>
                                <div class="col-md-4" id="estrategico2_pc">
                                    <label><?php echo $etiquetaOkrLOE; ?></label>
                                    <select class="multiples_responsables form-control" id="id_objetivo2" name="estrategico2_pc" onchange="select_organizacional(this);">
                                        <option value="">Seleccione ..</option>
                                        <?php

                                        $queryObjEstrategicos = mysqli_query($connect_okrs, "SELECT id, objetivo FROM Objetivos_estrategicos WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND anio = '" . $anio . "'");

                                        while ($dataObjEstra = mysqli_fetch_array($queryObjEstrategicos)) {

                                            if ($id_estrategico == $dataObjEstra["id"]) {
                                                echo '<option value="' . $dataObjEstra["id"] . '" selected>' . $dataObjEstra["objetivo"] . '</option>';
                                            } else {
                                                echo '<option value="' . $dataObjEstra["id"] . '">' . $dataObjEstra["objetivo"] . '</option>';
                                            }
                                        }

                                        ?>
                                    </select>

                                </div>

                                <div class="col-md-4" id="dependiente_pc" <?php if ($data_equipo["tipo"]) {
                                                                                echo 'style="display:block"';
                                                                            } ?>>
                                    <label><?php echo $etiquetaOkrOOD; ?> </label>
                                    <select class="multiples_responsables form-control" name="id_resultado_padre" id="id_resultado_padre">
                                        <option value="">Seleccione..</option>
                                        <?php
                                        if ($data_equipo) {
                                            // $queryOkrsResultado = mysqli_query($connect_okrs,"SELECT Okrs_Resultados.id, Okrs_Resultados.descripcion , Okrs.objetivo_okr AS objetivo_okr 
                                            // FROM Okrs_Resultados 
                                            // LEFT JOIN Okrs ON Okrs.id = Okrs_Resultados.id_okrs
                                            // WHERE Okrs.id_empresa = '".$_SESSION["id_empresa"]."' AND Okrs.anio = '".$_SESSION["anio"]."' AND Okrs.tipo = 1 ORDER BY Okrs_Resultados.id_okrs ASC  
                                            // ");

                                            $queryOkrsResultado = mysqli_query($connect_okrs, "SELECT id, objetivo_okr FROM Okrs WHERE tipo = 1 AND id_empresa = '" . $_SESSION["id_empresa"] . "' AND anio = '" . $data_equipo["anio"] . "' AND objetivos_estrategicos = '" . $data_equipo["objetivos_estrategicos"] . "'");

                                            while ($dataOkrsResultado = mysqli_fetch_array($queryOkrsResultado)) {
                                                if ($data_equipo["id_resultado_padre"] == $dataOkrsResultado["id"]) {
                                                    echo '<option value="' . $dataOkrsResultado["id"] . '" selected> ' . $dataOkrsResultado["objetivo_okr"] . '</option>';
                                                } else {
                                                    echo '<option value="' . $dataOkrsResultado["id"] . '">' . $dataOkrsResultado["objetivo_okr"] . '</option>';
                                                }
                                            }
                                        }

                                        ?>
                                    </select>
                                </div>



                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">

                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">

                                <div class="col-md-12">
                                    <label>Descripción del <?php echo $etiquetaOkr; ?> (O) *</label>
                                    <textarea rows="3" class="form-control" name="objetivo_okr" required placeholder="Ingrese su O en este espacio..."><?php if ($data) {
                                                                                                                                                            echo $data["objetivo_okr"];
                                                                                                                                                        } else {
                                                                                                                                                            echo $data_equipo["objetivo_okr"];
                                                                                                                                                        } ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                               
                                <div class="col-md-2">
                                    <label>* Periodo Inicia</label>
                                    <input type="date" class="form-control" name="fecha_inicia" value="<?php if ($data) {
                                                                                                            echo $data["fecha_inicia"];
                                                                                                        } else {
                                                                                                            echo $data_equipo["fecha_inicia"];
                                                                                                        } ?>" required>
                                </div>

                                <div class="col-md-2">
                                    <label>* Periodo Termina</label>
                                    <input type="date" class="form-control" name="fecha_termina" value="<?php if ($data) {
                                                                                                            echo $data["fecha_termina"];
                                                                                                        } else {
                                                                                                            echo $data_equipo["fecha_termina"];
                                                                                                        } ?>" required>
                                </div>

                                <div class="col-md-2">
                                    <label>Periodo *</label>
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
                                <div class="col-md-4">
                                    <label>Owner *</label>
                                    <select class="multiples_responsables form-control" name="owner" required>
                                        <option value="">Selecciona...</option>
                                        <?php
                                        $queryOwner = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND role IN (1,2) AND estado = 1 ORDER BY nombre");

                                        while ($dataOwner = mysqli_fetch_array($queryOwner)) {

                                            if ($data['id_empleado'] == $dataOwner["id"]) {
                                                echo '<option value="' . $dataOwner["id"] . '" selected>' . $dataOwner["nombre"] . '</option>';
                                            } else {
                                                echo '<option value="' . $dataOwner["id"] . '">' . $dataOwner["nombre"] . '</option>';
                                            }
                                        }

                                        ?>
                                    </select>
                                </div>

                                <script>
                                    $(document).ready(function() {
                                        $('.multiples_responsables').select2();
                                    });
                                </script>



                            </div>
                        </div>


                        <div align="right" style="margin-top: 15px">
                            <button type="button" class="btn btn-primary btn-block " onClick="Agregar_Objetivos()">
                                Agregar Objetivos Estratégicos
                            </button>
                        </div>


                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12" style="margin-top: 10px">
                                    <div id="lista_objetivos">
                                        <?php
                                        if ($data) {
                                            $objetivos_estrategicos = $data["objetivos_estrategicos"];
                                        } else {
                                            $objetivos_estrategicos = $data_equipo["objetivos_estrategicos"];
                                        }
                                        if ($objetivos_estrategicos) {
                                            $array_objetivos_estra = explode(",", $objetivos_estrategicos);
                                            foreach ($array_objetivos_estra as $obj_estra) {
                                                $queryObjEst = mysqli_query($connect_okrs, "SELECT * FROM Objetivos_estrategicos WHERE id = '" . $obj_estra . "' ");
                                                $dataObjEst = mysqli_fetch_array($queryObjEst);

                                                echo '<div id="obj_' . $dataObjEst["id"] . '"> <i class="fas fa-trash borrar" onClick="Eliminar_Objetivos(' . $dataObjEst["id"] . ')"></i> <b>' . $dataObjEst["objetivo"] . '</b> <input type="hidden" name="objetivos_estrategicos[]" value="' . $dataObjEst["id"] . '" ></div>';


                                                //echo '<b>'.$dataObjEst["objetivo"].'</b> <input type="hidden" name="objetivos_estrategicos[]" value="'.$dataObjEst["id"].'" ><br> ';
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>

                                <script>
                                    function Agregar_Objetivos() {
                                        id_objetivo1 = $("#id_objetivo1").val();
                                        id_objetivo2 = $("#id_objetivo2").val();
                                        if (id_objetivo1) {
                                            objetivo = $("#id_objetivo1 option:selected").text();

                                            string = '<div id="obj_' + id_objetivo1 + '"> <i class="fas fa-trash borrar" onClick="Eliminar_Objetivos(' + id_objetivo1 + ')"></i> <b>' + objetivo + '</b> <input type="hidden" name="objetivos_estrategicos[]" value="' + id_objetivo1 + '" ></div> ';

                                            //string = '<b>'+objetivo+'</b> <input type="hidden" name="objetivos_estrategicos[]" value="'+id_objetivo+'" ><br> ';
                                            $("#lista_objetivos").append(string);

                                            $("#id_objetivo1").val("");
                                        }
                                        if (id_objetivo2) {
                                            objetivo = $("#id_objetivo2 option:selected").text();

                                            string = '<div id="obj_' + id_objetivo2 + '"> <i class="fas fa-trash borrar" onClick="Eliminar_Objetivos(' + id_objetivo2 + ')"></i> <b>' + objetivo + '</b> <input type="hidden" name="objetivos_estrategicos[]" value="' + id_objetivo2 + '" ></div> ';

                                            //string = '<b>'+objetivo+'</b> <input type="hidden" name="objetivos_estrategicos[]" value="'+id_objetivo+'" ><br> ';
                                            $("#lista_objetivos").append(string);

                                            $("#id_objetivo2").val("");
                                        }
                                    }

                                    function Eliminar_Objetivos(id) {
                                        $("#obj_" + id).html("");
                                    }
                                </script>





                                <div class="col-md-12" style="margin-top: 15px">
                                    <button type="submit" class="btn btn-success ">Guardar</button>
                                </div>

                            </div>
                        </div>
                    </form>

        </div>
    </div>

























   

    <div class="row justify-content-center">
        <div class="col-md-12" align="center">

            


        </div>
    </div>

    <div class="row">
        <div class="col-md-12" align="center">

            <div class="card" style="margin-bottom: 15px">

                <div class="card-body">


                    

                    <?php if ($_GET["id"] || $dtEmpleado["role"] == 1) { ?>
                        <div align="right" style="margin-top: 15px">
                            <button type="button" class="btn btn-danger btn-sm " onClick="EliminarOKRs()">Eliminar</button>
                        </div>
                    <?php } ?>

                </div>
            </div>

        </div>
    </div>
    
</div>

<?php if ($_GET["id"]) { ?>
    <form action="" method="post" id="eliminar_registro">
        <input type="hidden" name="delete_registro" value="true">
        <input type="hidden" name="id_registro" value="<?php echo $_GET["id"]; ?>">
    </form>
<?php } ?>


<script>
    var api = '<?php echo $url; ?>api/okrs/';

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

    $('#tipo_objetivo').change(function() {
        var valorTipo = $(this).val();
        if (valorTipo == '2') {
            $('#anioOkrSelect2').css('display', 'block');
            $('#vicepresidencia').css('display', 'none');
            $('#dependiente').css('display', 'none');
            $("#anio").val('');
            document.getElementById("anio2").required = true;
            document.getElementById("anio1").required = false;
            $('#estrategico1_pc').css('display', 'none');
            $('#estrategico2_pc').css('display', 'none');
            $('#estrategico_desc_pc').css('display', 'none');
            $('#anioOkrSelect1').css('display', 'none');
        } else if (valorTipo == '1') {
            $('#anioOkrSelect1').css('display', 'block');
            $('#dependiente').css('display', 'none');
            $("#anio").val('');
            document.getElementById("anio1").required = true;
            document.getElementById("anio2").required = false;
            $('#estrategico1_pc').css('display', 'none');
            $('#estrategico2_pc').css('display', 'none');
            $('#estrategico_desc_pc').css('display', 'none');
            $('#vicepresidencia').css('display', 'block');
            $('#anioOkrSelect2').css('display', 'none');
        } else {
            $('#anioOkrSelect1').css('display', 'none');
            $('#anioOkrSelect2').css('display', 'none');
            $('#dependiente').css('display', 'none');
            $('#estrategico1_pc').css('display', 'none');
            $('#estrategico2_pc').css('display', 'none');
            $('#estrategico_desc_pc').css('display', 'none');
            $('#vicepresidencia').css('display', 'none');
            $("#anio").val('');
            document.getElementById("anio1").required = false;
            document.getElementById("anio2").required = false;
        }

    });

    $(function() {
        $('#anio1').change(function() {
            var yearOkr = $(this).val();
            $("#anioOkr").val(yearOkr);
        }).change();
        $('#anio2').change(function() {
            var yearOkr = $(this).val();
            $("#anioOkr").val(yearOkr);
        }).change();
    });

    function select_vicepresidencia() {
        // $("#id_objetivo2").on('change', function() {			
        $("#anio2 option:selected").each(function() {
            var valorTipoObjetivo = $("#tipo_objetivo").val();
            var anioOkr = $("#anio2").val();
            var empresa = $("#idEmpresa").val();
            var vicepresidencia = $("#idVicepresidencia").val();
            var resultadoPadre = $("#resultado_padre").val();
            objetivo = $(this).val();
            $.post("views/select_vp_okr.php", {
                id_objetivo: objetivo,
                anioOkr: anioOkr,
                resultado_padre: resultadoPadre,
                vicepresidencia: vicepresidencia,
                id_empresa: empresa
            }, function(data) {
                if (objetivo != "") {
                    if (valorTipoObjetivo == '2') {
                        $('#vicepresidencia').css('display', 'block');
                        $("#id_vicepresidencia").html(data);
                        var foption = $('#id_vicepresidencia option:first');
                        var soptions = $('#id_vicepresidencia option:not(:first)').sort(function(a, b) {
                            return a.text == b.text ? 0 : a.text < b.text ? -1 : 1
                        });
                        $('#area').html(soptions).prepend(foption);
                        $("#id_vicepresidencia").html(data);
                    }
                } else {
                    $('#vicepresidencia').css('display', 'none');
                    $('#dependiente_pc').css('display', 'none');
                }

            });
        });
        // });
    }


    function select_organizacional() {
        // $("#id_objetivo2").on('change', function() {			
        $("#anio2 option:selected").each(function() {
            var valorTipoObjetivo = $("#tipo_objetivo").val();
            var anioOkr = $("#anio2").val();
            var empresa = $("#idEmpresa").val();
            var resultadoPadre = $("#resultado_padre").val();
            var objetivoEstrategico = $("#id_objetivo2").val();
            var vicepresidencia = $("#id_vicepresidencia").val();
            objetivo = $(this).val();
            console.log(valorTipoObjetivo);
            $.post("views/dependiente_select.php", {
                id_objetivo: objetivoEstrategico,
                anioOkr: anioOkr,
                resultado_padre: resultadoPadre,
                id_empresa: empresa,
                id_vicepresidencia: vicepresidencia
            }, function(data) {
                if (objetivoEstrategico != "") {
                    if (valorTipoObjetivo == '2') {
                        $('#dependiente_pc').css('display', 'block');
                        $("#id_resultado_padre").html(data);
                    }
                } else {
                    $('#dependiente_pc').css('display', 'none');
                }
            });
        });
        // });
    }

    function select_estrategico_vp() {
        // $("#anio").on('change', function() {
        var valorTipo = $("#tipo_objetivo").val();

        $("#id_vicepresidencia option:selected").each(function() {
            var anioOkr = $("#anio2").val();
            var empresa = $("#idEmpresa").val();
            var resultadoEstrategico = $("#resultado_estrategico").val();
            vicepresidencia = $(this).val();
            $.post("views/select_vp_estrategico.php", {
                anioOkr: anioOkr,
                resultado_estrategico: resultadoEstrategico,
                id_empresa: empresa,
                vicepresidencia: vicepresidencia,
            }, function(data) {
                if (vicepresidencia != "") {
                    if (valorTipo == '2') {
                        $('#estrategico2_pc').css('display', 'block');
                        $("#id_objetivo2").html(data);
                    }
                } else {
                    $('#estrategico2_pc').css('display', 'none');
                    $('#dependiente_pc').css('display', 'none');
                }
            });
        });
        // });
    }

    function select_estrategico() {
        // $("#anio").on('change', function() {
        var valorTipo = $("#tipo_objetivo").val();
        $("#anio1 option:selected").each(function() {
            var anioOkr = $("#anio1").val();
            var empresa = $("#idEmpresa").val();
            var resultadoEstrategico = $("#resultado_estrategico").val();
            anio = $(this).val();
            $.post("views/estrategico_select.php", {
                anioOkr: anioOkr,
                resultado_estrategico: resultadoEstrategico,
                id_empresa: empresa
            }, function(data) {
                if (valorTipo == '1') {
                    $('#estrategico1_pc').css('display', 'block');
                    $("#id_objetivo1").html(data);
                }
                if (valorTipo == '2') {
                    $("#id_objetivo2").html(data);
                }
                if (anio == "") {
                    $('#estrategico1_pc').css('display', 'none');
                    $('#estrategico2_pc').css('display', 'none');
                }
            });
        });
        // });
    }



    $('#anio').change(function() {
        var valorTipo = $(this).val();
        var valorObjetivo = $("#tipo_objetivo").val();
        if (valorTipo != '') {
            if (valorObjetivo == '1') {
                $('#estrategico1_pc').css('display', 'block');
                $('#estrategico_desc_pc').css('display', 'block');
            }
            if (valorObjetivo == '2') {
                $('#estrategico2_pc').css('display', 'block');
                $('#estrategico_desc_pc').css('display', 'block');
            }
        } else {
            $('#estrategico').css('display', 'none');
            $('#estrategico_desc_pc').css('display', 'none');
        }
    });
</script>