<?php
namespace Notificaciones\Infraestructura\Web;

/**
 * Chequeos de la petición HTTP para las acciones que escriben. El proyecto no
 * valida tokens CSRF, así que las acciones de administración exigen POST desde
 * el mismo sitio (Origin o, si no viene, Referer).
 */
final class Peticion
{
    public static function esPostDelMismoSitio(): bool
    {
        $esPost = isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST';
        if (!$esPost) {
            return false;
        }
        $origen = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
        $hostOrigen = (string) parse_url($origen, PHP_URL_HOST);
        $hostSitio = isset($_SERVER['HTTP_HOST']) ? (string) parse_url('//' . $_SERVER['HTTP_HOST'], PHP_URL_HOST) : '';
        return $hostOrigen !== '' && strcasecmp($hostOrigen, $hostSitio) === 0;
    }
}
