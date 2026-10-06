<?php
namespace Notificaciones\Infraestructura\BaseDatos;

use DateTimeImmutable;

/** Bitácora de correos. No guarda direcciones ni cuerpos. */
final class EnviosRepositorio
{
    const ENVIADO = 'ENVIADO';
    const FALLIDO = 'FALLIDO';
    const OMITIDO_MODO = 'OMITIDO_MODO';
    const OMITIDO_INACTIVO = 'OMITIDO_INACTIVO';
    const OMITIDO_SIN_CORREO = 'OMITIDO_SIN_CORREO';
    const OMITIDO_CADUCADO = 'OMITIDO_CADUCADO';
    const OMITIDO_TIPO = 'OMITIDO_TIPO';

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
