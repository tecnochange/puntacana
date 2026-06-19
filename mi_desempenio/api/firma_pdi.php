<?php
include("../../app/connect.php");
include("../../app/arrays.php");

$hoy = date("Y-m-d H:i:s");

$queryColaborador = mysqli_query($connect_admin, "SELECT E.nombre AS nombre, E.documento AS documento, C.nombre as cargo, A.nombre AS area 
FROM Empleados E
INNER JOIN Cargos C ON C.id = E.id_cargo
INNER JOIN Areas A ON A.id = E.area
WHERE E.id = '" . $_POST["id_empleado"] . "' ");
$dataColaborador = mysqli_fetch_array($queryColaborador);

?>
<form action="" method="post" id="firmaPdi" autocomplete="off">
    <input type="hidden" name="id_empleado" value="<?php echo $_POST["id_empleado"]; ?>">
    <input type="hidden" name="guardar_firma" value="true">
    <div class="row">
        <div class="col-md-12" style="text-align: justify; font-family: Lato-Regular">
            Para hacer el proceso de firma, puede intentar varias veces su firma, recuerde que si quiere borrar la firma, lo hara por medio del boton de Limpiar Firma.<br>
            Cuando este de acuerdo con su firma, haga click en el boton de Aprobar Firma, y luego click en Firmar.<br>
            <b>Recuerde que la firma es un compromiso de cumplimiento de los planes de acción establecidos en el PDI.</b><br>

        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12" style="font-family: Lato-Regular">
            Firma de aprobación para:
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            Nombre: <?php echo $dataColaborador["nombre"]; ?><br>
            Documento: <?php echo $dataColaborador["documento"]; ?><br>
            Cargo: <?php echo $dataColaborador["cargo"]; ?><br>
            Área: <?php echo $dataColaborador["area"]; ?>
        </div>
    </div>
    <br>
    <div class="col-md-12">
        <canvas id="sig-canvas" width="450" height="160" style="border: 1px solid #ccc;"></canvas>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <textarea id="sig-dataUrl" name="firma" class="form-control" rows="5" hidden></textarea>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <!-- Botón para limpiar la firma -->
            <button class="btn btn-warning" id="sig-clearBtn" type="button">Limpiar Firma</button>
            <!-- Botón para aprobar la firma -->
            <button class="btn btn-success" id="sig-approveBtn" type="button">Aprobar Firma</button>
            <!-- Botón para enviar la firma -->
            <button class="btn btn-primary" id="sig-submitBtn" type="submit" disabled>Firmar</button>
        </div>
    </div>
</form>

<script src="<?php echo $url; ?>views/competencias/informes/firma.js"></script>
<script>

    
    
    // Obtener referencias a los elementos del DOM
    const canvas = document.getElementById('sig-canvas');
    
    const clearBtn = document.getElementById('sig-clearBtn');
    const approveBtn = document.getElementById('sig-approveBtn');
    const submitBtn = document.getElementById('sig-submitBtn');
    const dataUrlInput = document.getElementById('sig-dataUrl');

    // Configuración del canvas para la firma
    const ctx = canvas.getContext('2d');
    let drawing = false;

    canvas.addEventListener('mousedown', (e) => {
        drawing = true;
        ctx.beginPath();
        ctx.moveTo(e.offsetX, e.offsetY);
    });

    canvas.addEventListener('mousemove', (e) => {
        if (drawing) {
            ctx.lineTo(e.offsetX, e.offsetY);
            ctx.stroke();
        }
    });

    canvas.addEventListener('mouseup', () => {
        drawing = false;
        ctx.closePath();
    });

    canvas.addEventListener('mouseleave', () => {
        drawing = false;
    });

    // Función para limpiar el canvas
    clearBtn.addEventListener('click', () => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        dataUrlInput.value = ''; // Limpiar el valor del textarea
        submitBtn.disabled = true; // Deshabilitar el botón de enviar
    });

    // Función para aprobar la firma
    approveBtn.addEventListener('click', () => {
        const dataUrl = canvas.toDataURL(); // Obtener la firma como Data URL
        if (dataUrl) {
            dataUrlInput.value = dataUrl; // Guardar la firma en el textarea oculto
            submitBtn.disabled = false; // Habilitar el botón de enviar
            alert('Firma aprobada. Ahora puedes enviarla.');
        } else {
            alert('Por favor, realiza una firma antes de aprobar.');
        }
    });

    
    
</script>