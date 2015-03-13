<?php
/**
*
*/
class Despachos extends Connection
{

    /**
     * Comprueba si el despacho esta Ocupado o no
     * @param  integer  $idDespacho [description]
     * @return boolean             [description]
     */
    public function isOcupado($idDespacho)
    {
        $result = false;
        $idDespacho = str_pad($idDespacho, 4, "0", STR_PAD_LEFT);
        $sql = "Select * from z_sercont where servicio like 'Codigo Negocio'
        and valor like ?";
        $resultados = $this->consulta($sql, $idDespacho);
        if (count($resultados)) {
            $result = true;
        }
        return $result;
    }
}
