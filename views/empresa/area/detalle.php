<script>
$(document).ready(function() {
    $('#menuEmpresa').collapse();
    $('#bt_empresa_areas').addClass('active');
});
</script>

<?php
include("views/administrar/etiquetas.php");
$id = $_GET["id"];
$hoy = date("Y-m-d H:i:s");

//CONSULTA PARA NUEVO CLIENTE
//CONSULTA PARA NUEVO CLIENTE
if ($_POST["guardar_formulario"]) {

    if ($_POST["id_registro"]) {

        $sentecia = "UPDATE Areas SET jerarquia = '" . $_POST["jerarquia"] . "', nombre = '" . $_POST["nombre"] . "',
		padre = '" . $_POST["padre"] . "', estado =  '" . $_POST["estado"] . "'  
		WHERE id = '" . $_POST["id_registro"] . "'";
        mysqli_query($connect_admin, $sentecia);

        //BLOQUE PARA GUARDAR LA BITACORA
        //BLOQUE PARA GUARDAR LA BITACORA
        //BLOQUE PARA GUARDAR LA BITACORA
        $accion = 'ACTUALIZAR';
        $descripcion = 'Actualización de área ' . $_POST["nombre"];
        $modulo = 'Áreas';
        GuardarAuditoria( $user_log["id_empresa"], $user_log["id"], $accion, $descripcion, $modulo );

    } 
    else{
        $sentecia = "
			INSERT INTO Areas ( id_empresa , jerarquia , nombre , padre , estado, created_at ) 
			VALUES 
			( '".$user_log['id_empresa']."',' " . $_POST["jerarquia"] . "', '" . $_POST["nombre"] . "', '" . $_POST["padre"] . "', '" . $_POST["estado"] . "', '" . $hoy . "'   )
			";
        mysqli_query($connect_admin, $sentecia);
        $id = mysqli_insert_id($connect_admin);
		$_GET["id"] = $id;

        //BLOQUE PARA GUARDAR LA BITACORA
        //BLOQUE PARA GUARDAR LA BITACORA
        //BLOQUE PARA GUARDAR LA BITACORA
        $accion = 'CREAR';
        $descripcion = 'Creación de área ' . $_POST["nombre"];
        $modulo = 'Áreas';
        GuardarAuditoria( $user_log["id_empresa"], $user_log["id"], $accion, $descripcion, $modulo );
        
    }

    echo '<script> window.location = "?pg=empresa/areas";</script>'; //para evitar reinsersion  
}

$query = mysqli_query($connect_admin, "SELECT * FROM Areas WHERE id = '" . $id . "' ");
$data = mysqli_fetch_array($query);


