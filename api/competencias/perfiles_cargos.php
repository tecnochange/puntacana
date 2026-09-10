<?php
include("../../app/connect.php");
include("../../app/arrays.php");
$hoy = date("Y-m-d H:i:s");

$idEmpresa = $_POST["id_empresa"];
$url = $_POST["url"];

// print_r($_POST);

function eliminar_tildes($archivo)
{

    $cadena = $archivo;
    $cadena = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'Ã¡', 'Á', 'À', 'Â', 'Ä', 'Ã¡', 'Ã', 'Ã', 'ÃƒÁ'),
        array('á', 'á', 'á', 'á', 'á', 'á', 'Á', 'Á', 'Á', 'Á', 'Á', 'Á', 'Á', 'Á'),
        $cadena
    );

    $cadena = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë', 'Ã©', 'Ã‰'),
        array('e', 'e', 'e', 'e', 'É', 'É', 'É', 'É', 'é', 'É'),
        $cadena
    );

    $cadena = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î', 'Ã­', 'Ã'),
        array('i', 'i', 'i', 'i', 'Í', 'Í', 'Í', 'Í', 'Í', 'Í'),
        $cadena
    );

    $cadena = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô', 'Ã³', 'Ã“', 'Ã“', 'oÍ', 'ÃƒÁ“'),
        array('ó', 'ó', 'ó', 'ó', 'Ó', 'Ó', 'Ó', 'Ó', 'ó', 'Ó', 'Ó', 'ó', 'Ó'),
        $cadena
    );

    $cadena = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü', 'Ãº', 'Ãš'),
        array('u', 'u', 'u', 'u', 'Ú', 'Ú', 'Ú', 'Ú', 'ú', 'Ú'),
        $cadena
    );

    $cadena = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç', 'Ã±', 'ÃƒÁ±', 'Ã‘'),
        array('n', 'Ñ', 'c', 'C', 'ñ', 'ñ', 'Ñ'),
        $cadena
    );
    return $cadena;
}

$arrayTipos = array();

$queryT = mysqli_query($connect_valoracion, "SELECT * FROM Tipos 
    WHERE id_empresa = '" . $_POST['id_empresa'] . "' AND anio = '" . $_POST['anio_ciclo'] . "'  ORDER BY id DESC ");
while ($dataT = mysqli_fetch_array($queryT)) {
    array_push($arrayTipos, array($dataT["id"], $dataT["nombre"]));
}

$arrayNiveles = array();
$queryN = mysqli_query($connect_valoracion, "SELECT * FROM Niveles 
    WHERE id_empresa = '" . $_POST['id_empresa'] . "' AND anio = '" . $_POST['anio_ciclo'] . "' ORDER BY id DESC ");
while ($dataN = mysqli_fetch_array($queryN)) {
    array_push($arrayNiveles, array($dataN["id"], $dataN["nombre"]));
}
// echo "SELECT * FROM Cargos WHERE id_empresa = '" . $_POST['id_empresa'] . "' AND estado = 1 ORDER BY nombre ASC ";
$query = mysqli_query($connect_admin, "SELECT * FROM Cargos WHERE id_empresa = '" . $_POST['id_empresa'] . "' AND estado = 1 ORDER BY nombre ASC ");
$count = 1;
while ($data = mysqli_fetch_array($query)) {
    $vicepresidencias = mysqli_query($connect_admin, "SELECT DISTINCT(EE.vicepresidencia) AS id_vicepresidencia 
                                    FROM Estructura_Empresa EE
                                    INNER JOIN Areas AS A ON EE.area = A.id
                                    WHERE A.nombre = '" . $data["area"] . "'");
    $dataVP = mysqli_fetch_array($vicepresidencias);

    $queryVP = mysqli_query($connect_admin, "SELECT * FROM Vicepresidencia WHERE id = " . $dataVP["id_vicepresidencia"] . "");
    $VP = mysqli_fetch_array($queryVP);

    $queryPerfiles = mysqli_query($connect_valoracion, "SELECT * FROM Perfiles_Cargos 
				WHERE id_cargo = '" . $data["id"] . "' AND id_empresa = '" . $_POST["id_empresa"] . "' AND anio = '" . $_POST["anio_ciclo"] . "' ");
    $dataPerfiles = mysqli_fetch_array($queryPerfiles);

    $perfiles = explode(",", $dataPerfiles["perfiles"]);

    $tipo_list = '';
    $competencia_list = '';
    $nivel_list = '';
    foreach ($perfiles as $prf) {
        // echo "SELECT * FROM Competencias_Niveles WHERE id = '" . $prf . "'<br>";
        $queryNivel = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Niveles WHERE id = '" . $prf . "' ");
        $dataNivel = mysqli_fetch_array($queryNivel);
        // echo "SELECT * FROM Competencias WHERE id = '" . $dataNivel["id_competencia"] . "'  ";
        $queryComp = mysqli_query($connect_valoracion, "SELECT * FROM Competencias WHERE id = '" . $dataNivel["id_competencia"] . "'  ");
        $dataComp = mysqli_fetch_array($queryComp);

        $competencia_list .= $dataComp["nombre"] . "\n\r";

        $text_nivel = '';
        foreach ($arrayNiveles as &$nivel) {
            if ($dataNivel["id_nivel"] == $nivel["0"]) {
                $text_nivel = $nivel["1"];
            }
        }
        $nivel_list .= $text_nivel . "\n\r";

        $text_tipo = '';
        foreach ($arrayTipos as &$tipo) {
            if ($dataComp["id_tipo"] === $tipo["0"]) {

                $text_tipo = $tipo["1"];
            }
        }
        $tipo_list .= $text_tipo . "\n\r";
    }

    $dataPerfil[] = [
        "contador" => $count,
        "cargo" => eliminar_tildes($data["nombre"]),
        "vicepresidencia" => eliminar_tildes($VP["nombre"]),
        "area" => eliminar_tildes($data["area"]),
        "tipo_competencia" => eliminar_tildes($tipo_list),
        "competencia" => eliminar_tildes($competencia_list),
        "nivel" => eliminar_tildes($nivel_list),
        "anio" => $_POST['anio_ciclo'],
        "acciones" => '<a href="?pg=competencias/perfiles/detalle&id=' . $data["id"] . '" type="button" class="btn btn-primary btn-sm" >
                            <i class="bx bx-pencil" title="Editar"></i>
                        </a>'
    ];

    $count++;
}

$sqlTotal = "SELECT * FROM Cargos WHERE id_empresa = '" . $_POST['id_empresa'] . "' AND estado = 1 ORDER BY nombre ASC ";
$resultTotal = mysqli_query($connect_admin, $sqlTotal);
$totalRecords = mysqli_num_rows($resultTotal);

$response = [
    "test" => $arrayTipos,
    "draw" => $_POST['draw'],
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalRecords,
    "data" => $dataPerfil
];

echo json_encode($response);
