<?php
// TODO: Migrar de prototype a jQuery
// TODO: Que se pueda modificar la contraseña de acceso
// TODO: Agregar un nuevo campo a la factura: Nº Pedido
require_once 'inc/variables.php';
require_once 'inc/menu.php';
require_once 'inc/avisos.php';
$title = APLICACION ." - ". VERSION;
checkSession();
$msg = "";
if (isset($_GET["exit"])) {
    $msg = "<span class='ok'>Sesion Cerrada</span>";
}
if (isset($_GET["error"])) {
    $msg = "<span class='ko'>Usuario o Contraseña Incorrecta</span>";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="robots" content="noindex, nofollow">
    <link href="estilo/cni.css" rel="stylesheet" type="text/css">
    <link href="estilo/calendario.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src='js/prototype.js'></script>
    <script type="text/javascript" src="js/calendar.js"></script>
    <script type="text/javascript" src="js/lang/calendar-es.js"></script>
    <script type="text/javascript" src="js/calendar-setup.js"></script>
    <script type="text/javascript" src='js/independencia.js'></script>
    <title>Principal - <?php echo $title ?></title>
</head>
<body>
<div id='cuerpo'>
<?php
if (isset($_SESSION['usuario'])) :
    ?>
    <div id='menu_general'>
        <?php echo menu(); ?>
    </div>
    <?php
else :
    ?>
    <div id='registro'>
    <center>
        <img src='imagenes/logotipo2.png' width='538px'
        alt='The Perfect Place' >
    </center>
    <p />
    <center>
        <?php echo $msg; ?>
        <form id='login_usuario' method='post' action='inc/valida.php'>
            <table width='30%' class="login">
                <tr>
                    <td align='right'>Usuario:</td>
                    <td><input type='text' id="usuario" name="usuario" accesskey="u" tabindex="1" /></td>
                </tr>
                <tr>
                    <td align='right'>Contraseña:</td>
                    <td><input type='password' id="passwd" name="passwd" accesskey="c" tabindex="2" /></td>
                </tr>
                <tr>
                    <td align='center' colspan="2">
                        <input type='submit' class='boton' accesskey="e" tabindex="3"  value = '[->]Entrar' />
                    </td>
                </tr>
                <tr>
                    <td colspan='2'></td>
                </tr>
            </table>
        </form>
    </center>
    <p />
    <center>
        <p><span class="etiqueta">Desarrollado por:</span></p>
        <p><a href='http://www.ensenalia.com'><img src='imagenes/ensenalia.jpg' width='128' /></a></p>
        <p>
            Revisión 2015
        </p>
    </center>
    </div>
    <?php
endif; ?>
</div>
<div id='datos_interesantes'></div>
<div id='debug'></div>
<?php
if (isset($_SESSION['usuario'])) :
    ?>
    <div id='avisos'>
    <?php echo avisosHandler($_POST); ?>
    </div>
    <div id='resultados'></div>
    <div id='formulario'></div>
    <?php
endif; ?>
</body>
</html>