<?php
namespace Notificaciones\Infraestructura\BaseDatos;

use DateTimeImmutable;
use Notificaciones\Dominio\ResultadoEnvio;

/** Tabla Notificaciones_Envios: bitácora de correos. No guarda direcciones ni cuerpos. */
final class EnviosRepositorio
{
    private const LARGO_MAXIMO_MENSAJE_ERROR = 500;

    /** @var Conexion */
    private $conexion;

    public function __construct(Conexion $conexion)
    {
        $this->conexion = $conexion;
    }

    /** $resultado: ResultadoEnvio::* */
    public function registrar(
        int $idEmpresa,
        int $idDestinatario,
        int $idEmpleado,
        ?string $asunto,
        string $resultado,
        DateTimeImmutable $ahora,
        ?string $idMensajeProveedor = null,
        ?string $mensajeError = null
    ): void {
        $fecha = Conexion::fecha($ahora);
        $fechaEnvio = $resultado === ResultadoEnvio::ENVIADO ? $fecha : null;
        $this->conexion->modificar(
            'INSERT INTO Notificaciones_Envios
                (id_empresa, id_destinatario, id_empleado, asunto, resultado, id_mensaje_proveedor, mensaje_error, fecha_envio, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            'iiissssss',
            [
                $idEmpresa,
                $idDestinatario,
                $idEmpleado,
                $asunto,
                $resultado,
                $idMensajeProveedor,
                $mensajeError === null ? null : mb_substr($mensajeError, 0, self::LARGO_MAXIMO_MENSAJE_ERROR),
                $fechaEnvio,
                $fecha,
            ]
        );
    }

    /** Últimos intentos de correo de la empresa, con el título de su notificación. */
    public function listarDeEmpresa(int $idEmpresa, int $limite): array
    {
        return $this->conexion->consultar(
            'SELECT e.id, e.id_empleado, e.asunto, e.resultado, e.id_mensaje_proveedor, e.mensaje_error,
                    e.fecha_envio, e.created_at, n.id AS id_notificacion, n.titulo
             FROM Notificaciones_Envios e
             JOIN Notificaciones_Destinatarios d ON d.id = e.id_destinatario
             JOIN Notificaciones n ON n.id = d.id_notificacion
             WHERE e.id_empresa = ?
             ORDER BY e.id DESC
             LIMIT ?',
            'ii',
            [$idEmpresa, $limite]
        );
    }
}
