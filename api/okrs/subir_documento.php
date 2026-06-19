<?php
include("../../app/connect.php");
include("../../app/arrays.php");

$id_empresa = $_POST["id_empresa"];
$id_okrs = $_POST["id_okrs"];
$id_resultado = $_POST["id_resultado"];
$id_iniciativa = $_POST["id_iniciativa"];
$id_empleado = $_POST["id_empleado"];
$hoy = date("Y-m-d H:i:s");

$query = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Iniciativas WHERE id = '$id_iniciativa' ");
$data = mysqli_fetch_assoc($query);
?>
<style>
    .form-icon {
        color: #fff;
        font-size: 3rem;
    }

    .drop-container {
        background-color: #efefef;
        position: relative;
        display: flex;
        gap: 10px;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 10px;
        margin-top: 1rem;
        margin-bottom: 0.4rem;
        border-radius: 3px;
        border: 1px dashed #fff;
        color: black;
        cursor: pointer;
        transition: background .2s ease-in-out, border .2s ease-in-out;
    }

    .drop-container:hover {
        background: #efefef;
    }

    .drop-title {
        font-size: 20px;
        text-align: center;
        transition: color .2s ease-in-out;
    }
</style>
<div class="row">

    <input type="hidden" name="id_registro" value="<?php echo $data["id"]; ?>">

    <div class="col-md-12" style="margin-bottom: 15px">
        <div>Documento para:<br>
            <b>
                <h5><?php echo $data["descripcion"]; ?></h5>
            </b>
        </div>

        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <div class="row">
                    <input type="hidden" name="id_empresa" value="<?php echo $_POST["id_empresa"]; ?>">
                    <input type="hidden" name="id_okrs" value="<?php echo $id_okrs; ?>">
                    <input type="hidden" name="id_resultado" value="<?php echo $id_resultado; ?>">
                    <input type="hidden" name="id_iniciativa" value="<?php echo $id_iniciativa; ?>">
                    <input type="hidden" name="id_usuario" value="<?php echo $id_empleado; ?>">
                    <input type="hidden" name="cargar_documento_iniciativa" value="true">
                    
                    <div class="col-md-12">
                        <textarea id="comentario_doc" name="comentario" class="form-control" placeholder="Ingrese su comentario..." rows="3" required></textarea>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label for="file-input" class="drop-container">
                            <span class="drop-title">Arrastre el archivo aquí para cargarlo</span>
                            o
                            <input type="file" class="form-control" name="archivo_iniciativa" id="archivo_iniciativa" accept=".pdf, .docx,.doc, .xlsx, .xls, .ppt, .pptx" required />
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group mt-4">
                <div class="row">
                    <div class="col-md-12" align="right">
                        <button type="submit" class="btn btn-success btn-sm">Guardar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#comentario_doc').summernote({
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
    const uploadFieldIniciativa = document.getElementById("archivo_iniciativa");

    uploadFieldIniciativa.onchange = function() {
        if (this.files[0].size / (1024 * 1024) > 10) {
            alert("Archivo demasiado grande, el archivo no debe superar la s 10mb de tamaño");
            this.value = "";
        }
    };
</script>