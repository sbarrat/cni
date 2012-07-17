<?php
/**
 * estadisticas.php File Doc Comment
 *
 * Generacion de consultas de estadisticas nuevas Julio 2008-Agosto 2008
 *
 * PHP Version 5.2.6
 *
 * @category servicont
 * @package  cni/servicont
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/
 *           Creative Commons Reconocimiento-NoComercial-SinObraDerivada 
 *           3.0 Unported
 * @link     https://github.com/independenciacn/cni
 */
require_once '../inc/variables.php';
require_once '../inc/Cni.php';
Cni::chequeaSesion();
if ( isset( $_SESSION['usuario']) ) {
    if ( isset( $_POST['opcion'] ) ) {
        switch ($_POST['opcion']) {
            case(0):
                $cadena = formulario( $_POST );
                break;//Generamos el formulario
            case(1):
                $cadena = respuesta( $_POST );
                break;//Generamos la respuesta
            case(2):
                $cadena = comparativas( $_POST );
                break;//Genera la pantalla de comparativa
        }
        echo $cadena;
    } else {
        echo "No se ha pasado opcion";
    }
} else {
    echo "No se ha iniciado sesion";
}
/**
 * Devuelve el nombre del cliente
 * 
 * @param  integer $cliente Id del Cliente
 * 
 * @return string          Nombre del Cliente
 */
function nombreCliente($cliente)
{
    if ($cliente) {
        $sql = "SELECT Nombre FROM clientes WHERE id LIKE :idcliente";
        $params = array(':idcliente' => $cliente);
        $resultado = Cni::consultaPreparada($sql, $params);
        return $resultado[0]['Nombre'];
    } else {
        return "Cliente No especificado";
    }
}
/**
 * Listado de Clientes
 * 
 * @return string Select de clientes
 */
function clientes()
{
    if (isset ( $_GET ['emp'] )) {
        $_SESSION ['wcliente'] = $_GET ['emp'];
        $cliente = $_SESSION ['cliente'];
    } else {
        $cliente = 0;
    }
    $sql = "SELECT id, Nombre FROM clientes ORDER BY Nombre";
    $resultados = Cni::consulta($sql);
    $form = "<select id='cliente' name='cliente'>";
    $form .= "<option value='0'>-Cliente-</option>";
    foreach ($resultados as $resultado) {
        $marcado = ($cliente == $resultado [0]) ? "selected" : "";
        if ( trim( $resultado [1] ) != "" ) {
            $form .= "<option value='".$resultado[0]."' $marcado >" .
            $resultado [1] ."</option>";
        }
    }
    $form .= "</select>";

    return $form;
}
/**
 * Listado de las categorias
 * 
 * @return string
 */
function categorias()
{
    $sql = "SELECT categoria FROM clientes GROUP BY categoria";
    $resultados = Cni::consulta($sql);
    $form = "<select id='categoria' name='categoria'>";
    $form .= "<option value='0'>-Categorias-</option>";
    foreach ($resultados as $resultado) {
        if (trim ( $resultado [0] ) != "") {
            $form .= "<option value='" . $resultado [0] . "' >" .
            $resultado [0] . "</option>";
        }
    }
    $form .= "</select>";

    return $form;
}
/**
 * Segun el modo en el que estamos devuelve un formato de fecha u otro
 * 
 * @param string $modo
 * @return string
 */
function fecha($modo)
{
    $form = dias ( $modo ) . mes ( $modo ) . anyo ( $modo );

    return $form;
}

/**
 * Devuelve 31 dias independientemente del mes marcado
 * 
 * @todo Generar funcion que dependiendo del mes y año de un valor u otro
 *         cal_days_in_month($calendar, $month, $year)
 * 
 * @param string $modo
 * @return string
 */
function dias($modo)
{
    switch ($modo) {
        case 0 :
            $tipo = "dia";
            break;
        case 1 :
            $tipo = "diaf";
            break;
        case 2 :
            $tipo = "rdia";
            break;
        case 3 :
            $tipo = "rdiaf";
            break;
    }
    $select = "<select id='". $tipo ."' name='". $tipo ."'>";
    $select .= "<option value='0'>-Dia-</option>";
    for ($i = 1; $i <= 31; $i ++) {
        $select .= "<option value='".$i."'>".$i."</option>";
    }
    $select .= "</select>";

    return $select;
}
/**
 * Opciones del campo select que muestran el mes
 * 
 * @param string $modo
 * @return string
 */
function mes($modo)
{
    switch ($modo) {
        case 0 :
            $tipo = "mes";
            break;
        case 1 :
            $tipo = "mesf";
            break;
        case 2 :
            $tipo = "rmes";
            break;
        case 3 :
            $tipo = "rmesf";
            break;
    }
    $select = "<select id='". $tipo ."' name='". $tipo ."'>";
    $select .= "<option value=0>-Mes-</option>";
    foreach (Cni::$meses as $key => $mes) {
        $select .= "<option value='". $key ."'>". $mes ."</option>";
    }
    $select .= "</select>";

    return $select;
}
/**
 * Muestra el select de los años desde el 2007 hasta el actual
 * 
 * @param string $modo
 * @return string
 */
function anyo($modo)
{
    $select = "";
    switch ($modo) {
        case 0 :
            $tipo = "ano";
            break;
        case 1 :
            $tipo = "anof";
            break;
        case 2 :
            $tipo = "rano";
            break;
        case 3 :
            $tipo = "ranof";
            break;
    }
    $select .= "<select id='" . $tipo . "' name='" . $tipo . "'>";
    $select .= "<option value='0'>-A&ntilde;o-</option>";
    $select .= "<option value='2007'>2007</option>";
    for ($i = 2008; $i <= date('Y'); $i++) {
        $select .= "<option value='" . $i . "'>" . $i . "</option>";
    }
    $select .= "</select>";

    return $select;
}
/**
 * Select de los servicios
 * 
 * @fixme Coge los datos de servicio del historico???
 * @return string
 */
