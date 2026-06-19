<?php
class Users {
	
	//DATOS DEL USUARIO
    public function user($id, $connect_admin){
        global $recursos_publico;

        $sentencia_col = "
            SELECT
                Empleados.id AS id, 
                Empleados.id_empresa AS id_empresa,
                Empleados.nombre AS nombre,
                Empleados.documento AS documento, 
                Empleados.correo AS correo,
                Empleados.role AS role,
                Empleados.estado AS estado, 
                Empleados.compania AS compania, 
                Empleados.verificar AS verificar, 
                Empleados.renovar_codigo AS renovar_codigo,
                Empleados.foto AS foto, 
                
                Cargos.id AS id_cargo,
                Cargos.nombre AS cargo,
                Areas.nombre AS area,
                Areas.id AS id_area, 
                Vicepresidencia.nombre AS vicepresidencia, 
                Vicepresidencia.id AS id_vicepresidencia,
                Nivel_Jerarquico.nombre AS nivel_jerarquico, 
                Nivel_Jerarquico.id AS id_nivel_jerarquico, 
                Estructura_Empresa.unidad_organizativa AS unidad_organizativa, 
                Estructura_Empresa.id AS id_unidad, 
                Empresas.skin AS skin, 
                Empresas.skin_empresa AS skin_empresa, 
                Empresas.logo AS logo  

            FROM
                Empleados
            LEFT JOIN Cargos ON Cargos.id = Empleados.id_cargo
            LEFT JOIN Areas ON Areas.id = Empleados.area
            LEFT JOIN Vicepresidencia ON Vicepresidencia.id = Empleados.unidad_corporativa
            LEFT JOIN Nivel_Jerarquico ON Nivel_Jerarquico.id = Empleados.nivel_jerarquico 
            LEFT JOIN Estructura_Empresa ON Estructura_Empresa.id = Empleados.unidad_organizativa 
            LEFT JOIN Empresas ON Empresas.id = Empleados.id_empresa 
            WHERE
                Empleados.id = '".$id."' AND Empleados.id_empresa = '".$_SESSION["id_empresa"]."' 
            ORDER BY
                Empleados.nombre ASC 
        ";
        $qry = mysqli_query($connect_admin, $sentencia_col );
        $dt = mysqli_fetch_array($qry);


        $photo = $recursos_publico.$dt["foto"];
        if($dt["foto"] == ""){$photo = $recursos_publico."img_default.jpg"; }

        return array(
            "id" => $dt["id"], 
			"id_empresa" => $dt["id_empresa"],
            "id_position" => $dt["id_posicion"],
			"id_cargo" => $dt["id_cargo"],
            "id_area_macro" => $dt["id_vicepresidencia"],
			"id_area" => $dt["id_area"],
			"id_gerencia" => $dt["id_departamento"],
            "nombre" => $dt["nombre"].' '.$dt["nombre_2"], 
			"apellidos" => $dt["apellidos"].' '.$dt["apellidos_2"], 
            "correo" => $dt["correo_corporativo"], 
			"documento" => $dt["documento"], 
			"role" => $dt["role"],
			"foto" => $photo, 
			"cargo" => $dt["cargo"],
			"txt_role" => $txt_role,
			"cambio_pass" => $dt["cambio_pass"],
			"role_seleccion" => $dt["role_seleccion"],
			"role_formacion" => $dt["role_formacion"], 
            "modulos" => $dt["modulos"], 
            "logo" => $dt["logo"], 
            "nombre_empresa" => $dt["nombre_empresa"],  
            "skin" => $dt["skin"],  
            "skin_empresa" => $dt["skin_empresa"],  
            "renovar_codigo" => $dt["renovar_codigo"], 
            "estado" => $dt["estado"],   
             
        );
        
    }
    
}

?>