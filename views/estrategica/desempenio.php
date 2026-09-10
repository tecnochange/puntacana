<script>
$(document).ready(function() {
    $('#menuEstrategia').collapse();
    $('#bt_estrategia_configuracion').addClass('active');
});
</script>


<?php

$Array_Anio_desempenio = [2025, 2026, 2027, 2028, 2029, 2030];

?>

<div class="container">

    <ul class="nav nav-tabs justify-content-center">
        <li class="nav-item">
            <a class="nav-link" href="?pg=estrategica/configurar">General</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="?pg=estrategica/desempenio">Desempeño</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="?pg=estrategica/competencias">Competencias</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="?pg=estrategica/kpis">Kpis</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="?pg=estrategica/okrs">Okrs</a>
        </li>
    </ul>



    <div class="card mb-3">
            <div class="card-body">
                <form action="" method="post">
                    <input type="hidden" name="guardar_desempenio" value="true">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="">Seleccione Periodo:</label>
                                <select name="periodo" id="periodo" class="form-control mb-3" required>
                                    <option value="">Selecione...</option>
                                    <?php
                                    foreach ($Array_Anio_desempenio as $year) {
                                        if (!in_array($year, $arrayDesempenio)) {
                                            $periodoSelect = ((int)$year - 1) . "-" . (int)$year;
                                            echo '<option value="' . $year . '">' . $periodoSelect . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <table border="1" id="nivel_jerarquico" class="display table" style="width:100%;font-size: 1rem;">
                        <thead>
                            <tr>
                                <th>Nivel Jerárquico</th>
                                <th>OKR de Equipo</th>
                                <th>Competencias</th>
                                <th>KPIs
                                <th>Desempeño</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $queryNJ = mysqli_query($connect_admin, "SELECT * FROM Nivel_Jerarquico WHERE id_empresa = '" . $user_log["id_empresa"] . "' AND estado = 1");
                            while ($dataNJ1 = mysqli_fetch_array($queryNJ)) { ?>
                                <tr>
                                    <td><?php echo $dataNJ1["nombre"]; ?></td>
                                    <td><input type="text" name="mod_okr_<?php echo $dataNJ1["id"]; ?>" id="mod_okr_<?php echo $dataNJ1["id"]; ?>" class="form-control" onkeyup="return NumerosDecimales(this)" oninput="calcularDesempenio_<?php echo $dataNJ1["id"]; ?>()"></td>
                                    <td><input type="text" name="mod_competencias_<?php echo $dataNJ1["id"]; ?>" id="mod_competencias_<?php echo $dataNJ1["id"]; ?>" class="form-control" onkeyup="return NumerosDecimales(this)" oninput="calcularDesempenio_<?php echo $dataNJ1["id"]; ?>()"></td>
                                    <td><input type="text" name="mod_kpi_<?php echo $dataNJ1["id"]; ?>" id="mod_kpi_<?php echo $dataNJ1["id"]; ?>" class="form-control" onkeyup="return NumerosDecimales(this)" oninput="calcularDesempenio_<?php echo $dataNJ1["id"]; ?>()"></td>
                                    <td><input type="text" name="desempenio_<?php echo $dataNJ1["id"]; ?>" id="desempenio_<?php echo $dataNJ1["id"]; ?>" class="form-control" readonly></td>
                                </tr>
                                <script>
                                    function calcularDesempenio_<?php echo $dataNJ1["id"]; ?>() {
                                        var valor1 = parseFloat(document.getElementById("mod_okr_<?php echo $dataNJ1["id"]; ?>").value) || 0;
                                        var valor2 = parseFloat(document.getElementById("mod_competencias_<?php echo $dataNJ1["id"]; ?>").value) || 0;
                                        var valor3 = parseFloat(document.getElementById("mod_kpi_<?php echo $dataNJ1["id"]; ?>").value) || 0;

                                        var suma = valor1 + valor2 + valor3;

                                        document.getElementById("desempenio_<?php echo $dataNJ1["id"]; ?>").value = suma.toFixed(2);
                                    }
                                </script>
                            <?php } ?>
                        </tbody>
                    </table>
                    <br>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12" style="text-align: end;">
                                <button class="btn btn-success" type="submit">Guardar Ponderación</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
    </div>




    <?php
    $queryDesempenio = mysqli_query($connect_admin, "SELECT DISTINCT(anio) AS anio FROM Ponderar_Desempenio WHERE id_empresa = '" . $user_log["id_empresa"] . "' AND estado = 1 ");
    while ($dataDesempenio = mysqli_fetch_array($queryDesempenio)) {
        $periodo = ((int)$dataDesempenio["anio"] - 1) . " - " . $dataDesempenio["anio"];
    ?>

    <div class="card mb-3">
        <div class="card-header" role="tab" id="heading<?php echo $dataDesempenio["anio"]; ?>">
            <div class="row" style="align-items: center;">
                <div class="col-md-10">
                    <h5>Periodo <?php echo $periodo; ?></h5>
                </div>
                <div class="col-md-2" style="text-align: end;">
                                <a class="collapsed" data-toggle="collapse" href="#collapseD<?php echo $dataDesempenio["anio"]; ?>" aria-expanded="false" aria-controls="collapseD<?php echo $dataDesempenio["anio"]; ?>" id="datosOkrs_1" style="color: black !important;">
                                </a>
                </div>
            </div>
        </div>

        <div class="card-body">
           
            <table border="1" id="desempenio_<?php echo $dataDesempenio["anio"]; ?>" class="display table" style="width:100%;font-size: 1rem;">
                            <thead>
                                <tr>
                                    <th>Nivel Jerárquico</th>
                                    <th>OKR de Equipo</th>
                                    <th>Competencias</th>
                                    <?php if ($dataDesempenio["anio"] != 2024) { ?>
                                        <th>KPIs</th>
                                    <?php } ?>
                                    <th>Desempeño</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                $queryDesempenio1 = mysqli_query($connect_admin, "SELECT * FROM Ponderar_Desempenio WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND anio = '" . $dataDesempenio["anio"] . "' AND estado = 1 ");
                                while ($dataDesempenio1 = mysqli_fetch_array($queryDesempenio1)) {
                                    if ($dataDesempenio1["anio"] != 2024) {
                                        $readonly = '';
                                    } else {
                                        $readonly = 'readonly';
                                    }
                                    
                                    $queryNJ = mysqli_query($connect_admin, "SELECT * FROM Nivel_Jerarquico WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND id = '" . $dataDesempenio1["nivel"] . "' AND estado = 1 ");
                                    $dataNJ = mysqli_fetch_array($queryNJ);
                                    $total = $dataDesempenio1["mod_okrs"] + $dataDesempenio1["mod_competencias"] + $dataDesempenio1["mod_kpis"];
                                ?>

                                    <tr>
                                        <td><?php echo $dataNJ["nombre"]; ?></td>
                                        <td><input type="text" name="mod_okr_upd_<?php echo $dataDesempenio1["id"]; ?>" id="mod_okr_upd_<?php echo $dataDesempenio1["id"]; ?>" class="form-control" onkeyup="return NumerosDecimales(this)" oninput="calcularDesempenio_upd_<?php echo $dataDesempenio1["id"]; ?>()" value="<?php echo $dataDesempenio1["mod_okrs"]; ?>" onChange="actualizarPonderacion(this.value,<?php echo $dataDesempenio1["id"]; ?>,3)" <?php echo $readonly; ?>></td>
                                        <td><input type="text" name="mod_competencias_upd_<?php echo $dataDesempenio1["id"]; ?>" id="mod_competencias_upd_<?php echo $dataDesempenio1["id"]; ?>" class="form-control" onkeyup="return NumerosDecimales(this)" oninput="calcularDesempenio_upd_<?php echo $dataDesempenio1["id"]; ?>()" value="<?php echo $dataDesempenio1["mod_competencias"]; ?>" onChange="actualizarPonderacion(this.value,<?php echo $dataDesempenio1["id"]; ?>,1)" <?php echo $readonly; ?>></td>
                                        <?php if ($dataDesempenio1["anio"] != 2024) { ?>
                                            <td><input type="text" name="mod_kpi_upd_<?php echo $dataDesempenio1["id"]; ?>" id="mod_kpi_upd_<?php echo $dataDesempenio1["id"]; ?>" class="form-control" onkeyup="return NumerosDecimales(this)" oninput="calcularDesempenio_upd_<?php echo $dataDesempenio1["id"]; ?>()" value="<?php echo $dataDesempenio1["mod_kpis"]; ?>" onChange="actualizarPonderacion(this.value,<?php echo $dataDesempenio1["id"]; ?>,2)"></td>
                                        <?php } ?>
                                        <td><input type="text" name="desempenio_upd_<?php echo $dataDesempenio1["id"]; ?>" id="desempenio_upd_<?php echo $dataDesempenio1["id"]; ?>" class="form-control" readonly value="<?php echo $total; ?>"></td>
                                        <script>
                                            function calcularDesempenio_upd_<?php echo $dataDesempenio1["id"]; ?>() {
                                                var valor1 = parseFloat(document.getElementById("mod_okr_upd_<?php echo $dataDesempenio1["id"]; ?>").value) || 0;
                                                var valor2 = parseFloat(document.getElementById("mod_competencias_upd_<?php echo $dataDesempenio1["id"]; ?>").value) || 0;
                                                var valor3 = parseFloat(document.getElementById("mod_kpi_upd_<?php echo $dataDesempenio1["id"]; ?>").value) || 0;

                                                var suma = valor1 + valor2 + valor3;

                                                console.log('Valor 1:', valor1, 'Valor 2:', valor2, 'Valor 3:', valor3, 'Suma:', suma);

                                                document.getElementById("desempenio_upd_<?php echo $dataDesempenio1["id"]; ?>").value = suma.toFixed(2);
                                            }
                                        </script>
                                    </tr>

                                <?php } ?>
                            </tbody>
            </table>
            <script type="text/javascript">
                            $(document).ready(function() {
                                $('#desempenio_<?php echo $dataDesempenio["anio"]; ?>').DataTable({
                                    columnDefs: [{
                                            responsivePriority: 1,
                                            targets: 0
                                        },
                                        {
                                            responsivePriority: 2,
                                            targets: -1
                                        }
                                    ],
                                    order: [
                                        [0, 'asc']
                                    ],
                                    responsive: true,
                                    pageLength: 50,
                                    language: {
                                        processing: "Procesando...",
                                        search: "Buscar:",
                                        lengthMenu: "Mostrar _MENU_ registros.",
                                        info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                                        infoEmpty: "Mostrando registros del 0 al 0 de 0 registros",
                                        infoFiltered: "(filtrado de un total de _MAX_ registros)",
                                        infoPostFix: "",
                                        loadingRecords: "Cargando...",
                                        zeroRecords: "No se encontraron resultados",
                                        emptyTable: "Ningún dato disponible en esta tabla",
                                        row: "Registro",
                                        export: "Exportar",
                                        paginate: {
                                            first: "Primero",
                                            previous: "Anterior",
                                            next: "Siguiente",
                                            last: "Ultimo"
                                        },
                                        aria: {
                                            sortAscending: ": Activar para ordenar la columna de manera ascendente",
                                            sortDescending: ": Activar para ordenar la columna de manera descendente"
                                        },
                                        select: {
                                            row: "registro",
                                            selected: "seleccionado"
                                        }
                                    }

                                });

                            });
            </script>

            <div class="text-end">
                <button class="btn btn-success" type="button" onclick="GuardarPonderacion(<?php echo $_SESSION["id_empresa"]; ?>)">Actualizar Ponderación</button>
            </div>
                       
                        
        </div>
    </div>

    <?php }  ?>


</div>

