<?php

class Auditoria {

    public function auditoria_lista( $request ){
        global $connect_admin;

        $ARRAY = [];
        $sentencia = "
            SELECT
                Auditoria_Admin.*, Empleados.nombre AS nombre_empleado, 
                Vicepresidencia.nombre AS vicepresidencia, 
                Areas.nombre AS area
            FROM
                Auditoria_Admin
            LEFT JOIN Empleados ON Empleados.id = Auditoria_Admin.id_empleado 
            LEFT JOIN Vicepresidencia ON Vicepresidencia.id = Empleados.unidad_corporativa  
            LEFT JOIN Areas ON Areas.id = Empleados.area 
            WHERE
            Auditoria_Admin.id_empresa = '".$_SESSION["id_empresa"]."' 
            ORDER BY  Auditoria_Admin.id DESC
        ";
        $query = mysqli_query($connect_admin, $sentencia);
        while ($data = mysqli_fetch_array($query)){


            $ARRAY[$data["id"]] = $data;
        }

        return $ARRAY;
    }

}


?>