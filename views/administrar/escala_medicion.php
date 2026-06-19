<script>
    $(".menu_section").addClass("active");
    $("#nav_estrategia").addClass("active");
    jQuery("#menu_estrategia").css("display", "none");
    $("#bt_admin_escala").addClass("current-page");
</script>

<?php
//POR OKRS
include("views/okrs_equipos/functions.php");

?>


<style>
    .numeros_por {
        font-size: 30px;
        font-weight: bold;
        margin-top: 10px;
    }

    .parrafo_por {
        margin-bottom: 10px;
    }

    .card,
    .card-header,
    .card-footer {
        background-color: #FFFFFF !important;
        padding: 1.3rem !important;
    }
</style>

<?php include("views/okrs/layouts/modal_okr.php");

$hoy = date("Y-m-d H:i:s");

$respuesta = null;

$id_empresa = $_SESSION["id_empresa"];


if ($_POST["titulo_uno"] != "") {
    $titulo_uno = $_POST["titulo_uno"];
    $titulo_dos = $_POST["titulo_dos"];
    $titulo_tres = $_POST["titulo_tres"];
    $titulo_cuatro = $_POST["titulo_cuatro"];
    $titulo_cinco = $_POST["titulo_cinco"];
    $titulo_seis = $_POST["titulo_seis"];
    $titulo_siete = $_POST["titulo_siete"];
    $titulo_ocho = $_POST["titulo_ocho"];

    $subtitulo_uno = $_POST["subtitulo_uno"];
    $subtitulo_dos = $_POST["subtitulo_dos"];
    $subtitulo_tres = $_POST["subtitulo_tres"];
    $subtitulo_cuatro = $_POST["subtitulo_cuatro"];
    $subtitulo_cinco = $_POST["subtitulo_cinco"];
    $subtitulo_seis = $_POST["subtitulo_seis"];
    $subtitulo_siete = $_POST["subtitulo_siete"];
    $subtitulo_ocho = $_POST["subtitulo_ocho"];

    $porcentaje_uno = $_POST["porcentaje_uno"];
    $porcentaje_dos = $_POST["porcentaje_dos"];
    $porcentaje_tres = $_POST["porcentaje_tres"];
    $porcentaje_cuatro = $_POST["porcentaje_cuatro"];
    $porcentaje_cinco = $_POST["porcentaje_cinco"];
    $porcentaje_seis = $_POST["porcentaje_seis"];
    $porcentaje_siete = $_POST["porcentaje_siete"];
    $porcentaje_ocho = $_POST["porcentaje_ocho"];

    $querySelect = mysqli_query($connect_valentina, "SELECT * FROM Escala_Medicion WHERE id_empresa = " . $_SESSION["id_empresa"] . "");

    if ($querySelect->num_rows == 0) {

        $sentencia = "
                INSERT INTO Escala_Medicion (titulo_uno,titulo_dos,titulo_tres,titulo_cuatro,titulo_cinco,titulo_seis,subtitulo_uno,subtitulo_dos,subtitulo_tres,subtitulo_cuatro,subtitulo_cinco,subtitulo_seis,porcentaje_uno,porcentaje_dos,porcentaje_tres,porcentaje_cuatro,porcentaje_cinco,porcentaje_seis,porcentaje_siete,porcentaje_ocho,id_empresa,created_at) 
                VALUES ('$titulo_uno','$titulo_dos','$titulo_tres','$titulo_cuatro','$titulo_cinco','$titulo_seis','$subtitulo_uno','$subtitulo_dos','$subtitulo_tres','$subtitulo_cuatro','$subtitulo_cinco','$subtitulo_seis','$porcentaje_uno','$porcentaje_dos','$porcentaje_tres','$porcentaje_cuatro','$porcentaje_cinco','$porcentaje_seis','$porcentaje_siete','$porcentaje_ocho','$id_empresa','$hoy')";

        mysqli_query($connect_valentina, $sentencia);

        $accion = 'CREAR';
        $descripcion = 'Creación escala de medición ';
        
        $auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
	    VALUES (" . $_SESSION['id_empresa'] . ", " . $_SESSION['id_user'] . ",'$accion','$descripcion',0,0,0,0,'$hoy')";
        // echo $auditoria;
        mysqli_query($connect_okrs, $auditoria);

        $respuesta = '<div class="alert alert-success" role="alert">
                    Escala de medición guardada con exíto
                </div>
                ';
    } else {


        $sentencia = "
                UPDATE Escala_Medicion SET
                titulo_uno = '$titulo_uno',
                titulo_dos = '$titulo_dos',
                titulo_tres = '$titulo_tres',
                titulo_cuatro = '$titulo_cuatro',
                titulo_cinco = '$titulo_cinco',
                titulo_seis = '$titulo_seis',
                subtitulo_uno = '$subtitulo_uno',
                subtitulo_dos = '$subtitulo_dos',
                subtitulo_tres = '$subtitulo_tres',
                subtitulo_cuatro = '$subtitulo_cuatro',
                subtitulo_cinco = '$subtitulo_cinco',
                subtitulo_seis = '$subtitulo_seis',
                porcentaje_uno = '$porcentaje_uno',
                porcentaje_dos = '$porcentaje_dos',
                porcentaje_tres = '$porcentaje_tres',
                porcentaje_cuatro = '$porcentaje_cuatro',
                porcentaje_cinco = '$porcentaje_cinco',
                porcentaje_seis = '$porcentaje_seis',
                porcentaje_siete = '$porcentaje_siete',
                porcentaje_ocho = '$porcentaje_ocho',
                updated_at = '$hoy'
                WHERE id_empresa = $id_empresa";

        mysqli_query($connect_valentina, $sentencia);

        $accion = 'ACTUALIZAR';
        $descripcion = 'Actualización escala de medición ';
        
        $auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
	    VALUES (" . $_SESSION['id_empresa'] . ", " . $_SESSION['id_user'] . ",'$accion','$descripcion',0,0,0,0,'$hoy')";
        // echo $auditoria;
        mysqli_query($connect_okrs, $auditoria);

        $respuesta = '<div class="alert alert-success" role="alert">
                    Escala de medición actualizada con exíto
                </div>
                ';
    }
}

