-- 002-notificaciones-administracion.sql
-- Pantallas de administración del módulo de Notificaciones y primer tipo real.
-- Requiere 001-notificaciones.sql. Ejecutar ANTES de subir views/notificaciones/administrar
-- y api/notificaciones/administrar.

USE puntacana_admin;

-- Rutas de administración: solo rol 1 (Administrador).
INSERT INTO Rutas (id_empresa, ruta, roles, editar, crear, eliminar, exportar, modulo)
SELECT 1, 'notificaciones/administrar/tipos', '1', '', '', '', '', NULL
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM Rutas WHERE ruta = 'notificaciones/administrar/tipos');

INSERT INTO Rutas (id_empresa, ruta, roles, editar, crear, eliminar, exportar, modulo)
SELECT 1, 'notificaciones/administrar/tipo', '1', '', '', '', '', NULL
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM Rutas WHERE ruta = 'notificaciones/administrar/tipo');

INSERT INTO Rutas (id_empresa, ruta, roles, editar, crear, eliminar, exportar, modulo)
SELECT 1, 'notificaciones/administrar/configuracion', '1', '', '', '', '', NULL
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM Rutas WHERE ruta = 'notificaciones/administrar/configuracion');

INSERT INTO Rutas (id_empresa, ruta, roles, editar, crear, eliminar, exportar, modulo)
SELECT 1, 'notificaciones/administrar/envios', '1', '', '', '', '', NULL
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM Rutas WHERE ruta = 'notificaciones/administrar/envios');

-- correo_desvio_prueba: en modo PRUEBA, los correos a ids_empleados_prueba se envían a esta
-- dirección en vez de al correo del empleado. Vacío = se usa el correo del empleado.
INSERT INTO Notificaciones_Configuracion (id_empresa, clave, valor, created_at)
SELECT 0, 'correo_desvio_prueba', '', NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM Notificaciones_Configuracion WHERE id_empresa = 0 AND clave = 'correo_desvio_prueba');

-- Primer tipo real. Arranca INACTIVO: ningún flujo lo dispara todavía; solo el botón
-- "Enviarme una prueba" de la administración.
INSERT INTO Notificaciones_Tipos
  (id_empresa, codigo, nombre, descripcion, clase, incluye_autor, muestra_en_bandeja, envia_correo,
   plantilla_titulo, plantilla_mensaje, plantilla_asunto_correo, plantilla_cuerpo_correo,
   icono, color, estado, created_at)
SELECT 0, 'OKRS_KR_ASIGNACION', 'Asignación de resultado clave',
   'Avisa al owner y a los responsables cuando se les asigna un resultado clave (KR).',
   'AVISO', FALSE, TRUE, TRUE,
   'Te asignaron el resultado clave "{{kr_titulo}}"',
   'Pertenece al objetivo "{{okr_titulo}}".',
   'Nuevo resultado clave asignado: {{kr_titulo}}',
   '<p>Hola {{destinatario_nombre}},</p><p>Te asignaron el resultado clave <strong>{{kr_titulo}}</strong> del objetivo <strong>{{okr_titulo}}</strong>.</p><p><a href="{{enlace}}">Ver el resultado clave</a></p>',
   'bx-target-lock', '#0d6efd', 'INACTIVO', NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM Notificaciones_Tipos WHERE id_empresa = 0 AND codigo = 'OKRS_KR_ASIGNACION');

-- ---------------------------------------------------------------------------
-- Reversión (descomentar para deshacer):
-- DELETE FROM Notificaciones_Tipos WHERE codigo = 'OKRS_KR_ASIGNACION';
-- DELETE FROM Notificaciones_Configuracion WHERE clave = 'correo_desvio_prueba';
-- DELETE FROM Rutas WHERE ruta LIKE 'notificaciones/administrar/%';
