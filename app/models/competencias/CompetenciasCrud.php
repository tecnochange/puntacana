<?php
class CompetenciasCrud
{

    public $hoy;

    public function __construct()
    {
        // Asignar los valores a propiedades de la clase para usarlos en cualquier método.
        $this->hoy = date("Y-m-d H:i:s");
    }

    public function Guardar_Competencia($id_empresa, $ciclo, $anio, $request)
    {
        global $connect_valoracion;

        $sentencia = 
        "INSERT INTO
            Competencias (id_empresa, anio, id_ciclo, nombre, definicion, id_tipo, created_at, update_at) 
		VALUES (
            '$id_empresa',
            '$anio',
            '$ciclo',
            '{$request["nombre"]}',
            '{$request["definicion"]}',
            '{$request["id_tipo"]}',
            '{$this->hoy}',
            '{$this->hoy}'
            )
        ";
        mysqli_query($connect_valoracion, $sentencia);

        /*
        echo "Competencia Guardada";
        $request["id_empresa"] = $id_empresa;
        $request["anio"] = $anio;
        dd($request);
        */
    }

    public function Editar_Competencia($request)
    {
        global $connect_valoracion;

        $sentencia =
        "UPDATE
            Competencias
        SET
            nombre = '{$request["nombre"]}',
            id_tipo = '{$request["id_tipo"]}',  
			definicion = '{$request["definicion"]}'
        WHERE
            id = '{$request["id_competencia"]}'
        ";
        mysqli_query($connect_valoracion, $sentencia);
        /*
        echo "Competencia Editada";
        dd($request);
        */
    }

    public function Guardar_Tipo_Competencia($id_empresa, $anio, $request)
    {
        global $connect_valoracion;

        $sentencia =
        "INSERT INTO
            Tipos (id_empresa, anio, nombre, created_at, update_at) 
		VALUES (
            '$id_empresa',
            '$anio',
            '{$request["nombre"]}',
            '{$this->hoy}',
            '{$this->hoy}'
            )
        ";
        mysqli_query($connect_valoracion, $sentencia);

        /*
        echo "Tipo de Competencia Guardada";
        $request["id_empresa"] = $id_empresa;
        $request["anio"] = $anio;
        dd($request);
        */
    }

    public function Editar_Tipo_Competencia($request)
    {
        global $connect_valoracion;

        $sentencia =
        "UPDATE
            Tipos
        SET
            nombre = '{$request["nombre"]}',
            update_at = '{$this->hoy}'
        WHERE
            id = '{$request["id_tipo"]}'
        ";
        mysqli_query($connect_valoracion, $sentencia);
        /*
        echo "Tipo de Competencia Editado";
        dd($request);
        */
    }

    public function Guardar_Nivel_Competencia($id_empresa, $anio, $request)
    {
        global $connect_valoracion;

        $sentencia =
        "INSERT INTO
            Niveles (id_empresa, anio, nombre, created_at, update_at) 
		VALUES (
            '$id_empresa',
            '$anio',
            '{$request["nombre"]}',
            '{$this->hoy}',
            '{$this->hoy}'
            )
        ";
        mysqli_query($connect_valoracion, $sentencia);

        /*
        echo "Nivel de Competencia Guardado";
        $request["id_empresa"] = $id_empresa;
        $request["anio"] = $anio;
        dd($request);
        */
    }

    public function Editar_Nivel_Competencia($request)
    {
        global $connect_valoracion;

        $sentencia =
        "UPDATE
            Niveles
        SET
            nombre = '{$request["nombre"]}',
            update_at = '{$this->hoy}'
        WHERE
            id = '{$request["id_nivel"]}'
        ";
        mysqli_query($connect_valoracion, $sentencia);

        /*
        echo "Nivel de Competencia Editado";
        dd($request);
        */
    }

    public function Editar_Informe_Competencia($request)
    {
        global $connect_valoracion;

        $sentencia =
        "UPDATE
            Competencias_Preguntas
        SET
            fortaleza = '{$request["nombre"]}',
            oportunidad = '{$request["oportunidad"]}',
            update_at = '{$this->hoy}'
        WHERE
            id = '{$request["id_informe"]}'
        ";
        //mysqli_query($connect_valoracion, $sentencia);

        echo "Editar Informe de Competencia";
        dd($request);
    }

