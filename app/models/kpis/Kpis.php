<?php

class Kpis
{

    //AUDITORIAS
    public function Kpis_auditoria($id_empresa)
    {
        global $connect_kpis;
        $array = array();

        $sentencia =
            "SELECT
           Auditoria_Kpi.*,
           Kpis.indicador as indicador
        FROM
            Auditoria_Kpi
        LEFT JOIN
            Kpis ON Kpis.id = Auditoria_Kpi.id_kpi
        WHERE
            Auditoria_Kpi.id_empresa = '$id_empresa'
        ORDER BY
            Auditoria_Kpi.created_at DESC
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
            $empleado = $this->Empleado($data["id_empleado"]);
            $data["empleado"] = $empleado;

            array_push($array, $data);
        }

        return $array;
    }

    //LISTA DE VICEPRESIDENCIAS
    //LISTA DE VICEPRESIDENCIAS
    //LISTA DE VICEPRESIDENCIAS
    public function Vicepresidencias_lista($id_empresa)
    {
        global $connect_admin;
        $array = array();
        $sentencia = "
        SELECT
            Vicepresidencia.*,
            GROUP_CONCAT(DISTINCT Lideres_Vicepresidencia.id_lider) AS id_lideres,
            Estructura_Empresa.area AS id_area
        FROM
            Vicepresidencia
        LEFT JOIN Lideres_Vicepresidencia ON Lideres_Vicepresidencia.id_vicepresidencia = Vicepresidencia.id
        LEFT JOIN Estructura_Empresa ON Estructura_Empresa.vicepresidencia = Vicepresidencia.id
        WHERE
            Vicepresidencia.id_empresa = '" . $id_empresa . "'
            AND Vicepresidencia.estado = 1
        GROUP BY
            Vicepresidencia.id
        ORDER BY
            Vicepresidencia.nombre ASC";
        $query = mysqli_query($connect_admin, $sentencia);

        while ($data = mysqli_fetch_assoc($query)) {

            $areas = $this->areas_vicepresidencia_lista($id_empresa, $data["id"]);

            $data["areas"] = $areas;

            array_push($array, $data);
        }
        return $array;
    }

    //LISTA DE AREAS POR VICEPRESIDENCIA
    //LISTA DE AREAS POR VICEPRESIDENCIA
    //LISTA DE AREAS POR VICEPRESIDENCIA
    public function areas_vicepresidencia_lista($id_empresa, $id_viceprecidencia)
    {

        $filtros = "";

        if ($_SESSION["anio_fill"]) {
            $filtros .= " AND Kpis.anio = '" . $_SESSION["anio_fill"] . "' ";
        }
        if ($_SESSION["frecuencia_fill"]) {
            $filtros .= " AND Kpis.frecuencia = '" . $_SESSION["frecuencia_fill"] . "' ";
        }

        global $connect_kpis;
        $array = array();

        $sentencia = "
            SELECT 
            Kpis.id AS id_kpi,
            Kpis.*,
            puntacana_admin.Areas.nombre as area_nombre,
            puntacana_admin.Lideres_Area.id_lider as id_lider
            FROM
                Kpis
            LEFT JOIN
                puntacana_admin.Areas ON puntacana_admin.Areas.id = Kpis.area_proceso
            LEFT JOIN
                puntacana_admin.Lideres_Area ON puntacana_admin.Lideres_Area.id_area = Kpis.area_proceso
            WHERE
                Kpis.id_empresa = '" . $id_empresa . "'
                AND Kpis.area_macro = '" . $id_viceprecidencia . "' 
                " . $filtros . "
                
            GROUP BY
                Kpis.area_proceso
        ";

        $query = mysqli_query($connect_kpis, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {

            $kpis_lista = $this->kpis_area($id_empresa, $data["area_proceso"]);

            $kpis_cantidad = 0;
            $avance_area = 0;

            foreach ($kpis_lista as $kpi) {
                $avance_area += $kpi["avance"];
                $kpis_cantidad++;
            }

            $avance_total_area = 0;
            if ($avance_area != 0) {
                $avance_total_area = $avance_area / $kpis_cantidad;
            }

            $data["kpis_lista"] = $kpis_lista;
            $data["kpis_cantidad"] = $kpis_cantidad;


            $data["avance_area"] = $avance_total_area;

            array_push($array, $data);
        }
        return $array;
    }

    //TODOS LOS KPIS DE UN AREA
    //TODOS LOS KPIS DE UN AREA
    //TODOS LOS KPIS DE UN AREA
    public function kpis_area($id_empresa, $id_area)
    {

        $filtros = "";

        if ($_SESSION["anio_fill"]) {
            $filtros .= " AND Kpis.anio = '" . $_SESSION["anio_fill"] . "' ";
        }
        if ($_SESSION["frecuencia_fill"]) {
            $filtros .= " AND Kpis.frecuencia = '" . $_SESSION["frecuencia_fill"] . "' ";
        }

        global $connect_kpis;

        //OBTENEMOS LOS KPIS
        //OBTENEMOS LOS KPIS
        //OBTENEMOS LOS KPIS
        $array_kpis = array();

        $sentencia = " 
        SELECT
            Kpis.id AS id_kpi,
            Kpis.area_macro, 
            Kpis.unidad_medida,  
            Kpis.meta, 
            Kpis.tipo_calculo, 
            Kpis.tipo_resultado, 
            Kpis.indicador, 
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
        WHERE
            Kpis.id_empresa = " . $id_empresa . " 
            AND Kpis.area_proceso = '" . $id_area . "' 
            " . $filtros . "
        ORDER BY
            Kpis.indicador ASC
        ";

        $queryKpis = mysqli_query($connect_kpis, $sentencia);
        while ($dataKpis = mysqli_fetch_assoc($queryKpis)) {

            $avance = $this->AvanceKPI_2($dataKpis);

            echo $dataKpis["indicador"] . " || " . $avance;
            echo "<br>";

            $dataKpis["avance"] = $avance;

            array_push($array_kpis, $dataKpis);
        }

        echo "<hr>";

        return $array_kpis;
    }

    //KPIS VICEPRESIDENCIAS
    public function Vicepresidencias__($id_empresa)
    {

        global $connect_admin;
        $array = array();

        $sentencia = "
        SELECT
            Vicepresidencia.*,
            GROUP_CONCAT(DISTINCT Lideres_Vicepresidencia.id_lider) AS id_lideres,
            Estructura_Empresa.area AS id_area

        FROM
            Vicepresidencia
        LEFT JOIN
            Lideres_Vicepresidencia 
                ON Lideres_Vicepresidencia.id_vicepresidencia = Vicepresidencia.id
        LEFT JOIN
            Estructura_Empresa 
                ON Estructura_Empresa.vicepresidencia = Vicepresidencia.id
        WHERE
            Vicepresidencia.id_empresa = '$id_empresa'
            AND Vicepresidencia.estado = 1
        GROUP BY
            Vicepresidencia.id
        ORDER BY
            Vicepresidencia.nombre ASC";
        $query = mysqli_query($connect_admin, $sentencia);

        while ($data = mysqli_fetch_assoc($query)) {
            array_push($array, $data);
        }
        return $array;
    }

    /*
    //KPIS VICEPRESIDENCIAS
    public function Kpis_General($id_empresa){

        global $connect_admin;
        global $connect_kpis;
        $array = array();

        $filtros = "";

        if($_SESSION["anio_fill"]){ $filtros .= " AND Kpis.anio = '".$_SESSION["anio_fill"]."' "; }
        if($_SESSION["tipo_kpi_fill"]){ $filtros .= " AND Kpis.tipo_kpi = '".$_SESSION["tipo_kpi_fill"]."' "; }
        if($_SESSION["frecuencia_fill"]){ $filtros .= " AND Kpis.frecuencia = '".$_SESSION["frecuencia_fill"]."' "; }
        if($_SESSION["tipo_resultado_fill"]){ $filtros .= " AND Kpis.tipo_resultado = '".$_SESSION["tipo_resultado_fill"]."' "; }
        
        //if($_SESSION["periodo_inicio_fill"]){ $filtros .= " AND Kpis.tipo_resultado = '".$_SESSION["periodo_inicio_fill"]."' "; }
        //if($_SESSION["periodo_fin_fill"]){ $filtros .= " AND Kpis.tipo_resultado = '".$_SESSION["periodo_fin_fill"]."' "; }
        if($_SESSION["tipo_calculo_fill"]){ $filtros .= " AND Kpis.tipo_calculo = '".$_SESSION["tipo_calculo_fill"]."' "; }
        if($_SESSION["unidad_medida_fill"]){ $filtros .= " AND Kpis.unidad_medida = '".$_SESSION["unidad_medida_fill"]."' "; }

        //CONSULTAMOS LAS PRESIDENCIA
        $sentencia ="
            SELECT
                Vicepresidencia.*,
                GROUP_CONCAT(DISTINCT Lideres_Vicepresidencia.id_lider) AS id_lideres,
                Estructura_Empresa.area AS id_area

            FROM
                Vicepresidencia
            LEFT JOIN
                Lideres_Vicepresidencia 
                    ON Lideres_Vicepresidencia.id_vicepresidencia = Vicepresidencia.id
            LEFT JOIN
                Estructura_Empresa 
                    ON Estructura_Empresa.vicepresidencia = Vicepresidencia.id
            WHERE
                Vicepresidencia.id_empresa = '".$id_empresa."'
                AND Vicepresidencia.estado = 1
            GROUP BY
                Vicepresidencia.id
            ORDER BY
                Vicepresidencia.nombre ASC
        ";
        $query = mysqli_query($connect_admin, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {


            $sentencia = "
                SELECT 
                Kpis.id AS id_kpi,
                Kpis.*,
                puntacana_admin.Areas.nombre as area_nombre,
                puntacana_admin.Lideres_Area.id_lider as id_lider
                FROM
                    Kpis
                LEFT JOIN
                    puntacana_admin.Areas ON puntacana_admin.Areas.id = Kpis.area_proceso
                LEFT JOIN
                    puntacana_admin.Lideres_Area ON puntacana_admin.Lideres_Area.id_area = Kpis.area_proceso
                WHERE
                    Kpis.id_empresa = '$id_empresa'
                    AND Kpis.area_macro = '".$data["id"]."'
                    
                GROUP BY
                    Kpis.area_proceso
            ";

            $query = mysqli_query($connect_kpis, $sentencia);
            while ($data = mysqli_fetch_assoc($query)) {




            //OBTENEMOS TODOS LOS KPIS DE LA COMPAÑIA POR AÑO
            //OBTENEMOS TODOS LOS KPIS DE LA COMPAÑIA POR AÑO
            //OBTENEMOS TODOS LOS KPIS DE LA COMPAÑIA POR AÑO
            $array_kpis = array();
            $sentencia = " 
            SELECT
                Kpis.area_macro, 
                Kpis.unidad_medida,  
                Kpis.meta, 
                Kpis.tipo_calculo, 
                Kpis.tipo_resultado, 
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
            WHERE
                Kpis.id_empresa = ".$id_empresa."
                ".$filtros."
            ORDER BY
                Kpis.indicador ASC
            ";
            $queryKpis = mysqli_query($connect_kpis, $sentencia);
            while ($dataKpis = mysqli_fetch_assoc($queryKpis)) {
                array_push($array_kpis, $dataKpis);
            }

            $data[$array_kpis];

            array_push($array, $data);

        }
        }


        

        $sentencia ="
        SELECT
            Vicepresidencia.*,
            GROUP_CONCAT(DISTINCT Lideres_Vicepresidencia.id_lider) AS id_lideres,
            Estructura_Empresa.area AS id_area

        FROM
            Vicepresidencia
        LEFT JOIN
            Lideres_Vicepresidencia 
                ON Lideres_Vicepresidencia.id_vicepresidencia = Vicepresidencia.id
        LEFT JOIN
            Estructura_Empresa 
                ON Estructura_Empresa.vicepresidencia = Vicepresidencia.id
        WHERE
            Vicepresidencia.id_empresa = '$id_empresa'
            AND Vicepresidencia.estado = 1
        GROUP BY
            Vicepresidencia.id
        ORDER BY
            Vicepresidencia.nombre ASC";
        $query = mysqli_query($connect_admin, $sentencia);
        
        while ($data = mysqli_fetch_assoc($query)) {
            $response = $this->kpis_empledos_vicepresidencia($data["id"], $array_kpis);

            $data["cantidad"] = $response["cantidad"];
            $data["avance_general"] = $response["avance_general"];

            array_push($array, $data);
        }
        return $array;
    }
        */

    //KPIS VICEPRESIDENCIAS
    public function Vicepresidencias($id_empresa)
    {

        $filtros = "";

        if ($_SESSION["anio_fill"]) {
            $filtros .= " AND Kpis.anio = '" . $_SESSION["anio_fill"] . "' ";
        }
        if ($_SESSION["tipo_kpi_fill"]) {
            $filtros .= " AND Kpis.tipo_kpi = '" . $_SESSION["tipo_kpi_fill"] . "' ";
        }
        if ($_SESSION["frecuencia_fill"]) {
            $filtros .= " AND Kpis.frecuencia = '" . $_SESSION["frecuencia_fill"] . "' ";
        }
        if ($_SESSION["tipo_resultado_fill"]) {
            $filtros .= " AND Kpis.tipo_resultado = '" . $_SESSION["tipo_resultado_fill"] . "' ";
        }

        //if($_SESSION["periodo_inicio_fill"]){ $filtros .= " AND Kpis.tipo_resultado = '".$_SESSION["periodo_inicio_fill"]."' "; }
        //if($_SESSION["periodo_fin_fill"]){ $filtros .= " AND Kpis.tipo_resultado = '".$_SESSION["periodo_fin_fill"]."' "; }
        if ($_SESSION["tipo_calculo_fill"]) {
            $filtros .= " AND Kpis.tipo_calculo = '" . $_SESSION["tipo_calculo_fill"] . "' ";
        }
        if ($_SESSION["unidad_medida_fill"]) {
            $filtros .= " AND Kpis.unidad_medida = '" . $_SESSION["unidad_medida_fill"] . "' ";
        }

        global $connect_admin;
        global $connect_kpis;
        $array = array();

        //OBTENEMOS TODOS LOS KPIS DE LA COMPAÑIA POR AÑO
        //OBTENEMOS TODOS LOS KPIS DE LA COMPAÑIA POR AÑO
        //OBTENEMOS TODOS LOS KPIS DE LA COMPAÑIA POR AÑO
        $array_kpis = array();
        $sentencia = " 
        SELECT
            Kpis.area_macro, 
            Kpis.unidad_medida,  
            Kpis.meta, 
            Kpis.tipo_calculo, 
            Kpis.tipo_resultado, 
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
        WHERE
            Kpis.id_empresa = " . $id_empresa . "
            " . $filtros . "
        ORDER BY
            Kpis.indicador ASC
        ";
        $queryKpis = mysqli_query($connect_kpis, $sentencia);
        while ($dataKpis = mysqli_fetch_assoc($queryKpis)) {
            array_push($array_kpis, $dataKpis);
        }




        $sentencia = "
        SELECT
            Vicepresidencia.*,
            GROUP_CONCAT(DISTINCT Lideres_Vicepresidencia.id_lider) AS id_lideres,
            Estructura_Empresa.area AS id_area

        FROM
            Vicepresidencia
        LEFT JOIN
            Lideres_Vicepresidencia 
                ON Lideres_Vicepresidencia.id_vicepresidencia = Vicepresidencia.id
        LEFT JOIN
            Estructura_Empresa 
                ON Estructura_Empresa.vicepresidencia = Vicepresidencia.id
        WHERE
            Vicepresidencia.id_empresa = '$id_empresa'
            AND Vicepresidencia.estado = 1
        GROUP BY
            Vicepresidencia.id
        ORDER BY
            Vicepresidencia.nombre ASC";
        $query = mysqli_query($connect_admin, $sentencia);

        while ($data = mysqli_fetch_assoc($query)) {
            $response = $this->kpis_empledos_vicepresidencia($data["id"], $array_kpis);

            $data["cantidad"] = $response["cantidad"];
            $data["avance_general"] = $response["avance_general"];

            array_push($array, $data);
        }
        return $array;
    }





    //KPIS DE LOS EMPLEADOS Y RESULTADOS GENERALES
    public function kpis_empledos_vicepresidencia($id_vicepresidencia, $array_kpis)
    {
        global $connect_kpis;

        $avance_kpis = 0;
        $contador_kpis = 0;

        foreach ($array_kpis as $kpis) {
            if ($id_vicepresidencia == $kpis["area_macro"]) {

                $avance = $this->AvanceKPI_2($kpis);

                $avance_kpis += $avance;
                $contador_kpis++;
            }
        }

        $avance_general = 0;
        if ($contador_kpis > 0 && $avance_kpis > 0) {
            $avance_general = round(($avance_kpis / $contador_kpis), 2);
        }

        $array = array(
            "cantidad" => $contador_kpis,
            "avance_general" => $avance_general
        );

        return $array;
    }

    //AVANCE INDIVIDUAL DEL KPIS
    public function AvanceKPI_2($data)
    {

        if ($data) {
            //ULTIMO METAS
            $ultimo_meta = 0;
            $cant_avances = 0;

            //SOLO APLICA PARA UNIDAD DE MEDIDA - Horas-Minutos-Segundos
            //SOLO APLICA PARA UNIDAD DE MEDIDA - Horas-Minutos-Segundos
            //SOLO APLICA PARA UNIDAD DE MEDIDA - Horas-Minutos-Segundos
            if ($data["unidad_medida"] == 4) {
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
            if ($data["enero"] != 0) {
                $ultimo_meta = $data["enero"];
            }
            if ($data["febrero"] != 0) {
                $ultimo_meta = $data["febrero"];
            }
            if ($data["marzo"] != 0) {
                $ultimo_meta = $data["marzo"];
            }
            if ($data["abril"] != 0) {
                $ultimo_meta = $data["abril"];
            }
            if ($data["mayo"] != 0) {
                $ultimo_meta = $data["mayo"];
            }
            if ($data["junio"] != 0) {
                $ultimo_meta = $data["junio"];
            }

            if ($data["julio"] != 0) {
                $ultimo_meta =  $data["julio"];
            }
            if ($data["agosto"] != 0) {
                $ultimo_meta =  $data["agosto"];
            }
            if ($data["septiembre"] != 0) {
                $ultimo_meta =  $data["septiembre"];
            }
            if ($data["octubre"] != 0) {
                $ultimo_meta =  $data["octubre"];
            }
            if ($data["noviembre"] != 0) {
                $ultimo_meta =  $data["noviembre"];
            }
            if ($data["diciembre"] != 0) {
                $ultimo_meta =  $data["diciembre"];
            }

            //ULTIMO AVANCE
            $ultimo_avance = 0;

            //SOLO SE TOMA EL AVANCE SI EL VALOR ES DIFERENTE DE 0 SUMAR LA CANTIDAD DE AVANCES 
            //SOLO SE TOMA EL AVANCE SI EL VALOR ES DIFERENTE DE 0 SUMAR LA CANTIDAD DE AVANCES 
            if ($data["avance_1"] != "") {
                $cant_avances++;
                $ultimo_avance =  $data["avance_1"];
            }
            if ($data["avance_2"] != "") {
                $cant_avances++;
                $ultimo_avance =  $data["avance_2"];
            }
            if ($data["avance_3"] != "") {
                $cant_avances++;
                $ultimo_avance =  $data["avance_3"];
            }
            if ($data["avance_4"] != "") {
                $cant_avances++;
                $ultimo_avance =  $data["avance_4"];
            }
            if ($data["avance_5"] != "") {
                $cant_avances++;
                $ultimo_avance =  $data["avance_5"];
            }
            if ($data["avance_6"] != "") {
                $cant_avances++;
                $ultimo_avance =  $data["avance_6"];
            }

            if ($data["avance_7"] != "") {
                $cant_avances++;
                $ultimo_avance =  $data["avance_7"];
            }
            if ($data["avance_8"] != "") {
                $cant_avances++;
                $ultimo_avance =  $data["avance_8"];
            }
            if ($data["avance_9"] != "") {
                $cant_avances++;
                $ultimo_avance =  $data["avance_9"];
            }
            if ($data["avance_10"] != "") {
                $cant_avances++;
                $ultimo_avance =  $data["avance_10"];
            }
            if ($data["avance_11"] != "") {
                $cant_avances++;
                $ultimo_avance =  $data["avance_11"];
            }
            if ($data["avance_12"] != "") {
                $cant_avances++;
                $ultimo_avance =  $data["avance_12"];
            }

            //VARIABLES LOCALES
            $tipo_calculo = $data["tipo_calculo"];
            $tipo_resultado = $data["tipo_resultado"];
            $unidad_medida = $data["unidad_medida"];

            $cantidad_meses = 0;

            //CASO 2 SOLO TOMA LAS METAS
            $suma_avances = $data["avance_7"] + $data["avance_8"] + $data["avance_9"] + $data["avance_10"] + $data["avance_11"] + $data["avance_12"] + $data["avance_1"] + $data["avance_2"] + $data["avance_3"] + $data["avance_4"] + $data["avance_5"] + $data["avance_6"];

            if ($data["avance_7"] == "" && $data["avance_8"] == "" && $data["avance_9"] == "" && $data["avance_10"] == "" && $data["avance_11"] == "" && $data["avance_12"] == "" && $data["avance_1"] == "" && $data["avance_2"] == "" && $data["avance_3"] == "" && $data["avance_4"] == "" && $data["avance_5"] == "" && $data["avance_6"] == "") {
                $suma_avances = '';
            }

            //EN CASO DE NO TENER NINGUN AVANCE

            $PROGRESO_KPI = 0;
            //ACENDENTE
            if ($tipo_calculo == 1 && $suma_avances !== "") {

                $PROGRESO_KPI = 0;
                if ($suma_avances > 0) {

                    //ESTA ES LA FORMULA DE JULIAN
                    $PROGRESO_KPI = $suma_avances / $cant_avances;
                    $PROGRESO_KPI = round($PROGRESO_KPI, 2);

                    $PROGRESO_KPI = $PROGRESO_KPI * 100 / $data["meta"];
                    $PROGRESO_KPI = round($PROGRESO_KPI, 2);

                    //ACUMULATIVO
                    if ($tipo_resultado == 3) {
                        $PROGRESO_KPI = $ultimo_avance * 100 / $ultimo_meta;
                        $PROGRESO_KPI = round($PROGRESO_KPI, 2);
                    }
                }
            }

            //DESCENDENTE
            if ($tipo_calculo == 2 && $suma_avances !== "") {

                $PROM = 0;
                if ($suma_avances > 0) {
                    //ESTA ES LA FORMULA DE JULIAN


                    $PROM = $suma_avances / $cant_avances;
                    $PROM = round($PROM, 2);
                    $PROGRESO_KPI = $data["meta"] * 100 / $PROM;
                    $PROGRESO_KPI = round($PROGRESO_KPI, 2);


                    //ACUMULATIVO
                    if ($tipo_resultado == 3) {
                        $PROGRESO_KPI = $ultimo_meta * 100 / $ultimo_avance;
                        $PROGRESO_KPI = round($PROGRESO_KPI, 2);

                        //EN CASO DE QUE EL SEGUIMIENTO SERA MA
                        if ($ultimo_avance == 0) {
                            if ($ultimo_meta > $ultimo_avance) {
                                $PROGRESO_KPI = 100;
                            }
                        }

                        //print_r($ultimo_avance);

                    }
                }

                if (($suma_avances === 0 || $suma_avances === 0.0) && $data["meta"] > 0) {
                    $PROGRESO_KPI = 100;
                }
            }



            //SI LA META ES 0
            if ($data["meta"] == '0') {
                $PROGRESO_KPI = 0;
                if ($suma_avances > 0) {
                    $avance = ($suma_avances / $cant_avances);
                    $PROGRESO_KPI = max(0, 100 - ($avance * 10));
                    $PROGRESO_KPI = round($PROGRESO_KPI, 1);

                    //SOLO PARA LOS CASO DE ACUMULATIVO
                    if ($tipo_resultado == 3) {
                        $PROGRESO_KPI = max(0, 100 - ($ultimo_avance * 10));
                        $PROGRESO_KPI = round($PROGRESO_KPI, 1);
                    }
                }
            }



            //SOLA PARA VALOR ABSOLUTO ACUMULATIVO O NO ACUMULATIVO
            if ($tipo_calculo == 3) {

                if ($ultimo_avance > 0) {
                    $PROGRESO_KPI = 0;
                }
                if ($ultimo_avance == 0) {
                    $PROGRESO_KPI = 100;
                }
            }




            //NUEVO PORCENTAJE 
            if ($PROGRESO_KPI > 100) {
                $PROGRESO_KPI = 100;
            }

            if ($PROGRESO_KPI < -100) {
                $PROGRESO_KPI = -100;
            }

            if ($data["meta"] == 0 && $PROGRESO_KPI == 0 && $tipo_calculo != 3) {
                //$PROGRESO_KPI = 100;
            }



            //echo $PROGRESO_KPI;
            //echo "<br>";


            return $PROGRESO_KPI;

            //$porcentaje_global += $PROGRESO_KPI;
        } else {
            return 0;
        }
    }


    //KPIS DEL COLABORADOR
    public function Kpis_colaborador($id_empleado, $id_empresa, $anio)
    {
        global $connect_kpis;
        $array = array();

        $sentencia =
            "SELECT
            Kpis.*,
            puntacana_admin.Vicepresidencia.nombre as vicepresidencia_nombre,
            puntacana_admin.Areas.nombre as area_nombre,
            puntacana_admin.Empleados.role as empleado_role,
            puntacana_admin.Roles.nombre as role_nombre
        FROM
            Kpis
        INNER JOIN
            Kpis_Colaborador ON Kpis_Colaborador.id_kpi = Kpis.id
        INNER JOIN
            Frecuencia_Kpis ON Frecuencia_Kpis.id_kpi = Kpis.id
        LEFT JOIN
            puntacana_admin.Empleados ON puntacana_admin.Empleados.id = Kpis.id_empleado
        LEFT JOIN
            puntacana_admin.Roles ON puntacana_admin.Roles.id = puntacana_admin.Empleados.role
        LEFT JOIN
            puntacana_admin.Vicepresidencia ON puntacana_admin.Vicepresidencia.id = Kpis.area_macro
        LEFT JOIN
            puntacana_admin.Areas ON puntacana_admin.Areas.id = Kpis.area_proceso
        WHERE
            Kpis.id_empresa = '$id_empresa'
            AND Kpis.anio = '$anio'
            AND Kpis_Colaborador.id_colaborador = '$id_empleado'
        ";
        $query = mysqli_query($connect_kpis, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {

            $data["porcentaje_avance"] = $this->AvanceKPI($data["id"]);
            $data["color_avance"] = EscalaColor($data["porcentaje_avance"]);
            array_push($array, $data);
        }

        return $array;
    }

    public function equipos($id_empleado, $id_empresa, $anio)
    {
        global $connect_kpis;
        global $connect_admin;
        $sentencia = "";

        //VALIDAMOS SI ES LIDER DE VICEPRESIDENCIA
        $queryValidar = mysqli_query($connect_admin, "SELECT * FROM Lideres_Vicepresidencia WHERE id_lider = " . $id_empleado . " ");
        if (mysqli_num_rows($queryValidar) > 0) {

            $listadoLV = "";
            while ($dataValidar = mysqli_fetch_array($queryValidar)) {
                $listadoLV .= $dataValidar["id_vicepresidencia"] . ",";
            }
            $listadoLV = substr($listadoLV, 0, -1);
            $complemento = "AND Empleados.unidad_corporativa IN ($listadoLV)";

            $sentencia  = "
                SELECT 
                Empleados.id AS id_empleado, 
                Empleados.nombre AS nombre, 
                Empleados.documento AS documento, 
                Cargos.nombre AS cargo 
                FROM Empleados 
                LEFT JOIN Cargos ON Cargos.id = Empleados.id_cargo
                WHERE Empleados.id_empresa = " . $id_empresa . " $complemento AND Empleados.estado = 1 ORDER BY nombre 
            ";
        } else {
            $sentencia = "
            SELECT
                Lideres.*, 
                Empleados.id AS id_empleado, 
                Empleados.nombre AS nombre, 
                Empleados.id AS id_empleado, 
                Empleados.documento AS documento, 
                Cargos.nombre AS cargo 

            FROM
                Lideres 
                LEFT JOIN Empleados ON Empleados.id = Lideres.id_empleado 
                LEFT JOIN Cargos ON Cargos.id = Empleados.id_cargo
            WHERE
                Lideres.id_jefe = '" . $id_empleado . "' AND Lideres.id_empresa = '" . $id_empresa . "' 
            ";
        }

        $array = array();

        $query = mysqli_query($connect_admin, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {

            
            $resultados = $this->kpis_empleado($data["id_empleado"], $id_empresa, $anio);


            $data["asignados"] = $resultados["cantidad"];
            $data["progreso"] = $resultados["avance_general"];
            $data["acciones"] = 0;
            $data["progreso_acciones"] = 0;


            array_push($array, $data);
        }

        return $array;
    }

    //KPIS DEL COLABORADOR
    public function Kpis_compania($id_empleado, $id_empresa, $anio, $limite, $offset)
    {
        global $connect_kpis;
        $array = array();

        $sentencia =
            "SELECT
            Kpis.id
        FROM
            Kpis
        INNER JOIN
            Frecuencia_Kpis ON Frecuencia_Kpis.id_kpi = Kpis.id
        WHERE
            Kpis.id_empresa = '$id_empresa'
            AND Kpis.anio = '$anio'
        ";
        $query = mysqli_query($connect_kpis, $sentencia);
        $total_kpis = mysqli_num_rows($query);

        $array["total_kpis"] = $total_kpis;

        $sentencia =
            "SELECT
            Kpis.*,
            puntacana_admin.Vicepresidencia.nombre as vicepresidencia_nombre,
            puntacana_admin.Areas.nombre as area_nombre,
            puntacana_admin.Empleados.role as empleado_role,
            puntacana_admin.Roles.nombre as role_nombre
        FROM
            Kpis
        INNER JOIN
            Frecuencia_Kpis ON Frecuencia_Kpis.id_kpi = Kpis.id
        LEFT JOIN
            puntacana_admin.Empleados ON puntacana_admin.Empleados.id = Kpis.id_empleado
        LEFT JOIN
            puntacana_admin.Roles ON puntacana_admin.Roles.id = puntacana_admin.Empleados.role
        LEFT JOIN
            puntacana_admin.Vicepresidencia ON puntacana_admin.Vicepresidencia.id = Kpis.area_macro
        LEFT JOIN
            puntacana_admin.Areas ON puntacana_admin.Areas.id = Kpis.area_proceso
        WHERE
            Kpis.id_empresa = '$id_empresa'
            AND Kpis.anio = '$anio'
        LIMIT $limite OFFSET $offset
        ";
        $query = mysqli_query($connect_kpis, $sentencia);

        while ($data = mysqli_fetch_assoc($query)) {
            array_push($array, $data);
        }

        return $array;
    }

    public function kpis_empleado($id_empleado, $id_empresa, $anio)
    {
        global $connect_kpis;

        $avance_kpis = 0;
        $contador_kpis = 0;


        $sentencia = "
        SELECT DISTINCT 
            Kpis.*
        FROM
            Kpis
        INNER JOIN Kpis_Colaborador ON Kpis_Colaborador.id_kpi = Kpis.id
        INNER JOIN Frecuencia_Kpis ON Frecuencia_Kpis.id_kpi = Kpis.id
        WHERE
            Kpis.id_empresa = " . $id_empresa . " AND Kpis.anio = " . $anio . " AND Kpis_Colaborador.id_colaborador = '" . $id_empleado . "'
        ORDER BY
            Kpis.indicador ASC;
        ";
        $query = mysqli_query($connect_kpis, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {

            $avance = $this->AvanceKPI_2($data["id"]);

            $avance_kpis += $avance;
            $contador_kpis++;


            //array_push($array, $nodo);
        }

        $avance_general = 0;
        if ($contador_kpis > 0) {
            $avance_general = round(($avance_kpis / $contador_kpis), 2);
        }


        $array = array(
            "cantidad" => $contador_kpis,
            "avance_general" => $avance_general
        );


        return $array;
    }

    //AVANCE INDIVIDUAL DEL KPIS
    public function AvanceKPI($id_kpis)
    {

        global $connect_kpis;

        $sentencia = "
        SELECT
            Frecuencia_Kpis.*, 
            Kpis.tipo_calculo, 
            Kpis.meta AS meta, 
            Kpis.tipo_calculo AS tipo_calculo, 
            Kpis.tipo_resultado AS tipo_resultado, 
            Kpis.frecuencia AS frecuencia,  
            Kpis.unidad_medida AS unidad_medida
        FROM
        Frecuencia_Kpis
            LEFT JOIN Kpis ON Kpis.id = Frecuencia_Kpis.id_kpi
        WHERE
            Frecuencia_Kpis.id_kpi = '" . $id_kpis . "'
        ";

        $query = mysqli_query($connect_kpis, $sentencia);
        $data = mysqli_fetch_array($query);

        if ($query->num_rows > 0) {
            //ULTIMO METAS
            $ultimo_meta = 0;
            $cant_avances = 0;

            //SOLO APLICA PARA UNIDAD DE MEDIDA - Horas-Minutos-Segundos
            //SOLO APLICA PARA UNIDAD DE MEDIDA - Horas-Minutos-Segundos
            //SOLO APLICA PARA UNIDAD DE MEDIDA - Horas-Minutos-Segundos
            if ($data["unidad_medida"] == 4) {
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
            if ($data["enero"] != 0) {
                $ultimo_meta = $data["enero"];
            }
            if ($data["febrero"] != 0) {
                $ultimo_meta = $data["febrero"];
            }
            if ($data["marzo"] != 0) {
                $ultimo_meta = $data["marzo"];
            }
            if ($data["abril"] != 0) {
                $ultimo_meta = $data["abril"];
            }
            if ($data["mayo"] != 0) {
                $ultimo_meta = $data["mayo"];
            }
            if ($data["junio"] != 0) {
                $ultimo_meta = $data["junio"];
            }

            if ($data["julio"] != 0) {
                $ultimo_meta =  $data["julio"];
            }
            if ($data["agosto"] != 0) {
                $ultimo_meta =  $data["agosto"];
            }
            if ($data["septiembre"] != 0) {
                $ultimo_meta =  $data["septiembre"];
            }
            if ($data["octubre"] != 0) {
                $ultimo_meta =  $data["octubre"];
            }
            if ($data["noviembre"] != 0) {
                $ultimo_meta =  $data["noviembre"];
            }
            if ($data["diciembre"] != 0) {
                $ultimo_meta =  $data["diciembre"];
            }

            //ULTIMO AVANCE
            $ultimo_avance = 0;

            //SOLO SE TOMA EL AVANCE SI EL VALOR ES DIFERENTE DE 0 SUMAR LA CANTIDAD DE AVANCES 
            //SOLO SE TOMA EL AVANCE SI EL VALOR ES DIFERENTE DE 0 SUMAR LA CANTIDAD DE AVANCES 
            if ($data["avance_1"] != "") {
                $cant_avances++;
                $ultimo_avance =  $data["avance_1"];
            }
            if ($data["avance_2"] != "") {
                $cant_avances++;
                $ultimo_avance =  $data["avance_2"];
            }
            if ($data["avance_3"] != "") {
                $cant_avances++;
                $ultimo_avance =  $data["avance_3"];
            }
            if ($data["avance_4"] != "") {
                $cant_avances++;
                $ultimo_avance =  $data["avance_4"];
            }
            if ($data["avance_5"] != "") {
                $cant_avances++;
                $ultimo_avance =  $data["avance_5"];
            }
            if ($data["avance_6"] != "") {
                $cant_avances++;
                $ultimo_avance =  $data["avance_6"];
            }

            if ($data["avance_7"] != "") {
                $cant_avances++;
                $ultimo_avance =  $data["avance_7"];
            }
            if ($data["avance_8"] != "") {
                $cant_avances++;
                $ultimo_avance =  $data["avance_8"];
            }
            if ($data["avance_9"] != "") {
                $cant_avances++;
                $ultimo_avance =  $data["avance_9"];
            }
            if ($data["avance_10"] != "") {
                $cant_avances++;
                $ultimo_avance =  $data["avance_10"];
            }
            if ($data["avance_11"] != "") {
                $cant_avances++;
                $ultimo_avance =  $data["avance_11"];
            }
            if ($data["avance_12"] != "") {
                $cant_avances++;
                $ultimo_avance =  $data["avance_12"];
            }

            //VARIABLES LOCALES
            $tipo_calculo = $data["tipo_calculo"];
            $tipo_resultado = $data["tipo_resultado"];
            $unidad_medida = $data["unidad_medida"];

            $cantidad_meses = 0;

            //CASO 2 SOLO TOMA LAS METAS
            $suma_avances = $data["avance_7"] + $data["avance_8"] + $data["avance_9"] + $data["avance_10"] + $data["avance_11"] + $data["avance_12"] + $data["avance_1"] + $data["avance_2"] + $data["avance_3"] + $data["avance_4"] + $data["avance_5"] + $data["avance_6"];

            if ($data["avance_7"] == "" && $data["avance_8"] == "" && $data["avance_9"] == "" && $data["avance_10"] == "" && $data["avance_11"] == "" && $data["avance_12"] == "" && $data["avance_1"] == "" && $data["avance_2"] == "" && $data["avance_3"] == "" && $data["avance_4"] == "" && $data["avance_5"] == "" && $data["avance_6"] == "") {
                $suma_avances = '';
            }

            //EN CASO DE NO TENER NINGUN AVANCE

            $PROGRESO_KPI = 0;
            //ACENDENTE
            if ($tipo_calculo == 1 && $suma_avances !== "") {

                $PROGRESO_KPI = 0;
                if ($suma_avances > 0) {

                    //ESTA ES LA FORMULA DE JULIAN
                    $PROGRESO_KPI = $suma_avances / $cant_avances;
                    $PROGRESO_KPI = round($PROGRESO_KPI, 2);

                    $PROGRESO_KPI = $PROGRESO_KPI * 100 / $data["meta"];
                    $PROGRESO_KPI = round($PROGRESO_KPI, 2);

                    //ACUMULATIVO
                    if ($tipo_resultado == 3) {
                        $PROGRESO_KPI = $ultimo_avance * 100 / $ultimo_meta;
                        $PROGRESO_KPI = round($PROGRESO_KPI, 2);
                    }
                }
            }

            //DESCENDENTE
            if ($tipo_calculo == 2 && $suma_avances !== "") {

                $PROM = 0;
                if ($suma_avances > 0) {
                    //ESTA ES LA FORMULA DE JULIAN


                    $PROM = $suma_avances / $cant_avances;
                    $PROM = round($PROM, 2);
                    $PROGRESO_KPI = $data["meta"] * 100 / $PROM;
                    $PROGRESO_KPI = round($PROGRESO_KPI, 2);


                    //ACUMULATIVO
                    if ($tipo_resultado == 3) {
                        $PROGRESO_KPI = $ultimo_meta * 100 / $ultimo_avance;
                        $PROGRESO_KPI = round($PROGRESO_KPI, 2);

                        //EN CASO DE QUE EL SEGUIMIENTO SERA MA
                        if ($ultimo_avance == 0) {
                            if ($ultimo_meta > $ultimo_avance) {
                                $PROGRESO_KPI = 100;
                            }
                        }

                        //print_r($ultimo_avance);

                    }
                }

                if (($suma_avances === 0 || $suma_avances === 0.0) && $data["meta"] > 0) {
                    $PROGRESO_KPI = 100;
                }
            }



            //SI LA META ES 0
            if ($data["meta"] == '0') {
                $PROGRESO_KPI = 0;
                if ($suma_avances > 0) {
                    $avance = ($suma_avances / $cant_avances);
                    $PROGRESO_KPI = max(0, 100 - ($avance * 10));
                    $PROGRESO_KPI = round($PROGRESO_KPI, 1);

                    //SOLO PARA LOS CASO DE ACUMULATIVO
                    if ($tipo_resultado == 3) {
                        $PROGRESO_KPI = max(0, 100 - ($ultimo_avance * 10));
                        $PROGRESO_KPI = round($PROGRESO_KPI, 1);
                    }
                }
            }



            //SOLA PARA VALOR ABSOLUTO ACUMULATIVO O NO ACUMULATIVO
            if ($tipo_calculo == 3) {

                if ($ultimo_avance > 0) {
                    $PROGRESO_KPI = 0;
                }
                if ($ultimo_avance == 0) {
                    $PROGRESO_KPI = 100;
                }
            }




            //NUEVO PORCENTAJE 
            if ($PROGRESO_KPI > 100) {
                $PROGRESO_KPI = 100;
            }

            if ($PROGRESO_KPI < -100) {
                $PROGRESO_KPI = -100;
            }

            if ($data["meta"] == 0 && $PROGRESO_KPI == 0 && $tipo_calculo != 3) {
                //$PROGRESO_KPI = 100;
            }



            //echo $PROGRESO_KPI;
            //echo "<br>";


            return $PROGRESO_KPI;

            //$porcentaje_global += $PROGRESO_KPI;
        } else {
            return 0;
        }
    }

    /*
    public function Kpis_seguimiento_equipo($id_empleado, $id_empresa, $anio)
    {
        global $connect_kpis;
        $array = array();

        $sentencia ="
        SELECT
            DISTINCT(K.Id) AS id, K.tipo_kpi, K.anio, K.area_proceso, K.subproceso, K.objetivo_sg, K.indicador, K.objetivo_indicador,
                    K.formula, K.resultado_anterior, K.unidad_medida, K.tipo_calculo, K.meta, K.frecuencia, K.tipo_resultado, K.obj_meses
        FROM
            Kpis K
        INNER JOIN
            Kpis_Colaborador KC ON KC.id_kpi = K.id
        INNER JOIN
            Frecuencia_Kpis FKP ON FKP.id_kpi = K.id
        WHERE
            K.id_empresa = " . $id_empresa . " AND K.anio = " . $anio . " AND KC.id_colaborador = '" . $id_empleado . "' ORDER BY K.indicador ASC
        ";

        $query = mysqli_query($connect_kpis, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {
            array_push($array, $data);
        }

        return $array;
    }
        */

    public function Empleado($id)
    {
        global $connect_admin;

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
            puntacana_admin.Vicepresidencia.nombre AS nombre_vicepresidencia,
            puntacana_okrs.Roles_Okrs.nombre_rol AS rol
        FROM
            Empleados
        LEFT JOIN
            Cargos ON Cargos.id = Empleados.id_cargo
        LEFT JOIN
            Areas ON Areas.id = Empleados.area
        LEFT JOIN
            puntacana_admin.Estructura_Empresa ON Empleados.area = puntacana_admin.Estructura_Empresa.area
        LEFT JOIN
            puntacana_admin.Vicepresidencia ON puntacana_admin.Vicepresidencia.id = puntacana_admin.Estructura_Empresa.vicepresidencia
        LEFT JOIN
            puntacana_okrs.Roles_Okrs ON puntacana_okrs.Roles_Okrs.id = Empleados.role
        WHERE
            Empleados.id = '" . $id . "'
        ";
        $query = mysqli_query($connect_admin, $sentencia);
        $data = mysqli_num_rows($query) > 0 ? mysqli_fetch_assoc($query) : array();

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

    /*
    //PARA CALCULAR LOS MESES
    public function CalcularMes($meta, $avance, $tipo_calculo){

        $porcentaje = 0;

        if($meta != 0){
            if($avance != 0){
                if($tipo_calculo == 1){
                    $porcentaje = $avance*100/$meta;
                }
                if($tipo_calculo == 2){
                    $porcentaje = $meta*100/$avance;
                    
                    
                }
            }
        }

        //ANDREY INDICA QUE SI LA TENDENCIA ES ABSOLUTA Y EL AVANCE  0  ES IGUAL A (100%) SI ES DIFERENTE DE 0 EL PORCENTAJE ES IGUAL A (0%)
        if($tipo_calculo == 3){
            if($avance != 0){
                $porcentaje = 0;
            }  
            if($avance == 0){
                $porcentaje = 100;
            }  
        }

        
        if($porcentaje > 100){
            $porcentaje = 100;
        }

        if($porcentaje < -100){
            $porcentaje = -100;
        }
        


        return $porcentaje;
    }

    //PARA CALCULAR EL PROGRESO
    //PARA CALCULAR EL PROGRESO
    //PARA CALCULAR EL PROGRESO
    public function CalculoProgresoMes($meta, $avance){
        $progreso = 0;
        if ($meta == 0 && $avance == 0) {
            $progreso = 100;
        }

        if ($avance == 0 && ($meta > 0 || $meta < 0)) {
            $progreso = 100;
        }

        if ($meta > 0 && $avance == 0) {
            $progreso = 100;
        }

        if ($meta == 0 && $avance > 0) {
            $progreso = 100;
        }

        if ($progreso != 100) {
            if ($meta > 0 && $avance < 0) {
                $progreso = (round(($meta / $avance) * 100, 2)) * (-1);
            } else if ($meta == 0 && $avance > 0) {
                $progreso = 100 - ($avance * 10);
            } else {
                // $progreso = ($avance*100)/$meta; /// NUEVO CODIGO
                // $progreso = round($progreso, 2); /// NUEVO CODIGO
                $progreso = round(($meta / $avance) * 100, 2);
            }
        }

        return $progreso;
    }

    //OBTENER EL SEGUIMIENTO DE LOS MESES
    //OBTENER EL SEGUIMIENTO DE LOS MESES 
    //OBTENER EL SEGUIMIENTO DE LOS MESES
    public function ObtenerSegumientoMes($meta, $avance, $tipo){

        $absoluto = 0;
        $progreso = 0;

        if($meta > 0 ){
            $progreso = round(($avance * 100) / $meta, 2);
        }
        //DESCENDENTE
        if ($tipo == 2) {
            if ($avance != "" && $avance != null) {
                if ($meta == 0 && $avance <= 0) {
                    $progreso = 100;
                }else if ($meta == 0 && $avance > 0) {
                    $progreso = max(0, 100 - ($avance * 10));
                }else {
                    $progreso = CalculoProgresoMes($meta, $avance, 1);
                }
            }
        } 
        
        //ASCENDENTE
        if($tipo == 1){
            if ($meta == 0 && $avance != null) {
                if ($meta == 0 && $avance < 0) {
                    $progreso = max(0, 100 + ($avance * 10));
                } else {
                    $progreso = CalculoProgresoDesMes($meta, $avance, 1);
                }
            }
        }
        //ABSOLUTO
        else{
            if ($meta == 0 && $avance != null) {
                if ($meta == 0 && $avance <= 0) {
                    $progreso = 100;
                } else {
                    $progreso = 0;
                    $absoluto = 1;
                }
            }
        }

        if (is_infinite($progreso) || is_nan($progreso)) {
            $progreso = 0;
        }
        if ($progreso > 100) {
            $progreso = 100;
        }
        return $progreso;
    }
    */

    //PARA OBTENER EL PROMEDIO DE LOS KPIS - RECIBE EL EMPLEADO, LA EMPRESA, ANIO
    //PARA OBTENER EL PROMEDIO DE LOS KPIS - RECIBE EL EMPLEADO, LA EMPRESA, ANIO
    public function ResultadoKpis($id_empleado, $id_empresa, $anio)
    {
        global $connect_kpis;

        $porcentaje_global = 0;
        //CONSULTA GOBLAL => Kpis->Kpis_Colaborador->Frecuencia_Kpis
        $sentencia = "
        SELECT 
            Kpis.id AS id_kpi, 
            Kpis.meta AS meta, 
            Kpis.tipo_calculo AS tipo_calculo, 
            Kpis.tipo_resultado AS tipo_resultado, 
            Kpis.frecuencia AS frecuencia,  
            Kpis.unidad_medida AS unidad_medida,  
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
        LEFT JOIN Kpis_Colaborador ON Kpis_Colaborador.id_kpi = Kpis.id
        LEFT JOIN Frecuencia_Kpis ON Frecuencia_Kpis.id_kpi = Kpis.id
        WHERE
            Kpis.id_empresa = '" . $id_empresa . "' AND Kpis.anio = '" . $anio . "' AND Kpis_Colaborador.id_colaborador = '" . $id_empleado . "'  
        GROUP BY Kpis.id      
        ORDER BY
            Kpis.indicador ASC;
        ";
        //AND Kpis.id = 144    
        //CICLO DE LA CONSULTA
        $query = mysqli_query($connect_kpis, $sentencia);
        while ($data = mysqli_fetch_array($query)) { 

            $data["avance_1"] = trim($data["avance_1"]);
            $data["avance_2"] = trim($data["avance_2"]);
            $data["avance_3"] = trim($data["avance_3"]);
            $data["avance_4"] = trim($data["avance_4"]);
            $data["avance_5"] = trim($data["avance_5"]);
            $data["avance_6"] = trim($data["avance_6"]);
            $data["avance_7"] = trim($data["avance_7"]);
            $data["avance_8"] = trim($data["avance_8"]);
            $data["avance_9"] = trim($data["avance_9"]);
            $data["avance_10"] = trim($data["avance_10"]);
            $data["avance_11"] = trim($data["avance_11"]); 
            $data["avance_12"] = trim($data["avance_12"]);

            //ULTIMO METAS
            $ultimo_meta = 0;
            $cant_avances = 0;

            //SOLO SE TOMAN LAS METAS QUE TIENE UN VALOR DIFERENTE A 0 PARA SUMAR LA CANTIDAD DE SEGUIMIENTOS
            //SOLO SE TOMAN LAS METAS QUE TIENE UN VALOR DIFERENTE A 0 PARA SUMAR LA CANTIDAD DE SEGUIMIENTOS
            if ($data["julio"] != 0) {
                $ultimo_meta =  $data["julio"];
            }
            if ($data["agosto"] != 0) {
                $ultimo_meta =  $data["agosto"];
            }
            if ($data["septiembre"] != 0) {
                $ultimo_meta =  $data["septiembre"];
            }
            if ($data["octubre"] != 0) {
                $ultimo_meta =  $data["octubre"];
            }
            if ($data["noviembre"] != 0) {
                $ultimo_meta =  $data["noviembre"];
            }
            if ($data["diciembre"] != 0) {
                $ultimo_meta =  $data["diciembre"];
            }

            if ($data["enero"] != 0) {
                $ultimo_meta = $data["enero"];
            }
            if ($data["febrero"] != 0) {
                $ultimo_meta = $data["febrero"];
            }
            if ($data["marzo"] != 0) {
                $ultimo_meta = $data["marzo"];
            }
            if ($data["abril"] != 0) {
                $ultimo_meta = $data["abril"];
            }
            if ($data["mayo"] != 0) {
                $ultimo_meta = $data["mayo"];
            }
            if ($data["junio"] != 0) {
                $ultimo_meta = $data["junio"];
            }

            //ULTIMO AVANCE
            $ultimo_avance = 0;

            //SOLO SE TOMA EL AVANCE SI EL VALOR ES DIFERENTE DE 0 SUMAR LA CANTIDAD DE AVANCES 
            //SOLO SE TOMA EL AVANCE SI EL VALOR ES DIFERENTE DE 0 SUMAR LA CANTIDAD DE AVANCES 
            if ($data["avance_7"] != "") {
                $cant_avances++;
                $ultimo_avance =  $data["avance_7"];
            }
            if ($data["avance_8"] != "") {
                $cant_avances++;
                $ultimo_avance =  $data["avance_8"];
            }
            if ($data["avance_9"] != "") {
                $cant_avances++;
                $ultimo_avance =  $data["avance_9"];
            }
            if ($data["avance_10"] != "") {
                $cant_avances++;
                $ultimo_avance =  $data["avance_10"];
            }
            if ($data["avance_11"] != "") {
                $cant_avances++;
                $ultimo_avance =  $data["avance_11"];
            }
            if ($data["avance_12"] != "") {
                $cant_avances++;
                $ultimo_avance =  $data["avance_12"];
            }

            if ($data["avance_1"] != "") {
                $cant_avances++;
                $ultimo_avance =  $data["avance_1"];
            }
            if ($data["avance_2"] != "") {
                $cant_avances++;
                $ultimo_avance =  $data["avance_2"];
            }
            if ($data["avance_3"] != "") {
                $cant_avances++;
                $ultimo_avance =  $data["avance_3"];
            }
            if ($data["avance_4"] != "") {
                $cant_avances++;
                $ultimo_avance =  $data["avance_4"];
            }
            if ($data["avance_5"] != "") {
                $cant_avances++;
                $ultimo_avance =  $data["avance_5"];
            }
            if ($data["avance_6"] != "") {
                $cant_avances++;
                $ultimo_avance =  $data["avance_6"];
            }

            //VARIABLES LOCALES
            $tipo_calculo = $data["tipo_calculo"];
            $tipo_resultado = $data["tipo_resultado"];
            $unidad_medida = $data["unidad_medida"];

            //SOLO APLICA PARA UNIDAD DE MEDIDA - Horas-Minutos-Segundos
            //SOLO APLICA PARA UNIDAD DE MEDIDA - Horas-Minutos-Segundos
            //SOLO APLICA PARA UNIDAD DE MEDIDA - Horas-Minutos-Segundos
            if ($data["unidad_medida"] == 4) {
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

            $cantidad_meses = 0;
            $suma_avances = $data["avance_7"] + $data["avance_8"] + $data["avance_9"] + $data["avance_10"] + $data["avance_11"] + $data["avance_12"] + $data["avance_1"] + $data["avance_2"] + $data["avance_3"] + $data["avance_4"] + $data["avance_5"] + $data["avance_6"];

            if ($data["avance_7"] == "" && $data["avance_8"] == "" && $data["avance_9"] == "" && $data["avance_10"] == "" && $data["avance_11"] == "" && $data["avance_12"] == "" && $data["avance_1"] == "" && $data["avance_2"] == "" && $data["avance_3"] == "" && $data["avance_4"] == "" && $data["avance_5"] == "" && $data["avance_6"] == "") {
                $suma_avances = '';
            }

            //$data["avance_7"] == NULL && $data["avance_8"] == NULL && $data["avance_9"] == NULL && $data["avance_10"] == NULL && $data["avance_11"] == NULL && $data["avance_12"] == NULL && $data["avance_1"] == NULL && $data["avance_2"] == NULL && $data["avance_3"] == NULL && $data["avance_4"] == NULL && $data["avance_5"] == NULL && $data["avance_6"] == NULL


            //EN CASO DE NO TENER NINGUN AVANCE
            //if(!$suma_avances){ $suma_avances = 0; }

            $PROGRESO_KPI = 0;
            //ACENDENTE
            if ($tipo_calculo == 1 && $suma_avances !== "") {

                //ESTA ES LA FORMULA DE JULIAN
                $PROGRESO_KPI = $suma_avances / $cant_avances;
                $PROGRESO_KPI = round($PROGRESO_KPI, 2);
                $PROGRESO_KPI = $PROGRESO_KPI * 100 / $data["meta"];
                $PROGRESO_KPI = round($PROGRESO_KPI, 2);

                //var_dump($PROGRESO_KPI);


                //ACUMULATIVO
                if ($tipo_resultado == 3) {
                    $PROGRESO_KPI = $ultimo_avance * 100 / $ultimo_meta;
                    $PROGRESO_KPI = round($PROGRESO_KPI, 2);
                }
            }


            //DESCENDENTE
            if ($tipo_calculo == 2 && $suma_avances !== "") {

                //ESTA ES LA FORMULA DE JULIAN
                $PROM = $suma_avances / $cant_avances;
                $PROM = round($PROM, 2);
                $PROGRESO_KPI = $data["meta"] * 100 / $PROM;
                $PROGRESO_KPI = round($PROGRESO_KPI, 2);



                //ACUMULATIVO
                if ($tipo_resultado == 3) {
                    $PROGRESO_KPI = $ultimo_meta * 100 / $ultimo_avance;
                    $PROGRESO_KPI = round($PROGRESO_KPI, 2);



                    //EN CASO DE QUE EL SEGUIMIENTO SERA MA
                    if ($ultimo_avance == 0) {
                        if ($ultimo_meta > $ultimo_avance) {
                            $PROGRESO_KPI = 100;
                        }
                    }
                }
            }

            //SI LA META ES 0
            if ($data["meta"] == '0') {

            
                $avance = 0;
                if( $suma_avances != 0){
                    $avance = ($suma_avances / $cant_avances);
                }
                $PROGRESO_KPI = max(0, 100 - ($avance * 10));
                $PROGRESO_KPI = round($PROGRESO_KPI, 1);



                //SOLO PARA LOS CASO DE ACUMULATIVO
                if ($tipo_resultado == 3) {
                    $PROGRESO_KPI = max(0, 100 - ($ultimo_avance * 10));
                    $PROGRESO_KPI = round($PROGRESO_KPI, 1);
                }
            }

            //SOLA PARA VALOR ABSOLUTO ACUMULATIVO O NO ACUMULATIVO
            if ($tipo_calculo == 3) {
                if ($ultimo_avance > 0) {
                    $PROGRESO_KPI = 0;
                }
                if ($ultimo_avance == 0) {
                    $PROGRESO_KPI = 100;
                }
            }


            //NUEVO PORCENTAJE 
            if ($PROGRESO_KPI > 100) {
                $PROGRESO_KPI = 100;
            }

            if ($PROGRESO_KPI < -100) {
                $PROGRESO_KPI = -100;
            }

            //echo $PROGRESO_KPI;
            //echo "<br>";

            $porcentaje_global += $PROGRESO_KPI;
        }

        //PROMEDIAMOS EL RESULTADO FINAL
        if ($query->num_rows > 0) {
            $porcentaje_global = ($porcentaje_global / $query->num_rows);
        } else {
            $porcentaje_global = 0;
        }

        return round($porcentaje_global, 2);
    }

    public function Areas($id_empresa)
    {
        global $connect_admin;
        $array = array();

        $sentencia =
            "SELECT
            Areas.*,
            Estructura_Empresa.vicepresidencia AS id_vicepresidencia
        FROM
            Areas
        LEFT JOIN
            Estructura_Empresa ON Estructura_Empresa.area = Areas.id
        WHERE
            Areas.id_empresa = '$id_empresa'
            AND Areas.estado = 1
        GROUP BY
            Areas.id
        ORDER BY
            Areas.nombre ASC";
        $query = mysqli_query($connect_admin, $sentencia);

        while ($data = mysqli_fetch_assoc($query)) {
            array_push($array, $data);
        }
        return $array;
    }

    public function Unidades_Organizativas($id_empresa)
    {
        global $connect_admin;
        $array = array();

        $sentencia =
            "SELECT
            *
        FROM
            Estructura_Empresa
        WHERE
            id_empresa = '$id_empresa' AND unidad_organizativa != ''
        ORDER BY
            unidad_organizativa ASC";
        $query = mysqli_query($connect_admin, $sentencia);

        while ($data = mysqli_fetch_assoc($query)) {
            array_push($array, $data);
        }
        return $array;
    }

    public function Empleados($id_empresa)
    {
        global $connect_admin;
        $array = array();

        $sentencia =
            "SELECT
            id, nombre, area
        FROM
            Empleados
        WHERE
            id_empresa = '$id_empresa'
            AND estado = 1
        ORDER BY
            nombre ASC";
        $query = mysqli_query($connect_admin, $sentencia);

        while ($data = mysqli_fetch_assoc($query)) {
            array_push($array, $data);
        }
        return $array;
    }

    public function Kpis_Asignados($id_empresa, $area_macro, $anio)
    {
        global $connect_kpis;
        $array = array();

        $sentencia =
            "SELECT
            *
        FROM
            Kpis
        WHERE
            id_empresa = '$id_empresa'
            AND area_macro = '$area_macro'
            AND anio = '$anio'
        ";

        $query = mysqli_query($connect_kpis, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {

            //EMPLEADO LIDER
            $empleado = $this->Empleado($data["id_empleado"]);
            $data["lider"] = $empleado;

            array_push($array, $data);
        }
        return $array;
    }

    public function Kpis_Asignados_area_proceso($id_empresa, $area_macro, $anio)
    {
        /*
        $filtros = "";

        if($_SESSION["anio_fill"]){ $filtros .= " AND Kpis.anio = '".$_SESSION["anio_fill"]."' "; }
        if($_SESSION["tipo_kpi_fill"]){ $filtros .= " AND Kpis.tipo_kpi = '".$_SESSION["tipo_kpi_fill"]."' "; }
        if($_SESSION["frecuencia_fill"]){ $filtros .= " AND Kpis.frecuencia = '".$_SESSION["frecuencia_fill"]."' "; }
        if($_SESSION["tipo_resultado_fill"]){ $filtros .= " AND Kpis.tipo_resultado = '".$_SESSION["tipo_resultado_fill"]."' "; }
        
        //if($_SESSION["periodo_inicio_fill"]){ $filtros .= " AND Kpis.tipo_resultado = '".$_SESSION["periodo_inicio_fill"]."' "; }
        //if($_SESSION["periodo_fin_fill"]){ $filtros .= " AND Kpis.tipo_resultado = '".$_SESSION["periodo_fin_fill"]."' "; }
        if($_SESSION["tipo_calculo_fill"]){ $filtros .= " AND Kpis.tipo_calculo = '".$_SESSION["tipo_calculo_fill"]."' "; }
        if($_SESSION["unidad_medida_fill"]){ $filtros .= " AND Kpis.unidad_medida = '".$_SESSION["unidad_medida_fill"]."' "; }
        */

        global $connect_kpis;
        $array = array();

        $sentencia = "
            SELECT 
            Kpis.id AS id_kpi,
            Kpis.*,
            puntacana_admin.Areas.nombre as area_nombre,
            puntacana_admin.Lideres_Area.id_lider as id_lider
            FROM
                Kpis
            LEFT JOIN
                puntacana_admin.Areas ON puntacana_admin.Areas.id = Kpis.area_proceso
            LEFT JOIN
                puntacana_admin.Lideres_Area ON puntacana_admin.Lideres_Area.id_area = Kpis.area_proceso
            WHERE
                Kpis.id_empresa = '$id_empresa'
                AND Kpis.area_macro = '$area_macro'
                
            GROUP BY
                Kpis.area_proceso
        ";

        $query = mysqli_query($connect_kpis, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {

            //EMPLEADO LIDER
            $empleado = $this->Empleado($data["id_lider"]);
            $data["lider"] = $empleado;

            //PORCENTAJE DE AVANCE
            $porcentaje_avance = $this->AvanceKPI($data["id"]);
            //print_r($porcentaje_avance);
            //echo "<br>";

            $data["porcentaje_avance"] = $porcentaje_avance;

            array_push($array, $data);
        }
        return $array;
    }

    public function Kpis_Administradores($id_empresa)
    {
        global $connect_kpis;
        $array = array();

        $sentencia =
            "SELECT
            *
        FROM
            Administradores_Kpi
        WHERE
            id_empresa = '$id_empresa'
        ";

        $query = mysqli_query($connect_kpis, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {

            //EMPLEADO
            $empleado = $this->Empleado($data["id_empleado"]);
            $data["empleado"] = $empleado;

            array_push($array, $data);
        }
        return $array;
    }

    public function Kpis_Objetivos_SG($id_empresa)
    {
        global $connect_kpis;
        $array = array();

        $sentencia =
            "SELECT
            Objetivo_Sg.*,
            puntacana_admin.Vicepresidencia.nombre as nombre_vicepresidencia,
            puntacana_admin.Areas.nombre as nombre_area
        FROM
            Objetivo_Sg
        LEFT JOIN
            puntacana_admin.Vicepresidencia ON puntacana_admin.Vicepresidencia.id = Objetivo_Sg.id_vp
        LEFT JOIN
            puntacana_admin.Areas ON puntacana_admin.Areas.id = Objetivo_Sg.id_area
        WHERE
            Objetivo_Sg.id_empresa = '$id_empresa'
        ";

        $query = mysqli_query($connect_kpis, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {

            //EMPLEADO
            /* $empleado = $this->Empleado($data["id_empleado"]);
            $data["empleado"] = $empleado; */

            array_push($array, $data);
        }
        return $array;
    }

    public function Kpis_Objetivo_SG($id_empresa, $id_objetivo)
    {
        global $connect_kpis;
        $data = array();

        $sentencia =
            "SELECT
            Objetivo_Sg.*,
            puntacana_admin.Vicepresidencia.nombre as nombre_vicepresidencia,
            puntacana_admin.Areas.nombre as nombre_area
        FROM
            Objetivo_Sg
        LEFT JOIN
            puntacana_admin.Vicepresidencia ON puntacana_admin.Vicepresidencia.id = Objetivo_Sg.id_vp
        LEFT JOIN
            puntacana_admin.Areas ON puntacana_admin.Areas.id = Objetivo_Sg.id_area
        WHERE
            Objetivo_Sg.id_empresa = '$id_empresa' AND Objetivo_Sg.id = '$id_objetivo'
        LIMIT 1
        ";

        $query = mysqli_query($connect_kpis, $sentencia);
        $data = mysqli_num_rows($query) > 0 ? mysqli_fetch_assoc($query) : array();

        return $data;
    }

    public function Unidad_Organizativa($id)
    {
        global $connect_admin;
        $data = array();

        $sentencia =
            "SELECT
            unidad_organizativa
        FROM
            Estructura_Empresa
        WHERE
            id = '$id'
        LIMIT 1
        ";

        $query = mysqli_query($connect_admin, $sentencia);
        $data = mysqli_num_rows($query) > 0 ? mysqli_fetch_assoc($query) : array();

        return $data["unidad_organizativa"];
    }

    public function Obtener_kpi($id_kpi)
    {
        global $connect_kpis;
        $data = array();

        $sentencia =
        "SELECT
            *
        FROM
            Kpis
        WHERE
            id = '$id_kpi'
        LIMIT 1
        ";

        $query = mysqli_query($connect_kpis, $sentencia);
        $data = mysqli_num_rows($query) > 0 ? mysqli_fetch_assoc($query) : array();

        return $data;
    }
}
