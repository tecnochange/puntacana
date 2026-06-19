<?php

class Empresa {

    public function detalle(){
        global $connect_admin;

        $sentencia = " SELECT * FROM Empresa WHERE id = '".$_SESSION['id_empresa']."' ";
        $query = mysqli_query($connect_admin, $sentencia);
        $data = mysqli_fetch_array($query);

        return $data;
    }

}


?>