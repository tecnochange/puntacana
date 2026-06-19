<?php

class OkrsCrud
{
    public $hoy;

    public function __construct()
    {
        // Asignar los valores a propiedades de la clase para usarlos en cualquier método.
        $this->hoy = date("Y-m-d H:i:s");
    }

    public function Crear_Okrs($id_empresa, $request){
        global $connect_okrs;

        $sentencia = "
        INSERT INTO Okrs(
            id_empresa,
            id_resultado_padre,
            tipo,
            id_empleado,
            responsables,
            objetivos_estrategicos,
            objetivo_okr,
            fecha_inicia,
            fecha_termina,
            periodo,
            anio,
            exigible,
            estado,
            created_at
        )
        VALUES(
            '" . $id_empresa . "',
            '" . $request["id_resultado_padre"] . "',
            '" . $request["tipo"] . "',
            '" . $request["owner"] . "',
            '',
            '" . $request["objetivos_estrategicos"] . "',
            '" . $request["objetivo_okr"] . "',
            '" . $request["fecha_inicia"] . "',
            '" . $request["fecha_termina"] . "',
            '" . $request["periodo"] . "',
            '" . $request["anio"] . "',
            1,
            1,
            '" . $this->hoy . "'
        )
        ";

        mysqli_query($connect_okrs, $sentencia);
        $id_tmp = mysqli_insert_id($connect_okrs);

        //INSERTAR REGISTRO VICEPRESIDENCIA
        $sentencia_vicepresidencia = "
        INSERT INTO Okrs_Vicepresidencia ( id_empresa , id_vicepresidencia, id_obj_estrategico, id_obj_organizacional,id_okrs, created_at ) 
        VALUES 
        ( '" .$id_empresa. "', '".$_POST["vicepresidencia"]."', '".$request["objetivos_estrategicos"]."' ,'".$request["id_resultado_padre"]."' , '" . $id_tmp . "','" . $this->hoy . "' )
        ";
        mysqli_query($connect_okrs, $sentencia_vicepresidencia);

        //CREAR EL PRIMER MIEMBRO DE EQUIPO
        $sentencia_equipo = "
			INSERT INTO  Okrs_Equipos(id_empresa, id_okrs, id_empleado, tipo, estado, created_at) 
			VALUES 
			( '".$id_empresa."', '" . $id_tmp . "', '" . $request["owner"] . "', 1, 1, '" . $this->hoy . "' )
		";
        mysqli_query($connect_okrs, $sentencia_equipo);

        echo '
            <script>
                window.location.href = "?pg=okrs/okr/detalle&id=' . $id_tmp . '";
            </script>
        ';
    }

    public function Actualizar_Okrs($request)
    {
        global $connect_okrs;

        $sentencia = " 
        UPDATE
            Okrs
        SET
            tipo = '" . $request["tipo"] . "',
            id_empleado = '" . $request["owner"] . "',
            objetivos_estrategicos = '" . $request["objetivos_estrategicos"] . "',
            objetivo_okr = '" . $request["objetivo_okr"] . "',
            fecha_inicia = '" . $request["fecha_inicia"] . "',
            fecha_termina = '" . $request["fecha_termina"] . "',
            periodo = '" . $request["periodo"] . "',
            anio = '" . $request["anio"] . "'
        WHERE
            id = '" . $request["id_registro"] . "' 
        ";
        mysqli_query($connect_okrs, $sentencia);
        //echo '<script>window.location.href = "?pg=okrs/okr/detalle&id='.$request["id_registro"].'"</script>';
    }

    //PARA CREAR AREAS A LOS OKRS
    public function Crear_Okrs_Areas($id_empresa, $request){
        global $connect_okrs;

        $area = isset($request["id_area"]) ? $request["id_area"] : 0;

        $sentencia = "
        INSERT INTO Okrs_Areas(
            id_empresa,
            id_okrs,
            id_vicepresidencia, 
            id_area,
            created_at
        )
        VALUES(
            '$id_empresa',
            '" . $request["id_okrs"] . "',
            '" . $request["id_vicepresidencia"] . "',
            '" . $area . "',
            '" . $this->hoy . "'
        )
        ";

        mysqli_query($connect_okrs, $sentencia);
        $nuevo_id = mysqli_insert_id($connect_okrs);
    }

