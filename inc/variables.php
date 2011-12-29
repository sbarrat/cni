<?php 
/**
 * Variables File Doc Comment
 *
 * Funciones y variables requeridas por las funciones de la aplicacion
 *
 * PHP Version 5.2.6
 *
 * @category Valida
 * @package  cni/inc
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/
 * 			 Creative Commons Reconocimiento-NoComercial-SinObraDerivada 3.0 Unported
 * @link     https://github.com/independenciacn/cni
 * @version  2.0e Estable
 */
/**
 * Establecemos la zona horaria 
 */ 
date_default_timezone_set('Europe/Madrid'); 
/**
 * Conexion a la base de datos
 * @var
 */
$con = mysql_connect ("localhost","cni","inc") or die (mysql_error());
mysql_set_charset('utf8', $con);
/**
 * Nombre de la tabla
 * 
 * @deprecated - establecerlo dentro de la funcion mysql_select_db
 * @var string
 */
$dbname = "centro"; 
mysql_select_db($dbname, $con);
/**
 * Imagen en el mensaje de correcto
 * 
 * @deprecated - Estan siendo retiradas de donde aparecian
 * @var unknown_type
 */
define("OK", "imagenes/clean.png");
/**
 * Imagen en el mensaje de error
 * @deprecated - Estan siendo retiradas de donde aparecian
 * @var unknown_type
 */
define("NOK","imagenes/error.png");
//define("SISTEMA","*nix");
/**
 * Define el sistema operativo donde va a trabajar la aplicacion
 * @deprecated - Sustituir por rutas
 * @var unknown_type
 */
define("SISTEMA","windows");
function checkSession(){
    if ( session_id() != null ){
        session_regenerate_id();
    } else {
        session_start();
    }
}
/**
 * Devuelve el tipo de clase css que sera el campo
 * 
 * @param integer $k
 * @return string
 */
function clase($k)
{
    $clase = ( $k%2 == 0)? 'par': 'impar';
    return $clase;
}
/**
 * Se le puede pasar como parametro un array o una string y la sanea
 *
 * @param mixed $vars
 *
 */
function sanitize( &$vars ) {
    global $con;
    if ( is_array( $vars ) ) {
        foreach ( $vars as &$var ) {
            mysql_real_escape_string( $var, $con );
        }
    } elseif( is_string( $vars ) ) {
        mysql_real_escape_string( $vars, $con );
    }
}
/**
 * Convierte el texto a utf8
 * 
 * @deprecated
 * @param string $texto
 * @return string $texto
 */
function traduce($texto)
{
    return $texto;
}
/**
 * Traduce el texto de utf8
 * 
 * @deprecated
 * @param string $texto
 * @return string $texto
 */
function codifica($texto)
{
    return $texto;
}