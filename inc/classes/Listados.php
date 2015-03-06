<?php
/**
 * Genera el listado pasando el servicio
 */
class Listados extends Connection
{

    public function servicios($servicio)
    {
        $sql = "SELECT c.Id,c.Nombre, z.valor, z.servicio,
        (
            SELECT valor
            FROM z_sercont
            WHERE servicio LIKE 'Codigo Negocio'
            AND idemp LIKE z.idemp
            LIMIT 1
        )
        AS Despacho, c.Categoria
        FROM clientes AS c
        INNER JOIN z_sercont AS z ON c.Id = z.idemp
        WHERE z.servicio LIKE ?
        ORDER BY Despacho";
        return $this->conexion->consulta($sql, $servicio);
    }
}
