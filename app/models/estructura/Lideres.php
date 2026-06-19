<?php

class Lideres {

    public function lideres_lista($request){

        global $connect_admin;
        global $Array_Estado;

        //ARREGLO DE COLABORADORES
        $array_colaboradores = [];
        $sentencia_colaboradores = "
        SELECT
            Empleados.id,
            Empleados.documento,
            Empleados.nombre, 
            Cargos.nombre AS nombre_cargo,
            Areas.nombre AS nombre_area
        FROM
            Empleados
        LEFT JOIN Cargos ON Cargos.id = Empleados.id_cargo
        LEFT JOIN Areas ON Areas.id = Empleados.area
        WHERE
            Empleados.id_empresa = '" . $_SESSION["id_empresa"] . "'
        ORDER BY
            Empleados.nombre ASC
        ";
        $queryCol = mysqli_query($connect_admin, $sentencia_colaboradores);
        while ($dataCol = mysqli_fetch_array($queryCol)) { 
            $array_colaboradores[$dataCol["id"]] = $dataCol;
        }

        //ARREGLO DE LIDERES
        $array_lideres = array();
        $queryLideres = mysqli_query($connect_admin, "SELECT * FROM Lideres WHERE id_empresa = '".$_SESSION["id_empresa"]."' ");
        while ($dataLideres = mysqli_fetch_array($queryLideres)) { 
            array_push($array_lideres, $dataLideres);
        }


        //COLABORADORES
        $ARRAY = [];
        $sentencia = "
            SELECT
                Empleados.id,
                Empleados.documento,
                Empleados.nombre, 
                Cargos.nombre AS nombre_cargo,
                Areas.nombre AS nombre_area
            FROM
                Empleados
            LEFT JOIN Cargos ON Cargos.id = Empleados.id_cargo
            LEFT JOIN Areas ON Areas.id = Empleados.area
            WHERE
                Empleados.id_empresa = '" . $_SESSION["id_empresa"] . "' AND Empleados.estado = 1
            ORDER BY
                Empleados.nombre ASC
        ";

        $query = mysqli_query($connect_admin, $sentencia);
        while ($data = mysqli_fetch_array($query)){

            
            
            foreach($array_lideres as $lider){

                if($lider["id_empleado"] == $data["id"] ){
                    
                    $data_lider = $array_colaboradores[$lider["id_jefe"]];

                    $lista_jefes_acciones = '
                        <div style="display: flex;gap: 8px;' . ($tiene_margen ? $estilos : '') . '">
                            <button type="button" id="sidebarCollapse" class="btn btn-danger btn-sm" title="Quitar jefe" style="font-size: 12px; padding: 3px 5px;" onclick="Elimimar_Jefe(' . $lider["id"] . ')">
                                <i class="bx bx-trash"></i>
                            </button>
                        </div>
                    ';

                    $nodo = array(
                        "id" => $data["id"], 
                        "documento" => $data["documento"], 
                        "nombre" => $data["nombre"], 
                        "cargo" => $data["nombre_cargo"], 
                        "area" => $data["nombre_area"], 
                        "documento_lider" => $data_lider["documento"], 
                        "nombre_lider" => $data_lider["nombre"], 
                        "cargo_lider" => $data_lider["nombre_cargo"], 
                        "area_lider" => $data_lider["nombre_area"], 
                        "acciones" => $lista_jefes_acciones, 

                    );

                    $ARRAY[] = $nodo;
                }
            }

        }

        return $ARRAY;
    }

    
}


?>