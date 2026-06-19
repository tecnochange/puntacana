<?php
class Competencias
{

    public function Ciclo($id_empresa, $anio)
    {
        global $connect_valoracion;

        $sentencia = "SELECT * FROM Ciclos WHERE id_empresa = '$id_empresa' AND anio = '$anio'";
        $query = mysqli_query($connect_valoracion, $sentencia);
        $data = mysqli_num_rows($query) > 0 ? mysqli_fetch_assoc($query) : array();
        return $data;
    }

    public function Empleados($id_empresa)
    {
        global $connect_admin;
        $array = array();

        $sentencia =
            "SELECT
            Empleados.id AS id,
            Empleados.id_empresa AS id_empresa,
            Empleados.nombre AS nombre,
            Empleados.documento AS documento,
            Empleados.estado AS estado,
            Cargos.id AS id_cargo,
            Cargos.nombre AS nombre_cargo,
            Areas.nombre AS nombre_area,
            Areas.id AS id_area
        FROM
            Empleados
        LEFT JOIN
            Cargos ON Cargos.id = Empleados.id_cargo
        LEFT JOIN
            Areas ON Areas.id = Empleados.area
        WHERE
            Empleados.id_empresa = '$id_empresa'
        ORDER BY
            Empleados.nombre ASC 
        ";

        $query = mysqli_query($connect_admin, $sentencia);
        while ($empleado = mysqli_fetch_assoc($query)) {
            array_push($array, $empleado);
        }
        return $array;
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
            Empleados.estado,
            Cargos.nombre AS nombre_cargo,
            Areas.nombre AS nombre_area,
            goforagile_admin.Vicepresidencia.nombre AS nombre_vicepresidencia
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
        WHERE
            Empleados.id = '" . $id . "'
        ";
        $query = mysqli_query($connect_admin, $sentencia);
        $data = mysqli_num_rows($query) > 0 ? mysqli_fetch_assoc($query) : array();
        return $data;
    }

    public function Obtener_Competencia($id)
    {
        global $connect_valoracion;

        $sentencia = "SELECT * FROM Competencias WHERE id = '$id' ";
        $query = mysqli_query($connect_valoracion, $sentencia);
        $data = mysqli_num_rows($query) > 0 ? mysqli_fetch_assoc($query) : array();

        return $data;
    }

    public function Obtener_Tipo_Competencia($id)
    {
        global $connect_valoracion;

        $sentencia = "SELECT * FROM Tipos WHERE id = '$id' ";
        $query = mysqli_query($connect_valoracion, $sentencia);
        $data = mysqli_num_rows($query) > 0 ? mysqli_fetch_assoc($query) : array();

        return $data;
    }

    public function Obtener_Nivel_Competencia($id)
    {
        global $connect_valoracion;

        $sentencia = "SELECT * FROM Niveles WHERE id = '$id' ";
        $query = mysqli_query($connect_valoracion, $sentencia);
        $data = mysqli_num_rows($query) > 0 ? mysqli_fetch_assoc($query) : array();

        return $data;
    }

    public function Evaluadores($id_empresa, $anio, $id_ciclo)
    {
        global $connect_valoracion;
        global $array_Tipo_Colaborador;

        $array = array();

        $sentencia =
            "SELECT
            Evaluadores.*,
            goforagile_admin.Empleados.estado as empleado_estado
        FROM
            Evaluadores
        LEFT JOIN
            goforagile_admin.Empleados ON Empleados.id = Evaluadores.id_empleado
        WHERE
            Evaluadores.id_empresa = '$id_empresa' AND Evaluadores.anio = '$anio' AND Evaluadores.id_ciclo = '$id_ciclo'
        ";
        $query = mysqli_query($connect_valoracion, $sentencia);

        while ($data = mysqli_fetch_assoc($query)) {

            if ($data["empleado_estado"] == "1") { //SI EL COLABORADOR ESTÁ ACTIVO
                $data["empleado"] = $this->Empleado($data["id_empleado"]);
                $data["evaluador"] = $this->Empleado($data["id_evaluador"]);

                //TIPO DE COLABORADOR
                foreach ($array_Tipo_Colaborador as $tipo) {
                    if ($tipo[0] == $data["tipo"]) {
                        $data["txt_tipo"] = $tipo[1];
                    }
                }

                array_push($array, $data);
            }
        }

        return $array;
    }

    public function Evaluador($id_user, $anio, $id_ciclo)
    {
        global $connect_valoracion;

        $sentencia =
            "SELECT
            *
        FROM
            Evaluadores
        WHERE
            id_evaluador = '$id_user' AND id_empleado = '$id_user' AND anio = '$anio' AND id_ciclo = '$id_ciclo' ";
        $query = mysqli_query($connect_valoracion, $sentencia);
        $data = mysqli_num_rows($query) > 0 ? mysqli_fetch_assoc($query) : array();
        return $data;
    }

    //PARA OBTENER EL PROMEDIO DE COMPETENCIAS
    public function ResultadoCompetencias($id_empleado, $id_empresa, $anio)
    {
        global $connect_valoracion;

        $sentencia = "SELECT * FROM Competencias_Evaluaciones_New WHERE id_evaluado = '" . $id_empleado . "' AND tipo_evaluacion = 5 AND anio = '" . $anio . "' AND id_empresa = '" . $id_empresa . "'";

        $query = mysqli_query($connect_valoracion, $sentencia);
        $data = mysqli_fetch_array($query);

        $resultado = 0;
        $porcentaje = 0;
        if ($query->num_rows > 0) {
            $resultado = $data["promedio"];

            $porcentaje = round(($resultado * 100 / 5), 1);
        }

        return $porcentaje;
    }

