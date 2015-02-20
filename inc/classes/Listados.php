<?php
require_once 'Connection.php';

class Listados
{
	/**
     * @var Connection|null
     */
    private $conexion = null;
	
	public function __construct() {
		$this->conexion = new Connection();
	}
	
	public function servicios($servicio) {
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
    	WHERE z.servicio LIKE '".$servicio."'
    	ORDER BY Despacho";
		return $this->conexion->consulta($sql);
	}
}