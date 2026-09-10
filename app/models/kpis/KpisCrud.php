<?php

class KpisCrud
{
    public $hoy;

    public function __construct()
    {
        $this->hoy = date("Y-m-d H:i:s");
    }

    public function crear_Kpis($request)
    {
        dd($request);
    }

    public function obtener_data_kpis($id_kpis)
    {
        global $connect_kpis;

        $sentencia = "
            SELECT *
                FROM Kpis
            WHERE
                id = '" . $id_kpis . "' 
        ";
        $query = mysqli_query($connect_kpis, $sentencia);
        $data = mysqli_fetch_array($query);

        $sentencia_frecuencia = "
            SELECT *
                FROM Frecuencia_Kpis
            WHERE
                id_kpi = '" . $id_kpis . "' 
        ";

        $queryFrecuencia = mysqli_query($connect_kpis, $sentencia_frecuencia);
        $dataFrecuencia = mysqli_fetch_array($queryFrecuencia);

        $data["array_frecuencias"] = $dataFrecuencia;




        return $data;
    }

    //PARA ACTULIZAR EL AVANCE DE UN KPIS
    public function actualizar_avance_Kpis($request)
    {

        global $connect_kpis;
        $hoy = date("Y-m-d H:i:s");

        if ($request["tipo"] == 1) { //MENSUAL
            $avance_1 = $request["avance_1"];
            $avance_2 = $request["avance_2"];
            $avance_3 = $request["avance_3"];
            $avance_4 = $request["avance_4"];
            $avance_5 = $request["avance_5"];
            $avance_6 = $request["avance_6"];
            $avance_7 = $request["avance_7"];
            $avance_8 = $request["avance_8"];
            $avance_9 = $request["avance_9"];
            $avance_10 = $request["avance_10"];
            $avance_11 = $request["avance_11"];
            $avance_12 = $request["avance_12"];
        }

        if ($request["tipo"] == 2) { //BIMESTRA
            $avance_1 = $request["avance_1"];
            $avance_2 = $request["avance_1"];
            $avance_3 = $request["avance_3"];
            $avance_4 = $request["avance_3"];
            $avance_5 = $request["avance_5"];
            $avance_6 = $request["avance_5"];
            $avance_7 = $request["avance_7"];
            $avance_8 = $request["avance_7"];
            $avance_9 = $request["avance_9"];
            $avance_10 = $request["avance_9"];
            $avance_11 = $request["avance_11"];
            $avance_12 = $request["avance_11"];
        }

        if ($request["tipo"] == 3) { //TRIMESTRAL

            $avance_1 = $request["avance_1"];
            $avance_2 = $request["avance_1"];
            $avance_3 = $request["avance_1"];
            $avance_4 = $request["avance_4"];
            $avance_5 = $request["avance_4"];
            $avance_6 = $request["avance_4"];
            $avance_7 = $request["avance_7"];
            $avance_8 = $request["avance_7"];
            $avance_9 = $request["avance_7"];
            $avance_10 = $request["avance_10"];
            $avance_11 = $request["avance_10"];
            $avance_12 = $request["avance_10"];
        }
        if ($request["tipo"] == 6) {
            $avance_3 = $request["avance_3"];
            $avance_4 = $request["avance_3"];
            $avance_5 = $request["avance_3"];
            $avance_6 = $request["avance_3"];
            $avance_7 = $request["avance_7"];
            $avance_8 = $request["avance_7"];
            $avance_9 = $request["avance_7"];
            $avance_10 = $request["avance_7"];
            $avance_11 = $request["avance_11"];
            $avance_12 = $request["avance_11"];
            $avance_1 = $request["avance_11"];
            $avance_2 = $request["avance_11"];
        }
        if ($request["tipo"] == 4) {
            $avance_1 = $request["avance_1"];
            $avance_2 = $request["avance_1"];
            $avance_3 = $request["avance_1"];
            $avance_4 = $request["avance_1"];
            $avance_5 = $request["avance_1"];
            $avance_6 = $request["avance_1"];
            $avance_7 = $request["avance_7"];
            $avance_8 = $request["avance_7"];
            $avance_9 = $request["avance_7"];
            $avance_10 = $request["avance_7"];
            $avance_11 = $request["avance_7"];
            $avance_12 = $request["avance_7"];
        }

        if ($request["tipo"] == 5) {
            $avance_1 = $request["avance_7"];
            $avance_2 = $request["avance_7"];
            $avance_3 = $request["avance_7"];
            $avance_4 = $request["avance_7"];
            $avance_5 = $request["avance_7"];
            $avance_6 = $request["avance_7"];
            $avance_7 = $request["avance_7"];
            $avance_8 = $request["avance_7"];
            $avance_9 = $request["avance_7"];
            $avance_10 = $request["avance_7"];
            $avance_11 = $request["avance_7"];
            $avance_12 = $request["avance_7"];
        }


        $sentencia = "
            UPDATE
                Frecuencia_Kpis
            SET
                avance_1 = '" . $avance_1 . "',
                avance_2 = '" . $avance_2 . "',
                avance_3 = '" . $avance_3 . "',
                avance_4 = '" . $avance_4 . "',
                avance_5 = '" . $avance_5 . "',
                avance_6 = '" . $avance_6 . "',
                avance_7 = '" . $avance_7 . "',
                avance_8 = '" . $avance_8 . "',
                avance_9 = '" . $avance_9 . "',
                avance_10 = '" . $avance_10 . "',
                avance_11 = '" . $avance_11 . "',
                avance_12 = '" . $avance_12 . "',
                updated_at = '" . $hoy . "'
            WHERE
                id_kpi = '" . $request["id_kpi"] . "'
        ";

        //echo $sentencia;

        mysqli_query($connect_kpis, $sentencia);
    }

