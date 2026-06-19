<?php
    include("../../app/connect.php");
    include("../../app/arrays.php");
    include("../../app/functions.php");

    $hoy = date("Y-m-d H:i:s");
    $id_empresa = $_POST["id_empresa"];
    $id_user = $_POST["id_user"];
    $id_kpi = $_POST["id_kpi"];
    $urlRedirect = $_POST["url"];

    //OBTENEMOS TODOS LOS DATOS DEL KPI
    $queryKpis = mysqli_query($connect_kpis, "SELECT * FROM Kpis WHERE id = '".$id_kpi."' ");
    $dataKpis = mysqli_fetch_array($queryKpis);

    if( $dataKpis["tipo_kpi"] == 1 ){
       $txt_tipo = "Estratégico"; 
    }
    if( $dataKpis["tipo_kpi"] == 2 ){
       $txt_tipo = "Táctico"; 
    }
    
    
    $queryComentarios = mysqli_query($connect_kpis, "SELECT * FROM Comentarios_Kpis WHERE id_kpi = '".$id_kpi."' ");
    $queryDocumentos = mysqli_query($connect_kpis, "SELECT * FROM Documentos_Kpis WHERE id_kpi = '".$id_kpi."' "); 
    $queryFrecuencias = mysqli_query($connect_kpis, "SELECT * FROM Frecuencia_Kpis WHERE id_kpi = '".$id_kpi."' ");
    $queryFrecuenciasCol = mysqli_query($connect_kpis, "SELECT * FROM Frecuencia_Kpi_Colaborador WHERE id_kpi = '".$id_kpi."' ");
    $queryColaboradores = mysqli_query($connect_kpis, "SELECT * FROM Kpis_Colaborador WHERE id_kpi = '".$id_kpi."' "); 



    //BLOQUE PARA GUARDAR LA BITACORA
    //BLOQUE PARA GUARDAR LA BITACORA
    //BLOQUE PARA GUARDAR LA BITACORA
    $accion = 'ELIMINAR';
    $descripcion = 'Eliminación de KPIs '.$txt_tipo.': '.$dataKpis["indicador"]." || Comentarios: ".$queryComentarios->num_rows." || Documentos: ".$queryDocumentos->num_rows." || Frecuencias: ".$queryFrecuencias->num_rows." || Frecuencias Colaborador: ".$queryFrecuenciasCol->num_rows." || Colaborador: ".$queryColaboradores->num_rows;



    GuardarAuditoriaKpis( $id_empresa, $id_user, $accion, $descripcion, $id_kpi, $dataKpis["tipo_kpi"] ); 
    //BLOQUE PARA GUARDAR LA BITACORA
    //BLOQUE PARA GUARDAR LA BITACORA
    //BLOQUE PARA GUARDAR LA BITACORA

    mysqli_query($connect_kpis, "DELETE FROM Kpis WHERE id = '".$id_kpi."' ");
    mysqli_query($connect_kpis, "DELETE FROM Comentarios_Kpis WHERE id_kpi = '".$id_kpi."' ");
    mysqli_query($connect_kpis, "DELETE FROM Documentos_Kpis WHERE id_kpi = '".$id_kpi."' "); 
    mysqli_query($connect_kpis, "DELETE FROM Frecuencia_Kpis WHERE id_kpi = '".$id_kpi."' ");
    mysqli_query($connect_kpis, "DELETE FROM Frecuencia_Kpi_Colaborador WHERE id_kpi = '".$id_kpi."' ");
    mysqli_query($connect_kpis, "DELETE FROM Kpis_Colaborador WHERE id_kpi = '".$id_kpi."' "); 

?>

<script>
    window.location = "<?php echo $urlRedirect; ?>";
</script>