-- 005-rutas-paginas-prueba-ia.sql
-- Rutas de las páginas de prueba del asistente de IA (agent-engine) sobre los OKRs:
-- views/okrs/okr/detalle_ia.php y resultados_ia.php, copias que no guardan ni borran.
-- Solo administradores (rol 1). Mismo módulo (16) que las vistas originales.
-- Ejecutar ANTES de subir las dos vistas: sin la fila, ?pg=... muestra "sin permisos".
-- Se puede correr más de una vez: no duplica las filas.

USE puntacana_admin;

INSERT INTO Rutas (id_empresa, ruta, roles, editar, crear, eliminar, exportar, modulo)
SELECT 1, 'okrs/okr/detalle_ia', '1', '', '', '', '', '16' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM Rutas WHERE ruta = 'okrs/okr/detalle_ia');

INSERT INTO Rutas (id_empresa, ruta, roles, editar, crear, eliminar, exportar, modulo)
SELECT 1, 'okrs/okr/resultados_ia', '1', '', '', '', '', '16' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM Rutas WHERE ruta = 'okrs/okr/resultados_ia');

-- Comprobación: 2 filas, rol 1.
SELECT id, ruta, roles, modulo FROM Rutas WHERE ruta IN ('okrs/okr/detalle_ia', 'okrs/okr/resultados_ia');

-- ---------------------------------------------------------------------------
-- Reversión (cuando el asistente pase a las vistas reales y se borren las copias):
-- DELETE FROM Rutas WHERE ruta IN ('okrs/okr/detalle_ia', 'okrs/okr/resultados_ia');
