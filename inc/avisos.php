<?php
/**
 * Avisos File Doc Comment
 *
 * Muestra el cartel de avisos
 *
 * PHP Version 5.2.6
 *
 * @category Avisos
 * @package  cni/inc
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/
 *           Creative Commons Reconocimiento-NoComercial-SinObraDerivada 3.0 Unported
 * @link     https://github.com/independenciacn/cni
 * @version  2.0e Estable
 */
require_once 'variables.php';
require_once 'classes/Avisos.php';
require_once 'classes/Listados.php';

checkSession();

/**
 * Devuelve el listado de Avisos
 * @param  array $vars Opciones de visualizacíon
 * @return string       listado de avisos
 */
function avisosHandler($vars = array())
{
    $cadena = "";
    if (isset($_SESSION['usuario'])) {
        $cadena = avisos();
        if (isset($vars['opcion']) && $vars['opcion'] == 1) {
            $cadena = telefonos();
        }
    }
    return $cadena;
}

/**
 * Funcion que muestra los avisos
 *
 * @return string $cadena
 */
function avisos()
{
    $avisos = new Avisos();
    $texto = "
    <input type='button' class='boton' value='Ver Avisos' onclick='panelAvisos()'>
        <table class='tabla' id='tablaAvisos'>
        <tr>
            <th colspan='2'>Cartel de Avisos</th>
        </tr>
        <tr>
            <th>Cumpleaños</th>
            <th>Contratos</th>
        </tr>";

    $rangos = array(
        'hoy' => 'Hoy',
        'mañana' => 'Mañana',
        'mes' => 'Los proximos dias'
    );
    foreach ($rangos as $key => $rango) {
        $texto.= "
        <tr>
        <td valign='top'>
        <table width='100%'>
            <tr><th colspan='2'>".$rango." hacen los años</th></tr>";
        $resultados = array();
        $resultados = array_merge($resultados, $avisos->cumplesCentral($key));
        $resultados = array_merge($resultados, $avisos->cumplesEmpresa($key));
        $resultados = array_merge($resultados, $avisos->cumplesEmpleados($key));
        $cadena = "";
        // Ordenar por fecha solo en rango mes
        if ($key == 'mes') {
            foreach ($resultados as $result => $row) {
                $fecha[$result]  = $row['fecha'];
            }
            array_multisort($fecha, SORT_ASC, SORT_STRING, $resultados);
        }
        $clase = 0;
        foreach ($resultados as $resultado) {
            // Cambiar fecha a normal
            $empresa = ($resultado['empresa'] == 'Independenciacn')
                ? ""
                : " de
                <a href='javascript:muestra(".$resultado['id'].")'>" .
                $resultado['empresa'] ."</a>" ;
            $linea ="<tr class=".clase($clase)."><td colspan='2' >" .
                    $resultado['empleado']. $empresa.
                "</td></tr>";
            if ($key == 'mes') {
                $fecha = date_create($resultado['fecha']);
                $linea ="<tr class=".clase($clase).">
                    <td>" . $fecha->format('d-m') . "</td>
                    <td>" . $resultado['empleado'] . $empresa ."</td>
                    </tr>";
            }
            $cadena .= $linea;
            $clase++;
        }
        if (strlen($cadena) == 0) {
            $cadena = "<tr>
            <td colspan='2'>".$rango." nadie cumple los años</td>
            </tr>";
        }
        $texto .= $cadena;
        $texto .= "</table>";
    }

    $texto.="</td>";
    $texto.= "<td valign='top'>".contratos()."</td></tr></table>";
    return $texto;
}

/**
 * Genera el boton de ocultar telefono y el listado de telefonos
 *
 * @return string $cadena
 */
function telefonos()
{
    $cadena ="<input type='button' value='[v]Ocultar telefonos'
    onclick='cerrar_tablon_telefonos()'/>";
    $cadena .= listado('Telefono');
    $cadena .= listado('Fax');
    $cadena .= listado('Adsl');
    return $cadena;
}
/**
 * Devuelve el listado del servicio seleccionado
 *
 * @param string $servicio
 * @return string $cadena
 * @deprecated
 */
