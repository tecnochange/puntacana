<script>
    $(document).ready(function() {
        $('#menuOkrs').collapse();
        $('#bt_okrs_crear').addClass('active');
    });
</script>

<?php
include("app/models/okrs/OkrsCrud.php");
include("app/models/okrs/OkrsServicios.php");
$OkrsCRUD = new OkrsCrud();
$OkrsServicios = new OkrsServicios();

$hoy = date("Y-m-d H:i:s");

//PARA GUARDAR O EDITAR CONTENIDO
if ($_POST["guardar_formulario"]) {

    if($_POST["id_registro"]) {
        //ACTUALIZAR OKRS AREAS
        $_POST["id_okrs"] = $_SESSION["id_okrs_edit"];
        $OkrsCRUD->Crear_Okrs_Areas($user_log["id_empresa"], $_POST);
        echo '<script>window.location.href = "?pg=okrs/okr/areas"; </script>';
    }
}

$data = $OkrsServicios->obtener_okrs($_SESSION["id_okrs_edit"]); //OBTENER OKRS

if($data["tipo"] == 1){
    $listado_okrs_areas_asignadas = $OkrsServicios->listado_okrs_areas_asignadas_organizacional($user_log["id_empresa"], $data["id_vicepresidencia"], $_SESSION["id_okrs_edit"]); //OBTENER AREAS ASIGNADAS AL OKRS
}else if($data["tipo"] == 2){
    $listado_okrs_areas_asignadas = $OkrsServicios->listado_okrs_area_asignados($user_log["id_empresa"], $_SESSION["id_okrs_edit"]); //OBTENER AREAS ASIGNADAS AL OKRS
}

if (isset($_GET["id_okr_area"]) && !empty($_GET["id_okr_area"])) {
    $data_okr_area = $OkrsServicios->obtener_okr_area($_GET["id_okr_area"]); 
}
?>

<div class="container pb-4">

    <div class="card mb-3">
        <div class="card-body" style="font-size: 19px; font-weight: bold; color: #007bff;">
            <?php echo $data["objetivo_okr"] ?>
        </div>
    </div>

    <?php echo $respuesta; ?>

    <ul class="nav nav-tabs justify-content-center">
        <li class="nav-item">
            <a class="nav-link " href="<?php echo $url; ?>?pg=okrs/okr/detalle">Paso 1. OKRS del Equipo</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="<?php echo $url; ?>?pg=okrs/okr/areas">PASO 2. Áreas</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?php echo $url; ?>?pg=okrs/okr/integrantes">PASO 3. Integrantes</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?php echo $url; ?>?pg=okrs/okr/resultados">PASO 4. Resultados Claves (KR)</a>
        </li>
    </ul>

    <div class="card mb-3">

        <div class="card-body">

            <div class="alert alert-warning" role="alert">
                <h3>Importante: Estás creando un OKR de Equipo.</h3>
                Primero, selecciona la Alta Dirección (Vicepresidencia) a la que pertenece tu equipo. <br>
                Luego, elige el área o equipo específico que será responsable de liderar este objetivo. <br><br>

                En este paso no debes incluir otras áreas de apoyo.<br>
                Si hay otras áreas que colaboran, se vincularán más adelante al asignar los miembros en la célula de trabajo con los Resultados Clave o Iniciativas.<br><br>

                Esto nos ayuda a mantener una estructura clara para los informes y una trazabilidad precisa por equipo y por área.<br>
            </div>

            <form action="" method="POST">
                <input type="hidden" name="id_registro" value="<?= $_SESSION["id_okrs_edit"] ?? null; ?>">
                <input type="hidden" name="guardar_formulario" value="true">
                <div class="row">

                    <div class="col-md-4 mb-2">
                        <label><strong>Alta Dirección *</strong></label>
                        <select class="multiples_responsables form-control" id="vicepresidencia" name="id_vicepresidencia" onchange="ObtenerAreas()" required>
                            <option value="">Seleccione...</option>
                            <?php
                                $queryVicepresidencias = mysqli_query($connect_admin, "SELECT * FROM Vicepresidencia WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND estado = 1 ORDER BY nombre ASC");
                                while ($dataVicepresidencia = mysqli_fetch_array($queryVicepresidencias)):
                                ?>
                                    <option value="<?= $dataVicepresidencia["id"]; ?>" <?= isset($data_okr_area["id_vicepresidencia"]) && $data_okr_area["id_vicepresidencia"] == $dataVicepresidencia["id"] ? "selected" : "" ?>><?= $dataVicepresidencia["nombre"]; ?></option>
                                <?php 
                                endwhile;            
                            ?>
                        </select>
                    </div>

                    <?php if($data["tipo"] == 2): ?>
                    <div class="col-md-4 mb-2">
                        <label><strong>Área *</strong></label>
                        <select class="multiples_responsables form-control" id="area" name="id_area">
                            <option value="">Seleccione...</option>
                        </select>
                    </div>
                    <?php endif; ?>


                    <div class="col-md-12 mb-2">
                        <button type="submit" class="btn btn-success ">Guardar</button>
                    </div>

                </div>
            </form>

        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Alta dirección</th>
                            <th>Áreas</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($listado_okrs_areas_asignadas as $item): ?>
                            <tr>
                                <td><?= $item["nombre_vicepresidencia"]; ?></td>
                                <td><?= $item["nombre_area"]; ?></td>
                                <td>
                                    <a href="?pg=okrs/okr/areas&id_okr_area=<?= $item["id"]; ?>" class="btn btn-outline-dark btn-sm ">
                                        <i class="bx bx-pencil" title="Editar"></i>
                                    </a>
                                    <a href="#<?= $item["id"]; ?>" class="btn btn-outline-dark btn-sm disabled">
                                        <i class="bx bx-trash" title="Eliminar Item"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
    <?php if($_GET["id_okr_area"]){ ?>
    $( document ).ready(function() {
        ObtenerAreas(<?php echo $data_okr_area["id_area"]; ?> );
    });
    <?php } ?>

    var api = '<?php echo $url; ?>api/okrs/';

    function ObtenerAreas(id_area) {
        $("#area").html("");
        jQuery.ajax({
                url: api + "lista_areas_vicepresidencia.php",
                type: 'post',
                data: {
                    id_viceprecidencia: $("#vicepresidencia").val(),
                    id_empresa: <?php echo $user_log["id_empresa"] ?>,
                    id_area: id_area
                },
            }).done(function(resp) {
                $("#area").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});
    }
</script>