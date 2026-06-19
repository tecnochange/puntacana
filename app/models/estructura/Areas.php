<?php

class Areas {

    public function areas_lista( $request ){
        global $connect_admin;
        //COLABORADORES
        $ARRAY = [];
        $sentencia = "
            SELECT
                *
            FROM
                Areas
            WHERE
                id_empresa = '".$_SESSION["id_empresa"]."' 
        ";
        $query = mysqli_query($connect_admin, $sentencia);
        while ($data = mysqli_fetch_array($query)){

            //ESTADO
            $data["txt_estado"] = 'Activo';
            if($data["estado"] == 2){
                $data["txt_estado"] = 'Inactivo';
            }

            $listado_lideres = '';

            $queryLideres = mysqli_query($connect_admin, "SELECT * FROM Lideres_Area WHERE id_area = '" . $data["id"] . "'");
            if (mysqli_num_rows($queryLideres) > 0) {
                while ($dataLideres = mysqli_fetch_array($queryLideres)) {
                    $queryEmple = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id = '" . $dataLideres["id_lider"] . "' ");
                    $dataEmple = mysqli_fetch_array($queryEmple);

                    if (!$dataEmple["foto"]) {
                        $dataEmple["foto"] = "img_default.jpg";
                    }
                        $listado_lideres .= '<a data-bs-toggle="tooltip" href="javascript:Profile(' . $dataLideres["id_lider"] . ',1)" class="dropdown-item" id="profileOkr"><img data-src="https://goforagile.com/recursos/' . $dataEmple["foto"] . '" class="lazyload foto_min" title="' . $dataEmple["nombre"] . '" style="width: 35px !important;height: 35px !important;"></a>';
                    }
            } 
            else {
                $listado_lideres = 'Sin Asignar';
            }

            $data["lideres"] = $listado_lideres;

            //COLABORADORES ASIGNADOS
            $queryAreas = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE area = '" . $data["id"] . "' ");
            $data["colaboradores_asignados"] = $queryAreas->num_rows;

            $ARRAY[$data["id"]] = $data;
        }

        return $ARRAY;
    }

    /*
    public function area($id){ 

        global $connect_admin;

        $sentencia_col = "
            SELECT
                Empleados.id AS id,
                Empleados.nombre AS nombre,
                Empleados.documento AS documento, 
                Empleados.estado AS estado, 
                Empleados.correo AS correo, 
                Empleados.foto AS foto,
                Cargos.id AS id_cargo,
                Cargos.nombre AS nombre_cargo,
                Areas.nombre AS nombre_area,
                Areas.id AS id_area, 
                Vicepresidencia.nombre AS nombre_vicepresidencia, 
                Vicepresidencia.id AS id_vicepresidencia,
                Nivel_Jerarquico.nombre AS nombre_nivel_jerarquico, 
                Nivel_Jerarquico.id AS id_nivel_jerarquico, 
                Estructura_Empresa.unidad_organizativa AS nombre_unidad, 
                Estructura_Empresa.id AS id_unidad  
            FROM
                Empleados
            LEFT JOIN Cargos ON Cargos.id = Empleados.id_cargo
            LEFT JOIN Areas ON Areas.id = Empleados.area
            LEFT JOIN Vicepresidencia ON Vicepresidencia.id = Empleados.unidad_corporativa
            LEFT JOIN Nivel_Jerarquico ON Nivel_Jerarquico.id = Empleados.nivel_jerarquico 
            LEFT JOIN Estructura_Empresa ON Estructura_Empresa.id = Empleados.unidad_organizativa 
            WHERE
                Empleados.id = '".$id."' AND Empleados.id_empresa = '".$_SESSION["id_empresa"]."' 
            ORDER BY
                Empleados.nombre ASC 
        ";
        $queryColaborador = mysqli_query($connect_admin, $sentencia_col);
        $dataColaborador = mysqli_fetch_array($queryColaborador);

        if($queryColaborador->num_rows == 0){
            $dataColaborador = NULL;
        }

        return $dataColaborador;
    }
        */
    
}


?>