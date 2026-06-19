<?php
    include("../../app/connect.php");
    include("../../app/arrays.php");
    include("../../app/functions.php");

    $hoy = date("Y-m-d H:i:s");
    $id_empresa = $_POST["id_empresa"];
    $id_user = $_POST["id_user"];
    $id_resultado = $_POST["id_resultado"];
    $urlRedirect = $_POST["url"];

    $queryResultado = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Resultados WHERE id = '".$id_resultado."' ");
    $dataResultado = mysqli_fetch_array($queryResultado);

    //OBTENEMOS TODOS LOS DATOS DEL KPI
    $queryOkrs = mysqli_query($connect_okrs, "SELECT * FROM Okrs WHERE id = '".$dataResultado["id_okrs"]."' ");
    $dataOkrs = mysqli_fetch_array($queryOkrs);

    $id_okr = $dataResultado["id_okrs"];

    mysqli_query($connect_okrs, " DELETE FROM Okrs_Resultados WHERE id = '".$id_resultado."' ");


    //$queryComentarios = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Comentarios WHERE id_okrs = '".$id_okr."' ");














    

    /*
    if( $dataKpis["tipo_kpi"] == 1 ){
       $txt_tipo = "Estratégico"; 
    }
    if( $dataKpis["tipo_kpi"] == 2 ){
       $txt_tipo = "Táctico"; 
    }
       */
    
    
    //$queryResultados = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Resultados WHERE id_okrs = '".$id_okr."' ");
    //$queryActividades = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Actividades WHERE id_okrs = '".$id_okr."' ");
    
    
    //$queryDocumentos = mysqli_query($connect_okrs, "SELECT * FROM Documentos_Kpis WHERE id_okrs = '".$id_okr."' "); 
    //$queryFrecuencias = mysqli_query($connect_okrs, "SELECT * FROM Frecuencia_Kpis WHERE id_okrs = '".$id_okr."' ");


    /*
    $queryFrecuenciasCol = mysqli_query($connect_okrs, "SELECT * FROM Frecuencia_Kpi_Colaborador WHERE id_okrs = '".$id_okr."' ");
    $queryColaboradores = mysqli_query($connect_okrs, "SELECT * FROM Kpis_Colaborador WHERE id_okrs = '".$id_okr."' "); 
    */


    //BLOQUE PARA GUARDAR LA BITACORA
    //BLOQUE PARA GUARDAR LA BITACORA
    //BLOQUE PARA GUARDAR LA BITACORA
    $accion = 'ELIMINAR';
    $descripcion = 'Eliminación de KR: '.$dataResultado["descripcion"]." || ".' para el OKRs: '.$dataOkrs["objetivo_okr"];


    GuardarAuditoriaOkrs( $id_empresa, $id_user, $accion, $descripcion, $id_okr, $id_resultado, 0, 0 );
    //GuardarAuditoriaKpis( $id_empresa, $id_user, $accion, $descripcion, $id_okr, $dataKpis["tipo_kpi"] ); 
    //BLOQUE PARA GUARDAR LA BITACORA
    //BLOQUE PARA GUARDAR LA BITACORA
    //BLOQUE PARA GUARDAR LA BITACORA


    //mysqli_query($connect_okrs, " DELETE FROM Okrs WHERE id = '".$id_okr."' " );
    //
    //mysqli_query($connect_okrs, " DELETE FROM Okrs_Actividades WHERE id_okrs = '".$id_okr."' ");
    
    /*
    
    mysqli_query($connect_okrs, "DELETE FROM Documentos_Kpis WHERE id_okrs = '".$id_okr."' "); 
    mysqli_query($connect_okrs, "DELETE FROM Frecuencia_Kpis WHERE id_okrs = '".$id_okr."' ");
    mysqli_query($connect_okrs, "DELETE FROM Frecuencia_Kpi_Colaborador WHERE id_okrs = '".$id_okr."' ");
    mysqli_query($connect_okrs, "DELETE FROM Kpis_Colaborador WHERE id_okrs = '".$id_okr."' "); 
    */

?>

<script>
    window.location = "<?php echo $urlRedirect; ?>";
</script>