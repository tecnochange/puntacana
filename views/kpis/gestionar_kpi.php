<script>
    $(document).ready(function() {
        $('#menuKpis').collapse();
    });
</script>

<?php
$idKpi = $_GET["id"];
$hoy = date("Y-m-d H:i:s");

include("app/models/kpis/KpisServicios.php");
include("app/models/kpis/KpisCrud.php");

$ClassKpisServicios = new KpisServicios($user_log["id_empresa"]);
$ClassKpisCrud = new KpisCrud();

if (isset($_POST["guardar_avance_kpi"]) && !empty($_POST["guardar_avance_kpi"])) {
    $ClassKpisCrud->actualizar_avance_Kpis($_POST);
}

//$array_kpis_general = $ClassKpisServicios->kpis_colaborador($user_log["id_empresa"], NULL, NULL, $user_log["id"]);
$array_kpis_general = $ClassKpisServicios->kpis_empresa($user_log["id_empresa"], NULL, NULL, NULL);

$resultado = array_filter($array_kpis_general, function ($item) use ($idKpi) {
    return isset($item['id_kpi']) && (int)$item['id_kpi'] === (int)$idKpi;
});
$kpis = reset($resultado);

/* echo "<pre>";
print_r($kpis);
echo "</pre>"; */

include("componentes/modal_comentarios.php");
include("componentes/modal_documentos.php");

//GUARDAR COMENTARIO
if ($_POST["guardar_comentario_kpi"] != "") { 

    

    if( $_POST["id_registro_comentario"] ) {

        //echo "edicion ".$_POST["id_registro_comentario"];

        $sentencia_edit = "UPDATE Comentarios_Kpis SET  comentario = '" . $_POST["comentario"] . "', frecuencia = '" . $_POST["frecuencia"] . "' WHERE id = '".$_POST["id_registro_comentario"]."' ";
        echo $sentencia_co;
        mysqli_query($connect_kpis, $sentencia_edit);

        echo '<script> window.location.href = "?pg=kpis/gestionar_kpi&id=' . $idKpi . '&id_user=' . $user_log["id"] . '&role=' . $user_log["role"] . '&id_empresa='.$user_log["id_empresa"].' ";</script>';
    }   
    else{

        //echo "creación";
        echo "INSERT INTO Comentarios_Kpis (id_empresa, id_kpi, id_empleado, comentario, frecuencia, created_at) VALUES
        (" . $user_log["id_empresa"] . "," . $_POST["id_kpi"] . "," . $user_log["id"] . ",'" . $_POST["comentario"] . "','" . $_POST["frecuencia"] . "','$hoy')";

        $sentencia_co = "INSERT INTO Comentarios_Kpis (id_empresa, id_kpi, id_empleado, comentario, frecuencia, created_at) VALUES
        (" . $user_log["id_empresa"] . "," . $_POST["id_kpi"] . "," . $user_log["id"] . ",'" . $_POST["comentario"] . "','" . $_POST["frecuencia"] . "','$hoy')";
        mysqli_query($connect_kpis, $sentencia_co);

        echo '<script> window.location.href = "?pg=kpis/gestionar_kpi&id=' . $idKpi . '&id_user=' . $user_log["id"] . '&role=' . $user_log["role"] . '&id_empresa='.$user_log["id_empresa"].' ";</script>';
    }
}

