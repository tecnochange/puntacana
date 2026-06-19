<?php
include("../../app/connect.php");
include("../../app/arrays.php");
//include("../../app/models/arrays.php");

$hoy = date("Y-m-d H:i:s");

$id_okrs = $_POST["id_okrs"];
$id_iniciativa = $_POST["id_iniciativa"];
$id_iniciativa = $_POST["id_resultado"];
$id_user = $_POST["id_empleado"];
$id_empresa = $_POST["id_empresa"];
$descripcion = $_POST["descripcion"];

/* $queryEmpresa = mysqli_query($connect_admin, "SELECT * FROM Empresas WHERE id = $id_empresa");
$dataEmpresa = mysqli_fetch_array($queryEmpresa); */

/* $query = mysqli_query($connect_kpis, "SELECT * FROM Okrs WHERE id = '" . $id . "' ");
$data = mysqli_fetch_array($query); */

/* echo json_encode([
    "id" => $id,
    "id_empresa" => $id_empresa,
    "id_user" => $id_user
]); */
?>

<div class="row">

    <input type="hidden" name="id_registro" value="<?php echo $data["id"]; ?>">

    <div class="col-md-12" style="margin-bottom: 15px">
        <div>Comentarios para:<br>
            <b>
                <h5><?php echo $descripcion; ?></h5>
            </b>
        </div>

        <form action="" method="post">
            <div class="form-group">
                <div class="row">
                    <input type="hidden" name="guardar_comentario_iniciativa" value="true">
                    <input type="hidden" name="id_iniciativa" value="<?php echo $id; ?>">
                    <div class="col-md-12">
                        <textarea id="comentario_iniciativa" name="comentario" class="form-control" placeholder="Ingrese su comentario..." rows="3" required></textarea>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12 mt-4" align="right">
                        <button type="submit" class="btn btn-success btn-sm">Guardar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {

        $('#comentario_iniciativa').summernote({
            tabsize: 2,
            height: 150,
            minHeight: null,
            maxHeight: null,
            focus: true,

            toolbar: [
                ['para', ['ul', 'ol']]
            ],
            styleTags: [
                "p",
                "code",
                "blockquote",
                "pre",
                "h1",
                "h2",
                "h3",
                "h4",
                "h5",
                "h6",
            ],
        });
    });
</script>