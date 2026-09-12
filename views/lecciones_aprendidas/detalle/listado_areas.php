<?php
if ($_SESSION["role_plataforma"] == 2 || $_SESSION["role_plataforma"] == 3) {
    $id_area_values = array_column($resultado, 'area');

    $id_area_unique = array_unique($id_area_values);

    $areasSearch = implode(', ', $id_area_unique);
    if($filtro_area != ""){
        $areas = $filtro_area;
    }else{
        $areas = "AND area IN ($areasSearch)";
    }
    $queryInt = mysqli_query($connect_admin, "SELECT DISTINCT(area) AS area FROM Estructura_Empresa WHERE id_empresa = '" . $_SESSION['id_empresa'] . "'  AND estado = 1 AND vicepresidencia = '" . $dataVP["id"] . "' $areas ORDER BY area ");
}else{
    $queryInt = mysqli_query($connect_admin, "SELECT DISTINCT(area) AS area FROM Estructura_Empresa WHERE id_empresa = '" . $_SESSION['id_empresa'] . "'  AND estado = 1 AND vicepresidencia = '" . $dataVP["id"] . "' $filtro_area ORDER BY area ");
}


$contador = $prueba = $contVP = 0;

while ($dataInt = mysqli_fetch_array($queryInt)) {

    if ($_SESSION["role_plataforma"] == 1) {
        $resultado = CargaLeccionesAdmin($connect_clima, $connect_admin, $filtro_la, $filtro_kr, $filtro_claves, $filtro_area, $filtro_vp);
    } else if ($_SESSION["role_plataforma"] == 2) {
        $resultado = CargaLeccionesLider($connect_clima, $connect_admin, $filtro_la, $filtro_kr, $filtro_claves, $filtro_area, $filtro_vp, $_SESSION["id_user"]);
    } else {
        $resultado = CargaLeccionesColaborador($connect_clima, $connect_admin, $filtro_la, $filtro_kr, $filtro_claves, $filtro_area, $filtro_vp, $_SESSION["id_user"]);
    }

    $queryArea = mysqli_query($connect_admin, "SELECT * FROM Areas WHERE id = " . $dataInt["area"] . "");
    $dataArea = mysqli_fetch_array($queryArea);

    $arrayCol[$contVP]['id'] = $dataInt["area"];
    $arrayCol[$contVP]['nombre'] = $dataArea["nombre"];

    $id_area_values = array_column($resultado, 'area');

    $id_area_count = array_count_values($id_area_values);

    // print_r($OKRS);
    if (isset($id_area_count[$dataInt["area"]])) {
        $arrayCol[$contVP]['contador'] = $id_area_count[$dataInt["area"]];
    } else {
        $arrayCol[$contVP]['contador'] = 0;
    }
    

    $listado_lideres = '';

    $queryLideres = mysqli_query($connect_admin, "SELECT * FROM Lideres_Area WHERE id_area = '" . $dataInt["area"] . "'");

    if (mysqli_num_rows($queryLideres) > 0) {
        while ($dataLideres = mysqli_fetch_array($queryLideres)) {
            $queryEmple = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id = '" . $dataLideres["id_lider"] . "' ");
            $dataEmple = mysqli_fetch_array($queryEmple);

            if (!$dataEmple["foto"]) {
                $dataEmple["foto"] = "img_default.jpg";
            }
            $listado_lideres .= '<a data-bs-toggle="tooltip" href="javascript:Profile(' . $dataLideres["id_lider"] . ',1)" class="dropdown-item" id="profileOkr"><img data-src="' . $url . '/recursos/' . $dataEmple["foto"] . '" class="lazyload foto_min" title="' . $dataEmple["nombre"] . '" style="width: 35px !important;height: 35px !important;"></a>';
        }
    } else {
        $listado_lideres = 'Sin Asignar';
    }

    $arrayCol[$contVP]['nombre_lider'] = $listado_lideres;

    $contVP++;
}
