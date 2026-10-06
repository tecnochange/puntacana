<?php
namespace Notificaciones\Infraestructura\BaseDatos;

use DateTimeImmutable;
use Notificaciones\Dominio\ClaseNotificacion;
use Notificaciones\Dominio\EstadoNotificacion;

final class EventosRepositorio
{
    private const FORMATO_FECHA = 'Y-m-d H:i:s';

    public function __construct(private Consulta $consulta)
    {
    }

    public function idPorClaveUnica(int $idEmpresa, string $claveUnica): ?int
    {
        $fila = $this->consulta->fila(
            'SELECT id FROM Notificaciones_Eventos WHERE id_empresa = ? AND clave_unica = ?',
            'is',
            [$idEmpresa, $claveUnica]
        );
        return $fila ? (int) $fila['id'] : null;
    }

    public function insertar(array $evento, DateTimeImmutable $ahora): int
    {
        $this->consulta->ejecutar(
            'INSERT INTO Notificaciones_Eventos
                (id_empresa, id_tipo, codigo, clase, titulo, cuerpo, url, tipo_registro, id_registro,
                 datos, fecha_limite, estado, id_autor, clave_unica, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            'iissssssisssiss',
            [
                $evento['id_empresa'],
                $evento['id_tipo'],
                $evento['codigo'],
                $evento['clase'],
                $evento['titulo'],
                $evento['cuerpo'],
                $evento['url'],
                $evento['tipo_registro'],
                $evento['id_registro'],
                $evento['datos'],
                $evento['fecha_limite']?->format(self::FORMATO_FECHA),
                EstadoNotificacion::ABIERTA->value,
                $evento['id_autor'],
                $evento['clave_unica'],
                $ahora->format(self::FORMATO_FECHA),
            ]
        );
        return $this->consulta->ultimoId();
    }

    /** Cierra la notificación si sigue abierta. Devuelve si cambió algo. */
    public function cerrarPorClaveUnica(int $idEmpresa, string $claveUnica, EstadoNotificacion $estado, DateTimeImmutable $ahora): bool
    {
        $fecha = $ahora->format(self::FORMATO_FECHA);
        $afectadas = $this->consulta->ejecutar(
            'UPDATE Notificaciones_Eventos
             SET estado = ?, fecha_cierre = ?, updated_at = ?
             WHERE id_empresa = ? AND clave_unica = ? AND estado = ?',
            'sssiss',
            [$estado->value, $fecha, $fecha, $idEmpresa, $claveUnica, EstadoNotificacion::ABIERTA->value]
        );
        return $afectadas > 0;
    }

    /** Las tareas abiertas cuya fecha límite ya pasó quedan vencidas. */
    public function marcarTareasVencidas(DateTimeImmutable $ahora): int
    {
        $fecha = $ahora->format(self::FORMATO_FECHA);
        return $this->consulta->ejecutar(
            'UPDATE Notificaciones_Eventos
             SET estado = ?, fecha_cierre = ?, updated_at = ?
             WHERE estado = ? AND clase = ? AND fecha_limite < ?',
            'ssssss',
            [EstadoNotificacion::VENCIDA->value, $fecha, $fecha, EstadoNotificacion::ABIERTA->value, ClaseNotificacion::TAREA->value, $fecha]
        );
    }
}
