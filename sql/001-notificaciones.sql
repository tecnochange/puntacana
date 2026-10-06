-- 001-notificaciones.sql
-- Base del módulo de Notificaciones (puntacana_admin).
-- Solo crea tablas nuevas y agrega filas a Rutas: no altera nada existente.
-- Ejecutar ANTES de subir los archivos de app/models/notificaciones, views/notificaciones,
-- api/notificaciones y cron_jobs/notificaciones_despacho.php.
--
-- Valores enumerados en MAYÚSCULAS_CON_GUION_BAJO, iguales a las constantes de
-- app/models/notificaciones/Dominio. Charset utf8, igual que las conexiones de connect.php.

USE puntacana_admin;

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
  dias_anticipacion int(11) NULL,
  recordar_cada_dias int(11) NULL,
  max_recordatorios int(11) NULL,
  notificar_autor enum('SI','NO') NOT NULL DEFAULT 'NO',
  canal_plataforma enum('SI','NO') NOT NULL DEFAULT 'SI',
  canal_correo enum('SI','NO') NOT NULL DEFAULT 'NO',
  modo_correo enum('INMEDIATO','RESUMEN_DIARIO') NOT NULL DEFAULT 'INMEDIATO',
  correo_asunto varchar(200) NULL,
  correo_cuerpo text NULL,
  plataforma_titulo varchar(200) NOT NULL,
  plataforma_cuerpo varchar(500) NULL,
  icono varchar(50) NULL,
  color varchar(20) NULL,
  estado enum('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO',
  created_at datetime NOT NULL,
  updated_at datetime NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_tipos_empresa_codigo (id_empresa, codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Cada notificación generada. Nunca se borra: se completa, vence o cancela.
-- clase, titulo y cuerpo quedan fijos al crearla aunque luego cambie el tipo.
CREATE TABLE Notificaciones_Eventos (
  id int(11) NOT NULL AUTO_INCREMENT,
  id_empresa int(11) NOT NULL,
  id_tipo int(11) NOT NULL,
  codigo varchar(80) NOT NULL,
  clase enum('TAREA','AVISO') NOT NULL,
  titulo varchar(255) NOT NULL,
  cuerpo varchar(1000) NULL,
  url varchar(500) NULL,
  tipo_registro varchar(40) NULL,
  id_registro int(11) NULL,
  datos longtext NULL CHECK (datos IS NULL OR JSON_VALID(datos)),
  fecha_limite datetime NULL,
  estado enum('ABIERTA','COMPLETADA','VENCIDA','CANCELADA') NOT NULL DEFAULT 'ABIERTA',
  id_autor int(11) NULL,
  clave_unica varchar(150) NULL,
  fecha_cierre datetime NULL,
  created_at datetime NOT NULL,
  updated_at datetime NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_eventos_empresa_clave (id_empresa, clave_unica),
  KEY ix_eventos_empresa_estado_limite (id_empresa, estado, fecha_limite),
  KEY ix_eventos_registro (tipo_registro, id_registro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- La bandeja: un renglón por persona y notificación.
-- proximo_aviso es cuándo toca el siguiente correo (NULL = ninguno pendiente).
CREATE TABLE Notificaciones_Destinatarios (
  id int(11) NOT NULL AUTO_INCREMENT,
  id_empresa int(11) NOT NULL,
  id_evento int(11) NOT NULL,
  id_empleado int(11) NOT NULL,
  fecha_lectura datetime NULL,
  proximo_aviso datetime NULL,
  cantidad_avisos int(11) NOT NULL DEFAULT 0,
  ultimo_aviso datetime NULL,
  created_at datetime NOT NULL,
  updated_at datetime NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_destinatarios_evento_empleado (id_evento, id_empleado),
  KEY ix_destinatarios_empleado_lectura (id_empleado, fecha_lectura),
  KEY ix_destinatarios_proximo_aviso (proximo_aviso)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Bitácora de correos. No guarda la dirección ni el cuerpo del correo.
CREATE TABLE Notificaciones_Envios (
  id int(11) NOT NULL AUTO_INCREMENT,
  id_empresa int(11) NOT NULL,
  id_destinatario int(11) NULL,
  id_empleado int(11) NOT NULL,
  asunto varchar(200) NULL,
  estado enum('ENVIADO','FALLIDO','OMITIDO_MODO','OMITIDO_INACTIVO','OMITIDO_SIN_CORREO','OMITIDO_CADUCADO','OMITIDO_TIPO') NOT NULL,
  id_mensaje_proveedor varchar(150) NULL,
  error varchar(500) NULL,
  fecha_envio datetime NULL,
  created_at datetime NOT NULL,
  updated_at datetime NULL,
  PRIMARY KEY (id),
  KEY ix_envios_empleado (id_empleado),
  KEY ix_envios_destinatario (id_destinatario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Ajustes por empresa (id_empresa = 0 = valor por defecto). Es clave/valor, por
-- eso `valor` es texto; los valores válidos de `modo` están en ModoOperacion.
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

-- modo: APAGADO | SOLO_PLATAFORMA | PRUEBA | ACTIVO. Arranca APAGADO: el cron no envía nada.
-- lista_prueba: ids de Empleados (CSV) que reciben correo en modo PRUEBA.
-- hora_resumen: hora local (0-23) del resumen diario. ventana_envio: horas locales permitidas.
INSERT INTO Notificaciones_Configuracion (id_empresa, clave, valor, created_at) VALUES
  (0, 'modo', 'APAGADO', NOW()),
  (0, 'lista_prueba', '', NOW()),
  (0, 'hora_resumen', '7', NOW()),
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
-- Reversión (descomentar para deshacer):
-- DELETE FROM Rutas WHERE ruta IN ('notificaciones/bandeja', 'notificaciones/detalle');
-- DROP TABLE Notificaciones_Envios;
-- DROP TABLE Notificaciones_Destinatarios;
-- DROP TABLE Notificaciones_Eventos;
-- DROP TABLE Notificaciones_Configuracion;
-- DROP TABLE Notificaciones_Tipos;
