<?php
/**
 * Clase que controla las consultas a la tabla clientes
 * User: ruben
 * Date: 6/03/15
 * Time: 13:40
 */
class Clientes extends Connection
{
    /**
     * Devuelve el nombre del cliente pasando como parametro el id
     * @param $idCliente
     * @return array
     */
    public function nombreClientePorId($idCliente)
    {
        $sql = "SELECT Nombre FROM clientes WHERE Id LIKE ?";
        return $this->consulta($sql, $idCliente);
    }
}
