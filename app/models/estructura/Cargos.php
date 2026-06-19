<?php

class Cargos {

    public function cargos_lista($request){


        global $connect_admin;
        global $Array_Estado;
        //COLABORADORES
        $ARRAY = [];
        $sentencia = "
            SELECT
                Cargos.id, Cargos.nombre, Cargos.nivel_jerarquico, Areas.nombre AS nombre_area, Cargos.estado AS estado 
            FROM
                Cargos 
            LEFT JOIN Areas ON Areas.id = Cargos.id_area
            WHERE
                Cargos.id_empresa = '" . $_SESSION['id_empresa'] . "'
            ORDER BY
                Cargos.nombre ASC
        ";
        $query = mysqli_query($connect_admin, $sentencia);
        while ($data = mysqli_fetch_array($query)){

            //$queryCargos = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id_cargo = '" . $data["id"] . "' ");

            $txt_estado = '';
            foreach ($Array_Estado as $nivel) {
                if ($nivel[0] == $data["estado"]) {
                    $txt_estado = $nivel[1];
                }
            }

            $nodo = array(
                "id" => $data["id"], 
                "cargo" => $data["nombre"], 
                "nivel" => $data["nivel_jerarquico"], 
                "area" => $data["nombre_area"], 
                "asignados" => $queryCargos->num_rows, 
                "estado" => $txt_estado 

            );


            $ARRAY[$data["id"]] = $nodo;
        }

        return $ARRAY;
    }

    public function cargo($id){ 

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
    
}


?>