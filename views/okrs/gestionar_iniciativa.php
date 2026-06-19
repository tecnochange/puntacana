<script>
    $(document).ready(function() {
        $('#menuOkrs').collapse();
        $('#bt_okrs_reportes').addClass('active');
    });
</script>

<?php
// Mostrar errores en pantalla
/* ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); */
?>

<?php
$id_okrs = $_GET['id_okrs'];
$id_resultado = $_GET['id_resultado'];
$id_iniciativa = $_GET['id_iniciativa'];
$tab = $_GET['tab'] ?? '';
$hoy = date("Y-m-d H:i:s");

include("app/models/okrs/OkrsServicios.php");
$ClassOkrsServicios  = new OkrsServicios();
$iniciativas = $ClassOkrsServicios->okrs_iniciativas_resultados($id_okrs, $id_resultado);

//GUARDAR COMENTARIO
if ($_POST["guardar_comentario_iniciativa"] != "") {
    $sentencia_co = "INSERT INTO Okrs_Comentarios_Iniciativas (id_empresa, id_okrs, id_iniciativa, id_resultado, id_empleado, comentario, created_at) VALUES
    ('".$user_log["id_empresa"]."','".$id_okrs."','".$id_iniciativa."','".$id_resultado."','".$user_log["id"]."','" . $_POST["comentario"] . "','$hoy')";
    mysqli_query($connect_okrs, $sentencia_co);

    echo '<script> window.location.href = "?pg=okrs/gestionar_iniciativa&id_okrs='.$id_okrs.'&id_resultado='.$id_resultado.'&id_iniciativa='.$id_iniciativa.'";</script>';
}

//GUARDAR DOCUMENTO
if ($_POST["cargar_documento_iniciativa"]) {

    /* dd($_POST);
    dd($_FILES); */
    $archivo = "";

    if ($_FILES["archivo_iniciativa"]["name"]) {

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
        $archivo = Subir_Documento($_FILES["archivo_iniciativa"]);
    }
    $sentencia_doc = "
		INSERT INTO Okrs_Documentos ( id_empresa ,  id_okrs ,  id_resultado ,  id_iniciativa, id_empleado,  comentario, archivo, created_at ) 
		VALUES 
		( '" . $user_log["id_empresa"] . "', '" . $id_okrs . "', '" . $id_resultado . "', '" . $id_iniciativa . "', '" . $user_log["id"] . "', '" . $_POST["comentario"] . "', '".$archivo."', '" . $hoy . "' )
		";

    mysqli_query($connect_okrs, $sentencia_doc);

    echo '<script> window.location.href = "?pg=okrs/gestionar_iniciativa&id_okrs=' . $id_okrs . '&id_resultado=' . $id_resultado . '&id_iniciativa=' . $id_iniciativa . ' ";</script>';
}


//INICIATIVA
$iniciativa = [];
foreach ($iniciativas as $nodo) {
    if ($nodo["id"] == $id_iniciativa) {
        $iniciativa = $nodo;
    }
}

//PLANES DE ACCION
$planes_accion = $ClassOkrsServicios->okrs_obtener_plan_accion_iniciativa($iniciativa["id"]);

/* RESPONSABLES */
$responsables = "";

