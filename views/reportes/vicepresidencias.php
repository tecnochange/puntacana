<script>
    $(document).ready(function() {
        $('#menuReportes').collapse();
        $('#bt_reportes_vicepresidencia').addClass('active');
    });
</script>

<?php

function ObtenerOkrsVicepresidencias($id_vicepresidencia, $anio){
    global $connect_admin;
    $total = 0;
    $cantidad = 0;
    $sentecia = "SELECT * FROM Datos_Sincronizados WHERE anio = '".$anio."' AND id_vicepresidencia = '".$id_vicepresidencia."' ";
    $query = mysqli_query($connect_admin, $sentecia);
    while($data = mysqli_fetch_array($query)){
        $total += $data["okrs"];
        $cantidad++;
    }

    $resultado = 0;
    if($total > 0){
        $resultado = $total/$cantidad;
    }

    return $resultado; 
}

//VICEPRESIDECIAS
include("app/models/estructura/Vicepresidencias.php");
$ClassVicepresidencias = new Vicepresidencias();
$vicepresidencias_listas = $ClassVicepresidencias->vicepresidencias_lista(NULL);

include("app/models/okrs/OkrsServicios.php");
$ClassOkrServicios = new OkrsServicios();

?>


<div class="container">
    <div class="card mb-3">
        <div class="card-body">
            <h3>CONSOLIDADO PRIMER NIVEL ORGANIZACIONAL (ALTA DIRECCIÓN) AÑO <?php echo $_SESSION["anio_fill"]; ?></h3>
        </div>
    </div>

    <?php include("views/reportes/layouts/filtros.php"); ?>

    <div class="card">
        <div class="card-body">
            <table class="table" id="vicepresidencias_table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Líder</th>
                        <th>OKRs Asignados</th>
                        <th>Progreso</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach($vicepresidencias_listas as $vicepresidencia){ 

                        $data = $ClassOkrServicios->okrs_vicepresidencias($user_log["id_empresa"], $vicepresidencia["id"]);
                        //$okrs_filtro = $okrs_organizacion;
                        $datos_consolidado = $ClassOkrServicios->datos_consolidado_okrs($user_log["id_empresa"], $data);

                        echo '
                            <tr>
                                <td>'.$vicepresidencia["nombre"].'</td>
                                <td>'.$vicepresidencia["lideres"].'</td>
                                <td>'.$datos_consolidado["cantidad_okrs"].'</td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: '.$datos_consolidado["promedio_general"].'%;  background-color:'.$datos_consolidado["color_general"].'; color: #000000; ">
                                            '.$datos_consolidado["promedio_general"].'%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="?pg=reportes/vicepresidencia/detalle&id='.$vicepresidencia["id"].'" type="button" class="btn btn-success btn-sm" title="Vista rápida de los OKRs">
                                        <i class="bx bx-show"></i>
                                    </a>
                                </td>
                            </tr>
                        ';

                    }
  
                    ?>
                </tbody>

            </table>
        </div>
    </div>


</div>




<style>
    /* Margen debajo de la barra de herramientas (botones) */
	.dt-buttons {
		margin-bottom: 15px !important;
	}
	/* Margen debajo de la tabla (paginación) */
	.dataTables_paginate, .dataTables_info{
		margin-top: 15px !important;
	}
</style>



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
        $('#vicepresidencias_table').DataTable({
            pageLength: 25,
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

<script>
    var api = '<?php echo $url; ?>api/kpis/';
    function FichaEmpleado(id_empleado){

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
            .always(function(resp) {}
        );
    }
</script>