function servicios()
{
    $sql = "SELECT TRIM(servicio) FROM historico
    group by TRIM(servicio) ORDER BY TRIM(servicio)";
    $resultados = Cni::consulta($sql);
    $form = "<select id='servicios' name='servicios'>";
    $form .= "<option value='0'>-Servicios-</option>";
    foreach ($resultados as $resultado) {
        $form .= "<option value='".trim($resultado[0])."' >".
            trim($resultado[0])."</option>";
    }
    $form .= "</select>";

    return $form;
}
/**
 * Se genera el formulario para la consulta por cliente
 *
 * @param array $vars
 * @todo Cyclomatic 11
 */
function formulario($vars)
{
    //Este al ser entre fechas por cliente en formulario
    //tenemos fechas y clientes
    //y devolvera servicios
    $cadena = "
        <form name='consulta' id='consulta' method='post'
            onsubmit='procesa();return false'>
            <input type='hidden' name='formu' id='formu' 
                value='".$vars['form']."'>";
    $inicioFin = "Inicio:".fecha(0)."Fin:".fecha(1);
    $formulario = array(
            0 => clientes().$inicioFin,
            1 => categorias().$inicioFin,
            2 => servicios()."Inicio:".$inicioFin,
            3 => clientes().servicios()."<br/>".$inicioFin,
            4 => categorias().servicios()."<br/>".$inicioFin,
            5 => servicios()."<br/>".$inicioFin,
            6 => clientes()."<br/>".$inicioFin,
            7 => comparativas( $vars )
            );
    $cadena .= $formulario[$vars['form']];
    if ($vars['form'] !=7) {
        $cadena.="<br /><input type='radio' name='tipo' value='acumulado'
            checked='checked'> Acumulado
            <input type='radio' name='tipo' value='detallado'> Detallado
            <input type='radio' name='tipo' value='comparativa'>Comparativa
            &nbsp;&nbsp;Limite Comparativa:";
        $cadena.="<select name='limite'>";
        for ($i=10; $i<=90; $i=$i + 10) {
            $cadena.="<option value=".$i.">".$i."</option>";
        }
        $cadena.="<option value=0>Todos</option>";
        $cadena.="</select>";
        $cadena.="<input type='submit' class='boton' value='Buscar'>";
    }
    $cadena.="</form>";
    $cadena.="<div id='resultados'></div>";

    return $cadena;
}

/**
 * Genera el formulario de las comparativas
 * 
 * @param array $vars
 * @return string
 */
function comparativas($vars)
{
    if (!isset($vars['tipo'])) {
        $cadena =
        "<input type='hidden' name='formu' id='formu' value='7' />
        <label for='tipo_comparativa'>Comparacion de:</label>
        <select name='tipo_comparativa' id='tipo_comparativa' 
            onchange='comparativa()'>
            <option value='0'>-- Opcion --</option>
            <option value='1'>Clientes</option>
            <option value='2'>Servicio</option>
            <option value='3'>Categoria</option>
        </select>
        <div id='comparativas'></div>";
    } else {
        switch ($vars['tipo']) {
            case(1):
                $cadena="Seleccione Cliente:".clientes();
            break;
            case(2):
                $cadena="Seleccione Servicio:";
            break;
            case(3):
                $cadena="Seleccione Categoria:".categorias();
            break;
        }
        $cadena .= servicios();
        $cadenaFechas ="
        <br />
        <label for='fecha_inicio_a'>Inicio Rango:</label>
        <input type='text' readonly size='10' id='fecha_inicio_a' 
            name='fecha_inicio_a' />
            <button type='button' class='calendario' id='boton_fecha_inicio_a'>
            </button>
        <label for='fecha_fin_a'>Fin Rango:</label>
        <input type='text' readonly size='10'id='fecha_fin_a' 
            name='fecha_fin_a' />
        <button type='button' class='calendario' id='boton_fecha_fin_a' >
            </button>
        <strong><-- Frente a --></strong>
        <label for='fecha_inicio_b'>Inicio Rango:</label>
        <input type='text' readonly size='10' id='fecha_inicio_b'
            name='fecha_inicio_b' />
        <button TYPE='button' class='calendario' id='boton_fecha_inicio_b'>
            </button>
        <label for='fecha_fin_b'>Fin Rango:</label>
        <input type='text' readonly size='10' id='fecha_fin_b' 
            name='fecha_fin_b' />
        <button type='button' class='calendario' id='boton_fecha_fin_b'>
            </button>";
        $cadena.=$cadenaFechas."<input type='submit' value='Comparar' />";
    }
    return $cadena;
}
/**
 * Generamos la respuesta dependiendo de los parametros que llegan
 * 
 * @param array $vars
 * @return string
 */
function respuesta($vars)
{
    $titulo = "Consumo mensual y acumulado entre fechas por";
    $titulos = array(
            0 => $titulo." cliente",
            1 => $titulo." categoria",
            2 => $titulo." servicios",
            3 => $titulo." clientes/servicio",
            4 => $titulo." categoria/servicio",
            5 => "Servicios mas facturados",
            6 => "Clientes con mas facturación",
            7 => "Comparativas",
            );
    $vars['titulo'] = $titulos[$vars['formu']];
    return consulta($vars);
}
/**
 * Consultas en pruebas //O cogemos del global de globales
 * 
 * @todo: Revisar las consultas
 * @todo: Cyclomatic 44
 */
