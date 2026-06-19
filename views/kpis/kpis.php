<script>
    $(document).ready(function() {
        $('#menuKpis').collapse();
        $('#bt_kpis_compania').addClass('active');
    });
</script>

<?php
//PAGINACION
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$limite = isset($_GET["limite"]) ? (int)$_GET["limite"] : 50;
$offset = ($pagina - 1) * $limite;

include("app/models/kpis/Kpis.php");
$ClassOkrs = new Kpis();
$Kpis = $ClassOkrs->Kpis_compania($user_log["id"], $user_log["id_empresa"], $_SESSION["anio_fill"], $limite, $offset);
$resultado_general = 25;
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<div class="container-fluid pb-4" style="max-width: 90%; margin: 0 auto;">
    
    <!-- TITULO -->
    <div class="card mb-3">
        <div class="card-header">
            <h3>Todos los KPIs de la Compañía</h3>
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
    <div class="row pb-4">
        <div class="col-md-12">
            <div class="card p-1">
                <div class="card-body">
                    <div class="accordion" id="accordionKpis">

                        <?php if (!empty($Kpis )) : ?>

                            <?php foreach ($Kpis as $kpi) : ?>
                                <!-- KPIS -->
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

    <!-- PAGINACION -->
    <?php
    $total_kpis = $Kpis["total_kpis"];
    $total_paginas = ceil($total_kpis / $limite);
    ?>
    <nav aria-label="Paginación OKRs">
        <ul class="pagination justify-content-center">

            <li class="page-item mx-4 d-flex align-items-center">
                Mostrando&nbsp;
                <select id="limiteSelect" class="form-select form-select-sm" style="width: 80px;">
                    <option value="50" <?= isset($_GET["limite"]) && $_GET["limite"] == 50 ? 'selected' : '' ?>>50</option>
                    <option value="100" <?= isset($_GET["limite"]) && $_GET["limite"] == 100 ? 'selected' : '' ?>>100</option>
                </select>
            </li>

            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                    <a class="page-link"
                        href="?pg=kpis/kpis&pagina=<?= $i ?>&limite=<?= $limite ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>

        </ul>
    </nav>

</div>
<script>
document.getElementById("limiteSelect").addEventListener("change", function() {
    const limite = this.value;

    // Reinicia la página en la página 1 cuando cambia el límite
    window.location.href = "?pg=kpis/kpis&pagina=1&limite=" + limite;
});
</script>