include("componentes/modal_comentarios_iniciativa.php");
include("componentes/modal_documentos_iniciativa.php");
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
                                    <?php
                                    $resp_iniciativa = explode(',', $iniciativa["responsables"]);
                                    $totalRespIni = count($resp_iniciativa);

                                    for ($i = 0; $i < $totalRespIni; $i++):
                                        $respDataIni = $ClassOkrsServicios->Empleado($resp_iniciativa[$i]);

                                        if (!$respDataIni["foto"]) {
                                            $respDataIni["foto"] = "img_default.jpg";
                                        }
                                        $responsables .= '<img loading="lazy" src="' . $recursos_publico . '/' .  $respDataIni["foto"] . '" class="foto_miniaturas" title="' . $respDataIni["nombre"] . '" style="width: 50px !important;height: 50px !important;" onclick="FichaEmpleado(' . $respDataIni["id"] . ')">&nbsp;&nbsp;';
                                    endfor;
                                    ?>
                                    <?php echo $responsables; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label for="">Meta</label><br>
                                    <?php echo $iniciativa["meta"]; ?>
                                </div>
                                <div class="col-md-2">
                                    <label for="">Seguimiento</label><br>
                                    <?php echo $iniciativa["avance"]; ?>
                                </div>
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
                                <div class="easy-pie-chart" data-percent="<?= $iniciativa["porcentaje_avance"]; ?>">
                                    <div class="okr-progress-value">
                                        <?= $iniciativa["porcentaje_avance"]; ?>%
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card pt-2">
                <div class="card-body">
                    <div class="accordion" id="accordionExample">

                        <!-- PLANES DE ACCIÓN -->
                        <div class="accordion-item pt-2">
                            <h5 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo" id="boton_planes_accion<?php echo $id_okrs; ?>">
                                    <h5>PLANES DE ACCIÓN</h5>
                                </button>
                            </h5>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <?php include("componentes/planes_accion.php"); ?>
                                </div>
                            </div>
                        </div>

                        <!-- COMENTARIOS -->
                        <div class="accordion-item pt-2">
                            <h5 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree" id="boton_comentario_<?php echo $id_okrs; ?>">
                                    <h5>COMENTARIOS</h5>
                                </button>
                            </h5>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <div class="col-md-12 pb-2" style="text-align: right;">
                                        <button type="button" id="sidebarCollapse" class="btn btn-primary btn-md" style="border-radius: 30px" title="Agregar Comentario" onClick="CrearComentario(<?= $id_okrs; ?>, <?= $id_iniciativa; ?>, <?= $id_resultado; ?>, <?= $user_log["id_empresa"]; ?>, <?= $user_log["id"]; ?>, '<?= $iniciativa["descripcion"]; ?>')">
                                            <i class="bi bi-plus"></i> Agregar un Comentario
                                        </button>
                                    </div>
                                    <?php include("componentes/comentarios_iniciativa.php"); 
                                    ?>
                                </div>
                            </div>
                        </div>

                        <!-- DOCUMENTOS -->
                        <div class="accordion-item pt-2">
                            <h5 class="accordion-header" id="headingFour">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour" id="boton_documento_<?php echo $id_okrs; ?>">
                                    <h5>DOCUMENTOS</h5>
                                </button>
                            </h5>
                            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <div class="row">
                                        <div class="col-md-12" style="text-align: right;">
                                            <button type="button" id="sidebarCollapse" class="btn btn-primary btn-md" style="border-radius: 30px" title="Agregar Documento"
                                                onClick="CargarDocumento('<?= $user_log['id_empresa']; ?>', '<?= $id_okrs; ?>', '<?= $id_resultado; ?>', '<?= $id_iniciativa; ?>', '<?= $user_log['id']; ?>')"
                                            >
                                                <i class="bi bi-plus"></i> Agregar un Documento
                                            </button>
                                        </div>
                                    </div>
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