    public function Tipos_Competencias($id_empresa, $anio)
    {
        global $connect_valoracion;
        $array = array();

        $sentencia = "SELECT * FROM Tipos WHERE id_empresa = '$id_empresa' AND anio = '$anio' ORDER BY nombre ASC";

        $query = mysqli_query($connect_valoracion, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {
            array_push($array, $data);
        }

        return $array;
    }

     public function Obtener_Informe_Competencia($id)
     {
        global $connect_valoracion;

        $sentencia =
        "SELECT
            *
        FROM
            Competencias_Preguntas
        WHERE
            id = '$id'
        ";

        $query = mysqli_query($connect_valoracion, $sentencia);
        $data = mysqli_num_rows($query) > 0 ? mysqli_fetch_assoc($query) : array();

        return $data;
     }

    public function Vicepresidencias($id_empresa)
    {
        global $connect_admin;
        $array = array();

        $sentencia = "SELECT * FROM Vicepresidencia WHERE id_empresa = '$id_empresa' AND estado = 1 ORDER BY nombre ASC";
        $query = mysqli_query($connect_admin, $sentencia);

        while ($data = mysqli_fetch_assoc($query)) {
            array_push($array, $data);
        }

        return $array;
    }


    public function vista_General_Evaluaciones($id_empresa, $id_ciclo, $anio)
    {
        global $connect_valoracion;
        $array = array();

        //CONTADORES
        $contadores = [];
        $procesos_valoracion = $this->Procesos_Valoracion();

        $sentencia =
            "SELECT
            SQL_CALC_FOUND_ROWS id_empleado,
            doc_empleado,
            nombre_empleado,
            nombre_cargo,
            nombre_area,
            nombre_vp,
            doc_jefe,
            nombre_jefe,
            tipo_evaluador,
            proceso_valoracion,
            etiqueta,
            fondo,
            colorTexto
        FROM
            vista_general_evaluaciones
        WHERE
            ciclo = '$id_ciclo' AND anio = '$anio' AND empresa = '$id_empresa'
        ORDER BY
            nombre_empleado ASC
        ";

        $query = mysqli_query($connect_valoracion, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {
            array_push($array, $data);
        }

        //SIN VALORACION
        $contadores[0] = [
            "id" => "0",
            "etiqueta" => "Sin Valoración",
            "color_fondo" => "#f76d6b",
            "color_fuente" => "white",
            "cantidad" => 0,
            "porcentaje" => 0
        ];
        foreach ($procesos_valoracion as $contador) {
            //VALORADOS
            $contadores[$contador["id"]] = [
                "id" => $contador["id"],
                "etiqueta" => $contador["etiqueta"],
                "color_fondo" => $contador["color_fondo"],
                "color_fuente" => $contador["color_fuente"],
                "cantidad" => 0,
                "porcentaje" => 0
            ];
        }

        //CANTIDADES
        foreach ($array as $registro) {
            $proceso = (int) $registro["proceso_valoracion"];

            // Si existe el contador, suma
            if (isset($contadores[$proceso])) {
                $contadores[$proceso]["cantidad"]++;
            } else {
                // Si no existe o viene vacío, cuenta como "Sin Valoración"
                $contadores[0]["cantidad"]++;
            }
        }

        //PORCENTAJES
        if (count($array) > 0) {
            foreach ($contadores as $id => $contador) {
                $contadores[$id]["porcentaje"] = round(
                    ($contador["cantidad"] / count($array)) * 100,
                    2
                );
            }
        }

        $nodo = [
            "registros" => $array,
            "total_registros" => count($array),
            "contadores" => $contadores
        ];

        return $nodo;
    }

    public function Procesos_Valoracion()
    {
        global $connect_valoracion;
        $array = array();

        $sentencia = "SELECT * FROM Proceso_Valoracion";

        $query = mysqli_query($connect_valoracion, $sentencia);
        while ($data = mysqli_fetch_assoc($query)) {
            array_push($array, $data);
        }

        return $array;
    }

    public function Obtener_Escala($id_empresa, $anio)
    {
        global $connect_valoracion;

        $sentencia =
        "SELECT
            *
        FROM
            Escalas
        WHERE
            id_empresa = '$id_empresa'
            AND anio = '$anio'
        LIMIT 1
        ";
        $query = mysqli_query($connect_valoracion, $sentencia);
        $data = mysqli_num_rows($query) > 0 ? mysqli_fetch_assoc($query) : array();

        return $data;
    }

    public function Obtener_Escala_Interpretacion($id_empresa, $anio)
    {
        global $connect_valoracion;

        $sentencia =
        "SELECT
            *
        FROM
            Escalas_Interpretacion
        WHERE
            id_empresa = '$id_empresa'
            AND anio = '$anio'
        LIMIT 1
        ";
        $query = mysqli_query($connect_valoracion, $sentencia);
        
        $data = mysqli_num_rows($query) > 0 ? mysqli_fetch_assoc($query) : array();

        return $data;
    }
}
