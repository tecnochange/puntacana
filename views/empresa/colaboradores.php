<script>
$(document).ready(function() {
    $('#menuEmpresa').collapse();
    $('#bt_empresa_colaboradores').addClass('active');
});
</script>

<?php

$hoy = date("Y-m-d H:i:s");
if ($_POST["guardar_colaborador"] != "") {

    $queryVdl = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE documento = '" . $_POST["documento"] . "' AND id_empresa = '" . $_SESSION['id_empresa'] . "' AND id NOT IN (" . $_POST["id_colaborador"] . ")");
    $queryVd2 = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND correo = '" . $_POST['correo'] . "' AND id NOT IN (" . $_POST["id_colaborador"] . ")");

    if (mysqli_num_rows($queryVd1) > 0) {
        $respuesta = '
        <div class="alert alert-danger" role="alert">
            Lo sentimos, ya se encuentra un usuario creado con el documento ' . $_POST["documento"] . '
        </div>
        ';
    } else {
        if (mysqli_num_rows($queryVd2) > 0) {
            $respuesta = '
            <div class="alert alert-danger" role="alert">
                Lo sentimos, ya se encuentra un usuario creado con el correo ' . $_POST["correo"] . '
            </div>
            ';
        } else {
            $sentencia = "
            UPDATE  Empleados  SET
            documento = '" . $_POST["documento"] . "',
            nombre = '" . $_POST["nombre"] . "',
            fecha_ingreso = '" . $_POST["fecha_ingreso"] . "',
            antiguedad_anios = '" . $_POST["antiguedad_anios"] . "',
            antiguedad_meses = '" . $_POST["antiguedad_meses"] . "',
            antiguedad_dias = '" . $_POST["antiguedad_dias"] . "',
            id_cargo = '" . $_POST["id_cargo"] . "',
            cargo = '" . $cargo . "',
            correo = '" . $_POST["correo"] . "',
            nivel_jerarquico = '" . $_POST["nivel_jerarquico"] . "',
            unidad_corporativa = '" . $_POST["unidad_corporativa"] . "',
            area = '" . $_POST["area"] . "',
            role = '" . $_POST["role"] . "',
            estado = '" . $_POST["estado"] . "',
            password = '" . $_POST["password"] . "',
            verificar = '" . $_POST["verificar"] . "',
            updated_at = '" . $hoy . "'
            WHERE id = '" . $_POST["id_colaborador"] . "'
            ";

            // echo $sentencia;

            mysqli_query($connect_valentina, $sentencia);
            echo '<script> window.location = "?pg=administrar/colaboradores";</script>';
        }
    }
}

$_SESSION["id_colaborador_edit"] = "";

include("app/models/estructura/Colaboradores.php");
$ClassColaboradores = new Colaboradores();

$colaboradores = $ClassColaboradores->colaboradores_lista(null, $connect_admin);
?>

<div class="container-fluid">

    <div class="card">

        <div class="card-header">
            <table style="float:right">
                <tr>
                    <td align="right">
                        <?php if($VALIDAR_ROOT["crear"]){ ?>
                            <a href="<?php echo $url; ?>?pg=empresa/colaborador/detalle">
                                <button type="button" class="btn btn-primary btn-sm">
                                    Nuevo
                                </button>
                            </a>
                        <?php } ?>
                        
                        <button type="button" class="btn btn-success btn-sm" onclick="ExportarExcel()">
                            <i class="bx bx-download"></i> Excel
                        </button>
                    </td>
                </tr>
            </table>
            <h3>Colaboradores</h3>
        </div>

        <div class="card-body">

            <div class="table-responsive">
            <table class="table" id="tabla_general">
                <thead class="thead-success">
                    <tr>
                        <th scope="col" width="15">#</th>
                        <th scope="col">Documento</th>
                        <th scope="col">Nombres y Apellidos</th>
                        <th scope="col">Correo</th>
                        <th scope="col">Cargo</th>
                        <th scope="col">Área</th>
                        <th scope="col">Nivel Jerarquico</th>
                        <th scope="col">Compañia</th>
                        <th scope="col">Rol</th>
                        <th scope="col" class="text-center">Estado</th>
                        <th scope="col">Verificación</th>
                        <th scope="col" style="width: 90px;">Acciones</th>
                    </tr>
                </thead>

                <?php
                $count = 1;
                foreach($colaboradores  as $colaborador){

                    $bt_editar = '';
                    if($VALIDAR_ROOT["editar"]){
                        $bt_editar = '
                            <a href="' . $url . '?pg=empresa/colaborador/detalle&id=' . $colaborador["id"] . '">
                                <button type="button" class="btn btn-success btn-sm" title="Editar">
                                    <i class="bx bx-edit"></i>
                                </button>
                            </a>

                                <button type="button" class="btn btn-warning btn-sm" title="Editar" style="display:none">
                                    <i class="bx bx-face"></i>
                                </button>

                        ';
                    }

                    echo '
                    <tr>
                        
                        <td>'.$count.'</td>
                        <td>'.$colaborador["documento"].'</td>
                        <td>'.$colaborador["nombre"].'</td>
                        <td>'.$colaborador["correo"].'</td>
                        <td>'.$colaborador["nombre_cargo"].'</td>
                        <td>'.$colaborador["nombre_area"].'</td>
                        <td>'.$colaborador["nombre_nivel_jerarquico"].'</td>
                        <td>'.$colaborador["compania"].'</td>
                        <td>'.$colaborador["nombre_rol"].'</td>
                        <td class="text-center">'.$colaborador["txt_estado"].'</td>
                        <td>'.$colaborador["txt_verificado"].'</td>
                        <td>'.$bt_editar.'</td>
                        
                    </tr>
                    ';
                    $count++;
                }
                ?>
            </table>

        </div>
    </div>

</div>

<script>
    $(document).ready(function() {

        $('#tabla_general').DataTable(
            {
                pageLength: 50
            }
        );
    });



    function DetalleColaborador(id, id_empresa) {
        jQuery.ajax({
                url: api + "editar_colaborador.php",
                type: 'post',
                data: {
                    id: id,
                    id_empresa: id_empresa,
                },
            }).done(function(resp) {
                $("#modal_colaborador").modal("show");
                $("#modal_contenido").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});
    }
    
    function ExportarExcel() {

    var table = $('#tabla_general').DataTable();

    // Mostrar todos los registros
    table.page.len(-1).draw();

    setTimeout(function(){

        var tabla = document.getElementById("tabla_general").outerHTML;

        var archivo = new Blob(
            ['\ufeff' + tabla],
            { type: 'application/vnd.ms-excel' }
        );

        var url = URL.createObjectURL(archivo);

        var link = document.createElement("a");
        link.href = url;
        link.download = "Colaboradores.xls";

        document.body.appendChild(link);

        link.click();

        document.body.removeChild(link);

        // Volver a 50 registros
        table.page.len(50).draw();

    }, 500);
}

</script>

























