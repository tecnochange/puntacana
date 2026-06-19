<script>
$(document).ready(function() {
    $('#menuEstrategia').collapse();
    $('#bt_estrategia_objetivos').addClass('active');
});
</script>



<?php
$hoy = date("Y-m-d H:i:s");
$id = $_GET["id"];

if ($_POST["objetivo"] != "") {

    if ($_POST["id_registro"] != "") {
        $sentencia = "
			UPDATE  Objetivos_estrategicos  SET  dimensiones = '" . implode(",", $_POST["dimensiones"]) . "', 
			id_responsable = '" . $_POST["id_responsable"] . "', objetivo = '" . $_POST["objetivo"] . "' 
			WHERE id = '" . $_POST["id_registro"] . "'
			";

        mysqli_query($connect_okrs, $sentencia);
    } else {
        $sentencia = "
			INSERT INTO  Objetivos_estrategicos ( id_empresa ,  dimensiones ,  id_responsable ,  objetivo ,  estado ,  created_at ) 
			VALUES 
			( '" . $_SESSION['id_empresa'] . "', '" . implode(",", $_POST["dimensiones"]) . "', '" . $_POST["id_responsable"] . "', '" . $_POST["objetivo"] . "', 1, '" . $hoy . "'  )
			";
        mysqli_query($connect_okrs, $sentencia);
    }

    echo '<script> window.location.href = "?pg=okrs/objetivos";</script>';
}

$query = mysqli_query($connect_okrs, "SELECT * FROM Objetivos_estrategicos WHERE id = '" . $id . "' ");
$data = mysqli_fetch_array($query);
$array_dimensiones = explode(",", $data["dimensiones"]);

$querySM24 = mysqli_query($connect_admin, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 2 AND id_submenu = 13");
$dataSM24 = mysqli_fetch_array($querySM24);
?>
<?php echo $respuesta; ?>

<br>


<div class="container">
    <div class="card">
        <div class="card-header">
            
            <table style="float:right">
                <tr>
                    <td align="right">
                        <?php if($VALIDAR_ROOT["crear"]){ ?>
                        <a href="<?php echo $url; ?>?pg=estrategica/objetivo/detalle">
                            <button type="button" id="sidebarCollapse" class="btn btn-primary btn-sm">
                                Crear Objetivo
                            </button>
                        </a>
                        <?php } ?>
                    </td>
                </tr>
            </table>
            <h3>Definir Objetivos Estratégicos</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table border="1" id="tabla_general" class="display table" style="width:100%;">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Objetivo</th>
                                    <th scope="col">Responsable</th>
                                    <th scope="col">País</th>
                                    <th scope="col">Sucursal</th>
                                    <th scope="col">Dimensiones</th>
                                    <th scope="col">Ponderación</th>
                                    <th scope="col">Año</th>
                                    <th scope="col" style="width: 120px">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>

                            <?php
                            $count = 1;
                            $queryObjetivos = mysqli_query($connect_okrs, "SELECT * FROM Objetivos_estrategicos WHERE id_empresa = '" . $user_log['id_empresa'] . "' ");
                            while ($dataObjetivos = mysqli_fetch_array($queryObjetivos)) {
                                $query1 = mysqli_query($connect_okrs, "SELECT * FROM Objetivos_estrategicos WHERE id = '" . $dataObjetivos["id"] . "' ");
                                $data1 = mysqli_fetch_array($query1);
                                $array_dimensiones = explode(",", $data1["dimensiones"]);
                                $cont = 0;
                                $count_anios = array();
                                foreach ($Array_Anio as $value) {
                                    $queryAnio = mysqli_query($connect_okrs, "SELECT * FROM Objetivos_estrategicos WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = " . $value[1] . "");
                                    $count_anios[$cont]["anio"] = $value[1];
                                    $count_anios[$cont]["porcentaje"] = round(100 / (mysqli_num_rows($queryAnio)), 2);
                                    $cont++;
                                }
                                if (!isset($data1["ponderacion"])) {
                                    foreach ($count_anios as $periodo) {
                                        if ($data1["anio"] == $periodo["anio"]) {
                                            mysqli_query($connect_okrs, "UPDATE Objetivos_estrategicos SET ponderacion = ".$periodo["porcentaje"]." WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = " . $data1["anio"] . "");
                                            $ponderacion = $periodo["porcentaje"];
                                        }
                                    }
                                } else {
                                    if($data1["ponderacion"] == ""){
                                        foreach ($count_anios as $periodo) {
                                            if ($data1["anio"] == $periodo["anio"]) {
                                                mysqli_query($connect_okrs, "UPDATE Objetivos_estrategicos SET ponderacion = ".$periodo["porcentaje"]." WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = " . $data1["anio"] . "");
                                                $ponderacion = $periodo["porcentaje"];
                                            }
                                        }
                                    }else{
                                    $ponderacion = $data1["ponderacion"];
                                    }
                                }
                                $lista_dimensiones = "";
                                $array_dimensiones = explode(",", $dataObjetivos["dimensiones"]);
                                foreach ($array_dimensiones as $dimension) {
                                    $queryDim = mysqli_query($connect_okrs, "SELECT * FROM Dimensiones WHERE id = '" . $dimension . "' ");
                                    $dataDim = mysqli_fetch_array($queryDim);
                                    $lista_dimensiones .= $dataDim["nombre"] . "<br>";
                                }

                                $queryResponsable = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id = '" . $dataObjetivos["id_responsable"] . "' ");
                                $dataResponsable = mysqli_fetch_array($queryResponsable);

                                echo '
                                <tr>
                                    <td><b>' . $count . '</b></td>
                                    <td>' . $dataObjetivos["objetivo"] . '</td>
                                    <td>' . $dataResponsable["nombre"] . ' ' . $dataResponsable["apellidos"] . '</td>
                                    <td>' . $dataObjetivos["pais"] . '</td>
                                    <td>' . $dataObjetivos["sucursal"] . '</td>
                                    <td>' . $lista_dimensiones . '</td>
                                    <td>' . $dataObjetivos["ponderacion"] . '%</td>	
                                    <td>' . $dataObjetivos["anio"] . '</td>
                                                    
                                    <td>
                                        <a href="' . $url . '?pg=estrategica/objetivo/detalle&id=' . $dataObjetivos["id"] . '" >
                                        <button type="button" class="btn btn-success btn-sm bt_editar" title="editar">
                                            <i class="bx bx-edit"></i>
                                        </button>
                                        </a>
                                    </td>
                                </tr>

                                ';
                                $count++;
                            }
                            ?>


                </table>
            </div>
        </div>
    </div>
</div>




<script type="text/javascript">

    $(document).ready(function() {

        $('#tabla_general').DataTable(
            {
                pageLength: 50
            }
        );
    });


</script>





<script>
    var api = '<?php echo $url; ?>api/okrs/';

    var activar = false;

    function Elimimar(id) {

        if (activar == false) {
            $("#modal_general").modal("show");
            $("#modal_body").html('Está a punto de eliminar un objetivo, esta acción es irreversible ¿está seguro?<br><br>');
            $("#modal_body").append('<button type="button" class="btn btn-danger btn-sm" onclick="activar= true; Elimimar(' + id + ')"> Confirmar </button>');
        } else {

            jQuery.ajax({
                    url: api + "eliminar_objetivo.php",
                    type: 'post',
                    data: {
                        id: id,
                        url: "?pg=okrs/objetivos"
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