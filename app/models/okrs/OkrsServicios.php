<?php
class OkrsServicios
{

    //FUNCION PARA OBTENER LOS OKRS DE LA EMPRESA
    //FUNCION PARA OBTENER LOS OKRS DE LA EMPRESA
    //FUNCION PARA OBTENER LOS OKRS DE LA EMPRESA
    public function okrs_organizacion($id_empleado, $id_empresa){
        global $connect_okrs;

        $filtros = "";

        if ($_SESSION["anio_fill"]) {
            $filtros .= " AND Okrs.anio = '" . $_SESSION["anio_fill"] . "' ";
        }
        if ($_SESSION["vicepresidencias_fill"]) {
            $filtros .= " AND puntacana_admin.Estructura_Empresa.vicepresidencia = '" . $_SESSION["vicepresidencias_fill"] . "' ";
        }
        if ($_SESSION["objetivos_fill"]) {
            $filtros .= " AND Okrs.objetivos_estrategicos = '" . $_SESSION["objetivos_fill"] . "' ";
        }
        if ($_SESSION["okrs_fill"]) {
            $filtros .= " AND Okrs.id = '" . $_SESSION["okrs_fill"] . "' ";
        }
        if ($_SESSION["tipo_okrs_fill"]) {
            $filtros .= " AND Okrs.tipo = '" . $_SESSION["tipo_okrs_fill"] . "' ";
        }


        $array = array();

        $sentencia = "
        SELECT
            Okrs.id AS id_okrs, 
            Okrs_Areas.id_empresa AS id_empresa,
            Okrs.objetivo_okr AS objetivo,
            Okrs.id_empleado AS id_empleado_owner, 
            Okrs.tipo AS tipo_okrs,
            puntacana_admin.Estructura_Empresa.vicepresidencia AS id_vicepresidencia,
            puntacana_admin.Areas.nombre AS nombre_area,
            puntacana_admin.Empleados.unidad_organizativa as id_unidad_organizativa,
            puntacana_admin.Estructura_Empresa.unidad_organizativa as nombre_unidad_organizativa
        FROM
            Okrs_Areas 
        LEFT JOIN Okrs ON Okrs_Areas.id_okrs = Okrs.id
        LEFT JOIN puntacana_admin.Areas ON Okrs_Areas.id_area = puntacana_admin.Areas.id
        LEFT JOIN puntacana_admin.Empleados ON Okrs.id_empleado = puntacana_admin.Empleados.id
        LEFT JOIN puntacana_admin.Estructura_Empresa ON Okrs_Areas.id_area = puntacana_admin.Estructura_Empresa.area
        WHERE
            Okrs_Areas.id_empresa = '" . $id_empresa . "' 
            " . $filtros . " 
        GROUP BY
            Okrs.id 
        ORDER BY Okrs.objetivo_okr ASC
        ";

        //echo $sentencia;
        $query = mysqli_query($connect_okrs, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {

            //EMPLEADO
            $empleado = $this->Empleado($data['id_empleado_owner']);
            $data['empleado'] = $empleado;

            //RESULTADOS
            $resultados = $this->okrs_resultados($data["id_okrs"]);
            $data['resultados'] = $resultados;

            $porcentaje_avance = $this->PorcentajeAvanceOrks($data["id_okrs"]);
            $data['porcentaje_avance'] = $porcentaje_avance;

            $color = EscalaColor($porcentaje_avance);
            $data['color_avance'] = $color;


            if ($data['tipo_okrs'] == 1) {
                $data['tipo_okrs'] = "OKR Organizacional";
            } else {
                $data['tipo_okrs'] = "OKR Equipo";
            }


            array_push($array, $data);
        }
        return $array;
    }

    //FUNCION PARA OBTENER LOS OKRS DE LA EMPRESA
    //FUNCION PARA OBTENER LOS OKRS DE LA EMPRESA
    //FUNCION PARA OBTENER LOS OKRS DE LA EMPRESA
    public function okrs_organizacion_new($id_empresa){
        global $connect_okrs;

        $filtros = "";

        if ($_SESSION["anio_fill"]) {
            $filtros .= " AND Okrs.anio = '" . $_SESSION["anio_fill"] . "' ";
        }
        if ($_SESSION["vicepresidencias_fill"]) {
            $filtros .= " AND puntacana_admin.Estructura_Empresa.vicepresidencia = '" . $_SESSION["vicepresidencias_fill"] . "' ";
        }
        if ($_SESSION["objetivos_fill"]) {
            $filtros .= " AND Okrs.objetivos_estrategicos = '" . $_SESSION["objetivos_fill"] . "' ";
        }
        if ($_SESSION["okrs_fill"]) {
            $filtros .= " AND Okrs.id = '" . $_SESSION["okrs_fill"] . "' ";
        }
        if ($_SESSION["tipo_okrs_fill"]) {
            $filtros .= " AND Okrs.tipo = '" . $_SESSION["tipo_okrs_fill"] . "' ";
        }

        if ($_SESSION["responsables_fill"]) {
            $filtros .= " AND Okrs.id_empleado = '" . $_SESSION["responsables_fill"] . "' ";
        }


        $array = array();

        $sentencia = "
        SELECT
            Okrs.id AS id_okrs,
            Okrs.id_empresa AS id_empresa,
            Okrs.objetivo_okr AS objetivo,
            Okrs.id_empleado AS id_empleado_owner,
            Okrs.tipo AS tipo_okrs,
            puntacana_admin.Estructura_Empresa.vicepresidencia AS id_vicepresidencia,
            puntacana_admin.Areas.nombre AS nombre_area,
            puntacana_admin.Empleados.unidad_organizativa AS id_unidad_organizativa,
            puntacana_admin.Estructura_Empresa.unidad_organizativa AS nombre_unidad_organizativa
        FROM
            Okrs
        LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs.id
        LEFT JOIN puntacana_admin.Areas ON Okrs_Areas.id_area = puntacana_admin.Areas.id
        LEFT JOIN puntacana_admin.Empleados ON Okrs.id_empleado = puntacana_admin.Empleados.id
        LEFT JOIN puntacana_admin.Estructura_Empresa ON Okrs_Areas.id_area = puntacana_admin.Estructura_Empresa.area
        WHERE
            Okrs.id_empresa = '" . $id_empresa . "'  
            " . $filtros . " 
        GROUP BY Okrs.id
        ORDER BY Okrs.objetivo_okr ASC 
        ";

        //echo $sentencia;
        $query = mysqli_query($connect_okrs, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {

            //EMPLEADO
            $empleado = $this->Empleado($data['id_empleado_owner']);
            $data['empleado'] = $empleado;

            //RESULTADOS
            $resultados = $this->okrs_resultados($data["id_okrs"]);
            $data['resultados'] = $resultados;

            $porcentaje_avance = $this->PorcentajeAvanceOrks($data["id_okrs"]);
            $data['porcentaje_avance'] = $porcentaje_avance;

            $color = EscalaColor($porcentaje_avance);
            $data['color_avance'] = $color;


            if ($data['tipo_okrs'] == 1) {
                $data['tipo_okrs'] = "OKR Organizacional";
            } else {
                $data['tipo_okrs'] = "OKR Equipo";
            }


            array_push($array, $data);
        }
        return $array;
    }

    //FUNCION PARA OBTENER LOS OKRS DE LA EMPRESA
    //FUNCION PARA OBTENER LOS OKRS DE LA EMPRESA
    //FUNCION PARA OBTENER LOS OKRS DE LA EMPRESA
    public function okrs_organizacion_paginado($id_empresa, $posicion, $longitud){
        global $connect_okrs;

        $filtros = "";

        if ($_SESSION["anio_fill"]) {
            $filtros .= " AND Okrs.anio = '" . $_SESSION["anio_fill"] . "' ";
        }
        if ($_SESSION["vicepresidencias_fill"]) {
            $filtros .= " AND puntacana_admin.Estructura_Empresa.vicepresidencia = '" . $_SESSION["vicepresidencias_fill"] . "' ";
        }
        if ($_SESSION["objetivos_fill"]) {
            $filtros .= " AND Okrs.objetivos_estrategicos = '" . $_SESSION["objetivos_fill"] . "' ";
        }
        if ($_SESSION["okrs_fill"]) {
            $filtros .= " AND Okrs.id = '" . $_SESSION["okrs_fill"] . "' ";
        }
        if ($_SESSION["tipo_okrs_fill"]) {
            $filtros .= " AND Okrs.tipo = '" . $_SESSION["tipo_okrs_fill"] . "' ";
        }

        if ($_SESSION["responsables_fill"]) {
            $filtros .= " AND Okrs.id_empleado = '" . $_SESSION["responsables_fill"] . "' ";
        }


        


        $array = array();

        $sentencia = "
        SELECT
            Okrs.id AS id_okrs,
            Okrs.id_empresa AS id_empresa,
            Okrs.objetivo_okr AS objetivo,
            Okrs.id_empleado AS id_empleado_owner,
            Okrs.tipo AS tipo_okrs,
            puntacana_admin.Estructura_Empresa.vicepresidencia AS id_vicepresidencia,
            puntacana_admin.Areas.nombre AS nombre_area,
            puntacana_admin.Empleados.unidad_organizativa AS id_unidad_organizativa,
            puntacana_admin.Estructura_Empresa.unidad_organizativa AS nombre_unidad_organizativa
        FROM
            Okrs
        LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs.id
        LEFT JOIN puntacana_admin.Areas ON Okrs_Areas.id_area = puntacana_admin.Areas.id
        LEFT JOIN puntacana_admin.Empleados ON Okrs.id_empleado = puntacana_admin.Empleados.id
        LEFT JOIN puntacana_admin.Estructura_Empresa ON Okrs_Areas.id_area = puntacana_admin.Estructura_Empresa.area
        WHERE
            Okrs.id_empresa = '" . $id_empresa . "'  
            " . $filtros . " 
        GROUP BY Okrs.id
        ORDER BY Okrs.tipo ASC 
        LIMIT ".$posicion.",".$longitud."
        ";
        $query = mysqli_query($connect_okrs, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {

            //EMPLEADO
            $empleado = $this->Empleado($data['id_empleado_owner']);
            $data['empleado'] = $empleado; 
            $data['owner'] = $empleado;

            //RESULTADOS
            $resultados = $this->okrs_resultados($data["id_okrs"]);
            $data['resultados'] = $resultados;

            $porcentaje_avance = $this->PorcentajeAvanceOrks($data["id_okrs"]);
            $data['porcentaje_avance'] = $porcentaje_avance;

            $color = EscalaColor($porcentaje_avance);
            $data['color_avance'] = $color;


            if ($data['tipo_okrs'] == 1) {
                $data['tipo_okrs'] = "OKR Organizacional";
            } else {
                $data['tipo_okrs'] = "OKR Equipo";
            }


            array_push($array, $data);
        }
        return $array;
    }

    //FUNCION PARA OBTENER LOS OKRS DE LA EMPRESA
    //FUNCION PARA OBTENER LOS OKRS DE LA EMPRESA
    //FUNCION PARA OBTENER LOS OKRS DE LA EMPRESA
    public function okrs_vicepresidencias( $id_empresa, $id_vicepresidencia ){
        global $connect_okrs;

        $filtros = " AND Okrs_Areas.id_vicepresidencia = '".$id_vicepresidencia."' ";
        if ($_SESSION["anio_fill"]) {
            $filtros .= " AND Okrs.anio = '" . $_SESSION["anio_fill"] . "' ";
        }

        $array = array();

        $sentencia = "
        SELECT
            Okrs.id AS id_okrs,
            Okrs.id_empresa AS id_empresa,
            Okrs.objetivo_okr AS objetivo,
            Okrs.id_empleado AS id_empleado_owner,
            Okrs.tipo AS tipo_okrs,
            puntacana_admin.Estructura_Empresa.vicepresidencia AS id_vicepresidencia,
            puntacana_admin.Areas.nombre AS nombre_area,
            puntacana_admin.Empleados.unidad_organizativa AS id_unidad_organizativa,
            puntacana_admin.Estructura_Empresa.unidad_organizativa AS nombre_unidad_organizativa
        FROM
            Okrs
        LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs.id
        LEFT JOIN puntacana_admin.Areas ON Okrs_Areas.id_area = puntacana_admin.Areas.id
        LEFT JOIN puntacana_admin.Empleados ON Okrs.id_empleado = puntacana_admin.Empleados.id
        LEFT JOIN puntacana_admin.Estructura_Empresa ON Okrs_Areas.id_area = puntacana_admin.Estructura_Empresa.area
        WHERE
            Okrs.id_empresa = '" . $id_empresa . "'  
            " . $filtros . " 
        GROUP BY Okrs.id
        ORDER BY Okrs.objetivo_okr ASC 
        ";

        //echo $sentencia;
        $query = mysqli_query($connect_okrs, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {

            //EMPLEADO
            $empleado = $this->Empleado($data['id_empleado_owner']);
            $data['empleado'] = $empleado;

            //RESULTADOS
            $resultados = $this->okrs_resultados($data["id_okrs"]);
            $data['resultados'] = $resultados;

            $porcentaje_avance = $this->PorcentajeAvanceOrks($data["id_okrs"]);
            $data['porcentaje_avance'] = $porcentaje_avance;

            $color = EscalaColor($porcentaje_avance);
            $data['color_avance'] = $color;


            if ($data['tipo_okrs'] == 1) {
                $data['tipo_okrs'] = "OKR Organizacional";
            } else {
                $data['tipo_okrs'] = "OKR Equipo";
            }


            array_push($array, $data);
        }
        return $array;
    }


























    //FUNCION PARA OBTENER LOS OKRS DE AREA O EQUIPO DE TRABAJO
    //FUNCION PARA OBTENER LOS OKRS DE AREA O EQUIPO DE TRABAJO
    //FUNCION PARA OBTENER LOS OKRS DE AREA O EQUIPO DE TRABAJO
    public function okrs_area($id_empleado, $id_empresa, $id_area, $anio)
    {
        global $connect_okrs;

        $filtros = "";
        /*
        $_SESSION["anio_fill"] = $dtEmpresa["anio_curso"];
        $_SESSION["vicepresidencias_fill"] = "";
        $_SESSION["objetivos_fill"] = "";
        $_SESSION["okrs_fill"] = "";
        $_SESSION["responsables_fill"] = "";
        $_SESSION["tipo_okrs_fill"] = "";
        $_SESSION["periodo_fill"] = "";
        */

        if ($_SESSION["anio_fill"]) {
            $filtros .= " AND Okrs.anio = '" . $_SESSION["anio_fill"] . "' ";
        }
        if ($_SESSION["vicepresidencias_fill"]) {
            $filtros .= " AND puntacana_admin.Estructura_Empresa.vicepresidencia = '" . $_SESSION["vicepresidencias_fill"] . "' ";
        }
        if ($_SESSION["objetivos_fill"]) {
            $filtros .= " AND Okrs.objetivos_estrategicos = '" . $_SESSION["objetivos_fill"] . "' ";
        }
        if ($_SESSION["okrs_fill"]) {
            $filtros .= " AND Okrs.id = '" . $_SESSION["okrs_fill"] . "' ";
        }
        if ($_SESSION["tipo_okrs_fill"]) {
            $filtros .= " AND Okrs.tipo = '" . $_SESSION["tipo_okrs_fill"] . "' ";
        }

        //$_SESSION["tipo_okrs_fill"] = "";

        $array = array();

        $sentencia = "
        SELECT
            Okrs.id AS id_okrs, 
            Okrs_Areas.id_empresa AS id_empresa,
            Okrs.objetivo_okr AS objetivo,
            Okrs.id_empleado AS id_empleado_owner, 
            Okrs.tipo AS tipo_okrs,
            puntacana_admin.Estructura_Empresa.vicepresidencia AS id_vicepresidencia,
            puntacana_admin.Areas.nombre AS nombre_area,
            puntacana_admin.Empleados.unidad_organizativa as id_unidad_organizativa,
            puntacana_admin.Estructura_Empresa.unidad_organizativa as nombre_unidad_organizativa
        FROM
            Okrs_Areas 
        LEFT JOIN Okrs ON Okrs_Areas.id_okrs = Okrs.id
        LEFT JOIN puntacana_admin.Areas ON Okrs_Areas.id_area = puntacana_admin.Areas.id
        LEFT JOIN puntacana_admin.Empleados ON Okrs.id_empleado = puntacana_admin.Empleados.id
        LEFT JOIN puntacana_admin.Estructura_Empresa ON Okrs_Areas.id_area = puntacana_admin.Estructura_Empresa.area
        WHERE
            Okrs_Areas.id_empresa = '" . $id_empresa . "'
            AND Okrs_Areas.id_area = '" . $id_area . "'
            " . $filtros . "
        GROUP BY
            Okrs.id 
        ORDER BY Okrs.objetivo_okr ASC
        ";
        $query = mysqli_query($connect_okrs, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {

            //EMPLEADO
            $empleado = $this->Empleado($data['id_empleado_owner']);
            $data['empleado'] = $empleado;

            //RESULTADOS
            $resultados = $this->okrs_resultados($data["id_okrs"]);
            $data['resultados'] = $resultados;

            $porcentaje_avance = $this->PorcentajeAvanceOrks($data["id_okrs"]);
            $data['porcentaje_avance'] = $porcentaje_avance;

            $color = EscalaColor($porcentaje_avance);
            $data['color_avance'] = $color;


            if ($data['tipo_okrs'] == 1) {
                $data['tipo_okrs'] = "OKR Organizacional";
            } else {
                $data['tipo_okrs'] = "OKR Equipo";
            }


            array_push($array, $data);
        }
        return $array;
    }

    //FUNCION PARA EXTRAER LAS INICIATIVAS DE OKRS DEFINIDOS
    //FUNCION PARA EXTRAER LAS INICIATIVAS DE OKRS DEFINIDOS
    //FUNCION PARA EXTRAER LAS INICIATIVAS DE OKRS DEFINIDOS
    public function iniciativas($id_empresa, $okrs)
    {

        $ids_okrs = '';
        foreach ($okrs as $okr) {
            if ($ids_okrs == '') {
                $ids_okrs .= $okr["id_okrs"];
            } else {
                $ids_okrs .= "," . $okr["id_okrs"];
            }
        }

        global $connect_okrs;
        $array = array();

        $sentencia =
            "SELECT
                Okrs_Iniciativas.id, Okrs_Iniciativas.id_resultado, Okrs_Iniciativas.descripcion, Okrs_Iniciativas.responsables, Okrs_Iniciativas.mes,
                Okrs_Iniciativas.fecha_entrega, Okrs_Iniciativas.meta, Okrs_Iniciativas.avance,
                Okrs.id AS id_okrs, Okrs.objetivo_okr
            FROM
                Okrs_Iniciativas 
            LEFT JOIN Okrs ON Okrs.id = Okrs_Iniciativas.id_okrs
            WHERE Okrs.id_empresa = '$id_empresa' AND Okrs.anio = '" . $_SESSION["anio_fill"] . "' 
            AND Okrs_Iniciativas.id_okrs IN (" . $ids_okrs . ")
            GROUP BY Okrs_Iniciativas.id;
        ";

        //echo $sentencia;

        $query = mysqli_query($connect_okrs, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {

            $avance = $this->calcular_porcentaje_avance($data);
            $data["porcentaje_avance"] = $avance;

            $color = EscalaColor($avance);
            $data['bg_color'] = $color;

            array_push($array, $data);
        }

        return $array;
    }

    //FUNCION PARA EXTRAER LAS INICIATIVAS DE OKRS DEFINIDOS SOLO POR RESPONSABLE (MIS INICIATIVAS)
    //FUNCION PARA EXTRAER LAS INICIATIVAS DE OKRS DEFINIDOS SOLO POR RESPONSABLE (MIS INICIATIVAS)
    //FUNCION PARA EXTRAER LAS INICIATIVAS DE OKRS DEFINIDOS SOLO POR RESPONSABLE (MIS INICIATIVAS)
    public function iniciativas_responsable($id_empresa, $id_empleado, $okrs)
    {

        $ids_okrs = '';
        foreach ($okrs as $okr) {
            if ($ids_okrs == '') {
                $ids_okrs .= $okr["id_okrs"];
            } else {
                $ids_okrs .= "," . $okr["id_okrs"];
            }
        }

        //Okrs_Iniciativas.responsables
        global $connect_okrs;
        $array = array();

        $sentencia = "
        SELECT
            Okrs_Iniciativas.id,
            Okrs_Iniciativas.id_resultado,
            Okrs_Iniciativas.descripcion,
            Okrs_Iniciativas.responsables,
            Okrs_Iniciativas.mes,
            Okrs_Iniciativas.fecha_entrega,
            Okrs_Iniciativas.meta,
            Okrs_Iniciativas.avance,
            Okrs.id AS id_okrs,
            Okrs.objetivo_okr
        FROM
            Okrs_Iniciativas
        LEFT JOIN Okrs ON Okrs.id = Okrs_Iniciativas.id_okrs
        WHERE
            Okrs.id_empresa = '" . $id_empresa . "' AND
            Okrs.id IN (" . $ids_okrs . ") AND
            FIND_IN_SET('" . $id_empleado . "',Okrs_Iniciativas.responsables)
        GROUP BY
            Okrs_Iniciativas.id
        ";

        $query = mysqli_query($connect_okrs, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {

            $avance = $this->calcular_porcentaje_avance($data);
            $data["porcentaje_avance"] = $avance;

            $color = EscalaColor($avance);
            $data['bg_color'] = $color;

            array_push($array, $data);
        }

        return $array;
    }

    //FUNCION PARA EXTRAER PLANES DE ACCIÓN DE OKRS DEFINIDOS
    //FUNCION PARA EXTRAER PLANES DE ACCIÓN DE OKRS DEFINIDOS
    //FUNCION PARA EXTRAER PLANES DE ACCIÓN DE OKRS DEFINIDOS
    public function planes_accion($id_empresa, $okrs)
    {

        $ids_okrs = '';
        foreach ($okrs as $okr) {
            if ($ids_okrs == '') {
                $ids_okrs .= $okr["id_okrs"];
            } else {
                $ids_okrs .= "," . $okr["id_okrs"];
            }
        }

        global $connect_okrs;
        $array = array();

        $sentencia =
            "SELECT
            DISTINCT(OA.id) AS id,
            OA.id_empresa AS id_empresa,
            OA.id_okrs AS id_okrs,
            OA.id_resultado AS id_resultado,
            OA.id_iniciativa AS id_iniciativa,
            OI.descripcion AS descripcion_iniciativa,
            OA.id_empleado AS id_empleado,
            OA.id_asignado AS id_asignado,
            OA.ciclo AS ciclo,
            OA.descripcion AS descripcion,
            OA.prioridad AS prioridad,
            OA.meta AS meta,
            OA.progreso AS progreso,
            OA.estado_backlog AS estado_backlog,
            OA.fecha_inicia AS fecha_inicia,
            OA.fecha_entrega AS fecha_entrega,
            OA.checked AS checked,
            OA.aprobacion AS aprobacion
        FROM
            Okrs_Actividades OA
        LEFT JOIN Okrs_Iniciativas OI ON OI.id = OA.id_iniciativa
        LEFT JOIN Okrs O ON O.id = OI.id_okrs
        LEFT JOIN Okrs_Resultados ORE ON ORE.id = OI.id_resultado
        LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = OI.id_okrs
        WHERE
            O.id_empresa = '$id_empresa'
            AND O.anio = '" . $_SESSION["anio_fill"] . "' 
            AND OA.aprobacion = 1
            AND OA.id_okrs IN (" . $ids_okrs . ")
        ";

        $query = mysqli_query($connect_okrs, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {

            //PUBLICADO POR
            $publicado_por = $this->Empleado($data['id_empleado']);
            $data['publicado_por'] = $publicado_por;

            $data["avance"] = $data["progreso"];
            $avance = $this->calcular_porcentaje_avance($data);
            $data["porcentaje_avance"] = $avance;

            $color = EscalaColor($avance);
            $data['bg_color'] = $color;

            array_push($array, $data);
        }

        return $array;
    }

    public function planes_accion_responsable($id_empresa, $id_empleado, $okrs)
    {
        $ids_okrs = '';
        foreach ($okrs as $okr) {
            if ($ids_okrs == '') {
                $ids_okrs .= $okr["id_okrs"];
            } else {
                $ids_okrs .= "," . $okr["id_okrs"];
            }
        }

        global $connect_okrs;
        $array = array();

        $sentencia =
            "SELECT
            DISTINCT(OA.id) AS id,
            OA.id_empresa AS id_empresa,
            OA.id_okrs AS id_okrs,
            OA.id_resultado AS id_resultado,
            OA.id_iniciativa AS id_iniciativa,
            OI.descripcion AS descripcion_iniciativa,
            OA.id_empleado AS id_empleado,
            OA.id_asignado AS id_asignado,
            OA.ciclo AS ciclo,
            OA.descripcion AS descripcion,
            OA.prioridad AS prioridad,
            OA.meta AS meta,
            OA.progreso AS progreso,
            OA.estado_backlog AS estado_backlog,
            OA.fecha_inicia AS fecha_inicia,
            OA.fecha_entrega AS fecha_entrega,
            OA.checked AS checked,
            OA.aprobacion AS aprobacion
        FROM
            Okrs_Actividades OA
        LEFT JOIN Okrs_Iniciativas OI ON OI.id = OA.id_iniciativa
        LEFT JOIN Okrs O ON O.id = OI.id_okrs
        LEFT JOIN Okrs_Resultados ORE ON ORE.id = OI.id_resultado
        LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = OI.id_okrs
        WHERE
            O.id_empresa = '" . $id_empresa . "' 
            AND OA.aprobacion = 1 
            AND OA.id_okrs IN (" . $ids_okrs . ")
            AND CONCAT (',',OA.id_asignado,',') LIKE '%," . $id_empleado . ",%'
        ";

        //echo $sentencia;

        $query = mysqli_query($connect_okrs, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {

            //PUBLICADO POR
            $publicado_por = $this->Empleado($data['id_empleado']);
            $data['publicado_por'] = $publicado_por;

            $data["avance"] = $data["progreso"];
            $avance = $this->calcular_porcentaje_avance($data);
            $data["porcentaje_avance"] = $avance;

            $color = EscalaColor($avance);
            $data['bg_color'] = $color;

            array_push($array, $data);
        }

        return $array;
    }

    //FUNCION PARA OBTENER LOS OBJETIVOS ASOCIADOS SOLO POR RESPONSABLE (MIS OBJETIVOS)
    //FUNCION PARA OBTENER LOS OBJETIVOS ASOCIADOS SOLO POR RESPONSABLE (MIS OBJETIVOS)
    //FUNCION PARA OBTENER LOS OBJETIVOS ASOCIADOS SOLO POR RESPONSABLE (MIS OBJETIVOS)
    public function objetivos_asociados($id_empresa, $id_empleado, $area, $anio)
    {
        global $connect_okrs;
        $array = array();

        $filtros = "";

        if ($_SESSION["anio_fill"]) {
            $filtros .= " AND Okrs.anio = '" . $_SESSION["anio_fill"] . "' ";
        }
        if ($_SESSION["vicepresidencias_fill"]) {
            $filtros .= " AND goforagile_admin.Estructura_Empresa.vicepresidencia = '" . $_SESSION["vicepresidencias_fill"] . "' ";
        }
        if ($_SESSION["objetivos_fill"]) {
            $filtros .= " AND Okrs.objetivos_estrategicos = '" . $_SESSION["objetivos_fill"] . "' ";
        }
        if ($_SESSION["okrs_fill"]) {
            $filtros .= " AND Okrs.id = '" . $_SESSION["okrs_fill"] . "' ";
        }
        if ($_SESSION["tipo_okrs_fill"]) {
            $filtros .= " AND Okrs.tipo = '" . $_SESSION["tipo_okrs_fill"] . "' ";
        }
        if ($_SESSION["periodo_fill"]) {

            // Agregar comillas a cada valor
            $periodos = array_map(function ($p) {
                return "'" . $p . "'";
            }, $_SESSION["periodo_fill"]);

            $lista_periodos = implode(",", $periodos); // Convertir a string

            $filtros .= " AND Okrs_Resultados.periodo IN ($lista_periodos) ";
        }

        $sentencia = "
        SELECT
            Okrs_Equipos.id AS id, 
            Okrs_Equipos.id_okrs AS id_okrs,
            Okrs_Equipos.id_empresa AS id_empresa,
            Okrs_Equipos.id_empleado AS id_empleado,
            Okrs_Equipos.id_okrs AS id_okrs, 
            Okrs_Equipos.tipo AS tipo_role,
            Roles_Okrs.nombre_rol AS nombre_tu_rol,
            Okrs.objetivo_okr AS objetivo_okr,
            Okrs.fecha_inicia AS fecha_inicia,
            Okrs.fecha_termina AS fecha_termina,
            Okrs.tipo AS tipo,
            Okrs.periodo AS periodo,
            Okrs.objetivos_estrategicos AS objetivos_estrategicos,
            Okrs.anio AS anio,
            Okrs.id_empleado AS id_owner,
            Empleados.nombre AS nombre_empleado
        FROM
            Okrs_Equipos
        LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs 
        LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs_Equipos.id_okrs
        LEFT JOIN goforagile_admin.Empleados AS Empleados ON Empleados.id = Okrs_Equipos.id_empleado
        LEFT JOIN goforagile_admin.Estructura_Empresa ON Okrs_Areas.id_area = goforagile_admin.Estructura_Empresa.area
        LEFT JOIN Roles_Okrs ON Roles_Okrs.id_rol = Okrs_Equipos.tipo
        LEFT JOIN Okrs_Resultados ON Okrs.id = Okrs_Resultados.id_okrs
        WHERE
            Okrs_Equipos.id_empresa = $id_empresa AND
            Okrs_Equipos.id_empleado = '" . $id_empleado . "'  
            " . $filtros . " 
            GROUP BY Okrs.id
        ";
        $query = mysqli_query($connect_okrs, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {

            //OWNER
            $empleado_owner = $this->Empleado($data["id_owner"]);

            //RESULTADOS
            $resultados = $this->okrs_resultados($data["id_okrs"]);

            $porcentaje_avance = $this->PorcentajeAvanceOrks($data["id_okrs"]);
            //$data['porcentaje_avance'] = $porcentaje_avance;

            $color = EscalaColor($porcentaje_avance);

            $nodo = array(
                "id" => $data["id_okrs"],
                "id_okrs" => $data["id_okrs"],
                "objetivo" => $data["objetivo_okr"],
                "fecha_inicia" => $data["fecha_inicia"],
                "fecha_termina" => $data["fecha_termina"],
                "tipo" => $data["tipo"],
                "tipo_role" => $data["tipo_role"],
                "nombre_tu_rol" => $data["nombre_tu_rol"],
                "periodo" => $data["periodo"],
                "objetivos_estrategicos" => $data["objetivos_estrategicos"],
                "anio" => $data["anio"],
                "nombre_empleado" => $data["nombre_empleado"],
                "id_empleado" => $data["id_empleado"],
                "id_owner" => $data["id_owner"],
                "nombre_owner" => $data["nombre_owner"],
                "owner" => $empleado_owner,
                "resultados" => $resultados,
                "porcentaje_avance" => $porcentaje_avance,
                "color_avance" => $color

            );

            array_push($array, $nodo);
        }

        return $array;
    }

    public function objetivo_asociado($id_empresa, $id_empleado, $id_objetivo)
    {
        global $connect_okrs;

        $array = array();

        $sentencia = "
        SELECT
            Okrs_Equipos.id AS id, 
            Okrs_Equipos.id_okrs AS id_okrs,
            Okrs_Equipos.id_empresa AS id_empresa,
            Okrs_Equipos.id_empleado AS id_empleado,
            Okrs_Equipos.id_okrs AS id_okrs, 
            Okrs_Equipos.tipo AS tipo_role,
            Roles_Okrs.nombre_rol AS nombre_tu_rol,
            Okrs.objetivo_okr AS objetivo_okr,
            Okrs.fecha_inicia AS fecha_inicia,
            Okrs.fecha_termina AS fecha_termina,
            Okrs.tipo AS tipo,
            Okrs.periodo AS periodo,
            Okrs.objetivos_estrategicos AS objetivos_estrategicos,
            Okrs.anio AS anio,
            Okrs.id_empleado AS id_owner,
            Empleados.nombre AS nombre_empleado
        FROM
            Okrs_Equipos
        LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs 
        LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs_Equipos.id_okrs
        LEFT JOIN goforagile_admin.Empleados AS Empleados ON Empleados.id = Okrs_Equipos.id_empleado
        LEFT JOIN goforagile_admin.Estructura_Empresa ON Okrs_Areas.id_area = goforagile_admin.Estructura_Empresa.area
        LEFT JOIN Roles_Okrs ON Roles_Okrs.id_rol = Okrs_Equipos.tipo
        WHERE
            Okrs_Equipos.id_empresa = $id_empresa AND
            Okrs_Equipos.id_okrs = '" . $id_objetivo . "'
            GROUP BY Okrs.id
        ";

        $query = mysqli_query($connect_okrs, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {

            //OWNER
            $empleado_owner = $this->Empleado($data["id_owner"]);

            //RESULTADOS
            $resultados = $this->okrs_resultados($data["id_okrs"]);

            $porcentaje_avance = $this->PorcentajeAvanceOrks($data["id_okrs"]);
            //$data['porcentaje_avance'] = $porcentaje_avance;

            $color = EscalaColor($porcentaje_avance);

            $nodo = array(
                "id" => $data["id_okrs"],
                "id_okrs" => $data["id_okrs"],
                "objetivo" => $data["objetivo_okr"],
                "fecha_inicia" => $data["fecha_inicia"],
                "fecha_termina" => $data["fecha_termina"],
                "tipo" => $data["tipo"],
                "tipo_role" => $data["tipo_role"],
                "nombre_tu_rol" => $data["nombre_tu_rol"],
                "periodo" => $data["periodo"],
                "objetivos_estrategicos" => $data["objetivos_estrategicos"],
                "anio" => $data["anio"],
                "nombre_empleado" => $data["nombre_empleado"],
                "id_empleado" => $data["id_empleado"],
                "id_owner" => $data["id_owner"],
                "nombre_owner" => $data["nombre_owner"],
                "owner" => $empleado_owner,
                "resultados" => $resultados,
                "porcentaje_avance" => $porcentaje_avance,
                "color_avance" => $color

            );

            array_push($array, $nodo);
        }

        return $array;
    }

    public function mis_resultados_clave($id_empresa, $id_empleado, $area, $anio){

        global $connect_okrs;

        $filtros = "";

        if ($_SESSION["anio_fill"]) {
            $filtros .= " AND Okrs.anio = '" . $_SESSION["anio_fill"] . "' ";
        }
        if ($_SESSION["vicepresidencias_fill"]) {
            $filtros .= " AND goforagile_admin.Estructura_Empresa.vicepresidencia = '" . $_SESSION["vicepresidencias_fill"] . "' ";
        }
        if ($_SESSION["objetivos_fill"]) {
            $filtros .= " AND Okrs.objetivos_estrategicos = '" . $_SESSION["objetivos_fill"] . "' ";
        }
        if ($_SESSION["okrs_fill"]) {
            $filtros .= " AND Okrs.id = '" . $_SESSION["okrs_fill"] . "' ";
        }
        if ($_SESSION["tipo_okrs_fill"]) {
            $filtros .= " AND Okrs.tipo = '" . $_SESSION["tipo_okrs_fill"] . "' ";
        }
        if ($_SESSION["periodo_fill"]) {

            // Agregar comillas a cada valor
            $periodos = array_map(function ($p) {
                return "'" . $p . "'";
            }, $_SESSION["periodo_fill"]);
            
            $lista_periodos = implode(",", $periodos); // Convertir a string
            
            $filtros .= " AND Okrs_Resultados.periodo IN ($lista_periodos) ";
        }

        $array = array();

        $sentencia = "
        SELECT
            Okrs_Resultados.*,
            Okrs.objetivo_okr AS objetivo_okr,
            Okrs.fecha_inicia AS fecha_inicia,
            Okrs.fecha_termina AS fecha_termina,
            Okrs.tipo AS tipo,
            Okrs.periodo AS periodo,
            Okrs.objetivos_estrategicos AS objetivos_estrategicos,
            Okrs.anio AS anio,
            Okrs.id_empleado AS id_owner
        FROM
            Okrs_Resultados
        LEFT JOIN
            Okrs ON Okrs.id = Okrs_Resultados.id_okrs
        WHERE
            Okrs_Resultados.id_empresa = '$id_empresa'
            AND Okrs.anio = '$anio'
            AND FIND_IN_SET('$id_empleado', Okrs_Resultados.responsables)
            " . $filtros . "
        GROUP BY
            Okrs.id 
            ORDER BY Okrs.objetivo_okr ASC
        ";


        $query = mysqli_query($connect_okrs, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {

            //OWNER
            $empleado_owner = $this->Empleado($data["id_owner"]);

            //RESULTADOS
            $resultados = $this->okrs_resultados_colaborador($data["id_okrs"], $id_empleado );

            $porcentaje_avance = $this->PorcentajeAvanceOrksColaborador($data["id_okrs"], $id_empleado);
            $data['porcentaje_avance'] = $porcentaje_avance;

            $color = EscalaColor($porcentaje_avance);

            /* $nodo = array(
                "id" => $data["id_okrs"],
                "id_okrs" => $data["id_okrs"],
                "objetivo" => $data["objetivo_okr"],
                "fecha_inicia" => $data["fecha_inicia"],
                "fecha_termina" => $data["fecha_termina"],
                "tipo" => $data["tipo"],
                "tipo_role" => $data["tipo_role"],
                "nombre_tu_rol" => $data["nombre_tu_rol"],
                "periodo" => $data["periodo"],
                "objetivos_estrategicos" => $data["objetivos_estrategicos"],
                "anio" => $data["anio"],
                "nombre_empleado" => $data["nombre_empleado"],
                "id_empleado" => $data["id_empleado"],
                "id_owner" => $data["id_owner"],
                "nombre_owner" => $data["nombre_owner"],
                "owner" => $empleado_owner,
                "resultados" => $resultados,
                "porcentaje_avance" => $porcentaje_avance,
                "color_avance" => $color

            ); */

            $nodo = array(
                "id" => $data["id"],
                "id_okrs" => $data["id_okrs"],
                "objetivo" => $data["objetivo_okr"],
                "fecha_inicia" => $data["fecha_inicia"],

                "fecha_termina" => $data["fecha_termina"],
                "tipo" => $data["tipo"],
                "tipo_role" => $data["tipo_role"],
                "nombre_tu_rol" => $data["nombre_tu_rol"],
                "periodo" => $data["periodo"],
                "objetivos_estrategicos" => $data["objetivos_estrategicos"],
                "anio" => $data["anio"],
                "nombre_empleado" => $data["nombre_empleado"],
                "id_empleado" => $data["id_empleado"],
                "id_owner" => $data["id_owner"],
                "nombre_owner" => $data["nombre_owner"],
                "owner" => $empleado_owner,
                "resultados" => $resultados,
                "porcentaje_avance" => $porcentaje_avance,
                "color_avance" => $color
            );

            array_push($array, $nodo);
        }

        return $array;
    }

    //RESULTADOS POR OKRS DETALLE
    //RESULTADOS POR OKRS DETALLE
    //RESULTADOS POR OKRS DETALLE
    public function okrs_resultados($id_okrs)
    {
        global $connect_okrs;

        $filtros = "";
        $lista_periodos = "";

        if ($_SESSION["periodo_fill"]) {
            $lista_periodos .= " AND ( ";
            foreach ($_SESSION["periodo_fill"] as $periodo) {
                if ($lista_periodos == " AND ( ") {
                    $lista_periodos .= " Okrs_Resultados.periodo = '" . $periodo . "' ";
                } else {
                    $lista_periodos .= " OR Okrs_Resultados.periodo = '" . $periodo . "' ";
                }
            }
            $lista_periodos .= " ) ";
            $filtros .= $lista_periodos;
        }

        $array = array();

        $sentencia = "
        SELECT
            *
        FROM
            Okrs_Resultados
        WHERE
            id_okrs = '" . $id_okrs . "' 
            " . $filtros . " 
        ORDER BY
            periodo ASC;
        ";
        $query = mysqli_query($connect_okrs, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {

            $avance = $this->calcular_porcentaje_avance($data);
            $data["porcentaje_avance"] = $avance;

            $color_r = EscalaColor($avance);
            $data['color'] = $color_r;

            array_push($array, $data);
        }
        return $array;
    }

    public function okrs_resultados_colaborador($id_okrs, $id_empleado){
        global $connect_okrs;

        $filtros = "";
        $lista_periodos = "";

        if ($_SESSION["periodo_fill"]) {
            $lista_periodos .= " AND ( ";
            foreach ($_SESSION["periodo_fill"] as $periodo) {
                if ($lista_periodos == " AND ( ") {
                    $lista_periodos .= " Okrs_Resultados.periodo = '" . $periodo . "' ";
                } else {
                    $lista_periodos .= " OR Okrs_Resultados.periodo = '" . $periodo . "' ";
                }
            }
            $lista_periodos .= " ) ";
            $filtros .= $lista_periodos;
        }

        $array = array();

        $sentencia = "
        SELECT
            *
        FROM
            Okrs_Resultados
        WHERE
            id_okrs = '" . $id_okrs . "'  
            AND CONCAT(',', Okrs_Resultados.responsables, ',' ) LIKE '%,".$id_empleado.",%'
            " . $filtros . " 
        ORDER BY
            periodo ASC;
        ";
        $query = mysqli_query($connect_okrs, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {

            $avance = $this->calcular_porcentaje_avance($data);
            $data["porcentaje_avance"] = $avance;

            $color_r = EscalaColor($avance);
            $data['color'] = $color_r;

            array_push($array, $data);
        }
        return $array;
    }





































    //OBTENER OKR
    public function obtener_okrs($id_okrs)
    {
        global $connect_okrs;

        $sentencia = "
        SELECT
            Okrs.*,
            Okrs_Areas.id_area AS id_area,
            goforagile_admin.Estructura_Empresa.vicepresidencia AS id_vicepresidencia,
            OE.objetivo AS nombre_objetivo_estrategico
        FROM
            Okrs
        LEFT JOIN
            Okrs_Areas ON Okrs_Areas.id_okrs = Okrs.id
        LEFT JOIN
            goforagile_admin.Estructura_Empresa ON Okrs_Areas.id_area = goforagile_admin.Estructura_Empresa.area
        LEFT JOIN
            Objetivos_estrategicos OE ON OE.id = Okrs.objetivos_estrategicos
        WHERE
            Okrs.id = '" . $id_okrs . "'
        ";

        $query = mysqli_query($connect_okrs, $sentencia);
        $data = mysqli_num_rows($query) > 0 ? mysqli_fetch_assoc($query) : array();

        $sentencia = "
        SELECT
            *
        FROM
            Okrs_Vicepresidencia
        WHERE
            id_okrs = '" . $id_okrs . "'
        ";

        $queryVicepresidencia = mysqli_query($connect_okrs, $sentencia);
        $dataVicepresidencia = mysqli_num_rows($queryVicepresidencia) > 0 ? mysqli_fetch_assoc($queryVicepresidencia) : array();

        $data["id_vicepresidencia"] = $dataVicepresidencia["id_vicepresidencia"];

        $porcentaje_avance = $this->PorcentajeAvanceOrks($id_okrs);
        $data["avance"] = $porcentaje_avance;
        $data["avance_color"] = EscalaColor($porcentaje_avance);



        return $data;
    }

    public function Obtener_Objetivos_Estrategicos($id_empresa, $anio)
    {
        global $connect_okrs;
        $array = array();

        $sentencia = "
        SELECT
            id,
            objetivo 
        FROM
            Objetivos_estrategicos
        WHERE
            id_empresa = '" . $id_empresa . "'
            AND anio = '" . $anio . "'
        ";
        $query = mysqli_query($connect_okrs, $sentencia);
        if (mysqli_num_rows($query) > 0) {
            while ($data = mysqli_fetch_assoc($query)) {
                array_push($array, $data);
            }
        }

        return $array;
    }

    //OKRS EMPRESA
    public function okrs_empresa($request, $id_empresa, $anio)
    {
        global $connect_okrs;
        $array = array();

        $sentencia = "
            SELECT 
                Okrs.objetivo_okr AS objetivo,
                Okrs.id AS id_okrs
            FROM
                Okrs 
            WHERE
                anio = '" . $anio . "'
        ";
        $query = mysqli_query($connect_okrs, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {

            $porcentaje = (45 * 100) / 120;
            $porcentaje = $this->PorcentajeAvance(120, 45);

            $nodo = array(
                "objetivo" => $data["objetivo"],
                "porcentaje" => $porcentaje
            );
            array_push($array, $nodo);
        }

        return $array;
    }

    public function listado_okrs_areas_asignadas_organizacional($id_empresa, $id_vicepresidencia, $id_okrs)
    {
        global $connect_okrs;
        $array = array();

        $sentencia =
            "SELECT
            Okrs_Areas.*,
            goforagile_admin.Vicepresidencia.nombre as nombre_vicepresidencia
        FROM
            Okrs_Areas
        LEFT JOIN
            goforagile_admin.Estructura_Empresa ON Okrs_Areas.id_area = goforagile_admin.Estructura_Empresa.area
        LEFT JOIN
            goforagile_admin.Vicepresidencia ON goforagile_admin.Vicepresidencia.id = Okrs_Areas.id_vicepresidencia
        WHERE
            Okrs_Areas.id_empresa = '$id_empresa'
            AND Okrs_Areas.id_okrs = '$id_okrs'
        GROUP BY
            Okrs_Areas.id
        ";
        $query = mysqli_query($connect_okrs, $sentencia);

        if (mysqli_num_rows($query) > 0) {
            while ($data = mysqli_fetch_assoc($query)) {
                array_push($array, $data);
            }
        }

        return $array;
    }

    public function listado_okrs_area_asignados($id_empresa, $id_okrs)
    {
        global $connect_okrs;
        $array = array();

        $sentencia =
            "SELECT
            Okrs_Areas.*,
            goforagile_admin.Areas.nombre AS nombre_area,
            goforagile_admin.Vicepresidencia.nombre as nombre_vicepresidencia
        FROM
            Okrs_Areas
        LEFT JOIN
            goforagile_admin.Areas ON Okrs_Areas.id_area = goforagile_admin.Areas.id
        LEFT JOIN
            goforagile_admin.Estructura_Empresa ON Okrs_Areas.id_area = goforagile_admin.Estructura_Empresa.area
        LEFT JOIN
            goforagile_admin.Vicepresidencia ON goforagile_admin.Vicepresidencia.id = goforagile_admin.Estructura_Empresa.vicepresidencia
        WHERE
            Okrs_Areas.id_empresa = '$id_empresa'
            AND Okrs_Areas.id_okrs = '$id_okrs'
        GROUP BY
            Okrs_Areas.id
        ";
        $query = mysqli_query($connect_okrs, $sentencia);

        if (mysqli_num_rows($query) > 0) {
            while ($data = mysqli_fetch_assoc($query)) {
                array_push($array, $data);
            }
        }

        return $array;
    }

    public function listado_resultados_claves_asignados($id_empresa, $id_empleado, $id_okrs)
    {
        global $connect_okrs;
        $array = array();

        $sentencia =
            "SELECT
            *
        FROM
            Okrs_Resultados
        WHERE
            id_empresa = '$id_empresa'
            AND id_okrs = '$id_okrs'
            AND estado = 1 
        ";
        $query = mysqli_query($connect_okrs, $sentencia);

        if (mysqli_num_rows($query) > 0) {
            while ($data = mysqli_fetch_assoc($query)) {

                //EMPLEADO
                $empleado = $this->Empleado($data['id_empleado']);
                $data['empleado_owner'] = $empleado;

                array_push($array, $data);
            }
        }

        return $array;
    }



    public function obtener_okr_area($id_okr_area)
    {
        global $connect_okrs;
        $sentencia = "SELECT * FROM Okrs_Areas WHERE id = '$id_okr_area' LIMIT 1 ";
        $query = mysqli_query($connect_okrs, $sentencia);
        $data = mysqli_num_rows($query) > 0 ? mysqli_fetch_assoc($query) : array();
        return $data;
    }

    //LISTA DE OBJETIVOS DEL AREA
    public function okrs_area_objetivos($id_empleado, $id_empresa, $id_area, $anio)
    {
        global $connect_okrs;
        $array = array();

        //EMPLEADO OWNER
        $empleado_owner = $this->Empleado($id_empleado);

        $sentencia = "
        SELECT
            Okrs.*,
            Okrs.objetivo_okr AS objetivo
        FROM
            Okrs_Equipos 
        LEFT JOIN
            Okrs ON Okrs.id = Okrs_Equipos.id_okrs
        LEFT JOIN
            Okrs_Areas ON Okrs_Areas.id_okrs = Okrs_Equipos.id_okrs  
        WHERE
            Okrs_Equipos.id_empresa = '" . $id_empresa . "'
            AND Okrs_Areas.id_area = '" . $id_area . "'
            AND Okrs.anio = '" . $anio . "'
        GROUP BY
            Okrs.id
        ORDER BY
            Okrs.objetivo_okr ASC
        ";

        $query = mysqli_query($connect_okrs, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {

            //PROGRESO DESEMPENO
            $data['progreso_desempeno'] = 0;

            //EMPLEADO
            $data['empleado_equipo'] = $this->Empleado($data['id_empleado']);

            //RESULTADOS
            $resultados = $this->okrs_resultados($data["id_okrs"]);
            $data['resultados'] = $resultados;

            array_push($array, $data);
        }

        return $array;
    }

    public function okrs_area_iniciativas($id_empleado, $id_empresa, $id_area, $anio)
    {

        global $connect_okrs;
        $array = array();

        $sentencia =
            "SELECT
                Okrs_Iniciativas.id, Okrs_Iniciativas.id_resultado, Okrs_Iniciativas.descripcion, Okrs_Iniciativas.responsables, Okrs_Iniciativas.mes,
                Okrs_Iniciativas.fecha_entrega, Okrs_Iniciativas.meta, Okrs_Iniciativas.avance,
                Okrs.id AS id_okrs, Okrs.objetivo_okr
            FROM
                Okrs_Iniciativas 
            LEFT JOIN Okrs ON Okrs.id = Okrs_Iniciativas.id_okrs
            WHERE Okrs.id_empresa = '$id_empresa' AND Okrs.anio = '$anio' 
            
            GROUP BY Okrs_Iniciativas.id;
        ";

        //AND FIND_IN_SET('$id_empleado', Okrs_Iniciativas.responsables)

        $query = mysqli_query($connect_okrs, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {

            $avance = $this->calcular_porcentaje_avance($data);
            $data["porcentaje_avance"] = $avance;

            $color = EscalaColor($avance);
            $data['bg_color'] = $color;

            array_push($array, $data);
        }

        return $array;
    }

    public function iniciativa($id_iniciativa)
    {

        global $connect_okrs;
        $array = array();

        $sentencia =
            "SELECT
                Okrs_Iniciativas.id, Okrs_Iniciativas.id_resultado, Okrs_Iniciativas.descripcion, Okrs_Iniciativas.responsables, Okrs_Iniciativas.mes,
                Okrs_Iniciativas.fecha_entrega, Okrs_Iniciativas.meta, Okrs_Iniciativas.avance,
                Okrs.id AS id_okrs, Okrs.objetivo_okr
            FROM
                Okrs_Iniciativas 
            LEFT JOIN
                Okrs ON Okrs.id = Okrs_Iniciativas.id_okrs
            WHERE
                Okrs_Iniciativas.id = '$id_iniciativa'
            GROUP BY Okrs_Iniciativas.id;
        ";

        $query = mysqli_query($connect_okrs, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {

            $avance = $this->calcular_porcentaje_avance($data);
            $data["porcentaje_avance"] = $avance;

            $color = EscalaColor($avance);
            $data['bg_color'] = $color;

            array_push($array, $data);
        }

        return $array;
    }

    public function okrs_area_planes_accion($id_empresa, $id_empleado, $anio)
    {
        global $connect_okrs;
        $array = array();

        $sentencia =
            "SELECT
            DISTINCT(OA.id) AS id,
            OA.id_empresa AS id_empresa,
            OA.id_okrs AS id_okrs,
            OA.id_resultado AS id_resultado,
            OA.id_iniciativa AS id_iniciativa,
            OI.descripcion AS descripcion_iniciativa,
            OA.id_empleado AS id_empleado,
            OA.id_asignado AS id_asignado,
            OA.ciclo AS ciclo,
            OA.descripcion AS descripcion,
            OA.prioridad AS prioridad,
            OA.meta AS meta,
            OA.progreso AS progreso,
            OA.estado_backlog AS estado_backlog,
            OA.fecha_inicia AS fecha_inicia,
            OA.fecha_entrega AS fecha_entrega,
            OA.checked AS checked,
            OA.aprobacion AS aprobacion
        FROM
            Okrs_Actividades OA
        LEFT JOIN Okrs_Iniciativas OI ON OI.id = OA.id_iniciativa
        LEFT JOIN Okrs O ON O.id = OI.id_okrs
        LEFT JOIN Okrs_Resultados ORE ON ORE.id = OI.id_resultado
        LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = OI.id_okrs
        WHERE
            O.id_empresa = '$id_empresa'
            AND O.anio = '$anio' 
            AND OA.aprobacion = 1
            AND CONCAT (',',OA.id_asignado,',') LIKE '%," . $id_empleado . ",%'
        ";

        $query = mysqli_query($connect_okrs, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {

            //PRIORIDAD
            $prioridades = [
                1 => '<span class="btn btn-sm btn-info bg-white">Bajo</span>',
                2 => '<span class="btn btn-sm btn-success bg-success">Medio</span>',
                3 => '<span class="btn btn-sm btn-warning">Alto</span>',
                4 => '<span class="btn btn-sm btn-danger">Urgente</span>'
            ];
            $data["prioridad_txt"] = $prioridades[$data["prioridad"]] ?? "Sin definir";

            //BACKLOG
            $backlogs = [
                1 => '<span class="btn btn-sm btn-info bg-white">Planificado</span>',
                2 => '<span class="btn btn-sm btn-success">En progreso</span>',
                3 => '<span class="btn btn-sm btn-warning">En revisión</span>',
                4 => '<span class="btn btn-sm btn-success bg-success">Completado</span>'
            ];
            $data["estado_backlog_txt"] = $backlogs[$data["estado_backlog"]] ?? "Sin definir";

            //PUBLICADO POR
            $publicado_por = $this->Empleado($data['id_empleado']);
            $data['publicado_por'] = $publicado_por;

            $data["avance"] = $data["progreso"];
            $avance = $this->calcular_porcentaje_avance($data);
            $data["porcentaje_avance"] = $avance;

            $color = EscalaColor($avance);
            $data['bg_color'] = $color;

            array_push($array, $data);
        }

        return $array;
    }

    public function obtener_resultado_clave($id_resultado)
    {
        global $connect_okrs;

        $sentencia = "SELECT * FROM Okrs_Resultados WHERE id = '$id_resultado' LIMIT 1 ";
        $query = mysqli_query($connect_okrs, $sentencia);
        $data = mysqli_num_rows($query) > 0 ? mysqli_fetch_assoc($query) : array();

        return $data;
    }


    public function PorcentajeAvance($meta, $seguimiento)
    {
        $porcentaje = 0;
        if ($seguimiento > 0) {
            $porcentaje = ($seguimiento * 100) / $meta;
        }
        return $porcentaje;
    }

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
            Empleados.id = '" . $id . "' 
            ORDER BY goforagile_admin.Estructura_Empresa.id DESC
        ";
        $query = mysqli_query(
            $connect_admin,
            $sentencia
        );
        $data = mysqli_num_rows($query) > 0 ? mysqli_fetch_assoc($query) : array();
        return $data;
    }

    public function okrs_iniciativas($empresa, $id_empleado, $anio)
    {

        global $connect_okrs;
        $array = array();

        $sentencia = "
        SELECT
            DISTINCT(OI.id) AS id,
            OI.id_okrs,
            OI.id_resultado,
            OI.id_empleado,
            OI.responsables,
            OI.descripcion,
            OI.mes,
            OI.fecha_entrega,
            OI.meta,
            OI.avance,
            OI.tendencia,
            ORE.descripcion AS descripcion_kr
        FROM
            Okrs_Iniciativas OI
        LEFT JOIN
            Okrs O ON O.id = OI.id_okrs
        LEFT JOIN
            Okrs_Resultados ORE ON ORE.id = OI.id_resultado
        LEFT JOIN
            Okrs_Areas ON Okrs_Areas.id_okrs = OI.id_okrs
        WHERE
            O.id_empresa = '" . $empresa . "'
            AND O.anio = '" . $anio . "'
            AND CONCAT(',', OI.responsables, ',') LIKE '%," . $id_empleado . ",%'
        ORDER BY
            OI.fecha_entrega ASC 
        ";
        $query = mysqli_query($connect_okrs, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {
            $data["seguimiento"] = 0;
            $data["porcentaje"] = 0;
            $data["planes_accion"] = $this->okrs_planes_accion($data["id_okrs"]);
            array_push($array, $data);
        }

        $nodo = array(
            "avance_general" => "0",
            "avance_total_iniciativas" => "0",
            "empleado_owner" => $id_empleado,
            "cantidad_iniciativas" => count($array),
            "iniciativas" => $array
        );

        return $nodo;
    }

    public function okrs_iniciativas_resultados($id_okrs, $id_resultado)
    {
        global $connect_okrs;
        $array = array();

        $sentencia =
            "SELECT
            *
        FROM
            Okrs_Iniciativas
        WHERE
            id_okrs = '$id_okrs'
            AND id_resultado = '$id_resultado' 
        ";
        $query = mysqli_query($connect_okrs, $sentencia);

        if (mysqli_num_rows($query) > 0) {
            while ($data = mysqli_fetch_assoc($query)) {

                $avance = $this->calcular_porcentaje_avance($data);
                $data["porcentaje_avance"] = $avance;

                $color_i = EscalaColor($avance);
                $data['color'] = $color_i;

                array_push($array, $data);
            }
        }

        return $array;
    }

    public function okrs_obtener_iniciativa($id_iniciativa)
    {
        global $connect_okrs;
        $array = array();

        $sentencia =
            "SELECT
            *
        FROM
            Okrs_Iniciativas
        WHERE
            id = '$id_iniciativa'
        LIMIT 1
        ";
        $query = mysqli_query($connect_okrs, $sentencia);

        $data = mysqli_num_rows($query) > 0 ? mysqli_fetch_assoc($query) : array();

        return $data;
    }

    public function okrs_planes_accion($id_okrs)
    {
        global $connect_okrs;
        $array = array();

        $sentencia = "
        SELECT
            DISTINCT(OA.id) AS id,
            OA.id_empresa AS id_empresa,
            OA.id_okrs AS id_okrs,
            OA.id_resultado AS id_resultado,
            OA.id_iniciativa AS id_iniciativa,
            OA.id_empleado AS id_empleado,
            OA.id_asignado AS id_asignado,
            OA.ciclo AS ciclo,
            OA.descripcion AS descripcion,
            OA.prioridad AS prioridad,
            OA.meta AS meta,
            OA.progreso AS progreso,
            OA.estado_backlog AS estado_backlog,
            OA.fecha_inicia AS fecha_inicia,
            OA.fecha_entrega AS fecha_entrega,
            OA.checked AS checked,
            OA.aprobacion AS aprobacion
        FROM
            Okrs_Actividades OA
        LEFT JOIN
            Okrs_Iniciativas OI ON OI.id = OA.id_iniciativa
        LEFT JOIN
            Okrs O ON O.id = OI.id_okrs
        LEFT JOIN
            Okrs_Resultados ORE ON ORE.id = OI.id_resultado
        LEFT JOIN
            Okrs_Areas ON Okrs_Areas.id_okrs = OI.id_okrs
        WHERE
            O.id_empresa = 1
            AND O.anio = '2026'
            AND CONCAT(',', OA.id_asignado, ',') LIKE '%,374,%'
        ";
        $query = mysqli_query($connect_okrs, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {
            array_push($array, $data);
        }

        return $array;
    }

    public function okrs_obtener_plan_accion($id_plan_accion)
    {
        global $connect_okrs;

        $sentencia = "SELECT * FROM Okrs_Actividades WHERE id = '$id_plan_accion' LIMIT 1 ";
        $query = mysqli_query($connect_okrs, $sentencia);
        $data = mysqli_num_rows($query) > 0 ? mysqli_fetch_assoc($query) : array();

        return $data;
    }

    public function okrs_obtener_plan_accion_iniciativa($id_iniciativa)
    {
        global $connect_okrs;
        $array = array();

        $sentencia =
            "SELECT
            Okrs_Actividades.*,
            Okrs_Iniciativas.descripcion as descripcion_iniciativa
        FROM
            Okrs_Actividades
        LEFT JOIN
            Okrs_Iniciativas ON Okrs_Iniciativas.id = Okrs_Actividades.id_iniciativa
        WHERE
           Okrs_Actividades.id_iniciativa = '$id_iniciativa' ";

        $query = mysqli_query($connect_okrs, $sentencia);

        while ($data = mysqli_fetch_assoc($query)) {

            //PRIORIDAD
            $prioridades = [
                1 => '<span class="btn btn-sm btn-info bg-white">Bajo</span>',
                2 => '<span class="btn btn-sm btn-success bg-success">Medio</span>',
                3 => '<span class="btn btn-sm btn-warning">Alto</span>',
                4 => '<span class="btn btn-sm btn-danger">Urgente</span>'
            ];
            $data["prioridad_txt"] = $prioridades[$data["prioridad"]] ?? "Sin definir";

            //BACKLOG
            $backlogs = [
                1 => '<span class="btn btn-sm btn-info bg-white">Planificado</span>',
                2 => '<span class="btn btn-sm btn-success">En progreso</span>',
                3 => '<span class="btn btn-sm btn-warning">En revisión</span>',
                4 => '<span class="btn btn-sm btn-success bg-success">Completado</span>'
            ];
            $data["estado_backlog_txt"] = $backlogs[$data["estado_backlog"]] ?? "Sin definir";

            $data["publicado_por"] = $this->Empleado($data['id_empleado']);

            $data["avance"] = $data["progreso"];
            $avance = $this->calcular_porcentaje_avance($data);
            $data["porcentaje_avance"] = $avance;

            $color = EscalaColor($avance);
            $data['bg_color'] = $color;

            array_push($array, $data);
        }
        return $array;
    }

    public function reporte_okrs_all($id_empresa, $anio)
    {
        global $connect_okrs;
        $array = array();

        $filtros = "";
        
        if ($_SESSION["vicepresidencias_fill"] > 0) {
            $filtros .= " AND Okrs_Vicepresidencia.id_vicepresidencia = '" . $_SESSION["vicepresidencias_fill"] . "' ";
        }
        if ($_SESSION["objetivos_fill"] > 0) {
            $filtros .= " AND Okrs.objetivos_estrategicos = '" . $_SESSION["objetivos_fill"] . "' ";
        }
        if ($_SESSION["okrs_fill"] > 0) {
            $filtros .= " AND Okrs.id = '" . $_SESSION["okrs_fill"] . "' ";
        }
        if ($_SESSION["tipo_okrs_fill"] > 0) {
            $filtros .= " AND Okrs.tipo = '" . $_SESSION["tipo_okrs_fill"] . "' ";
        }
        if ($_SESSION["responsables_fill"] > 0) {
            $filtros .= " AND Okrs_Equipos.id_empleado = '" . $_SESSION["responsables_fill"] . "' ";
        }

        

        $sentencia = "
		SELECT
            Okrs_Equipos.id AS id,
            Okrs_Equipos.id_empresa AS id_empresa,
		    Okrs_Equipos.id_empleado AS id_empleado,
            Okrs_Equipos.id_okrs AS id_okrs,
            Okrs_Equipos.tipo AS tipo_role,
		    Okrs.objetivo_okr AS objetivo_okr, 
            
            Okrs.fecha_inicia AS fecha_inicia,
		    Okrs.fecha_termina AS fecha_termina,
            Okrs.tipo AS tipo,
            Okrs.periodo AS periodo,
		    Okrs.objetivos_estrategicos AS objetivos_estrategicos,
            Okrs.anio AS anio,
            Okrs.id_empleado AS id_owner,
            Empleados.nombre AS nombre_empleado,
            EO.nombre AS nombre_owner,
            Okrs_Areas.id AS id_area
		FROM
            Okrs_Equipos
		LEFT JOIN
            Okrs ON Okrs.id = Okrs_Equipos.id_okrs
		LEFT JOIN
            Okrs_Areas ON Okrs_Areas.id_okrs = Okrs_Equipos.id_okrs
		LEFT
            JOIN Okrs_Vicepresidencia ON Okrs_Vicepresidencia.id_okrs = Okrs.id
        LEFT JOIN
            goforagile_admin.Empleados AS Empleados ON Empleados.id = Okrs_Equipos.id_empleado
        LEFT JOIN
            goforagile_admin.Empleados AS EO ON EO.id = Okrs.id_empleado
		WHERE
            Okrs_Equipos.id_empresa = '" . $id_empresa . "' AND Okrs.anio = '" . $anio . "' 
            ".$filtros."
		GROUP BY
            Okrs.id
		ORDER BY
            Okrs.objetivo_okr ASC 
		";
    
        $query = mysqli_query($connect_okrs, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {
            if (!empty($data["id_okrs"])) {

                //Empleado Owner
                $empleado_owner = $this->Empleado($data['id_owner']);
                $data['owner'] = $empleado_owner;

                $resultados = $this->okrs_resultados($data["id_okrs"]);
                $data['resultados'] = $resultados;

                $avance = $this->PorcentajeAvanceOrks($data["id_okrs"]);
                $data["porcentaje_avance"] = $avance;

                $color = EscalaColor($avance);
                $data['bg_color'] = $color;

                array_push($array, $data);
            }
        }

        return $array;
    }

    //TODOS LOS OKRS
    public function reporte_okrs_all_paginado($id_empresa, $anio, $limite, $offset)
    {

        $filtros = "";
        if ($_SESSION["anio_fill"]) {
            $filtros .= " AND Okrs.anio = '" . $_SESSION["anio_fill"] . "' ";
        }
        if ($_SESSION["vicepresidencias_fill"] > 0) {
            $filtros .= " AND goforagile_admin.Estructura_Empresa.vicepresidencia = '" . $_SESSION["vicepresidencias_fill"] . "' ";
        }
        if ($_SESSION["objetivos_fill"] > 0) {
            $filtros .= " AND Okrs.objetivos_estrategicos = '" . $_SESSION["objetivos_fill"] . "' ";
        }
        if ($_SESSION["okrs_fill"] > 0) {
            $filtros .= " AND Okrs.id = '" . $_SESSION["okrs_fill"] . "' ";
        }
        if ($_SESSION["tipo_okrs_fill"] > 0) {
            $filtros .= " AND Okrs.tipo = '" . $_SESSION["tipo_okrs_fill"] . "' ";
        }
        if ($_SESSION["responsables_fill"] > 0) {
            $filtros .= " AND Okrs_Equipos.id_empleado = '" . $_SESSION["responsables_fill"] . "' ";
        }

        /*

        
        
        
        
        
        if ($_SESSION["periodo_fill"]) {

            // Agregar comillas a cada valor
            $periodos = array_map(function ($p) {
                return "'" . $p . "'";
            }, $_SESSION["periodo_fill"]);

            $lista_periodos = implode(",", $periodos); // Convertir a string

            $filtros .= " AND Okrs_Resultados.periodo IN ($lista_periodos) ";
        }

        */
        global $connect_okrs;
        $array = array();

        $sentencia = "
		SELECT
            Okrs.id AS id_okrs,
            Okrs_Equipos.id AS id,
            Okrs_Equipos.id_empresa AS id_empresa,
		    Okrs_Equipos.id_empleado AS id_empleado,
            Okrs_Equipos.id_okrs AS id_okrs,
            Okrs_Equipos.tipo AS tipo_role,
		    Okrs.objetivo_okr AS objetivo_okr, 
            Okrs.objetivo_okr AS objetivo, 
            Okrs.fecha_inicia AS fecha_inicia,
		    Okrs.fecha_termina AS fecha_termina,
            Okrs.tipo AS tipo,
            Okrs.periodo AS periodo,
		    Okrs.objetivos_estrategicos AS objetivos_estrategicos,
            Okrs.anio AS anio,
            Okrs.id_empleado AS id_owner,
            Empleados.nombre AS nombre_empleado,
            EO.nombre AS nombre_owner,
            Okrs_Areas.id AS id_area
		FROM
            Okrs_Equipos
		LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Vicepresidencia ON Okrs_Vicepresidencia.id_okrs = Okrs.id
        LEFT JOIN goforagile_admin.Empleados AS Empleados ON Empleados.id = Okrs_Equipos.id_empleado
        LEFT JOIN goforagile_admin.Empleados AS EO ON EO.id = Okrs.id_empleado 
        LEFT JOIN goforagile_admin.Estructura_Empresa ON Okrs_Areas.id_area = goforagile_admin.Estructura_Empresa.area
        LEFT JOIN Okrs_Resultados ON Okrs.id = Okrs_Resultados.id_okrs
		WHERE
            Okrs_Equipos.id_empresa = '" . $id_empresa . "' 
            " . $filtros . "
		GROUP BY
            Okrs.id
		ORDER BY
            Okrs.objetivo_okr ASC
        LIMIT $limite OFFSET $offset
		";
        $query = mysqli_query($connect_okrs, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {
            if (!empty($data["id_okrs"])) {

                if ($data["tipo"] == 1) {
                    $data['tipo_okrs'] = "OKR Organizacional";
                }
                if ($data["tipo"] == 2) {
                    $data['tipo_okrs'] = "OKR Equipo";
                }

                //Empleado Owner
                $empleado_owner = $this->Empleado($data['id_owner']);
                $data['owner'] = $empleado_owner;

                $resultados = $this->okrs_resultados($data["id_okrs"]);
                $data['resultados'] = $resultados;

                $avance = $this->PorcentajeAvanceOrks($data["id_okrs"]);
                $data["porcentaje_avance"] = $avance;

                $color = EscalaColor($avance);
                $data['bg_color'] = $color;

                array_push($array, $data);
            }
        }

        return $array;
    }

    public function obtener_integrantes_asociados($id_empresa, $id_okrs)
    {

        global $connect_okrs;
        $array = array();

        $id_empresa = isset($id_empresa) ? $id_empresa : null;
        $id_okrs = isset($id_okrs) ? $id_okrs : null;

        $sentencia =
            "SELECT
            *
        FROM
            Okrs_Equipos
        WHERE
            id_empresa = '$id_empresa'
            AND id_okrs = '$id_okrs'
        GROUP BY
            id_empleado
        ";

        if ($id_empresa && $id_okrs) {
            $query = mysqli_query($connect_okrs, $sentencia);
            if (mysqli_num_rows($query) > 0) {
                while ($data = mysqli_fetch_assoc($query)) {

                    $empleado = $this->Empleado($data['id_empleado']);
                    $data['empleado'] = $empleado;

                    array_push($array, $data);
                }
            }
            return $array;
        } else {
            return false;
        }
    }

    public function nombreMes($n)
    {
        static $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        return ($n >= 1 && $n <= 12) ? $meses[$n - 1] : '';
    }












    //PARA OBTENER EL PORCENTAJE DE AVANCE POR OKRS
    //PARA OBTENER EL PORCENTAJE DE AVANCE POR OKRS
    //PARA OBTENER EL PORCENTAJE DE AVANCE POR OKRS
    public function PorcentajeAvanceOrks($id_okrs)
    {

        global $connect_okrs;

        $porcentaje_global = 0;
        $conteno_global = 0;

        $sentencia = "
        SELECT 
            avance, 
            tendencia, 
            meta 
        FROM Okrs_Resultados 
            WHERE id_okrs = '" . $id_okrs . "'
        ";
        $query = mysqli_query($connect_okrs, $sentencia);
        while ($data = mysqli_fetch_array($query)) {

            $porcentaje = $this->calcular_porcentaje_avance($data);

            if ($porcentaje != 0 && is_numeric($porcentaje)) {
                $porcentaje_global += $porcentaje;
            }
            $conteno_global++;
        }

        //VALIDAMOS SI EXISTE ALGO
        if ($porcentaje_global != 0) {
            $porcentaje_global = $porcentaje_global / $conteno_global;
        }

        return round($porcentaje_global);
    }

    public function PorcentajeAvanceOrksColaborador($id_okrs, $id_empleado){

        global $connect_okrs;

        $porcentaje_global = 0;
        $conteno_global = 0;

        $sentencia = "
        SELECT 
            avance, 
            tendencia, 
            meta 
        FROM Okrs_Resultados 
            WHERE id_okrs = '" . $id_okrs . "' 
            AND CONCAT(',', Okrs_Resultados.responsables, ',' ) LIKE '%,".$id_empleado.",%' 
        ";
        $query = mysqli_query($connect_okrs, $sentencia);
        while ($data = mysqli_fetch_array($query)) {

            $porcentaje = $this->calcular_porcentaje_avance($data);

            if ($porcentaje != 0 && is_numeric($porcentaje)) {
                $porcentaje_global += $porcentaje;
            }
            $conteno_global++;
        }

        //VALIDAMOS SI EXISTE ALGO
        if ($porcentaje_global != 0) {
            $porcentaje_global = $porcentaje_global / $conteno_global;
        }

        return round($porcentaje_global);
    }

    //FUNCTION PARA CALCULAR EL PORCENTAJE DE AVANCE DE UN RESULTADO CLAVE - SE PUEDE USAR EN 1 O VARIOS OKRS
    //FUNCTION PARA CALCULAR EL PORCENTAJE DE AVANCE DE UN RESULTADO CLAVE - SE PUEDE USAR EN 1 O VARIOS OKRS
    public function calcular_porcentaje_avance($data)
    {
        $porcentaje = 0;


        $data["tendencia"] = isset($data["tendencia"]) ? $data["tendencia"] : 1;

        if ($data["meta"]) {
            $avance = $data["avance"];
            $meta  = $data["meta"];

            //PARA LOS CASOS ASCENDENTES
            $porcentaje = ($avance  * 100) / $meta;

            //SOLO PARA LOS CASOS DONDE LA META ES NEGATIVA Y LA TENDENCIA ASCENDENTE
            if ($meta < 0) {
                if ($avance > $meta) {
                    $porcentaje = 100;
                }
            }

            //PARA LOS CASOS DESENTENTES
            if ($data["tendencia"] == 2) {
                //$porcentaje = ($meta / $avance  * 100);
                $porcentaje = ($meta * 100) / $avance;
            }

            if ($porcentaje > 100) {
                $porcentaje = 100;
            }
        } else {
            $porcentaje = 0;
        }

        if (is_nan($porcentaje)) {
            $porcentaje = 0;
        }

        return $porcentaje;
    }

    //PARA OBTENER EL PORCENTAJE DE AVANCE DE UNA INICIATIVA
    public function porcentaje_iniciativa($dataIniciativa)
    {
        $meta = $dataIniciativa["meta"];
        $avance = $dataIniciativa["avance"];
        $porcentaje = 0;

        if ($dataIniciativa["tendencia"] == 1) {
            $porcentaje = $avance * 100 / $meta;
        }
        if ($dataIniciativa["tendencia"] == 2) {
            $porcentaje = $meta * 100 / $avance;
        }
        return $porcentaje;
    }

    //FUNCTION PARA RETORNAS LAS ESCALAS DE LOS OKRS
    //FUNCTION PARA RETORNAS LAS ESCALAS DE LOS OKRS
    //FUNCTION PARA RETORNAS LAS ESCALAS DE LOS OKRS
    public function datos_consolidado_okrs($id_empresa, $okrs)
    {

        global $connect_admin;

        $queryEscala = mysqli_query($connect_admin, "SELECT * FROM Escala_Medicion WHERE id_empresa = " . $id_empresa . " ");
        $dataEscala = mysqli_fetch_array($queryEscala);

        /*

        $tituloRes1 = $dataEscala['titulo_uno'];
        $tituloRes2 = $dataEscala['titulo_tres'];
        $tituloRes3 = $dataEscala['titulo_cuatro'];
        $tituloRes4 = $dataEscala['titulo_cinco'];
        $tituloRes5 = $dataEscala['titulo_seis'];

        $colorRes1 = "#FF0000";
        $colorRes2 = "#FFF200";
        $colorRes3 = "#95FA03";
        $colorRes4 = "#14F209";
        $colorRes5 = "#00D30A";
        */

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

        foreach ($okrs as $okr) {

            $promedio = round($okr["porcentaje_avance"]);

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
            if ($promedio > $dataEscala['porcentaje_ocho']) {
                $aplica_5++;
            }
        }

        $cantidad_total = count($okrs);

        if ($aplica_1 > 0) {
            $porcentaje_1 = $aplica_1 * 100 / $cantidad_total;
        }
        if ($aplica_2 > 0) {
            $porcentaje_2 = $aplica_2 * 100 / $cantidad_total;
        }
        if ($aplica_3 > 0) {
            $porcentaje_3 = $aplica_3 * 100 / $cantidad_total;
        }
        if ($aplica_4 > 0) {
            $porcentaje_4 = $aplica_4 * 100 / $cantidad_total;
        }
        if ($aplica_5 > 0) {
            $porcentaje_5 = $aplica_5 * 100 / $cantidad_total;
        }

        //$resultado_componente = 0;
        $resultado_componente = 0;
        if ($count_componente > 0) {
            $resultado_componente = $general_componente / $count_componente;
        }

        $color_general = EscalaColor($resultado_componente);

        $array = array(
            "promedio_general" => round($resultado_componente, 2),
            "color_general" => $color_general,
            "cantidad_okrs" => $cantidad_total,
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

    //FUNCTION PARA RETORNAS LAS ESCALAS DE LOS INICIATIVAS
    //FUNCTION PARA RETORNAS LAS ESCALAS DE LOS INICIATIVAS
    //FUNCTION PARA RETORNAS LAS ESCALAS DE LOS INICIATIVAS
    public function datos_consolidado_iniciativas($id_empresa, $iniciativas)
    {
        global $connect_admin;

        $queryEscala = mysqli_query($connect_admin, "SELECT * FROM Escala_Medicion WHERE id_empresa = " . $id_empresa . " ");
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

        foreach ($iniciativas as $iniciativa) {

            $promedio = round($iniciativa["porcentaje_avance"]);

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
            if ($promedio > $dataEscala['porcentaje_ocho']) {
                $aplica_5++;
            }
        }

        $cantidad_total = count($iniciativas);

        if ($aplica_1 > 0) {
            $porcentaje_1 = $aplica_1 * 100 / $cantidad_total;
        }
        if ($aplica_2 > 0) {
            $porcentaje_2 = $aplica_2 * 100 / $cantidad_total;
        }
        if ($aplica_3 > 0) {
            $porcentaje_3 = $aplica_3 * 100 / $cantidad_total;
        }
        if ($aplica_4 > 0) {
            $porcentaje_4 = $aplica_4 * 100 / $cantidad_total;
        }
        if ($aplica_5 > 0) {
            $porcentaje_5 = $aplica_5 * 100 / $cantidad_total;
        }

        $resultado_componente = $general_componente / $count_componente;

        $color_general = EscalaColor($resultado_componente);

        $array = array(
            "promedio_general" => round($resultado_componente, 2),
            "color_general" => $color_general,
            "cantidad_iniciativas" => $cantidad_total,
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

    //FUNCTION PARA RETORNAS LAS ESCALAS DE LOS PLANES DE ACCION
    //FUNCTION PARA RETORNAS LAS ESCALAS DE LOS PLANES DE ACCION
    //FUNCTION PARA RETORNAS LAS ESCALAS DE LOS PLANES DE ACCION
    public function datos_consolidado_planes_accion($id_empresa, $planes)
    {
        global $connect_admin;

        $queryEscala = mysqli_query($connect_admin, "SELECT * FROM Escala_Medicion WHERE id_empresa = " . $id_empresa . " ");
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

        foreach ($planes as $plan) {

            $promedio = round($plan["porcentaje_avance"]);

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
            if ($promedio > $dataEscala['porcentaje_ocho']) {
                $aplica_5++;
            }
        }

        $cantidad_total = count($planes);

        if ($aplica_1 > 0) {
            $porcentaje_1 = $aplica_1 * 100 / $cantidad_total;
        }
        if ($aplica_2 > 0) {
            $porcentaje_2 = $aplica_2 * 100 / $cantidad_total;
        }
        if ($aplica_3 > 0) {
            $porcentaje_3 = $aplica_3 * 100 / $cantidad_total;
        }
        if ($aplica_4 > 0) {
            $porcentaje_4 = $aplica_4 * 100 / $cantidad_total;
        }
        if ($aplica_5 > 0) {
            $porcentaje_5 = $aplica_5 * 100 / $cantidad_total;
        }

        $resultado_componente = $general_componente / $count_componente;

        $color_general = EscalaColor($resultado_componente);

        $array = array(
            "promedio_general" => $resultado_componente,
            "color_general" => $color_general,
            "cantidad_planes" => $cantidad_total,
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

    public function reporte_lista_vicepresidencias($id_empresa)
    {
        global $connect_admin;
        $array = array();

        $sentencia =
            "SELECT
            Vicepresidencia.*,
            GROUP_CONCAT(DISTINCT Lideres_Vicepresidencia.id_lider) AS id_lideres
        FROM
            Vicepresidencia 
        LEFT JOIN
            Lideres_Vicepresidencia ON Lideres_Vicepresidencia.id_vicepresidencia = Vicepresidencia.id
        WHERE
            Vicepresidencia.id_empresa = '$id_empresa' 
            AND Vicepresidencia.estado = 1
        GROUP BY
            Vicepresidencia.id
        ORDER BY
            Vicepresidencia.nombre ASC
        ";
        $query = mysqli_query($connect_admin, $sentencia);
        if (mysqli_num_rows($query) > 0) {
            while ($data = mysqli_fetch_assoc($query)) {

                $lideres = explode(',', $data["id_lideres"]);

                $array_lideres = array();
                foreach ($lideres as $lider) {
                    $datos_lider = $this->DatosEmpleado($lider);
                    array_push($array_lideres, $datos_lider);
                }

                $data["array_lideres"] = $array_lideres;
                array_push($array, $data);
            }
        }

        return $array;
    }

    public function DatosEmpleado($id)
    {

        global $connect_admin;

        $sentencia =
            "SELECT
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
            Empleados.id = '$id'
        LIMIT 1
        ";

        $query = mysqli_query($connect_admin, $sentencia);
        $empleado = mysqli_num_rows($query) > 0 ? mysqli_fetch_assoc($query) : array();

        return $empleado;
    }

    //MOVER OKRS
    //ESTA FUNCION MUEVE LOS RESULTADOS CLAVES Y TODAS SUS RELACIONES A OTROS OKRS
    public function MoverResultadoClave($id_resultado, $id_okrs_nuevo){

        global $connect_okrs;

        //DATOS RESULTADO CLAVE
        $sentencia_resultado = "SELECT * FROM Okrs_Resultados WHERE id = '".$id_resultado."' ";
        $query = mysqli_query($connect_okrs, $sentencia_resultado);
        $data = mysqli_fetch_array($query);

        $queryOkrData = mysqli_query($connect_okrs, "SELECT * FROM Okrs WHERE id = '".$data["id_okrs"]."' ");
        $dataOkrData = mysqli_fetch_array($queryOkrData);

        //TABLAS
        /*
        Documentos_Resultados
        Okrs_Actividades
        Okrs_Comentarios
        Okrs_Comentarios_Iniciativas
        Okrs_Documentos
        Okrs_Iniciativas
        Okrs_Iniciativas_punto_restauracion
        */

        $id_okr_anterior = $data["id_okrs"];

        $sentencia_resultados =  "UPDATE Okrs_Resultados SET id_okrs = '".$id_okrs_nuevo."' WHERE id = '".$id_resultado."' " ;
        $sentencia_documento_resultados = "UPDATE Documentos_Resultados SET id_okrs = '".$id_okrs_nuevo."' WHERE id_resultado = '".$id_resultado."' ";
        $sentencia_actividades = "UPDATE Okrs_Actividades SET id_okrs = '".$id_okrs_nuevo."' WHERE id_resultado = '".$id_resultado."' ";
        $sentencia_comentarios = "UPDATE Okrs_Comentarios SET id_okrs = '".$id_okrs_nuevo."' WHERE id_resultado = '".$id_resultado."' "; 
        $sentencia_comentarios_iniciativas = "UPDATE Okrs_Comentarios_Iniciativas SET id_okrs = '".$id_okrs_nuevo."' WHERE id_resultado = '".$id_resultado."' ";
        $sentencia_documentos = "UPDATE Okrs_Documentos SET id_okrs = '".$id_okrs_nuevo."' WHERE id_resultado = '".$id_resultado."' ";
        $sentencia_iniciativas = "UPDATE Okrs_Iniciativas SET id_okrs = '".$id_okrs_nuevo."' WHERE id_resultado = '".$id_resultado."' ";
        $sentencia_iniciativas_restaura = "UPDATE Okrs_Iniciativas_punto_restauracion SET id_okrs = '".$id_okrs_nuevo."' WHERE id_resultado = '".$id_resultado."' ";

        /*
        mysqli_query($connect_okrs, $sentencia_documento_resultados);
        mysqli_query($connect_okrs, $sentencia_actividades);
        mysqli_query($connect_okrs, $sentencia_comentarios);
        mysqli_query($connect_okrs, $sentencia_comentarios_iniciativas);
        mysqli_query($connect_okrs, $sentencia_documentos);
        mysqli_query($connect_okrs, $sentencia_iniciativas);
        mysqli_query($connect_okrs, $sentencia_iniciativas_restaura);
        */

        //echo $id_okr_anterior;
        //echo "<br>";

        /*
        echo $sentencia_documento_resultados;
        echo "<br>";
        echo $sentencia_actividades;
        echo "<br>";
        */

        
 

      

        $accion = "MOVER";
        $descripcion = 'Se mueve el resultado clave '.$id_resultado.' : '.$data["descripcion"].' del Okrs '.$id_okr_anterior.' al '.$id_okrs_nuevo;
        
        $nodo = array(
            "accion" => $accion, 
            "descripcion" => $descripcion, 
            "id_okrs_nuevo" => $id_okrs_nuevo, 
            "id_okr_anterior" => $id_okr_anterior, 
            "id_resultado" => $id_resultado, 
            "tipo" => $dataOkrData["tipo"]
        );
       

        return $nodo;
    }

    //MOVER OKRS
    //ESTA FUNCION MUEVE LOS RESULTADOS CLAVES Y TODAS SUS RELACIONES A OTROS OKRS
    public function DuplicarResultadoClave($id_resultado){

        global $connect_okrs;

        //DATOS RESULTADO CLAVE
        $sentencia_resultado = "SELECT * FROM Okrs_Resultados WHERE id = '".$id_resultado."' ";
        $query = mysqli_query($connect_okrs, $sentencia_resultado);
        $data = mysqli_fetch_array($query);

        $queryOkrData = mysqli_query($connect_okrs, "SELECT * FROM Okrs WHERE id = '".$data["id_okrs"]."' ");
        $dataOkrData = mysqli_fetch_array($queryOkrData);

        //TABLAS
        /*
        Documentos_Resultados
        Okrs_Actividades
        Okrs_Comentarios
        Okrs_Comentarios_Iniciativas
        Okrs_Documentos
        Okrs_Iniciativas
        Okrs_Iniciativas_punto_restauracion
        */

        $id_okrs = $data["id_okrs"];

        $sentencia_resultados =  "
            INSERT INTO Okrs_Resultados(
                id_empresa,
                id_okrs,
                id_okrs_padre,
                id_empleado,
                responsables,
                descripcion,
                avance,
                fecha_inicia,
                fecha_entrega,
                tendencia,
                medicion,
                meta,
                meta_minimo,
                meta_maximo,
                periodo,
                estado,
                orden,
                created_at,
                updated_at
            )
            VALUES(
                id_empresa,
                '".$data["id_okrs"]."',
                '".$data["id_okrs_padre"]."',
                '".$data["id_empleado"]."',
                responsables,
                descripcion,
                avance,
                fecha_inicia,
                fecha_entrega,
                tendencia,
                medicion,
                meta,
                meta_minimo,
                meta_maximo,
                periodo,
                estado,
                orden,
                created_at,
                updated_at
            )
        ";


        

        /*
        mysqli_query($connect_okrs, $sentencia_documento_resultados);
        mysqli_query($connect_okrs, $sentencia_actividades);
        mysqli_query($connect_okrs, $sentencia_comentarios);
        mysqli_query($connect_okrs, $sentencia_comentarios_iniciativas);
        mysqli_query($connect_okrs, $sentencia_documentos);
        mysqli_query($connect_okrs, $sentencia_iniciativas);
        mysqli_query($connect_okrs, $sentencia_iniciativas_restaura);
        */

        

        
 

      

        $accion = "DUPLICAR";
        $descripcion = 'Se duplica el resultado clave '.$id_resultado.' : '.$data["descripcion"].' del Okrs '.$id_okrs;
        
        $nodo = array(
            "accion" => $accion, 
            "descripcion" => $descripcion, 
            "id_okrs" => $id_okrs, 
            "id_resultado" => $id_resultado, 
            "tipo" => $dataOkrData["tipo"]
        );
       

        return $nodo;
    }
}
