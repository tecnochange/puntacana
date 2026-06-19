<?php

class EstructuraEmpresa {

    public function estructura_lista( $request ){
        global $connect_admin;

        $ARRAY = [];
        $sentencia = " 
                                SELECT
                                    Estructura_Empresa.id, 
                                    Estructura_Empresa.compania, 
                                    Estructura_Empresa.unidad_organizativa,   
                                    Estructura_Empresa.estado,   
                                    Vicepresidencia.nombre AS nombre_vicepresidencia,
                                    Areas.nombre AS nombre_area, 
                                    Nivel_Jerarquico.nombre AS nombre_nivel 
                                FROM
                                    Estructura_Empresa 
                                    LEFT JOIN Vicepresidencia ON Vicepresidencia.id = Estructura_Empresa.vicepresidencia 
                                    LEFT JOIN Areas ON Areas.id = Estructura_Empresa.area 
                                    LEFT JOIN Nivel_Jerarquico ON Nivel_Jerarquico.id = Estructura_Empresa.nivel_jerarquico 
                                WHERE
                                    Estructura_Empresa.id_empresa = '".$_SESSION['id_empresa']."' 
                                ORDER BY Vicepresidencia.nombre ASC;
        ";
        $query = mysqli_query($connect_admin, $sentencia);
        while ($data = mysqli_fetch_array($query)){

            $ARRAY[$data["id"]] = $data;
        }

        return $ARRAY;
    }

}


?>