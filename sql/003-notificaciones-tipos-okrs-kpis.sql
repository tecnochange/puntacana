-- 003-notificaciones-tipos-okrs-kpis.sql
-- Tipos de notificación del mapa de alertas de OKRs y KPIs (el de OKRS_KR_ASIGNACION ya
-- existe desde 002). Requiere 001 y 002.
--
-- Todos arrancan INACTIVO: ningún flujo los dispara todavía; solo el botón
-- "Enviarme una prueba" de la administración. Sus códigos ya están en CodigoNotificacion.
--
-- Asignaciones y eventos de KPI: AVISO (informan). Fechas de entrega y seguimientos: TAREA
-- (piden una acción y vencen); dias_antes_de_vencer marca cuándo sale su correo.
-- Ningún mensaje lleva textos libres (comentarios) ni datos personales: solo títulos y nombres.

USE puntacana_admin;

INSERT INTO Notificaciones_Tipos
  (id_empresa, codigo, nombre, descripcion, clase, dias_antes_de_vencer, incluye_autor, muestra_en_bandeja, envia_correo,
   plantilla_titulo, plantilla_mensaje, plantilla_asunto_correo, plantilla_cuerpo_correo,
   icono, color, estado, created_at)
SELECT t.* FROM (
  -- Objetivos
  SELECT 0 AS id_empresa, 'OKRS_OBJETIVO_ASIGNACION' AS codigo, 'Asignación de objetivo' AS nombre,
     'Avisa al owner cuando se le asigna un objetivo.' AS descripcion,
     'AVISO' AS clase, NULL AS dias_antes_de_vencer, FALSE AS incluye_autor, TRUE AS muestra_en_bandeja, TRUE AS envia_correo,
     'Te asignaron el objetivo "{{okr_titulo}}"' AS plantilla_titulo,
     'Eres el owner de este objetivo. Revisa sus resultados clave y su fecha de entrega.' AS plantilla_mensaje,
     'Nuevo objetivo asignado: {{okr_titulo}}' AS plantilla_asunto_correo,
     '<p>Hola {{destinatario_nombre}},</p><p>Te asignaron como owner del objetivo <strong>{{okr_titulo}}</strong>.</p><p><a href="{{enlace}}">Ver el objetivo</a></p>' AS plantilla_cuerpo_correo,
     'bx-flag' AS icono, '#0d6efd' AS color, 'INACTIVO' AS estado, NOW() AS created_at
  UNION ALL
  SELECT 0, 'OKRS_OBJETIVO_VENCIMIENTO', 'Objetivo próximo a vencer',
     'Recuerda al owner la fecha de entrega del objetivo, 10 días antes.',
     'TAREA', 10, FALSE, TRUE, TRUE,
     'El objetivo "{{okr_titulo}}" vence el {{fecha_vencimiento}}',
     'Revisa el avance de sus resultados clave antes de la fecha de entrega.',
     'Tu objetivo vence el {{fecha_vencimiento}}: {{okr_titulo}}',
     '<p>Hola {{destinatario_nombre}},</p><p>El objetivo <strong>{{okr_titulo}}</strong> vence el <strong>{{fecha_vencimiento}}</strong>. Revisa el avance de sus resultados clave antes de esa fecha.</p><p><a href="{{enlace}}">Ver el objetivo</a></p>',
     'bx-calendar-exclamation', '#f08c00', 'INACTIVO', NOW()
  -- Resultados clave
  UNION ALL
  SELECT 0, 'OKRS_KR_VENCIMIENTO', 'Resultado clave próximo a vencer',
     'Recuerda al owner y a los responsables la fecha de entrega del resultado clave, 10 días antes.',
     'TAREA', 10, FALSE, TRUE, TRUE,
     'El resultado clave "{{kr_titulo}}" vence el {{fecha_vencimiento}}',
     'Pertenece al objetivo "{{okr_titulo}}". Actualiza su avance antes de la fecha de entrega.',
     'Tu resultado clave vence el {{fecha_vencimiento}}: {{kr_titulo}}',
     '<p>Hola {{destinatario_nombre}},</p><p>El resultado clave <strong>{{kr_titulo}}</strong> del objetivo <strong>{{okr_titulo}}</strong> vence el <strong>{{fecha_vencimiento}}</strong>.</p><p><a href="{{enlace}}">Actualizar el avance</a></p>',
     'bx-calendar-exclamation', '#f08c00', 'INACTIVO', NOW()
  UNION ALL
  SELECT 0, 'OKRS_KR_SEGUIMIENTO_ETAPA', 'Seguimiento de etapa del resultado clave',
     'Recuerda al owner y a los responsables el seguimiento de una etapa del resultado clave, 3 días antes.',
     'TAREA', 3, FALSE, TRUE, TRUE,
     'Seguimiento de la etapa "{{etapa_nombre}}" de "{{kr_titulo}}"',
     'Registra el seguimiento antes del {{fecha_vencimiento}}. Objetivo: "{{okr_titulo}}".',
     'Seguimiento pendiente: {{etapa_nombre}} de {{kr_titulo}}',
     '<p>Hola {{destinatario_nombre}},</p><p>La etapa <strong>{{etapa_nombre}}</strong> del resultado clave <strong>{{kr_titulo}}</strong> tiene seguimiento el <strong>{{fecha_vencimiento}}</strong>.</p><p><a href="{{enlace}}">Registrar el seguimiento</a></p>',
     'bx-calendar-check', '#7048e8', 'INACTIVO', NOW()
  -- Iniciativas
  UNION ALL
  SELECT 0, 'OKRS_INICIATIVA_ASIGNACION', 'Asignación de iniciativa',
     'Avisa al owner y a los responsables cuando se les asigna una iniciativa.',
     'AVISO', NULL, FALSE, TRUE, TRUE,
     'Te asignaron la iniciativa "{{iniciativa_titulo}}"',
     'Aporta al resultado clave "{{kr_titulo}}".',
     'Nueva iniciativa asignada: {{iniciativa_titulo}}',
     '<p>Hola {{destinatario_nombre}},</p><p>Te asignaron la iniciativa <strong>{{iniciativa_titulo}}</strong> del resultado clave <strong>{{kr_titulo}}</strong>.</p><p><a href="{{enlace}}">Ver la iniciativa</a></p>',
     'bx-bulb', '#0d6efd', 'INACTIVO', NOW()
  UNION ALL
  SELECT 0, 'OKRS_INICIATIVA_VENCIMIENTO', 'Iniciativa próxima a vencer',
     'Recuerda al owner y a los responsables la fecha de entrega de la iniciativa, 10 días antes.',
     'TAREA', 10, FALSE, TRUE, TRUE,
     'La iniciativa "{{iniciativa_titulo}}" vence el {{fecha_vencimiento}}',
     'Aporta al resultado clave "{{kr_titulo}}". Actualiza su avance antes de la fecha de entrega.',
     'Tu iniciativa vence el {{fecha_vencimiento}}: {{iniciativa_titulo}}',
     '<p>Hola {{destinatario_nombre}},</p><p>La iniciativa <strong>{{iniciativa_titulo}}</strong> vence el <strong>{{fecha_vencimiento}}</strong>.</p><p><a href="{{enlace}}">Actualizar el avance</a></p>',
     'bx-calendar-exclamation', '#f08c00', 'INACTIVO', NOW()
  -- Planes de acción
  UNION ALL
  SELECT 0, 'OKRS_PLAN_ACCION_ASIGNACION', 'Asignación de plan de acción',
     'Avisa al owner y a los responsables cuando se les asigna un plan de acción.',
     'AVISO', NULL, FALSE, TRUE, TRUE,
     'Te asignaron el plan de acción "{{plan_accion_titulo}}"',
     'Revisa sus etapas y su fecha de entrega.',
     'Nuevo plan de acción asignado: {{plan_accion_titulo}}',
     '<p>Hola {{destinatario_nombre}},</p><p>Te asignaron el plan de acción <strong>{{plan_accion_titulo}}</strong>.</p><p><a href="{{enlace}}">Ver el plan de acción</a></p>',
     'bx-list-check', '#0d6efd', 'INACTIVO', NOW()
  UNION ALL
  SELECT 0, 'OKRS_PLAN_ACCION_VENCIMIENTO', 'Plan de acción próximo a vencer',
     'Recuerda al owner y a los responsables la fecha de entrega del plan de acción, 10 días antes.',
     'TAREA', 10, FALSE, TRUE, TRUE,
     'El plan de acción "{{plan_accion_titulo}}" vence el {{fecha_vencimiento}}',
     'Actualiza su avance antes de la fecha de entrega.',
     'Tu plan de acción vence el {{fecha_vencimiento}}: {{plan_accion_titulo}}',
     '<p>Hola {{destinatario_nombre}},</p><p>El plan de acción <strong>{{plan_accion_titulo}}</strong> vence el <strong>{{fecha_vencimiento}}</strong>.</p><p><a href="{{enlace}}">Actualizar el avance</a></p>',
     'bx-calendar-exclamation', '#f08c00', 'INACTIVO', NOW()
  UNION ALL
  SELECT 0, 'OKRS_PLAN_ACCION_SEGUIMIENTO_ETAPA', 'Seguimiento de etapa del plan de acción',
     'Recuerda al owner y a los responsables el seguimiento de una etapa del plan de acción, 3 días antes.',
     'TAREA', 3, FALSE, TRUE, TRUE,
     'Seguimiento de la etapa "{{etapa_nombre}}" de "{{plan_accion_titulo}}"',
     'Registra el seguimiento antes del {{fecha_vencimiento}}.',
     'Seguimiento pendiente: {{etapa_nombre}} de {{plan_accion_titulo}}',
     '<p>Hola {{destinatario_nombre}},</p><p>La etapa <strong>{{etapa_nombre}}</strong> del plan de acción <strong>{{plan_accion_titulo}}</strong> tiene seguimiento el <strong>{{fecha_vencimiento}}</strong>.</p><p><a href="{{enlace}}">Registrar el seguimiento</a></p>',
     'bx-calendar-check', '#7048e8', 'INACTIVO', NOW()
  -- KPIs
  UNION ALL
  SELECT 0, 'KPIS_KPI_ASIGNACION', 'Asignación de KPI',
     'Avisa a los integrantes cuando se les asigna un KPI.',
     'AVISO', NULL, FALSE, TRUE, TRUE,
     'Te asignaron el KPI "{{kpi_nombre}}"',
     'Ahora eres integrante de este KPI.',
     'Nuevo KPI asignado: {{kpi_nombre}}',
     '<p>Hola {{destinatario_nombre}},</p><p>Ahora eres integrante del KPI <strong>{{kpi_nombre}}</strong>.</p><p><a href="{{enlace}}">Ver el KPI</a></p>',
     'bx-line-chart', '#12b886', 'INACTIVO', NOW()
  UNION ALL
  SELECT 0, 'KPIS_KPI_SEGUIMIENTO_PERIODO', 'Seguimiento de periodo del KPI',
     'Recuerda a los líderes del KPI el seguimiento de un periodo, 3 días antes.',
     'TAREA', 3, FALSE, TRUE, TRUE,
     'Seguimiento del periodo {{periodo_nombre}} de "{{kpi_nombre}}"',
     'Registra el seguimiento antes del {{fecha_vencimiento}}.',
     'Seguimiento pendiente: {{kpi_nombre}} ({{periodo_nombre}})',
     '<p>Hola {{destinatario_nombre}},</p><p>El KPI <strong>{{kpi_nombre}}</strong> tiene seguimiento del periodo <strong>{{periodo_nombre}}</strong> el <strong>{{fecha_vencimiento}}</strong>.</p><p><a href="{{enlace}}">Registrar el seguimiento</a></p>',
     'bx-calendar-check', '#7048e8', 'INACTIVO', NOW()
  UNION ALL
  SELECT 0, 'KPIS_KPI_INTEGRANTE_RETIRADO', 'Integrante retirado del KPI',
     'Avisa al integrante retirado y a los líderes del KPI.',
     'AVISO', NULL, FALSE, TRUE, TRUE,
     '{{integrante_nombre}} ya no es integrante de "{{kpi_nombre}}"',
     NULL,
     'Cambio de integrantes en el KPI {{kpi_nombre}}',
     '<p>Hola {{destinatario_nombre}},</p><p><strong>{{integrante_nombre}}</strong> ya no es integrante del KPI <strong>{{kpi_nombre}}</strong>.</p><p><a href="{{enlace}}">Ver el KPI</a></p>',
     'bx-user-minus', '#e03131', 'INACTIVO', NOW()
  UNION ALL
  SELECT 0, 'KPIS_KPI_COMENTARIO', 'Nuevo comentario en KPI',
     'Avisa a los líderes del KPI cuando alguien comenta. No incluye el texto del comentario.',
     'AVISO', NULL, FALSE, TRUE, TRUE,
     '{{autor_nombre}} comentó en "{{kpi_nombre}}"',
     'Entra al KPI para leer el comentario.',
     'Nuevo comentario en el KPI {{kpi_nombre}}',
     '<p>Hola {{destinatario_nombre}},</p><p><strong>{{autor_nombre}}</strong> dejó un comentario en el KPI <strong>{{kpi_nombre}}</strong>.</p><p><a href="{{enlace}}">Leer el comentario</a></p>',
     'bx-message-rounded-dots', '#7048e8', 'INACTIVO', NOW()
  UNION ALL
  SELECT 0, 'KPIS_KPI_DOCUMENTO', 'Nuevo documento en KPI',
     'Avisa a los líderes del KPI cuando alguien sube un archivo.',
     'AVISO', NULL, FALSE, TRUE, TRUE,
     '{{autor_nombre}} subió "{{archivo_nombre}}" a "{{kpi_nombre}}"',
     NULL,
     'Nuevo documento en el KPI {{kpi_nombre}}',
     '<p>Hola {{destinatario_nombre}},</p><p><strong>{{autor_nombre}}</strong> subió el archivo <strong>{{archivo_nombre}}</strong> al KPI <strong>{{kpi_nombre}}</strong>.</p><p><a href="{{enlace}}">Ver el KPI</a></p>',
     'bx-file', '#12b886', 'INACTIVO', NOW()
  UNION ALL
  SELECT 0, 'KPIS_KPI_RESULTADO_CARGADO', 'Resultados cargados en KPI',
     'Avisa a los integrantes cuando se cargan los resultados de un periodo.',
     'AVISO', NULL, FALSE, TRUE, TRUE,
     'Se cargaron los resultados de {{periodo_nombre}} en "{{kpi_nombre}}"',
     NULL,
     'Resultados de {{periodo_nombre}}: {{kpi_nombre}}',
     '<p>Hola {{destinatario_nombre}},</p><p>Ya están los resultados del periodo <strong>{{periodo_nombre}}</strong> del KPI <strong>{{kpi_nombre}}</strong>.</p><p><a href="{{enlace}}">Ver los resultados</a></p>',
     'bx-bar-chart-alt-2', '#12b886', 'INACTIVO', NOW()
) AS t
WHERE NOT EXISTS (SELECT 1 FROM Notificaciones_Tipos e WHERE e.id_empresa = 0 AND e.codigo = t.codigo);

-- ---------------------------------------------------------------------------
-- Reversión (descomentar para deshacer). Solo si no se generaron notificaciones con estos tipos:
-- DELETE FROM Notificaciones_Tipos WHERE id_empresa = 0 AND codigo IN (
--   'OKRS_OBJETIVO_ASIGNACION', 'OKRS_OBJETIVO_VENCIMIENTO', 'OKRS_KR_VENCIMIENTO', 'OKRS_KR_SEGUIMIENTO_ETAPA',
--   'OKRS_INICIATIVA_ASIGNACION', 'OKRS_INICIATIVA_VENCIMIENTO', 'OKRS_PLAN_ACCION_ASIGNACION',
--   'OKRS_PLAN_ACCION_VENCIMIENTO', 'OKRS_PLAN_ACCION_SEGUIMIENTO_ETAPA', 'KPIS_KPI_ASIGNACION',
--   'KPIS_KPI_SEGUIMIENTO_PERIODO', 'KPIS_KPI_INTEGRANTE_RETIRADO', 'KPIS_KPI_COMENTARIO',
--   'KPIS_KPI_DOCUMENTO', 'KPIS_KPI_RESULTADO_CARGADO');
