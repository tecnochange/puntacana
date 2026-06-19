<form action="" method="POST">
<div class="card mb-3">
    <div class="card-body">
    
        <div class="row">

            <div class="col-md-4 mb-2">
                <select class="form-control form-control-sm" name="anio_fill" >
                    <option value="-1" style="color: blue;">Por Año...</option>
                    <?php
                        foreach ($Array_Anio as $periodo) {
                            if ($_SESSION["anio_fill"] ==  $periodo[0]) {
                                echo '<option value="'.$periodo[0].'" selected>'.$periodo[1].'</option>';
                            } else {
                                echo '<option value="'.$periodo[0].'">'.$periodo[1].'</option>';
                            }
                        }
                    ?>
                </select>
            </div>

            <div class="col-md-5">
                <div class="row" style="align-items: center;">
					<div class="col-md-2">
						<label for="" style="color: black;">Periodo:</label>
					</div>
					<?php $selectedOptions = isset($_SESSION['selected_periodo_okr']) ? $_SESSION['selected_periodo_okr'] : []; ?>
					<div class="col-md-2">
						<input type="checkbox" name="periodo[]" value="Q1" <?php echo in_array('Q1', $selectedOptions) ? 'checked' : ''; ?> class="form-check-input"> Q1
					</div>
					<div class="col-md-2">
						<input type="checkbox" name="periodo[]" value="Q2" <?php echo in_array('Q2', $selectedOptions) ? 'checked' : ''; ?> class="form-check-input"> Q2
					</div>
					<div class="col-md-2">
						<input type="checkbox" name="periodo[]" value="Q3" <?php echo in_array('Q3', $selectedOptions) ? 'checked' : ''; ?> class="form-check-input"> Q3
					</div>
					<div class="col-md-2">
						<input type="checkbox" name="periodo[]" value="Q4" <?php echo in_array('Q4', $selectedOptions) ? 'checked' : ''; ?> class="form-check-input"> Q4
					</div>
					<div class="col-md-2">
						<input type="checkbox" name="periodo[]" value="Anual" <?php echo in_array('Anual', $selectedOptions) ? 'checked' : ''; ?> class="form-check-input"> Anual
					</div>
				</div>
            </div>

            <div class="col-md-3 text-end ">
                <button type="submit" class="btn btn-success ">Filtrar</button>
                <button type="button" class="btn btn-danger" onclick="ResetFiltros();">Resetear Filtros</button>
            </div> 

            

            


        </div>

    </div>
</div>
</form>

<form action="" method="POST" id="formulario_filtros">
    <input type="hidden" name="resetear_filtros" value="true">
</form>

<script>
    function ResetFiltros(){
        $("#formulario_filtros").submit();
    }
</script>



