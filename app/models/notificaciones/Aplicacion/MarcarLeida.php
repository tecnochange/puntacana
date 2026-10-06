<?php
namespace Notificaciones\Aplicacion;

use DateTimeImmutable;
use mysqli;
use Notificaciones\Infraestructura\BaseDatos\Consulta;
use Notificaciones\Infraestructura\BaseDatos\DestinatariosRepositorio;

final class MarcarLeida
{
    /**
     * Marca leída y devuelve la notificación, solo si esta persona es
     * destinataria. NULL si no lo es: nadie abre una notificación ajena
     * cambiando el id en la URL.
     */
    public static function ejecutar(mysqli $conexion, int $idEmpresa, int $idEmpleado, int $idEvento): ?array
    {
        $bandejas = new DestinatariosRepositorio(new Consulta($conexion));
        $evento = $bandejas->eventoDeEmpleado($idEmpresa, $idEmpleado, $idEvento);
        if ($evento === null) {
            return null;
        }
        $bandejas->marcarLeida($idEmpresa, $idEmpleado, $idEvento, new DateTimeImmutable());
        return $evento;
    }

    /** Marca leídas todas las de la persona. Devuelve cuántas cambiaron. */
    public static function todas(mysqli $conexion, int $idEmpresa, int $idEmpleado): int
    {
        $bandejas = new DestinatariosRepositorio(new Consulta($conexion));
        return $bandejas->marcarTodasLeidas($idEmpresa, $idEmpleado, new DateTimeImmutable());
    }
}
