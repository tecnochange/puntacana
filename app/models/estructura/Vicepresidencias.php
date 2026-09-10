<?php

class Vicepresidencias {

    public function vicepresidencias_lista( $request ){
        global $connect_admin;

        $ARRAY = [];
        $sentencia = "
            SELECT
               *
            FROM
                Vicepresidencia
           
            WHERE
            Vicepresidencia.id_empresa = '".$_SESSION["id_empresa"]."' 
        ";
        $query = mysqli_query($connect_admin, $sentencia);
        while ($data = mysqli_fetch_array($query)){

            //ESTADO
            $data["txt_estado"] = 'Activo';
            if($data["estado"] == 2){
                $data["txt_estado"] = 'Inactivo';
            }

            $listado_lideres = '';
            $queryLideres = mysqli_query($connect_admin, "SELECT * FROM Lideres_Vicepresidencia 
            WHERE id_vicepresidencia = '" . $data["id"] . "'");

            if(mysqli_num_rows($queryLideres) > 0){
                                while ($dataLideres = mysqli_fetch_array($queryLideres)) {
                                    $queryEmple = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id = '" . $dataLideres["id_lider"] . "' ");
                                    $dataEmple = mysqli_fetch_array($queryEmple);

                                    
                                    if (!$dataEmple["foto"]) {
                                        $dataEmple["foto"] = "img_default.jpg";
                                    }

                                    $foto_img = '
                                        <img src="https://puntacana.goforagile.com/recursos/'.$dataEmple["foto"].'" class="foto_miniaturas" title="'.$dataEmple["nombre"].'">
                                    
                                    ';

                                    $listado_lideres .= $foto_img;
                                }
            }
            else{
                $listado_lideres = 'Sin Asignar';
            }

            $data["lideres"] = $listado_lideres;

            $ARRAY[$data["id"]] = $data;
        }

        return $ARRAY;
    }

}


?>