<?php
session_start();
//VALIDACION DE SESION
if ($_SESSION['id_super_user_valentina'] == "") {
    header('Location: log.php');
} 
else {
    include("../app/connect.php");
    //include("../app/functions.php");
    include("../app/arrays.php");

    $qryEmpresa = mysqli_query($connect_admin, "SELECT * FROM Empresas WHERE id = '" . $_SESSION['id_empresa_valentina'] . "' ");
    $dtEmpresa = mysqli_fetch_array($qryEmpresa);

    if (!$_SESSION["anio_valentina"]) {
        $_SESSION["anio_valentina"] = $dtEmpresa["anio_curso"];
        $_SESSION["periodo_desempenio"] = $dtEmpresa["anio_curso"];
    }
    if ($_POST["anio_valentina"]) {
        $_SESSION["anio_valentina"] = $_POST["anio_valentina"];
        $_SESSION["periodo_desempenio"] = $dtEmpresa["anio_valentina"];
    }
}
$pagina = $_GET["pg"];

//PAGINA VACIA
if ($pagina == "") {
    $pagina = "home";
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../IsotipoGFA.png">
    <title>GoForAgile - Mi Desempeño</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <style>
        .card,
        .card-header,
        .card-body,
        .card-footer {
            background-color: white !important;
        }
    </style>
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.2.2/b-3.2.2/b-colvis-3.2.2/b-html5-3.2.2/b-print-3.2.2/r-3.0.4/datatables.min.css" rel="stylesheet" integrity="sha384-36xVYoVpuj+hzfRrR91sSCo/8Iji7nvnohlqUFSlILIW0+haq7cly9vtVhRt/1J+" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <img class="img-profile rounded-circle"
                        src="https://puntacana.goforagile.com/recursos/1698844635LogoGrupoPuntacanaFullColor.png" style="max-width: 50px;max-height: 50px;">
                    <ul class="navbar-nav ml-auto">

                        <div class="topbar-divider d-none d-sm-block"></div>
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small" style="font-size: 1rem !important;"><?php echo $_SESSION['nombre_super_valentina']; ?></span>
                                <img class="img-profile rounded-circle"
                                    src="https://puntacana.goforagile.com/recursos/<?php echo $_SESSION['foto_valentina']; ?>">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">

                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal" style="font-size: 1rem !important;">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400" style="color: red !important;"></i>
                                    Cerrar Sesión
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <div class="container-fluid">
                    <?php include("views/consolidado_desempenio.php"); ?>
                </div>
            </div>

        </div>
    </div>
    <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>

    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Desea cerrar sesión?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Al cerrar sesión, volvera a la página de login.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                    <a class="btn btn-danger" href="close_session.php">Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </div>



    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <script src="js/sb-admin-2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js" integrity="sha384-VFQrHzqBh5qiJIU0uGU5CIW3+OWpdGGJM9LBnGbuIH2mkICcFZ7lPd/AAtI7SNf7" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js" integrity="sha384-/RlQG9uf0M2vcTw3CX7fbqgbj/h8wKxw7C3zu9/GxcBPRKOEcESxaxufwRXqzq6n" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.2.2/b-3.2.2/b-colvis-3.2.2/b-html5-3.2.2/b-print-3.2.2/r-3.0.4/datatables.min.js" integrity="sha384-qWl+l3Q5bpd1v1LD0Y3N+Y6jhIaFL5gVbG4JelfgXbdRjEuLsQwYrdOYIVeQgS0J" crossorigin="anonymous"></script>
    <script src="https://goforagile.com/js/highcharts/code/highcharts.js"></script>
    <script src="https://goforagile.com/js/highcharts/code/highcharts-more.js"></script>
    <script src="https://goforagile.com/js/highcharts/code/modules/exporting.js"></script>
    <script src="https://goforagile.com/js/highcharts/code/modules/export-data.js"></script>
    <script src="https://goforagile.com/js/highcharts/code/modules/accessibility.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            
            $('#competencias').DataTable({
                columnDefs: [{
                        responsivePriority: 1,
                        targets: 0
                    },
                    {
                        responsivePriority: 2,
                        targets: -1
                    },

                ],
                responsive: false,
                pageLength: 100,
                order: [
                    [<?php if ($_SESSION["periodo_desempenio"] == 2024 && $_SESSION["id_empresa_valentina"] == 1) {
                            echo 10;
                        } else {
                            echo  11;
                        } ?>, 'desc']
                ],
                language: {
                    processing: "Procesando...",
                    search: "Buscar:",
                    lengthMenu: "Mostrar _MENU_ registros.",
                    info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    infoEmpty: "Mostrando registros del 0 al 0 de 0 registros",
                    infoFiltered: "(filtrado de un total de _MAX_ registros)",
                    infoPostFix: "",
                    loadingRecords: "Cargando...",
                    zeroRecords: "No se encontraron resultados",
                    emptyTable: "Ningún dato disponible en esta tabla",
                    row: "Registro",
                    export: "Exportar",
                    paginate: {
                        first: "Primero",
                        previous: "Anterior",
                        next: "Siguiente",
                        last: "Ultimo"
                    },
                    aria: {
                        sortAscending: ": Activar para ordenar la columna de manera ascendente",
                        sortDescending: ": Activar para ordenar la columna de manera descendente"
                    },
                    select: {
                        row: "registro",
                        selected: "seleccionado"
                    }
                },
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'collection',
                        text: 'Exportar',
                        buttons: [
                            'copy',
                            'excel',
                            'csv',
                            {
                                extend: 'pdfHtml5',
                                text: 'PDF',
                                orientation: 'landscape',
                                pageSize: 'LEGAL'
                            },
                            {
                                extend: 'print',
                                customize: function(win) {
                                    $(win.document.body)
                                        .css('font-size', '10pt');

                                    $(win.document.body).find('table')
                                        .addClass('compact')
                                        .css('font-size', 'inherit');
                                }
                            }
                        ]
                    }

                ]

            });

        });
    </script>
</body>

</html>