$queryEscala = mysqli_query($connect_valentina, "SELECT * FROM Escala_Medicion WHERE id_empresa = " . $_SESSION["id_empresa"] . "");
$dataEscala = mysqli_fetch_array($queryEscala);
$querySM23 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 2 AND id_submenu = 12");
$dataSM23 = mysqli_fetch_array($querySM23);
?>
<div class="row">
    <div class="col-md-12">
        <div class="card" style="padding: unset !important;">
            <div class="card-header" style="background-color: #FFFFFF !important;padding: .5rem 1rem !important;">
                <div class="row">
                    <div class="col-md-12" style="text-align: start !important;">
                        <h4><i class="fas fa-sitemap" id="iconCabecera"></i>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo $dataSM23["nombre"]; ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12" align="center">
            <div class="card" style="margin-bottom: 15px">
                <div class="card-header">
                    <?php if ($respuesta) {
                        echo $respuesta;
                    } ?>
                    <h2>Escala de Medición para el Progreso y Logro de OKRs</h2><br>
                    <p style="font-size: 14px;text-align: left;">A continuación presentamos una escala numérica de <b style="font-weight: bold;">6 niveles</b> para la medición del avance y/o cumplimiento de OKRs en la organización. Los primeros 4 niveles son sugeridos y pueden ser personalizados por la compañia.</p>
                    <p style="font-size: 14px;text-align: left;"><b style="font-weight: bold;">El 5to nivel</b> representa el cumplimiento esperado, alcanzando el 100%, mientras que el <b style="font-weight: bold;">último nivel (Súper Verde)</b> se agrega para destacar resultados excepcionales, como los OKRs tipo Moonshot o logros extraordinarios.</p>
                    <p style="font-size: 14px;text-align: left;">Esta escala optimizará el seguimiento de los OKRs y las visualizaciones en la plataforma, facilitando una evaluación detallada del progreso hacia los objetivos empresariales.</p>
                    <p style="font-size: 14px;text-align: left;">Cada nivel indica un grado incremental de logro, proporcionando una medida precisa del rendimiento en relación con los objetivos establecidos y los recursos asignados para su gestión.</p>
                </div>
                <div class="card-body">
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6" style="text-align: left;">
                                    ¿Desea configurar los valores por defecto para la escala de medición?
                                </div>
                                <div class="col-md-2">
                                    <select name="edicion_medicion" id="edicion_medicion" class="form-control" required>
                                        <option value="">Seleccione..</option>
                                        <option value="1">Sí</option>
                                        <option value="2">No</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table" width="100%" style="font-weight: bold; font-size: 15px;">
                                <tr style="border-style:none !important;font-weight: 700;border-color:transparent !important;">
                                    <td>
                                        <input type="text" name="titulo_uno" id="titulo_uno" placeholder="Avance Incipiente" class="form-control" value="<?php echo $dataEscala["titulo_uno"]; ?>" required>
                                    </td>
                                    <!-- <td>
                                    <input type="text" name="titulo_dos" id="titulo_dos" placeholder="Avance Mínimo" class="form-control" value="<?php //echo $dataEscala["titulo_dos"]; 
                                                                                                                                                    ?>" required>			
                                </td> -->
                                    <td>
                                        <input type="text" name="titulo_tres" id="titulo_tres" placeholder="Cumplimiento Parcial" class="form-control" value="<?php echo $dataEscala["titulo_tres"]; ?>" required>
                                    </td>
                                    <td>
                                        <input type="text" name="titulo_cuatro" id="titulo_cuatro" placeholder="Cumplimiento Sustancial" class="form-control" value="<?php echo $dataEscala["titulo_cuatro"]; ?>" required>
                                    </td>
                                    <td>
                                        <input type="text" name="titulo_cinco" id="titulo_cinco" placeholder="Cumplimiento Esperado" class="form-control" value="<?php echo $dataEscala["titulo_cinco"]; ?>" required>
                                    </td>
                                    <td>
                                        <input type="text" name="titulo_seis" id="titulo_seis" placeholder="Cumplimiento Excepcional" class="form-control" value="<?php echo $dataEscala["titulo_seis"]; ?>" required>
                                    </td>
                                </tr>
                                <tr style="font-size: 13px;">
                                    <td><textarea name="subtitulo_uno" id="subtitulo_uno" cols="30" rows="5" placeholder="Requiere plena atención para asegurar mejores resultados." class="form-control" required><?php echo $dataEscala["subtitulo_uno"]; ?></textarea></td>
                                    <!-- <td><textarea name="subtitulo_dos" id="subtitulo_dos" cols="30" rows="5" placeholder="Requiere atención especial para asegurar un avance con mejores resultados." class="form-control" required><?php //echo $dataEscala["subtitulo_dos"]; 
                                                                                                                                                                                                                                            ?></textarea></td> -->
                                    <td><textarea name="subtitulo_tres" id="subtitulo_tres" cols="30" rows="5" placeholder="Esfuerzo por conquistar las metas, pero no suficiente para un crecimiento escalado o competitivo." class="form-control" required><?php echo $dataEscala["subtitulo_tres"]; ?></textarea></td>
                                    <td><textarea name="subtitulo_cuatro" id="subtitulo_cuatro" cols="30" rows="5" placeholder="Mayor esfuerzo hacia el logro de metas." class="form-control" required><?php echo $dataEscala["subtitulo_cuatro"]; ?></textarea></td>
                                    <td><textarea name="subtitulo_cinco" id="subtitulo_cinco" cols="30" rows="5" placeholder="Logros alcanzado en el Plan Estratégico." class="form-control" required><?php echo $dataEscala["subtitulo_cinco"]; ?></textarea></td>
                                    <td><textarea name="subtitulo_seis" id="subtitulo_seis" cols="30" rows="5" placeholder="Reconocimiento y compensación extraordinaria." class="form-control" required><?php echo $dataEscala["subtitulo_seis"]; ?></textarea></td>
                                </tr>
                                <tr>
                                    <td width="15%" align="center" style="background-color: #FF0000;height: 30px;" bgcolor="#FF0000">
                                    </td>
                                    <!-- <td width="15%" align="center" style="background-color: #FF671D;height: 30px;" bgcolor="#FF671D">
                                </td> -->
                                    <td width="15%" align="center" style="background-color: #FFF200;height: 30px;" bgcolor="#FFF200">
                                    </td>
                                    <td width="15%" align="center" style="background-color: #95FA03;height: 30px;" bgcolor="#95FA03">
                                    </td>
                                    <td width="15%" align="center" style="background-color: #14F209;height: 30px;" bgcolor="#14F209">
                                    </td>
                                    <td width="15%" align="center" style="background-color: #00D30A;height: 30px;" bgcolor="#00D30A">
                                    </td>
                                </tr>
                                <tr style="border-style:none !important;border-color:transparent !important;">
                                    <td width="15%" align="center">
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" placeholder="0" aria-label="0" aria-describedby="basic-addon2" name="porcentaje_uno" id="porcentaje_uno" value="<?php echo $dataEscala["porcentaje_uno"]; ?>" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="basic-addon2">al</span>
                                            </div>
                                            <input type="text" class="form-control" placeholder="40" aria-label="94" aria-describedby="basic-addon2" name="porcentaje_dos" id="porcentaje_dos" value="<?php echo $dataEscala["porcentaje_dos"]; ?>" required>

                                        </div>
                                    </td>
                                    <td width="15%" align="center">
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" placeholder="41" aria-label="41" aria-describedby="basic-addon2" name="porcentaje_tres" id="porcentaje_tres" value="<?php echo $dataEscala["porcentaje_tres"]; ?>" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="basic-addon2">al</span>
                                            </div>
                                            <input type="text" class="form-control" placeholder="69" aria-label="69" aria-describedby="basic-addon2" name="porcentaje_cuatro" id="porcentaje_cuatro" value="<?php echo $dataEscala["porcentaje_cuatro"]; ?>" required>

                                        </div>
                                    </td>
                                    <td width="15%" align="center">
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" placeholder="70" aria-label="70" aria-describedby="basic-addon2" name="porcentaje_cinco" id="porcentaje_cinco" value="<?php echo $dataEscala["porcentaje_cinco"]; ?>" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="basic-addon2">al</span>
                                            </div>
                                            <input type="text" class="form-control" placeholder="94" aria-label="94" aria-describedby="basic-addon2" name="porcentaje_seis" id="porcentaje_seis" value="<?php echo $dataEscala["porcentaje_seis"]; ?>" required>

                                        </div>
                                    </td>
                                    <td width="15%" align="center">
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" placeholder="95" aria-label="95" aria-describedby="basic-addon2" name="porcentaje_siete" id="porcentaje_siete" value="<?php echo $dataEscala["porcentaje_siete"]; ?>" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="basic-addon2">al</span>
                                            </div>
                                            <input type="text" class="form-control" placeholder="99" aria-label="99" aria-describedby="basic-addon2" name="porcentaje_ocho" id="porcentaje_ocho" value="<?php echo $dataEscala["porcentaje_ocho"]; ?>" required>
                                        </div>
                                    </td>
                                    <!-- <td width="15%" align="center">100%</td> -->
                                    <td width="15%" align="center">> 100%</td>
                                </tr>
                            </table>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <?php if ($dtEmpleado["role"] == 1 || $_SESSION["id_colaborador_edit"] == $_SESSION['id_user_seleccion']) { ?>
                                    <div class="col-md-12" style="margin-bottom: 10px; margin-top: 15px">
                                        <button type="submit" id="sidebarCollapse" class="btn btn-success btn-block btn-sm">
                                            <i class="fas fa-check"></i> <?php echo $IDIOMA["estructura_guardar"]; ?>
                                        </button>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('#edicion_medicion').on('change', function() {
        var selectValor = $(this).val();
        var textos = document.querySelectorAll("input");
        var descripcion = document.querySelectorAll("textarea");

        if (selectValor == '1') {

            for (var i = 0; i < textos.length; i++) {
                textos[i].setAttribute('readonly', true);
            }

            for (var i = 0; i < descripcion.length; i++) {
                descripcion[i].setAttribute('readonly', true);
            }

            $('#titulo_uno').val('Avance Incipiente');
            // $('#titulo_dos').val('Avance Mínimo');
            $('#titulo_tres').val('Cumplimiento Parcial');
            $('#titulo_cuatro').val('Cumplimiento Sustancial');
            $('#titulo_cinco').val('Cumplimiento Esperado');
            $('#titulo_seis').val('Cumplimiento Excepcional');

            $('#subtitulo_uno').val('Alto Riesgo de ineficiencia');
            // $('#subtitulo_dos').val('Alto Riesgo de ineficiencias');
            $('#subtitulo_tres').val('Requiere atención y acompañamiento para el siguiente nivel');
            $('#subtitulo_cuatro').val('Se sugiere impulsar al equipo para asegurar mejores conquistas y cumplimientos sostenidos');
            $('#subtitulo_cinco').val('Evidencia aprendizaje, eficiencia y compromiso del equipo');
            $('#subtitulo_seis').val('Se deben propiciar espacios de aprendizaje de las buenas prácticas');

            $('#porcentaje_uno').val('0');
            $('#porcentaje_dos').val('50');
            $('#porcentaje_tres').val('51');
            $('#porcentaje_cuatro').val('80');
            $('#porcentaje_cinco').val('81');
            $('#porcentaje_seis').val('95');
            $('#porcentaje_siete').val('96');
            $('#porcentaje_ocho').val('100');

        } else {
            for (var i = 0; i < textos.length; i++) {
                textos[i].removeAttribute('readonly');
            }

            for (var i = 0; i < descripcion.length; i++) {
                descripcion[i].removeAttribute('readonly');
            }
        }
    });
</script>