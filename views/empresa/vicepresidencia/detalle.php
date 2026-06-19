<script>
$(document).ready(function() {
    $('#menuEmpresa').collapse();
    $('#bt_empresa_vicepresidencias').addClass('active');
});
</script>

<?php
$id = $_GET["id"];
$hoy = date("Y-m-d H:i:s");

//PARA GUARDAR O EDITAR UN REGISTRO
//PARA GUARDAR O EDITAR UN REGISTRO
if ($_POST["guardar_formulario"] != "") {

    if ($_POST["id_registro"] != "") {

        mysqli_query($connect_admin, "UPDATE Vicepresidencia SET nombre = '" . $_POST["nombre"] . "', estado = '" . $_POST["estado"] . "', updated_at = '" . $hoy . "' WHERE id = '" . $_POST["id_registro"] . "'  ");

        //BLOQUE PARA GUARDAR LA BITACORA
        //BLOQUE PARA GUARDAR LA BITACORA
        //BLOQUE PARA GUARDAR LA BITACORA
        $accion = 'ACTUALIZAR';
        $descripcion = 'Actualización de vicepresidencia ' . $_POST["nombre"];
        $modulo = 'Vicepresidencia';
        GuardarAuditoria( $user_log["id_empresa"], $user_log["id"], $accion, $descripcion, $modulo );
    } 
    else{

        mysqli_query($connect_admin, "INSERT INTO Vicepresidencia (id_empresa, nombre, estado, created_at ) 
		VALUES 
		( '" . $_SESSION['id_empresa'] . "', '" . $_POST["nombre"] . "', '" . $_POST["estado"] . "', '" . $hoy . "' ) ");

        //BLOQUE PARA GUARDAR LA BITACORA
        //BLOQUE PARA GUARDAR LA BITACORA
        //BLOQUE PARA GUARDAR LA BITACORA
        $accion = 'CREAR';
        $descripcion = 'Creación de vicepresidencia ' . $_POST["nombre"];
        $modulo = 'Vicepresidencia';
        GuardarAuditoria( $user_log["id_empresa"], $user_log["id"], $accion, $descripcion, $modulo );


    }

    echo '<script> window.location = "?pg=empresa/vicepresidencia/detalle&id='.$id.'";</script>'; //para evitar reinsersion
}

//PARA GUARDAR O EDITAR UN REGISTRO
//PARA GUARDAR O EDITAR UN REGISTRO
if ($_POST["guardar_lider"] != "") {
    mysqli_query($connect_admin, "INSERT INTO Lideres_Vicepresidencia (id_empresa, id_vicepresidencia, id_lider, estado, created_at ) 
    VALUES 
    ( '".$_SESSION['id_empresa']."', '".$id."', '".$_POST["lider"]."', 1, '".$hoy."' ) ");

    $accion = 'CREAR';
    $descripcion = 'Asignación de lider a la vicepresidencia ' . $data["nombre"];

    $auditoria = "INSERT INTO Auditoria_Admin (id_empresa,id_empleado,accion,descripcion,modulo,created_at)
            VALUES (" . $_SESSION['id_empresa'] . ", " . $_SESSION['id_user'] . ",'$accion','$descripcion','Vicepresidencia','$hoy')";
    // echo $auditoria;
    //mysqli_query($connect_valentina, $auditoria);
}




//INFORMACION DE LA BATERIA
$query = mysqli_query($connect_admin, "SELECT * FROM Vicepresidencia WHERE id = '".$id."' ");
$data = mysqli_fetch_array($query);

?>

<div class="container">

    <a href="<?php echo $url; ?>?pg=empresa/vicepresidencias" >
        <button class="btn btn-success mb-3"> << Volver</button>
    </a>

    <div class="card mb-3">
        <div class="card-header">
            <h3>Ficha Empresa</h3>
        </div>

        <form action="" method="POST">
        <input type="hidden" name="guardar_formulario" value="true">
        <input type="hidden" name="id_registro" value="<?php echo $id; ?>">
        <div class="card-body">
            <div class="row">

                <div class="col-md-8 mb-2">
                    <lable>Nombre</lable>
                    <input type="text" class="form-control" name="nombre" value="<?php echo $data["nombre"]; ?>" required>
                </div>

                <div class="col-md-4 mb-2">
                    <lable>Estado</lable>
                    <select class="form-control" name="estado">
                        <option value="">Selecciona...</option>
                        <?php
                        foreach ($Array_Estado as $nivel) {
                            if ($data["estado"] == $nivel[0]) {
                                echo '<option value="' . $nivel[0] . '" selected>' . $nivel[1] . '</option>';
                            } else {
                                echo '<option value="' . $nivel[0] . '">' . $nivel[1] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-12 mb-2" >
                    <button type="submit" class="btn btn-success">
                        Guardar
                    </button>
                </div>

            </div>
        </div>
        </form>

    </div>


    <?php if($id){ ?>
    <div class="card mb-3">
        <div class="card-header">
            <h3>Líderes Relacionados</h3>
        </div>

        <form action="" method="POST">
        <input type="hidden" name="id_registro" value="<?php echo $id; ?>">
        <input type="hidden" name="guardar_lider" value="true">
        <div class="card-body">
            <div class="row">

                <div class="col-md-8 mb-2">
                    <lable>Lider</lable>
                    <select class="multiples_responsables form-control" name="lider">
                        <option value="">Seleccione Lider..</option>
                        <?php
                        $queryLider = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND role IN (1,2) AND estado = 1 AND unidad_corporativa = '".$id."' ORDER BY nombre ASC ");
                        while ($dataLider = mysqli_fetch_array($queryLider)) {
                            if ($data["lider"] == $dataLider["id"]) {
                                echo '<option value="' . $dataLider["id"] . '" selected>' . $dataLider["nombre"] . '</option>';
                            } else {
                                echo '<option value="' . $dataLider["id"] . '">' . $dataLider["nombre"] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-12 mb-2" >
                    <button type="submit" class="btn btn-success">
                        Asignar Líder
                    </button>
                </div>
                
            </div>
        </div>
        </form>

    </div>


    <div class="card mb-3">
        
        <div class="card-body">

                            <div class="table-responsive">
                                <table border="1" id="lideres" class="display table" style="width:100%">
                                    <thead>
                                        <th>Documento</th>
                                        <th>Foto</th>
                                        <th>Nombre</th>
                                        <th>Área</th>
                                        <th>Cargo</th>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $queryLideres = mysqli_query($connect_admin, "SELECT * FROM Lideres_Vicepresidencia 
                                        WHERE id_vicepresidencia = '".$id. "'");
                                        while ($dataLideres = mysqli_fetch_array($queryLideres)) {
                                            $queryEmple = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id = '" . $dataLideres["id_lider"] . "' ");
                                            $dataEmple = mysqli_fetch_array($queryEmple);

                                            $queryArea = mysqli_query($connect_admin, "SELECT * FROM Areas WHERE id = " . $dataEmple["area"] . "");
                                            $dataArea = mysqli_fetch_array($queryArea);

                                            $queryCargo = mysqli_query($connect_admin, "SELECT * FROM Cargos WHERE id = " . $dataEmple["id_cargo"] . "");
                                            $dataCargo = mysqli_fetch_array($queryCargo);

                                            if (!$dataEmple["foto"]) {
                                                $dataEmple["foto"] = "img_default.jpg";
                                            }

                                            $foto_img = '
                                                <img src="https://goforagile.com/recursos/'.$dataEmple["foto"].'" class="foto_miniaturas" title="'.$dataEmple["nombre"].'">
                                            
                                            ';

                                        ?>
                                            <tr style="vertical-align: middle;">
                                                <td><?php echo $dataEmple["documento"]; ?></td>
                                                <td><?php echo $foto_img; ?></td>
                                                <td><?php echo $dataEmple["nombre"]; ?></td>
                                                <td><?php echo $dataArea["nombre"]; ?></td>
                                                <td><?php echo $dataCargo["nombre"]; ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
        </div>

    </div>

    <?php } ?>

</div>


<script>
    var api = '<?php echo $url; ?>api/administrar/';

    var activar = false;

    function Elimimar_Vicepresidencia(id, id_empresa, id_user) {

        if (activar == false) {
            $("#modal_general").modal("show");
            $("#modal_body").html('Está a punto de eliminar una vicepresidencia, esta acción es irreversible ¿está seguro?<br><br>');
            $("#modal_body").append('<button type="button" class="btn btn-danger btn-sm" onclick="activar= true; Elimimar_Vicepresidencia(' + id + ',' + id_empresa + ',' + id_user + ')"> Confirmar </button>');
        } else {

            jQuery.ajax({
                    url: api + "eliminar_vicepresidencia.php",
                    type: 'post',
                    data: {
                        id: id,
                        id_empresa: id_empresa,
                        id_user: id_user,
                        url: "?pg=administrar/vicepresidencias"
                    },
                }).done(function(resp) {
                    $("#xscript").html(resp);
                })
                .fail(function(resp) {
                    console.log(resp);
                })
                .always(function(resp) {});

        }
    }

    function Elimimar_Lider(id, id_empresa, id_user) {

        if (activar == false) {
            $("#modal_general").modal("show");
            $("#modal_body").html('Está a punto de eliminar un lider, esta acción es irreversible ¿está seguro?<br><br>');
            $("#modal_body").append('<button type="button" class="btn btn-danger btn-sm" onclick="activar= true; Elimimar_Lider(' + id + ',' + id_user + ')"> Confirmar </button>');
        } else {

            jQuery.ajax({
                    url: api + "eliminar_lider_vp.php",
                    type: 'post',
                    data: {
                        id: id,
                        id_empresa: id_empresa,
                        id_user: id_user,
                        url: "?pg=administrar/vicepresidencia/detalle&id=<?php echo $id; ?>"
                    },
                }).done(function(resp) {
                    $("#xscript").html(resp);
                })
                .fail(function(resp) {
                    console.log(resp);
                })
                .always(function(resp) {});

        }
    }
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#lideres').DataTable({
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
                [2, 'asc']
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

<script>
    $(".menu_section").addClass("active");
    // $("#nav_empresa").addClass("active");
    jQuery("#menu_empresa").css("display", "none");
    $("#bt_admin_vicepresidencias").addClass("current-page");
</script>