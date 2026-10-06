<?php
namespace Notificaciones\Dominio;

/** Qué hace el despacho de correos para una empresa (Notificaciones_Configuracion.modo). */
enum ModoOperacion: string
{
    /** El despacho no toca nada: los avisos pendientes esperan. */
    case APAGADO = 'apagado';

    /** Solo bandeja: los avisos se consumen y se registran como omitidos, sin correo. */
    case SOLO_PLATAFORMA = 'solo_plataforma';

    /** Correo únicamente a la lista de prueba; al resto se le registra como omitido. */
    case PRUEBA = 'prueba';

    case ACTIVO = 'activo';
}
