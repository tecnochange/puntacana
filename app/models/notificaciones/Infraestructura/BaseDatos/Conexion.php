<?php
namespace Notificaciones\Infraestructura\BaseDatos;

use DateTimeImmutable;
use mysqli;
use mysqli_sql_exception;
use mysqli_stmt;

/**
 * Acceso a la base del módulo: sentencias preparadas y transacciones sobre una
 * conexión mysqli existente. Ningún valor se concatena en una consulta.
 */
final class Conexion
{
    /** Formato de DATETIME en la BD. */
    const FORMATO_FECHA = 'Y-m-d H:i:s';

    /** @var mysqli */
    private $mysqli;

    public function __construct(mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
    }

    /** Fecha lista para un parámetro DATETIME (NULL se queda NULL). */
    public static function fecha(?DateTimeImmutable $fecha): ?string
    {
        return $fecha === null ? null : $fecha->format(self::FORMATO_FECHA);
    }

    /** SELECT que devuelve todas las filas. */
    public function consultar(string $sql, string $tipos = '', array $parametros = []): array
    {
        $sentencia = $this->ejecutarSentencia($sql, $tipos, $parametros);
        $resultado = mysqli_stmt_get_result($sentencia);
        $filas = $resultado ? mysqli_fetch_all($resultado, MYSQLI_ASSOC) : [];
        mysqli_stmt_close($sentencia);
        return $filas;
    }

    /** SELECT que devuelve una fila o NULL. */
    public function consultarUno(string $sql, string $tipos = '', array $parametros = []): ?array
    {
        $filas = $this->consultar($sql, $tipos, $parametros);
        return $filas ? $filas[0] : null;
    }

    /** INSERT/UPDATE/DELETE; devuelve las filas afectadas. */
    public function modificar(string $sql, string $tipos = '', array $parametros = []): int
    {
        $sentencia = $this->ejecutarSentencia($sql, $tipos, $parametros);
        $afectadas = mysqli_stmt_affected_rows($sentencia);
        mysqli_stmt_close($sentencia);
        return $afectadas;
    }

    public function ultimoIdInsertado(): int
    {
        return (int) mysqli_insert_id($this->mysqli);
    }

    /** Ejecuta $operacion dentro de una transacción; si lanza, deshace todo. */
    public function enTransaccion(callable $operacion)
    {
        mysqli_begin_transaction($this->mysqli);
        try {
            $resultado = $operacion();
            mysqli_commit($this->mysqli);
            return $resultado;
        } catch (\Throwable $error) {
            mysqli_rollback($this->mysqli);
            throw $error;
        }
    }

    private function ejecutarSentencia(string $sql, string $tipos, array $parametros): mysqli_stmt
    {
        try {
            $sentencia = mysqli_prepare($this->mysqli, $sql);
            if ($sentencia === false) {
                throw new ErrorBaseDatos(mysqli_error($this->mysqli), mysqli_errno($this->mysqli));
            }
            if ($tipos !== '') {
                mysqli_stmt_bind_param($sentencia, $tipos, ...$parametros);
            }
            if (!mysqli_stmt_execute($sentencia)) {
                throw new ErrorBaseDatos(mysqli_stmt_error($sentencia), mysqli_stmt_errno($sentencia));
            }
            return $sentencia;
        } catch (mysqli_sql_exception $error) {
            throw new ErrorBaseDatos($error->getMessage(), $error->getCode(), $error);
        }
    }
}
