<script>
    $(document).ready(function() {
        $("#bt_academia_cursos").addClass("active");
        $("#mod_academia").addClass("active_qa");
    });
</script>

<?php
$id = $_GET["id"];
$hoy = date("Y-m-d H:i:s");
if ($id != "") {
    $_SESSION['id_curso_edit'] = $id;
}
if ($_SESSION['id_curso_edit'] == "") {
    echo '<script>window.location = "' . $url_gestion . '?pg=goforexpert/admin/cursos";</script>'; //para evitar reinsersion
}

//INFORMACION DE LA BATERIA
$query = mysqli_query($connect_academia, "SELECT * FROM Cursos WHERE id = '" . $_SESSION['id_curso_edit'] . "' ");
$data = mysqli_fetch_array($query);

$queryPro = mysqli_query($connect_academia, "SELECT * FROM Programas WHERE id = '" . $data["id_programa"] . "'  ");
$dataPro = mysqli_fetch_array($queryPro);
?>


<style>
    .contenidos {
        display: none;
    }
</style>


<?php echo $respuesta; ?>

<div class="container-fluid">

    <nav aria-label="breadcrumb" style=" margin-top: 10px; margin-bottom: 10px">
        <ol class="breadcrumb" style="background-color: rgba(0,0,0,0); margin-bottom:0px">
            <li class="breadcrumb-item"><a href="?pg=home">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page"><a href="?pg=goforexpert/admin/cursos">Cursos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detalle</li>
        </ol>
    </nav>

    <style>
        label {
            color: #9d9b9b;
            margin-bottom: 0px;
        }
    </style>

    <!-- Resumen y módulo -->
    <div class="card" style="margin-bottom: 20px">

        <div class="card-body">

            <div class="row">

                <!-- nuevo modulo -->
                <div class="col-md-6">
                    <h4><?php echo $data["nombre"]; ?></h4>
                    <label>Programa</label>: <b><?php echo $dataPro["nombre"]; ?></b><br>
                    <label>Inicia</label>: <b><?php echo $data["fecha_inicia"]; ?></b>
                </div>



                <div class="col-md-6" align="right">

                    
                        <a href="<?php echo $url_admin; ?>?pg=academia/admin/curso/detalle_modulo">
                            <button type="button" class="btn btn-warning btn-sm" title="Editar Módulo">
                                Nuevo Módulo
                            </button>
                        </a>
                        <a href="<?php echo $url_admin; ?>?pg=academia/admin/curso/detalle_capitulo">
                            <button type="button" class="btn btn-danger btn-sm" title="Editar Módulo">
                                Nuevo Capítulo
                            </button>
                        </a>
                        <a href="<?php echo $url_admin; ?>?pg=academia/admin/curso/detalle_contenido">
                            <button type="button" class="btn btn-success btn-sm" title="Editar Módulo">
                                Nuevo Contenido
                            </button>
                        </a>
                        <a href="<?php echo $url_admin; ?>?pg=academia/admin/curso/detalle_evaluacion">
                            <button type="button" class="btn btn-info btn-sm" title="Editar Módulo">
                                Nueva Evaluación
                            </button>
                        </a>
                        
                    
                    


                </div>
            </div>



        </div>
    </div>

    <style>
        .tabla_indice td {
            padding: 3px;
        }
    </style>

    <div class="card">
        <div class="card-body">

            <h3>Indice</h3>


            <table class="tabla_indice">
                <?php
                $query = mysqli_query($connect_academia, "SELECT * FROM Cursos_Modulos WHERE id_curso = '" . $_SESSION['id_curso_edit'] . "' ORDER BY id ASC ");
                while ($data = mysqli_fetch_array($query)) {

                    $capitulos_fila = '';

                    $queryCaps = mysqli_query($connect_academia, "SELECT * FROM Cursos_Capitulos WHERE id_modulo = '" . $data["id"] . "' ORDER BY orden ASC ");
                    while ($dataCaps = mysqli_fetch_array($queryCaps)) {

                        $cotenido_list = '';
                        $queryContent = mysqli_query($connect_academia, "SELECT * FROM Cursos_Contenidos WHERE id_curso_capitulo = '" . $dataCaps["id"] . "' ORDER BY id ASC ");
                        while ($dataContent = mysqli_fetch_array($queryContent)) {

                            $vista = '';

                            if ($dataContent["tipo"] == 1 || $dataContent["tipo"] == 2 || $dataContent["tipo"] == 3 || $dataContent["tipo"] == 4 || $dataContent["tipo"] == 5 || $dataContent["tipo"] == 7 || $dataContent["tipo"] == 9 || $dataContent["tipo"] == 10 || $dataContent["tipo"] == 11 || $dataContent["tipo"] == 12) {
                                $vista = $url_admin . '?pg=academia/admin/curso/detalle_contenido&id=' . $dataContent["id"];
                            }

                            if ($dataContent["tipo"] == 6) {
                                $vista = $url_admin . '?pg=academia/admin/curso/detalle_evaluacion&id=' . $dataContent["id"];
                            }
                            
                            
                            

                            $cotenido_list .= '
                            <div>
							' . $dataContent["nombre"] . ' 
                            <a href="' . $vista . '">
                                <button type="button" class="btn btn-success btn-sm" title="Editar Contenido" style="margin-bottom: 3px;">
                                    <i class="bx bx-edit"></i> 
                                </button>
                            </a>
                            <button type="button" class="btn btn-danger btn-sm" title="Eliminar Contenido" style="margin-bottom: 3px;" onclick="EliminarContenido(' . $dataContent["id"] . ')">
                                    <i class="bx bx-trash"></i> 
                            </button>
                            </div>
                            
                            
                        ';
                        }

                        $capitulos_fila .= '
                        <tr>
                            <td></td>
                            <td valign="top">
                                ' . $dataCaps["nombre"] . '
                                <a href="' . $url_admin . '?pg=goforexpert/admin/curso/detalle_capitulo&id=' . $dataCaps["id"] . '">
                                    <button type="button" class="btn btn-success btn-sm" title="Editar Módulo">
                                        <i class="bx bx-edit"></i> 
                                    </button>
                                </a>
                                <button type="button" class="btn btn-danger btn-sm" title="Eliminar Contenido" onclick="EliminarCapitulos(' . $dataCaps["id"] . ')">
                                        <i class="bx bx-trash"></i> 
                                </button>
                                
                            </td>
                            <td valign="top">' . $cotenido_list . '</td>
                        </tr>
                        ';
                    }


                    echo '
                    <tr>
                        <td>

                            <b>' . $data["nombre"] . '</b>
                            <a href="' . $url_admin . '?pg=goforexpert/admin/curso/detalle_modulo&id=' . $data["id"] . '">
                                <button type="button" class="btn btn-success btn-sm" title="Editar Módulo">
                                    <i class="bx bx-edit"></i> 
                                </button>
                                
                            </a>
                            <button type="button" class="btn btn-danger btn-sm" title="Eliminar Contenido" onclick="EliminarModulo(' . $data["id"] . ')">
                                <i class="bx bx-trash"></i> 
                            </button>

                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    ';

                    echo $capitulos_fila;
                }



                ?>
            </table>









        </div>


    </div>

