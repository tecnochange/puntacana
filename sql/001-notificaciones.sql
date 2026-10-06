-- 001-notificaciones.sql
-- Base del módulo de Notificaciones (puntacana_admin).
-- Ejecutar ANTES de subir los archivos de app/models/notificaciones, views/notificaciones,
-- api/notificaciones y cron_jobs/notificaciones_despacho.php.
--
-- Convenciones:
--   - Enumeraciones como ENUM, valores en MAYÚSCULAS_CON_GUION_BAJO (iguales a las
--     constantes de app/models/notificaciones/Dominio). Lo que es sí/no, BOOLEAN.
--   - Fechas de negocio: fecha_<hecho>. Llaves foráneas: id_<entidad>[_<rol>].
--   - Charset utf8, igual que las conexiones de app/connect.php.

USE puntacana_admin;

-- La tabla Notificaciones que ya existía está vacía (0 filas, auto_increment en 1,
-- nunca actualizada) y ningún archivo, vista, procedimiento ni trigger la usa.
-- Se renombra en vez de borrarla para que el cambio sea reversible.
RENAME TABLE Notificaciones TO Obsoleta_Notificaciones;

-- Configuración de cada tipo. `codigo` es el valor de una constante de
-- Notificaciones\Dominio\CodigoNotificacion (p. ej. 'OKRS_KR_ASIGNACION').
-- id_empresa = 0 aplica a todas las empresas; una fila con el mismo código y una
-- empresa concreta la sobreescribe (también para desactivarlo solo en esa empresa).
CREATE TABLE Notificaciones_Tipos (
  id int(11) NOT NULL AUTO_INCREMENT,
  id_empresa int(11) NOT NULL DEFAULT 0,
  codigo varchar(80) NOT NULL,
  nombre varchar(150) NOT NULL,
  descripcion text NULL,
  clase enum('TAREA','AVISO') NOT NULL DEFAULT 'AVISO',
  dias_antes_de_vencer int(11) NULL,
  dias_entre_recordatorios int(11) NULL,
  maximo_recordatorios int(11) NULL,
  -- Si el autor viene entre los destinatarios, también la recibe (si no, se le excluye).
  incluye_autor boolean NOT NULL DEFAULT FALSE,
  muestra_en_bandeja boolean NOT NULL DEFAULT TRUE,
  envia_correo boolean NOT NULL DEFAULT FALSE,
  -- Ambos NULL: un correo por notificación, de inmediato. Ambos con valor: se
  -- agrupan en un resumen cada <intervalo_resumen> <unidad_intervalo_resumen>.
  intervalo_resumen int(11) NULL,
  unidad_intervalo_resumen enum('HORAS','DIAS','SEMANAS') NULL,
  plantilla_titulo varchar(200) NOT NULL,
  plantilla_mensaje varchar(500) NULL,
  plantilla_asunto_correo varchar(200) NULL,
  plantilla_cuerpo_correo text NULL,
  icono varchar(50) NULL,
  color varchar(20) NULL,
  estado enum('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO',
  created_at datetime NOT NULL,
  updated_at datetime NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_tipos_empresa_codigo (id_empresa, codigo),
  CONSTRAINT ck_tipos_intervalo_resumen CHECK (
    (intervalo_resumen IS NULL AND unidad_intervalo_resumen IS NULL)
    OR (intervalo_resumen > 0 AND unidad_intervalo_resumen IS NOT NULL)
  )
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Cada notificación generada. Nunca se borra: se completa, vence o cancela.
-- clase, titulo y mensaje quedan fijos al crearla aunque luego cambie el tipo.
-- clave_evento identifica el hecho de negocio que la originó: una segunda llamada
-- con la misma clave devuelve la notificación existente en vez de duplicarla.
CREATE TABLE Notificaciones (
  id int(11) NOT NULL AUTO_INCREMENT,
  id_empresa int(11) NOT NULL,
  id_tipo int(11) NOT NULL,
  clase enum('TAREA','AVISO') NOT NULL,
  titulo varchar(255) NOT NULL,
  mensaje varchar(1000) NULL,
  url varchar(500) NULL,
  tipo_registro varchar(40) NULL,
  id_registro int(11) NULL,
  datos_plantilla longtext NULL CHECK (datos_plantilla IS NULL OR JSON_VALID(datos_plantilla)),
  fecha_vencimiento datetime NULL,
  estado enum('ABIERTA','COMPLETADA','VENCIDA','CANCELADA') NOT NULL DEFAULT 'ABIERTA',
  id_empleado_autor int(11) NULL,
  clave_evento varchar(150) NULL,
  fecha_cierre datetime NULL,
  created_at datetime NOT NULL,
  updated_at datetime NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_notificaciones_empresa_clave_evento (id_empresa, clave_evento),
  KEY ix_notificaciones_empresa_estado_vencimiento (id_empresa, estado, fecha_vencimiento),
  KEY ix_notificaciones_registro (tipo_registro, id_registro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Una fila por notificación y empleado: su bandeja y la programación de sus correos.
-- fecha_proximo_correo NULL = no hay correo pendiente.
-- cantidad_intentos_correo cuenta envíos, fallos y omisiones (el detalle está en Notificaciones_Envios).
CREATE TABLE Notificaciones_Destinatarios (
  id int(11) NOT NULL AUTO_INCREMENT,
  id_empresa int(11) NOT NULL,
  id_notificacion int(11) NOT NULL,
  id_empleado int(11) NOT NULL,
  fecha_lectura datetime NULL,
  fecha_proximo_correo datetime NULL,
  cantidad_intentos_correo int(11) NOT NULL DEFAULT 0,
  fecha_ultimo_intento_correo datetime NULL,
  created_at datetime NOT NULL,
  updated_at datetime NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_destinatarios_notificacion_empleado (id_notificacion, id_empleado),
  KEY ix_destinatarios_empleado_lectura (id_empleado, fecha_lectura),
  KEY ix_destinatarios_proximo_correo (fecha_proximo_correo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Bitácora de correos. No guarda la dirección ni el cuerpo del correo.
-- id_destinatario apunta a Notificaciones_Destinatarios.id (no a un empleado).
CREATE TABLE Notificaciones_Envios (
  id int(11) NOT NULL AUTO_INCREMENT,
  id_empresa int(11) NOT NULL,
  id_destinatario int(11) NOT NULL,
  id_empleado int(11) NOT NULL,
  asunto varchar(200) NULL,
  resultado enum('ENVIADO','FALLIDO','OMITIDO_MODO','OMITIDO_EMPLEADO_INACTIVO','OMITIDO_SIN_CORREO','OMITIDO_CADUCADO','OMITIDO_TIPO_INACTIVO') NOT NULL,
  id_mensaje_proveedor varchar(150) NULL,
  mensaje_error varchar(500) NULL,
  fecha_envio datetime NULL,
  created_at datetime NOT NULL,
  updated_at datetime NULL,
  PRIMARY KEY (id),
  KEY ix_envios_empleado (id_empleado),
  KEY ix_envios_destinatario (id_destinatario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Ajustes por empresa (id_empresa = 0 = valor por defecto). Claves en snake_case;
-- los valores enumerados en MAYÚSCULAS.
CREATE TABLE Notificaciones_Configuracion (
  id int(11) NOT NULL AUTO_INCREMENT,
  id_empresa int(11) NOT NULL DEFAULT 0,
  clave varchar(50) NOT NULL,
  valor varchar(500) NULL,
  created_at datetime NOT NULL,
  updated_at datetime NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_configuracion_empresa_clave (id_empresa, clave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- modo_operacion: APAGADO | SOLO_BANDEJA | PRUEBA | ACTIVO. Arranca APAGADO: el cron no envía nada.
-- ids_empleados_prueba: ids de Empleados (CSV) que reciben correo en modo PRUEBA.
-- hora_resumen: hora local (0-23) de los resúmenes por DIAS y SEMANAS.
-- dia_semana_resumen: 1-7 (ISO, 1 = lunes) de los resúmenes por SEMANAS.
-- ventana_envio: horas locales en que se envían correos ('07-19' = de 07:00 a 18:59).
INSERT INTO Notificaciones_Configuracion (id_empresa, clave, valor, created_at) VALUES
  (0, 'modo_operacion', 'APAGADO', NOW()),
  (0, 'ids_empleados_prueba', '', NOW()),
  (0, 'hora_resumen', '7', NOW()),
  (0, 'dia_semana_resumen', '1', NOW()),
  (0, 'ventana_envio', '07-19', NOW());

-- Rutas de las pantallas nuevas (sin esto, index.php muestra sin_permisos).
-- editar/crear/eliminar/exportar en '' y no NULL: ValidarRuta() hace explode() sobre ellos.
INSERT INTO Rutas (id_empresa, ruta, roles, editar, crear, eliminar, exportar, modulo)
SELECT 1, 'notificaciones/bandeja', '1,2,3,4', '', '', '', '', NULL
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM Rutas WHERE ruta = 'notificaciones/bandeja');

INSERT INTO Rutas (id_empresa, ruta, roles, editar, crear, eliminar, exportar, modulo)
SELECT 1, 'notificaciones/detalle', '1,2,3,4', '', '', '', '', NULL
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM Rutas WHERE ruta = 'notificaciones/detalle');

-- ---------------------------------------------------------------------------
-- Reversión (descomentar para deshacer, en este orden):
-- DELETE FROM Rutas WHERE ruta IN ('notificaciones/bandeja', 'notificaciones/detalle');
-- DROP TABLE Notificaciones_Envios;
-- DROP TABLE Notificaciones_Destinatarios;
-- DROP TABLE Notificaciones;
-- DROP TABLE Notificaciones_Configuracion;
-- DROP TABLE Notificaciones_Tipos;
-- RENAME TABLE Obsoleta_Notificaciones TO Notificaciones;
