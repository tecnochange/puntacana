<?php
namespace Notificaciones\Dominio;

use Notificaciones\Dominio\Excepciones\CodigoDesconocido;

/**
 * Catálogo de notificaciones. Cada constante es un código que debe existir
 * también como fila en Notificaciones_Tipos (ahí vive su configuración).
 *
 * El valor es igual al nombre, en MAYÚSCULAS_CON_GUION_BAJO, con el módulo como
 * prefijo. Agregar una notificación nueva = una constante aquí, sus datos en
 * DATOS_PERMITIDOS y su fila en Notificaciones_Tipos con el mismo codigo.
 *
 * El valor de una constante no se cambia nunca: es la llave contra la BD.
 *
 * Un tipo se puede crear antes en la plataforma ("sin implementar"): su fila
 * existe pero su código todavía no está aquí, y sus plantillas solo pueden usar
 * Plantilla::DATOS_BASE hasta que se agregue la constante.
 */
final class CodigoNotificacion
{
    // OKRs: objetivos
    /** Al owner cuando se le asigna un objetivo. */
    const OKRS_OBJETIVO_ASIGNACION = 'OKRS_OBJETIVO_ASIGNACION';
    /** Al owner, 10 días antes de la fecha de entrega del objetivo. */
    const OKRS_OBJETIVO_VENCIMIENTO = 'OKRS_OBJETIVO_VENCIMIENTO';

    // OKRs: resultados clave
    /** Al owner y a los responsables cuando se les asigna un resultado clave. */
    const OKRS_KR_ASIGNACION = 'OKRS_KR_ASIGNACION';
    /** Al owner y a los responsables, 10 días antes de la fecha de entrega del resultado clave. */
    const OKRS_KR_VENCIMIENTO = 'OKRS_KR_VENCIMIENTO';
    /** Al owner y a los responsables, 3 días antes del seguimiento de una etapa del resultado clave. */
    const OKRS_KR_SEGUIMIENTO_ETAPA = 'OKRS_KR_SEGUIMIENTO_ETAPA';

    // OKRs: iniciativas
    /** Al owner y a los responsables cuando se les asigna una iniciativa. */
    const OKRS_INICIATIVA_ASIGNACION = 'OKRS_INICIATIVA_ASIGNACION';
    /** Al owner y a los responsables, 10 días antes de la fecha de entrega de la iniciativa. */
    const OKRS_INICIATIVA_VENCIMIENTO = 'OKRS_INICIATIVA_VENCIMIENTO';

    // OKRs: planes de acción
    /** Al owner y a los responsables cuando se les asigna un plan de acción. */
    const OKRS_PLAN_ACCION_ASIGNACION = 'OKRS_PLAN_ACCION_ASIGNACION';
    /** Al owner y a los responsables, 10 días antes de la fecha de entrega del plan de acción. */
    const OKRS_PLAN_ACCION_VENCIMIENTO = 'OKRS_PLAN_ACCION_VENCIMIENTO';
    /** Al owner y a los responsables, 3 días antes del seguimiento de una etapa del plan de acción. */
    const OKRS_PLAN_ACCION_SEGUIMIENTO_ETAPA = 'OKRS_PLAN_ACCION_SEGUIMIENTO_ETAPA';

    // KPIs
    /** A los integrantes cuando se les asigna un KPI. */
    const KPIS_KPI_ASIGNACION = 'KPIS_KPI_ASIGNACION';
    /** A los líderes del KPI, 3 días antes del seguimiento de un periodo. */
    const KPIS_KPI_SEGUIMIENTO_PERIODO = 'KPIS_KPI_SEGUIMIENTO_PERIODO';
    /** Al integrante retirado y a los líderes del KPI. */
    const KPIS_KPI_INTEGRANTE_RETIRADO = 'KPIS_KPI_INTEGRANTE_RETIRADO';
    /** A los líderes del KPI cuando alguien comenta (sin el texto del comentario). */
    const KPIS_KPI_COMENTARIO = 'KPIS_KPI_COMENTARIO';
    /** A los líderes del KPI cuando alguien sube un archivo. */
    const KPIS_KPI_DOCUMENTO = 'KPIS_KPI_DOCUMENTO';
    /** A los integrantes cuando se cargan los resultados de un periodo. */
    const KPIS_KPI_RESULTADO_CARGADO = 'KPIS_KPI_RESULTADO_CARGADO';

    /**
     * Nombres de datos que acepta cada código en datos_plantilla, y por lo tanto
     * los únicos {{marcadores}} que sus plantillas pueden usar (además de
     * Plantilla::DATOS_BASE).
     */
    private const DATOS_PERMITIDOS = [
        self::OKRS_OBJETIVO_ASIGNACION => ['okr_titulo'],
        self::OKRS_OBJETIVO_VENCIMIENTO => ['okr_titulo'],
        self::OKRS_KR_ASIGNACION => ['kr_titulo', 'okr_titulo'],
        self::OKRS_KR_VENCIMIENTO => ['kr_titulo', 'okr_titulo'],
        self::OKRS_KR_SEGUIMIENTO_ETAPA => ['kr_titulo', 'okr_titulo', 'etapa_nombre'],
        self::OKRS_INICIATIVA_ASIGNACION => ['iniciativa_titulo', 'kr_titulo'],
        self::OKRS_INICIATIVA_VENCIMIENTO => ['iniciativa_titulo', 'kr_titulo'],
        self::OKRS_PLAN_ACCION_ASIGNACION => ['plan_accion_titulo'],
        self::OKRS_PLAN_ACCION_VENCIMIENTO => ['plan_accion_titulo'],
        self::OKRS_PLAN_ACCION_SEGUIMIENTO_ETAPA => ['plan_accion_titulo', 'etapa_nombre'],
        self::KPIS_KPI_ASIGNACION => ['kpi_nombre'],
        self::KPIS_KPI_SEGUIMIENTO_PERIODO => ['kpi_nombre', 'periodo_nombre'],
        self::KPIS_KPI_INTEGRANTE_RETIRADO => ['kpi_nombre', 'integrante_nombre'],
        self::KPIS_KPI_COMENTARIO => ['kpi_nombre', 'autor_nombre'],
        // 'documento' está en DatosSensibles (cédulas): el nombre del archivo va como archivo_nombre.
        self::KPIS_KPI_DOCUMENTO => ['kpi_nombre', 'archivo_nombre', 'autor_nombre'],
        self::KPIS_KPI_RESULTADO_CARGADO => ['kpi_nombre', 'periodo_nombre'],
    ];

    /** MAYÚSCULAS_CON_GUION_BAJO: empieza con letra, sin guiones bajos dobles ni al final. */
    private const PATRON_CODIGO = '/^[A-Z][A-Z0-9]*(_[A-Z0-9]+)*$/';

    public static function tieneFormatoValido(string $codigo): bool
    {
        return (bool) preg_match(self::PATRON_CODIGO, $codigo);
    }

    /** Si el código ya está implementado en el sistema (tiene constante aquí). */
    public static function existe(string $codigo): bool
    {
        return array_key_exists($codigo, self::DATOS_PERMITIDOS);
    }

    /** Datos de un código implementado; [] si aún no lo está (solo podrá usar los datos base). */
    public static function datosPermitidosSiExiste(string $codigo): array
    {
        return self::existe($codigo) ? self::DATOS_PERMITIDOS[$codigo] : [];
    }

    public static function datosPermitidos(string $codigo): array
    {
        if (!self::existe($codigo)) {
            throw new CodigoDesconocido("El código '$codigo' no está en CodigoNotificacion.");
        }
        return self::DATOS_PERMITIDOS[$codigo];
    }
}
