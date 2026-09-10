<?php
include("../../app/connect.php");
$id_empresa = $_POST["id_empresa"];
$anio = $_POST["anio"];
$id_resultado = $_POST["id_resultado"];


$sentencia_resultado = "SELECT * FROM Okrs_Resultados WHERE id = '".$id_resultado."' ";
$query = mysqli_query($connect_okrs, $sentencia_resultado);
$data = mysqli_fetch_array($query);

$queryOkrData = mysqli_query($connect_okrs, "SELECT * FROM Okrs WHERE id = '".$data["id_okrs"]."' ");
$dataOkrData = mysqli_fetch_array($queryOkrData);

?>



<form action="" method="POST">
    <input type="hidden" name="id_resultado_duplicar" id="id_resultado_mover"> 
    <input type="hidden" name="duplicar_okrs" value="true">
    <div class="row">
        <div class="col-md-4">
            <label for="">Fecha de Inicio *</label>
            <input type="date" class="form-control" name="fecha_inicia_duplicar" value="<?php echo $data["fecha_inicia"]; ?>">
        </div>
                    <div class="col-md-4">
                        <label for="">Fecha de entrega *</label>
                        <input type="date" class="form-control" name=""  value="<?php echo $data["fecha_entrega"]; ?>">
                    </div>

                    <div class="col-md-4">
                        <label for="">Medición *</label>
                        <select class="form-control" name="medicion" required>
                            <option value="">Seleccione...</option>
                            <option value="1" <?= isset($data["medicion"]) && $data["medicion"] == 1 ? "selected" : "" ?>>Cantidad</option>
                            <option value="2" <?= isset($data["medicion"]) && $data["medicion"] == 2 ? "selected" : "" ?>>Horas</option>
                            <option value="3" <?= isset($data["medicion"]) && $data["medicion"] == 3 ? "selected" : "" ?>>Moneda</option>
                            <option value="4" <?= isset($data["medicion"]) && $data["medicion"] == 4 ? "selected" : "" ?>>Porcentaje</option>
                            <option value="5" <?= isset($data["medicion"]) && $data["medicion"] == 5 ? "selected" : "" ?>>Documento</option>
                            <option value="6" <?= isset($data["medicion"]) && $data["medicion"] == 6 ? "selected" : "" ?>>Hito</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="">Tendencia *</label>
                        <select class="form-control" name="tendencia" required>
                            <option value="">Seleccione...</option>
                            <option value="1" <?= isset($data["tendencia"]) && $data["tendencia"] == 1 ? "selected" : "" ?>>Ascendente</option>
                            <option value="2" <?= isset($data["tendencia"]) && $data["tendencia"] == 2 ? "selected" : "" ?>>Descendente</option>
                        </select>
                    </div>

                    

                    <div class="col-md-4">
                        <label for="">Meta *</label>
                        <input type="text" class="form-control" name="" value="<?php echo $data["meta"]; ?>">
                    </div>

                    

                    <div class="col-md-4">
                        <label for="">Periodo</label>
                        <select class="form-control" name="periodo" required>
                            <option value="">Seleccione...</option>
                            <option value="Q1" <?= isset($data["periodo"]) && $data["periodo"] == "Q1" ? "selected" : "" ?>>Q1</option>
                            <option value="Q2" <?= isset($data["periodo"]) && $data["periodo"] == "Q2" ? "selected" : "" ?>>Q2</option>
                            <option value="Q3" <?= isset($data["periodo"]) && $data["periodo"] == "Q3" ? "selected" : "" ?>>Q3</option>
                            <option value="Q4" <?= isset($data["periodo"]) && $data["periodo"] == "Q4" ? "selected" : "" ?>>Q4</option>
                            <option value="Anual" <?= isset($data["periodo"]) && $data["periodo"] == "Anual" ? "selected" : "" ?>>Anual</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="">Responsables</label>
                        <input type="text" class="form-control" name="">
                    </div>

                    <div class="col-md-12">
                        <label for="">Descripción</label>
                        <textarea rows="3" name="" id="" class="form-control"><?php echo $data["descripcion"]; ?></textarea>
                    </div>

                    <div class="col-md-12">
                        <label for="">Desea agregar tambien las Iniciativas relacionadas con este Resultados Clave (KR)</label>
                        <select name="" id="" class="form-control">
                            <option value="">Seleccione...</option>
                            <option value="Si">Si</option>
                            <option value="No">No</option>
                        </select>
                    </div>

                    <div class="col-md-12">
                        <button class="btn btn-danger mt-3" type="submit">
                            Duplicar
                        </button>
                    </div>
    </div>
</form>

