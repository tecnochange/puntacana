<?php

class Niveles {

    public function niveles_lista( $request ){
        global $connect_admin;
        global $Array_Estado;
        //COLABORADORES
        $ARRAY = [];
        $sentencia = "
            SELECT * FROM Nivel_Jerarquico WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' ORDER BY nombre ASC 
        ";
        $query = mysqli_query($connect_admin, $sentencia);
        while ($data = mysqli_fetch_array($query)){

            $txt_estado = '';
            foreach ($Array_Estado  as $estado) {
                if ($estado[0] == $data["estado"]) {
                    $txt_estado = $estado[1];
                }
            }

            $nodo = array(
                "id" => $data["id"], 
                "nivel" => $data["nivel"], 
                "nombre" => $data["nombre"], 
                "estado" => $txt_estado, 
            );
            

            $ARRAY[$data["id"]] = $nodo;
        }

        return $ARRAY;
    }
    
}


?>