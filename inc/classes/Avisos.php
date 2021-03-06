<?php
class Avisos extends Connection
{
    /**
     * Devuelve los cumpleaños de los empleados de la central
     * @param $cuando
     * @return array
     */
    public function cumplesCentral($cuando)
    {
        $between = $this->filterBetween($cuando);
        $sql = "Select clientes.id, clientes.Nombre as empresa,
        pcentral.persona_central as empleado,
        @anyo:= IF (MONTH(CURDATE()) = 12,
                    IF (MONTH(pcentral.cumple) = 1,
                        YEAR(date_add(curdate(), INTERVAL 1 YEAR)),
                        YEAR(curdate())
                    ),
                    YEAR(curdate())
        ),
        @month:= MONTH(pcentral.cumple),
        @day:= DAY(pcentral.cumple),
        STR_TO_DATE(CONCAT(@anyo, '-', @month, '-', @day), '%Y-%m-%d') as fecha
        FROM clientes INNER JOIN pcentral ON clientes.Id = pcentral.idemp
        WHERE clientes.Estado_de_cliente != 0
        HAVING fecha $between
        ORDER BY fecha";
        return $this->consulta($sql);
    }

    /**
     * Devuelve los cumpleaños de los empleados de la empresa
     * @param string $cuando
     * @return array
     */
    public function cumplesEmpresa($cuando = 'hoy')
    {
        $between = $this->filterBetween($cuando);
        $sql = "Select clientes.id, clientes.Nombre as empresa,
        CONCAT(pempresa.nombre, ' ', pempresa.apellidos) as empleado,
        @anyo:= IF (MONTH(CURDATE()) = 12,
                    IF (MONTH(pempresa.cumple) = 1,
                        YEAR(date_add(curdate(), INTERVAL 1 YEAR)),
                        YEAR(curdate())
                    ),
                    YEAR(curdate())
        ),
        @month:= MONTH(pempresa.cumple),
        @day:= DAY(pempresa.cumple),
        STR_TO_DATE(CONCAT(@anyo, '-', @month, '-', @day), '%Y-%m-%d') as fecha
        FROM clientes INNER JOIN pempresa ON clientes.Id = pempresa.idemp
        WHERE clientes.Estado_de_cliente != 0
        HAVING fecha $between
        ORDER BY fecha";
        return $this->consulta($sql);
    }

    /**
     * Devuelve los cumpleaños de los empleados
     * @param string $cuando
     * @return array
     */
    public function cumplesEmpleados($cuando = 'hoy')
    {
        $between = $this->filterBetween($cuando);
        $sql = "SELECT Id as id, 'Independenciacn' as empresa,
        CONCAT(Nombre, ' ', Apell1, ' ', Apell2) as empleado,
        @anyo:= IF (MONTH(CURDATE()) = 12,
                    IF (MONTH(FechNac) = 1,
                        YEAR(date_add(curdate(), INTERVAL 1 YEAR)),
                        YEAR(curdate())
                    ),
                    YEAR(curdate())
        ),
        @month:= MONTH(FechNac),
        @day:= DAY(FechNac),
        STR_TO_DATE(CONCAT(@anyo, '-', @month, '-', @day), '%Y-%m-%d') as fecha
        FROM empleados
        HAVING fecha $between
        ORDER BY fecha";
        return $this->consulta($sql);
    }

    /**
     * @param string $cuando
     * @return array
     */
    public function finalizanContrato($cuando = 'hoy')
    {
        $between = $this->filterBetweenContrato($cuando);
        $sql = "SELECT facturacion.id,
        facturacion.idemp,
        DATE_FORMAT(facturacion.finicio, '%d-%m-%Y') as finicio,
        facturacion.duracion,
        DATE_FORMAT(facturacion.renovacion, '%d-%m-%Y') as renovacion,
        clientes.Nombre
        FROM facturacion INNER JOIN clientes ON facturacion.idemp = clientes.Id
        WHERE clientes.Estado_de_cliente != 0 AND renovacion $between
        ORDER BY DAY(renovacion) asc";
        return $this->consulta($sql);
    }

    /**
     * @param $cuando
     * @return string
     */
    private function filterBetweenContrato($cuando)
    {
        $between = " BETWEEN
            DATE_ADD(
                DATE_ADD(CURDATE(), INTERVAL 1 DAY), INTERVAL 30 DAY)
            AND DATE_ADD(CURDATE(), INTERVAL 60 DAY)";
        if ($cuando == 'hoy') {
            $between = " LIKE CURDATE()";
        } elseif ($cuando == 'mes') {
            $between = " BETWEEN
                DATE_ADD(CURDATE(), INTERVAL 1 DAY)
                AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)";
        }
        return $between;
    }

    /**
     * @param $cuando
     * @return string
     */
    private function filterBetween($cuando)
    {
        $between = "BETWEEN
                DATE_ADD(CURDATE(), INTERVAL 2 DAY) AND
                DATE_ADD(CURDATE(), INTERVAL 1 MONTH)";
        if ($cuando == 'hoy') {
            $between = " = CURDATE()";
        } elseif ($cuando == 'mañana') {
            $between = " = DATE_ADD(CURDATE(), INTERVAL 1 DAY)";
        }
        return $between;
    }
}