if ($_POST["guardar_lider"] != "") {
    mysqli_query($connect_admin, "INSERT INTO Lideres_Area (id_empresa, id_area, id_lider, estado, created_at ) 
    VALUES 
    ( '" . $_SESSION['id_empresa'] . "', $id, '" . $_POST["lider"] . "', 1, '" . $hoy . "' ) ");

    $accion = 'CREAR';
    $descripcion = 'Asignación de lider al Área ' . $data["nombre"];

    $auditoria = "INSERT INTO Auditoria_Admin (id_empresa,id_empleado,accion,descripcion,modulo,created_at)
            VALUES (" . $_SESSION['id_empresa'] . ", " . $_SESSION['id_user'] . ",'$accion','$descripcion','Vicepresidencia','$hoy')";
    // echo $auditoria;
    mysqli_query($connect_admin, $auditoria);
}


//INFORMACION DE LA BATERIA

?>

<div class="container">

    <a href="<?php echo $url; ?>?pg=empresa/areas" >
        <button class="btn btn-success mb-3"> << Volver</button>
    </a>

    <div class="card mb-3">
        <div class="card-header">
            <h3>Ficha Área</h3>
        </div>

        <form action="" method="POST">
        <input type="hidden" name="guardar_formulario" value="true">
        <input type="hidden" name="id_registro" value="<?php echo $id; ?>">

        <div class="card-body">
            <div class="row">

                <div class="col-md-4" style="margin-bottom: 10px">
                    <lable>Nombre</lable>
                    <input type="text" class="form-control" name="nombre" value="<?php echo $data["nombre"]; ?>">
                </div>

                <div class="col-md-4" style="margin-bottom: 10px">
                    <lable>Padre</lable>
                    <select class="form-control" name="padre" onChange="CargarNivelJerarquia(this.value)">
                        <option value="">Selecciona..</option>
                        <option value="0">Inicial</option>
                            <?php
                            $queryJer = mysqli_query($connect_admin, "SELECT * FROM Areas WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' ORDER BY nombre ASC ");
                            while ($dataJer = mysqli_fetch_array($queryJer)) {
                                if ($data["padre"] == $dataJer["id"]) {
                                    echo '<option value="' . $dataJer["id"] . '" selected>' . $dataJer["nombre"] . ' - ' . $dataJer["jerarquia"] . '</option>';
                                } else {
                                    echo '<option value="' . $dataJer["id"] . '">' . $dataJer["nombre"] . ' - ' . $dataJer["jerarquia"] . '</option>';
                                }
                            }
                        ?>
                    </select>
                </div>


                <div class="col-md-4" style="margin-bottom: 10px">
                    <lable>Jerarquia</lable>
                    <input type="text" class="form-control" name="jerarquia" value="<?php echo $data["jerarquia"]; ?>">
                </div>

                <div class="col-md-4" style="margin-bottom: 10px">
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

                <div class="col-md-12" style="margin-bottom: 10px">
                    <button type="submit" class="btn btn-success">
                        Guardar
                    </button>
                </div>
            </div>
        </div>

    </div>

    <?php if ($id > 0) { ?>
    <div class="card mb-3">
        <div class="card-body">
            <form action="" method="post">
            <div class="row">
                <input type="hidden" name="guardar_lider" value="true">

                <div class="col-md-10 mb-2" >
                    <select class="multiples_responsables form-control" name="lider">
                        <option value="">Seleccione Lider..</option>
                        <?php
                            $queryLider = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id_empresa = '" . $user_log['id_empresa'] . "' AND role IN (1,2) AND estado = 1 AND area = $id ORDER BY nombre ASC ");
                            while ($dataLider = mysqli_fetch_array($queryLider)) {
                                if ($data["lider"] == $dataLider["id"]) {
                                    echo '<option value="' . $dataLider["id"] . '" selected>' . $dataLider["nombre"] . '</option>';
                                } 
                                else {
                                    echo '<option value="' . $dataLider["id"] . '">' . $dataLider["nombre"] . '</option>';
                                }
                            }
                        ?>
                    </select>
                </div>

                <div class="col-md-2 mb-2">
                    <button type="submit" id="sidebarCollapse" class="btn btn-success">
                        Asignar Lider
                    </button>
                </div>

            </div>
            </form>
        </div>

        <div class="card-body">
        
            <div class="table-responsive">
                <table border="1" id="lideres" class="display table" style="width:100%">
                    <thead>
                                        <th>Documento</th>
                                        <th>Foto</th>
                                        <th>Nombre</th>
                                        <th><?php echo $etiquetaAdminArea; ?></th>
                                        <th>Cargo</th>
                                        <th>Acciones</th>
                    </thead>
                    <tbody>
                    <?php
                        $queryLideres = mysqli_query($connect_admin, "SELECT * FROM Lideres_Area WHERE id_area = '" . $_GET["id"] . "'");

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
                                            $foto = '<a data-bs-toggle="tooltip" href="javascript:Profile(' . $dataLideres["id_lider"] . ',1)" class="dropdown-item" id="profileOkr"><img data-src="' . $url . '/recursos/' . $dataEmple["foto"] . '" class="lazyload foto_min" title="' . $dataEmple["nombre"] . '" style="width: 35px !important;height: 35px !important;"></a>';

                    ?>
                        <tr style="vertical-align: middle;">
                            <td><?php echo $dataEmple["documento"]; ?></td>
                            <td><?php echo $foto; ?></td>
                            <td><?php echo $dataEmple["nombre"]; ?></td>
                            <td><?php echo $dataArea["nombre"]; ?></td>
                            <td><?php echo $dataCargo["nombre"]; ?></td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm" onClick="Elimimar_Lider(<?php echo $dataLideres["id"] . "," . $dataEmple["id"] . "," . $_SESSION["id_user"]; ?>)">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>                    
                                
                            
    </div>
    <?php } ?>

</div>

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