</div>








<script>
    $(document).ready(function() {
        $("#bt_sala").addClass("btn-primary");
    });

    var api = '<?php echo $api; ?>/academia/';
    var activar = false;

    function EliminarContenido(id_contenido) {
        if (activar == false) {
            $("#modal_body").html('Estas a punto de eliminar este contenido, esta acción es irreversible ¿Estás seguro?<br><br>');
            $("#modal_body").append('<button type="button" class="btn btn-danger" style="margin-right: 10px;" onclick="activar = true; EliminarContenido(' + id_contenido + ')">Eliminar</button>');
            $("#modal_general").modal('show');
        } else {

            jQuery.ajax({
                    url: api + "eliminar_contenido.php",
                    type: 'post',
                    data: {
                        id_contenido: id_contenido,
                        url: "?pg=academia/admin/curso/contenidos"
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


    function EliminarCapitulos(id_cap) {
        if (activar == false) {
            $("#modal_body").html('Estas a punto de eliminar este capítulo, esta acción es irreversible ¿Estás seguro?<br><br>');
            $("#modal_body").append('<button type="button" class="btn btn-danger" style="margin-right: 10px;" onclick="activar = true; EliminarCapitulos(' + id_cap + ')">Eliminar</button>');
            $("#modal_general").modal('show');
        } else {

            jQuery.ajax({
                    url: api + "eliminar_capitulos.php",
                    type: 'post',
                    data: {
                        id_cap: id_cap,
                        url: "?pg=academia/admin/curso/contenidos"
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

    function EliminarModulo(id_modulo) {
        if (activar == false) {
            $("#modal_body").html('Estas a punto de eliminar este módulo, esta acción es irreversible ¿Estás seguro?<br><br>');
            $("#modal_body").append('<button type="button" class="btn btn-danger" style="margin-right: 10px;" onclick="activar = true; EliminarModulo(' + id_modulo + ')">Eliminar</button>');
            $("#modal_general").modal('show');
        } else {

            jQuery.ajax({
                    url: api + "eliminar_modulo.php",
                    type: 'post',
                    data: {
                        id_modulo: id_modulo,
                        url: "?pg=academia/admin/curso/contenidos"
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





    function Ver_Tipo_Contenido(tipo) {

        $(".contenidos").hide();

        if (tipo == 1) {
            $("#video_youtube").show();
        }

        if (tipo == 2) {
            $("#video_mp4").show();
        }
    }

    <?php if ($id_contenido != "") { ?>
        $(document).ready(function() {
            Ver_Tipo_Contenido(<?php echo $dataContenido["tipo"]; ?>);
        });
    <?php } ?>
</script>