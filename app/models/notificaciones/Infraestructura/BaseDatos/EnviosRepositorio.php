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
}
