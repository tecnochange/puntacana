<?php
include("../../app/connect.php");
include("../../app/arrays.php");
//include("../../app/models/arrays.php");

$hoy = date("Y-m-d H:i:s");
$id = $_POST["id"];
$id_user = $_POST["id_user"];
$id_empresa = $_POST["id_empresa"];

$queryEmpresa = mysqli_query($connect_admin, "SELECT * FROM Empresas WHERE id = $id_empresa");
$dataEmpresa = mysqli_fetch_array($queryEmpresa);

// $mesInicio = (int)$dataEmpresa["mes_inicio"];
$inicio = explode('-', $dataEmpresa["mes_inicio"]);

$mesInicio = (int)$inicio[1];

if ($_POST["id_tipo"] != 1) {
    $mesesFrecuencia = agruparMesesPorFrecuencia($mesInicio, $_POST["id_tipo"]);
} else {
    $mesesFrecuencia = obtenerListadoDesdeMes($mesInicio);
}

function agruparMesesPorFrecuencia($mes_inicial, $frecuencia)
{
    $meses = obtenerListadoDesdeMes($mes_inicial);
    $meses_grouped = [];

    $mes_count = count($meses);
    $group_size = 0;

    switch ((int)$frecuencia) {
        case 2:
            $group_size = 2;
            break;
        case 3:
            $group_size = 3;
            break;
        case 6:
            $group_size = 4;
            break;
        case 4:
            $group_size = 6;
            break;
        case 5:
            $group_size = 12;
            break;
        default:
            return [];
    }

    $i = 0;
    while ($i < $mes_count) {
        $group = [];

        if ($frecuencia == 4) {
            $group = array_merge(array_slice($meses, $i, 6));
            $i += 6;
        } else {
            $group = array_merge(array_slice($meses, $i, $group_size));
            $i += $group_size;
        }

        $end_month = end($group);
        $end_month_num = array_search($end_month, $meses);

        $start_month = $group[0];
        $meses_grouped[$end_month_num] = "$start_month - $end_month";
    }

    return $meses_grouped;
}

function obtenerListadoDesdeMes($mesinicial)
{

    $inicio = explode('-', $mesinicial);

    $mes_inicial = $mesinicial;
    $meses = [
        1 => 'Enero',
        2 => 'Febrero',
        3 => 'Marzo',
        4 => 'Abril',
        5 => 'Mayo',
        6 => 'Junio',
        7 => 'Julio',
        8 => 'Agosto',
        9 => 'Septiembre',
        10 => 'Octubre',
        11 => 'Noviembre',
        12 => 'Diciembre'
    ];

    $meses_seleccionados = [];

    for ($i = $mes_inicial; $i <= 12; $i++) {
        $meses_seleccionados[$i] = $meses[$i];
    }

    for ($i = 1; $i < $mes_inicial; $i++) {
        $meses_seleccionados[$i] = $meses[$i];
    }

    return $meses_seleccionados;
}

$query = mysqli_query($connect_kpis, "SELECT * FROM Comentarios_Kpis WHERE id = '" . $id . "' ");
$data = mysqli_fetch_array($query);

$queryKpis = mysqli_query($connect_kpis, "SELECT * FROM Kpis WHERE id = '" . $data["id_kpi"] . "' ");
$dataKpis = mysqli_fetch_array($queryKpis);



?>

<div class="row">

    <input type="hidden" name="id_registro" value="<?php echo $data["id"]; ?>">
    
    <input type="hidden" name="guardar_editar_comentario" value="true">

    <div class="col-md-12" style="margin-bottom: 15px">
        <div>Comentarios para:<br>
            <b>
                <h5><?php echo $dataKpis["indicador"]; ?></h5>
            </b>
        </div>

        <form action="" method="post">
            <div class="form-group">
                <div class="row">
                    <input type="hidden" name="guardar_comentario_kpi" value="true">
                    <input type="hidden" name="id_registro_comentario" value="<?php echo $data["id"]; ?>">
                    <input type="hidden" name="id_kpi" value="<?php echo $id; ?>">
                    <div class="col-md-12">
                        <textarea id="comentario_kpi" name="comentario" class="form-control" placeholder="Ingrese su comentario..." rows="3" required><?php echo $data["comentario"]; ?></textarea>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-3">
                        <label for="">Frecuencia:</label>
                        <select name="frecuencia" id="frecuencia" class="form-control">
                            <option value="">Selecciona...</option>
                            <?php
                            foreach ($mesesFrecuencia as $key => $value) {
                                if($data["frecuencia"] == $value ){
                                    echo '<option value="' . $value . '" selected>' . $value . '</option>';
                            
                                }
                                else{
                                    echo '<option value="' . $value . '">' . $value . '</option>';
                                
                                }
                            }
                                
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12" align="right">
                        <button type="submit" class="btn btn-success btn-sm">Guardar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {

        $('#comentario_kpi').summernote({
            tabsize: 2,
            height: 150,
            minHeight: null,
            maxHeight: null,
            focus: true,

            toolbar: [
                ['para', ['ul', 'ol']]
            ],
            styleTags: [
                "p",
                "code",
                "blockquote",
                "pre",
                "h1",
                "h2",
                "h3",
                "h4",
                "h5",
                "h6",
            ],
        });
    });
</script>