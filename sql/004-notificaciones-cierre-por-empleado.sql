-- 004-notificaciones-cierre-por-empleado.sql
-- Quién cerró una tarea a mano ("Marcar como completada" en el detalle). NULL cuando
-- la cerró el sistema (al detectar la acción) o venció sola.
-- Requiere 001. Ejecutar ANTES de subir la versión que muestra el botón.

USE puntacana_admin;

ALTER TABLE Notificaciones
  ADD COLUMN id_empleado_cierre int(11) NULL AFTER fecha_cierre;

-- ---------------------------------------------------------------------------
-- Reversión (descomentar para deshacer, después de bajar el código que la usa):
-- ALTER TABLE Notificaciones DROP COLUMN id_empleado_cierre;
