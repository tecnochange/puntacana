<script>
    $(document).ready(function() {
        $('#menuOkrs').collapse();
        $('#bt_okrs_mis_iniciativas').addClass('active');
    });
</script>

<?php
include("app/models/okrs/OkrsServicios.php");
$ClassOkrsServicios = new OkrsServicios();
/*
$okrs_iniciativas = $ClassOkrsServicios->okrs_area_iniciativas($user_log["id"], $user_log["id_empresa"], $user_log["id_area"], $_SESSION["anio_fill"]); 
*/
$okrs_objetivos_asociados = $ClassOkrsServicios->objetivos_asociados($user_log["id_empresa"], $user_log["id"], $user_log["id_area"], $_SESSION["anio_fill"]);

$okrs_iniciativas = $ClassOkrsServicios->iniciativas_responsable($user_log["id_empresa"], $user_log["id"], $okrs_objetivos_asociados);

$datos_consolidado_iniciativas = $ClassOkrsServicios->datos_consolidado_iniciativas($user_log["id_empresa"], $okrs_iniciativas);

$total = 0;
$count_total = 0;
$bg_color = '';
foreach ($okrs_iniciativas as $ini){
    $total += $ini["porcentaje_avance"]; 
    $count_total++;
}

$resultado_general = round($total/$count_total);
$bg_color = EscalaColor($resultado_general);
?>

<div class="container-fluid pb-4" style="max-width: 90%; margin: 0 auto;">

    <!-- TITULO -->
    <div class="card mb-3">
        <div class="card-header">
            <h3>Gestionar Mis Iniciativas</h3>
        </div>
        <div class="card-body">
            <div>
                Aquí podrá gestionar los objetivos, resultados, iniciativas y planes de acción de su área o equipo, recuerde que puede utilizar los filtros para acceder de forma rápida a la información requerida.
            </div>
        </div>
    </div>

    <!-- FILTROS -->
    <?php include("views/okrs/componentes/filtros.php"); ?>

    <?php if(count($okrs_iniciativas) == 0): ?>
        <div class="card">
            <div class="card-body text-center">
                <h5 class="text-secondary">Actualmente no hay Iniciativas asociadas</h5>
            </div>
        </div>
    <?php exit; endif; ?>

    <!-- AVANCE GENERAL -->
    <div class="card mb-3">

        <div class="card-body">

            <h5>AVANCE GENERAL DE MIS INICIATIVAS</h5>
            <div class="progress mb-4" style="height: auto;">
                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $datos_consolidado_iniciativas["promedio_general"]; ?>%; background-color: <?php echo $datos_consolidado_iniciativas["color_general"]; ?> !important;" aria-valuenow="<?php echo $resultado_general; ?>" aria-valuemin="0" aria-valuemax="100">
                    <div style="font-size: 40px; color: #000000;"><b><?php echo $datos_consolidado_iniciativas["promedio_general"]; ?>%</b></div>
                </div>
            </div>

            <!-- CONSOLIDADO -->
            <?php include("views/okrs/componentes/consolidado_iniciativas.php"); ?>

        </div>
    </div>

    <!-- LISTADO DE INICIATIVAS -->
    <div class="card mb-3">

        <div class="card-body">

            <div class="alert alert-warning" role="alert">
                <h3>¡Atención! Formato numérico en el sistema</h3>

                Digita los números sin puntos ni comas como separadores de miles. El sistema aplicará el formato correcto (americano o europeo) según tu configuración.<br><br>

                Ejemplos:<br>
                escribe 1500000.25 y se mostrará como 1,500,000.25 o 1.500.000,25<br>
                Evita errores comunes:<br>
                ✖ No uses puntos para separar miles manualmente<br>
                ✖ No mezcles símbolos como en 1.000,45 o 1,000,45<br>
                ✔ Escribe los números de forma continua y deja que el sistema los formatee<br>
            </div>

            <div class="table-responsive mt-4" style="min-height: 400px !important;">
                <table class="table" id="tabla_general">
                    <thead>
                        <tr>
                            <th>Iniciativa</th>
                            <th class="text-center">Mes</th>
                            <th class="text-center">Fecha Entrega</th>
                            <th class="text-center">Meta</th>
                            <th class="text-center">Seguimiento</th>
                            <th class="text-center">Porcentaje</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($okrs_iniciativas as $iniciativa): ?>
                            <tr>
                                <td><?= $iniciativa["descripcion"]; ?></td>
                                <td class="text-center"><?= $ClassOkrsServicios->nombreMes($iniciativa["mes"]); ?></td>
                                <td class="text-center"><?= $iniciativa["fecha_entrega"]; ?></td>
                                <td class="text-center"><?= $iniciativa["meta"]; ?></td>
                                <td class="text-center">
                                    <input
                                        type="text"
                                        class="form-control form-control-sm decimales"
                                        style="max-width: 80%; display: inline-block;"
                                        onkeyup="okrsClass.EditarSeguimiento('Okrs_Iniciativas',<?= $iniciativa['id'] ?>,this.value)" value="<?= $iniciativa["avance"]; ?>"
                                    >
                                    <a href="" class="btn btn-sm btn-warning float-end"><i class="bi bi-arrow-clockwise" title="Actualizar"></i></a>
                                </td>
                                <td class="text-center">
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= empty($iniciativa["porcentaje_avance"]) || (float)$iniciativa["porcentaje_avance"] == 0 ? '100' : $iniciativa["porcentaje_avance"]; ?>%; background-color:<?= $iniciativa["bg_color"] ?> !important; " aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <b><?= $iniciativa["porcentaje_avance"]; ?>%</b>
                                </td>
                                <td>
                                    <button class="btn btn-sm" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bx bx-dots-horizontal-rounded"></i>
                                    </button>
                                    <ul class="dropdown-menu" id="menu_acciones">
                                        <li>
                                            <a href="?pg=okrs/okr/iniciativas&id_okr=<?= $iniciativa["id_okrs"]; ?>&id_resultado=<?= $iniciativa["id_resultado"]; ?>&id_iniciativa=<?= $iniciativa["id"]; ?>" class=" dropdown-item">Editar Iniciativa</a>
                                        </li>
                                        
                                        <li>
                                            <a href="#"
                                                class="dropdown-item btn-comentarios"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modal_comentarios_iniciativas"
                                                data-id_okr="<?= $iniciativa["id_okrs"]; ?>"
                                                data-id_resultado="<?= $iniciativa["id_resultado"]; ?>"
                                                data-id_empleado="<?= $user_log["id"]; ?>"
                                                data-id_iniciativa="<?= $iniciativa["id"]; ?>"
                                                data-descripcion="<?= $iniciativa["descripcion"]; ?>">
                                                Comentarios
                                            </a>
                                        </li>
                                        <li>
                                            <a target="_blank" href="?pg=okrs/gestionar_iniciativa&id_okrs=<?= $iniciativa["id_okrs"]; ?>&id_resultado=<?= $iniciativa["id_resultado"]; ?>&id_iniciativa=<?= $iniciativa["id"]; ?>" class="dropdown-item">Documentos</a>
                                        </li>
                                        <li>
                                            <a href="?pg=okrs/okr/planes_accion&id_okrs=<?= $iniciativa["id_okrs"]; ?>&id_resultado=<?= $iniciativa["id_resultado"]; ?>&id_iniciativa=<?= $iniciativa["id"]; ?>" class="dropdown-item">Planes de Acción</a>
                                        </li>
                                        <li>
                                            <a target="_blank" href="?pg=okrs/gestionar_iniciativa&id_okrs=<?= $iniciativa["id_okrs"]; ?>&id_resultado=<?= $iniciativa["id_resultado"]; ?>&id_iniciativa=<?= $iniciativa["id"]; ?>" class="dropdown-item">Gestionar Iniciativas</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-secondary">Duplicar Iniciativa</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" onclick="EliminarIniciativa(<?= $iniciativa["id"]; ?>)" >Eliminar Iniciativas</a>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>