    //PARA ACTUALIZAR LAS AREAS DE LOS OKRS
    public function Actualizar_Okrs_Areas($request){
        global $connect_okrs;

        $sentencia =
            " UPDATE
            Okrs_Areas
        SET
            id_vicepresidencia = '".$request["id_vicepresidencia"]."',
            id_area = '".$request["id_area"]."'
        WHERE
            id = '".$request["id_registro"]."'  
        ";
        mysqli_query($connect_okrs, $sentencia);
        
    }

    public function Guardar_Integrantes($id_empresa, $request)
    {
        global $connect_okrs;
        $empleados = $request["empleados"] ?? [];

        foreach ($empleados as $id_empleado => $roles) {
            // Determinar qué checkboxes fueron marcados
            $tipo = isset($roles["admin_kr"]) ? 1 : (isset($roles["contrib_dir"]) ? 2 : (isset($roles["contrib_ap"]) ? 3 : 0));

            $sentencia =
                "INSERT INTO
                Okrs_Equipos (id_empresa, id_okrs, id_empleado, tipo, estado, created_at)
            VALUES
                ('$id_empresa', '" . $request["id_registro"] . "', '$id_empleado', '$tipo', 1, '" . $this->hoy . "')";
            mysqli_query($connect_okrs, $sentencia);
        }
        echo "<div class='alert alert-success'>Integrantes asociados correctamente al OKRs.</div>";
    }

    public function Crear_Resultados_Claves($id_empresa, $request)
    {
        global $connect_okrs;
        $request = $request ?? [];

        $responsables = implode(',', $request["id_responsables"]);
        $orden = 1;

        $sentencia =
            "INSERT INTO
            Okrs_Resultados (
                id_empresa,
                id_okrs,
                id_empleado,
                responsables,
                descripcion,
                avance,
                fecha_inicia,
                fecha_entrega,
                tendencia,
                medicion,
                meta,
                periodo,
                estado,
                orden,
                created_at)
        VALUES
            (
                '$id_empresa',
                '" . $request["id_okrs"] . "',
                '" . $request["id_empleado"] . "',
                '$responsables',
                '" . $request["descripcion_del_resultado"] . "',
                0,
                '" . $request["fecha_inicia"] . "',
                '" . $request["fecha_termina"] . "',
                '" . $request["tendencia"] . "',
                '" . $request["medicion"] . "',
                '" . $request["meta"] . "',
                '" . $request["periodo"] . "',
                '1',
                '$orden', 
                '$this->hoy')
        ";

        mysqli_query($connect_okrs, $sentencia);
    }

    public function Actualizar_Resultados_Claves($request)
    {
        global $connect_okrs;

        $responsables = implode(',', $request["id_responsables"]);

        $sentencia =
            " UPDATE
            Okrs_Resultados
        SET
            responsables = '" . $responsables . "',
            fecha_inicia = '" . $request["fecha_inicia"] . "',
            fecha_entrega = '" . $request["fecha_termina"] . "',
            medicion = '" . $request["medicion"] . "',
            tendencia = '" . $request["tendencia"] . "',
            meta = '" . $request["meta"] . "',
            periodo = '" . $request["periodo"] . "',
            descripcion = '" . $request["descripcion_del_resultado"] . "'
        WHERE
            id = '" . $request["id_registro"] . "' 
        ";

        mysqli_query($connect_okrs, $sentencia);
    }

    public function Crear_Iniciativa($request)
    {

        global $connect_okrs;
        $request = $request ?? [];

        $responsables = implode(',', $request["id_responsables"]);
        $orden = 1;

        $sentencia =
            "INSERT INTO
            Okrs_Iniciativas (
                id_okrs,
                id_resultado,
                id_empleado,
                responsables,
                descripcion,
                mes,
                fecha_entrega,
                meta,
                avance,
                tendencia,
                aprobacion,
                created_at
            )
        VALUES
            (
                '" . $request["id_okr"] . "',
                '" . $request["id_resultado"] . "',
                '" . $request["id_empleado"] . "',
                '$responsables',
                '" . $request["descripcion_iniciativa"] . "',
                '" . $request["mes"] . "',
                '" . $request["fecha_entrega"] . "',
                '" . $request["meta"] . "',
                '0',
                '" . $request["tendencia"] . "',
                '1', 
                '$this->hoy'
            )
        ";

        try {
            mysqli_query($connect_okrs, $sentencia);
            echo '<script>window.location.href = "?pg=okrs/okr/iniciativas&id_okr=' . $request["id_okr"] . '&id_resultado=' . $request["id_resultado"] . '"</script>';
        } catch (\Throwable $th) {
            throw $th;
            return false;
        }
    }

