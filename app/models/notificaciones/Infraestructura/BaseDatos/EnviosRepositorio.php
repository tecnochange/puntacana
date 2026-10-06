<?php
namespace Notificaciones\Infraestructura\BaseDatos;

use DateTimeImmutable;

/** Bitácora de correos. No guarda direcciones ni cuerpos. */
final class EnviosRepositorio
{
    const ENVIADO = 'enviado';
    const FALLIDO = 'fallido';
    const OMITIDO_MODO = 'omitido_modo';
    const OMITIDO_INACTIVO = 'omitido_inactivo';
    const OMITIDO_SIN_CORREO = 'omitido_sin_correo';
    const OMITIDO_CADUCADO = 'omitido_caducado';
    const OMITIDO_TIPO = 'omitido_tipo';

    private const LARGO_MAXIMO_ERROR = 500;

    /** @var Consulta */
    private $consulta;

    public function __construct(Consulta $consulta)
    {
        $this->consulta = $consulta;
    }

    public function registrar(
        int $idEmpresa,
        ?int $idDestinatario,
        int $idEmpleado,
        ?string $asunto,
        string $estado,
        DateTimeImmutable $ahora,
        ?string $idMensajeProveedor = null,
        ?string $error = null
    ): void {
        $fecha = Consulta::fecha($ahora);
        $fechaEnvio = $estado === self::ENVIADO ? $fecha : null;
        $this->consulta->ejecutar(
            'INSERT INTO Notificaciones_Envios
                (id_empresa, id_destinatario, id_empleado, asunto, estado, id_mensaje_proveedor, error, fecha_envio, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            'iiissssss',
            [
                $idEmpresa,
                $idDestinatario,
                $idEmpleado,
                $asunto,
                $estado,
                $idMensajeProveedor,
                $error === null ? null : mb_substr($error, 0, self::LARGO_MAXIMO_ERROR),
                $fechaEnvio,
                $fecha,
            ]
        );
    }
}