function consulta($vars)
{
    var_dump($vars);
    $esql = null;
    $print = true;
    $conFecha = "";
    $limite = "";
    /**
     * Inicio Consulta Select
     */
    $sql = "SELECT";
    /**
     * Parte comun de las consultas de acumulado
     */
    $acumuladoSql = "
                SUM(h.cantidad) AS Unidades,
                SUM(h.cantidad * h.unitario) AS Importe,
                SUM(h.cantidad * h.unitario * h.iva / 100) AS Iva,
                SUM(
                    h.cantidad * h.unitario +
                    h.cantidad * h.unitario * h.iva / 100
                ) AS Total
                FROM `historico` as h
                INNER JOIN `regfacturas` AS c on h.factura = c.codigo
                INNER JOIN `clientes` AS l ON c.id_cliente = l.Id ";
    /**
     * Parte comun del no acumulado
     */
    $noAcumuladoSql = "
                h.cantidad AS Unidades,
                h.cantidad * h.unitario AS Importe,
                h.cantidad * h.unitario * h.iva / 100 AS Iva,
                (
                    h.cantidad * h.unitario +
                    h.cantidad * h.unitario * h.iva / 100
                ) AS Total
                FROM `historico` as h
                INNER JOIN `regfacturas` AS c on h.factura = c.codigo
                INNER JOIN `clientes` AS l ON c.id_cliente = l.Id ";
    /**
     * Comprobamos si la consulta tiene rangos de fechas
     */
    if ( consultaFecha($vars) != "" && $vars['formu'] != 5 ) {
        $conFecha = " AND ". consultaFecha($vars);
    } elseif ( $vars['formu'] == 5 ) {
        $conFecha = consultaFecha($vars);
    }
    /**
     * Comprobamos si la consulta tiene limite de registro
     */
    if ($vars['limite'] != 0) {
        $limite = " LIMIT ".$vars['limite']." ";
    }
    /**
     * FIXME Corregir las consultas y unificarlas
     */
    /**
     * Filtro de la categoria de la consulta
     */
    if ($vars['formu'] == 0) {
        if ($vars['tipo'] == "acumulado") {
            $sql .= "
                TRIM(h.servicio) AS Servicio,
                ".$acumuladoSql."
                WHERE c.id_cliente LIKE '".$vars['cliente']."' ".$conFecha."
                GROUP BY h.servicio";
        } else {
            $sql .= "
                c.fecha, 
                TRIM(h.servicio) AS Servicio,
                TRIM(h.obs) AS Observaciones, 
                h.cantidad AS Unidades,
                h.unitario AS Importe, 
                h.iva AS Iva,
                (
                    (h.unitario * h.cantidad) + 
                    (h.unitario * h.cantidad) * h.iva/100
                ) AS Total
                FROM `historico` AS h
                INNER JOIN `regfacturas` AS c on h.`factura` = c.`codigo`
                INNER JOIN `clientes` AS l ON c.id_cliente = l.Id
                WHERE c.id_cliente like '".$vars['cliente']."' ".$conFecha." 
                order by l.fecha";
        }
        $subtitulo = nombreCliente($vars['cliente']);
    }
    /**
     * Consultas entre fechas de categorias
     */
    if ($vars['formu'] == 1) {
        if ($vars['tipo'] == "acumulado") {
            $sql = "
                SELECT TRIM(h.servicio) AS Servicio,
                ".$acumuladoSql."
                INNER JOIN `entradas_salidas` AS e ON e.idemp like l.Id
                WHERE ( 
                    (e.salida >= c.fecha OR e.salida like '0000-00-00')
                    AND e.entrada <= c.fecha
                    AND e.Categoria like '".$vars['categoria']."'
                    ".$conFecha."
                ) GROUP BY h.servicio";
        } else {
            /**
             * ¿Por que de entradas y salidas????
             */
            $sql = "
                SELECT c.fecha, 
                s.Nombre, 
                TRIM(d.servicio) AS Servicio,
                TRIM(d.obs) AS Observaciones, 
                d.cantidad AS Unidades,
                d.unitario AS Importe,
                d.iva AS Iva, 
                (
                    (d.unitario * d.cantidad) + 
                    (d.unitario * d.cantidad) * d.iva / 100
                ) AS Total
                FROM entradas_salidas AS e
                INNER JOIN clientes AS s ON e.idemp LIKE s.Id
                INNER JOIN regfacturas AS c ON e.idemp LIKE c.id_cliente
                INNER JOIN historico AS d ON c.codigo LIKE d.factura
                WHERE (
                    (e.salida >= c.fecha OR e.salida LIKE '0000-00-00')
                    AND e.entrada <= c.fecha
                    AND e.categoria LIKE '".$vars['categoria']."' 
                    ".$conFecha."
                ) ORDER BY c.fecha, s.Nombre";
        }
        $subtitulo = $vars['categoria'];
    }
    /**
     * Consumo mensual y acumulado entre fechas por servicios, control
     * de almacenaje y fijos
     */
    if ($vars['formu'] == 2) {
        if ($vars['tipo'] == "acumulado") {
            $sql = "
                SELECT 
                ".$acumuladoSql."
                WHERE TRIM(h.servicio) LIKE '".$vars['servicios']."' 
                ".$conFecha."
                GROUP BY h.servicio";
        } else {
            $sql = "
                SELECT c.fecha, 
                s.nombre AS Cliente, 
                d.obs AS Observaciones,
                d.cantidad AS Unidades, 
                d.unitario AS Importe, 
                d.iva AS Iva,
                (
                    (d.unitario * d.cantidad) + 
                    (d.unitario * d.cantidad) * d.iva / 100
                ) AS Total
                FROM `historico` AS d
                INNER JOIN `regfacturas` AS c ON c.`codigo` = d.`factura`
                INNER JOIN `clientes` AS s ON c.id_cliente = s.Id
                WHERE TRIM(d.servicio) LIKE '".$vars['servicios']."' 
                ".$conFecha."
                ORDER BY c.fecha";
        }
        $subtitulo = $vars['servicios'];
    }
    /**
     * Consumo mensual y acumulados entre fechas por cliente/servicio
     */
    if ($vars['formu'] == 3) {
        if ($vars['tipo'] == "acumulado") {
            $sql = "
                SELECT 
                ".$acumuladoSql."
                WHERE TRIM(h.servicio) LIKE '".$vars['servicios']."' 
                ".$conFecha."
                AND c.id_cliente LIKE '".$vars['cliente']."' 
                GROUP BY h.servicio";
        } else {
            $sql = "
                SELECT c.fecha, 
                d.obs AS Observaciones, 
                d.cantidad AS Unidades,
                d.unitario AS Importe, 
                d.iva AS Iva,
                (
                    (d.unitario * d.cantidad) + 
                    (d.unitario * d.cantidad) * d.iva / 100
                ) AS Total
                FROM `historico` AS d
                INNER JOIN `regfacturas` AS c ON c.`codigo` = d.`factura`
                INNER JOIN `clientes` AS s ON c.id_cliente = s.Id
                WHERE TRIM(d.servicio) LIKE '".$vars['servicios']."' 
                ".$conFecha."
                AND c.id_cliente LIKE '".$vars['cliente']."' 
                ORDER BY c.fecha";
        }
        $subtitulo = nombreCliente($vars['cliente'])." / ".$vars['servicios'];
    }
    /**
     * Consumo mensual y acumulado entre fechas por categoria/servicio
     */
    if ($vars['formu'] == 4) {
        if ($vars['tipo'] == "acumulado") {
            $sql="
                SELECT 
                ".$acumuladoSql."
                WHERE TRIM(h.servicio) LIKE '".$vars['servicios']."' 
                ".$conFecha."
                AND l.Categoria LIKE '".$vars['categoria']."' 
                GROUP BY h.servicio";
        } else {
            $sql="
                SELECT c.fecha, 
                s.Nombre AS Cliente, 
                d.obs AS Observaciones,
                d.cantidad AS Unidades, 
                d.unitario AS Importe, 
                d.iva AS IVA,
                (
                    (d.unitario * d.cantidad) + 
                    (d.unitario * d.cantidad) * d.iva / 100
                ) AS Total
                FROM `historico` AS d
                INNER JOIN `regfacturas` AS c ON c.`codigo` = d.`factura`
                INNER JOIN `clientes` AS s ON c.id_cliente = s.Id
                WHERE TRIM(d.servicio) LIKE '".$vars['servicios']."'
                ".$conFecha." 
                AND s.Categoria LIKE '".$vars['categoria']."'
                ORDER by c.fecha";
        }
        $subtitulo = nombreCliente($vars['categoria'])." / ".$vars['servicios'];
    }
	/**
     * Servicios por volumen de facturacion + facturados
     */
    if ($vars['formu'] == 5) {
        if ($vars['tipo'] == "acumulado") {
            $sql="
                SELECT 
                TRIM(h.servicio) AS Servicio,
                ".$acumuladoSql."
                ".$conFecha." 
                GROUP BY TRIM(h.servicio) 
                ORDER BY Total DESC";
        } else {
            if ($vars['tipo'] == "detallado") {
                $sql="
                   SELECT c.fecha, 
                   TRIM(d.servicio) AS Servicio,
                   TRIM(d.obs) AS Observaciones, 
                   s.Nombre AS Cliente,
                   d.cantidad AS Unidades, 
                   d.unitario AS Importe, 
                   d.iva AS Iva,
                   (
                        (d.unitario * d.cantidad) + 
                        (d.unitario * d.cantidad) * d.iva / 100
                   ) AS Total
                   FROM `historico` AS d
                   INNER JOIN `regfacturas` AS c ON c.`codigo` = d.`factura`
                   INNER JOIN `clientes` AS s ON c.id_cliente = s.Id
                    ".$conFecha." order by c.fecha";
        	} else {
            	if ( $vars['servicios'] != "0") {
                    $filtraServicio = " AND TRIM(h.servicio)
                    LIKE TRIM('".$vars['servicios']."') ";
        		} else {
                    $filtraServicio = "  ";
                }
                $rango = arrayRangos($vars);
                foreach ($rango as $rangillo) {
                    $esql[]="
                        SELECT TRIM(h.servicio) AS Servicio,
                        SUM(
                            h.cantidad * h.unitario + 
                            h.cantidad * h.unitario * h.iva / 100
                        ) AS Total
                        FROM `historico` AS h
                        INNER JOIN `regfacturas` AS c ON h.factura = c.codigo
                        INNER JOIN `clientes` AS l ON c.id_cliente = l.Id
                        ".$rangillo." ".$filtraServicio." 
                        GROUP BY TRIM(h.servicio)
                        ORDER BY Total DESC ".$limite;
                }
            }
        }
        $subtitulo = "";
    }
    /**
     * Clientes por volumen de facturacion + facturados
     */
    if ($vars['formu'] == 6) {
        if ($vars['tipo'] == "acumulado") {
        	$sql="
        		SELECT l.Nombre AS Cliente, 
            	".$acumuladoSql."
        		".$conFecha." 
        		GROUP BY l.Nombre ORDER BY Total DESC";
        } else {
            if ($vars['tipo'] == "detallado") {
        		$sql="
        		    SELECT c.fecha, 
        		    s.Nombre AS Cliente,
        			TRIM(d.servicio) AS Servicio, 
        		    TRIM(d.obs) AS Observaciones,
        			d.cantidad AS Unidades, 
        		    d.unitario AS Importe, 
        		    d.iva AS Iva,
        			(
        		        (d.unitario * d.cantidad) + 
        		        (d.unitario * d.cantidad) * d.iva / 100
        		    ) AS Total
        			FROM `historico` AS d
        			INNER JOIN `regfacturas` AS c ON c.`codigo` = d.`factura`
        			INNER JOIN `clientes` AS s ON c.id_cliente = s.Id
        			".$conFecha." order by c.fecha";
            } else {
                /**
                 * Comparativa entre rangos
                 */
            	if ($vars['cliente'] != 0) {
                    $filtra_cliente = " 
                    	    AND c.id_cliente 
                    	    LIKE ".$vars['cliente']." ";
            	} else {
                    $filtra_cliente = "";
            	}
            }
            $rango = arrayRangos($vars);
            foreach ($rango as $rangillo) {
                    $esql[]="
                        SELECT l.Nombre AS Cliente,
                    	SUM(
                            h.cantidad * h.unitario + 
                            h.cantidad * h.unitario * h.iva / 100
                        ) AS Total
                    	FROM `historico` AS h
                    	INNER JOIN `regfacturas` AS c ON h.factura = c.codigo 
                        INNER JOIN `clientes` AS l ON c.id_cliente = l.Id
                    	".$rangillo." ".$filtra_cliente."
                    	GROUP BY l.Nombre 
                    	ORDER BY Total DESC ".$limite;
            }
        }
        $subtitulo = "";
    }
    /**
     * Nueva seccion de Comparativas
     */
    if ($vars['formu'] == 7) {
        switch ($vars['tipo_comparativa']) {
            /**
             * Clientes
             */
            case 1:
            	$sql="
            	    SELECT l.Nombre AS Cliente,
            		SUM(
            	        h.cantidad * h.unitario + 
            	        h.cantidad * h.unitario * h.iva / 100
            	        ) AS Total
            		FROM `historico` AS h
            		INNER JOIN `regfacturas` AS c ON h.factura = c.codigo
            		INNER JOIN `clientes` AS l ON c.id_cliente = l.Id";
            	if ($vars['cliente'] != 0) {
                	$filtro = " 
                		    AND c.id_cliente LIKE ".$vars['cliente']." ";
                	$grupo = " GROUP BY l.Nombre";
            	} else {
                	$sql = preg_replace(
                			"#l.Nombre AS Cliente,#",
                		    " 1 ,",
                		    $sql
                		    );
                	$filtro = " ";
            	}
            	if ($vars['servicios'] != "0") {
                	$filtro .= " AND TRIM(h.servicio) LIKE 
                		    TRIM('".$vars['servicios']."') ";
            	}
            	break;
			/**
			 * Servicios
			 */
            case 2:
            	$sql="
            	    SELECT TRIM(h.servicio) AS Servicio,
                	SUM(
            	        h.cantidad * h.unitario + 
            	        h.cantidad * h.unitario * h.iva/100
            	    ) AS Total
                	FROM `historico` AS h
                	INNER JOIN `regfacturas` AS c ON h.factura = c.codigo
                	INNER JOIN `clientes` AS l ON c.id_cliente = l.Id";
            	if ($vars['servicios'] != "0") {
                	$filtro = " AND TRIM(h.servicio) LIKE 
                	        TRIM('".$vars['servicios']."') ";
                	$grupo = " GROUP BY TRIM(h.servicio)";
            	} else {
                	$sql="
                    	SELECT 1, 
                    	SUM(h.cantidad * h.unitario + 
                	        h.cantidad * h.unitario * h.iva / 100) AS Total
                    	FROM `historico` as h
                    	INNER JOIN `regfacturas` AS c ON h.factura = c.codigo
                    	INNER JOIN `clientes` AS l ON c.id_cliente = l.Id";
                	$filtro = "  ";
            	}
            	break;
			/**
			 * Categorias
			 */
            case 3:
            	$sql="
            	    SELECT 1, 
            	    SUM(
            	        h.cantidad * h.unitario + 
            	        h.cantidad * h.unitario * h.iva / 100
            	    ) AS Total
            		FROM `historico` AS h
            		INNER JOIN `regfacturas` AS c ON h.factura = c.codigo 
            	    INNER JOIN `clientes` AS l ON c.id_cliente = l.Id";
            	if ($vars['categoria'] != "0") {
                	$filtro = " AND l.Categoria LIKE '".$vars['categoria']."' ";
                	$grupo = " GROUP BY l.Categoria";
            	} else {
               		$sql = preg_replace(
               		        "#l.Categoria as Categoria,#",
               		        " 1 ,",
               		        $sql
               		        );
                	$filtro = "  ";
            	}
            	if ($vars['servicios'] != "0") {
                	$filtro .= " AND TRIM(h.servicio) 
                	        LIKE TRIM('".$vars['servicios']."') ";
            	}
            	break;
        }
        /**
		 * Rango de las fechas de la comparativa
         */
        $rangoA = generaConsultas(
        	$vars['fecha_inicio_a'],
        	$vars['fecha_fin_a']
        	);
	    $subtitulo = "Datos del ".$vars['fecha_inicio_a']." al ".
	        	$vars['fecha_fin_a']." de ";
        if ( is_array($rangoA) ) {
        	foreach ($rangoA as $rango) {
            	$rango .= "-1";
            	$esql[] = $sql." WHERE YEAR('".$rango."') 
            		LIKE YEAR(c.Fecha)
            		AND MONTH('".$rango."') LIKE MONTH(c.fecha)  
            		".$filtro.$grupo;
        	}
        }
        if ($vars['fecha_inicio_b'] != '' && $vars['fecha_fin_b']!='') {
            $rangoB = generaConsultas(
            	    $vars['fecha_inicio_b'],
            	    $vars['fecha_fin_b']
            	    );
            $subtitulo2 = "Datos del ".$vars['fecha_inicio_b']." al ".
            	$vars['fecha_fin_b'];
            if ( is_array($rangoB) ) {
            	foreach ($rangoB as $rango) {
                	$rango .= "-1";
                	$esql2[] = $sql." WHERE YEAR('$rango') LIKE YEAR(c.Fecha)
                		AND MONTH('$rango') LIKE MONTH(c.fecha)  
                		".$filtro.$grupo;
            	}
            }
        }
    }
    /**
     * Ejectuamos las consultas
     */
	if ($vars['formu'] != 7) {
    	$_SESSION['consulta'] = $sql;
    	$_SESSION['titulo'] = $vars['titulo']." - ".$subtitulo;
    	if ( is_array($esql) ) {
        	$cadena .= generaTablaComparativas($esql, $vars, $subtitulo);
        	$print = false;
    	} else {
        	$cadena = Cni::generaTablaDatos(
        	        $sql,
        	        $vars['titulo']." - ".$subtitulo
        	    );
    	}
    	$_SESSION['sqlQuery'] = $sql;
    	if ($print) {
        	$cadena .= "<br/><span class='boton'
            	onclick='window.open(\"print.php\",\"_self\")'>Imprimir</span>";
    	}
	} else {
    	if ( is_array($esql) ) {
        	$cadena .= generaTablaComparativasMejorada(
        	        $esql,
        	        $vars,
        	        $subtitulo
        	        );
        	$print = false;
        	if ( is_array($esql2) ) {
        		$cadena.="</tr></table><br/>";
        		$cadena.=generaTablaComparativasMejorada(
        		        $esql2,
        		        $vars,
        		        $subtitulo2
        		        );
        		$print = false;
        	}
    	}
    	$cadena .= "</tr></table>";
    	$_SESSION['sqlQuery'] = $sql;
    	if ($print) {
        	$cadena .= "<br/><span class='boton'
            	onclick='window.open(\"print.php\",\"_self\")'>Imprimir</span>";
    	}
    	//session_start();
    	unset($_SESSION['acumulado']);
    	unset($_SESSION['datos_ant']);
	}
    return $cadena;
}
/**
 * Genera consultas.
 * 
 * @todo No se muy bien que hace
 * @param string $inicio fecha Inicio rango
 * @param string $fin fecha Fin rango
 * @return string
 */
