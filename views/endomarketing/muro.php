<script>
$(document).ready(function() {
    $('#menuEndomarketing').collapse();
    $('#bt_endomarketing_muro').addClass('active');
});
</script>

<style>
    .visible {
        display: block;
    }

    .parrafo_card a {
        color: #003CFF;
    }

    .miniatura_home {
        width: 60px;
        height: 60px;
        background-size: cover;
        border-radius: 100px;
        border: 2px solid #5c349e;
    }
</style>



<?php
include("views/clima/wandbook/layouts/modal_borrar.php");

$tipo = $_GET["tp"];
$hoy = date("Y-m-d H:i:s");
$ahora = date("Y-m-d");
$all = $_GET["all"];

$limit = 20;
if ($all) {
    $limit = 100;
}

//PARA GUARDAR LOS COMENTARIOS
if ($_POST["id_comentar"] != "" && $_POST["comentario"] != "") {
    mysqli_query($connect_clima, "INSERT INTO Comentarios (id_publicacion, id_user, comentario, nombre_full, created_at) VALUES 
		( '" . $_POST["id_comentar"] . "', '" . $_SESSION['id_user'] . "', '" . utf8_encode($_POST["comentario"]) . "', '" . $_SESSION['nombre_valentina'] . "', '" . $hoy . "' ) ");

    echo '<script> //location.href ="?pg=home";</script>';
}

//PARA MOSTRAR LOS TIPO
if ($_GET["tp"] != "") {
    $filtro = ' AND tipo = ' . $tipo . ' ';
}

$queryEmploy = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id = '" . $_SESSION['id_user'] . "' ");
$dataEmploy = mysqli_fetch_array($queryEmploy);

//CONSULTAMOS LAS PUBLICACIONES

$queryVarEmpresa = mysqli_query($connect_clima, "SELECT * FROM Encuestas WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' ");
$dataVarEmpresa = mysqli_fetch_array($queryVarEmpresa);
$varaibles = explode(",", $dataVarEmpresa["variables"]);
?>

<?php
function replaceLinks($s)
{
    return preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.%-=#]*(\?\S+)?)?)?)@', '<a href="$1">$1</a>', $s);
}


function ConvertirLink($entrada)
{
    $url = '@(http(s)?)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';
    $string = preg_replace($url, '<a href="http$2://$4" target="_blank" title="$0">$0</a>', $entrada);
    return $string;
}

$querySM51 = mysqli_query($connect_admin, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 5 AND id_submenu = 31");
$dataSM51 = mysqli_fetch_array($querySM51);
?>


<style>
    .item_filtro {
        width: 24.1%;
        display: inline-table;
        border: 1px solid #e3e1e1;
        margin-bottom: 3px;
        padding: 15px 5px;
        border-radius: 5px;
    }

    .sub_cat_iconos {
        display: block;
    }
    .iconos_dash{
        font-size: 40px;
        margin-bottom: 10px;
        margin-top: 10px;
    }

    @media (max-width: 768px) {
        .sub_cat_iconos {
            display: none;
        }
    }
</style>



<div class="container" style="max-width: 700px">
    <div class="card mb-3">
        <div class="card-header">
            <h3>Muro de Endomarketing</h3>
        </div>
    </div>

    <!-- FILTROS -->
    <div class="row justify-content-md-center mb-3">

        <div class="col-md-4" style="margin-bottom: 20px">
            <div class="card">
                <div class="card-body">
                    <a href="<?php echo $url; ?>?pg=endomarketing/muro&tp=3">
                        <div align="center">
                            <i class="bx bx-medal iconos_dash" ></i><br>
                            <div class="sub_cat_iconos">Reconocimientos</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4" style="margin-bottom: 20px">
            <div class="card">
                <div class="card-body">
                    <a href="<?php echo $url; ?>?pg=endomarketing/muro&tp=5">
                        <div align="center">
                            <i class="bx bx-news iconos_dash" ></i><br>
                            <div class="sub_cat_iconos">Noticias</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4" style="margin-bottom: 20px">
            <div class="card">
                <div class="card-body">
                    <a href="<?php echo $url; ?>?pg=endomarketing/muro">
                        <div align="center">
                            <i class="bx bx-grid-small iconos_dash" ></i><br>
                            <div class="sub_cat_iconos">Todos</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

    </div>

    <!-- PUBLICACIONES -->
    <?php
        $tab_index = 1;
        //CONSULTAMOS LOS DATOS DE LA ASIGNACION
        $query = mysqli_query($connect_clima, "SELECT * FROM Publicaciones WHERE id_empresa = '" . $user_log["id_empresa"] . "' AND estado = 1 AND fecha_publicacion <= '" . $ahora . "' " . $filtro . " ORDER BY id DESC LIMIT 20 ");
        while ($data = mysqli_fetch_array($query)) {

            $listMegusta = '';
            //CONSULTAMOS EL EMPLEADO
            $queryUser = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id = '" . $data["id_user"] . "' ");
            $dataUser = mysqli_fetch_array($queryUser);
            //CONSULTAMOS LOS COMENTARIOS
            $queryComentarios = mysqli_query($connect_clima, "SELECT * FROM Comentarios WHERE id_publicacion = '" . $data["id"] . "' ");
            //CONSULTAMOS LOS MEGUSTA
            $queryMegusta = mysqli_query($connect_clima, "SELECT * FROM Me_Gusta WHERE id_publicacion = '" . $data["id"] . "' ");
            $queryMegustaList = mysqli_query($connect_clima, "SELECT * FROM Me_Gusta WHERE id_publicacion = '" . $data["id"] . "' ORDER BY id DESC LIMIT 10 ");
            while ($dataMegustaList = mysqli_fetch_array($queryMegustaList)) {
                $listMegusta .= $dataMegustaList["nombre_full"] . ' <br> ';
            }

            $queryAcepto = mysqli_query($connect_clima, "SELECT * FROM Aceptados WHERE id_publicacion = '" . $data["id"] . "' ");

            $queryVar = mysqli_query($connect_clima, "SELECT nombre FROM Variables WHERE id = '" . $data["variable"] . "' ");
            $dataVar = mysqli_fetch_array($queryVar);

            //RETO
            if ($data["tipo"] == 1) {
                if ($data["visibilidad"] == 1) {
                    include("views/home/layouts/reto_ficha.php");
                }
                if ($data["visibilidad"] == 2) {
                    if ($data["id_proceso"] == $user_log['id_departamento']) {
                        include("views/home/layouts/layouts/reto_ficha.php");
                    }
                }
            }
            //MOTIVADOR 
            if ($data["tipo"] == 2) {
                if ($data["visibilidad"] == 1) {
                    include("views/home/layouts/motivador_ficha.php");
                }
                if ($data["visibilidad"] == 2) {
                    if ($data["id_proceso"] == $user_log['id_departamento']) {
                        include("views/home/layouts/motivador_ficha.php");
                    }
                }
            }
            //RECONOCIMIENTO
            if ($data["tipo"] == 3) {
                if ($data["visibilidad"] == 1) {
                    include("views/endomarketing/layouts/reconocimiento_ficha.php");
                }
                if ($data["visibilidad"] == 2) {
                    if ($data["id_proceso"] == $user_log['id_departamento']) {
                        include("views/endomarketing/layouts/reconocimiento_ficha.php");
                    }
                }
                if ($data["visibilidad"] == 3) {
                    if ($data["titulo"] == $_SESSION['id_user']) {
                        include("views/endomarketing/layouts/reconocimiento_ficha.php");
                    }
                }
            }

            //GENERALES
            if ($data["tipo"] == 4) {
                if ($data["visibilidad"] == 1) {
                    include("views/home/layouts/clasificado_ficha.php");
                }
                if ($data["visibilidad"] == 2) {
                    if ($data["id_proceso"] == $user_log['id_departamento']) {
                        include("views/home/layouts/clasificado_ficha.php");
                    }
                }
                if ($data["visibilidad"] == 3) {
                    if ($data["titulo"] == $_SESSION['id_user']) {
                        include("views/home/layouts/clasificado_ficha.php");
                    }
                }
            }


            //GENERALES
            if ($data["tipo"] >= 5) {
                if ($data["visibilidad"] == 1) {
                    include("views/endomarketing/layouts/generales_ficha.php");
                }
                if ($data["visibilidad"] == 2) {
                    if ($data["id_proceso"] == $user_log['id_departamento']) {
                        include("views/endomarketing/layouts/generales_ficha.php");
                    }
                }
                if ($data["visibilidad"] == 3) {
                    if ($data["titulo"] == $_SESSION['id_user']) {
                        include("views/endomarketing/layouts/generales_ficha.php");
                    }
                }
            }

            
        }
    ?>

</div>























<style>
    .botones_img {
        box-shadow: 1px 1px 12px rgba(0, 0, 0, 0.2);
        transition: 0.5s all;
        cursor: pointer;
    }

    .botones_img:hover {
        transform: scale(1.02);
    }
</style>



<script>
    // $("#bt_endo_muro").addClass("active_item");
    // $("#nav_endomarketing").addClass("menu-is-opening menu-open");
    $(".menu_section").addClass("active");
    // $("#nav_endomarketing").addClass("active");
    jQuery("#menu_endomarketing").css("display", "none");
    $("#bt_endo_muro").addClass("current-page");
</script>

<script>
    $(document).ready(function() {

        var swiper = new Swiper('.swiper-container', {
            autoHeight: true, //enable auto height
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            autoplay: {
                delay: 3000,
            },
        });

    });
</script>




<style>
    .parrafo_card {
        padding: 10px 15px;
    }

    .bt_option_card {
        background-color: #efefef;
        cursor: pointer;
    }

    .bt_option_card:hover {
        background-color: #cccccc;
    }

    .swiper-container {
        width: 100%;
        height: auto;
    }

    .swiper-slide {
        text-align: center;
        font-size: 18px;
        background: #fff;
    }

    .swiper-container .swiper-slide {}

    .comentarios_item {
        padding: 5px 15px;
        border: 1px solid #e4e4e4;
        margin: 5px 15px;
    }

    .comentario_fecha {
        font-size: 10px;
    }
</style>

<script>
    var urls = "<?php echo $url; ?>/api/clima/";


    var activar = false;
    //GUARDAR ACADEMICO
    function Borrar_Reto(val) {

        if (activar == false) {

            console.log("ingreso");
            $("#modal_borrar").modal("show");

            $("#cont_modal_borrar").html('Esta a punto de eliminar esta publicación, esto eliminará los comentarios y me gusta relacionados ¿Esta seguro?');

            $("#botones_modal").html('<button type="button" class="btn btn-danger" onclick="activar = true; Borrar_Reto(' + val + ')">Eliminar</button>');
            $("#botones_modal").append('<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>');
        } else {

            $("#preloader").show();

            jQuery.ajax({
                    url: urls + "borrar_reto.php",
                    type: 'post',
                    data: {
                        id: val,
                        url: '?pg=endomarketing/muro'
                    },
                }).done(function(resp) {
                    //$("this").hide();	
                    $("#xscript").html(resp);
                })
                .fail(function() {})
                .always(function(resp) {
                    $("#preloader").hide();
                });
        }
    }

    $(function() {
        $('[data-toggle="popover"]').popover({
            trigger: 'focus',
            html: true
        });
    });

    function Me_Gusta(val) {
        $("#preloader").show();

        data = {
            id: val,
            u: <?php echo $_SESSION['id_user_valentina']; ?>,
            name: '<?php echo $dtEmpleado["nombre"] . " " . $dtEmpleado["apellidos"]; ?>'
        };

        jQuery.ajax({
                url: urls + "me_gusta.php",
                type: 'post',
                data: data,
            }).done(function(resp) {
                //$("this").hide();	
                $("#xscript").html(resp);
            })
            .fail(function() {})
            .always(function(resp) {
                $("#preloader").hide();
            });
    }


    function Acepto_Reto(val) {

        data = {
            id: val,
            u: <?php echo $_SESSION['id_user_valentina']; ?>,
            name: '<?php echo $dtEmpleado["nombre"] . " " . $dtEmpleado["apellidos"]; ?>'
        };

        $("#preloader").show();
        jQuery.ajax({
                url: urls + "acepto_reto.php",
                type: 'post',
                data: data,
            }).done(function(resp) {
                //$("this").hide();	
                $("#xscript").html(resp);
            })
            .fail(function() {})
            .always(function(resp) {
                $("#preloader").hide();
            });
    }

    function Comentar(val) {
        $("#modal_comentar").modal("show");
        $("#cont_modal_comentar").html('<textarea id="comentario" rows="5" style="width: 100%;" required></textarea>');
        $("#cont_modal_comentar").append('<input type="hidden" value="' + val + '" id="id_comentar" />');
        $("#cont_modal_comentar").append('<input type="button" value="Comentar" class="btn btn-md btn-primary" onclick="Registrar_Comentar()"/>');

    }

    function Registrar_Comentar() {

        if ($("#comentario").val() != "") {

            $("#preloader").show();

            jQuery.ajax({
                    url: urls + "comentario.php",
                    type: 'post',
                    data: {
                        id: $("#id_comentar").val(),
                        u: <?php echo $_SESSION['id_user_valentina']; ?>,
                        name: '<?php echo $dtEmpleado["nombre"] . " " . $dtEmpleado["apellidos"]; ?>',
                        comentario: $("#comentario").val()
                    },
                }).done(function(resp) {
                    //$("this").hide();	
                    window.location.reload();
                    $("#xscript").html(resp);
                })
                .fail(function() {})
                .always(function(resp) {
                    $("#preloader").hide();
                });
        } else {
            alert("Debes registrar un comentario");
        }
    }

    function Filtrar_Variable(val) {
        location.href = "?pg=home&tp=<?php echo $tipo ?>" + "&var=" + val;

    }
</script>