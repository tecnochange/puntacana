<?php
namespace Notificaciones\Infraestructura\BaseDatos;

use DateTimeImmutable;
use Notificaciones\Dominio\ClaseNotificacion;
use Notificaciones\Dominio\EstadoNotificacion;

/** Tabla Notificaciones. */
final class NotificacionesRepositorio
{
    /** @var Conexion */
    private $conexion;

    public function __construct(Conexion $conexion)
    {
        $this->conexion = $conexion;
    }

    public function idPorClaveEvento(int $idEmpresa, string $claveEvento): ?int
    {
        $fila = $this->conexion->consultarUno(
            'SELECT id FROM Notificaciones WHERE id_empresa = ? AND clave_evento = ?',
            'is',
            [$idEmpresa, $claveEvento]
        );
        return $fila ? (int) $fila['id'] : null;
    }

    /** @param array $notificacion columnas de Notificaciones (sin id, estado ni fechas de auditoría) */
    public function insertar(array $notificacion, DateTimeImmutable $ahora): int
    {
        $this->conexion->modificar(
            'INSERT INTO Notificaciones
                (id_empresa, id_tipo, clase, titulo, mensaje, url, tipo_registro, id_registro,
                 datos_plantilla, fecha_vencimiento, estado, id_empleado_autor, clave_evento, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            'iisssssisssiss',
            [
                $notificacion['id_empresa'],
                $notificacion['id_tipo'],
                $notificacion['clase'],
                $notificacion['titulo'],
                $notificacion['mensaje'],
                $notificacion['url'],
                $notificacion['tipo_registro'],
                $notificacion['id_registro'],
                $notificacion['datos_plantilla'],
                Conexion::fecha($notificacion['fecha_vencimiento']),
                EstadoNotificacion::ABIERTA,
                $notificacion['id_empleado_autor'],
                $notificacion['clave_evento'],
                Conexion::fecha($ahora),
            ]
        );
        return $this->conexion->ultimoIdInsertado();
    }

    /** Cierra la notificación si sigue ABIERTA. Devuelve si cambió algo. */
    public function cerrarPorClaveEvento(int $idEmpresa, string $claveEvento, string $estado, DateTimeImmutable $ahora): bool
    {
        $fecha = Conexion::fecha($ahora);
        $afectadas = $this->conexion->modificar(
            'UPDATE Notificaciones
             SET estado = ?, fecha_cierre = ?, updated_at = ?
             WHERE id_empresa = ? AND clave_evento = ? AND estado = ?',
            'sssiss',
            [$estado, $fecha, $fecha, $idEmpresa, $claveEvento, EstadoNotificacion::ABIERTA]
        );
        return $afectadas > 0;
    }

    /** Las TAREA abiertas cuya fecha_vencimiento ya pasó quedan VENCIDA. */
    public function marcarTareasVencidas(DateTimeImmutable $ahora): int
    {
        $fecha = Conexion::fecha($ahora);
        return $this->conexion->modificar(
            'UPDATE Notificaciones
             SET estado = ?, fecha_cierre = ?, updated_at = ?
             WHERE estado = ? AND clase = ? AND fecha_vencimiento < ?',
            'ssssss',
            [EstadoNotificacion::VENCIDA, $fecha, $fecha, EstadoNotificacion::ABIERTA, ClaseNotificacion::TAREA, $fecha]
        );
    }
}
