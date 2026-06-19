<script>
    // $("#bt_okrs_reportes_equipos").addClass("active_item");
    // $("#nav_visualizaciones").addClass("menu-is-opening menu-open");
    $(".menu_section").addClass("active");	
	// $("#nav_visualizaciones").addClass("active");
	jQuery("#menu_visualizaciones").css("display", "none");
	$("#bt_okrs_derivacion").addClass("current-page");
</script>

<?php
	$alto_resultados = 0;
	$alto = 0;

	//VALIDAMOS LOS FILTROS
	if($_POST["equipo_fill"] > 0){ $_SESSION["equipo_fill"] = $_POST["equipo_fill"]; } 
	if($_POST["equipo_fill"] == -1){ $_SESSION["equipo_fill"] = ""; } 

	if($_POST["anio_fill"] != ""){ $_SESSION["anio_fill"] = $_POST["anio_fill"]; } 
	if($_POST["anio_fill"] == -1){ $_SESSION["anio_fill"] = ""; } 

	include("views/okrs_equipos/functions.php");
	$OKRS = OkrsUsuario( $dtEmpleado["id"], $dtEmpleado['id_empresa'], $connect_okrs, null );

	$nodo_2 = '';

	if($_SESSION["equipo_fill"] > 0 && count($OKRS) > 0 ){
		
		$objetivo = preg_replace("/[\r\n|\n|\r]+/", " ", $OKRS[0]["objetivo"] );
		
		$nodo_2 .= "{ id: '0', parent: '', name: '".$objetivo."', color: '#6B21FF' } ";

		
		$queryResultados = mysqli_query($connect_okrs,"SELECT * FROM Okrs_Resultados 
		WHERE id_okrs = '".$OKRS[0]["id"]."' ");
		while($dataResultados = mysqli_fetch_array($queryResultados)){
			
			
			
			/*
			$queryHijosOKRs = mysqli_query($connect_okrs,"SELECT * FROM Okrs 
			WHERE id_resultado_padre = '".$dataResultados["id"]."'  ");
			while($dataHijosOKRs = mysqli_fetch_array($queryHijosOKRs)){
				$descripcion_hijo = preg_replace("/[\r\n|\n|\r]+/", " ", $dataHijosOKRs["objetivo_okr"] );
				$nodes .= '{ id: '.$dataHijosOKRs["id"].', pid: '.$dataResultados["id"].', Nombre: "'.$descripcion_hijo.'", Cargo: "OKRs Hijo", tags: ["amarillo_claro"] },';
				
				//$nodo_2 .= '{ id: '.$dataHijosOKRs["id"].', parent: "'.$OKRS[0]["id"].'", name: "'.$descripcion_hijo.'" }';
				
				$queryResultadosHijo = mysqli_query($connect_okrs,"SELECT * FROM Okrs_Resultados 
				WHERE id_okrs = '".$dataHijosOKRs["id"]."' ");
				while($dataResultadosHijo = mysqli_fetch_array($queryResultadosHijo)){
					$descripcion_res_hijo = preg_replace("/[\r\n|\n|\r]+/", " ", $dataResultadosHijo["descripcion"] );
					$nodes .= '{ id: '.$dataResultadosHijo["id"].', pid: '.$dataHijosOKRs["id"].', Nombre: "'.$descripcion_res_hijo.'", Cargo: "KR Hijo", tags: ["amarillo_claro"] },';
					
					
					
				}
			}
			*/
			
			$descripcion = preg_replace("/[\r\n|\n|\r]+/", " ", $dataResultados["descripcion"] );
			$nodo_2 .= ",{ id: '".$dataResultados["id"]."', parent: '0', name: '".$descripcion."', color: '#6d16a6' } ";

			$queryIniciativas = mysqli_query($connect_okrs,"SELECT * FROM Okrs_Iniciativas 
			WHERE id_resultado = '".$dataResultados["id"]."' ");
			while($dataIniciativas = mysqli_fetch_array($queryIniciativas)){
				
				$alto_resultados += 65;
				
				$descripcion_2 = preg_replace("/[\r\n|\n|\r]+/", " ", $dataResultados["descripcion"] );
				
				$nodes .= '{ id: '.$dataIniciativas["id"].', pid: '.$dataResultados["id"].', Nombre: "'.$descripcion_2.'", Cargo: "Iniciativas / Actividades", tags: ["azul_claro"] }';
				
				$nodo_2 .= ",{ id: '".$dataIniciativas["id"]."', parent: '".$dataResultados["id"]."', name: '".$descripcion_2."', color: '#8222c2' } ";
			}
			
			if($queryIniciativas->num_rows == 0){ 
				$alto_resultados += 65;
			}
			
		}
	}


	if($alto_resultados > 0){
		$alto = $alto_resultados;
	}

	


	$ARRAY_OKRS_FILTRO_USUARIO = OkrsFiltro( $dtEmpleado["id"], $dtEmpleado['id_empresa'], $connect_okrs, null, null);


?>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/treemap.js"></script>
<script src="https://code.highcharts.com/modules/treegraph.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<style>
	#contenedor_grafica {
		min-width: 360px;
		margin: 0 auto;
		height: <?php echo $alto; ?>px;
	}
</style>

<div class="container-fluid">
	<?php include("views/okrs_equipos/componentes/filtros_cascada.php"); ?>
</div>

<div style="background-color: #ffffff; padding: 30px 0px;">
	<div class="container" id="contenedor_grafica"></div>
</div>


<script>
	
function Filtrar(){
	$("#formulario_filtro").submit();
}
	
const data = [<?php echo $nodo_2; ?>];

//console.log(data);
/*	
const data = [
    {
        id: '0.0',
        parent: '',
        name: 'The World'
    },
    {
        id: '1.3',
        parent: '0.0',
        name: 'Asia'
    },
    {
        id: '1.1',
        parent: '0.0',
        name: 'Africa'
    },
    {
        id: '1.2',
        parent: '0.0',
        name: 'America'
    },
];
*/
	
Highcharts.setOptions({
    colors: ['#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4']
});

Highcharts.chart('contenedor_grafica', {
    title: {
        text: 'Derivación de OKRs'
    },
    series: [
        {
            type: 'treegraph',
            data,
            tooltip: {
                pointFormat: '{point.name}'
            },
            marker: {
                symbol: 'rect',
                width: '30%', 
				fillColor: '#ffffff',
                lineWidth: 2, 
				height: 50,
            },
            borderRadius: 3,
            dataLabels: {
				align: 'left', 
                pointFormat: '{point.name}',
                style: {
					color: '#000000',
                    textOutline: '3px #ffffff',
                    /*whiteSpace: 'nowrap'*/
					height: 100, 
					margin: 20, 
                }
            },
            levels: [
                {
                    level: 1, 
                    levelIsConstant: false, 
                },
                {
                    level: 2,
                    colorByPoint: false, 
                },
                {
                    level: 3, 
					colorByPoint: false, 
                    colorVariation: {
                        key: 'brightness',
                        to: -0.5
                    }
                },
                {
                    level: 4,
                    colorVariation: {
                        key: 'brightness',
                        to: 0.5
                    }
                }
            ]
        }
    ]
});
</script>