    public function Guardar_Escala($id_empresa, $anio, $request)
    {
        global $connect_valoracion;

        $sentencia =
        "INSERT INTO Escalas (
            id_empresa,
            anio, 
            nombre_n_1, descripcion_n_1, 
            nombre_n_2, descripcion_n_2, 
            nombre_n_3, descripcion_n_3, 
            nombre_n_4, descripcion_n_4,
            nombre_n_5, descripcion_n_5, 
			nombre_n_6, descripcion_n_6,
            created_at
            ) 
		VALUES 
			( '$id_empresa', '$anio', 
            '" . $request["nombre_n_1"] . "', '" . $request["descripcion_n_1"] . "', 
            '" . $request["nombre_n_2"] . "', '" . $request["descripcion_n_2"] . "', 
            '" . $request["nombre_n_3"] . "', '" . $request["descripcion_n_3"] . "', 
            '" . $request["nombre_n_4"] . "', '" . $request["descripcion_n_4"] . "', 
            '" . $request["nombre_n_5"] . "', '" . $request["descripcion_n_5"] . "', 
			'" . $request["nombre_n_6"] . "', '" . $request["descripcion_n_6"] . "', 
            '" . $this->hoy . "'
            )
        ";
        mysqli_query($connect_valoracion, $sentencia);
        /*
        echo "Escala Guardada";
        $request["id_empresa"] = $id_empresa;
        $request["anio"] = $anio;
        dd($request);
        */
    }

    public function Editar_Escala($request)
    {
        global $connect_valoracion;

        $sentencia = 
        "UPDATE Escalas SET 
            nombre_n_1 = '{$request["nombre_n_1"]}', descripcion_n_1 = '{$request["descripcion_n_1"]}', 
            nombre_n_2 = '{$request["nombre_n_2"]}', descripcion_n_2 = '{$request["descripcion_n_2"]}', 
            nombre_n_3 = '{$request["nombre_n_3"]}', descripcion_n_3 = '{$request["descripcion_n_3"]}', 
            nombre_n_4 = '{$request["nombre_n_4"]}', descripcion_n_4 = '{$request["descripcion_n_4"]}', 
            nombre_n_5 = '{$request["nombre_n_5"]}', descripcion_n_5 = '{$request["descripcion_n_5"]}', 
			nombre_n_6 = '{$request["nombre_n_6"]}', descripcion_n_6 = '{$request["descripcion_n_6"]}'
        WHERE
            id = '{$request["id_registro"]}'
        ";
        mysqli_query($connect_valoracion, $sentencia);
        /*
        echo "Escala Editada";
        dd($request);
        */
    }

    public function Guardar_Escala_Interpretacion($id_empresa, $anio, $request)
    {
        global $connect_valoracion;

        $sentencia =
        "INSERT INTO
            Escalas_Interpretacion (
            id_empresa,
            anio, 
            nombre_n_1, descripcion_n_1, rango_1, 
            nombre_n_2, descripcion_n_2, rango_2,  
            nombre_n_3, descripcion_n_3, rango_3,  
            nombre_n_4, descripcion_n_4, rango_4, 
            created_at
            ) 
		VALUES 
			( '$id_empresa', '$anio', 
            '" . $request["nombre_n_1"] . "', '" . $request["descripcion_n_1"] . "', '" . $request["rango_1"] . "', 
            '" . $request["nombre_n_2"] . "', '" . $request["descripcion_n_2"] . "', '" . $request["rango_2"] . "', 
            '" . $request["nombre_n_3"] . "', '" . $request["descripcion_n_3"] . "', '" . $request["rango_3"] . "',  
            '" . $request["nombre_n_4"] . "', '" . $request["descripcion_n_4"] . "', '" . $request["rango_4"] . "',  
            '" . $this->hoy . "'
            )
            ";
        mysqli_query($connect_valoracion, $sentencia);
        /*
        echo "Escala Interpretacion Guardada";
        $request["id_empresa"] = $id_empresa;
        $request["anio"] = $anio;
        dd($request);
        */
    }

    public function Editar_Escala_Interpretacion($request)
    {
        global $connect_valoracion;

        $sentencia = 
        "UPDATE Escalas_Interpretacion
        SET 
            nombre_n_1 = '" . $request["nombre_n_1"] . "', descripcion_n_1 = '" . $request["descripcion_n_1"] . "', rango_1 = '" . $request["rango_1"] . "',  
            nombre_n_2 = '" . $request["nombre_n_2"] . "', descripcion_n_2 = '" . $request["descripcion_n_2"] . "', rango_2 = '" . $request["rango_2"] . "', 
            nombre_n_3 = '" . $request["nombre_n_3"] . "', descripcion_n_3 = '" . $request["descripcion_n_3"] . "', rango_3 = '" . $request["rango_3"] . "', 
            nombre_n_4 = '" . $request["nombre_n_4"] . "', descripcion_n_4 = '" . $request["descripcion_n_4"] . "', rango_4 = '" . $request["rango_4"] . "'   
        WHERE
            id = '" . $_POST["id_registro"] . "'
        ";
        mysqli_query($connect_valoracion, $sentencia);
        /*
        echo "Escala Interpretacion Editada";
        dd($request);
        */
    }
}
