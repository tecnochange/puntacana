<?php
include("../../app/connect.php");

$hoy = date("Y-m-d H:i:s");
$tipo = $_POST["tipo"];
$id_user = $_POST["id"];
$id_empresa = $_POST["id_empresa"];
$ciclo = $_POST["ciclo"];

$array_empleados = array();

$array_evaluadores = array();
$queryEval = mysqli_query($connect_valoracion, "SELECT * FROM Evaluadores WHERE id_empleado = '" . $id_user . "' AND id_ciclo = '" . $ciclo . "' ");
while ($dataEval = mysqli_fetch_array($queryEval)) {
    array_push($array_evaluadores, $dataEval["id_evaluador"]);
}

//TIPO AUTO
if ($tipo == 1) {
    $queryJer = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id = '" . $id_user . "' AND id_empresa = '" . $id_empresa . "' AND estado = 1 ORDER BY nombre ASC ");
    while ($dataJer = mysqli_fetch_array($queryJer)) {
        array_push($array_empleados, array("id" => $dataJer["id"], "nombre" => $dataJer["nombre"]));
    }
} else if ($tipo == 5) {

    $queryJefes = mysqli_query($connect_admin, "SELECT * FROM Lideres WHERE id_empleado = '" . $id_user . "' ");

    while ($jefe = mysqli_fetch_assoc($queryJefes)) {

        $sentencia = "SELECT * FROM Empleados WHERE id = '" . $jefe["id_jefe"] . "' AND estado = 1 LIMIT 1";
        $queryEmpl = mysqli_query($connect_admin, $sentencia);
        $dataEmpl = mysqli_num_rows($queryEmpl) > 0 ? mysqli_fetch_assoc($queryEmpl) : null;

        if ($dataEmpl) {
            array_push($array_empleados, array("id" => $dataEmpl["id"], "nombre" => $dataEmpl["nombre"]));
        }
    }
} else {
    $queryJer = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id != '" . $id_user . "' AND id_empresa = '" . $id_empresa . "' AND estado = 1 ORDER BY nombre ASC ");
    while ($dataJer = mysqli_fetch_array($queryJer)) {
        array_push($array_empleados, array("id" => $dataJer["id"], "nombre" => $dataJer["nombre"]));
    }
}
?>


<option value="">Seleccione..</option>
<?php
foreach ($array_empleados as $empl) {

    $permitir = true;

    /* foreach($array_evaluadores as $eval){
        if($eval == $empl["id"]){
            $permitir = false;
        }
    } */

    if ($permitir == true) {
        if ($data["id_jefe"] == $empl["id"]) {
            echo '<option value="' . $empl["id"] . '" selected>' . $empl["nombre"] . '</option>';
        } else {
            echo '<option value="' . $empl["id"] . '">' . $empl["nombre"] . '</option>';
        }
    }
}
?>