-- 001-notificaciones.sql
-- Base del módulo de Notificaciones (puntacana_admin).
-- Solo crea tablas nuevas y agrega filas a Rutas: no altera nada existente.
-- Ejecutar ANTES de subir los archivos de app/models/notificaciones, views/notificaciones,
-- api/notificaciones y cron_jobs/notificaciones_despacho.php.

USE puntacana_admin;

-- Configuración de cada tipo. `codigo` es el valor del enum
-- Notificaciones\Dominio\CodigoNotificacion. id_empresa = 0 aplica a todas las
-- empresas; una fila con el mismo código y una empresa concreta la sobreescribe.
CREATE TABLE Notificaciones_Tipos (
  id int(11) NOT NULL AUTO_INCREMENT,
  id_empresa int(11) NOT NULL DEFAULT 0,
  codigo varchar(80) NOT NULL,
  nombre varchar(150) NOT NULL,
  descripcion text NULL,
  clase varchar(10) NOT NULL DEFAULT 'aviso',
  dias_anticipacion int(11) NULL,
  recordar_cada_dias int(11) NULL,
  max_recordatorios int(11) NULL,
  notificar_autor tinyint(1) NOT NULL DEFAULT 0,
  canal_plataforma tinyint(1) NOT NULL DEFAULT 1,
  canal_correo tinyint(1) NOT NULL DEFAULT 0,
  modo_correo varchar(15) NOT NULL DEFAULT 'inmediato',
  correo_asunto varchar(200) NULL,
  correo_cuerpo text NULL,
  plataforma_titulo varchar(200) NOT NULL,
  plataforma_cuerpo varchar(500) NULL,
  icono varchar(50) NULL,
  color varchar(20) NULL,
  estado tinyint(1) NOT NULL DEFAULT 1,
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
  clase varchar(10) NOT NULL,
  titulo varchar(255) NOT NULL,
  cuerpo varchar(1000) NULL,
  url varchar(500) NULL,
  tipo_registro varchar(40) NULL,
  id_registro int(11) NULL,
  datos longtext NULL CHECK (datos IS NULL OR JSON_VALID(datos)),
  fecha_limite datetime NULL,
  estado varchar(12) NOT NULL DEFAULT 'abierta',
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
  estado varchar(25) NOT NULL,
  id_mensaje_proveedor varchar(150) NULL,
  error varchar(500) NULL,
  fecha_envio datetime NULL,
  created_at datetime NOT NULL,
  updated_at datetime NULL,
  PRIMARY KEY (id),
  KEY ix_envios_empleado (id_empleado),
  KEY ix_envios_destinatario (id_destinatario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Ajustes por empresa (id_empresa = 0 = valor por defecto).
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

-- modo: apagado | solo_plataforma | prueba | activo. Arranca apagado: el cron no envía nada.
-- lista_prueba: ids de Empleados (CSV) que reciben correo en modo prueba.
-- hora_resumen: hora local (0-23) del resumen diario. ventana_envio: horas locales permitidas.
INSERT INTO Notificaciones_Configuracion (id_empresa, clave, valor, created_at) VALUES
  (0, 'modo', 'apagado', NOW()),
  (0, 'lista_prueba', '', NOW()),
  (0, 'hora_resumen', '7', NOW()),
  (0, 'ventana_envio', '07-19', NOW());

-- Tipo de prueba: lo usa ?pg=notificaciones/prueba para validar el ciclo completo.
INSERT INTO Notificaciones_Tipos
  (id_empresa, codigo, nombre, descripcion, clase, canal_plataforma, canal_correo, modo_correo,
   correo_asunto, correo_cuerpo, plataforma_titulo, plataforma_cuerpo, icono, color, estado, created_at)
VALUES
  (0, 'general.prueba', 'Notificación de prueba',
   'Valida el módulo de punta a punta. Solo la genera un administrador para sí mismo.',
   'aviso', 1, 1, 'inmediato',
   'Prueba de notificaciones: {{titulo}}',
   '<p>Hola {{destinatario_nombre}},</p><p>{{mensaje}}</p><p><a href="{{enlace}}">Ver la notificación</a></p>',
   '{{titulo}}', '{{mensaje}}', 'bx-bell', '#0d6efd', 1, NOW());

-- Rutas de las pantallas nuevas (sin esto, index.php muestra sin_permisos).
-- editar/crear/eliminar/exportar en '' y no NULL: ValidarRuta() hace explode() sobre ellos.
INSERT INTO Rutas (id_empresa, ruta, roles, editar, crear, eliminar, exportar, modulo)
SELECT 1, 'notificaciones/bandeja', '1,2,3,4', '', '', '', '', NULL
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM Rutas WHERE ruta = 'notificaciones/bandeja');

INSERT INTO Rutas (id_empresa, ruta, roles, editar, crear, eliminar, exportar, modulo)
SELECT 1, 'notificaciones/detalle', '1,2,3,4', '', '', '', '', NULL
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM Rutas WHERE ruta = 'notificaciones/detalle');

INSERT INTO Rutas (id_empresa, ruta, roles, editar, crear, eliminar, exportar, modulo)
SELECT 1, 'notificaciones/prueba', '1', '', '', '', '', NULL
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM Rutas WHERE ruta = 'notificaciones/prueba');

-- ---------------------------------------------------------------------------
-- Reversión (descomentar para deshacer):
-- DELETE FROM Rutas WHERE ruta IN ('notificaciones/bandeja', 'notificaciones/detalle', 'notificaciones/prueba');
-- DROP TABLE Notificaciones_Envios;
-- DROP TABLE Notificaciones_Destinatarios;
-- DROP TABLE Notificaciones_Eventos;
-- DROP TABLE Notificaciones_Configuracion;
-- DROP TABLE Notificaciones_Tipos;
