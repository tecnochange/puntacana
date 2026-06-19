<script>
    $(document).ready(function() {
        $('#menuAcademia').collapse();
        $("#bt_goforexpert_mis_cursos").addClass("active");
    });
</script>

<?php

$hoy = date("Y-m-d H:i:s");
$id_curso = $pagina[1];

$filtro = "";
if ($_GET["p"]) {
    $filtro .= " AND id_programa = '" . $_GET["p"] . "' ";
}

//ID CURSO SELECCIONADO
if ($_POST["id_curso"]) {
    $_SESSION['id_curso'] = $_POST["id_curso"];
    echo '
            <script>
                window.location = "' . $url . '?pg=academia/panel";
            </script>
        ';
}

//PARA REGISTRAR UN NUEVO CURSO
if ($id_curso) {
    $queryVal = mysqli_query($connect_academia, "SELECT * FROM Estudiantes_Cursos WHERE id_estudiante = '" . $user_log['id'] . "' AND id_curso = '" . $id_curso . "' ");
    if ($queryVal->num_rows == 0) {

        mysqli_query($connect, "INSERT INTO Estudiantes_Cursos (id_curso, id_estudiante, estado, created_at) 
            VALUES 
            ('" . $id_curso . "', '" . $dtEmpleado['id'] . "', 1, '" . $hoy . "')
            ");
    }
}

?>

<div class="container">

    <div class="row justify-content-center">

        <div class="col-md-12" align="center" style="margin-bottom: 20px">
            <h3>Programas </h3>
        </div>

        <?php
        //echo $_SESSION['id_user_valentina'];
        $query = mysqli_query($connect_academia, "SELECT * FROM Programas WHERE estado = '1' ");
        while ($data = mysqli_fetch_array($query)) {

            $sentencias = "
			SELECT Estudiantes_Cursos.id 
			FROM Estudiantes_Cursos 
			LEFT JOIN Cursos ON Cursos.id = Estudiantes_Cursos.id_curso
			WHERE id_estudiante = '" . $dtEmpleado['id'] . "' AND Cursos.id_programa = '" . $data["id"] . "'  
			";
            $queryVal = mysqli_query($connect_academia, $sentencias);
            if ($queryVal->num_rows > 0) {
        ?>
                <!-- CURSOS DEL PROGRAMA -->
                <div class="col-md-4" align="center" style="margin-bottom: 15px">
                    <div class="card" style="height: 100%;">

                        <div class="card-body" style="height: 100%; ">
                            <img src="<?php echo $url; ?>/recursos/<?php echo $data["imagen"]; ?>" class="minuatura">
                            <h4><?php echo $data["nombre"]; ?></h4>

                           

                                <a href="<?php echo $url; ?>?pg=academia/mis_cursos&p=<?php echo $data["id"]; ?>">
                                    <button type="submit" class="btn btn-primary w-100" style="margin-bottom: 20px; ">
                                        Ver Cursos
                                    </button>
                                </a>

                            

                        </div>
                    </div>
                </div>

        <?php }
        } ?>


        <?php if ($query->num_rows == 0) { ?>
            <div class="caja_curso">

                <div align="left">
                    Sin Programas

                </div>

            </div>

        <?php } ?>

    </div>


</div>

<style>
    .caja_curso {
        box-shadow: 1px 1px 10px rgb(0 0 0 / 20%);
        border-radius: 10px;
        padding: 20px;
        margin: 6px;
        margin-bottom: 20px;
        width: 260px;
        display: inline-table;
    }

    h4 {
        font-size: 16px;
        height: 40px;
    }

    .minuatura {
        width: 100%;
        margin-bottom: 20px;
    }
</style>