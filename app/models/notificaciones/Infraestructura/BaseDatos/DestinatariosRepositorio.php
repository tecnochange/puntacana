<?php
namespace Notificaciones\Infraestructura\BaseDatos;

use DateTimeImmutable;
use Notificaciones\Dominio\EstadoNotificacion;
use Notificaciones\Dominio\ModoCorreo;

final class DestinatariosRepositorio
{
    private const FORMATO_FECHA = 'Y-m-d H:i:s';
    private const LIMITE_BANDEJA = 500;

    public function __construct(private Consulta $consulta)
    {
    }

    public function insertar(int $idEmpresa, int $idEvento, int $idEmpleado, ?DateTimeImmutable $proximoAviso, DateTimeImmutable $ahora): void
    {
        $this->consulta->ejecutar(
            'INSERT INTO Notificaciones_Destinatarios (id_empresa, id_evento, id_empleado, proximo_aviso, created_at)
             VALUES (?, ?, ?, ?, ?)',
            'iiiss',
            [$idEmpresa, $idEvento, $idEmpleado, $proximoAviso?->format(self::FORMATO_FECHA), $ahora->format(self::FORMATO_FECHA)]
        );
    }

    /** Empresas que tienen algún correo pendiente hasta esa fecha. */
    public function empresasConAvisoPendiente(DateTimeImmutable $hasta): array
    {
        $filas = $this->consulta->filas(
            'SELECT DISTINCT id_empresa FROM Notificaciones_Destinatarios WHERE proximo_aviso <= ?',
            's',
            [$hasta->format(self::FORMATO_FECHA)]
        );
        return array_map('intval', array_column($filas, 'id_empresa'));
    }

    /**
     * Correos pendientes de esas empresas y ese modo, cuya notificación sigue
     * abierta. Se filtra en SQL para que lo que hoy no se puede enviar (otra
     * empresa apagada, resúmenes esperando su hora) no ocupe el cupo de la corrida.
     */
    public function conAvisoPendiente(DateTimeImmutable $hasta, ModoCorreo $modo, array $idsEmpresa, int $limite): array
    {
        if (!$idsEmpresa) {
            return [];
        }
        $marcadores = implode(',', array_fill(0, count($idsEmpresa), '?'));
        return $this->consulta->filas(
            "SELECT d.id, d.id_empresa, d.id_empleado, d.proximo_aviso, d.cantidad_avisos,
                    e.id AS id_evento, e.codigo, e.titulo, e.datos, e.fecha_limite
             FROM Notificaciones_Destinatarios d
             JOIN Notificaciones_Eventos e ON e.id = d.id_evento
             JOIN Notificaciones_Tipos t ON t.id = e.id_tipo
             WHERE d.proximo_aviso <= ? AND e.estado = ? AND t.modo_correo = ?
               AND d.id_empresa IN ($marcadores)
             ORDER BY d.proximo_aviso
             LIMIT ?",
            'sss' . str_repeat('i', count($idsEmpresa)) . 'i',
            array_merge(
                [$hasta->format(self::FORMATO_FECHA), EstadoNotificacion::ABIERTA->value, $modo->value],
                $idsEmpresa,
                [$limite]
            )
        );
    }

    /**
     * Toma el aviso para esta corrida: solo afecta la fila si proximo_aviso sigue
     * siendo el que se leyó. Si otra corrida lo tomó primero, devuelve false y
     * no se envía nada (así no hay correos duplicados).
     */
    public function reclamarAviso(int $id, string $proximoAvisoLeido, ?DateTimeImmutable $siguienteAviso, DateTimeImmutable $ahora): bool
    {
        $fecha = $ahora->format(self::FORMATO_FECHA);
        $afectadas = $this->consulta->ejecutar(
            'UPDATE Notificaciones_Destinatarios
             SET proximo_aviso = ?, cantidad_avisos = cantidad_avisos + 1, ultimo_aviso = ?, updated_at = ?
             WHERE id = ? AND proximo_aviso = ?',
            'sssis',
            [$siguienteAviso?->format(self::FORMATO_FECHA), $fecha, $fecha, $id, $proximoAvisoLeido]
        );
        return $afectadas === 1;
    }

    /** Quita los avisos pendientes de notificaciones que ya no están abiertas. */
    public function limpiarAvisosDeCerradas(): int
    {
        return $this->consulta->ejecutar(
            'UPDATE Notificaciones_Destinatarios d
             JOIN Notificaciones_Eventos e ON e.id = d.id_evento
             SET d.proximo_aviso = NULL
             WHERE d.proximo_aviso IS NOT NULL AND e.estado <> ?',
            's',
            [EstadoNotificacion::ABIERTA->value]
        );
    }

    /** Bandeja de una persona: solo tipos con canal de plataforma. */
    public function bandeja(int $idEmpresa, int $idEmpleado): array
    {
        return $this->consulta->filas(
            'SELECT e.id, e.codigo, e.titulo, e.cuerpo, e.estado, e.fecha_limite, e.created_at,
                    e.tipo_registro, e.id_registro, e.clase, t.icono, t.color, d.fecha_lectura
             FROM Notificaciones_Destinatarios d
             JOIN Notificaciones_Eventos e ON e.id = d.id_evento
             JOIN Notificaciones_Tipos t ON t.id = e.id_tipo
             WHERE d.id_empresa = ? AND d.id_empleado = ? AND t.canal_plataforma = 1
             ORDER BY e.created_at DESC
             LIMIT ?',
            'iii',
            [$idEmpresa, $idEmpleado, self::LIMITE_BANDEJA]
        );
    }

    /** La notificación, solo si esta persona es destinataria. */
    public function eventoDeEmpleado(int $idEmpresa, int $idEmpleado, int $idEvento): ?array
    {
        return $this->consulta->fila(
            'SELECT e.id, e.codigo, e.titulo, e.cuerpo, e.url, e.estado, e.fecha_limite, e.created_at,
                    e.tipo_registro, e.id_registro, e.clase, t.nombre AS tipo_nombre, t.icono, t.color, d.fecha_lectura
             FROM Notificaciones_Destinatarios d
             JOIN Notificaciones_Eventos e ON e.id = d.id_evento
             JOIN Notificaciones_Tipos t ON t.id = e.id_tipo
             WHERE d.id_empresa = ? AND d.id_empleado = ? AND d.id_evento = ?',
            'iii',
            [$idEmpresa, $idEmpleado, $idEvento]
        );
    }

    public function marcarLeida(int $idEmpresa, int $idEmpleado, int $idEvento, DateTimeImmutable $ahora): void
    {
        $fecha = $ahora->format(self::FORMATO_FECHA);
        $this->consulta->ejecutar(
            'UPDATE Notificaciones_Destinatarios
             SET fecha_lectura = ?, updated_at = ?
             WHERE id_empresa = ? AND id_empleado = ? AND id_evento = ? AND fecha_lectura IS NULL',
            'ssiii',
            [$fecha, $fecha, $idEmpresa, $idEmpleado, $idEvento]
        );
    }

    public function marcarTodasLeidas(int $idEmpresa, int $idEmpleado, DateTimeImmutable $ahora): int
    {
        $fecha = $ahora->format(self::FORMATO_FECHA);
        return $this->consulta->ejecutar(
            'UPDATE Notificaciones_Destinatarios
             SET fecha_lectura = ?, updated_at = ?
             WHERE id_empresa = ? AND id_empleado = ? AND fecha_lectura IS NULL',
            'ssii',
            [$fecha, $fecha, $idEmpresa, $idEmpleado]
        );
    }
}
