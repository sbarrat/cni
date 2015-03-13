<?php
/**
 * Muestra la tabla de los despachos con su ocupación o no
 */
require_once '../inc/autoload.php';
$con = new Connection();
$sql = "SELECT z.valor, c.Nombre, c.id ,c.Categoria
    FROM clientes AS c  JOIN z_sercont AS z ON c.id like z.idemp
    WHERE  Estado_de_cliente != 0 AND
    c.Categoria LIKE '%despacho%' AND
    z.servicio like 'Codigo Negocio'
    order by z.valor asc";
$resultados = $con->consulta($sql);
$despachos = array();
$clase = array();
$cliente = array();
$cadena = "";
$despacho = 0;
$filas = 6;
$columnas = 6;
foreach ($resultados as $resultado) {
    $despachos[intval($resultado['valor'])] = $resultado['Nombre'];
    $clase[intval($resultado['valor'])] = "despacho_ocupado";
    $cliente[intval($resultado['valor'])] = $resultado['id'];
}
?>
<table id='agenda' width='100%'>
<?php
for ($i = 0; $i < $filas; $i++) :
    ?>
    <tr>
    <?php
    for ($j = 0; $j < $columnas; $j++) :
        $despacho ++;
        $titulo = ($despacho == 23) ? "Sala de Juntas" : "Despacho ".$despacho;
        $txtDespacho = "</div>";
        $value = "";
        if (isset($cliente[$despacho]) && $cliente[$despacho] != '') {
            $txtDespacho .= "<div class='".$clase[$despacho]."' height='100%'>".
                $despachos[$despacho]."<br/>
                <span class='mini_boton'
                onclick='informacion_cliente(".$cliente[$despacho].")'>
                &nbsp;+Info&nbsp;</span>";
                $value = $cliente[$despacho];
        }
        $txtDespacho .= "<input type='hidden' id='cliente_despacho_".$despacho."'
            value='".$value."' />";
        ?>
        <td width='16.66%' id='despacho_<?php echo $despacho ?>' valign='top'>
            <div class='cabezera_despacho'>
                <?php echo $titulo ?>
                <?php echo $txtDespacho ?>
            </div>
        </td>
        <?php
    endfor; ?>
    </tr>
    <?php
endfor; ?>
</table>