<script src="https://cdn.jsdelivr.net/npm/easy-pie-chart@2.1.7/dist/jquery.easypiechart.min.js"></script>
<script>
    $(document).ready(function() {
        $('.easy-pie-chart').easyPieChart({
            // Opciones principales
            size: 120, // Diámetro del círculo
            lineWidth: 15, // Grosor del anillo
            barColor: '<?= $iniciativa["color"]; ?>', // Color de la parte llena (verde)
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
    var api_okrs = '<?php echo $url; ?>api/okrs/';

    function CrearComentario(id_okrs, id_iniciativa, id_resultado, id_empresa, id_empleado, descripcion) {
        jQuery.ajax({
                url: api_okrs + "crear_comentario_iniciativa.php",
                type: 'post',
                data: {
                    id_okrs: id_okrs,
                    id_iniciativa: id_iniciativa,
                    id_resultado: id_resultado,
                    id_empresa: id_empresa,
                    id_empleado: id_empleado,
                    descripcion: descripcion
                },
            }).done(function(resp) {
                $("#modal_comentarios_iniciativa").modal("show");
                $("#modal_contenido_comentario_iniciativa").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});
    }

    function EditarComentario(id, id_empresa, id_empleado, id_area, id_tipo) {
        /* jQuery.ajax({
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
            .always(function(resp) {}); */
    }


    function EliminarComentario(btn, id, id_empresa, id_empleado) {

        /* if (!confirm("Esta acción es irreversible. ¿Desea eliminar el comentario?")) {
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
                fila.fadeOut(300, function() {
                    $(this).remove();
                });
            })
            .fail(function(resp) {
                console.log(resp);
            }); */
    }

    function CargarDocumento(id_empresa, id_okrs, id_resultado, id_iniciativa, id_empleado) {
        //console.log(id_empresa, id_okrs, id_resultado, id_iniciativa, id_empleado);
        
        jQuery.ajax({
                url: api_okrs + "subir_documento.php",
                type: 'post',
                data: {
                    id_empresa: id_empresa,
                    id_okrs: id_okrs,
                    id_resultado: id_resultado,
                    id_iniciativa: id_iniciativa,
                    id_empleado: id_empleado
                },
            }).done(function(resp) {
                $("#modal_documentos_iniciativa").modal("show");
                $("#modal_contenido_iniciativa").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});
    }

    /* function EliminarDocumento(btn, id, id_empresa, id_empleado) {
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
                fila.fadeOut(300, function() {
                    $(this).remove();
                });
            })
            .fail(function(resp) {
                console.log(resp);
            });
    } */
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

<?php include("app/models/okrs/OkrsScripts.php"); ?>
<script>
    // Instancia global accesible por los inputs
    const okrsClass = new OkrsScripts();
</script>

<script>
    const cambiarPrioridad = (id) => {

        const select = $("#select_prioridad_" + id);
        const container = select.closest("div");
        const texto = container.find(".prioridad-texto");

        // Mostrar select
        select.removeClass("d-none");

        // Evitar múltiples eventos
        select.off("change");

        select.on("change", function() {
            const prioridad = $(this).val();

            const prioridades = {
                1: '<span class="btn btn-sm btn-info bg-white">Bajo</span>',
                2: '<span class="btn btn-sm btn-success bg-success">Medio</span>',
                3: '<span class="btn btn-sm btn-warning">Alto</span>',
                4: '<span class="btn btn-sm btn-danger">Urgente</span>'
            };

            // 👇 AQUÍ EL CAMBIO
            texto.html(prioridades[prioridad]);

            $(this).addClass("d-none");

            editarPrioridad(id, prioridad);
        });
    };
    const editarPrioridad = (id, prioridad) => {
        $.ajax({
            url: "api/okrs/editar_prioridad.php",
            method: "POST",
            data: {
                id: id,
                prioridad: prioridad
            },
            success: function(res) {
                //window.location.reload();
                //console.log(res);
            },
            error: function() {
                alert("Error al actualizar prioridad");
            }
        });
    };

    const cambiarBacklog = (id) => {

        const select = $("#select_backlog_" + id);
        const container = select.closest("div");
        const texto = container.find(".backlog-texto");

        // Mostrar select
        select.removeClass("d-none");

        // Evitar múltiples eventos
        select.off("change");

        select.on("change", function() {
            const backlog = $(this).val();

            const backlogs = {
                1: '<span class="btn btn-sm btn-info bg-white">Planificado</span>',
                2: '<span class="btn btn-sm btn-success bg-success">En Progreso</span>',
                3: '<span class="btn btn-sm btn-warning">En Revisión</span>',
                4: '<span class="btn btn-sm btn-success bg-success">Completado</span>'
            };

            // 👇 AQUÍ EL CAMBIO
            texto.html(backlogs[backlog]);

            $(this).addClass("d-none");

            editarBacklog(id, backlog);
        });
    };
    const editarBacklog = (id, backlog) => {
        const select = $("#select_backlog_" + id);
        const container = select.closest("div");
        const texto = container.find(".backlog-texto");
        const backlogs = {
            1: '<span class="btn btn-sm btn-info bg-white">Planificado</span>',
            2: '<span class="btn btn-sm btn-success">En Progreso</span>',
            3: '<span class="btn btn-sm btn-warning">En Revisión</span>',
            4: '<span class="btn btn-sm btn-success bg-success">Completado</span>'
        };
        texto.html(backlogs[backlog]);

        jQuery.ajax({
            url: "api/okrs/editar_estado_backlog.php",
            type: 'post',
            data: {
                id_plan_accion: id,
                estado: backlog
            },
            success: function(res) {
                //window.location.reload();
            }
        });
    };
</script>
<script>
    $(document).ready(function() {
        const tab = "<?= $tab ?>";

        if (tab === "comentarios") {
            const collapse = new bootstrap.Collapse(document.getElementById('collapseThree'), {
                toggle: true
            });
        }
        if (tab === "documentos") {
            const collapse = new bootstrap.Collapse(document.getElementById('collapseFour'), {
                toggle: true
            });
        }
    });
</script>