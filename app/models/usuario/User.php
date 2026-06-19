<?php
class User{
	
	//INICIO DE SESION
    public function usuario($id, $connect_admin){
		
         $qryEmpleado = mysqli_query($connect_admin, "SELECT * FROM Empleados 
        WHERE id = '".$id."' ");
		
		$query = mysqli_query($connect_admin,"SELECT * FROM Empleados WHERE correo = '".$user."' AND password = '".$pass."' AND estado = 1 ");
		if($query->num_rows > 0){
			$data = mysqli_fetch_array($query);
			
			$_SESSION['id_user'] = $data["id"];
            $_SESSION['id_empresa'] = $data["id_empresa"];
			return true;
		}
		else{
			return false;	
		}  
    }
	
	//TERMINAR SESIÓN
    public function logout(){
		$_SESSION = array();
		session_destroy();
		return true;
	}
    
}

?>