</div>
<?php include("app/models/okrs/OkrsScripts.php"); ?>
<?php include("views/okrs/okr/js/comentarios_iniciativas.php"); ?>
<script>

    $(document).ready(function() {
        $('#tabla_general').DataTable({
            pageLength: 50,
            order: [[2, 'asc']],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            }
        });
    });

    // Instancia global accesible por los inputs
    const okrsClass = new OkrsScripts();
</script>




<script>
    var api = '<?php echo $url; ?>api/okrs/';
    var permitir = false;
    function EliminarIniciativa(id_iniciativa){

        if(permitir == false){
            $("#modal_general").modal("show");
            $("#modal_body").html("Estas a punto de eliminar una iniciativa, esto eliminará todos los datos relacionados. esta acción  es irreversible. ¿Está seguro? <br><br> ");
            $("#modal_body").append('<button type="button" class="btn btn-danger btn-sm" onclick="permitir = true;EliminarIniciativa('+id_iniciativa+')">Eliminar Iniciativa</button> <br> Nota: este esta acción será registrada en la auditoría con su nombre.');
            
        }
        else{

            data = {
                id_empresa: <?php echo $user_log["id_empresa"]; ?>, 
                id_user: <?php echo $user_log["id"]; ?>,
                id_iniciativa: id_iniciativa, 
                url: '?pg=okrs/mis_iniciativas'
            };
            jQuery.ajax({
                url: api + "eliminar_iniciativa.php",
                type: 'post',
                data: data,
                })
                .done(function(resp) {
                    $("#xscript").html(resp);
                })
                .fail(function(resp) {
                    console.log(resp);
                })
                .always(function(resp) {}
            );

        }

    }
</script>