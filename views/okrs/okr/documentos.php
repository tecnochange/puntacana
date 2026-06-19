<script>
    $(document).ready(function() {
        $('#menuOkrs').collapse();
        $('#bt_okrs_crear').addClass('active');
    });
</script>

<?php
if($_GET["new"]){
    $_SESSION["id_okrs_edit"] = "";
}

include("app/models/okrs/OkrsCrud.php");
include("app/models/okrs/OkrsServicios.php");

$OkrsCRUD = new OkrsCrud();
$OkrsServicios = new OkrsServicios();

if($_GET["id"]){
    $_SESSION["id_okrs_edit"] = $_GET["id"];
    $disabled = "disabled";
}

$hoy = date("Y-m-d H:i:s");

//PARA GUARDAR O EDITAR CONTENIDO
if ($_POST["guardar_formulario"]) {

    if($_POST["id_registro"]) {
        //ACTUALIZAR OKRS
        $OkrsCRUD->Actualizar_Okrs($_POST);
    } 
    else {
        //CREAR OKRS
        $OkrsCRUD->Crear_Okrs($user_log["id_empresa"], $_POST);
    }
}
?>

<div class="container">

    <?php echo $respuesta; ?>

    <?php if($_SESSION["id_okrs_edit"]){ ?>
        <div class="card mb-3">
            <div class="card-body" style="font-size: 19px; font-weight: bold; color: #007bff;">
                <?php echo $data["objetivo_okr"] ?>
            </div>
        </div>

        <ul class="nav nav-tabs justify-content-center">
            <li class="nav-item">
                <a class="nav-link" href="<?php echo $url; ?>?pg=okrs/okr/detalle">PASO 1. OKRS del Equipo</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo $url; ?>?pg=okrs/okr/areas" >PASO 2. Áreas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo $url; ?>?pg=okrs/okr/integrantes" >PASO 3. Integrantes</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo $url; ?>?pg=okrs/okr/resultados" >PASO 4. Resultados Claves (KR)</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="<?php echo $url; ?>?pg=okrs/okr/documentos" >PASO 5. Documentos</a>
            </li>
        </ul>
    <?php  } ?>

    <div class="card mb-3">
        <div class="card-header">
            <h3>Ficha del OKRs</h3>
        </div>
        <div class="card-body">
        </div>
    </div>
</div>