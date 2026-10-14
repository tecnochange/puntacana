<?php
namespace Notificaciones\Infraestructura\BaseDatos;

use DateTimeImmutable;
use Notificaciones\Dominio\EstadoNotificacion;

/** Tabla Notificaciones_Destinatarios: la bandeja de cada empleado y la programación de sus correos. */
final class DestinatariosRepositorio
{
    private const LIMITE_BANDEJA = 500;

    /** @var Conexion */
    private $conexion;

    public function __construct(Conexion $conexion)
    {
        $this->conexion = $conexion;
    }

    public function insertar(int $idEmpresa, int $idNotificacion, int $idEmpleado, ?DateTimeImmutable $fechaProximoCorreo, DateTimeImmutable $ahora): void
    {
        $this->conexion->modificar(
            'INSERT INTO Notificaciones_Destinatarios (id_empresa, id_notificacion, id_empleado, fecha_proximo_correo, created_at)
             VALUES (?, ?, ?, ?, ?)',
            'iiiss',
            [$idEmpresa, $idNotificacion, $idEmpleado, Conexion::fecha($fechaProximoCorreo), Conexion::fecha($ahora)]
        );
    }

    /** Empresas con algún correo pendiente hasta esa fecha. */
    public function empresasConCorreoPendiente(DateTimeImmutable $hasta): array
    {
        $filas = $this->conexion->consultar(
            'SELECT DISTINCT id_empresa FROM Notificaciones_Destinatarios WHERE fecha_proximo_correo <= ?',
            's',
            [Conexion::fecha($hasta)]
        );
        return array_map('intval', array_column($filas, 'id_empresa'));
    }

    /**
     * Correos pendientes de esas empresas cuya notificación sigue ABIERTA,
     * separados en los que salen uno por uno ($agrupados = false) y los que van
     * en resumen ($agrupados = true). Se filtra en SQL para que lo que hoy no se
     * puede enviar no ocupe el cupo de la corrida.
     */
    public function pendientesDeCorreo(DateTimeImmutable $hasta, bool $agrupados, array $idsEmpresa, int $limite): array
    {
        if (!$idsEmpresa) {
            return [];
        }
        $marcadores = implode(',', array_fill(0, count($idsEmpresa), '?'));
        $condicionResumen = $agrupados ? 't.intervalo_resumen IS NOT NULL' : 't.intervalo_resumen IS NULL';
        return $this->conexion->consultar(
            "SELECT d.id, d.id_empresa, d.id_empleado, d.fecha_proximo_correo, d.cantidad_intentos_correo,
                    n.id AS id_notificacion, n.id_tipo, n.tipo_registro, n.titulo, n.datos_plantilla, n.fecha_vencimiento
             FROM Notificaciones_Destinatarios d
             JOIN Notificaciones n ON n.id = d.id_notificacion
             JOIN Notificaciones_Tipos t ON t.id = n.id_tipo
             WHERE d.fecha_proximo_correo <= ? AND n.estado = ? AND $condicionResumen
               AND d.id_empresa IN ($marcadores)
             ORDER BY d.fecha_proximo_correo
             LIMIT ?",
            'ss' . str_repeat('i', count($idsEmpresa)) . 'i',
            array_merge(
                [Conexion::fecha($hasta), EstadoNotificacion::ABIERTA],
                $idsEmpresa,
                [$limite]
            )
        );
    }

    /**
     * Reserva el correo para esta corrida: solo afecta la fila si
     * fecha_proximo_correo sigue siendo la que se leyó. Si otra corrida lo
     * reservó primero devuelve false y no se envía nada (sin correos duplicados).
     */
    public function reservarCorreo(int $id, string $fechaProximoCorreoLeida, ?DateTimeImmutable $fechaSiguienteCorreo, DateTimeImmutable $ahora): bool
    {
        $fecha = Conexion::fecha($ahora);
        $afectadas = $this->conexion->modificar(
            'UPDATE Notificaciones_Destinatarios
             SET fecha_proximo_correo = ?, cantidad_intentos_correo = cantidad_intentos_correo + 1,
                 fecha_ultimo_intento_correo = ?, updated_at = ?
             WHERE id = ? AND fecha_proximo_correo = ?',
            'sssis',
            [Conexion::fecha($fechaSiguienteCorreo), $fecha, $fecha, $id, $fechaProximoCorreoLeida]
        );
        return $afectadas === 1;
    }

    /** Quita los correos pendientes de notificaciones que ya no están ABIERTA. */
    public function cancelarCorreosDeCerradas(): int
    {
        return $this->conexion->modificar(
            'UPDATE Notificaciones_Destinatarios d
             JOIN Notificaciones n ON n.id = d.id_notificacion
             SET d.fecha_proximo_correo = NULL
             WHERE d.fecha_proximo_correo IS NOT NULL AND n.estado <> ?',
            's',
            [EstadoNotificacion::ABIERTA]
        );
    }