//GUARDAR DOCUMENTO
if ($_POST["cargar_documento_kpi"]) {

    //dd($_POST);
    //dd($_FILES);
    $archivo = "";

    if ($_FILES["archivo_kpi"]["name"]) {

        function Subir_Documento($file)
        {

            $name = preg_replace('([^A-Za-z0-9.])', '', $file['name']);

            $sku = time();
            $dir_subida = '/var/www/html/goforagile.com/recursos/'; //carpeta de recursos
            $fichero_subido = $dir_subida . basename($sku . $name);

            if (move_uploaded_file($file['tmp_name'], $fichero_subido)) {
                //echo "El fichero es válido y se subió con éxito.\n";
            } else {
                //echo "¡Posible ataque de subida de ficheros!\n";
            }

            return $sku . $name;
        }
        $archivo = Subir_Documento($_FILES["archivo_kpi"]);
    }
    $sentencia_doc = "
		INSERT INTO Documentos_Kpis ( id_empresa ,  id_kpi ,  id_empleado,  archivo , comentario,  frecuencia, created_at ) 
		VALUES 
		( '" . $_POST["id_empresa"] . "', '" . $_POST["id_kpi"] . "', '" . $_POST["id_usuario"] . "', '" . $archivo . "', '" . $_POST["comentario"] . "','" . $_POST["frecuencia"] . "', '" . $hoy . "' )
		";

    mysqli_query($connect_kpis, $sentencia_doc);

    echo '<script> window.location.href = "?pg=kpis/gestionar_kpi&id=' . $idKpi . '&id_user=' . $user_log["id"] . '&role=' . $user_log["role"] . '&id_empresa=' . $user_log["id_empresa"] . '";</script>';
}

/* DATOS DEL KPI */
$queryKPI = mysqli_query($connect_kpis, "SELECT * FROM Kpis WHERE id = $idKpi");
$dataKPI = mysqli_fetch_array($queryKPI);

/* DATOS DEL EMPLEADO */
$queryEmpleado = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id = '".$user_log["id"]."'");
$dataEmpleado = mysqli_fetch_array($queryEmpleado);

