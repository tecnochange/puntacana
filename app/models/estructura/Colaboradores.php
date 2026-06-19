<?php

class Colaboradores {

    public function colaboradores_lista($request, $connect_admin){

        global $connect_admin;

        //COLABORADORES
        $ARRAY_COLABORADORES = [];
        $sentencia_col = "
            SELECT
                Empleados.id AS id,
                Empleados.nombre AS nombre,
                Empleados.documento AS documento, 
                Empleados.correo AS correo,
                Empleados.role AS role,
                Empleados.estado AS estado, 
                Empleados.compania AS compania, 
                Empleados.verificar AS verificar,
                Roles.nombre AS nombre_rol,
                
                Cargos.id AS id_cargo,
                Cargos.nombre AS nombre_cargo,
                Areas.nombre AS nombre_area,
                Areas.id AS id_area, 
                Vicepresidencia.nombre AS nombre_vicepresidencia, 
                Vicepresidencia.id AS id_vicepresidencia,
                Nivel_Jerarquico.nombre AS nombre_nivel_jerarquico, 
                Nivel_Jerarquico.id AS id_nivel_jerarquico, 
                Estructura_Empresa.unidad_organizativa AS nombre_unidad, 
                Estructura_Empresa.id AS id_unidad  
            FROM
                Empleados
            LEFT JOIN Cargos ON Cargos.id = Empleados.id_cargo
            LEFT JOIN Areas ON Areas.id = Empleados.area
            LEFT JOIN Vicepresidencia ON Vicepresidencia.id = Empleados.unidad_corporativa
            LEFT JOIN Nivel_Jerarquico ON Nivel_Jerarquico.id = Empleados.nivel_jerarquico 
            LEFT JOIN Estructura_Empresa ON Estructura_Empresa.id = Empleados.unidad_organizativa
            LEFT JOIN Roles ON Roles.id = Empleados.role 
            WHERE
                Empleados.id > 0 AND Empleados.id_empresa = '".$_SESSION["id_empresa"]."' 
            ORDER BY
                Empleados.nombre ASC 
        ";
        $queryColaborador = mysqli_query($connect_admin, $sentencia_col);
        while ($dataColaborador = mysqli_fetch_array($queryColaborador)){

            $dataColaborador["txt_estado"] = '<i class="bi bi-person-check-fill text-success fs-4" title="Activo"></i>';
            if($dataColaborador["estado"] == 2){
                $dataColaborador["txt_estado"] = '<i class="bi bi-person-x-fill text-danger" title="inactivo"></i>';
            }

            $dataColaborador["txt_verificado"] = 'Verificado';
            if($dataColaborador["verificar"] == 2){
                $dataColaborador["txt_verificado"] = 'Pendiente';
            }
            if($dataColaborador["verificar"] == 3){
                $dataColaborador["txt_verificado"] = 'No Verificado';
            }


            $ARRAY_COLABORADORES[$dataColaborador["id"]] = $dataColaborador;
        }

        return $ARRAY_COLABORADORES;
    }

    public function colaborador($id){ 

        global $connect_admin;

        $sentencia_col = "
            SELECT
                Empleados.id AS id,
                Empleados.nombre AS nombre,
                Empleados.documento AS documento, 
                Empleados.estado AS estado, 
                Empleados.correo AS correo, 
                Empleados.foto AS foto,
                Cargos.id AS id_cargo,
                Cargos.nombre AS nombre_cargo,
                Areas.nombre AS nombre_area,
                Areas.id AS id_area, 
                Vicepresidencia.nombre AS nombre_vicepresidencia, 
                Vicepresidencia.id AS id_vicepresidencia,
                Nivel_Jerarquico.nombre AS nombre_nivel_jerarquico, 
                Nivel_Jerarquico.id AS id_nivel_jerarquico, 
                Estructura_Empresa.unidad_organizativa AS nombre_unidad, 
                Estructura_Empresa.id AS id_unidad  
            FROM
                Empleados
            LEFT JOIN Cargos ON Cargos.id = Empleados.id_cargo
            LEFT JOIN Areas ON Areas.id = Empleados.area
            LEFT JOIN Vicepresidencia ON Vicepresidencia.id = Empleados.unidad_corporativa
            LEFT JOIN Nivel_Jerarquico ON Nivel_Jerarquico.id = Empleados.nivel_jerarquico 
            LEFT JOIN Estructura_Empresa ON Estructura_Empresa.id = Empleados.unidad_organizativa 
            WHERE
                Empleados.id = '".$id."' AND Empleados.id_empresa = '".$_SESSION["id_empresa"]."' 
            ORDER BY
                Empleados.nombre ASC 
        ";
        $queryColaborador = mysqli_query($connect_admin, $sentencia_col);
        $dataColaborador = mysqli_fetch_array($queryColaborador);

        if($queryColaborador->num_rows == 0){
            $dataColaborador = NULL;
        }

        return $dataColaborador;
    }


