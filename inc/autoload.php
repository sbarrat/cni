<?php
/**
 * Autoload.php Carga automatica de clases y variables
 *
 * Descripcion del fichero
 *
 * @package         cni
 * @subpackage      src
 * @author          Ruben Lacasa Mas <ruben@ensenalia.com>
 * @copyright       2013 Ruben Lacasa Mas.
 * @license         http://creativecommons.org/licenses/by-nc-nd/3.0
 *                  CC-BY-NC-ND-3.0
 * @link            https://bitbucket.org/sbarrat/webtransfer
 */
require_once __DIR__.'/variables.php';
require_once __DIR__.'/../vendor/autoload.php';
setlocale(LC_ALL, 'es_ES.UTF-8');
$autoloader = \Zend_Loader_Autoloader::getInstance();
$autoloader->pushAutoloader(
    function ($className) {
        \Zend_Loader::loadClass($className, __DIR__.'/classes/', true);
    }
);
$view = new Zend_View();
$view->setScriptPath(VIEWS);
