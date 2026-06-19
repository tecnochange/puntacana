<script>
    $(document).ready(function() {
        $('#menuOkrs').collapse();
        // $('#bt_okrs_crear').addClass('active');
    });
</script>

<?php
if (isset($_GET["id_okr"]) && !empty($_GET["id_okr"])) {
    $_SESSION["id_okrs_edit"] = $_GET["id_okr"];
}else{
    echo "No hay un Okr asociado para crear alguna iniciativa";
    exit;
}

include("app/models/okrs/OkrsCrud.php");
include("app/models/okrs/OkrsServicios.php");
$OkrsCRUD = new OkrsCrud();
$OkrsServicios = new OkrsServicios();

$hoy = date("Y-m-d H:i:s");

//PARA GUARDAR O EDITAR CONTENIDO
if ($_POST["guardar_formulario"]) {

    if($_POST["id_registro"]) {
        //ACTUALIZAR RESULTADOS CLAVES
        $OkrsCRUD->Actualizar_Iniciativa($_POST);
    } 
    else {
        //CREAR RESULTADOS CLAVES
        $OkrsCRUD->Crear_Iniciativa($_POST);
    }
}

$data = $OkrsServicios->obtener_okrs($_SESSION["id_okrs_edit"]); //OBTENER OKRS
$integrantes_asociados = $OkrsServicios->obtener_integrantes_asociados($user_log["id_empresa"], $_SESSION["id_okrs_edit"]); //OBTENER INTEGRANTES ASOCIADOS AL OKRS

$listado_resultados_claves_asignados = $OkrsServicios->listado_resultados_claves_asignados($user_log["id_empresa"], $data["id_empleado"], $_SESSION["id_okrs_edit"]); //OBTENER LISTADO DE RESULTADOS CLAVES ASIGNADOS AL OKRS

if(isset($_GET["id_resultado"]) && !empty($_GET["id_resultado"])){
    $id_resultado = $_GET["id_resultado"];
    $lista_iniciativas = $OkrsServicios->okrs_iniciativas_resultados($_SESSION["id_okrs_edit"], $id_resultado);
}else{
    echo "No hay resultados de Okr asociados para crear alguna iniciativa";
    exit;
}