function listado($servicio)
{
    $cadena ="<p/><u><b>".$servicio." del centro</b></u><p/>";
    $listado = new Listados();
    $resultados = $listado->servicios($servicio);
    $cadena .="<table><tr>";
    $i=0;
    if ($resultados) {
        foreach ($resultados as $resultado) {
            var_dump($resultado);
        }
    }
    if (mysql_numrows($consulta)!=0) {
        while (true == ($resultado = mysql_fetch_array($consulta)))
        {
            if (preg_match('#despacho#i', $resultado[5])) {
                $color="#69C";
            } elseif (preg_match('#domicili#i', $resultado[5])) {
                $color="#F90";
            } else {
                $color="#ccc";
            }
            if ($i%4==0) {
                $cadena .="</tr><tr>";
            }
            $cadena .= "<th bgcolor='".$color."' align='left'>
                <a href='javascript:muestra($resultado[0])'>"
                .$resultado[4]."-".$resultado[1]."-
            <u><b>".$resultado[2]."</b></u></a></th>";
            $i++;
        }
    }
    $cadena .="</tr></table>";
    return $cadena;
}
/**
 * Devuelve el estado de los contratos
 * @return string
 */
function contratos()
{
    $avisos = new Avisos();
    $hnocump = 0;
    $k=0;
    //Clientes Finalizan contrato Hoy
    $finalizan = $avisos->finalizanContrato('hoy');
    $cadena ="<table width='100%'>";
    $cadena .= "<tr><th Colspan='2'>Hoy finalizan contrato</th></tr>";
    if (count($finalizan)) {
        foreach ($finalizan as $resultado) {
            $cadena .="<tr><td class='".clase($k++)."'>
                <a href='javascript:muestra(".$resultado['idemp'].")' >"
                .$resultado['Nombre']."</a></td></tr>";
                $k++;
        }
    } else {
        $hnocump++;
        $cadena.="<tr><td class='".clase($k++)."' colspan='2'>
        Nadie Finaliza contrato hoy</td></tr>";
    }
    $cadena .= "</table>";

    // Clientes finalizan contrato este mes
    $finalizan = $avisos->finalizanContrato('hoy');
    $cadena .= "
    <table width='100%'>
    <tr>
    <th>Dia</th>
    <th>Finalizan contrato en los proximos 30 días</th>
    </tr>";
    if (count($finalizan)) {
        foreach ($finalizan as $resultado) {
            $cadena .="<tr>
            <td class='".clase($k)."'>".$resultado['renovacion']."</td>
            <td class='".clase($k)."'>
            <a href='javascript:muestra(".$resultado['idemp'].")' >"
            .$resultado['Nombre']."</a>
            </td>
            </tr>";
            $k++;
        }
    } else {
        $hnocump++;
        $cadena.="<tr><td colspan='2' class='".clase($k++)."'>
        Nadie Finaliza contrato en los proximos 30 días</td></tr>";
    }
    $cadena .= "</table>";
    // Proximos 60 dias
    $finalizan = $avisos->finalizanContrato('60');
    $cadena .= "
    <table width='100%'>
    <tr>
    <th>Dia</th>
    <th>Finalizan contrato en los proximos 60 dias</th>
    </tr>";
    if (count($finalizan)) {
        foreach ($finalizan as $resultado) {
            $cadena .="<tr>
            <td class='".clase($k)."'>".$resultado['renovacion']."</td>
            <td class='".clase($k)."'>
            <a href='javascript:muestra(".$resultado['idemp'].")' >"
            .$resultado['Nombre']."</a>
            </td></tr>";
            $k++;
        }
    } else {
        $hnocump++;
        $cadena.="<tr><td colspan='2' class='".clase($k++)."'>
        Nadie Finaliza contrato en los proximos 60 dias</td></tr>";
    }
    $cadena .= "</table>";
    return $cadena;
}
