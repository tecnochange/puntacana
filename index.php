<?php
include("app/connect.php");
include("app/arrays.php");
include("app/functions.php");
include("app/models/lenguaje.php");
include("app/controllers/middleware.php");
?>

<!DOCTYPE html>

<head>
    <html lang="en">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo $csrf_token; ?>">
    <title>GO FOR AGILE - OKR Suite By Change Americas</title>
    <link rel="icon" href="<?php echo $url; ?>icon.png">

    <!-- ESTILOS -->
    <link rel="stylesheet" href="<?php echo $url; ?>assets/css/bootstrap_5_0_2.css">
    <link rel="stylesheet" href="<?php echo $url; ?>assets/css/dataTables.min.css">
    <?php include("assets/css/style.php"); ?>
    <?php include("assets/css/skin.php"); ?>

    <!-- ESTILOS -->
    <script src="<?php echo $url; ?>assets/js/jquery-3.6.0.min.js"></script>
    <script src="<?php echo $url; ?>assets/js/jquery-ui.min.js"></script>
    <script src="<?php echo $url; ?>assets/js/bootstrap_5_0_2.js?id=2"></script>
    <script src="<?php echo $url; ?>assets/js/dataTables.min.js"></script>

    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://unpkg.com/boxicons@2.1.1/dist/boxicons.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"> 

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-QR4WZ86XSL"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-QR4WZ86XSL');
    </script>
</head>

<body>
    <?php include("views/layouts/modal.php"); ?>
    <?php include("views/competencias/modal_avance_valoracion.php"); ?>

    <div class="header">
        <?php include("views/layouts/header.php"); ?>
    </div>

    <div class="main_container">
        <!-- SIDEBAR -->
        <?php include("views/layouts/sidebar.php"); ?>

        <div id="content">
            <div style="height: 77px"></div>
            <?php include($route); ?>
        </div>
    </div>

    <div id="xscript"></div>

     <?php if( !$_SESSION["emergente"] ){ ?>
      <script>
        $(document).ready(function() {
                $("#modal_avance_valoracion").modal("show");
            
                // Detectar cuando el primer modal se cierra completamente
                $('#modal_avance_valoracion').on('hidden.bs.modal', function () {

                    // Mostrar el segundo modal
                    $('#modal_avance_valoracion_2').modal('show');

                });

                //$("#modal_general").modal("show");
                //$("#modal_body").html('<img src="assets/img/Lanzamiento_gofor.png" style="width: 100%;">');

                

        });
    </script>
            
    <?php $_SESSION["emergente"] = true; }  ?>

    <script>
        $(document).ready(function() {

            $('#sidebarCollapse').on('click', function() {

                $(this).toggleClass('bx-align-middle');
                $(this).toggleClass('bx-chevron-left-circle');

                $('#sidebar, #content').toggleClass('active');
                $('.collapse.in').toggleClass('in');

                $('a[aria-expanded=true]').attr('aria-expanded', 'false');
            });

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>
</body>

</html>