    /** Bandeja de un empleado: solo tipos que se muestran en bandeja. */
    public function listarBandeja(int $idEmpresa, int $idEmpleado): array
    {
        return $this->conexion->consultar(
            'SELECT n.id, n.id_tipo, n.clase, n.titulo, n.mensaje, n.estado, n.fecha_vencimiento, n.created_at,
                    n.tipo_registro, n.id_registro, t.nombre AS nombre_tipo, t.icono, t.color, d.fecha_lectura
             FROM Notificaciones_Destinatarios d
             JOIN Notificaciones n ON n.id = d.id_notificacion
             JOIN Notificaciones_Tipos t ON t.id = n.id_tipo
             WHERE d.id_empresa = ? AND d.id_empleado = ? AND t.muestra_en_bandeja = TRUE
             ORDER BY n.created_at DESC
             LIMIT ?',
            'iii',
            [$idEmpresa, $idEmpleado, self::LIMITE_BANDEJA]
        );
    }

    /** Cuántas notificaciones de su bandeja no ha leído el empleado (el número de la campana). */
    public function contarSinLeer(int $idEmpresa, int $idEmpleado): int
    {
        $fila = $this->conexion->consultarUno(
            'SELECT COUNT(*) AS total
             FROM Notificaciones_Destinatarios d
             JOIN Notificaciones n ON n.id = d.id_notificacion
             JOIN Notificaciones_Tipos t ON t.id = n.id_tipo
             WHERE d.id_empleado = ? AND d.fecha_lectura IS NULL AND d.id_empresa = ? AND t.muestra_en_bandeja = TRUE',
            'ii',
            [$idEmpleado, $idEmpresa]
        );
        return $fila !== null ? (int) $fila['total'] : 0;
    }

    /** La notificación, solo si este empleado es destinatario. */
    public function notificacionDeEmpleado(int $idEmpresa, int $idEmpleado, int $idNotificacion): ?array
    {
        return $this->conexion->consultarUno(
            'SELECT n.id, n.clase, n.titulo, n.mensaje, n.url, n.estado, n.fecha_vencimiento, n.fecha_cierre, n.id_empleado_cierre, n.created_at,
                    n.tipo_registro, n.id_registro, n.id_empleado_autor, t.nombre AS nombre_tipo, t.icono, t.color,
                    t.muestra_en_bandeja, d.id AS id_destinatario, d.fecha_lectura
             FROM Notificaciones_Destinatarios d
             JOIN Notificaciones n ON n.id = d.id_notificacion
             JOIN Notificaciones_Tipos t ON t.id = n.id_tipo
             WHERE d.id_empresa = ? AND d.id_empleado = ? AND d.id_notificacion = ?',
            'iii',
            [$idEmpresa, $idEmpleado, $idNotificacion]
        );
    }

    /**
     * Id de la notificación vecina en la bandeja (mismo orden: created_at y luego id),
     * la más reciente o la más antigua. NULL si es la primera o la última.
     */
    public function vecinaEnBandeja(int $idEmpresa, int $idEmpleado, string $creada, int $idNotificacion, bool $masReciente): ?int
    {
        $comparacion = $masReciente ? '>' : '<';
        $orden = $masReciente ? 'ASC' : 'DESC';
        $fila = $this->conexion->consultarUno(
            "SELECT n.id
             FROM Notificaciones_Destinatarios d
             JOIN Notificaciones n ON n.id = d.id_notificacion
             JOIN Notificaciones_Tipos t ON t.id = n.id_tipo
             WHERE d.id_empresa = ? AND d.id_empleado = ? AND t.muestra_en_bandeja = TRUE
               AND (n.created_at $comparacion ? OR (n.created_at = ? AND n.id $comparacion ?))
             ORDER BY n.created_at $orden, n.id $orden
             LIMIT 1",
            'iissi',
            [$idEmpresa, $idEmpleado, $creada, $creada, $idNotificacion]
        );
        return $fila ? (int) $fila['id'] : null;
    }

    public function marcarLeida(int $idEmpresa, int $idEmpleado, int $idNotificacion, DateTimeImmutable $ahora): void
    {
        $fecha = Conexion::fecha($ahora);
        $this->conexion->modificar(
            'UPDATE Notificaciones_Destinatarios
             SET fecha_lectura = ?, updated_at = ?
             WHERE id_empresa = ? AND id_empleado = ? AND id_notificacion = ? AND fecha_lectura IS NULL',
            'ssiii',
            [$fecha, $fecha, $idEmpresa, $idEmpleado, $idNotificacion]
        );
    }

    public function marcarTodasLeidas(int $idEmpresa, int $idEmpleado, DateTimeImmutable $ahora): int
    {
        $fecha = Conexion::fecha($ahora);
        return $this->conexion->modificar(
            'UPDATE Notificaciones_Destinatarios
             SET fecha_lectura = ?, updated_at = ?
             WHERE id_empresa = ? AND id_empleado = ? AND fecha_lectura IS NULL',
            'ssii',
            [$fecha, $fecha, $idEmpresa, $idEmpleado]
        );
    }
}
