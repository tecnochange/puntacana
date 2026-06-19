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
if($_POST){
    if(empty($_POST["id_nivel"])){
        $ClassCompetenciasCrud->Guardar_Nivel_Competencia($user_log["id_empresa"], $_SESSION["anio_ciclo"], $_POST); //Guardar
    }else{
        $ClassCompetenciasCrud->Editar_Nivel_Competencia($_POST); //Editar
    }
    echo '<script> window.location = "?pg=competencias/competencias/niveles"; </script>';
}

//OBTENER TIPO DE COMPETENCIA
$nivel_competencia = null;
if(isset($_GET["id"]) && !empty($_GET["id"])){
    $nivel_competencia = $ClassCompetencias->Obtener_Nivel_Competencia($_GET["id"]);
}
?>

<div class="container-fluid" style="max-width: 90%; margin: 0 auto;">

    <!-- TITULO -->
    <div class="card mb-3">
        <div class="card-header">
            <h3>Nivel de Competencia</h3>
        </div>
    </div>

    <!-- Breadcrumb -->
	<nav aria-label="breadcrumb" style="margin-top: 15px;">
		<ol class="breadcrumb">
			<li class="breadcrumb-item" aria-current="page"><a href="?pg=competencias/competencias/niveles">Niveles</a></li>
			<li class="breadcrumb-item active" aria-current="page">Detalle</li>
		</ol>
	</nav>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">

                    <div class="row">
                        <form action="" method="post">
                            <input type="hidden" name="id_nivel" value="<?= isset($_GET["id"]) && !empty($_GET["id"]) ? $_GET["id"] : null; ?>">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="nombre">Nombre *</label>
                                        <input type="text" class="form-control" name="nombre" id="nombre" value="<?= isset($nivel_competencia) ? $nivel_competencia["nombre"] : null; ?>" required>
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