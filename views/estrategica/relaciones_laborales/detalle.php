<script>
$(document).ready(function() {
    $('#menuEstrategia').collapse();
    $('#bt_estrategia_relaciones_laborales').addClass('active');
});
</script>

<?php
$id = $_GET["id"];
$hoy = date("Y-m-d H:i:s");
$query = mysqli_query($connect_admin, "SELECT * FROM Relaciones_Laborales WHERE id = $id");
$data = mysqli_fetch_array($query);
$queryEmpleado = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id = '" . $data["id_empleado"] . "'");
$dataEmpleado = mysqli_fetch_array($queryEmpleado);

if ($_POST["guardar_formulario"] != "") {

    if ($_POST["mod_okrs"] == "" && $_POST["mod_kpis"] == "" && $_POST["mod_competencias"] == "") {
        echo '<script> alert("Debe seleccionar por lo menos un mod para asignar al usuario");</script>';
    } 
    else {

        if ($_POST["id_registro"] != "") {

            mysqli_query($connect_admin, "UPDATE Relaciones_Laborales SET id_empleado = '" . $_POST["colaborador"] . "', id_vp = '" . $_POST["vicepresidencia_rl"] . "', id_area = '" . $_POST["area_rl"] . "', 
                mod_okrs = '" . $_POST["mod_okrs"] . "', 
                mod_kpis = '" . $_POST["mod_kpis"] . "',
                mod_competencias = '" . $_POST["mod_competencias"] . "', 
                mod_desempenio = '" . $_POST["mod_desempenio"] . "', 
                updated_at = '$hoy'
                WHERE id = '" . $_POST["id_registro"] . "'  ");
            echo '<script> window.location = "?pg=estrategica/relaciones_laborales";</script>';
        } 
        else {

            if ($_POST["area_rl"] == "") {
                // echo "1";
                $queryRL = mysqli_query($connect_admin, "SELECT * FROM Relaciones_Laborales WHERE id_empleado = " . $_POST["colaborador"] . " AND id_vp = '" . $_POST["vicepresidencia_rl"] . "' AND id_area = '' AND estado = 1");
                $dataRL = mysqli_fetch_array($queryRL);
                if (mysqli_num_rows($queryRL) > 0) {
                    echo '<script> alert("El usuario ya tiene asignada esta vicepresidencia");</script>';
                } else {
                    mysqli_query($connect_admin, "INSERT INTO Relaciones_Laborales (id_empresa, id_empleado, id_vp, id_area, mod_okrs , 
                    mod_kpis,
                    mod_competencias, 
                    mod_desempenio, 
                    estado,
                    created_at
                    ) 
                    VALUES 
                    ('" . $_SESSION["id_empresa"] . "', '" . $_POST["colaborador"] . "', '" . $_POST["vicepresidencia_rl"] . "', '" . $_POST["area_rl"] . "',
                    '" . $_POST["mod_okrs"] . "', 
                    '" . $_POST["mod_kpis"] . "',
                    '" . $_POST["mod_competencias"] . "', 
                    '" . $_POST["mod_desempenio"] . "', 
                    '1', '" . $hoy . "' ) ");
                    echo '<script> window.location = "?pg=estrategica/relaciones_laborales";</script>';
                }
            } else {
                // echo "2";
                $queryRL = mysqli_query($connect_admin, "SELECT * FROM Relaciones_Laborales WHERE id_empleado = " . $_POST["colaborador"] . " AND id_vp = '" . $_POST["vicepresidencia_rl"] . "' AND id_area = '" . $_POST["area_rl"] . "' AND estado = 1");
                $dataRL = mysqli_fetch_array($queryRL);
                if (mysqli_num_rows($queryRL) > 0) {
                    echo '<script> alert("El usuario ya tiene asignada esta área");</script>';
                } else {
                    mysqli_query($connect_admin, "INSERT INTO Relaciones_Laborales (id_empresa, id_empleado, id_vp, id_area, mod_okrs , 
                    mod_kpis,
                    mod_competencias, 
                    mod_desempenio, 
                    estado,
                    created_at
                    ) 
                    VALUES 
                    ('" . $_SESSION["id_empresa"] . "', '" . $_POST["colaborador"] . "', '" . $_POST["vicepresidencia_rl"] . "', '" . $_POST["area_rl"] . "',
                    '" . $_POST["mod_okrs"] . "', 
                    '" . $_POST["mod_kpis"] . "',
                    '" . $_POST["mod_competencias"] . "', 
                    '" . $_POST["mod_desempenio"] . "', 
                    '1', '" . $hoy . "' ) ");
                    echo '<script> window.location = "?pg=estrategica/relaciones_laborales";</script>';
                }
            }
        }
    }

    //para evitar reinsersion
}
?>

<div class="container">

    <a href="<?php echo $url; ?>?pg=estrategica/relaciones_laborales" >
        <button class="btn btn-success mb-3"> << Volver</button>
    </a>

    <div class="card">
        <div class="card-header">
            <h3>Ficha Relaciones Laborales</h3>
        </div>

        <form action="" method="POST">
        <input type="hidden" name="guardar_formulario" value="true">
        <input type="hidden" name="id_registro" value="<?php echo $id; ?>">

        <input type="hidden" name="id_empresa" id="id_empresa" value="<?php echo $_SESSION["id_empresa"]; ?>">
        <input type="hidden" name="id_area_rl" id="id_area_rl" value="<?php echo $data["id_area"]; ?>">
        <input type="hidden" name="id_vp_rl" id="id_vp_rl" value="<?php echo $data["id_vp"]; ?>">
        <div class="card-body">

            <div class="row">
                <div class="col-md-12">
                    <p style="font-size: 1rem !important;">
                        Recuerde asignar una unica vez la vicepresidencia o área al usuario. Un usuario no puede tener asignado mas de una vez la misma vicepresidencia (sin área) o mas de una vez una misma área.
                    </p>
                </div>

                <div class="col-md-12">
                    <h5>Colaborador Asignado</h5>
                </div>

                 <div class="col-md-3">
                    <lable>Vicepresidencia / Gerencia / Dirección *</lable>
                    <select class="multiples_responsables form-control" style="width: 100%" name="vicepresidencia" id="vicepresidencia" onchange="select_vicepresidencia(this);">
                                        <option value="">Seleccione...</option>
                                        <?php
                                        $queryvicepresidencias = mysqli_query($connect_admin, "SELECT * FROM Vicepresidencia WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' ORDER BY nombre ASC ");
                                        while ($datavicepresidencia = mysqli_fetch_array($queryvicepresidencias)) {
                                            if ($datavicepresidencia["id"] == $dataEmpleado["unidad_corporativa"]) {
                                                echo '
                                  <option value="' . $datavicepresidencia["id"] . '" selected>' . $datavicepresidencia["nombre"] . '</option>
                                  ';
                                            } else {
                                                echo '
                                  <option value="' . $datavicepresidencia["id"] . '">' . $datavicepresidencia["nombre"] . '</option>
                                  ';
                                            }
                                        }
                                        ?>
                        </select>
                </div>
                <div class="col-md-3">
                        <lable>Área / Equipo*</lable>
                        <select class="multiples_responsables form-control" style="width: 100%" name="area" id="area" required onchange="select_area(this);">
                                        <option value="">Seleccione...</option>
                                        <?php
                                        $queryAreas = mysqli_query($connect_admin, "SELECT * FROM Areas WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND estado = 1 ORDER BY nombre ASC ");
                                        while ($dataArea = mysqli_fetch_array($queryAreas)) {
                                            if ($dataArea["id"] == $dataEmpleado["area"]) {
                                                echo '
                                  <option value="' . $dataArea["id"] . '" selected>' . $dataArea["nombre"] . '</option>
                                  ';
                                            } else {
                                                echo '
                                  <option value="' . $dataArea["id"] . '">' . $dataArea["nombre"] . '</option>
                                  ';
                                            }
                                        }
                                        ?>
                        </select>
                </div>
                <div class="col-md-3">
                        <label>Colaborador</label>
                        <select class="multiples_responsables form-control" style="width: 100%" name="colaborador" id="colaborador" required>
                                        <option value="">Seleccione...</option>
                                        <?php
                                        $queryAreas = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND estado = 1 ORDER BY nombre ASC ");
                                        while ($dataArea = mysqli_fetch_array($queryAreas)) {
                                            if ($dataArea["id"] == $dataEmpleado["id"]) {
                                                echo '
                                  <option value="' . $dataArea["id"] . '" selected>' . $dataArea["nombre"] . '</option>
                                  ';
                                            } else {
                                                echo '
                                  <option value="' . $dataArea["id"] . '">' . $dataArea["nombre"] . '</option>
                                  ';
                                            }
                                        }
                                        ?>

                        </select>
                </div>

                <div class="col-md-12">
                    <h5>Vicepresidencia / Gerencia / Dirección Asignada</h5>
                </div>

                <div class="col-md-3">
                                    <lable>Vicepresidencia / Gerencia / Dirección *</lable>
                                    <select class="multiples_responsables form-control" style="width: 100%" name="vicepresidencia_rl" id="vicepresidencia_rl" onchange="select_vicepresidencia_rl(this);">
                                        <option value="">Seleccione...</option>
                                        <?php
                                        $queryvicepresidencias = mysqli_query($connect_admin, "SELECT * FROM Vicepresidencia WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' ORDER BY nombre ASC ");
                                        while ($datavicepresidencia = mysqli_fetch_array($queryvicepresidencias)) {
                                            if ($datavicepresidencia["id"] == $data["id_vp"]) {
                                                echo '
                                  <option value="' . $datavicepresidencia["id"] . '" selected>' . $datavicepresidencia["nombre"] . '</option>
                                  ';
                                            } else {
                                                echo '
                                  <option value="' . $datavicepresidencia["id"] . '">' . $datavicepresidencia["nombre"] . '</option>
                                  ';
                                            }
                                        }
                                        ?>
                                    </select>
                </div>
                <div class="col-md-3">
                                    <lable>Área / Equipo</lable>
                                    <select class="multiples_responsables form-control" style="width: 100%" name="area_rl" id="area_rl">
                                        <option value="">Seleccione...</option>
                                        <?php
                                        $queryAreas = mysqli_query($connect_admin, "SELECT * FROM Areas WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND estado = 1 ORDER BY nombre ASC ");
                                        while ($dataArea = mysqli_fetch_array($queryAreas)) {
                                            if ($dataArea["id"] == $data["id_area"]) {
                                                echo '
                                  <option value="' . $dataArea["id"] . '" selected>' . $dataArea["nombre"] . '</option>
                                  ';
                                            } else {
                                                echo '
                                  <option value="' . $dataArea["id"] . '">' . $dataArea["nombre"] . '</option>
                                  ';
                                            }
                                        }
                                        ?>
                                    </select>
                </div>

                <div class="col-md-12"></div>

                <div class="col-md-2" align="center">
                                    <label class="ti_label">OKRS</label><br>
                                    <?php
                                    if ($data["mod_okrs"] == "on") {
                                        echo '<input type="checkbox" name="mod_okrs" style="width: 20px; height: 20px" checked >';
                                    } else {
                                        echo '<input type="checkbox" name="mod_okrs" style="width: 20px; height: 20px" >';
                                    }
                                    ?>
                </div>

                <div class="col-md-2" align="center">
                                    <label class="ti_label">KPIS</label><br>
                                    <?php
                                    if ($data["mod_kpis"] == "on") {
                                        echo '<input type="checkbox" name="mod_kpis" style="width: 20px; height: 20px" checked >';
                                    } else {
                                        echo '<input type="checkbox" name="mod_kpis" style="width: 20px; height: 20px" >';
                                    }
                                    ?>
                </div>

                <div class="col-md-2" align="center">
                                    <label class="ti_label">Competencias</label><br>
                                    <?php
                                    if ($data["mod_competencias"] == "on") {
                                        echo '<input type="checkbox" name="mod_competencias" style="width: 20px; height: 20px" checked >';
                                    } else {
                                        echo '<input type="checkbox" name="mod_competencias" style="width: 20px; height: 20px" >';
                                    }
                                    ?>
                </div>

                <div class="col-md-2" align="center">
                                    <label class="ti_label">Desempeño</label><br>
                                    <?php
                                    if ($data["mod_desempenio"] == "on") {
                                        echo '<input type="checkbox" name="mod_desempenio" style="width: 20px; height: 20px" checked >';
                                    } else {
                                        echo '<input type="checkbox" name="mod_desempenio" style="width: 20px; height: 20px" >';
                                    }
                                    ?>
                </div>

                <div class="col-md-12" style="text-align: end;">
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>

            </div>

        </div>
        </form>
    </div>
</div>










<script>
    var api = '<?php echo $url; ?>/api/administrar/';

    function select_vicepresidencia() {

        $("#vicepresidencia option:selected").each(function() {
            var empresa = $("#id_empresa").val();
            var area = $("#id_area").val();
            id = $(this).val();
            // console.log(id);
            $.post(api + "cargar_vicepresidencias.php", {
                id: id,
                id_empresa: empresa,
                area: area
            }, function(data) {
                $("#area").html(data);
                var foption = $('#area option:first');
                var soptions = $('#area option:not(:first)').sort(function(a, b) {
                    return a.text == b.text ? 0 : a.text < b.text ? -1 : 1
                });
                $('#area').html(soptions).prepend(foption);
            });
        });

    }

    function select_area() {
        $("#area option:selected").each(function() {
            var empresa = $("#id_empresa").val();
            var vicepresidencia = $("#vicepresidencia").val();
            var area = $("#id_area_kpi").val();
            var idVicepresidencia = $("#id_vicepresidencia_kpi").val();
            var idUnidadOrg = $("#id_UnidadOrg").val();
            id = $(this).val();
            // console.log(id);
            $.post(api + "colaboradores_area.php", {
                id: id,
                id_empresa: empresa

            }, function(data) {
                if (id != '') {
                    // $('#unidad_organizativa').css('display', 'block');
                    $("#colaborador").html(data);
                    var foption = $('#colaborador option:first');
                    var soptions = $('#colaborador option:not(:first)').sort(function(a, b) {
                        return a.text == b.text ? 0 : a.text < b.text ? -1 : 1
                    });
                    $('#colaborador').html(soptions).prepend(foption);
                } else {
                    $("#colaborador").html('');
                }

            });
        });

    }

    function select_vicepresidencia_rl() {

        $("#vicepresidencia_rl option:selected").each(function() {
            var empresa = $("#id_empresa").val();
            var area = $("#id_area_rl").val();
            id = $(this).val();
            // console.log(id);
            $.post(api + "cargar_vicepresidencias.php", {
                id: id,
                id_empresa: empresa,
                area: area
            }, function(data) {
                $("#area_rl").html(data);
                var foption = $('#area_rl option:first');
                var soptions = $('#area_rl option:not(:first)').sort(function(a, b) {
                    return a.text == b.text ? 0 : a.text < b.text ? -1 : 1
                });
                $('#area_rl').html(soptions).prepend(foption);
            });
        });

    }

    function select_area_rl() {
        $("#area option:selected").each(function() {
            var empresa = $("#id_empresa").val();
            var vicepresidencia = $("#vicepresidencia_rl").val();
            var area = $("#id_area_rl").val();
            var idVicepresidencia = $("#id_vicepresidencia_rl").val();

            id = $(this).val();
            // console.log(id);
            $.post(api + "colaboradores_area.php", {
                id: id,
                id_empresa: empresa

            }, function(data) {
                if (id != '') {
                    // $('#unidad_organizativa').css('display', 'block');
                    $("#colaborador").html(data);
                    var foption = $('#colaborador option:first');
                    var soptions = $('#colaborador option:not(:first)').sort(function(a, b) {
                        return a.text == b.text ? 0 : a.text < b.text ? -1 : 1
                    });
                    $('#colaborador').html(soptions).prepend(foption);
                } else {
                    $("#colaborador").html('');
                }

            });
        });

    }

    var activar = false;

    function EliminarRelacion(id, id_empresa) {
        if (activar == false) {
            $("#modal_general").modal("show");
            $("#modal_body").html('Está a punto de eliminar un colaborador. ESTA ACCIÓN ES IRREVERSIBLE. se perderán los datos. ¿está seguro?<br><br>');
            $("#modal_body").append('<button type="button" class="btn btn-danger btn-sm" onclick="activar= true; EliminarRelacion(' + id + ',' + id_empresa + ')"> Eliminar </button>');

            $("#modal_okr").modal("hide");
        } else {
            jQuery.ajax({
                    url: api + "eliminar_relacion.php",
                    type: 'post',
                    data: {
                        id: id,
                        id_empresa: id_empresa,
                        url: "?pg=estrategica/relaciones_laborales"
                    },
                }).done(function(resp) {
                    $("#xscript").html(resp);
                })
                .fail(function(resp) {
                    console.log(resp);
                })
                .always(function(resp) {

                });
        }
    }
</script>