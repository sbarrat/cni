<?php
/**
 * Valida File Doc Comment
 *
 * Fichero encargado de la validacion de usuarios
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
require_once 'variables.php';
require_once 'inc/classes/Connection.php';
checkSession();
$url = 'Location:../index.php';
$error = '?error=1';
if (isset($_POST['usuario']) && isset($_POST['passwd'])) {
    $usuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'passwd', FILTER_SANITIZE_STRING);

    $sql = "SELECT 1 from usuarios WHERE nick LIKE ?
    AND contra like sha1(?)";
    $con = new Connection();
    $resultado = $con->consulta($sql, array($usuario, $password));
    if (count($resultado) == 1) {
        $_SESSION['usuario'] = $usuario;
        $error = '';
    }
}
header($url.$error);
exit(0);
