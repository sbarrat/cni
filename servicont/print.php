<?php
require_once '../inc/variables.php';
require_once '../inc/classes/Connection.php';

$title = APLICACION ." - ". VERSION;
checkSession();
/**
 * Almacenamos el titulo pasado como variable de sesion
 * @var string | boolean
 */
$titulo = isset($_SESSION['titulo']) ? $_SESSION['titulo'] : false;
/**
 * Consulta sql pasada para ejecutarla
 * @var string | boolean
 */
$sql = isset($_SESSION['sqlQuery']) ? $_SESSION['titulo'] : false;
/**
 * Resultados de la consulta
 * @var array | boolean
 */
$resultados = false;
/**
 * Numero total de Columnas
 * @var integer
 */
$totalCampos = 0;
/**
 * Numero total de resultados
 * @var integer
 */
$totalCeldas = 0;
/**
 * Mensaje de los resultados
 * @var string
 */
$mensaje = "";
if ($sql && $titulo) {
    $conexion = new Connection();
    $resultados = $conexion->consulta($sql);
    $totalCeldas = count($resultados);
    $totales = array();
    if ($totalCeldas >= 10000) {
        $mensaje = "<tr><td>
        Demasiados Resultados. Filtre mas</td></tr>";
    } elseif ($totalCeldas == 0) {
        $mensaje = "<tr><td>
        No hay Resultados</td></tr>";
    } else {
        $totalCampos = count($resultados[0]);
        foreach ($resultados as $key => $resultado) {
            $mensaje .= "<tr>";
            $clase = clase($key);
            foreach ($resultado as $row => $var) {
                if (is_numeric($var)) {
                    $dato = number_format($var, 2, ',', '.');
                    if (!isset($totales[$row])) {
                        $totales[$row] = 0;
                    }
                    $totales[$row] += $var;
                } elseif (preg_match('#^[0-9]{4}-[0-9]{2}-[0-9]{2}', $var)) {
                    $fecha = date_create($var);
                    $dato = date_format('%d-%m-%Y');
                } else {
                    $dato = $var;
                }
                $mensaje .= "<td class='".$clase."'>".$dato."</td>";
            }
            $mensaje .= "</tr>";
        }
    }
}
// TODO: Codigo antiguo borrar al terminar
/*if (isset($_SESSION['titulo'])) {
        $sql = $_SESSION['sqlQuery'];
        $consulta = mysql_query($sql, $con);
        $totalCampos = mysql_num_fields($consulta);
        $totalCeldas = mysql_numrows($consulta);
        $mensaje = "";
        $j=0;
        $cadena = "
        <table class='tabla' width='100%'>
            <tr>
                <th colspan='".$totalCampos."'>
                    ".$_SESSION['titulo']."
                </th>
            </tr>";
        if ( $totalCeldas >= 10000 ) {
            $mensaje = "Demasiados Resultados. Filtre mas";
            $cadena.="<tr><th colspan='".$totalCampos."'>".$mensaje."</th></tr>";
        } elseif ( $totalCeldas == 0 ) {
            $mensaje = "No hay Resultados";
            $cadena.="<tr><th colspan='".$totalCampos."'>".$mensaje."</th></tr>";
        } else {
            $cadena.="<tr>";
            for ( $i = 0; $i < $totalCampos; $i ++ ) {
                $cadena.= "<th>".mysql_field_name($consulta,$i)."</th>";
            }
            $cadena.="</tr>";
            while ( true == ( $resultado = mysql_fetch_array( $consulta ) ) ) {
                $clase = ( $j++ % 2 == 0) ? "par" : "impar";
                $cadena."<tr>";
                for ( $i = 0; $i < $totalCampos; $i++ ) {
                    switch ( mysql_field_type( $consulta, $i ) ) {
                        case "string":
                            $campo = $resultado[$i];
                            break;
                        case "real":
                        if (mysql_field_name($consulta, $i) == 'Unidades'
                                    && $_SESSION['tipo'] == 'detallado') {
                            $campoUnidades = $i;
                            $mostrar = $resultado [$i];
                            $calculo = $resultado [$i];
                        } elseif (mysql_field_name($consulta, $i) == 'Importe'
                                    && $_SESSION['tipo'] == 'detallado') {
                            $mostrar = $resultado [$i];
                            $calculo =
                                $resultado [$i] * $resultado [$campoUnidades];
                        } else {
                                $mostrar = $resultado[$i];
                                $calculo = $resultado[$i];
                        }
                        $campo = number_format($mostrar, 2, ',', '.');
                        $tot[$i]=$tot[$i] + $calculo;
                        break;
                        case "date":
                            $campo = cambiaf($resultado[$i]);
                            break;
                        default:
                            $campo = $resultado[$i];
                            $tot[$i] ="";
                            break;
                    }
                    $cadena.="<td class='".$clase."'>".$campo."</td>";
                }
                $cadena.="</tr>";
            }
            $cadena.="<tr>";
            for ( $i = 0; $i < $totalCampos; $i++ ) {
                switch ( mysql_field_type( $consulta, $i ) ) {
                    case "string":
                        $cadena.="<th></th>";
                        break;
                    case "real":
                        $cadena.="<th>".number_format($tot[$i],2,',','.')."</th>";
                        break;
                    default:
                        $cadena.="<th></th>";
                        break;
                }
            }
        }
        $cadena.="</tr>";
        $cadena.="</table>";
        $cadena.="<div id='titulo'>Total Resultados: ".$totalCeldas."</div>";

}*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="estilo/print.css" rel="stylesheet" type="text/css"></link>
<title><?php echo $title; ?></title>
<body>
    <span class='volver' onclick='window.history.back()'>&larr; Volver</span>
    <table class='tabla' width="100%">
    <thead>
    <tr>
    </tr>
    </thead>
    <tbody>
    <?php echo $mensaje; ?>
    </tbody>
    <tfoot>
    </tfoot>
    </table>
    <div id='titulo'>Total Resultados: <?php echo $totalCeldas ?></div>
</body>
</html>