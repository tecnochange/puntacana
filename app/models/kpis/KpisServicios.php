<?php

class KpisServicios{

    public $datos_colaboradores = [];
    public $datos_areas = [];
    public $datos_unidades = [];

    //CARGAMOS A TODOS LOS COLABORADORES PARA USARLOS CON UNA SOLA CONSULTA
    public function __construct($id_empresa) {
        $this->datos_colaboradores = $this->lista_empleados($id_empresa);
        $this->datos_areas = $this->lista_areas($id_empresa);
        $this->datos_unidades = $this->lista_unidades_organizativas($id_empresa);
        
    }

    //AUDITORIAS
    public function kpis_auditoria($id_empresa){
        global $connect_kpis;
        $array = array();

        $sentencia = "
        SELECT
           Auditoria_Kpi.*,
           Kpis.indicador as indicador
        FROM
            Auditoria_Kpi
        LEFT JOIN
            Kpis ON Kpis.id = Auditoria_Kpi.id_kpi
        WHERE
            Auditoria_Kpi.id_empresa = '$id_empresa'
        ORDER BY
            Auditoria_Kpi.created_at ASC
        ";

        $query = mysqli_query($connect_kpis, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {

            //COLOR DE ACCION
            switch ($data["accion"]) {
                case 'CREACIÓN':
                    $color = 'green';
                    break;
                case 'ACTUALIZAR':
                    $color = 'orange';
                    break;
                case 'ELIMINAR':
                    $color = 'red';
                    break;
            }
            $data["color_accion"] = $color;

            //EMPLEADO
            $empleado = $this->DatosEmpleado($data["id_empleado"]);
            $data["empleado"] = $empleado;

            array_push($array, $data);
        }

        return $array;
    }

    //LISTA DE VICEPRESIDENCIAS
    //LISTA DE VICEPRESIDENCIAS
    //LISTA DE VICEPRESIDENCIAS
    public function vicepresidencias_lista($id_empresa){
        global $connect_admin;
        $array = array();

        $sentencia ="
        SELECT
            Vicepresidencia.*,
            GROUP_CONCAT(DISTINCT Lideres_Vicepresidencia.id_lider) AS id_lideres,
            Estructura_Empresa.area AS id_area,
            Estructura_Empresa.id AS id_subproceso
        FROM
            Vicepresidencia
        LEFT JOIN Lideres_Vicepresidencia ON Lideres_Vicepresidencia.id_vicepresidencia = Vicepresidencia.id
        LEFT JOIN Estructura_Empresa ON Estructura_Empresa.vicepresidencia = Vicepresidencia.id
        WHERE
            Vicepresidencia.id_empresa = '".$id_empresa."'
            AND Vicepresidencia.estado = 1
        GROUP BY
            Vicepresidencia.id
        ORDER BY
            Vicepresidencia.nombre ASC";
        $query = mysqli_query($connect_admin, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {

            $lideres = explode(',', $data["id_lideres"]);

            $array_lideres = array();
            foreach( $lideres as $lider){
                $datos_lider = $this->DatosEmpleado( $lider);
                array_push($array_lideres, $datos_lider);
            }

            $count_areas = 0;
            $count_kpis_asignados = 0;
            $progreso_vicepresidencia = 0;
            $areas = $this->areas_vicepresidencia_lista($id_empresa, $data["id"]);
            foreach($areas as $area){
                $count_kpis_asignados += $area["kpis_cantidad"];
                $progreso_vicepresidencia +=  $area["avance_area"];
                $count_areas++;
            }
            $data["kpis_asignados"] = $count_kpis_asignados;
             
            if($progreso_vicepresidencia > 0){
                $progreso_vicepresidencia = round(($progreso_vicepresidencia/$count_areas),2);
            }

            $data["areas_lista"] = $areas;
            $data["array_lideres"] = $array_lideres;
            $data["avance_vicepresidencia"] = $progreso_vicepresidencia;
            array_push($array, $data);
        }
        return $array;
    }

    //LISTA DE VICEPRESIDENCIAS
    //LISTA DE VICEPRESIDENCIAS
    //LISTA DE VICEPRESIDENCIAS
    public function vicepresidencias_lista_relaciones($id_empresa, $array_vicepresidencia){
        global $connect_admin;
        $array = array();

        //OBTENEMOS LAS VICEPRESIDENCIAS Y AREAS
        $id_vicepresidencias =  implode(",", $array_vicepresidencia);
        $id_areas =  implode(",", $array_areas ?? []);


        $filtros = " AND Vicepresidencia.id IN (".$id_vicepresidencias.") ";
        if($id_vicepresidencias > 0){
            $filtros .= " AND Vicepresidencia.id IN (".$id_vicepresidencias.") ";
        }
        if($id_areas != ""){
            //$filtros .= " AND Kpis.area_proceso IN ( ".$id_areas." ) ";
        }

        $sentencia ="
        SELECT
            Vicepresidencia.*,
            GROUP_CONCAT(DISTINCT Lideres_Vicepresidencia.id_lider) AS id_lideres,
            Estructura_Empresa.area AS id_area
        FROM
            Vicepresidencia
        LEFT JOIN Lideres_Vicepresidencia ON Lideres_Vicepresidencia.id_vicepresidencia = Vicepresidencia.id
        LEFT JOIN Estructura_Empresa ON Estructura_Empresa.vicepresidencia = Vicepresidencia.id
        WHERE
            Vicepresidencia.id_empresa = '".$id_empresa."'
            AND Vicepresidencia.estado = 1 
            ".$filtros."
        GROUP BY
            Vicepresidencia.id
        ORDER BY
            Vicepresidencia.nombre ASC";

        $query = mysqli_query($connect_admin, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {

            $lideres = explode(',', $data["id_lideres"]);

            $array_lideres = array();
            foreach( $lideres as $lider){
                $datos_lider = $this->DatosEmpleado( $lider);
                array_push($array_lideres, $datos_lider);
            }

            $count_areas = 0;
            $count_kpis_asignados = 0;
            $progreso_vicepresidencia = 0;
            $areas = $this->areas_vicepresidencia_lista($id_empresa, $data["id"]);
            foreach($areas as $area){
                $count_kpis_asignados += $area["kpis_cantidad"];
                $progreso_vicepresidencia +=  $area["avance_area"];
                $count_areas++;
            }
            $data["kpis_asignados"] = $count_kpis_asignados;
             
            if($progreso_vicepresidencia > 0){
                $progreso_vicepresidencia = round(($progreso_vicepresidencia/$count_areas),2);
            }

            $data["areas_lista"] = $areas;
            $data["array_lideres"] = $array_lideres;
            $data["avance_vicepresidencia"] = $progreso_vicepresidencia;
            array_push($array, $data);
        }
        return $array;
    }

    //LISTA DE AREAS POR VICEPRESIDENCIA
    //LISTA DE AREAS POR VICEPRESIDENCIA
    //LISTA DE AREAS POR VICEPRESIDENCIA
    public function areas_vicepresidencia_lista($id_empresa, $id_viceprecidencia){

        $filtros = "";

        if($_SESSION["anio_fill"]){ $filtros .= " AND Kpis.anio = '".$_SESSION["anio_fill"]."' "; } 
        if($_SESSION["tipo_kpi_fill"]){ $filtros .= " AND Kpis.tipo_kpi = '".$_SESSION["tipo_kpi_fill"]."' "; }
        if($_SESSION["frecuencia_fill"]){ $filtros .= " AND Kpis.frecuencia = '".$_SESSION["frecuencia_fill"]."' "; } 
        if($_SESSION["tipo_resultado_fill"]){ $filtros .= " AND Kpis.tipo_resultado = '".$_SESSION["tipo_resultado_fill"]."' "; }
        if($_SESSION["tipo_calculo_fill"]){ $filtros .= " AND Kpis.tipo_calculo = '".$_SESSION["tipo_calculo_fill"]."' "; }
        if($_SESSION["unidad_medida_fill"]){ $filtros .= " AND Kpis.unidad_medida = '".$_SESSION["unidad_medida_fill"]."' "; }
        

        global $connect_kpis;
        $array = array();

        $sentencia = "
            SELECT 
                Kpis.id AS id_kpi, 
                Kpis.area_proceso AS area_proceso, 
                Kpis.area_macro AS area_macro, 
                goforagile_admin.Areas.nombre as nombre_area,
                goforagile_admin.Lideres_Area.id_lider as id_lider
            FROM
                Kpis
            LEFT JOIN
                goforagile_admin.Areas ON goforagile_admin.Areas.id = Kpis.area_proceso
            LEFT JOIN
                goforagile_admin.Lideres_Area ON goforagile_admin.Lideres_Area.id_area = Kpis.area_proceso
            
            WHERE
                Kpis.id_empresa = '".$id_empresa."'
                AND Kpis.area_macro = '".$id_viceprecidencia."' 
                ".$filtros."
            GROUP BY
                Kpis.area_proceso
        ";
        $query = mysqli_query($connect_kpis, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {
            
            $kpis_lista = $this->kpis_area($id_empresa, $data["area_proceso"], $data["area_macro"] );

            $kpis_cantidad = 0;
            $avance_area = 0;
            
            foreach($kpis_lista as $kpi){
                $avance_area += $kpi["avance_kpis"];
                $kpis_cantidad++;
            }

            $avance_total_area = 0;
            if($avance_area != 0){
                $avance_total_area = $avance_area/$kpis_cantidad;
            }

            if($avance_total_area > 0){
                $avance_total_area = round($avance_total_area, 2);
            }

            $data["kpis_lista"] = $kpis_lista;
            $data["kpis_cantidad"] = $kpis_cantidad;

            
            $data["avance_area"] = $avance_total_area;
            $data["avance_color"] = EscalaColor($data["avance_area"]);

            //DATOS DEL LIDER
            $datos_lider = $this->DatosEmpleado( $data["id_lider"]);
            $data["lider_area"] = $datos_lider;

            array_push($array, $data);
        }
        return $array;

    }

    //TODOS LOS KPIS DE UN AREA
    //TODOS LOS KPIS DE UN AREA
    //TODOS LOS KPIS DE UN AREA
    public function kpis_area( $id_empresa, $id_area, $id_vicepresidencia ){

        $filtros = "";

        if($id_vicepresidencia > 0){
            $filtros .= " AND Kpis.area_macro = '".$id_vicepresidencia."' ";
        }

        if($_SESSION["anio_fill"]){ $filtros .= " AND Kpis.anio = '".$_SESSION["anio_fill"]."' "; }
        if($_SESSION["tipo_kpi_fill"]){ $filtros .= " AND Kpis.tipo_kpi = '".$_SESSION["tipo_kpi_fill"]."' "; }
        if($_SESSION["frecuencia_fill"]){ $filtros .= " AND Kpis.frecuencia = '".$_SESSION["frecuencia_fill"]."' "; } 
        if($_SESSION["tipo_resultado_fill"]){ $filtros .= " AND Kpis.tipo_resultado = '".$_SESSION["tipo_resultado_fill"]."' "; } 
        if($_SESSION["tipo_calculo_fill"]){ $filtros .= " AND Kpis.tipo_calculo = '".$_SESSION["tipo_calculo_fill"]."' "; }
        if($_SESSION["unidad_medida_fill"]){ $filtros .= " AND Kpis.unidad_medida = '".$_SESSION["unidad_medida_fill"]."' "; } 

        if($_SESSION["area_macro_fill"]){ $filtros .= " AND Kpis.area_macro = '".$_SESSION["area_macro_fill"]."' "; }
        if($_SESSION["area_proceso_fill"]){ $filtros .= " AND Kpis.area_proceso = '".$_SESSION["area_proceso_fill"]."' "; }
        if($_SESSION["subproceso_fill"]){ $filtros .= " AND Kpis.subproceso = '".$_SESSION["subproceso_fill"]."' "; }

        

        global $connect_kpis;

        //OBTENEMOS LOS KPIS
        //OBTENEMOS LOS KPIS
        //OBTENEMOS LOS KPIS
        $array_kpis = array();

        $sentencia = " 
        SELECT
            Kpis.id AS id_kpi,
            Kpis.area_macro, 
            Kpis.tipo_kpi,
            Kpis.unidad_medida,  
            Kpis.meta, 
            Kpis.subproceso,
            goforagile_admin.Estructura_Empresa.unidad_organizativa as nombre_unidad, 
            Kpis.tipo_calculo, 
            Kpis.tipo_resultado, 
            Kpis.indicador, 
            Kpis.objetivo_indicador, 
            Kpis.formula, 
            Kpis.frecuencia, 
            Frecuencia_Kpis.tipo, 
            Frecuencia_Kpis.enero, Frecuencia_Kpis.avance_1, 
            Frecuencia_Kpis.febrero, Frecuencia_Kpis.avance_2, 
            Frecuencia_Kpis.marzo, Frecuencia_Kpis.avance_3, 
            Frecuencia_Kpis.abril, Frecuencia_Kpis.avance_4, 
            Frecuencia_Kpis.mayo, Frecuencia_Kpis.avance_5, 
            Frecuencia_Kpis.junio, Frecuencia_Kpis.avance_6, 
            Frecuencia_Kpis.julio, Frecuencia_Kpis.avance_7, 
            Frecuencia_Kpis.agosto, Frecuencia_Kpis.avance_8, 
            Frecuencia_Kpis.septiembre, Frecuencia_Kpis.avance_9, 
            Frecuencia_Kpis.octubre, Frecuencia_Kpis.avance_10, 
            Frecuencia_Kpis.noviembre, Frecuencia_Kpis.avance_11, 
            Frecuencia_Kpis.diciembre, Frecuencia_Kpis.avance_12
        FROM
            Kpis
        INNER JOIN Frecuencia_Kpis ON Frecuencia_Kpis.id_kpi = Kpis.id
        LEFT JOIN goforagile_admin.Estructura_Empresa ON goforagile_admin.Estructura_Empresa.id = Kpis.subproceso
        WHERE
            Kpis.id_empresa = ".$id_empresa." 
            AND Kpis.area_proceso = '".$id_area."' 
            ".$filtros."
        ORDER BY
            Kpis.indicador ASC
        ";
        
        $queryKpis = mysqli_query($connect_kpis, $sentencia);
        while ($dataKpis = mysqli_fetch_assoc($queryKpis)) {

            $dataKpis["avance_1"] = trim($dataKpis["avance_1"]);
            $dataKpis["avance_2"] = trim($dataKpis["avance_2"]);
            $dataKpis["avance_3"] = trim($dataKpis["avance_3"]);
            $dataKpis["avance_4"] = trim($dataKpis["avance_4"]);
            $dataKpis["avance_5"] = trim($dataKpis["avance_5"]);
            $dataKpis["avance_6"] = trim($dataKpis["avance_6"]);
            $dataKpis["avance_7"] = trim($dataKpis["avance_7"]);
            $dataKpis["avance_8"] = trim($dataKpis["avance_8"]);
            $dataKpis["avance_9"] = trim($dataKpis["avance_9"]);
            $dataKpis["avance_10"] = trim($dataKpis["avance_10"]);
            $dataKpis["avance_11"] = trim($dataKpis["avance_11"]); 
            $dataKpis["avance_12"] = trim($dataKpis["avance_12"]);

            //TIPO KPIS
            $txt_tipo = 'Estratégico';
            if($dataKpis["tipo_kpi"] == 2 ){
                $txt_tipo = 'Táctico';
            }
            $dataKpis["tipo_txt"] = $txt_tipo;

            //TIPO RESULTADO
            $txt_tipo_resultado = "";
            if($dataKpis["tipo_resultado"] == 1 ){ 
                $txt_tipo_resultado = "No Acumulativo";
            }
            if($dataKpis["tipo_resultado"] == 3 ){ 
                $txt_tipo_resultado = "Acumulativo";
            }
            $dataKpis["tipo_resultado_txt"] = $txt_tipo_resultado;

            //TIPO CALCULO
            $txt_tipo_calculo = "";
            if($dataKpis["tipo_calculo"] == 1 ){ 
                $txt_tipo_calculo = "Ascendente";
            }
            if($dataKpis["tipo_calculo"] == 2 ){ 
                $txt_tipo_calculo = "Descendente";
            }
            if($dataKpis["tipo_calculo"] == 3 ){ 
                $txt_tipo_calculo = "Valor Absoluto";
            }
            $dataKpis["tipo_calculo_txt"] = $txt_tipo_calculo;

            //UNIDAD DE MEDIDA
            $txt_unidad_medida = $this->UnidadMedidaText($dataKpis["unidad_medida"]);
            $dataKpis["unidad_medida_txt"] = $txt_unidad_medida;

            //AVANCE DEL KPIS
            $avance = $this->AvanceKPI_2($dataKpis);
            $dataKpis["color_avance_kpis"] = EscalaColor($avance["avance_porcentaje"]);

            //print_r(EscalaColor($avance));

            //echo $dataKpis["indicador"]." || ".$avance;
            //echo "<br>";

            $dataKpis["avance_kpis"] = $avance["avance_porcentaje"]; 
            $dataKpis["avance_plano_kpis"] = $avance["avance_numero"];

            array_push($array_kpis, $dataKpis);
        }

        //echo "<hr>";

        return $array_kpis;
    }
    public function kpis_area_paginado( $id_empresa, $id_area, $id_vicepresidencia, $posicion, $longitud ){

        $filtros = "";

        if($id_vicepresidencia > 0){
            $filtros .= " AND Kpis.area_macro = '".$id_vicepresidencia."' ";
        }

        if($_SESSION["anio_fill"]){ $filtros .= " AND Kpis.anio = '".$_SESSION["anio_fill"]."' "; }
        if($_SESSION["tipo_kpi_fill"]){ $filtros .= " AND Kpis.tipo_kpi = '".$_SESSION["tipo_kpi_fill"]."' "; }
        if($_SESSION["frecuencia_fill"]){ $filtros .= " AND Kpis.frecuencia = '".$_SESSION["frecuencia_fill"]."' "; } 
        if($_SESSION["tipo_resultado_fill"]){ $filtros .= " AND Kpis.tipo_resultado = '".$_SESSION["tipo_resultado_fill"]."' "; } 
        if($_SESSION["tipo_calculo_fill"]){ $filtros .= " AND Kpis.tipo_calculo = '".$_SESSION["tipo_calculo_fill"]."' "; }
        if($_SESSION["unidad_medida_fill"]){ $filtros .= " AND Kpis.unidad_medida = '".$_SESSION["unidad_medida_fill"]."' "; } 

        if($_SESSION["area_macro_fill"]){ $filtros .= " AND Kpis.area_macro = '".$_SESSION["area_macro_fill"]."' "; }
        if($_SESSION["area_proceso_fill"]){ $filtros .= " AND Kpis.area_proceso = '".$_SESSION["area_proceso_fill"]."' "; }
        if($_SESSION["subproceso_fill"]){ $filtros .= " AND Kpis.subproceso = '".$_SESSION["subproceso_fill"]."' "; }

        

        global $connect_kpis;

        //OBTENEMOS LOS KPIS
        //OBTENEMOS LOS KPIS
        //OBTENEMOS LOS KPIS
        $array_kpis = array();

        $sentencia = " 
        SELECT
            Kpis.id AS id_kpi,
            Kpis.area_macro, 
            Kpis.tipo_kpi,
            Kpis.unidad_medida,  
            Kpis.meta, 
            Kpis.subproceso,
            goforagile_admin.Estructura_Empresa.unidad_organizativa as nombre_unidad, 
            Kpis.tipo_calculo, 
            Kpis.tipo_resultado, 
            Kpis.indicador, 
            Kpis.objetivo_indicador, 
            Kpis.formula, 
            Kpis.frecuencia, 
            Frecuencia_Kpis.tipo, 
            Frecuencia_Kpis.enero, Frecuencia_Kpis.avance_1, 
            Frecuencia_Kpis.febrero, Frecuencia_Kpis.avance_2, 
            Frecuencia_Kpis.marzo, Frecuencia_Kpis.avance_3, 
            Frecuencia_Kpis.abril, Frecuencia_Kpis.avance_4, 
            Frecuencia_Kpis.mayo, Frecuencia_Kpis.avance_5, 
            Frecuencia_Kpis.junio, Frecuencia_Kpis.avance_6, 
            Frecuencia_Kpis.julio, Frecuencia_Kpis.avance_7, 
            Frecuencia_Kpis.agosto, Frecuencia_Kpis.avance_8, 
            Frecuencia_Kpis.septiembre, Frecuencia_Kpis.avance_9, 
            Frecuencia_Kpis.octubre, Frecuencia_Kpis.avance_10, 
            Frecuencia_Kpis.noviembre, Frecuencia_Kpis.avance_11, 
            Frecuencia_Kpis.diciembre, Frecuencia_Kpis.avance_12
        FROM
            Kpis
        INNER JOIN Frecuencia_Kpis ON Frecuencia_Kpis.id_kpi = Kpis.id
        LEFT JOIN goforagile_admin.Estructura_Empresa ON goforagile_admin.Estructura_Empresa.id = Kpis.subproceso
        WHERE
            Kpis.id_empresa = ".$id_empresa." 
            AND Kpis.area_proceso = '".$id_area."' 
            ".$filtros."
        ORDER BY
            Kpis.indicador ASC
        LIMIT ".$posicion.", ".$longitud."
        ";
        
        $queryKpis = mysqli_query($connect_kpis, $sentencia);
        while ($dataKpis = mysqli_fetch_assoc($queryKpis)) {

            $dataKpis["avance_1"] = trim($dataKpis["avance_1"]);
            $dataKpis["avance_2"] = trim($dataKpis["avance_2"]);
            $dataKpis["avance_3"] = trim($dataKpis["avance_3"]);
            $dataKpis["avance_4"] = trim($dataKpis["avance_4"]);
            $dataKpis["avance_5"] = trim($dataKpis["avance_5"]);
            $dataKpis["avance_6"] = trim($dataKpis["avance_6"]);
            $dataKpis["avance_7"] = trim($dataKpis["avance_7"]);
            $dataKpis["avance_8"] = trim($dataKpis["avance_8"]);
            $dataKpis["avance_9"] = trim($dataKpis["avance_9"]);
            $dataKpis["avance_10"] = trim($dataKpis["avance_10"]);
            $dataKpis["avance_11"] = trim($dataKpis["avance_11"]); 
            $dataKpis["avance_12"] = trim($dataKpis["avance_12"]);

            //TIPO KPIS
            $txt_tipo = 'Estratégico';
            if($dataKpis["tipo_kpi"] == 2 ){
                $txt_tipo = 'Táctico';
            }
            $dataKpis["tipo_txt"] = $txt_tipo;

            //TIPO RESULTADO
            $txt_tipo_resultado = "";
            if($dataKpis["tipo_resultado"] == 1 ){ 
                $txt_tipo_resultado = "No Acumulativo";
            }
            if($dataKpis["tipo_resultado"] == 3 ){ 
                $txt_tipo_resultado = "Acumulativo";
            }
            $dataKpis["tipo_resultado_txt"] = $txt_tipo_resultado;

            //TIPO CALCULO
            $txt_tipo_calculo = "";
            if($dataKpis["tipo_calculo"] == 1 ){ 
                $txt_tipo_calculo = "Ascendente";
            }
            if($dataKpis["tipo_calculo"] == 2 ){ 
                $txt_tipo_calculo = "Descendente";
            }
            if($dataKpis["tipo_calculo"] == 3 ){ 
                $txt_tipo_calculo = "Valor Absoluto";
            }
            $dataKpis["tipo_calculo_txt"] = $txt_tipo_calculo;

            //UNIDAD DE MEDIDA
            $txt_unidad_medida = $this->UnidadMedidaText($dataKpis["unidad_medida"]);
            $dataKpis["unidad_medida_txt"] = $txt_unidad_medida;

            //AVANCE DEL KPIS
            $avance = $this->AvanceKPI_2($dataKpis);
            $dataKpis["color_avance_kpis"] = EscalaColor($avance["avance_porcentaje"]);

            //print_r(EscalaColor($avance));

            //echo $dataKpis["indicador"]." || ".$avance;
            //echo "<br>";

            $dataKpis["avance_kpis"] = $avance["avance_porcentaje"]; 
            $dataKpis["avance_plano_kpis"] = $avance["avance_numero"];

            array_push($array_kpis, $dataKpis);
        }

        //echo "<hr>";

        return $array_kpis;
    }

    //AVANCE INDIVIDUAL DEL KPIS
    //AVANCE INDIVIDUAL DEL KPIS
    //AVANCE INDIVIDUAL DEL KPIS
    public function AvanceKPI_2($data){ 

        if($data){

            $data["meta"] = trim($data["meta"]);

            //ULTIMO METAS
            $ultimo_meta = 0;
            $cant_avances = 0;

            //SOLO APLICA PARA UNIDAD DE MEDIDA - Horas-Minutos-Segundos
            //SOLO APLICA PARA UNIDAD DE MEDIDA - Horas-Minutos-Segundos
            //SOLO APLICA PARA UNIDAD DE MEDIDA - Horas-Minutos-Segundos
            if( $data["unidad_medida"] == 4 ){
                //CONVIERTE LAS METAS Y VALOR CALCULABLE
                $data["julio"] = $this->ConvertirHorasMinutosSegundos($data["julio"]);
                $data["agosto"] = $this->ConvertirHorasMinutosSegundos($data["agosto"]);
                $data["septiembre"] = $this->ConvertirHorasMinutosSegundos($data["septiembre"]);
                $data["octubre"] = $this->ConvertirHorasMinutosSegundos($data["octubre"]);
                $data["noviembre"] = $this->ConvertirHorasMinutosSegundos($data["noviembre"]);
                $data["diciembre"] = $this->ConvertirHorasMinutosSegundos($data["diciembre"]);

                $data["enero"] = $this->ConvertirHorasMinutosSegundos($data["enero"]);
                $data["febrero"] = $this->ConvertirHorasMinutosSegundos($data["febrero"]);
                $data["marzo"] = $this->ConvertirHorasMinutosSegundos($data["marzo"]);
                $data["abril"] = $this->ConvertirHorasMinutosSegundos($data["abril"]);
                $data["mayo"] = $this->ConvertirHorasMinutosSegundos($data["mayo"]);
                $data["junio"] = $this->ConvertirHorasMinutosSegundos($data["junio"]);

                //CONVIERTE LOS SEGUIMIENTOS Y VALOR CALCULABLE
                $data["avance_7"] = $this->ConvertirHorasMinutosSegundos($data["avance_7"]);
                $data["avance_8"] = $this->ConvertirHorasMinutosSegundos($data["avance_8"]);
                $data["avance_9"] = $this->ConvertirHorasMinutosSegundos($data["avance_9"]);
                $data["avance_10"] = $this->ConvertirHorasMinutosSegundos($data["avance_10"]);
                $data["avance_11"] = $this->ConvertirHorasMinutosSegundos($data["avance_11"]);
                $data["avance_12"] = $this->ConvertirHorasMinutosSegundos($data["avance_12"]);

                $data["avance_1"] = $this->ConvertirHorasMinutosSegundos($data["avance_1"]);
                $data["avance_2"] = $this->ConvertirHorasMinutosSegundos($data["avance_2"]);
                $data["avance_3"] = $this->ConvertirHorasMinutosSegundos($data["avance_3"]);
                $data["avance_4"] = $this->ConvertirHorasMinutosSegundos($data["avance_4"]);
                $data["avance_5"] = $this->ConvertirHorasMinutosSegundos($data["avance_5"]);
                $data["avance_6"] = $this->ConvertirHorasMinutosSegundos($data["avance_6"]);

                $data["meta"] = $this->ConvertirHorasMinutosSegundos($data["meta"]);
            }

            //SOLO SE TOMAN LAS METAS QUE TIENE UN VALOR DIFERENTE A 0 PARA SUMAR LA CANTIDAD DE SEGUIMIENTOS
            //SOLO SE TOMAN LAS METAS QUE TIENE UN VALOR DIFERENTE A 0 PARA SUMAR LA CANTIDAD DE SEGUIMIENTOS
            

            if($data["julio"] != 0 ){  $ultimo_meta =  $data["julio"]; }
            if($data["agosto"] != 0 ){ $ultimo_meta =  $data["agosto"]; }
            if($data["septiembre"] != 0 ){ $ultimo_meta =  $data["septiembre"]; }
            if($data["octubre"] != 0 ){ $ultimo_meta =  $data["octubre"]; }
            if($data["noviembre"] != 0 ){ $ultimo_meta =  $data["noviembre"]; }
            if($data["diciembre"] != 0 ){ $ultimo_meta =  $data["diciembre"]; }

            if($data["enero"] != 0 ){ $ultimo_meta = $data["enero"]; }
            if($data["febrero"] != 0 ){ $ultimo_meta = $data["febrero"]; }
            if($data["marzo"] != 0 ){ $ultimo_meta = $data["marzo"]; }
            if($data["abril"] != 0 ){ $ultimo_meta = $data["abril"]; }
            if($data["mayo"] != 0 ){ $ultimo_meta = $data["mayo"]; }
            if($data["junio"] != 0 ){ $ultimo_meta = $data["junio"]; }

            //ULTIMO AVANCE
            $ultimo_avance = 0;

            //SOLO SE TOMA EL AVANCE SI EL VALOR ES DIFERENTE DE 0 SUMAR LA CANTIDAD DE AVANCES 
            //SOLO SE TOMA EL AVANCE SI EL VALOR ES DIFERENTE DE 0 SUMAR LA CANTIDAD DE AVANCES 
            

            if(trim($data["avance_7"]) != "" ){ $cant_avances++; $ultimo_avance =  $data["avance_7"]; }
            if(trim($data["avance_8"]) != "" ){ $cant_avances++; $ultimo_avance =  $data["avance_8"]; }
            if(trim($data["avance_9"]) != "" ){ $cant_avances++; $ultimo_avance =  $data["avance_9"]; }
            if(trim($data["avance_10"]) != "" ){ $cant_avances++; $ultimo_avance =  $data["avance_10"]; }
            if(trim($data["avance_11"]) != "" ){ $cant_avances++; $ultimo_avance =  $data["avance_11"]; }
            if(trim($data["avance_12"]) != "" ){ $cant_avances++; $ultimo_avance =  $data["avance_12"]; }

            if(trim($data["avance_1"]) != "" ){ $cant_avances++; $ultimo_avance =  $data["avance_1"]; }
            if(trim($data["avance_2"]) != "" ){ $cant_avances++; $ultimo_avance =  $data["avance_2"]; }
            if(trim($data["avance_3"]) != "" ){ $cant_avances++; $ultimo_avance =  $data["avance_3"]; }
            if(trim($data["avance_4"]) != "" ){ $cant_avances++; $ultimo_avance =  $data["avance_4"]; }
            if(trim($data["avance_5"]) != "" ){ $cant_avances++; $ultimo_avance =  $data["avance_5"]; }
            if(trim($data["avance_6"]) != "" ){ $cant_avances++; $ultimo_avance =  $data["avance_6"]; }

            //VARIABLES LOCALES
            $tipo_calculo = $data["tipo_calculo"];
            $tipo_resultado = $data["tipo_resultado"];
            $unidad_medida = $data["unidad_medida"];

            $cantidad_meses = 0;

            //CASO 2 SOLO TOMA LAS METAS
            $suma_avances = 
                (int)$data["avance_7"] +
                (int)$data["avance_8"] +
                (int)$data["avance_9"] +
                (int)$data["avance_10"] +
                (int)$data["avance_11"] +
                (int)$data["avance_12"] +
                (int)$data["avance_1"] +
                (int)$data["avance_2"] +
                (int)$data["avance_3"] +
                (int)$data["avance_4"] +
                (int)$data["avance_5"] +
                (int)$data["avance_6"]; 

            if( $data["avance_7"] == "" && $data["avance_8"] == "" && $data["avance_9"] == "" && $data["avance_10"] == "" && $data["avance_11"] == "" && $data["avance_12"] == "" && $data["avance_1"] == "" && $data["avance_2"] == "" && $data["avance_3"] == "" && $data["avance_4"] == "" && $data["avance_5"] == "" && $data["avance_6"] == "" ){
                $suma_avances = '';
            }

            //EN CASO DE NO TENER NINGUN AVANCE
           
            $PROGRESO_KPI = 0; 
            $avance_numerico = 0;
            //ACENDENTE
            if($tipo_calculo == 1 && $suma_avances !== ""  ){ 

                $PROGRESO_KPI = 0;
                if($suma_avances > 0){

                    $avance_numerico = $cant_avances == 0 || $cant_avances < 0 ? 0 : $suma_avances/$cant_avances; 

                    //ESTA ES LA FORMULA DE JULIAN
                    $PROGRESO_KPI = $cant_avances == 0 || $cant_avances < 0 ? 0 : $suma_avances/$cant_avances;
                    $PROGRESO_KPI = round($PROGRESO_KPI,2);
                    
                    $PROGRESO_KPI = $data["meta"] == 0 || $data["meta"] < 0 ? 0 : $PROGRESO_KPI*100/$data["meta"];
                    $PROGRESO_KPI = round($PROGRESO_KPI,2);

                    //ACUMULATIVO
                    if($tipo_resultado == 3){ 

                        $avance_numerico = $ultimo_avance; 

                        $PROGRESO_KPI = $ultimo_meta == 0 || $ultimo_meta < 0 ? 0 : $ultimo_avance*100/$ultimo_meta;
                        $PROGRESO_KPI = round($PROGRESO_KPI,2);
                    }
                    
                }

      
            }

            //DESCENDENTE
            if($tipo_calculo == 2 && $suma_avances !== "" ){ 

                $PROM = 0;
                if($suma_avances != 0){
                    //ESTA ES LA FORMULA DE JULIAN
                    $avance_numerico = $cant_avances == 0 || $cant_avances < 0 ? 0 : $suma_avances/$cant_avances; //NUEVO CODIGO ABRIL

                    $PROM = $suma_avances/$cant_avances; 
                    $PROM = round($PROM,2);
                    $PROGRESO_KPI = $data["meta"]*100/$PROM;
                    $PROGRESO_KPI = round($PROGRESO_KPI,2); 

                    //ACUMULATIVO
                    if($tipo_resultado == 3){
                        $PROGRESO_KPI = $ultimo_meta*100/$ultimo_avance;
                        $PROGRESO_KPI = round($PROGRESO_KPI,2);

                        //EN CASO DE QUE EL SEGUIMIENTO SERA MA
                        if($ultimo_avance == 0 ){
                            if($ultimo_meta > $ultimo_avance ){
                                $PROGRESO_KPI = 100;
                            }
                        }
                        $avance_numerico = $ultimo_avance; 
                        //print_r($ultimo_avance);
                    }

                }

                if( ( $suma_avances === 0 || $suma_avances === 0.0 ) && $data["meta"] != 0 ){ 
                    $PROGRESO_KPI = 100; 
                }

                if( $avance_numerico < $data["meta"] ){ 
                    $PROGRESO_KPI = 100; 
                }



               
            }

            //echo $avance_numerico;
            //echo "<br>";
            
            //SI LA META ES 0
            if($data["meta"] == '0' ){
                $PROGRESO_KPI = 0;
                if($suma_avances > 0){
                    $avance = ($suma_avances/$cant_avances);

                    $avance_numerico = ($suma_avances/$cant_avances);

                    $PROGRESO_KPI = max(0, 100 - ($avance * 10));
                    $PROGRESO_KPI = round($PROGRESO_KPI,1);

                    //SOLO PARA LOS CASO DE ACUMULATIVO
                    if($tipo_resultado == 3){
                        $avance_numerico = ($ultimo_avance);

                        $PROGRESO_KPI = max(0, 100 - ($ultimo_avance * 10));
                        $PROGRESO_KPI = round($PROGRESO_KPI,1);

                    }
                }
            }

            //SOLA PARA VALOR ABSOLUTO ACUMULATIVO O NO ACUMULATIVO
            if($tipo_calculo == 3){
                if($ultimo_avance > 0){ $PROGRESO_KPI = 0; $avance_numerico = 0; }
                if($ultimo_avance == 0){ $PROGRESO_KPI = 100; $avance_numerico = 100; }
            }

            //NUEVO PORCENTAJE 
            if ($PROGRESO_KPI > 100) {
                $PROGRESO_KPI = 100;
            }

            if ($PROGRESO_KPI < -100) {
                $PROGRESO_KPI = -100;
            }

            if($data["meta"] === '0' && $PROGRESO_KPI === 0 && $tipo_calculo != 3){
                $PROGRESO_KPI = 100;
            }

            //echo $PROGRESO_KPI;
            //echo "<br>";

            $nodo = array(
                "avance_porcentaje" => $PROGRESO_KPI,
                "avance_numero" => $avance_numerico
            );
            
            return $nodo;

            //$porcentaje_global += $PROGRESO_KPI;
        }

        else{
            return 0;
        }

    }

    //AVANCE UN MES ESPECIFICO
    //AVANCE UN MES ESPECIFICO
    //AVANCE UN MES ESPECIFICO
    public function avance_kpi_mes($val_meta, $val_seguimiento, $kpis){ 

        $val_seguimiento_validar = $val_seguimiento;

        if( $kpis["unidad_medida"] == 4 ){
            //SOLO APLICA PARA UNIDAD DE MEDIDA - Horas-Minutos-Segundos
            //SOLO APLICA PARA UNIDAD DE MEDIDA - Horas-Minutos-Segundos
            //SOLO APLICA PARA UNIDAD DE MEDIDA - Horas-Minutos-Segundos
            $val_meta = $this->ConvertirHorasMinutosSegundos($val_meta);
            $val_seguimiento = $this->ConvertirHorasMinutosSegundos($val_seguimiento);
        }

        $PROGRESO_KPI_MES = 0;
        //ACENDENTE
        if($kpis["tipo_calculo"] == 1 && $val_seguimiento !== ""  ){ 

            if($val_seguimiento != 0){
                //ESTA ES LA FORMULA DE JULIAN
                $PROGRESO_KPI_MES = ($val_seguimiento*100)/$val_meta; 
                $PROGRESO_KPI_MES = round($PROGRESO_KPI_MES,2);

                if($val_meta < 0){
                    $PROGRESO_KPI_MES = ($val_meta*100)/$val_seguimiento; 
                    $PROGRESO_KPI_MES = round($PROGRESO_KPI_MES,2);
                }

                if($val_seguimiento > $val_meta){
                    $PROGRESO_KPI_MES = 100;
                }
            }

            if($val_meta <= 0 && $val_seguimiento == 0){
                $PROGRESO_KPI_MES = 100;
            }

            if($val_meta >= 0 && $val_seguimiento < 0){
                $PROGRESO_KPI_MES = 0;
            }
        }

        //DESCENDENTE
        if($kpis["tipo_calculo"] == 2 && $val_seguimiento !== "" ){ 

                

            if($val_seguimiento > 0){
                $PROGRESO_KPI_MES = ($val_meta*100)/$val_seguimiento; 
                $PROGRESO_KPI_MES = round($PROGRESO_KPI_MES,2);

            }

            if( $val_seguimiento === 0 || $val_seguimiento === 0.0 || $val_seguimiento === "0" || $val_seguimiento === "0.0"  ){ 
                $PROGRESO_KPI_MES = 100; 
            }

            if($val_seguimiento < 0 && ($val_seguimiento < $val_meta ) ){
                $PROGRESO_KPI_MES = 100;

            }

            //SI EL SEGUIMIENTO ES IGUAL A 0 Y MENOR QUE LA META
            if( $val_seguimiento == 0 && ( $val_seguimiento < $val_meta ) ){
                $PROGRESO_KPI_MES = 100;
            }

        }


        //SOLA PARA VALOR ABSOLUTO ACUMULATIVO O NO ACUMULATIVO
        if($kpis["tipo_calculo"] == 3){
            if($val_seguimiento > 0){ $PROGRESO_KPI_MES = 0; }
            if($val_seguimiento == 0){ $PROGRESO_KPI_MES = 100; }
        }

        //NUEVO PORCENTAJE 
        if ($PROGRESO_KPI_MES > 100) {
            $PROGRESO_KPI_MES = 100;
        }

        if ($PROGRESO_KPI_MES < -100) {
            $PROGRESO_KPI_MES = -100;
        }

        if( $val_seguimiento_validar == "" ){
            $PROGRESO_KPI_MES = 0;
        }

        $bg_color = EscalaColor($PROGRESO_KPI_MES);
        $html = '
            <div class="progress">
				<div class="progress-bar bg-success" role="progressbar" style="width: '.round($PROGRESO_KPI_MES).'%; background-color: '.$bg_color.'  !important;" aria-valuenow="'.round($PROGRESO_KPI_MES).'" aria-valuemin="0" aria-valuemax="100"></div>
			</div>
        ';

        $nodo = array(
            "porcentaje" => $PROGRESO_KPI_MES,
            "bg_color" => $bg_color, 
            "html" => $html
        );

        return $nodo;
        
        /*
        else{
            return 0;
        }
        */

    }

    //FUNCTION PARA RETORNAS LAS ESCALAS DE LOS INICIATIVAS
    //FUNCTION PARA RETORNAS LAS ESCALAS DE LOS INICIATIVAS
    //FUNCTION PARA RETORNAS LAS ESCALAS DE LOS INICIATIVAS
    public function datos_consolidado_kpis($id_empresa, $kpis){
        global $connect_admin;

        $queryEscala = mysqli_query($connect_admin, "SELECT * FROM Escala_Medicion WHERE id_empresa = ".$id_empresa." ");
        $dataEscala = mysqli_fetch_array($queryEscala);

        $general_componente = 0;
        $count_componente = 0;

        $aplica_1 = 0;
        $aplica_2 = 0;
        $aplica_3 = 0;
        $aplica_4 = 0;
        $aplica_5 = 0;

        $porcentaje_1 = 0;
        $porcentaje_2 = 0;
        $porcentaje_3 = 0;
        $porcentaje_4 = 0;
        $porcentaje_5 = 0;

        foreach($kpis as $kpi){

            $promedio = round($kpi["avance_kpis"]);

            $general_componente += $promedio;
            $count_componente++;

            if ($promedio >= $dataEscala['porcentaje_uno'] && $promedio < $dataEscala['porcentaje_tres']) {
                $aplica_1++;
            }
            if ($promedio >= $dataEscala['porcentaje_tres'] && $promedio < $dataEscala['porcentaje_cinco']) {
                $aplica_2++;
            }
            if ($promedio >= $dataEscala['porcentaje_cinco'] && $promedio < $dataEscala['porcentaje_siete']) {
                $aplica_3++;
            }
            if ($promedio >= $dataEscala['porcentaje_siete'] && $promedio <= $dataEscala['porcentaje_ocho']) {
                $aplica_4++;
            }
            if ($promedio > $dataEscala['porcentaje_ocho'] ) {
                $aplica_5++;
            }

        }

        $cantidad_total = count($kpis);

        if($aplica_1 > 0){ $porcentaje_1 = $aplica_1*100/$cantidad_total; }
        if($aplica_2 > 0){ $porcentaje_2 = $aplica_2*100/$cantidad_total; }
        if($aplica_3 > 0){ $porcentaje_3 = $aplica_3*100/$cantidad_total; }
        if($aplica_4 > 0){ $porcentaje_4 = $aplica_4*100/$cantidad_total; }
        if($aplica_5 > 0){ $porcentaje_5 = $aplica_5*100/$cantidad_total; }

        $resultado_componente = $general_componente/$count_componente;

        $color_general = EscalaColor($resultado_componente);

        $array = array(
            "promedio_general" => round($resultado_componente, 2), 
            "color_general" => $color_general,
            "cantidad_kpis" => $cantidad_total,
            "cantidad_1" => $aplica_1, 
            "cantidad_2" => $aplica_2,
            "cantidad_3" => $aplica_3,
            "cantidad_4" => $aplica_4, 
            "cantidad_5" => $aplica_5, 
            "porcentaje_1" => $porcentaje_1, 
            "porcentaje_2" => $porcentaje_2,
            "porcentaje_3" => $porcentaje_3,
            "porcentaje_4" => $porcentaje_4, 
            "porcentaje_5" => $porcentaje_5, 
            "color_1" => "#FF0000", 
            "color_2" => "#FFF200", 
            "color_3" => "#95FA03", 
            "color_4" => "#14F209", 
            "color_5" => "#00D30A", 
            "titulo_1" => $dataEscala["titulo_uno"], 
            "titulo_2" => $dataEscala["titulo_tres"],
            "titulo_3" => $dataEscala["titulo_cuatro"],
            "titulo_4" => $dataEscala["titulo_cinco"],
            "titulo_5" => $dataEscala["titulo_seis"]
        );

        return $array; 
    }


    //PARA REFACTORIZAR Y DEJAR EN UNA SOLA CONSULTA
    //TODOS LOS KPIS DE UN COLABORADOR
    //TODOS LOS KPIS DE UN COLABORADOR
    //TODOS LOS KPIS DE UN COLABORADOR
    public function kpis_colaborador( $id_empresa, $id_area, $id_vicepresidencia, $id_colaborador ){ 

        global $connect_kpis;
        global $Array_Frecuencia_PC;

        //print_r( $this->DatosEmpleado($id_colaborador) );

        $filtros = "";

        if($id_area > 0){
            $filtros .= " AND Kpis.area_proceso = '".$id_area."' ";
        }

        if($id_vicepresidencia > 0){
            $filtros .= " AND Kpis.area_macro = '".$id_vicepresidencia."' ";
        }
        
        if(isset($_SESSION["anio_fill"])){ $filtros .= " AND Kpis.anio = '".$_SESSION["anio_fill"]."' "; }
        if(isset($_SESSION["tipo_kpi_fill"])){ $filtros .= " AND Kpis.tipo_kpi = '".$_SESSION["tipo_kpi_fill"]."' "; }
        if(isset($_SESSION["frecuencia_fill"])){ $filtros .= " AND Kpis.frecuencia = '".$_SESSION["frecuencia_fill"]."' "; } 
        if(isset($_SESSION["tipo_resultado_fill"])){ $filtros .= " AND Kpis.tipo_resultado = '".$_SESSION["tipo_resultado_fill"]."' "; } 
        if(isset($_SESSION["tipo_calculo_fill"])){ $filtros .= " AND Kpis.tipo_calculo = '".$_SESSION["tipo_calculo_fill"]."' "; }
        if(isset($_SESSION["unidad_medida_fill"])){ $filtros .= " AND Kpis.unidad_medida = '".$_SESSION["unidad_medida_fill"]."' "; }
        if(isset($_SESSION["area_macro_fill"])){ $filtros .= " AND Kpis.area_macro = '".$_SESSION["area_macro_fill"]."' "; }
        if(isset($_SESSION["area_proceso_fill"])){ $filtros .= " AND Kpis.area_proceso = '".$_SESSION["area_proceso_fill"]."' "; }
        if(isset($_SESSION["subproceso_fill"])){ $filtros .= " AND Kpis.subproceso = '".$_SESSION["subproceso_fill"]."' "; }

        //OBTENEMOS LOS KPIS
        //OBTENEMOS LOS KPIS
        //OBTENEMOS LOS KPIS
        $array_kpis = array();

        $sentencia = " 
        SELECT 
            Kpis.id AS id_kpi,
            Kpis.area_macro, 
            goforagile_admin.Vicepresidencia.nombre AS nombre_area_macro,  
            Kpis.area_proceso,
            goforagile_admin.Areas.nombre AS nombre_area_proceso, 
            Kpis.tipo_kpi,
            Kpis.unidad_medida, 
            Kpis.subproceso AS subproceso,  
            Kpis.meta, 
            Kpis.tipo_calculo, 
            Kpis.tipo_resultado, 
            Kpis.indicador, 
            Kpis.objetivo_indicador, 
            Kpis.formula, 
            Kpis.frecuencia,
            Kpis_Colaborador.tipo as rol_colaborador, 
            Frecuencia_Kpis.tipo, 
            Frecuencia_Kpis.enero, Frecuencia_Kpis.avance_1, 
            Frecuencia_Kpis.febrero, Frecuencia_Kpis.avance_2, 
            Frecuencia_Kpis.marzo, Frecuencia_Kpis.avance_3, 
            Frecuencia_Kpis.abril, Frecuencia_Kpis.avance_4, 
            Frecuencia_Kpis.mayo, Frecuencia_Kpis.avance_5, 
            Frecuencia_Kpis.junio, Frecuencia_Kpis.avance_6, 
            Frecuencia_Kpis.julio, Frecuencia_Kpis.avance_7, 
            Frecuencia_Kpis.agosto, Frecuencia_Kpis.avance_8, 
            Frecuencia_Kpis.septiembre, Frecuencia_Kpis.avance_9, 
            Frecuencia_Kpis.octubre, Frecuencia_Kpis.avance_10, 
            Frecuencia_Kpis.noviembre, Frecuencia_Kpis.avance_11, 
            Frecuencia_Kpis.diciembre, Frecuencia_Kpis.avance_12
        FROM
            Kpis
        INNER JOIN Frecuencia_Kpis ON Frecuencia_Kpis.id_kpi = Kpis.id 
        LEFT JOIN Kpis_Colaborador ON Kpis_Colaborador.id_kpi = Kpis.id 
        LEFT JOIN goforagile_admin.Vicepresidencia ON goforagile_admin.Vicepresidencia.id = Kpis.area_macro
        LEFT JOIN goforagile_admin.Areas ON goforagile_admin.Areas.id = Kpis.area_proceso
        WHERE
            Kpis.id_empresa = ".$id_empresa." 
            AND Kpis_Colaborador.id_colaborador = '".$id_colaborador."' 
            ".$filtros." 
        GROUP BY Kpis.id 
        ORDER BY
            Kpis.indicador ASC
        ";
        
        $queryKpis = mysqli_query($connect_kpis, $sentencia);
        while ($dataKpis = mysqli_fetch_assoc($queryKpis)) {

            $dataKpis["avance_1"] = trim($dataKpis["avance_1"]);
            $dataKpis["avance_2"] = trim($dataKpis["avance_2"]);
            $dataKpis["avance_3"] = trim($dataKpis["avance_3"]);
            $dataKpis["avance_4"] = trim($dataKpis["avance_4"]);
            $dataKpis["avance_5"] = trim($dataKpis["avance_5"]);
            $dataKpis["avance_6"] = trim($dataKpis["avance_6"]);
            $dataKpis["avance_7"] = trim($dataKpis["avance_7"]);
            $dataKpis["avance_8"] = trim($dataKpis["avance_8"]);
            $dataKpis["avance_9"] = trim($dataKpis["avance_9"]);
            $dataKpis["avance_10"] = trim($dataKpis["avance_10"]);
            $dataKpis["avance_11"] = trim($dataKpis["avance_11"]); 
            $dataKpis["avance_12"] = trim($dataKpis["avance_12"]);

            $area = $this->DatosAreas($dataKpis["area_proceso"]);
            $subproceso = $this->DatosUnidades($dataKpis["subproceso"]);

            //TIPO KPIS
            $txt_tipo = 'Estratégico';
            if($dataKpis["tipo_kpi"] == 2 ){
                $txt_tipo = 'Táctico';
            }
            $dataKpis["tipo_txt"] = $txt_tipo;

            //TIPO RESULTADO
            $txt_tipo_resultado = "";
            if($dataKpis["tipo_resultado"] == 1 ){ 
                $txt_tipo_resultado = "No Acumulativo";
            }
            if($dataKpis["tipo_resultado"] == 3 ){ 
                $txt_tipo_resultado = "Acumulativo";
            }
            $dataKpis["tipo_resultado_txt"] = $txt_tipo_resultado;

            //TIPO CALCULO
            $txt_tipo_calculo = "";
            if($dataKpis["tipo_calculo"] == 1 ){ 
                $txt_tipo_calculo = "Ascendente";
            }
            if($dataKpis["tipo_calculo"] == 2 ){ 
                $txt_tipo_calculo = "Descendente";
            }
            if($dataKpis["tipo_calculo"] == 3 ){ 
                $txt_tipo_calculo = "Valor Absoluto";
            }
            $dataKpis["tipo_calculo_txt"] = $txt_tipo_calculo;

            //UNIDAD DE MEDIDA
            $txt_unidad_medida = $this->UnidadMedidaText($dataKpis["unidad_medida"]);
            $dataKpis["unidad_medida_txt"] = $txt_unidad_medida;

            //FRECUENCIA 
            $txt_frecuencia = "";
            foreach ($Array_Frecuencia_PC as $frecuencia) {
                if ($frecuencia[0] == $dataKpis["frecuencia"]) {
                    $txt_frecuencia = $frecuencia[1];
                } 
            }

            
            $dataKpis["txt_frecuencia"] = $txt_frecuencia;
            
            //AVANCE DEL KPIS
            $avance = $this->AvanceKPI_2($dataKpis);
            $dataKpis["color_avance_kpis"] = EscalaColor($avance["avance_porcentaje"]);

            //print_r(EscalaColor($avance));

            //echo $dataKpis["indicador"]." || ".$avance;
            //echo "<br>";

            $dataKpis["avance_kpis"] = $avance["avance_porcentaje"];
            $dataKpis["avance_plano_kpis"] = $avance["avance_numero"];

            $dataKpis["area_txt"] = $area["nombre"]; 
            $dataKpis["subproceso_txt"] = isset($subproceso["unidad_organizativa"]) ? $subproceso["unidad_organizativa"] : "No definido"; 

            $dataKpis["integrantes"] = $this->obtener_integrantes($id_empresa, $dataKpis["id_kpi"]);
            //print_r($integrantes);

            array_push($array_kpis, $dataKpis);
        }

        //echo "<hr>";

        return $array_kpis;
    }

    Public function kpis_colaborador_paginado($id_empresa, $id_area, $id_vicepresidencia, $id_colaborador, $posicion, $longitud){

        global $connect_kpis;
        global $Array_Frecuencia_PC;

        $filtros = "";

        if($id_area > 0){
            $filtros .= " AND Kpis.area_proceso = '".$id_area."' ";
        }

        if($id_vicepresidencia > 0){
            $filtros .= " AND Kpis.area_macro = '".$id_vicepresidencia."' ";
        }
        
        if(isset($_SESSION["anio_fill"])){ $filtros .= " AND Kpis.anio = '".$_SESSION["anio_fill"]."' "; }
        if(isset($_SESSION["tipo_kpi_fill"])){ $filtros .= " AND Kpis.tipo_kpi = '".$_SESSION["tipo_kpi_fill"]."' "; }
        if(isset($_SESSION["frecuencia_fill"])){ $filtros .= " AND Kpis.frecuencia = '".$_SESSION["frecuencia_fill"]."' "; } 
        if(isset($_SESSION["tipo_resultado_fill"])){ $filtros .= " AND Kpis.tipo_resultado = '".$_SESSION["tipo_resultado_fill"]."' "; } 
        if(isset($_SESSION["tipo_calculo_fill"])){ $filtros .= " AND Kpis.tipo_calculo = '".$_SESSION["tipo_calculo_fill"]."' "; }
        if(isset($_SESSION["unidad_medida_fill"])){ $filtros .= " AND Kpis.unidad_medida = '".$_SESSION["unidad_medida_fill"]."' "; }
        if(isset($_SESSION["area_macro_fill"])){ $filtros .= " AND Kpis.area_macro = '".$_SESSION["area_macro_fill"]."' "; }
        if(isset($_SESSION["area_proceso_fill"])){ $filtros .= " AND Kpis.area_proceso = '".$_SESSION["area_proceso_fill"]."' "; }
        if(isset($_SESSION["subproceso_fill"])){ $filtros .= " AND Kpis.subproceso = '".$_SESSION["subproceso_fill"]."' "; }

        //OBTENEMOS LOS KPIS
        //OBTENEMOS LOS KPIS
        //OBTENEMOS LOS KPIS
        $array_kpis = array();

        $sentencia = " 
        SELECT 
            Kpis.id AS id_kpi,
            Kpis.area_macro, 
            goforagile_admin.Vicepresidencia.nombre AS nombre_area_macro,  
            Kpis.area_proceso,
            goforagile_admin.Areas.nombre AS nombre_area_proceso, 
            Kpis.tipo_kpi,
            Kpis.unidad_medida, 
            Kpis.subproceso AS subproceso,  
            Kpis.meta, 
            Kpis.tipo_calculo, 
            Kpis.tipo_resultado, 
            Kpis.indicador, 
            Kpis.objetivo_indicador, 
            Kpis.formula, 
            Kpis.frecuencia,
            Kpis_Colaborador.tipo as rol_colaborador, 
            Frecuencia_Kpis.tipo, 
            Frecuencia_Kpis.enero, Frecuencia_Kpis.avance_1, 
            Frecuencia_Kpis.febrero, Frecuencia_Kpis.avance_2, 
            Frecuencia_Kpis.marzo, Frecuencia_Kpis.avance_3, 
            Frecuencia_Kpis.abril, Frecuencia_Kpis.avance_4, 
            Frecuencia_Kpis.mayo, Frecuencia_Kpis.avance_5, 
            Frecuencia_Kpis.junio, Frecuencia_Kpis.avance_6, 
            Frecuencia_Kpis.julio, Frecuencia_Kpis.avance_7, 
            Frecuencia_Kpis.agosto, Frecuencia_Kpis.avance_8, 
            Frecuencia_Kpis.septiembre, Frecuencia_Kpis.avance_9, 
            Frecuencia_Kpis.octubre, Frecuencia_Kpis.avance_10, 
            Frecuencia_Kpis.noviembre, Frecuencia_Kpis.avance_11, 
            Frecuencia_Kpis.diciembre, Frecuencia_Kpis.avance_12
        FROM
            Kpis
        INNER JOIN Frecuencia_Kpis ON Frecuencia_Kpis.id_kpi = Kpis.id 
        LEFT JOIN Kpis_Colaborador ON Kpis_Colaborador.id_kpi = Kpis.id 
        LEFT JOIN goforagile_admin.Vicepresidencia ON goforagile_admin.Vicepresidencia.id = Kpis.area_macro
        LEFT JOIN goforagile_admin.Areas ON goforagile_admin.Areas.id = Kpis.area_proceso
        WHERE
            Kpis.id_empresa = ".$id_empresa." 
            AND Kpis_Colaborador.id_colaborador = '".$id_colaborador."' 
            ".$filtros." 
        GROUP BY Kpis.id 
        ORDER BY
            Kpis.indicador ASC
        LIMIT ".$posicion.", ".$longitud."
        ";
        
        $queryKpis = mysqli_query($connect_kpis, $sentencia);
        while ($dataKpis = mysqli_fetch_assoc($queryKpis)) {

            $dataKpis["avance_1"] = trim($dataKpis["avance_1"]);
            $dataKpis["avance_2"] = trim($dataKpis["avance_2"]);
            $dataKpis["avance_3"] = trim($dataKpis["avance_3"]);
            $dataKpis["avance_4"] = trim($dataKpis["avance_4"]);
            $dataKpis["avance_5"] = trim($dataKpis["avance_5"]);
            $dataKpis["avance_6"] = trim($dataKpis["avance_6"]);
            $dataKpis["avance_7"] = trim($dataKpis["avance_7"]);
            $dataKpis["avance_8"] = trim($dataKpis["avance_8"]);
            $dataKpis["avance_9"] = trim($dataKpis["avance_9"]);
            $dataKpis["avance_10"] = trim($dataKpis["avance_10"]);
            $dataKpis["avance_11"] = trim($dataKpis["avance_11"]); 
            $dataKpis["avance_12"] = trim($dataKpis["avance_12"]);

            $area = $this->DatosAreas($dataKpis["area_proceso"]);
            $subproceso = $this->DatosUnidades($dataKpis["subproceso"]);

            //TIPO KPIS
            $txt_tipo = 'Estratégico';
            if($dataKpis["tipo_kpi"] == 2 ){
                $txt_tipo = 'Táctico';
            }
            $dataKpis["tipo_txt"] = $txt_tipo;

            //TIPO RESULTADO
            $txt_tipo_resultado = "";
            if($dataKpis["tipo_resultado"] == 1 ){ 
                $txt_tipo_resultado = "No Acumulativo";
            }
            if($dataKpis["tipo_resultado"] == 3 ){ 
                $txt_tipo_resultado = "Acumulativo";
            }
            $dataKpis["tipo_resultado_txt"] = $txt_tipo_resultado;

            //TIPO CALCULO
            $txt_tipo_calculo = "";
            if($dataKpis["tipo_calculo"] == 1 ){ 
                $txt_tipo_calculo = "Ascendente";
            }
            if($dataKpis["tipo_calculo"] == 2 ){ 
                $txt_tipo_calculo = "Descendente";
            }
            if($dataKpis["tipo_calculo"] == 3 ){ 
                $txt_tipo_calculo = "Valor Absoluto";
            }
            $dataKpis["tipo_calculo_txt"] = $txt_tipo_calculo;

            //UNIDAD DE MEDIDA
            $txt_unidad_medida = $this->UnidadMedidaText($dataKpis["unidad_medida"]);
            $dataKpis["unidad_medida_txt"] = $txt_unidad_medida;

            //FRECUENCIA 
            $txt_frecuencia = "";
            foreach ($Array_Frecuencia_PC as $frecuencia) {
                if ($frecuencia[0] == $dataKpis["frecuencia"]) {
                    $txt_frecuencia = $frecuencia[1];
                } 
            }

            
            $dataKpis["txt_frecuencia"] = $txt_frecuencia;
            
            //AVANCE DEL KPIS
            $avance = $this->AvanceKPI_2($dataKpis);
            $dataKpis["color_avance_kpis"] = EscalaColor($avance["avance_porcentaje"]);

            //print_r(EscalaColor($avance));

            //echo $dataKpis["indicador"]." || ".$avance;
            //echo "<br>";

            $dataKpis["avance_kpis"] = $avance["avance_porcentaje"];
            $dataKpis["avance_plano_kpis"] = $avance["avance_numero"];

            $dataKpis["area_txt"] = $area["nombre"]; 
            $dataKpis["subproceso_txt"] = isset($subproceso["unidad_organizativa"]) ? $subproceso["unidad_organizativa"] : "No definido"; 

            $dataKpis["integrantes"] = $this->obtener_integrantes($id_empresa, $dataKpis["id_kpi"]);
            //print_r($integrantes);

            array_push($array_kpis, $dataKpis);
        }

        //echo "<hr>";

        return $array_kpis;
    }

    //PARA REFACTORIZAR Y DEJAR EN UNA SOLA CONSULTA
    //TODOS LOS KPIS DE UN COLABORADOR
    //TODOS LOS KPIS DE UN COLABORADOR
    //TODOS LOS KPIS DE UN COLABORADOR
    public function kpis_empresa( $id_empresa, $id_area, $id_vicepresidencia, $id_colaborador ){

        $filtros = "";
        $responsables = $this->lista_colaboradores_kpis($id_empresa);

        if($id_area > 0){
            $filtros .= " AND Kpis.area_proceso = '".$id_area."' ";
        }

        if($id_vicepresidencia > 0){
            $filtros .= " AND Kpis.area_macro = '".$id_vicepresidencia."' ";
        }
        if($id_colaborador > 0){
            $filtros .= " AND Kpis_Colaborador.id_colaborador = '".$id_colaborador."' ";
        }

        if($_SESSION["anio_fill"]){ $filtros .= " AND Kpis.anio = '".$_SESSION["anio_fill"]."' "; }
        /* if($_SESSION["periodo_inicio_fill"] && $_SESSION["periodo_fin_fill"]){
            $ciclo_inicio = (int)$_SESSION["anio_fill"];
            $periodo_inicio = (int)$_SESSION["periodo_inicio_fill"];
            $periodo_fin = (int)$_SESSION["periodo_fin_fill"];
            $ciclo_fin = $periodo_inicio > $periodo_fin ? $ciclo_inicio + 1 : $ciclo_inicio;

            $filtros .= " AND (Kpis.anio = '$ciclo_inicio' OR Kpis.anio = '$ciclo_fin') ";
        } */

        if($_SESSION["tipo_kpi_fill"]){ $filtros .= " AND Kpis.tipo_kpi = '".$_SESSION["tipo_kpi_fill"]."' "; }
        if($_SESSION["frecuencia_fill"]){ $filtros .= " AND Kpis.frecuencia = '".$_SESSION["frecuencia_fill"]."' "; } 
        if($_SESSION["tipo_resultado_fill"]){ $filtros .= " AND Kpis.tipo_resultado = '".$_SESSION["tipo_resultado_fill"]."' "; } 
        if($_SESSION["tipo_calculo_fill"]){ $filtros .= " AND Kpis.tipo_calculo = '".$_SESSION["tipo_calculo_fill"]."' "; }
        if($_SESSION["unidad_medida_fill"]){ $filtros .= " AND Kpis.unidad_medida = '".$_SESSION["unidad_medida_fill"]."' "; } 

        if($_SESSION["area_macro_fill"]){ $filtros .= " AND Kpis.area_macro = '".$_SESSION["area_macro_fill"]."' "; }
        if($_SESSION["area_proceso_fill"]){ $filtros .= " AND Kpis.area_proceso = '".$_SESSION["area_proceso_fill"]."' "; }
        if($_SESSION["subproceso_fill"]){ $filtros .= " AND Kpis.subproceso = '".$_SESSION["subproceso_fill"]."' "; } 

        if($_SESSION["objetivo_sg_fill"]){ $filtros .= " AND Kpis.objetivo_sg = '".$_SESSION["objetivo_sg_fill"]."' "; }


        

        global $connect_kpis;

        //OBTENEMOS LOS KPIS
        //OBTENEMOS LOS KPIS
        //OBTENEMOS LOS KPIS
        $array_kpis = array();

        $sentencia = " 
        SELECT
            Kpis.id AS id_kpi,
            Kpis.area_macro,
            goforagile_admin.Vicepresidencia.nombre AS nombre_area_macro,  
            Kpis.area_proceso,
            goforagile_admin.Areas.nombre AS nombre_area_proceso,
            Kpis.subproceso, 
            goforagile_admin.Estructura_Empresa.unidad_organizativa AS nombre_subproceso,
            Kpis.tipo_kpi,
            Kpis.unidad_medida,  
            Kpis.meta, 
            Kpis.tipo_calculo, 
            Kpis.tipo_resultado, 
            Kpis.indicador, 
            Kpis.objetivo_indicador, 
            Kpis.formula, 
            Kpis.frecuencia, 
            Frecuencia_Kpis.tipo, 
            Frecuencia_Kpis.enero, Frecuencia_Kpis.avance_1, 
            Frecuencia_Kpis.febrero, Frecuencia_Kpis.avance_2, 
            Frecuencia_Kpis.marzo, Frecuencia_Kpis.avance_3, 
            Frecuencia_Kpis.abril, Frecuencia_Kpis.avance_4, 
            Frecuencia_Kpis.mayo, Frecuencia_Kpis.avance_5, 
            Frecuencia_Kpis.junio, Frecuencia_Kpis.avance_6, 
            Frecuencia_Kpis.julio, Frecuencia_Kpis.avance_7, 
            Frecuencia_Kpis.agosto, Frecuencia_Kpis.avance_8, 
            Frecuencia_Kpis.septiembre, Frecuencia_Kpis.avance_9, 
            Frecuencia_Kpis.octubre, Frecuencia_Kpis.avance_10, 
            Frecuencia_Kpis.noviembre, Frecuencia_Kpis.avance_11, 
            Frecuencia_Kpis.diciembre, Frecuencia_Kpis.avance_12
        FROM
            Kpis
        INNER JOIN
            Frecuencia_Kpis ON Frecuencia_Kpis.id_kpi = Kpis.id 
        LEFT JOIN
            goforagile_admin.Vicepresidencia ON goforagile_admin.Vicepresidencia.id = Kpis.area_macro
        LEFT JOIN
            goforagile_admin.Areas ON goforagile_admin.Areas.id = Kpis.area_proceso
        LEFT JOIN
            goforagile_admin.Estructura_Empresa ON goforagile_admin.Estructura_Empresa.id = Kpis.subproceso
        WHERE
            Kpis.id_empresa = ".$id_empresa." 
            ".$filtros." 
        GROUP BY Kpis.id 
        ORDER BY
            Kpis.indicador ASC 
        ";

        //LEFT JOIN Kpis_Colaborador ON Kpis_Colaborador.id_kpi = Kpis.id
        
        $queryKpis = mysqli_query($connect_kpis, $sentencia);
        while ($dataKpis = mysqli_fetch_assoc($queryKpis)) { 

            $dataKpis["avance_1"] = trim($dataKpis["avance_1"]);
            $dataKpis["avance_2"] = trim($dataKpis["avance_2"]);
            $dataKpis["avance_3"] = trim($dataKpis["avance_3"]);
            $dataKpis["avance_4"] = trim($dataKpis["avance_4"]);
            $dataKpis["avance_5"] = trim($dataKpis["avance_5"]);
            $dataKpis["avance_6"] = trim($dataKpis["avance_6"]);
            $dataKpis["avance_7"] = trim($dataKpis["avance_7"]);
            $dataKpis["avance_8"] = trim($dataKpis["avance_8"]);
            $dataKpis["avance_9"] = trim($dataKpis["avance_9"]);
            $dataKpis["avance_10"] = trim($dataKpis["avance_10"]);
            $dataKpis["avance_11"] = trim($dataKpis["avance_11"]); 
            $dataKpis["avance_12"] = trim($dataKpis["avance_12"]);

            $dataKpis["avance_1"] = trim($dataKpis["avance_1"]);
            $dataKpis["avance_2"] = trim($dataKpis["avance_2"]);
            $dataKpis["avance_3"] = trim($dataKpis["avance_3"]);
            $dataKpis["avance_4"] = trim($dataKpis["avance_4"]);
            $dataKpis["avance_5"] = trim($dataKpis["avance_5"]);
            $dataKpis["avance_6"] = trim($dataKpis["avance_6"]);
            $dataKpis["avance_7"] = trim($dataKpis["avance_7"]);
            $dataKpis["avance_8"] = trim($dataKpis["avance_8"]);
            $dataKpis["avance_9"] = trim($dataKpis["avance_9"]);
            $dataKpis["avance_10"] = trim($dataKpis["avance_10"]);
            $dataKpis["avance_11"] = trim($dataKpis["avance_11"]); 
            $dataKpis["avance_12"] = trim($dataKpis["avance_12"]);
            
            //TIPO KPIS
            $txt_tipo = 'Estratégico';
            if($dataKpis["tipo_kpi"] == 2 ){
                $txt_tipo = 'Táctico';
            }
            $dataKpis["tipo_txt"] = $txt_tipo;

            //TIPO RESULTADO
            $txt_tipo_resultado = "";
            if($dataKpis["tipo_resultado"] == 1 ){ 
                $txt_tipo_resultado = "No Acumulativo";
            }
            if($dataKpis["tipo_resultado"] == 3 ){ 
                $txt_tipo_resultado = "Acumulativo";
            }
            $dataKpis["tipo_resultado_txt"] = $txt_tipo_resultado;

            //TIPO CALCULO
            $txt_tipo_calculo = "";
            if($dataKpis["tipo_calculo"] == 1 ){ 
                $txt_tipo_calculo = "Ascendente";
            }
            if($dataKpis["tipo_calculo"] == 2 ){ 
                $txt_tipo_calculo = "Descendente";
            }
            if($dataKpis["tipo_calculo"] == 3 ){ 
                $txt_tipo_calculo = "Valor Absoluto";
            }
            $dataKpis["tipo_calculo_txt"] = $txt_tipo_calculo;

            //UNIDAD DE MEDIDA
            $txt_unidad_medida = $this->UnidadMedidaText($dataKpis["unidad_medida"]);
            $dataKpis["unidad_medida_txt"] = $txt_unidad_medida;

            //AVANCE DEL KPIS
            $avance = $this->AvanceKPI_2($dataKpis);
            $dataKpis["color_avance_kpis"] = EscalaColor($avance["avance_porcentaje"]);

            $dataKpis["avance_kpis"] = $avance["avance_porcentaje"];
            $dataKpis["avance_plano_kpis"] = $avance["avance_numero"];

            array_push($array_kpis, $dataKpis);
        }

        //echo "<hr>";

        return $array_kpis;
    }

    //PARA REFACTORIZAR Y DEJAR EN UNA SOLA CONSULTA
    //TODOS LOS KPIS DE UN COLABORADOR
    //TODOS LOS KPIS DE UN COLABORADOR
    //TODOS LOS KPIS DE UN COLABORADOR
    public function kpis_empresa_paginado( $id_empresa, $posicion, $longitud ){

        $filtros = "";
        $responsables = $this->lista_colaboradores_kpis($id_empresa);

        
        if($_SESSION["tipo_kpi_fill"]){ $filtros .= " AND Kpis.tipo_kpi = '".$_SESSION["tipo_kpi_fill"]."' "; }
        if($_SESSION["frecuencia_fill"]){ $filtros .= " AND Kpis.frecuencia = '".$_SESSION["frecuencia_fill"]."' "; } 
        if($_SESSION["tipo_resultado_fill"]){ $filtros .= " AND Kpis.tipo_resultado = '".$_SESSION["tipo_resultado_fill"]."' "; } 
        if($_SESSION["tipo_calculo_fill"]){ $filtros .= " AND Kpis.tipo_calculo = '".$_SESSION["tipo_calculo_fill"]."' "; }
        if($_SESSION["unidad_medida_fill"]){ $filtros .= " AND Kpis.unidad_medida = '".$_SESSION["unidad_medida_fill"]."' "; } 

        if($_SESSION["area_macro_fill"]){ $filtros .= " AND Kpis.area_macro = '".$_SESSION["area_macro_fill"]."' "; }
        if($_SESSION["area_proceso_fill"]){ $filtros .= " AND Kpis.area_proceso = '".$_SESSION["area_proceso_fill"]."' "; }
        if($_SESSION["subproceso_fill"]){ $filtros .= " AND Kpis.subproceso = '".$_SESSION["subproceso_fill"]."' "; }
        if($_SESSION["objetivo_sg_fill"]){ $filtros .= " AND Kpis.objetivo_sg = '".$_SESSION["objetivo_sg_fill"]."' "; } 

        if($_SESSION["anio_fill"]){ $filtros .= " AND Kpis.anio = '".$_SESSION["anio_fill"]."' "; }
        /* if($_SESSION["periodo_inicio_fill"] && $_SESSION["periodo_fin_fill"]){
            $ciclo_inicio = (int)$_SESSION["anio_fill"];
            $periodo_inicio = (int)$_SESSION["periodo_inicio_fill"];
            $periodo_fin = (int)$_SESSION["periodo_fin_fill"];
            $ciclo_fin = $periodo_inicio > $periodo_fin ? $ciclo_inicio + 1 : $ciclo_inicio;

            echo "Mes Inicio: ".$mes[$periodo_inicio]." Ciclo: ".$ciclo_inicio;
            echo "<br>";
            echo "Mes Fin: ".$mes[$periodo_fin]." Ciclo: ".$ciclo_fin;
            echo "<br>";

            $filtros .= " AND (Kpis.anio = '$ciclo_inicio' OR Kpis.anio = '$ciclo_fin') ";
        } */

        global $connect_kpis;

        //OBTENEMOS LOS KPIS
        $array_kpis = array();

        $sentencia = " 
        SELECT
            Kpis.id AS id_kpi,
            Kpis.area_macro,
            goforagile_admin.Vicepresidencia.nombre AS nombre_area_macro,  
            Kpis.area_proceso,
            goforagile_admin.Areas.nombre AS nombre_area_proceso,
            Kpis.subproceso, 
            goforagile_admin.Estructura_Empresa.unidad_organizativa AS nombre_subproceso,
            Kpis.tipo_kpi,
            Kpis.unidad_medida,  
            Kpis.meta, 
            Kpis.tipo_calculo, 
            Kpis.tipo_resultado, 
            Kpis.indicador, 
            Kpis.objetivo_indicador, 
            Kpis.formula, 
            Kpis.frecuencia, 
            Frecuencia_Kpis.tipo, 
            Frecuencia_Kpis.enero, Frecuencia_Kpis.avance_1, 
            Frecuencia_Kpis.febrero, Frecuencia_Kpis.avance_2, 
            Frecuencia_Kpis.marzo, Frecuencia_Kpis.avance_3, 
            Frecuencia_Kpis.abril, Frecuencia_Kpis.avance_4, 
            Frecuencia_Kpis.mayo, Frecuencia_Kpis.avance_5, 
            Frecuencia_Kpis.junio, Frecuencia_Kpis.avance_6, 
            Frecuencia_Kpis.julio, Frecuencia_Kpis.avance_7, 
            Frecuencia_Kpis.agosto, Frecuencia_Kpis.avance_8, 
            Frecuencia_Kpis.septiembre, Frecuencia_Kpis.avance_9, 
            Frecuencia_Kpis.octubre, Frecuencia_Kpis.avance_10, 
            Frecuencia_Kpis.noviembre, Frecuencia_Kpis.avance_11, 
            Frecuencia_Kpis.diciembre, Frecuencia_Kpis.avance_12
        FROM
            Kpis
        INNER JOIN
            Frecuencia_Kpis ON Frecuencia_Kpis.id_kpi = Kpis.id 
        LEFT JOIN
            goforagile_admin.Vicepresidencia ON goforagile_admin.Vicepresidencia.id = Kpis.area_macro
        LEFT JOIN
            goforagile_admin.Areas ON goforagile_admin.Areas.id = Kpis.area_proceso
        LEFT JOIN
            goforagile_admin.Estructura_Empresa ON goforagile_admin.Estructura_Empresa.id = Kpis.subproceso
        WHERE
            Kpis.id_empresa = ".$id_empresa." 
            ".$filtros." 
        GROUP BY Kpis.id 
        ORDER BY
            Kpis.indicador ASC 
        LIMIT ".$posicion.", ".$longitud."
        ";

        //LEFT JOIN Kpis_Colaborador ON Kpis_Colaborador.id_kpi = Kpis.id
        
        $queryKpis = mysqli_query($connect_kpis, $sentencia);
        while ($dataKpis = mysqli_fetch_assoc($queryKpis)) { 

            $dataKpis["avance_1"] = trim($dataKpis["avance_1"]);
            $dataKpis["avance_2"] = trim($dataKpis["avance_2"]);
            $dataKpis["avance_3"] = trim($dataKpis["avance_3"]);
            $dataKpis["avance_4"] = trim($dataKpis["avance_4"]);
            $dataKpis["avance_5"] = trim($dataKpis["avance_5"]);
            $dataKpis["avance_6"] = trim($dataKpis["avance_6"]);
            $dataKpis["avance_7"] = trim($dataKpis["avance_7"]);
            $dataKpis["avance_8"] = trim($dataKpis["avance_8"]);
            $dataKpis["avance_9"] = trim($dataKpis["avance_9"]);
            $dataKpis["avance_10"] = trim($dataKpis["avance_10"]);
            $dataKpis["avance_11"] = trim($dataKpis["avance_11"]); 
            $dataKpis["avance_12"] = trim($dataKpis["avance_12"]);

            $dataKpis["avance_1"] = trim($dataKpis["avance_1"]);
            $dataKpis["avance_2"] = trim($dataKpis["avance_2"]);
            $dataKpis["avance_3"] = trim($dataKpis["avance_3"]);
            $dataKpis["avance_4"] = trim($dataKpis["avance_4"]);
            $dataKpis["avance_5"] = trim($dataKpis["avance_5"]);
            $dataKpis["avance_6"] = trim($dataKpis["avance_6"]);
            $dataKpis["avance_7"] = trim($dataKpis["avance_7"]);
            $dataKpis["avance_8"] = trim($dataKpis["avance_8"]);
            $dataKpis["avance_9"] = trim($dataKpis["avance_9"]);
            $dataKpis["avance_10"] = trim($dataKpis["avance_10"]);
            $dataKpis["avance_11"] = trim($dataKpis["avance_11"]); 
            $dataKpis["avance_12"] = trim($dataKpis["avance_12"]);
            
            //TIPO KPIS
            $txt_tipo = 'Estratégico';
            if($dataKpis["tipo_kpi"] == 2 ){
                $txt_tipo = 'Táctico';
            }
            $dataKpis["tipo_txt"] = $txt_tipo;

            //TIPO RESULTADO
            $txt_tipo_resultado = "";
            if($dataKpis["tipo_resultado"] == 1 ){ 
                $txt_tipo_resultado = "No Acumulativo";
            }
            if($dataKpis["tipo_resultado"] == 3 ){ 
                $txt_tipo_resultado = "Acumulativo";
            }
            $dataKpis["tipo_resultado_txt"] = $txt_tipo_resultado;

            //TIPO CALCULO
            $txt_tipo_calculo = "";
            if($dataKpis["tipo_calculo"] == 1 ){ 
                $txt_tipo_calculo = "Ascendente";
            }
            if($dataKpis["tipo_calculo"] == 2 ){ 
                $txt_tipo_calculo = "Descendente";
            }
            if($dataKpis["tipo_calculo"] == 3 ){ 
                $txt_tipo_calculo = "Valor Absoluto";
            }
            $dataKpis["tipo_calculo_txt"] = $txt_tipo_calculo;

            //UNIDAD DE MEDIDA
            $txt_unidad_medida = $this->UnidadMedidaText($dataKpis["unidad_medida"]);
            $dataKpis["unidad_medida_txt"] = $txt_unidad_medida;

            //AVANCE DEL KPIS
            $avance = $this->AvanceKPI_2($dataKpis);
            $dataKpis["color_avance_kpis"] = EscalaColor($avance["avance_porcentaje"]);

            //print_r(EscalaColor($avance));

            //echo $dataKpis["indicador"]." || ".$avance;
            //echo "<br>";

            $dataKpis["avance_kpis"] = $avance["avance_porcentaje"];
            $dataKpis["avance_plano_kpis"] = $avance["avance_numero"];

            //Integrantes
            //$dataKpis["integrantes"] = $this->obtener_integrantes($id_empresa, $dataKpis["id_kpi"]);
            $dataKpis["integrantes"] = $this->obtener_integrantes_v2($id_empresa, $dataKpis["id_kpi"], $responsables); //NUEVO MODELO

            array_push($array_kpis, $dataKpis);
        }

        //echo "<hr>";

        return $array_kpis;
    }

    //PARA REFACTORIZAR Y DEJAR EN UNA SOLA CONSULTA
    //TODOS LOS KPIS DE UN COLABORADOR
    //TODOS LOS KPIS DE UN COLABORADOR
    //TODOS LOS KPIS DE UN COLABORADOR
    public function kpis_relaciones( $id_empresa, $array_vicepresidencia, $array_areas ){

        $filtros = "";
        $responsables = $this->lista_colaboradores_kpis($id_empresa);

        //OBTENEMOS LAS VICEPRESIDENCIAS Y AREAS
        $id_vicepresidencias =  implode(",", $array_vicepresidencia);
        $id_areas =  implode(",", $array_areas);


        if($id_vicepresidencias > 0){
            $filtros .= " AND Kpis.area_macro IN (".$id_vicepresidencias.") ";
        }
        if($id_areas != ""){
            $filtros .= " AND Kpis.area_proceso IN ( ".$id_areas." ) ";
        }

        if($_SESSION["anio_fill"]){ $filtros .= " AND Kpis.anio = '".$_SESSION["anio_fill"]."' "; }
        if($_SESSION["tipo_kpi_fill"]){ $filtros .= " AND Kpis.tipo_kpi = '".$_SESSION["tipo_kpi_fill"]."' "; }
        if($_SESSION["frecuencia_fill"]){ $filtros .= " AND Kpis.frecuencia = '".$_SESSION["frecuencia_fill"]."' "; } 
        if($_SESSION["tipo_resultado_fill"]){ $filtros .= " AND Kpis.tipo_resultado = '".$_SESSION["tipo_resultado_fill"]."' "; } 
        if($_SESSION["tipo_calculo_fill"]){ $filtros .= " AND Kpis.tipo_calculo = '".$_SESSION["tipo_calculo_fill"]."' "; }
        if($_SESSION["unidad_medida_fill"]){ $filtros .= " AND Kpis.unidad_medida = '".$_SESSION["unidad_medida_fill"]."' "; } 

        //no aplica filtros de area macro y area proceso

        /*
        if($_SESSION["area_macro_fill"]){ $filtros .= " AND Kpis.area_macro = '".$_SESSION["area_macro_fill"]."' "; }
        if($_SESSION["area_proceso_fill"]){ $filtros .= " AND Kpis.area_proceso = '".$_SESSION["area_proceso_fill"]."' "; }
        if($_SESSION["subproceso_fill"]){ $filtros .= " AND Kpis.subproceso = '".$_SESSION["subproceso_fill"]."' "; }
        */
        

        global $connect_kpis;

        //OBTENEMOS LOS KPIS
        //OBTENEMOS LOS KPIS
        //OBTENEMOS LOS KPIS
        $array_kpis = array();

        $sentencia = " 
        SELECT
            Kpis.id AS id_kpi,
            Kpis.area_macro,
            goforagile_admin.Vicepresidencia.nombre AS nombre_area_macro,  
            Kpis.area_proceso,
            goforagile_admin.Areas.nombre AS nombre_area_proceso,
            Kpis.subproceso, 
            goforagile_admin.Estructura_Empresa.unidad_organizativa AS nombre_subproceso,
            Kpis.tipo_kpi,
            Kpis.unidad_medida,  
            Kpis.meta, 
            Kpis.tipo_calculo, 
            Kpis.tipo_resultado, 
            Kpis.indicador, 
            Kpis.objetivo_indicador, 
            Kpis.formula, 
            Kpis.frecuencia, 
            Frecuencia_Kpis.tipo, 
            Frecuencia_Kpis.enero, Frecuencia_Kpis.avance_1, 
            Frecuencia_Kpis.febrero, Frecuencia_Kpis.avance_2, 
            Frecuencia_Kpis.marzo, Frecuencia_Kpis.avance_3, 
            Frecuencia_Kpis.abril, Frecuencia_Kpis.avance_4, 
            Frecuencia_Kpis.mayo, Frecuencia_Kpis.avance_5, 
            Frecuencia_Kpis.junio, Frecuencia_Kpis.avance_6, 
            Frecuencia_Kpis.julio, Frecuencia_Kpis.avance_7, 
            Frecuencia_Kpis.agosto, Frecuencia_Kpis.avance_8, 
            Frecuencia_Kpis.septiembre, Frecuencia_Kpis.avance_9, 
            Frecuencia_Kpis.octubre, Frecuencia_Kpis.avance_10, 
            Frecuencia_Kpis.noviembre, Frecuencia_Kpis.avance_11, 
            Frecuencia_Kpis.diciembre, Frecuencia_Kpis.avance_12
        FROM
            Kpis
        INNER JOIN
            Frecuencia_Kpis ON Frecuencia_Kpis.id_kpi = Kpis.id 
        LEFT JOIN
            goforagile_admin.Vicepresidencia ON goforagile_admin.Vicepresidencia.id = Kpis.area_macro
        LEFT JOIN
            goforagile_admin.Areas ON goforagile_admin.Areas.id = Kpis.area_proceso
        LEFT JOIN
            goforagile_admin.Estructura_Empresa ON goforagile_admin.Estructura_Empresa.id = Kpis.subproceso
        WHERE
            Kpis.id_empresa = ".$id_empresa." 
            ".$filtros." 
        GROUP BY Kpis.id 
        ORDER BY
            Kpis.indicador ASC 
        ";

        //LEFT JOIN Kpis_Colaborador ON Kpis_Colaborador.id_kpi = Kpis.id
        
        $queryKpis = mysqli_query($connect_kpis, $sentencia);
        while ($dataKpis = mysqli_fetch_assoc($queryKpis)) { 

            $dataKpis["avance_1"] = trim($dataKpis["avance_1"]);
            $dataKpis["avance_2"] = trim($dataKpis["avance_2"]);
            $dataKpis["avance_3"] = trim($dataKpis["avance_3"]);
            $dataKpis["avance_4"] = trim($dataKpis["avance_4"]);
            $dataKpis["avance_5"] = trim($dataKpis["avance_5"]);
            $dataKpis["avance_6"] = trim($dataKpis["avance_6"]);
            $dataKpis["avance_7"] = trim($dataKpis["avance_7"]);
            $dataKpis["avance_8"] = trim($dataKpis["avance_8"]);
            $dataKpis["avance_9"] = trim($dataKpis["avance_9"]);
            $dataKpis["avance_10"] = trim($dataKpis["avance_10"]);
            $dataKpis["avance_11"] = trim($dataKpis["avance_11"]); 
            $dataKpis["avance_12"] = trim($dataKpis["avance_12"]);

            $dataKpis["avance_1"] = trim($dataKpis["avance_1"]);
            $dataKpis["avance_2"] = trim($dataKpis["avance_2"]);
            $dataKpis["avance_3"] = trim($dataKpis["avance_3"]);
            $dataKpis["avance_4"] = trim($dataKpis["avance_4"]);
            $dataKpis["avance_5"] = trim($dataKpis["avance_5"]);
            $dataKpis["avance_6"] = trim($dataKpis["avance_6"]);
            $dataKpis["avance_7"] = trim($dataKpis["avance_7"]);
            $dataKpis["avance_8"] = trim($dataKpis["avance_8"]);
            $dataKpis["avance_9"] = trim($dataKpis["avance_9"]);
            $dataKpis["avance_10"] = trim($dataKpis["avance_10"]);
            $dataKpis["avance_11"] = trim($dataKpis["avance_11"]); 
            $dataKpis["avance_12"] = trim($dataKpis["avance_12"]);
            
            //TIPO KPIS
            $txt_tipo = 'Estratégico';
            if($dataKpis["tipo_kpi"] == 2 ){
                $txt_tipo = 'Táctico';
            }
            $dataKpis["tipo_txt"] = $txt_tipo;

            //TIPO RESULTADO
            $txt_tipo_resultado = "";
            if($dataKpis["tipo_resultado"] == 1 ){ 
                $txt_tipo_resultado = "No Acumulativo";
            }
            if($dataKpis["tipo_resultado"] == 3 ){ 
                $txt_tipo_resultado = "Acumulativo";
            }
            $dataKpis["tipo_resultado_txt"] = $txt_tipo_resultado;

            //TIPO CALCULO
            $txt_tipo_calculo = "";
            if($dataKpis["tipo_calculo"] == 1 ){ 
                $txt_tipo_calculo = "Ascendente";
            }
            if($dataKpis["tipo_calculo"] == 2 ){ 
                $txt_tipo_calculo = "Descendente";
            }
            if($dataKpis["tipo_calculo"] == 3 ){ 
                $txt_tipo_calculo = "Valor Absoluto";
            }
            $dataKpis["tipo_calculo_txt"] = $txt_tipo_calculo;

            //UNIDAD DE MEDIDA
            $txt_unidad_medida = $this->UnidadMedidaText($dataKpis["unidad_medida"]);
            $dataKpis["unidad_medida_txt"] = $txt_unidad_medida;

            //AVANCE DEL KPIS
            $avance = $this->AvanceKPI_2($dataKpis);
            $dataKpis["color_avance_kpis"] = EscalaColor($avance["avance_porcentaje"]);

            $dataKpis["avance_kpis"] = $avance["avance_porcentaje"];
            $dataKpis["avance_plano_kpis"] = $avance["avance_numero"];

            array_push($array_kpis, $dataKpis);
        }

        //echo "<hr>";

        return $array_kpis;
    }

    //PARA REFACTORIZAR Y DEJAR EN UNA SOLA CONSULTA
    //TODOS LOS KPIS DE UN COLABORADOR
    //TODOS LOS KPIS DE UN COLABORADOR
    //TODOS LOS KPIS DE UN COLABORADOR
    public function kpis_relaciones_paginado( $id_empresa, $posicion, $longitud, $array_vicepresidencia, $array_areas ){

        $filtros = "";

        //OBTENEMOS LAS VICEPRESIDENCIAS Y AREAS
        $id_vicepresidencias =  implode(",", $array_vicepresidencia);
        $id_areas =  implode(",", $array_areas);

        if($id_vicepresidencias > 0){
            $filtros .= " AND Kpis.area_macro IN (".$id_vicepresidencias.") ";
        }
        if($id_areas != ""){
            $filtros .= " AND Kpis.area_proceso IN ( ".$id_areas." ) ";
        }

        $responsables = $this->lista_colaboradores_kpis($id_empresa);

        if($_SESSION["anio_fill"]){ $filtros .= " AND Kpis.anio = '".$_SESSION["anio_fill"]."' "; }
        if($_SESSION["tipo_kpi_fill"]){ $filtros .= " AND Kpis.tipo_kpi = '".$_SESSION["tipo_kpi_fill"]."' "; }
        if($_SESSION["frecuencia_fill"]){ $filtros .= " AND Kpis.frecuencia = '".$_SESSION["frecuencia_fill"]."' "; } 
        if($_SESSION["tipo_resultado_fill"]){ $filtros .= " AND Kpis.tipo_resultado = '".$_SESSION["tipo_resultado_fill"]."' "; } 
        if($_SESSION["tipo_calculo_fill"]){ $filtros .= " AND Kpis.tipo_calculo = '".$_SESSION["tipo_calculo_fill"]."' "; }
        if($_SESSION["unidad_medida_fill"]){ $filtros .= " AND Kpis.unidad_medida = '".$_SESSION["unidad_medida_fill"]."' "; } 

        /*
        if($_SESSION["area_macro_fill"]){ $filtros .= " AND Kpis.area_macro = '".$_SESSION["area_macro_fill"]."' "; }
        if($_SESSION["area_proceso_fill"]){ $filtros .= " AND Kpis.area_proceso = '".$_SESSION["area_proceso_fill"]."' "; }
        if($_SESSION["subproceso_fill"]){ $filtros .= " AND Kpis.subproceso = '".$_SESSION["subproceso_fill"]."' "; }
        */

        

        global $connect_kpis;

        //OBTENEMOS LOS KPIS
        //OBTENEMOS LOS KPIS
        //OBTENEMOS LOS KPIS
        $array_kpis = array();

        $sentencia = " 
        SELECT
            Kpis.id AS id_kpi,
            Kpis.area_macro,
            goforagile_admin.Vicepresidencia.nombre AS nombre_area_macro,  
            Kpis.area_proceso,
            goforagile_admin.Areas.nombre AS nombre_area_proceso,
            Kpis.subproceso, 
            goforagile_admin.Estructura_Empresa.unidad_organizativa AS nombre_subproceso,
            Kpis.tipo_kpi,
            Kpis.unidad_medida,  
            Kpis.meta, 
            Kpis.tipo_calculo, 
            Kpis.tipo_resultado, 
            Kpis.indicador, 
            Kpis.objetivo_indicador, 
            Kpis.formula, 
            Kpis.frecuencia, 
            Frecuencia_Kpis.tipo, 
            Frecuencia_Kpis.enero, Frecuencia_Kpis.avance_1, 
            Frecuencia_Kpis.febrero, Frecuencia_Kpis.avance_2, 
            Frecuencia_Kpis.marzo, Frecuencia_Kpis.avance_3, 
            Frecuencia_Kpis.abril, Frecuencia_Kpis.avance_4, 
            Frecuencia_Kpis.mayo, Frecuencia_Kpis.avance_5, 
            Frecuencia_Kpis.junio, Frecuencia_Kpis.avance_6, 
            Frecuencia_Kpis.julio, Frecuencia_Kpis.avance_7, 
            Frecuencia_Kpis.agosto, Frecuencia_Kpis.avance_8, 
            Frecuencia_Kpis.septiembre, Frecuencia_Kpis.avance_9, 
            Frecuencia_Kpis.octubre, Frecuencia_Kpis.avance_10, 
            Frecuencia_Kpis.noviembre, Frecuencia_Kpis.avance_11, 
            Frecuencia_Kpis.diciembre, Frecuencia_Kpis.avance_12
        FROM
            Kpis
        INNER JOIN
            Frecuencia_Kpis ON Frecuencia_Kpis.id_kpi = Kpis.id 
        LEFT JOIN
            goforagile_admin.Vicepresidencia ON goforagile_admin.Vicepresidencia.id = Kpis.area_macro
        LEFT JOIN
            goforagile_admin.Areas ON goforagile_admin.Areas.id = Kpis.area_proceso
        LEFT JOIN
            goforagile_admin.Estructura_Empresa ON goforagile_admin.Estructura_Empresa.id = Kpis.subproceso
        WHERE
            Kpis.id_empresa = ".$id_empresa." 
            ".$filtros." 
        GROUP BY Kpis.id 
        ORDER BY
            Kpis.indicador ASC 
        LIMIT ".$posicion.", ".$longitud."
        ";

        //LEFT JOIN Kpis_Colaborador ON Kpis_Colaborador.id_kpi = Kpis.id
        
        $queryKpis = mysqli_query($connect_kpis, $sentencia);
        while ($dataKpis = mysqli_fetch_assoc($queryKpis)) { 

            $dataKpis["avance_1"] = trim($dataKpis["avance_1"]);
            $dataKpis["avance_2"] = trim($dataKpis["avance_2"]);
            $dataKpis["avance_3"] = trim($dataKpis["avance_3"]);
            $dataKpis["avance_4"] = trim($dataKpis["avance_4"]);
            $dataKpis["avance_5"] = trim($dataKpis["avance_5"]);
            $dataKpis["avance_6"] = trim($dataKpis["avance_6"]);
            $dataKpis["avance_7"] = trim($dataKpis["avance_7"]);
            $dataKpis["avance_8"] = trim($dataKpis["avance_8"]);
            $dataKpis["avance_9"] = trim($dataKpis["avance_9"]);
            $dataKpis["avance_10"] = trim($dataKpis["avance_10"]);
            $dataKpis["avance_11"] = trim($dataKpis["avance_11"]); 
            $dataKpis["avance_12"] = trim($dataKpis["avance_12"]);

            $dataKpis["avance_1"] = trim($dataKpis["avance_1"]);
            $dataKpis["avance_2"] = trim($dataKpis["avance_2"]);
            $dataKpis["avance_3"] = trim($dataKpis["avance_3"]);
            $dataKpis["avance_4"] = trim($dataKpis["avance_4"]);
            $dataKpis["avance_5"] = trim($dataKpis["avance_5"]);
            $dataKpis["avance_6"] = trim($dataKpis["avance_6"]);
            $dataKpis["avance_7"] = trim($dataKpis["avance_7"]);
            $dataKpis["avance_8"] = trim($dataKpis["avance_8"]);
            $dataKpis["avance_9"] = trim($dataKpis["avance_9"]);
            $dataKpis["avance_10"] = trim($dataKpis["avance_10"]);
            $dataKpis["avance_11"] = trim($dataKpis["avance_11"]); 
            $dataKpis["avance_12"] = trim($dataKpis["avance_12"]);
            
            //TIPO KPIS
            $txt_tipo = 'Estratégico';
            if($dataKpis["tipo_kpi"] == 2 ){
                $txt_tipo = 'Táctico';
            }
            $dataKpis["tipo_txt"] = $txt_tipo;

            //TIPO RESULTADO
            $txt_tipo_resultado = "";
            if($dataKpis["tipo_resultado"] == 1 ){ 
                $txt_tipo_resultado = "No Acumulativo";
            }
            if($dataKpis["tipo_resultado"] == 3 ){ 
                $txt_tipo_resultado = "Acumulativo";
            }
            $dataKpis["tipo_resultado_txt"] = $txt_tipo_resultado;

            //TIPO CALCULO
            $txt_tipo_calculo = "";
            if($dataKpis["tipo_calculo"] == 1 ){ 
                $txt_tipo_calculo = "Ascendente";
            }
            if($dataKpis["tipo_calculo"] == 2 ){ 
                $txt_tipo_calculo = "Descendente";
            }
            if($dataKpis["tipo_calculo"] == 3 ){ 
                $txt_tipo_calculo = "Valor Absoluto";
            }
            $dataKpis["tipo_calculo_txt"] = $txt_tipo_calculo;

            //UNIDAD DE MEDIDA
            $txt_unidad_medida = $this->UnidadMedidaText($dataKpis["unidad_medida"]);
            $dataKpis["unidad_medida_txt"] = $txt_unidad_medida;

            //AVANCE DEL KPIS
            $avance = $this->AvanceKPI_2($dataKpis);
            $dataKpis["color_avance_kpis"] = EscalaColor($avance["avance_porcentaje"]);

            //print_r(EscalaColor($avance));

            //echo $dataKpis["indicador"]." || ".$avance;
            //echo "<br>";

            $dataKpis["avance_kpis"] = $avance["avance_porcentaje"];
            $dataKpis["avance_plano_kpis"] = $avance["avance_numero"];

            //Integrantes
            //$dataKpis["integrantes"] = $this->obtener_integrantes($id_empresa, $dataKpis["id_kpi"]);
            $dataKpis["integrantes"] = $this->obtener_integrantes_v2($id_empresa, $dataKpis["id_kpi"], $responsables); //NUEVO MODELO

            array_push($array_kpis, $dataKpis);
        }

        //echo "<hr>";

        return $array_kpis;
    }






    //OBTENER INTEGRANTES V2
    public function obtener_integrantes_v2($id_empresa, $id_kpi, $responsables){

        $array = array();

        foreach($responsables as $data){
            if($data["id_kpi"] == $id_kpi){
                $empleado = $this->DatosEmpleado($data["id_colaborador"]);
                $data["foto"] = $empleado["foto"];
                $data["nombre"] = $empleado["nombre"]; 
                $data["cargo"] = $empleado["nombre_cargo"]; 
                $data["vicepresidencia"] = $empleado["nombre_vicepresidencia"]; 
                $data["area"] = $empleado["nombre_area"];
                $data["role"] = $empleado["role"]; 
                array_push($array, $data);
            }
        }
        return $array;
    }

    //LISTA DE INTEGRANTES DE LOS KPIS
    public function obtener_integrantes($id_empresa, $id_kpi){

        global $connect_kpis;
        $array = array();

        $sentencia = "
        SELECT
            *
        FROM
            Kpis_Colaborador
        WHERE
            id_empresa = '".$id_empresa."' AND
            id_kpi = '".$id_kpi."'
        ";
        $query = mysqli_query($connect_kpis, $sentencia);
        while ($data = mysqli_fetch_array($query)){

            $empleado = $this->DatosEmpleado($data["id_colaborador"]);

            $data["foto"] = $empleado["foto"];
            $data["nombre"] = $empleado["nombre"]; 
            $data["cargo"] = $empleado["nombre_cargo"]; 
            $data["vicepresidencia"] = $empleado["nombre_vicepresidencia"]; 
            $data["area"] = $empleado["nombre_area"];
            $data["role"] = $empleado["role"]; 

            array_push($array, $data);
        }

        return $array;

    }




































    

    
    
    
    ///FUNCIONES DE APOYO 
    ///FUNCIONES DE APOYO 
    ///FUNCIONES DE APOYO 
    ///FUNCIONES DE APOYO 

    //TODOS LOS EMPLEADOS
    //TODOS LOS EMPLEADOS
    //TODOS LOS EMPLEADOS
    public function lista_empleados($id_empresa){

        global $connect_admin;

        $array_empleados = array();

        
        $sentencia = "
        SELECT
            Empleados.id,
            Empleados.documento,
            Empleados.nombre,
            Empleados.role,
            Empleados.foto,
            Empleados.id_cargo,
            Cargos.nombre AS nombre_cargo,
            Areas.id AS id_area,
            Areas.nombre AS nombre_area,
            goforagile_admin.Vicepresidencia.nombre AS nombre_vicepresidencia,
            goforagile_okrs.Roles_Okrs.nombre_rol AS rol
        FROM
            Empleados
        LEFT JOIN
            Cargos ON Cargos.id = Empleados.id_cargo
        LEFT JOIN
            Areas ON Areas.id = Empleados.area
        LEFT JOIN
            goforagile_admin.Estructura_Empresa ON Empleados.area = goforagile_admin.Estructura_Empresa.area
        LEFT JOIN
            goforagile_admin.Vicepresidencia ON goforagile_admin.Vicepresidencia.id = goforagile_admin.Estructura_Empresa.vicepresidencia
        LEFT JOIN
            goforagile_okrs.Roles_Okrs ON goforagile_okrs.Roles_Okrs.id = Empleados.role
        WHERE
            Empleados.id_empresa = '".$id_empresa."'
        ";
        
        $query = mysqli_query($connect_admin, $sentencia);
        while ($data = mysqli_fetch_array($query)) { 
            
            $data["foto"] = !empty($data["foto"]) ? $data["foto"] : "img_default.jpg";

            $array_empleados[$data["id"]] = $data;

            //array_push($array_empleados, $data);
        }

        return $array_empleados;
    }

    //TODAS LAS AREAS
    //TODAS LAS AREAS
    //TODAS LAS AREAS
    public function lista_areas($id_empresa){
        global $connect_admin;
        $array_areas = array();
        $sentencia = "SELECT * FROM Areas WHERE Areas.id_empresa = '".$id_empresa."' ";
        $query = mysqli_query($connect_admin, $sentencia);
        while ($data = mysqli_fetch_array($query)) { 
            array_push($array_areas, $data);
        }

        return $array_areas;
    }

    //TODAS LAS UNIDADES ORGANIZATIVAS
    //TODAS LAS UNIDADES ORGANIZATIVAS
    //TODAS LAS UNIDADES ORGANIZATIVAS
    public function lista_unidades_organizativas($id_empresa){
        global $connect_admin;
        $array_unidades = array();
        $sentencia = "SELECT * FROM Estructura_Empresa WHERE id_empresa = '".$id_empresa."' ";
        $query = mysqli_query($connect_admin, $sentencia);
        while ($data = mysqli_fetch_array($query)) { 
            array_push($array_unidades, $data);
        }

        return $array_unidades;
    }

    //TODOS LOS COLABORADORES DEL LOS KPIS
    //TODOS LOS COLABORADORES DEL LOS KPIS
    //TODOS LOS COLABORADORES DEL LOS KPIS
    public function lista_colaboradores_kpis($id_empresa){

        global $connect_kpis;
        $array = array();

        $sentencia = "SELECT id_colaborador, id_kpi FROM Kpis_Colaborador WHERE id_empresa = '".$id_empresa."' ";
        $query = mysqli_query($connect_kpis, $sentencia);
        while ($data = mysqli_fetch_array($query)){
            array_push($array, $data);
        }

        return $array;
    }


    

    //DATOS DEL EMPLEADO
    public function DatosEmpleado($id){

        $data = array();
        foreach( $this->datos_colaboradores as $empleado){
            if($empleado["id"] == $id){
                $data = $empleado;
            }
        }

        return $data;
    }

    //DATOS AREAS
    public function DatosAreas($id){
        $data = array();
        foreach( $this->datos_areas as $area){
            if($area["id"] == $id){
                $data = $area;
            }
        }
        return $data;
    }

    //DATOS UNIDADES ORGANIZATIVAS
    public function DatosUnidades($id){
        $data = array();
        foreach( $this->datos_unidades as $unidad){
            if($unidad["id"] == $id){
                $data = $unidad;
            }
        }
        return $data;
    }
    
    //PARA CONVERTIR LAS HORARIOS EN SEGUNDOS
    public function ConvertirHorasMinutosSegundos($valor)
    {
        $tiempo_partes =  explode(":", $valor);
        $hor = $tiempo_partes[0] * 60 * 60;
        $min = $tiempo_partes[1] * 60;
        $seg = $tiempo_partes[2];

        $total_seg = $hor + $min + $seg;
        return $total_seg;
    }

    //PARA CONVERTIR LAS HORARIOS EN SEGUNDOS
    public function ConvertirNumeroPlanoAHorasMinutosSegundos($total_seg)
    {
        $horas = floor($total_seg / 3600);
        $minutos = floor(($total_seg % 3600) / 60);
        $segundos = $total_seg % 60;

        // Formatear con ceros a la izquierda
        return sprintf("%02d:%02d:%02d", $horas, $minutos, $segundos);
    }


    //PARA OBTENER MENSAJE Y ALERTAS DE LOS KPIS
    public function mensajes_kpis($id_empresa){
        global $connect_kpis; 

        $array_mensajes = array();
        $queryRK = mysqli_query($connect_kpis, "SELECT * FROM Roles_Kpis WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1");
        while ($dataRK = mysqli_fetch_array($queryRK)) {
            array_push($array_mensajes, $dataRK);
        }
        $nodo = array(
            "roles_kpis" => $array_mensajes
        );

        return $nodo;
    }

    public function obtener_comentarios($id_kpi, $frecuencia){
        global $connect_kpis;

        $array = array();

        $sentencia =
        "SELECT
            *
        FROM
            Comentarios_Kpis
        WHERE
            id_kpi = '$id_kpi'
            AND frecuencia = '$frecuencia'
        ";
        $query = mysqli_query($connect_kpis, $sentencia);

        $analisis = "";
        if(mysqli_num_rows($query) > 0){
            $analisis = '<span class="btn btn-sm" style="color:white; font-size:0.6rem; background-color:#198754; min-width:110px;">
                Análisis Finalizado
            </span>';
            return $analisis;
        }else{
            $analisis = '
            <a href="?pg=kpis/gestionar_kpi&id='.$id_kpi.'" target="_blank">
                <span class="btn btn-sm btn-danger" style="color:white; font-size:0.6rem; min-width:110px;">
                    Pendiente Análisis
                </span>
            </a>
            ';
            return $analisis;
        }
    }

    //FUNCION UNIDAD DE MEDIDA TEXTO
    public function UnidadMedidaText($unidad_medida){
        //UNIDAD DE MEDIDA
        $txt_unidad_medida = "";
        if($unidad_medida == 1 ){ 
            $txt_unidad_medida = "Cantidad";
        }
        if($unidad_medida == 2 ){ 
            $txt_unidad_medida = "Porcentaje";
        }
        if($unidad_medida == 3 ){ 
            $txt_unidad_medida = "Moneda";
        }
        if($unidad_medida == 4 ){ 
            $txt_unidad_medida = "Hora / Minuto / Segundos";
        }

        return $txt_unidad_medida;
    }

}

?>