/* RESPONSABLES */
$responsables = "";
$queryResponsableKpi = mysqli_query($connect_kpis, "SELECT * FROM Kpis_Colaborador WHERE id_kpi = $idKpi");
while ($dataResponsableKpi = mysqli_fetch_array($queryResponsableKpi)) {

    if ($dataResponsableKpi["id_colaborador"]) {
        $queryEmple = mysqli_query($connect_admin, "SELECT * FROM Empleados 
					WHERE id = '" . $dataResponsableKpi["id_colaborador"] . "' ");
        $dataEmple = mysqli_fetch_array($queryEmple);
        if ($dataEmple) {
            if (!$dataEmple["foto"]) {
                $dataEmple["foto"] = "img_default.jpg";
            }
            $responsables .= '<img loading="lazy" src="' . $recursos_publico . '/' .  $dataEmple["foto"] . '" class="foto_miniaturas" title="' . $dataEmple["nombre"] . '" style="width: 30px !important;height: 30px !important;" onclick="FichaEmpleado(' . $dataResponsableKpi["id_colaborador"] . ')">&nbsp;&nbsp;';
        }
    }
}
?>

<style>
    .card-body {
        flex: 1 1 auto !important;
        padding: 0.5rem 1rem 1rem !important;
    }

    .card,
    .card-header,
    .card-body,
    .card-footer {
        background-color: white !important;
    }
</style>
<style>
    /* Estilo del texto dentro del círculo */
    .easy-pie-chart {
        /* Centra el texto vertical y horizontalmente */
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 20px;
        font-weight: bold;
        color: #333;
        /* Asegúrate de que el contenedor del texto pueda tener margen */
        line-height: normal;
    }

    .okr-progress-value {
        position: absolute;
        margin: auto;
        font-size: 1.5em;
    }

    /* Estilo para la etiqueta de abajo */
    .progress-label {
        text-align: center;
        font-size: 16px;
        color: #333;
        margin-top: 10px;
    }
</style>

<!-- Summernote (compatible con Bootstrap 5) -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
<div class="container-fluid pb-4">

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row" style="align-items:center;">
                        <div class="col-md-9">
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <label for="">Responsables</label><br>
                                    <?php echo $responsables; ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <h5 style="color: black !important;"><?php echo $kpis["indicador"]; ?></h5>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-2">
                                    <label for="">Tipo de Kpi</label><br>
                                    <?php foreach ($Array_tipo_kpi_PC1 as $tipoKpi) {
                                        if ($kpis["tipo_kpi"] ==  $tipoKpi[0]) {
                                            echo $tipoKpi[1];
                                        }
                                    } ?>
                                </div>
                                <div class="col-md-2">
                                    <label for="">Frecuencia</label><br>
                                    <?= $kpis["txt_frecuencia"]; ?>
                                </div>
                                <div class="col-md-2">
                                    <label for="">Meta</label><br>
                                    <?php echo $kpis["meta"]; ?>
                                </div>
                                <div class="col-md-2">
                                    <label for="">Seguimiento</label><br>
                                    <?= number_format($kpis["avance_plano_kpis"], 2) ?>
                                </div>
                                <div class="col-md-2">
                                    <label for="">Tipo de Resultado: <span id="spanResultado" class="circleSpan" data-bs-toggle="tooltip" style="color:#365189 !important;font-size: 12px !important;" title="Ver información de los tipos de resultado"><i class="fa fa-question"></i></span></label><br>
                                    <?php
                                    foreach ($Array_Acumulativo_PC as $objetivo) {
                                        if ($objetivo[0] == $kpis["tipo_resultado"]) {
                                            echo $objetivo[1];
                                        }
                                    }
                                    ?>
                                </div>
                                <div class="col-md-2">
                                    <label for="">Tipo de Cálculo: &nbsp;<span id="spanCalculo" class="circleSpan" data-bs-toggle="tooltip" style="color:#365189 !important;font-size: 12px !important;" title="Ver información de los tipos de cálculo"><i class="fa fa-question"></i></span></label><br>
                                    <?php
                                    foreach ($Array_Tendencia_KPIS as $objetivo) {
                                        if ($objetivo[0] == $kpis["tipo_calculo"]) {
                                            echo $objetivo[1];
                                        }
                                    }
                                    ?>
                                </div>

                            </div>

                            <!-- TU ROL -->
                            <div class="col-md-12 mt-3">
                                <?php
                                $queryOE = mysqli_query($connect_kpis, "SELECT * FROM Kpis_Colaborador WHERE id_kpi = '" . $idKpi . "' AND id_empresa = " . $dataEmpleado["id_empresa"] . " AND id_colaborador = " . $dataEmpleado['id'] . "");
                                $dataOE = mysqli_fetch_array($queryOE);
                                $queryRolKPI = mysqli_query($connect_kpis, "SELECT * FROM Roles_Kpis WHERE id_empresa = " . $dataEmpleado["id_empresa"] . " AND id_rol = " . $dataOE["tipo"] . " AND estado = 1");
                                $dataRolKPI = mysqli_fetch_array($queryRolKPI);
                                if (mysqli_num_rows($queryOE)) { ?>
                                    <span class="label" onClick="VerRol(<?php echo $dataRolKPI["id"]; ?>)" data-bs-toggle="tooltip" style="color:black !important; font-size: 0.9rem !important;">
                                        <button class="btn btn-warning btn-sm">
                                            <i class="bx bx-user"></i>
                                        </button>&nbsp;<?php echo "<b>Tu Rol:</b> " . $dataRolKPI['nombre_rol']; ?>&nbsp;
                                    </span>
                                <?php }
                                ?>
                            </div>

                        </div>
                        <div class="col-md-3 d-flex align-items-center justify-content-center" style="text-align:center; text-align: -webkit-center;">
                            <div class="row circular-chart-component">
                                <style>
                                    .progreso-bar {
                                        width: 150px !important;
                                        height: 150px !important;
                                    }

                                    .objetivo-okr {
                                        font-size: 2rem !important;
                                    }
                                </style>
                                <div class="easy-pie-chart" data-percent="<?= $kpis["avance_kpis"]; ?>">
                                    <div class="okr-progress-value">
                                        <?= $kpis["avance_kpis"]; ?>%
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="card-footer">
                    <?php $queryAK = mysqli_query($connect_kpis, "SELECT * FROM Administradores_Kpi WHERE id_empleado = '".$user_log["id"]."' ");
                    $dataAK = mysqli_fetch_array($queryAK);
                    $queryEK = mysqli_query($connect_kpis, "SELECT * FROM Kpis_Colaborador WHERE id_colaborador = '".$user_log["id"]."' AND id_kpi = " . $idKpi . " AND tipo = 2");
                    $dataEK = mysqli_fetch_array($queryEK);
                    ?>
                    <form action="" id="form-frecuencia" method="post">
                        <input type="hidden" name="guardar_avance_kpi" value="true">
                        <input type="hidden" name="id_kpi" value="<?= $kpis["id_kpi"] ?>">
                        <input type="hidden" name="tipo__" value="<?= $kpis["tipo_kpi"] ?>">
                        <input type="hidden" name="tipo" value="<?= $kpis["frecuencia"] ?>">
                        <div class="row mb-6">
                            <?php
                            switch ($kpis["frecuencia"]) {
                                case 1:
                                    include("consolidados/tabla_mensual.php");
                                    break;
                                case 2:
                                    include("consolidados/tabla_bimestral.php");
                                    break;
                                case 3:
                                    include("consolidados/tabla_trimestral.php");
                                    break;
                                case 4:
                                    include("consolidados/tabla_semestral.php");
                                    break;
                                case 5:
                                    include("consolidados/tabla_anual.php");
                                    break;
                                case 6:
                                    include("consolidados/tabla_cuatrimestral.php");
                                    break;
                            }
                            ?>
                        </div>

                        <div class="row" style="text-align: end;">
                            <div class="col-md-12">
                                <!-- <?php if ($user_log["role"] == 1 || (mysqli_num_rows($queryAK) > 0) || (mysqli_num_rows($queryEK) > 0)): ?>
                                    <button class="btn btn-success" type="submit" style="display: none">Guardar avances</button>
                                <?php endif; ?> -->

                                <button class="btn btn-success btn-sm" type="submit">Guardar avances</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card pt-2">
                <div class="card-body">
                    <div class="accordion" id="accordionExample">

                        <!-- COMENTARIOS -->
                        <div class="accordion-item pt-2">
                            <h5 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree" id="boton_comentario_<?php echo $idKpi; ?>">
                                    <h5>COMENTARIOS</h5>
                                </button>
                            </h5>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <?php if ($VALIDAR_ROOT["crear"] || $dataRolKPI['nombre_rol'] == 'Lider KPI' || 1 == 1 ): ?>
                                        <div class="col-md-12 pb-2" style="text-align: right;">
                                            <button type="button" id="sidebarCollapse" class="btn btn-primary btn-md" style="border-radius: 30px" title="Agregar Comentario" onClick="CrearComentario(<?php echo $idKpi . "," . $dataEmpleado["id_empresa"] . "," . $dataEmpleado["id"] . "," . $dataEmpleado["area"] . "," . $kpis["frecuencia"]; ?>);">
                                                <i class="bi bi-plus"></i> Agregar un Comentario
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                    <?php include("componentes/comentarios.php"); ?>
                                </div>
                            </div>
                        </div>

                        <!-- DOCUMENTOS -->
                        <div class="accordion-item pt-2">
                            <h5 class="accordion-header" id="headingFour">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour" id="boton_documento_<?php echo $idKpi; ?>">
                                    <h5>DOCUMENTOS</h5>
                                </button>
                            </h5>
                            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <?php
                                    include("permisos_rol.php");
                                    if ( $VALIDAR_ROOT["crear"] || $dataRolKPI['nombre_rol'] == 'Lider KPI' ) { ?>
                                        <div class="row">
                                            <div class="col-md-12" style="text-align: right;">
                                                <button type="button" id="sidebarCollapse" class="btn btn-primary btn-md" style="border-radius: 30px" title="Agregar Documento" onClick="CargarDocumento(<?php echo $idKpi . "," . $dataEmpleado["id_empresa"] . "," . $dataEmpleado["id"] . "," . $dataEmpleado["area"] . "," . $kpis["frecuencia"]; ?>);">
                                                    <i class="bi bi-plus"></i> Agregar un Documento
                                                </button>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php include("componentes/documentos.php"); ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
/* MODAL PARA ACCION CORRECTIVA */
strpos($_SERVER['REQUEST_URI'], 'gestionar_kpi') !== false
    && include "views/kpis/componentes/modal_accion_correctiva.php";
?>

<script src="https://cdn.jsdelivr.net/npm/easy-pie-chart@2.1.7/dist/jquery.easypiechart.min.js"></script>
<script>
    $(document).ready(function() {
        $('.easy-pie-chart').easyPieChart({
            // Opciones principales
            size: 120, // Diámetro del círculo
            lineWidth: 15, // Grosor del anillo
            barColor: '<?= $kpis["color_avance_kpis"]; ?>', // Color de la parte llena (verde)
            trackColor: '#E6E6E6', // Color de la parte vacía (gris claro)
            scaleColor: false, // Oculta las marcas de escala
            lineCap: 'butt', // Estilo del final del progreso ('round' para redondeado)
            animate: 1000, // Duración de la animación en ms al cargar

            // Función para actualizar el texto si el valor es dinámico
            onStep: function(from, to, percent) {
                // Este código mantiene el número centrado y lo actualiza durante la animación
                $(this.el).find('.okr-progress-value').text(Math.round(percent) + '%');
            }
        });
    });
</script>

<script>
    var api_kpi = '<?php echo $url; ?>api/kpis/';

    function CrearComentario(id, id_empresa, id_empleado, id_area, id_tipo) {
        jQuery.ajax({
                url: api_kpi + "crear_comentario.php",
                type: 'post',
                data: {
                    id: id,
                    id_empresa: id_empresa,
                    id_empleado: id_empleado,
                    id_area: id_area,
                    id_tipo: id_tipo
                },
            }).done(function(resp) {
                $("#modal_comentarios").modal("show");
                $("#modal_contenido_c").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});
    }

    function EditarComentario(id, id_empresa, id_empleado, id_area, id_tipo) {
         jQuery.ajax({
                url: api_kpi + "editar_comentario.php",
                type: 'post',
                data: {
                    id: id,
                    id_empresa: id_empresa,
                    id_empleado: id_empleado,
                    id_area: id_area,
                    id_tipo: id_tipo
                },
            }).done(function(resp) {
                $("#modal_editar_comentarios").modal("show");
                $("#modal_contenido_c_e").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {}); 
    }


    function EliminarComentario(btn, id, id_empresa, id_empleado) {

        if (!confirm("Esta acción es irreversible. ¿Desea eliminar el comentario?")) {
            return;
        }
        
        let fila = $(btn).closest("tr");
        $.ajax({
                url: api_kpi + "eliminar_comentario.php",
                type: "POST",
                data: {
                    id: id,
                    id_empresa: id_empresa,
                    id_empleado: id_empleado
                }
            })
            .done(function(resp) {
                location.reload();
                /* fila.fadeOut(300, function() {
                    $(this).remove();
                }); */
            })
            .fail(function(resp) {
                console.log(resp);
            });
    }

    function CargarDocumento(id, id_empresa, id_empleado, id_area, id_tipo) {
        jQuery.ajax({
                url: api_kpi + "cargar_documento.php",
                type: 'post',
                data: {
                    id: id,
                    id_empresa: id_empresa,
                    id_empleado: id_empleado,
                    id_area: id_area,
                    id_tipo: id_tipo
                },
            }).done(function(resp) {
                $("#modal_documentos").modal("show");
                $("#modal_contenido_d").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});
    }

    function EliminarDocumento(btn, id, id_empresa, id_empleado) {
        if (!confirm("Esta acción es irreversible. ¿Desea eliminar el documento?")) {
            return;
        }
        
        let fila = $(btn).closest("tr");

        $.ajax({
                url: api_kpi + "eliminar_documento.php",
                type: "POST",
                data: {
                    id: id,
                    id_empresa: id_empresa,
                    id_empleado: id_empleado
                }
            })
            .done(function(resp) {
                location.reload();
                /* fila.fadeOut(300, function() {
                    $(this).remove();
                }); */
            })
            .fail(function(resp) {
                console.log(resp);
            });
    }
</script>

<script>
    var api = '<?php echo $url; ?>api/kpis/';
    function FichaEmpleado(id_empleado) {

        var id_empresa = <?php echo $user_log["id_empresa"]; ?>

        $("#modal_empleado").modal("show");
        $("#body_empleado").html("Cargando...");

        jQuery.ajax({
                url: api + "ficha_empleado.php",
                type: 'post',
                data: {
                    id_empresa: id_empresa,
                    id_empleado: id_empleado,
                },
            })
            .done(function(resp) {
                $("#body_empleado").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});
    }
</script>