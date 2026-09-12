<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form action="" method="post" id="formulario_filtro">
                    <div class="row">
                        <div class="col-md-1">
                            <select class="form-control form-control-sm" name="anio_fill" onChange="Filtrar()">
                                <?php
                                foreach ($Array_Anio as $periodo) {
                                    if ($_SESSION["anio_fill"] ==  $periodo[0]) {
                                        echo '<option value="' . $periodo[0] . '" selected>' . $periodo[1] . '</option>';
                                    } else {
                                        echo '<option value="' . $periodo[0] . '">' . $periodo[1] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-11">
                            <select class="form-control form-control-sm" name="leccion_fill" onChange="Filtrar()">
                                <option value="">Filtrar por Lección Aprendida...</option>
                                <?php
                                foreach ($queryLA as $dataL) {
                                    if ($_SESSION["leccion_fill"] ==  $dataL["id"]) {
                                        echo '<option value="' . $dataL["id"] . '" selected>' . $dataL["descripcion"] . '</option>';
                                    } else {
                                        echo '<option value="' . $dataL["id"] . '">' . $dataL["descripcion"] . '</option>';
                                    }
                                }

                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <select class="form-control form-control-sm" name="area_lA" onChange="Filtrar()">
                                <option value="">Filtrar por Area...</option>
                                <?php

                                while ($dataAreaLecciones = mysqli_fetch_array($queryAreaLecciones)) {
                                    $queryAreasL = mysqli_query($connect_admin, "SELECT * FROM Areas WHERE id_empresa = '" . $dtEmpleado["id_empresa"] . "' AND id = " . $dataAreaLecciones["area"] . "");
                                    $dataAreas = mysqli_fetch_array($queryAreasL);
                                    if ($_SESSION["area_lA"] ==  $dataAreaLecciones["area"]) {
                                        echo '<option value="' . $dataAreas["id"] . '" selected>' . $dataAreas["nombre"] . '</option>';
                                    } else {
                                        echo '<option value="' . $dataAreas["id"] . '">' . $dataAreas["nombre"] . '</option>';
                                    }
                                }

                                ?>
                            </select>
                        </div>
                        
                        
                        <div class="col-md-2">
                            <label for="" style="color: black;">Seleccione el peridodo</label>
                        </div>
                        <div class="col-md-1">
                            <input type="checkbox" name="Q1" id="Q1" <?php echo $check1; ?> onChange="Filtrar()" class="form-check-input"> Q1
                        </div>
                        <div class="col-md-1">
                            <input type="checkbox" name="Q2" id="Q2" <?php echo $check2; ?> onChange="Filtrar()" class="form-check-input"> Q2
                        </div>
                        <div class="col-md-1">
                            <input type="checkbox" name="Q3" id="Q3" <?php echo $check3; ?> onChange="Filtrar()" class="form-check-input"> Q3
                        </div>
                        <div class="col-md-1">
                            <input type="checkbox" name="Q4" id="Q4" <?php echo $check4; ?> onChange="Filtrar()" class="form-check-input"> Q4
                        </div>
                        <div class="col-md-1">
                            <input type="checkbox" name="Anual" id="Anual" <?php echo $check5; ?> onChange="Filtrar()" class="form-check-input"> Anual
                        </div>

                    </div>
                </form>
            </div>
        </div>

    </div>
</div>