function generaConsultas($inicio, $fin)
{
    $params = array(':inicio' => $inicio, ':fin' => $fin);
    $sql = "SELECT YEAR(fecha) FROM `regfacturas`
        WHERE ( 
            DATE_FORMAT(fecha, '%d-%m-%Y) >= :inicio 
            AND DATE_FORMAT(fecha, '%d-%m-%Y) <= :fin
        ) 
        GROUP BY YEAR(fecha)
        ORDER BY YEAR(fecha)";
    $anyosInicio = Cni::consultaPreparada($sql, $params);
    foreach ($anyosInicio as $anyo) {
        $sql = "SELECT MONTH(fecha) FROM `regfacturas` 
            WHERE (
                DATE_FORMAT(fecha, '%d-%m-%Y) >= :inicio 
                AND DATE_FORMAT(fecha, '%d-%m-%Y) <= :fin 
                AND YEAR(fecha) LIKE :anyo
            )
            GROUP BY MONTH(fecha) 
            ORDER BY MONTH(fecha)";
        $params[':anyo'] = $anyo;
        $meses = Cni::consultaPreparada($sql, $params);
        foreach ($meses as $mes) {
            $mesAnyo[] = $anyo."-".$mes;
        }
    }
    return $mesAnyo;
}
/**
 * Para las comparativas, una consulta por mes
 * 
 * @param array $vars
 * @return array
 * @todo: Cyclomatic 8
 */
function arrayRangos($vars)
{
    /**
     * Si no se ha marcado ningun año
     */
    if ($vars['ano']==0 && $vars['anof']==0) {
        $sql = "SELECT YEAR(fecha) 
            FROM `regfacturas` 
            GROUP BY YEAR(fecha)";
        $resultados = Cni::consulta($sql);
        foreach ($resultados as $resultado) {
            if ($resultado[0] >= '2008') {
                $cadena[] = " WHERE YEAR(c.Fecha) lIKE ".$resultado[0]." ";
            } else {
                $cadena[] = " WHERE MONTH(c.Fecha) >= 8 
                    AND YEAR(c.Fecha) LIKE ".$resultado[0]." ";
            }
        }
    }
    /**
     * Si se han marcado los dos años
     */
    if ($vars['ano']!=0 && $vars['anof']!=0) {
        $sql = "SELECT MONTH(fecha) FROM regfacturas
                    WHERE YEAR(fecha) LIKE :anyo";
        for ($i=$vars['ano']; $i<=$vars['anof']; $i++) {
            if ($i == 2007 ) {
                $sql .= " AND MONTH(fecha) >= 8 ";
            }
            $sql .= " GROUP BY MONTH(fecha) ";
            $resultados = Cni::consultaPreparada($sql, array(':anyo' => $i));
            foreach ($resultados as $resultado) {
                $cadena[]= " WHERE MONTH(c.Fecha) 
                    LIKE ".$resultado[0]."
                    AND YEAR(fecha) LIKE ".$i;
            }
        }
    }
    return $cadena;
}

/**
 * Generación de la consulta con las fechas
 * 
 * @param array $vars
 * @return string
 * @todo: Cyclomatic 16
 */
function consultaFecha($vars)
{
    $check = 0;
    $cadena = "";
    if ($vars['diaf']==0 && $vars['mesf']==0 && $vars['anof']==0) {
        if ($vars['dia']!=0) {
            $cadena.= " DAY(c.Fecha) LIKE ".$vars['dia']." ";
            $check = 1;
        }
        if ($vars['mes']!=0) {
            if ($check == 1) {
                $cadena.= " AND ";
            }
            $cadena.=" MONTH(c.Fecha) LIKE ".$vars['mes']." ";
            $check=1;
        }
        if ($vars['ano']!=0) {
            if ($check == 1) {
                $cadena.= " AND ";
            }
            $cadena.=" YEAR(c.Fecha) LIKE ".$vars['ano']." ";
            $check=1;
        }
    } else {
        if ($vars['dia']!=0) {
            $cadena.= " DAY(c.Fecha) >= ".$vars['dia']." ";
            $check = 1;
        }
        if ($vars['mes']!=0) {
            if ($check == 1) {
                $cadena.= " AND ";
            }
            $cadena.=" MONTH(c.Fecha) >= ".$vars['mes']." ";
            $check=1;
        }
        if ($vars['ano']!=0) {
            if ($check == 1) {
                $cadena.= " and ";
            }
            $cadena.=" YEAR(c.Fecha) >= ".$vars['ano']." ";
            $check=1;
        }
        if ($vars['diaf']!= 0) {
                $cadena.= " AND DAY(c.Fecha) <= ".$vars['diaf']." ";
        }
        if ($vars['mesf']!= 0) {
                $cadena.= " AND MONTH(c.Fecha) <= ".$vars['mesf']." ";
        }
        if ($vars['anof']!= 0) {
                $cadena.= " AND YEAR(c.Fecha) <= ".$vars['anof']." ";
        }
    }
    if ($vars['formu']== 5 && $cadena != "") {
        $cadena= " WHERE ".$cadena;
    }

    return $cadena;
}
/**
 * Generacion de las tablas de las comparativas
 * 
 * @param unknown_type $sql
 * @param unknown_type $vars
 * @param unknown_type $subtitulo
 * @return string
 * @todo: Cyclomatic 16
 */
function generaTablaComparativas($sql, $vars, $subtitulo)
{
    $clavesAnteriores = null;
    $estadoAnterior = null;
    $i=0;
    foreach ($sql as $key => $esquel) {
        $titulo[]=generamosTitulo($esquel);
        $resultados = Cni::consulta($esquel);
        foreach ($resultados as $resultado) {
            $subdatos[$resultado[0]] = $resultado[1];
        }
        $datos[$i] = $subdatos;
        $i++;
        unset($subdatos);
    }
    $cadena = "Tabla de comparativas ".$subtitulo;
    $cadena .= "";
    /**
     * Contador de titulo
     */
    $k=0;
    /**
     * Llenamos la tabla de claves
     */
    foreach ($datos as $key => $dato) {
        $claves[0] = "";
        $datillos[0] = "";
        if ( is_array($dato) ) {
            foreach ($dato as $clave => $datillo) {
                $claves[] = $clave;
            }
        }
        /**
         * Llenamos la tabla de datos
         */
        if ( is_array($dato) ) {
            foreach ($dato as $clave => $datillo) {
                $datillos[] = $datillo;
                $estad[$clave] = $datillo;
            }
        }
    }
    $cadena .= "
        <tr>
            <th colspan='10'height='2px'>".$titulo[$k]."</th>
        </tr>";
    /**
     * A partir de aqui en columnas de 10
     */
    $cadena .= "<tr>";
    echo count($dato);
    /**
     * @todo En el caso de las comparativas al mostrar solo 1 categoria
     * Solo sale columna, esto es lo que hay que arreglar
     */
    for ($j = 1; $j <= count($dato); $j++) {
        $cadena .= "<th>".$j."</th>";
        /**
         * Llegamos al valor 10 y saltamos
         */
        if ($j%10 == 0 || $j == count($dato)) {
            $cadena.="</tr><tr>";
            /**
             * Aqui recorremos 10 veces la tabla que almacena las claves
             */
            if ($j == count($dato) && $j % 10 != 0 ) {
                $ciclos = count($dato) % 10;
            } else {
                $ciclos = 10;
            }
            for ($l = $j - $ciclos; $l <= $j - 1; $l++) {
                if ($vars['formu'] == 6) {
                    $cadena .= "<td class='par' valign='top'>
                        <b>".$claves[$l+1]."</b></td>";
                } else {
                    $cadena .= "<td class='par' valign='top'>
                        <b>".$claves[$l+1]."</b></td>";
                }
            }
            $cadena .= "</tr><tr>";
            for ($l = $j - $ciclos; $l <= $j - 1; $l++) {
                $cadena .= "<td class='impar'><b>" .
                Cni::formateaNumero($datillos[$l+1], true).
                "</b></td>";
            }
            $cadena .= "</tr><tr>";
            /**
             * Diferencia de facturacion
             */
            for ($l = $j -$ciclos; $l <= $j - 1; $l++) {
                $posicion = diferencia(
                        $claves[$l + 1],
                        $estadoAnterior,
                        $estad
                    );
                $cadena .= "<td class='par'>".$posicion."</td>";
            }
            /**
             * Diferencia de posicion
             */
            $cadena .= "</tr><tr>";
            for ($l = $j - $ciclos; $l <= $j - 1; $l++) {
                $posicion = posicion(
                        $l + 1,
                        $clavesAnteriores,
                        $claves[ $l + 1]
                    );
                $cadena .= "<td class='impar'>".$posicion."</td>";
            }
            $cadena.="</tr><tr>";
        }
        $clavesAnteriores = $claves; //Para la comparativa
        $datillos_ant = $datillos; //Para la comparativa
        $estadoAnterior = $estad; //Para la comparativa
        unset($claves);
        unset($datillos);
        unset($estad);
        $cadena.="</tr>";
        $k++;
    }
    $cadena.="</tabla>";

    return $cadena;
}
/**
 * Posiciona los valores en el array para su comparacion
 * 
 * @param string $l posicion actual
 * @param array $claves array de claves anteriores 
 * @param string $clave valor de clave en la pos
 * @return Ambigous <number, string>
 */
function posicion($l, $claves, $clave)
{
    if ( is_array($claves)) {
    	$pos = array_search($clave, $claves) - $l;
    	if ( $pos > 0 ) {
        	$pos="<font color='green'><b><- ".$pos."</b></font>";
    	} else {
        	if ( $pos < 0 ) {
            	$pos = $pos * -1;
            	if ( array_search($clave, $claves) ) {
            		$pos = "<font color='red'><b>-> ".$pos."</b></font>";
            	} else {
            		$pos = "--Sin datos--";
            	}
        	} else {
            	$pos = "<b>=</b>";
    		}
    	}
    } else {
    	$pos = "--Sin datos--";
    }
    return $pos;
}
/**
 * Devuelve el valor formateado segun la diferencia
 * 
 * @param unknown_type $aguja
 * @param unknown_type $pajarAnt
 * @param unknown_type $pajar
 * @return string
 */
function diferencia($aguja, $pajarAnt, $pajar)
{
    $html = "--Sin datos--";
    $valor = 0;
    $color = false;
    if ( is_array($pajarAnt) && is_array($pajar) ) {
        if ( array_key_exists($aguja, $pajarAnt)
            && array_key_exists($aguja, $pajar) ) {
            $valor = $pajar[$aguja] - $pajarAnt[$aguja];
            if ( $valor > 0 ) {
                $color = 'green';
            } elseif( $valor < 0 ) {
                $color = 'red';
            } else {
                $color = 'black';
            }
            $html = "
                <font color='".$color."'>
                    <strong>".Cni::formateaNumero($valor, true)."</strong>
                </font>";
        }
    }
    return $html;
}
/**
 * Se genera el titulo de la tabla
 * 
 * @param string $sql
 * @return string
 */
function generamosTitulo($sql)
{
    $primeraParte = explode("WHERE MONTH(c.Fecha) LIKE", $sql);
    if ($primeraParte[1] != "") {
        $segundaParte = explode("AND YEAR(fecha) LIKE", $primeraParte[1]);
    } else {
        $segundaParte = explode("YEAR(c.Fecha) LIKE", $primeraParte[0]);
    }
    $terceraParte = explode("GROUP BY", $segundaParte[1]);
    if ($primeraParte[1] != "") {
        $titulo = Cni::$meses[intval($segundaParte[0])]."-".$terceraParte[0];
    } else {
        $titulo = $terceraParte[0];
    }
    return $titulo;
}
/**
 * Generamos la tabla de las comparativas tabla chunga 2.0
 * 
 * @param string $sql
 * @param array $vars
 * @param string $subtitulo
 * @return string
 * @todo: Cyclomatic 14
 */
function generaTablaComparativasMejorada($sql, $vars, $subtitulo)
{
    $i = 0;
    $j = 0;
    $l = 0;
    $acumulado = 0;
    $dato_ant = 0;
    foreach ($sql as $key => $esquel) {
        $titulo [] = generamosTituloComparativa ( $esquel );
        $resultados = Cni::consulta( $esquel );
        foreach ($resultados as $resultado) {
            $subdatos[$resultado[0]] = $resultado[1];
        }
        $datos[$i] = $subdatos;
        $i++;
        unset( $subdatos );
    }
    $cadena = "
        <div class='nuevas_comparativas'>
            <h3>Tabla de comparativas " . $subtitulo . "</h3>";
    foreach ($titulo as $tit) {
        $cadena .= "<div class='tit_compa'>
        <div class='titulo'>" . $tit . "</div>";
        $matriz = $datos [$j];
        if (is_array ( $matriz )) {
            foreach ($matriz as $key => $dato) {
                $cadena .= "<div class='dato_impar'>" .
                     Cni::formateaNumero($dato, true) . "</div>";
                $diferencia = round ( $dato - $dato_ant, 2 );
                $acumulado = $acumulado + $dato;
                $dato_ant = $dato;
            }
        } else {
            $cadena .= "<div class='dato_impar'>--Sin datos--</div>";
            $dato = 0;
        }
        $datos_ant [] = $dato;
        if (isset ( $_SESSION ['datos_ant'] )) {
            if (($dato != 0) && ($_SESSION ['datos_ant'] [$l] != 0)) {
                $porcentaje = Cni::formateaNumero(
                        ($dato * 100 / $_SESSION ['datos_ant'] [$l]) - 100
                    );
                if ($porcentaje > 0) {
                    $mmi = "<font color='green'>" . $porcentaje . "%</font>";
                } elseif ($porcentaje == 0) {
                    $mmi = $porcentaje . "%";
                } else {
                    $mmi = "<font color='red'>" . $porcentaje . "%</font>";
                }
                $cadena .= "<div class='dato_par'>" . $mmi . "</div>";
            } else {
                $cadena .= "<div class='dato_par'>--Sin Datos--</div>";
        	}
        }
        $l++;
        $cadena .= "</div>";
        $j++;
    }
    /**
     * Tabla Totales
     */
    $cadena .= "
        <div class='tit_compa'>
            <div class='titulo'>Acumulado</div>
                <div class='dato_impar'>" .
                    Cni::formateaNumero($acumulado, true) .
                "</div>";
    if (isset ( $_SESSION ['acumulado'] )) {
        $total = Cni::formateaNumero($acumulado - $_SESSION ['acumulado']);
        if ($_SESSION ['acumulado'] != 0) {
            $porcentaje = Cni::formateaNumero(
                    ($acumulado * 100 / $_SESSION ['acumulado']) - 100
                );
            if ($porcentaje > 0) {
                $mmi = "<font color='green'>" . $porcentaje . "%</font>";
            } elseif ($diferencia == 0) {
                $mmi = $porcentaje;
            } else {
                $mmi = "<font color='red'>" . $porcentaje . "%</font>";
            }
        } else {
            $mmi = "--Sin datos--";
        }
        $cadena .= "<div class='dato_par'>" . $mmi . "</div>";
        $cadena .= "</div>";
    } else {
        $_SESSION ['acumulado'] = $acumulado;
        $_SESSION ['datos_ant'] = $datos_ant;
    }
    $cadena .= "</div>";

    return $cadena;
}
/**
 * Genera el titulo de la Comparativa
 * 
 * @todo Otra que tampoco se que hace
 * @param string $sql
 * @return string
 */
function generamosTituloComparativa($sql)
{
    $cadena = explode ( "year('", $sql );
    $cadena1 = explode ( "-1')", $cadena [1] );
    $cadena2 = Cni::cambiaFormatoFecha($cadena1[0]);
    $cadena3 = explode ( "-", $cadena2 );
    return Cni::$meses[$cadena3 [1]] . " / " . $cadena3 [2];
}

