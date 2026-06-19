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
    if(empty($_POST["id_tipo"])){
        $ClassCompetenciasCrud->Guardar_Tipo_Competencia($user_log["id_empresa"], $_SESSION["anio_ciclo"], $_POST); //Guardar
    }else{
        $ClassCompetenciasCrud->Editar_Tipo_Competencia($_POST); //Editar
    }

    echo '<script> window.location = "?pg=competencias/competencias/tipos"; </script>';
}

//OBTENER TIPO DE COMPETENCIA
$tipo_competencia = null;
if(isset($_GET["id"]) && !empty($_GET["id"])){
    $tipo_competencia = $ClassCompetencias->Obtener_Tipo_Competencia($_GET["id"]);
}
?>



<div class="container-fluid" style="max-width: 90%; margin: 0 auto;">

    <!-- TITULO -->
    <div class="card mb-3">
        <div class="card-header">
            <h3>Tipo de Competencia</h3>
        </div>
    </div>

    <!-- Breadcrumb -->
	<nav aria-label="breadcrumb" style="margin-top: 15px;">
		<ol class="breadcrumb">
			<li class="breadcrumb-item" aria-current="page"><a href="?pg=competencias/competencias/tipos">Tipos</a></li>
			<li class="breadcrumb-item active" aria-current="page">Detalle</li>
		</ol>
	</nav>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">

                    <div class="row">
                        <form action="" method="post">
                            <input type="hidden" name="id_tipo" value="<?= isset($_GET["id"]) && !empty($_GET["id"]) ? $_GET["id"] : null; ?>">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="nombre">Nombre *</label>
                                        <input type="text" class="form-control" name="nombre" id="nombre" value="<?= isset($tipo_competencia) ? $tipo_competencia["nombre"] : null; ?>" required>
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