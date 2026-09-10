<script>
	$(document).ready(function() {
		$('#menuKpis').collapse();
		$("#bt_seguimiento").addClass("active");
	});
</script>

<?php

include("app/models/kpis/KpisServicios.php");
$ClassKpisServicios = new KpisServicios($user_log["id_empresa"]);

include("app/models/kpis/Kpis.php");
$ClassKpis = new Kpis();
$Kpis_seguimiento_equipo = $ClassKpis->equipos($user_log["id"], $user_log["id_empresa"], $_SESSION["anio_fill"]);

?>

<div class="container-fluid" style="max-width: 90%; margin: 0 auto;">

    <!-- TITULO -->
    <div class="card mb-3">
        <div class="card-header">
            <h3>Seguimiento de Equipo</h3>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5>AVANCE GENERAL EQUIPO</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="tabla_general">
                    <thead>
                        <tr>
                            <th class="text-start">Documento</th>
                            <th>Nombre</th>
                            <th>Cargo</th>
                            <th class="text-center">KPIS Asignados</th>
                            <th class="text-center">Progreso KPIS</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        <?php foreach($Kpis_seguimiento_equipo as $kpis): ?>

                            <?php
                            $array_kpis_general = $ClassKpisServicios->kpis_colaborador($user_log["id_empresa"], NULL, NULL, $kpis["id_empleado"]);
                            $datos_consolidado_kpis = $ClassKpisServicios->datos_consolidado_kpis($user_log["id_empresa"], $array_kpis_general);
                            //print_r($datos_consolidado_kpis["promedio_general"]);

                            $porcentaje = 0;
                            if($datos_consolidado_kpis["promedio_general"] > 0){
                                $porcentaje = $datos_consolidado_kpis["promedio_general"];
                            }
                            ?>

                            <tr>
                                <td class="text-start"><?= $kpis["documento"]; ?></td>
                                <td><?= $kpis["nombre"]; ?></td>
                                <td><?= $kpis["cargo"]; ?></td>
                                <td class="text-center"><?= $kpis["asignados"]; ?></td>
                                <td class="text-center">
                                    Progreso
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= $porcentaje; ?>%; background-color:<?= EscalaColor($kpis["progreso"]); ?> !important;" aria-valuenow="<?= $kpis["progreso"]; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <b><?= number_format($porcentaje, 0); ?>%</b> 
                                </td>
                                <td class="text-center">
                                    <?php if($VALIDAR_ROOT["editar"]): ?>
                                        <a href="?pg=kpis/consolidados/colaborador&id=<?= $kpis["id_empleado"]; ?>" type="button" class="btn btn-success btn-sm" title="Editar">
                                            <i class="bx bx-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-warning btn-sm" title="Editar" style="display:none">
                                            <i class="bx bx-face"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script>
    $(document).ready(function() {
        $('#tabla_general').DataTable({
            pageLength: 50,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            }
        });
    });
</script>