    public function Actualizar_Iniciativa($request)
    {
        global $connect_okrs;

        $responsables = implode(',', $request["id_responsables"]);

        $sentencia =
            " UPDATE
            Okrs_Iniciativas
        SET
            responsables = '" . $responsables . "',
            descripcion = '" . $request["descripcion_iniciativa"] . "',
            mes = '" . $request["mes"] . "',
            fecha_entrega = '" . $request["fecha_entrega"] . "',
            meta = '" . $request["meta"] . "',
            tendencia = '" . $request["tendencia"] . "',
            updated_at = '" . $this->hoy . "'
        WHERE
            id = '" . $request["id_registro"] . "' 
        ";

        try {
            mysqli_query($connect_okrs, $sentencia);
            echo '<script>window.location.href = "?pg=okrs/okr/iniciativas&id_okr=' . $request["id_okr"] . '&id_resultado=' . $request["id_resultado"] . '"</script>';
        } catch (\Throwable $th) {
            throw $th;
            return false;
        }
    }

    public function Crear_plan_accion($id_empleado, $id_empresa, $request)
    {

        global $connect_okrs;
        $request = $request ?? [];

        $sentencia =
            "INSERT INTO
            Okrs_Actividades (
                id_empresa,
                id_okrs,
                id_resultado,
                id_iniciativa,
                id_empleado,
                id_asignado,
                ciclo, 
                descripcion,
                prioridad,
                meta,
                progreso,
                estado_backlog,
                fecha_inicia,
                fecha_entrega,
                aprobacion,
                created_at
            )
        VALUES
            (
                '$id_empresa',
                '".$request["id_okrs"]."',
                '".$request["id_resultado"]."',
                '".$request["id_iniciativa"]."',
                '$id_empleado',
                '$id_empleado', 
                '".$request["ciclo"]."',
                '".$request["descripcion"]."',
                '".$request["prioridad"]."',
                '".$request["meta"]."',
                '0',
                '1',
                '".$request["fecha_inicia"]."',
                '".$request["fecha_entrega"]."',
                '1',
                '$this->hoy'
            )
        "; 


        try {
            mysqli_query($connect_okrs, $sentencia);

            $id_tmp = mysqli_insert_id($connect_okrs);

            return $id_tmp;
            echo '<script> //window.location.href = "?pg=okrs/okr/planes_accion&id_plan_accion='.$id_tmp.'"</script>';
            //return $id_tmp;
        } catch (\Throwable $th) {
            throw $th;
            return false;
        }
    }

    public function Actualizar_Plan_Accion($request)
    {
        global $connect_okrs;

        $sentencia =
            " UPDATE
            Okrs_Actividades
        SET
            descripcion = '".$request["descripcion"]."',
            prioridad = '".$request["prioridad"]."',
            meta = '".$request["meta"]."', 
            progreso = '".$request["progreso"]."', 
            estado_backlog = '".$request["estado_backlog"]."', 
            fecha_inicia = '".$request["fecha_inicia"]."',
            fecha_entrega = '".$request["fecha_entrega"]."', 
            ciclo = '".$request["ciclo"]."', 
            updated_at = '" . $this->hoy . "'
        WHERE
            id = '" . $request["id_registro"] . "' 
        ";

        //echo $sentencia;

        try {
            mysqli_query($connect_okrs, $sentencia);
            echo '<script> //window.location.href = "?pg=okrs/okr/planes_accion&id_plan_accion='.$request["id_registro"].'"</script>';
        } catch (\Throwable $th) {
            throw $th;
            return false;
        }
    }
}
