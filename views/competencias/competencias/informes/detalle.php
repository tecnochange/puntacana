<script>
    $(document).ready(function() {
        $('#menuCompetencias').collapse();
        $('#bt_tipo_competencias').addClass('active');
    });
</script>

<?php
include("app/models/competencias/Competencias.php");
include("app/models/competencias/CompetenciasCrud.php");
$ClassCompetencias = new Competencias();
$ClassCompetenciasCrud = new CompetenciasCrud();

//PROCESAR
if ($_POST) {
    if (!empty($_POST["id_informe"])) {
        //Editar
        $ClassCompetenciasCrud->Editar_Informe_Competencia($_POST); //Editar
    }
}

//OBTENER INFORME DE COMPETENCIA
$informe_competencia = null;
if (isset($_GET["id"]) && !empty($_GET["id"])) {
    $informe_competencia = $ClassCompetencias->Obtener_Informe_Competencia($_GET["id"]);
}else{
    echo "No hay un identificador válido";
    return;
}
?>

<div class="container-fluid" style="max-width: 90%; margin: 0 auto;">

    <!-- TITULO -->
    <div class="card mb-3">
        <div class="card-header">
            <h3>Informe</h3>
        </div>
    </div>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" style="margin-top: 15px;">
        <ol class="breadcrumb">
            <li class="breadcrumb-item" aria-current="page"><a href="?pg=competencias/competencias/informes">Informes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detalle</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">

                    <div class="row">
                        <form action="" method="post">
                            <input type="hidden" name="id_informe" value="<?= isset($_GET["id"]) && !empty($_GET["id"]) ? $_GET["id"] : null; ?>">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3 id="nombre_indicador"><?= $informe_competencia["indicador"]; ?></h3>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="ti_label">Fortaleza</label>
                                        <textarea class="form-control" name="fortaleza" required placeholder="Ingrese..." rows="3"><?= isset($informe_competencia["fortaleza"]) ? $informe_competencia["fortaleza"] : ''; ?></textarea>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="ti_label">Oportunidad de mejora</label>
                                        <textarea class="form-control" name="oportunidad" required placeholder="Ingrese..." rows="3"><?= isset($informe_competencia["oportunidad"]) ? $informe_competencia["oportunidad"] : ''; ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" style="margin-top: 15px">
                                <button type="submit" class="btn btn-success w-100">
                                    Guardar
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>