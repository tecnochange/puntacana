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

$tipos = $ClassCompetencias->Tipos_Competencias($user_log["id_empresa"], $_SESSION["anio_ciclo"]);

//PROCESAR
if($_POST){
    if(empty($_POST["id_competencia"])){
        $ClassCompetenciasCrud->Guardar_Competencia($user_log["id_empresa"], $_SESSION["ciclo"], $_SESSION["anio_ciclo"], $_POST); //Guardar
    }else{
        $ClassCompetenciasCrud->Editar_Competencia($_POST); //Editar
    }
    echo '<script> window.location = "?pg=competencias/competencias/competencias"; </script>';
}

//OBTENER COMPETENCIA
$competencia = null;
if(isset($_GET["id"]) && !empty($_GET["id"])){
    $competencia = $ClassCompetencias->Obtener_Competencia($_GET["id"]);
}
?>

<div class="container-fluid" style="max-width: 90%; margin: 0 auto;">

    <!-- TITULO -->
    <div class="card mb-3">
        <div class="card-header">
            <h3>Nueva Ficha de Competencia</h3>
        </div>
    </div>

    <!-- Breadcrumb -->
	<nav aria-label="breadcrumb" style="margin-top: 15px;">
		<ol class="breadcrumb">
			<li class="breadcrumb-item" aria-current="page"><a href="?pg=competencias/competencias/competencias">Competencias</a></li>
			<li class="breadcrumb-item active" aria-current="page">Detalle</li>
		</ol>
	</nav>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">

                    <div class="row">
                        <form action="" method="post">
                            <input type="hidden" name="id_competencia" value="<?= isset($_GET["id"]) && !empty($_GET["id"]) ? $_GET["id"] : null; ?>">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-8">
                                        <label for="nombre">Nombre *</label>
                                        <input type="text" class="form-control" name="nombre" id="nombre" value="<?= isset($competencia) ? $competencia["nombre"] : null; ?>" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="id_tipo">Tipo *</label>
                                        <select class="form-control" name="id_tipo" id="id_tipo" required>
                                            <option value="">Selecciona...</option>
                                            <?php foreach($tipos as $tipo): ?>
                                                <option value="<?= $tipo["id"]; ?>" <?= isset($competencia["id_tipo"]) && $competencia["id_tipo"] == $tipo["id"] ? "selected" : ""; ?> ><?= $tipo["nombre"]; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="definicion">Definición *</label>
                                        <textarea class="form-control" name="definicion" id="definicion" rows="3" required><?= isset($competencia) ? $competencia["definicion"] : null; ?></textarea>
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