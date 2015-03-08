<?php
/**
* Clase que genera los elementos Html
*/
class Html
{

    public function __construct()
    {

    }

    /**
     * Convierte la fecha y el tiempo de un formato a otro
     * @param $fecha
     * @param string $origin
     * @param string $dest
     * @return string
     */
    public function covertDate($fecha, $origin = 'Y-m-d', $dest = 'd-m-Y')
    {
        $fecha = date_create_from_format($origin, $fecha);
        return $fecha->format($dest);
    }
}
