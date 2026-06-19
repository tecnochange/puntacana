<?php
    $respuesta = '';
    $hoy = date("Y-m-d H:i:s");

    //EDITAR O CREAR REGISTROS
    if( $_POST["password"] != "" && $_POST["nuevo_password"] != "" ){
        
        $queryPw = mysqli_query($connect_admin,"SELECT * FROM Empleados WHERE id = '".$user_log['id']."' AND password = '".$_POST["password"]."' ");
        if($queryPw->num_rows > 0){

            //VALIDAR EN EL HISTORICO DE CONTRASEÑA
            $queryhistoria = mysqli_query($connect_admin,"SELECT * FROM Empleados_Historico_Contranias WHERE id_empleado = '".$user_log['id']."' AND pass = '".$_POST["nuevo_password"]."' ");
            if($queryhistoria->num_rows == 0){

                $sentencia_actualizar = "UPDATE Empleados SET 
                    password = '".$_POST["nuevo_password"]."', cambio_pass = 2, fecha_cambio_pass = '".$hoy."'  
                    WHERE id = '".$user_log['id']."' 
                ";

                mysqli_query( $connect_admin, $sentencia_actualizar);

                $sentencia_historico = "
                INSERT INTO Empleados_Historico_Contranias(
                    id_empleado,
                    pass,
                    created_at
                )
                VALUES(
                    '".$user_log["id"]."',
                    '".$_POST["nuevo_password"]."',
                    '".$hoy."'
                )
                ";
                mysqli_query( $connect_admin, $sentencia_historico);

                $respuesta = '
                    <div class="alert alert-success" role="alert">
                    La contraseña ha sido actualizado con éxito.
                    </div>
                ';

                $user_log['cambio_pass'] = 2;
                $user_log['cambio_pass'] = 2;

            }
            else{
                 $respuesta = '
                    <div class="alert alert-danger" role="alert">
                        Esta contreseña ya ha sido usada para esta cuenta. Pruebe con una nueva contraseña.
                    </div>
                ';
            }

            
            
        }
        else{
            $respuesta = '
                <div class="alert alert-danger" role="alert">
                  Lo sentimos. la contraseña actual no coinciede con nuestros registros.
                </div>
            ';
        }
    }
?>

<style>
    .valid { color: green; }
    .invalid { color: red; }
</style>

<div class="container">
    
    <div class="row">
        
   
    
        <!-- AREA DE TRABAJO -->
        <div class="col-md-12">
            
            <div class="card custom-card" style="margin-bottom: 30px">

                <div class="card-body">

                    <?php if($user_log['cambio_pass'] == 1){ ?>
                    <div class="alert alert-danger text-center" role="alert">
                        Requiere Cambio de contraseña
                    </div>
                    <?php } ?>
                    
                    <h3>Cambiar Contraseña</h3>
                    A continuación podrá modificar la contraseña actual por una nueva.<br><br>
                    
                    <?php echo $respuesta; ?>
                    
                    <form action="" method="post" id="formulario">
                    <div class="row">

                        <input type="hidden" value="<?php echo $_SESSION['id_user']; ?>" name="id_registro">
                        
                        <div class="col-md-6">
                            <label>Contraseña Anterior *</label>
                            <input type="text" class="form-control" name="password" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label>Nueva contraseña *</label>
                            <input type="text" class="form-control" name="nuevo_password" id="nuevo_password" onkeyup="validatePassword()" required >
                        </div>

                        <div class="col-md-12 mt-3">
                            <ul>
                                <li id="length" class="invalid">Mínimo 8 caracteres</li>
                                <li id="uppercase" class="invalid">Al menos una letra mayúscula</li>
                                <li id="lowercase" class="invalid">Al menos una letra minúscula</li>
                                <li id="number" class="invalid">Al menos un número</li>
                                <li id="special" class="invalid">Al menos un carácter especial (!@#$%^&*)</li>
                            </ul>
                        </div>
                        
                        <div class="col-md-12" align="right" style="margin-top: 20px">
                            <button type="submit" class="btn btn-success" id="btnSubmit">Cambiar</button>    
                        </div>
                        
                    </div>
                    </form>

                </div>

            </div>
            
        </div>

    </div>

</div>



<script>
function validatePassword() {
    const password = document.getElementById("nuevo_password").value;

    // Reglas
    const length = password.length >= 8;
    const uppercase = /[A-Z]/.test(password);
    const lowercase = /[a-z]/.test(password);
    const number = /[0-9]/.test(password);
    const special = /[!@#$%^&*(),.?":{}|<>]/.test(password);

    // Validar cada regla
    updateRule("length", length);
    updateRule("uppercase", uppercase);
    updateRule("lowercase", lowercase);
    updateRule("number", number);
    updateRule("special", special);

    // Habilitar o deshabilitar el botón
      const allValid = length && uppercase && lowercase && number && special;
      document.getElementById("btnSubmit").disabled = !allValid;
}

function updateRule(id, valid) {
    const element = document.getElementById(id);
    if (valid) {
        element.classList.remove("invalid");
        element.classList.add("valid");
    } else {
        element.classList.remove("valid");
        element.classList.add("invalid");
    }
}
    
</script>