if (isset($_GET["id_iniciativa"]) && !empty($_GET["id_iniciativa"])) {
    $data_iniciativa = $OkrsServicios->okrs_obtener_iniciativa($_GET["id_iniciativa"]);
    $responsables_seleccionados = explode(",", $data_iniciativa["responsables"]);
}
?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="container pb-4">

    <!-- <div class="card mb-3">
        <div class="card-body" style="font-size: 19px; font-weight: bold; color: #007bff;">
            <?php echo $data["objetivo_okr"] ?>
        </div>
    </div> -->

    <?php echo $respuesta; ?>

    <div class="card mb-3">

        <div class="card-header">
            <h3>Iniciativas</h3>
        </div>

        <div class="card-body">

            <div class="alert alert-warning" role="alert">
                <h3>¡Atención! Formato numérico en el sistema</h3>
                Digita los números sin puntos ni comas como separadores de miles. El sistema aplicará el formato correcto (americano o europeo) según tu configuración. <br><br>
                Ejemplos:<br>
                escribe 1500000.25 y se mostrará como 1,500,000.25 o 1.500.000,25<br><br>
                <b>Evita errores comunes:</b><br>
                ✖ No uses puntos para separar miles manualmente <br>
                ✖ No mezcles símbolos como en 1.000,45 o 1,000,45 <br>
                ✔ Escribe los números de forma continua y deja que el sistema los formatee <br>
            </div>

            <!-- FORMULARIO -->
            <form action="" method="POST">
                <input type="hidden" name="id_registro" value="<?= isset($_GET["id_iniciativa"]) ? $_GET["id_iniciativa"] : null ?>">
                <input type="hidden" name="id_okr" value="<?= isset($_SESSION["id_okrs_edit"]) ? $_SESSION["id_okrs_edit"] : null ?>">
                <input type="hidden" name="id_resultado" value="<?= isset($id_resultado) ? $id_resultado : null ?>">
                <input type="hidden" name="id_empleado" value="<?= $data["id_empleado"]; ?>">
                <input type="hidden" name="guardar_formulario" value="true">
                <div class="row">

                    <div class="col-md-12 mb-2">
                        <label>* Descripción de la iniciativa</label>
                        <textarea name="descripcion_iniciativa" class="form-control"><?= $data_iniciativa["descripcion"]; ?></textarea>
                    </div>

                    <div class="col-md-3 mb-2">
                        <label><strong>* Mes</strong></label>
                        <select class="form-control" name="mes" required>
                            <option value="">Seleccione...</option>
                            <option value="1" <?= isset($data_iniciativa["mes"]) && $data_iniciativa["mes"] == "1" ? "selected" : "" ?>>Enero</option>
                            <option value="2" <?= isset($data_iniciativa["mes"]) && $data_iniciativa["mes"] == "2" ? "selected" : "" ?>>Febrero</option>
                            <option value="3" <?= isset($data_iniciativa["mes"]) && $data_iniciativa["mes"] == "3" ? "selected" : "" ?>>Marzo</option>
                            <option value="4" <?= isset($data_iniciativa["mes"]) && $data_iniciativa["mes"] == "4" ? "selected" : "" ?>>Abril</option>
                            <option value="5" <?= isset($data_iniciativa["mes"]) && $data_iniciativa["mes"] == "5" ? "selected" : "" ?>>Mayo</option>
                            <option value="6" <?= isset($data_iniciativa["mes"]) && $data_iniciativa["mes"] == "6" ? "selected" : "" ?>>Junio</option>
                            <option value="7" <?= isset($data_iniciativa["mes"]) && $data_iniciativa["mes"] == "7" ? "selected" : "" ?>>Julio</option>
                            <option value="8" <?= isset($data_iniciativa["mes"]) && $data_iniciativa["mes"] == "8" ? "selected" : "" ?>>Agosto</option>
                            <option value="9" <?= isset($data_iniciativa["mes"]) && $data_iniciativa["mes"] == "9" ? "selected" : "" ?>>Septiembre</option>
                            <option value="10" <?= isset($data_iniciativa["mes"]) && $data_iniciativa["mes"] == "10" ? "selected" : "" ?>>Octubre</option>
                            <option value="11" <?= isset($data_iniciativa["mes"]) && $data_iniciativa["mes"] == "11" ? "selected" : "" ?>>Noviembre</option>
                            <option value="12" <?= isset($data_iniciativa["mes"]) && $data_iniciativa["mes"] == "12" ? "selected" : "" ?>>Diciembre</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-2">
                        <label><strong>* Fecha de entrega</strong></label>
                        <input type="date" class="form-control" name="fecha_entrega" value="<?php echo $data_iniciativa["fecha_entrega"]; ?>" required>
                    </div>

                    <div class="col-md-3 mb-2">
                        <label><strong>* Meta</strong></label>
                        <input type="text" class="form-control" name="meta" value="<?= $data_iniciativa["meta"]; ?>" required>
                    </div>

                    <div class="col-md-3 mb-2">
                        <label><strong>* Tendencia</strong></label>
                        <select class="form-control" name="tendencia" required>
                            <option value="">Seleccione...</option>
                            <option value="1" <?= isset($data_iniciativa["tendencia"]) && $data_iniciativa["tendencia"] == 1 ? "selected" : "" ?>>Ascendente</option>
                            <option value="2" <?= isset($data_iniciativa["tendencia"]) && $data_iniciativa["tendencia"] == 2 ? "selected" : "" ?>>Descendente</option>
                        </select>
                    </div>

                    <div class="col-md-12 mb-2">
                        <label><strong>* Responsables</strong></label>
                        <select class="multiples_responsables form-control" style="width: 100%" name="id_responsables[]" id="id_responsables" multiple="multiple">
                            <?php foreach ($integrantes_asociados as $integrante): ?>
                                <option value="<?= $integrante["empleado"]["id"]; ?>" <?= in_array($integrante["empleado"]["id"], $responsables_seleccionados) ? "selected" : "" ?>>
                                    <?= $integrante["empleado"]["nombre"]; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-12 mt-2 mb-2">
                        <button type="submit" class="btn btn-success ">Guardar</button>
                    </div>

                </div>
            </form>

        </div>
    </div>

    <!-- TABLA DE RESULTADOS -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Responsables</th>
                            <th class="text-center">Estado de aprobación</th>
                            <th class="text-center">Fecha de asignación</th>
                            <th class="text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lista_iniciativas as $item): ?>

                            <?php
                            //dd($item);
                            $empleado_owner_foto = !empty($item["id_empleado"]["foto"]) ? $item["id_empleado"]["foto"] : "img_default.jpg"; //FOTO DEL EMPELADO OWNER
                            $responsables = explode(',', $item["responsables"]);
                            ?>

                            <tr>
                                <td>
                                    <?php for ($i = 0; $i < count($responsables); $i++): ?>
                                        <?php
                                        $id_responsable = $responsables[$i];
                                        $responsableData = $OkrsServicios->Empleado($id_responsable); //OBTENER DATOS DEL RESPONSABLE
                                        $foto_responsable = !empty($responsableData["foto"]) ? $responsableData["foto"] : "img_default.jpg"; //FOTO DEL RESPONSABLE
                                        ?>
                                        <img src="<?= 'https://goforagile.com/recursos/' . $foto_responsable; ?>" width="35" height="35" class="rounded-circle" title="<?= $responsableData["nombre"]; ?>">
                                    <?php endfor; ?>
                                </td>
                                <td class="text-center"><?= $item["aprobacion"] == '1' ? 'Aprobado' : 'Pendiente por aprobación'; ?></td>
                                <td class="text-center"><?= $item["fecha_entrega"]; ?></td>
                                <td style="width:100px;">
                                    <a href="?pg=okrs/okr/iniciativas&id_okr=<?= $_SESSION["id_okrs_edit"]; ?>&id_resultado=<?= $item["id_resultado"]; ?>&id_iniciativa=<?= $item["id"]; ?>">
                                        <button type="button" class="btn btn-outline-dark btn-sm ">
                                            <i class="bx bx-pencil" title="Editar Iniciativa"></i>
                                        </button>
                                    </a>
                                    <!-- <a href="?pg=okrs/okr/iniciativas&id_okr=<?= $_SESSION["id_okrs_edit"]; ?>&id_resultado=<?= $item["id_resultado"]; ?>&id_iniciativa=<?= $item["id"]; ?>">
                                        <button type="button" class="btn btn-outline-dark btn-sm">
                                            <i class="bx bx-trash" title="Eliminar Item"></i>
                                        </button>
                                    </a> -->
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
    $(document).ready(function() {
        $('.multiples_responsables').select2();
    });
</script>