    public function equipo( $id_empleado ){ 

        global $connect_admin;
        $array_equipo = array();

        //EQUIPO DE TRABAJO
        $sentencia = "
        SELECT
            Empleados.id AS id_empleado,
            Lideres.id,
            Empleados.documento,
            Empleados.nombre AS nombre,
            Cargos.nombre AS nombre_cargo,
            Areas.nombre AS nombre_area
        FROM
            Lideres
        INNER JOIN Empleados ON Empleados.id = Lideres.id_empleado
        LEFT JOIN Posiciones ON Posiciones.id = Empleados.id_posicion
        LEFT JOIN Cargos ON Cargos.id = Empleados.id_cargo
        LEFT JOIN Areas ON Areas.id = Empleados.area
        WHERE
            Lideres.id_jefe = '".$id_empleado."' AND Empleados.estado = 1 AND Lideres.id_empresa = ".$_SESSION["id_empresa"]."
        ";
        $queryJefes = mysqli_query($connect_admin, $sentencia);
        while ($dataJefes = mysqli_fetch_array($queryJefes)) {
            array_push( $array_equipo, $dataJefes );
        }

        return $array_equipo;
    }

    public function Lista_Cargos($id_empresa)
    {
        global $connect_admin;
        $array = array();

        $sentencia = "SELECT * FROM Cargos WHERE id_empresa = '$id_empresa' AND estado = 1 ORDER BY nombre ASC";
        $query = mysqli_query($connect_admin, $sentencia);
        if(mysqli_num_rows($query) > 0){
            while ($data = mysqli_fetch_assoc($query)) {
                array_push( $array, $data );
            }
        }

        return $array;
    }

    public function Lista_Vicepresidencias($id_empresa)
    {
        global $connect_admin;
        $array = array();

        $sentencia = "SELECT * FROM Vicepresidencia WHERE id_empresa = '$id_empresa' AND estado = 1 ORDER BY nombre ASC";
        $query = mysqli_query($connect_admin, $sentencia);
        if(mysqli_num_rows($query) > 0){
            while ($data = mysqli_fetch_assoc($query)) {
                array_push( $array, $data );
            }
        }

        return $array;
    }

    public function Obtener_Vicepresidencia_de_Area($id_empresa, $id_area)
    {
        global $connect_admin;

        $sentencia = "SELECT vicepresidencia FROM Estructura_Empresa WHERE id_empresa = '$id_empresa' AND area = '$id_area' AND estado = 1 LIMIT 1";
        $query = mysqli_query($connect_admin, $sentencia);
        $data = mysqli_num_rows($query) > 0 ? mysqli_fetch_assoc($query) : array();

        return $data;
    }

    public function Lista_Areas($id_empresa)
    {
        global $connect_admin;
        $array = array();

        $sentencia = "SELECT * FROM Areas WHERE id_empresa = '$id_empresa' AND estado = 1 ORDER BY nombre ASC";
        $query = mysqli_query($connect_admin, $sentencia);
        if(mysqli_num_rows($query) > 0){
            while ($data = mysqli_fetch_assoc($query)) {
                array_push( $array, $data );
            }
        }

        return $array;
    }

    public function Lista_Unidades_Organizativas($id_empresa)
    {
        global $connect_admin;
        $array = array();

        $sentencia = "SELECT * FROM Estructura_Empresa WHERE id_empresa = '$id_empresa' AND estado = 1 GROUP BY unidad_organizativa ORDER BY unidad_organizativa ASC";
        $query = mysqli_query($connect_admin, $sentencia);
        if(mysqli_num_rows($query) > 0){
            while ($data = mysqli_fetch_assoc($query)) {
                if(!empty($data["unidad_organizativa"])){
                    array_push( $array, $data );
                }
            }
        }

        return $array;
    }

    public function Lista_Jerarquia($id_empresa)
    {
        global $connect_admin;
        $array = array();

        $sentencia = "SELECT * FROM Nivel_Jerarquico WHERE id_empresa = '$id_empresa' AND estado = 1 ORDER BY nombre ASC";
        $query = mysqli_query($connect_admin, $sentencia);
        if(mysqli_num_rows($query) > 0){
            while ($data = mysqli_fetch_assoc($query)) {
                array_push( $array, $data );
            }
        }

        return $array;
    }
    
