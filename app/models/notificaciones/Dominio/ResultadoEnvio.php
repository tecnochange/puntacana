<?php
namespace Notificaciones\Dominio;

/** Notificaciones_Envios.resultado: qué pasó con cada intento de correo. */
final class ResultadoEnvio
{
    const ENVIADO = 'ENVIADO';
    const FALLIDO = 'FALLIDO';
    /** El modo_operacion de la empresa no permite enviarle (SOLO_BANDEJA, o PRUEBA y no está en la lista). */
    const OMITIDO_MODO = 'OMITIDO_MODO';
    const OMITIDO_EMPLEADO_INACTIVO = 'OMITIDO_EMPLEADO_INACTIVO';
    const OMITIDO_SIN_CORREO = 'OMITIDO_SIN_CORREO';
    /** Llevaba demasiado tiempo pendiente (p. ej. el módulo estuvo APAGADO). */
    const OMITIDO_CADUCADO = 'OMITIDO_CADUCADO';
    /** El tipo se desactivó, ya no está en CodigoNotificacion o está mal configurado. */
    const OMITIDO_TIPO_INACTIVO = 'OMITIDO_TIPO_INACTIVO';
}
