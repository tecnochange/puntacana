<script>
    $(document).ready(function() {
        $('#menuKpis').collapse();
        $('#bt_mis_kpis').addClass('active');
    });
</script>

<?php
include("app/models/kpis/Kpis.php");
$ClassOkrs = new Kpis();
$Kpis_colaborador = $ClassOkrs->Kpis_colaborador($user_log["id"], $user_log["id_empresa"], $_SESSION["anio_fill"]);

$resultado_general = 25;
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<div class="container-fluid pb-4" style="max-width: 90%; margin: 0 auto;">

    <!-- TITULO -->
    <div class="card mb-3">
        <div class="card-header">
            <h3>Ver Mis KPIs</h3>
        </div>
    </div>

    <!-- FILTROS -->
    <div class="row">
        <div class="col-md-12">
            <?php include("views/kpis/componentes/filtros.php"); ?>
        </div>
    </div>

    <!-- AVANCE GENERAL -->
    <div class="card mb-3">

        <div class="card-body">

            <h5>AVANCE GENERAL DE KPIS</h5>
            <div class="progress" style="height: auto;">
                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $resultado_general; ?>%; background-color:<?= EscalaColor($resultado_general); ?>!important;" aria-valuenow="<?php echo $resultado_general; ?>" aria-valuemin="0" aria-valuemax="100">
                    <div style="font-size: 40px"><b><?php echo $resultado_general; ?>%</b></div>
                </div>
            </div>

        </div>
    </div>

    <!-- LISTADO DE KPIS -->
    <div class="row">
        <div class="col-md-12">
            <div class="card p-1">
                <div class="card-body">
                    <div class="accordion" id="accordionKpis">

                        <?php if (!empty($Kpis_colaborador)) : ?>

                            <?php foreach ($Kpis_colaborador as $kpi) : ?>
                                <!-- KPIS -->
                                 <!-- <?php dd($kpi); ?> -->
                                <?php include("views/kpis/componentes/kpis.php"); ?>
                            <?php endforeach; ?>

                        <?php else : ?>

                            <div class="alert alert-info">
                                No tienes KPIs asignados para este periodo.
                            </div>

                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>