    public function Guardar_Colaborador($request, $files, $id_empresa){
        global $connect_admin;
        $hoy = date("Y-m-d H:i:s");

        //***ALMACENAR IMAGEN***//
        //Carpeta destino
        $carpeta = "/var/www/html/goforagile.com/recursos/";
        $archivo = $files["foto"];
        $nombre_archivo = uniqid() . "_" . basename($archivo["name"]);
        $tmp = $archivo["tmp_name"];
        $ruta = $carpeta . $nombre_archivo;

        if (!move_uploaded_file($tmp, $ruta)) {
            echo json_encode(["ok" => false, "error" => "No se pudo guardar el archivo"]);
        }
        //***FIN ALMACENAR IMAGEN***//

        $sentencia = "
        INSERT INTO Empleados(
            id_empresa,
            documento,
            nombre,
            genero,
            fecha_ingreso,
            antiguedad_anios,
            antiguedad_meses,
            antiguedad_dias,
            id_cargo,
            cargo,
            correo,
            correo_personal,
            telefono_movil,
            telefono_fijo,
            nivel_jerarquico,
            nivel_general,
            compania,
            sucursal,
            unidad_corporativa,
            gerencia,
            unidad_estrategica,
            area,
            unidad_organizativa,
            nombre_jefe,
            cargo_jefe,
            role,
            estado,
            password,
            cambio_pass,
            fecha_cambio_pass,
            contrasena,
            foto,
            foto_webp,
            id_posicion,
            pais,
            verificar,
            force_password_reset,
            created_at,
            updated_at,
            actualizado_en
        )
        VALUES(
            '".$id_empresa."',
            '".$request["documento"]."', 
            '".$request["nombre"]."', 
            '".$request["genero"]."', 
            '".$request["fecha_ingreso"]."', 
            '".$request["antiguedad_anios"]."', 
            '".$request["antiguedad_meses"]."', 
            '".$request["antiguedad_dias"]."', 
            '".$request["id_cargo"]."', 
            '".$request["cargo"]."', 
            '".$request["correo"]."', 
            '".$request["correo_personal"]."', 
            '".$request["telefono_movil"]."', 
            '".$request["telefono_fijo"]."', 
            '".$request["nivel_jerarquico"]."', 
            '".$request["nivel_general"]."', 
            '".$request["compania"]."', 
            '".$request["sucursal"]."', 
            '".$request["unidad_corporativa"]."', 
            '".$request["gerencia"]."', 
            '".$request["unidad_estrategica"]."', 
            '".$request["area"]."', 
            '".$request["unidad_organizativa"]."', 
            '".$request["nombre_jefe"]."', 
            '".$request["cargo_jefe"]."', 
            '".implode(",", $request["role"])."',
            '".$request["estado"]."', 
            '".$request["password"]."', 
            '".$request["cambio_pass"]."', 
            '".$request["fecha_cambio_pass"]."', 
            '".$request["contrasena"]."', 
            '".$nombre_archivo."', 
            '".$request["foto_webp"]."', 
            '".$request["id_posicion"]."', 
            '".$request["pais,"]."', 
            '".$request["verificar"]."', 
            '1', 
            '".$hoy."', 
            '".$hoy."', 
            '".$hoy."'
        )
        ";

        //echo $sentencia;
        mysqli_query($connect_admin, $sentencia);


        
        //echo "Colaborador Guardado";
        //dd($request);

        //MYSQL
    }

    public function Editar_Colaborador($request, $files, $id_colaborador)
    {
        global $connect_admin;
        $hoy = date("Y-m-d H:i:s");

        //***ALMACENAR IMAGEN***//
        //Carpeta destino
        $carpeta = "/var/www/html/goforagile.com/recursos/";
        $archivo = $files["foto"];

        if(!empty($archivo["name"])){
            $nombre_archivo = uniqid() . "_" . basename($archivo["name"]);
            $tmp = $archivo["tmp_name"];
            $ruta = $carpeta . $nombre_archivo;

            if (!move_uploaded_file($tmp, $ruta)) {
                echo json_encode(["ok" => false, "error" => "No se pudo guardar el archivo"]);
            }
        }
        //***FIN ALMACENAR IMAGEN***//

        $sentencia = "
        UPDATE
            Empleados 
        SET
            documento = '".$request["documento"]."', 
            nombre = '".$request["nombre"]."', 
            genero = '".$request["genero"]."', 
            fecha_ingreso = '".$request["fecha_ingreso"]."', 
            antiguedad_anios = '".$request["antiguedad_anios"]."', 
            antiguedad_meses = '".$request["antiguedad_meses"]."', 
            antiguedad_dias = '".$request["antiguedad_dias"]."', 
            id_cargo = '".$request["id_cargo"]."', 
            cargo = '".$request["cargo"]."', 
            correo = '".$request["correo"]."', 
            correo_personal = '".$request["correo_personal"]."', 
            telefono_movil = '".$request["telefono_movil"]."', 
            telefono_fijo = '".$request["telefono_fijo"]."', 
            nivel_jerarquico = '".$request["nivel_jerarquico"]."', 
            nivel_general = '".$request["nivel_general"]."', 
            compania = '".$request["compania"]."', 
            sucursal = '".$request["sucursal"]."', 
            unidad_corporativa = '".$request["unidad_corporativa"]."', 
            gerencia = '".$request["gerencia"]."', 
            unidad_estrategica = '".$request["unidad_estrategica"]."', 
            area = '".$request["area"]."', 
            unidad_organizativa = '".$request["unidad_organizativa"]."', 
            role = '".implode(",", $request["role"])."',
            estado = '".$request["estado"]."', 
            password = '".$request["password"]."', 
            contrasena = '".$request["contrasena"]."', 
            foto = '".$nombre_archivo."',  
            id_posicion = '".$request["id_posicion"]."', 
            updated_at = '".$hoy."',
            actualizado_en = '".$hoy."'
        WHERE
            id = '".$id_colaborador."'
        ";
        mysqli_query($connect_admin, $sentencia);
        //dd($request);
    }
}


?>