    //PARA CREAR LOS ADMINSITRADORES DE LOS KPIS
    public function crear_Kpis_Administradores($id_empresa, $request)
    {

        global $connect_kpis;



        /* VERIFICAR QUE NO SE REPITA EL REGISTRO */
        $sentencia = "
            SELECT
                id
            FROM
                Administradores_Kpi
            WHERE
                id_empleado = '" . $request["id_colaborador"] . "' AND 
                id_vp = '" . $request["id_vicepresidencia"] . "' AND 
                id_area = '" . $request["id_area"] . "' AND 
                id_empresa = '" . $id_empresa . "'
        ";
        $query = mysqli_query($connect_kpis, $sentencia);
        if ($query->num_rows > 0) {
        } else {
            $sentencia = "
            INSERT INTO Administradores_Kpi(
                id_empresa,
                id_empleado,
                id_vp,
                id_area,
                estado,
                created_at, 
                updated_at
            )
            VALUES(
                '" . $id_empresa . "',
                '" . $request["id_colaborador"] . "',
                '" . $request["id_vicepresidencia"] . "',
                '" . $request["id_area"] . "',
                '1',
                '" . $this->hoy . "',
                '" . $this->hoy . "'
            )
           ";

            $query = mysqli_query($connect_kpis, $sentencia);
        }

        /*
        if (mysqli_num_rows($query) > 0) {
            return '<div class="alert alert-danger" role="alert">El empleado ya se había asignado como administrador previamente, y no se puede reasignar.</div>';
        } else {
            
            return '<div class="alert alert-success text-light" role="alert">El empleado ha sido asignado como administrador.</div>';
        }
        */
    }

    public function Crear_Objetivo_SG($id_empresa, $request)
    {

        global $connect_kpis;
        $hoy = date("Y-m-d H:i:s");
        $sentencia = "
        INSERT INTO `Objetivo_Sg`(
            id_empresa,
            anio,
            id_vp,
            id_area,
            objetivo,
            estado,
            created_at,
            updated_at
        )
        VALUES(
            '" . $id_empresa . "',
            '" . $request["anio"] . "',
            '" . $request["id_vicepresidencia"] . "', 
            '" . $request["id_area"] . "', 
            '" . $request["objetivo"] . "', 
            1,
            '" . $hoy . "',
            '" . $hoy . "'
        )
        ";

        $query = mysqli_query($connect_kpis, $sentencia);
        $id_tmp = mysqli_insert_id($connect_kpis);

        return $id_tmp;
    }

    public function Acutualizar_Objetivo_SG($id, $request)
    {
        global $connect_kpis;
        $hoy = date("Y-m-d H:i:s");
        $sentencia = "
        UPDATE Objetivo_Sg SET
           
            anio = '" . $request["anio"] . "',
            id_vp = '" . $request["id_vicepresidencia"] . "', 
            id_area = '" . $request["id_area"] . "', 
            objetivo = '" . $request["objetivo"] . "', 
            updated_at = '" . $hoy . "'
            WHERE 
                id = '" . $id . "'
        ";

        $query = mysqli_query($connect_kpis, $sentencia);
        $id_tmp = mysqli_insert_id($connect_kpis);
    }

    public function Guardar_Integrantes($id_empresa, $id_empleado, $anio, $request)
    {
        global $connect_kpis;
        $empleados = $request["empleados"] ?? [];

        foreach ($empleados as $id_colaborador => $tipo) {

            $sentencia =
                "INSERT INTO Kpis_Colaborador
            (id_empresa, id_empleado, id_kpi, anio, area_macro, area_proceso, id_colaborador, tipo, created_at)
            VALUES
            ('$id_empresa',
            '$id_empleado',
            '" . $request["id_registro"] . "',
            '$anio',
            '" . $request["id_area_macro"] . "',
            '" . $request["id_area_proceso"] . "',
            '$id_colaborador',
            '$tipo',
            '" . $this->hoy . "')";

                mysqli_query($connect_kpis, $sentencia);
        }
        echo "<div class='alert alert-success'>Integrantes asociados correctamente al Kpi.</div>";

        /* dd($id_empresa);
        dd($id_empleado);
        dd($anio);
        dd($request); */
    }
}
