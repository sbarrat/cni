<?php
/**
 * Validacion File Doc Comment
 *
 * Solo genera el menu - Refractorizar el nombre a menu.php
 *
 * PHP Version 5.2.6
 *
 * @category Validacion
 * @package  cni/inc
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/
 *           Creative Commons Reconocimiento-NoComercial-SinObraDerivada 3.0 Unported
 * @link     https://github.com/independenciacn/cni
 * @version  2.0e Estable
 */
require_once 'variables.php';
require_once 'inc/classes/Connection.php';
checkSession();

/**
 * Genera el menu de la aplicacion
 * @return string $tabla
 */
function menu()
{
    $con = new Connection();
    $resultado = $con->consulta("Select * from menus");
    $tabla = "<table width='100%'><tr>";
    foreach ($resultado as $value) {
        switch ($value['id']) {
            case 7:
                $function = 'javascript:datos(1)';
                break;
            case 8:
                $function = 'javascript:datos(2)';
                break;
            case 9:
                $function = 'javascript:datos(3)';
                break;
            default:
                $function = 'javascript:menu('. $value['id'] .')';
                break;
        }
        $tabla .= "
            <th><a href='".$function."'>
                <img src='".$value['imagen']."' alt='".$value['nombre']."'
                width='32'>
                <br/>".$value['nombre']."
                </a>
            </th>";
    }
    $tabla .="<th><a href='inc/logout.php'>
    <img src='imagenes/salir.png' width='32' alt='Salir'><p/>Salir<a></th>";
    $tabla .= "</tr></table><div id='principal'></div>";
    return $tabla;
}
