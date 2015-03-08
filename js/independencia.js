/**
 * Almacena el setTimeout;
 */
var timeOut;

/**
 * Funcion de consulta Ajax Generica
 * @param url
 * @param pars
 * @param div
 * @param callback Funcion a lanzar
 */
function ajaxPostRequest(url, pars, div, callback)
{
    var myAjax = new Ajax.Request(
        url,
        {
            method: 'post',
            parameters: pars,
            onComplete: function gen(t)
            {
                $(div).innerHTML = t.responseText;
                if (typeof callback === 'function') {
                    callback();
                }
            }
        }
    );
    return false;
}
/**
 * Funcion generica para mostrar la ventana
 */
function showWindow(div)
{
    var estilo = $(div).style;
    estilo.visibility = "visible";
    estilo.display = "block";
}
/**
 * Funcion generica para ocultar la ventana
 */
function hideWindow(div)
{
    var estilo = $(div).style;
    estilo.visibility = "hidden";
    estilo.display = "none";
}
/**
 * Valida al usuario
 * @returns {boolean}
 */
function validar()
{
    var usuario = $F('usuario');
    var passwd = $F('passwd');
    var url = "inc/validacion.php";
    var pars = "opcion=0&usuario="+usuario+"&passwd="+passwd;
    var myAjax = new Ajax.Request(url,
    {
        method:'post',
        parameters: pars,
        onComplete: function gen(t)
        {
            $('cuerpo').innerHTML = t.responseText;
        },
        onCreate: function gen(t)
        {
            $('cuerpo').innerHTML = '<center>' +
            '<p class="validacion">Validando Usuario<br/>' +
            '<img src="imagenes/loader.gif" alt="Validando Usuario" /></p></center>';
        }
    });
    return false;
}
/**
 * Carga el menu
 * @param codigo
 */
function menu(codigo)
{
    var url = "inc/generator.php";
    var pars = "opcion=0&codigo="+codigo;
    ajaxPostRequest(url, pars, 'principal');
}
/**
 * Busca
 */
function busca()
{
    var estilo = $('resultados').style;
    estilo.visibility = "visible";
    estilo.display = "block";
    var url = "inc/generator.php";
    var texto = $F('texto').toUpperCase();
    var tabla = $F('tabla');
    var pars = "opcion=1&texto="+texto+"&tabla="+tabla;
    pars = encodeURI(pars);
    ajaxPostRequest(url, pars, 'resultados');
}
/**
 * Cierra el formulario de busqueda
 * @deprecated Sustituir en origen por hideWindow(nombre div)
 */
function cierra_frm_busca()
{
    hideWindow('resultados');
}
/**
 * Cierra el formulario
 * @deprecated Sustituir en origen por hideWindow(nombre div)
 */
function cierra_el_formulario()
{
    hideWindow('formulario');
}
/**
 * Muestra el formulario
 * @deprecated Sustitur en origen por showWindow(nombre div)
 */
function muestra_el_formulario()
{
    showWindow('formulario');
}
/**
 * Muestra los resgistros seleccionados
 * @param registro
 */
function muestra(registro) //solo vale para las raices
{
    var url = "inc/generator.php";
    var tabla = $F('tabla');
    var pars = "opcion=2&registro="+registro+"&tabla="+tabla;
    ajaxPostRequest(url, tabla, pars, function() {
        campos_fecha(tabla);
        hideWindow('resultados');
        showWindow('formulario');
    });
}

/**
 * Muestra el submenu
 * @param codigo
 */
function submenu(codigo)
{
    var url = "inc/generator.php";
    var registro = $F('idemp');
    var pars = "opcion=3&codigo="+codigo+"&registro="+registro;
    ajaxPostRequest(url, pars, 'formulario', function() {
       campos_fecha($F('nombre_tabla'));
    });
}

/**
 * Funciona generica para los campos de fecha
 * @param tabla
 * @deprecated cambiar por html nativo
 */
function campos_fecha(tabla)
{
    var inputFields = [];
    switch(tabla) {
        case "facturacion":
            inputFields = ['finicio', 'duracion', 'renovacion'];
            break;
        case "pcentral":
        case "pempresa":
            inputFields = ['cumple'];
            break;
        case "z_facturacion":
            inputFields = ['finicio', 'renovacion'];
            break;
        case "empleados":
            inputFields = ['fnac', 'fcon'];
            break;
        case "entradas_salidas":
            inputFields = ['entrada', 'salida'];
            break;
        case "agenda":
            inputFileds = ['finc', 'ffin'];
            break;
    }
    for (var i = 0; i < inputFields.length; i++) {
        Calendar.setup({
            inputField: inputFields[i],      // id of the input field
            ifFormat: '%d-%m-%Y',       // format of the input field
            showsTime: true,            // will display a time selector
            button: 'f_trigger_' + inputFields[i],   // trigger for the calendar (button ID)
            singleClick: false,           // double-click mode
            step: 1
        });
    }
}
/**
 * Funcion nueva de actualización
 */
function actualiza_registro()
{
    muestra_debug();
    var url = "inc/generator.php";
    var registro = $F('numero_registro');
    var formulario = $('formulario_actualizacion');
    var pars = "opcion=4&"+Form.serialize(formulario);
    ajaxPostRequest(url, pars, 'debug', function() {
        muestra(registro);
        timeOut = setTimeout(hideWindow('debug'), 2000);
    });
}
/**
 * Muestra la ventana de depuracion
 * @deprecated
 */
function muestra_debug()
{
    $('debug').innerHTML = "";
    showWindow('debug');
}
/**
 * Cierra la ventana de depuracion
 * @deprecated
 */
function cierra_debug()
{
    hideWindow('debug');
}

//generacion de un nuevo registro
/**
 * Generacion de un nuevo registro
 * @param codigo
 */
function nuevo(codigo)
{
    var tabla = $F('tabla');
    var url = "inc/generator.php";
    var pars = "opcion=5&tabla="+codigo;
    ajaxPostRequest(url, pars, 'formulario', function() {
        campos_fecha(tabla);
        showWindow('formulario');
    });
}
/**
 * Agrega un nuevo registro
 */
function agrega_registro()
{
    var url = "inc/generator.php";
    var opcion = $F('opcion');
    var formulario = $('formulario_alta');
    var opciones = [2, 3, 4, 5, 6, 8, 11];
    muestra_debug();
    ajaxPostRequest(url, "opcion=6&" + Form.serialize(formulario), 'debug', function() {
        busca();
        timeOut = setTimeout(hideWindow('debug'), 2000);
        for (var i = 0; i < opciones.length; i++) {
            if (opcion === opciones[i]) {
                submenu(opcion);
            }
        }
    });
}
/**
 * Borrado del registro
 * @param registro
 */
function borrar_registro(registro)
{
    if (confirm("�Borrar Registro?")) {
        //var codigo = $F('codigo')
        var tabla = $F('nombre_tabla');
        var opcion = $F('opcion');
        var url = "inc/generator.php";
        var pars = "opcion=7&tabla="+tabla+"&registro="+registro;
        pars = encodeURI(pars);
        muestra_debug();
        ajaxPostRequest(url, pars, 'debug', function() {
            busca();
            timeOut = setTimeout(hideWindow('debug'), 2000);
            if (opcion !== 0) {
                submenu(opcion);
            } else {
                nuevo($F('nuevo'));
            }
        });
    }
}
/**
 * Muestra el registro seleccionado
 * @param registro
 */
function muestra_registro(registro)
{
    var codigo = $F('codigo');
    var tabla=$F('nombre_tabla');
    var opcion = $F('opcion');
    var url = "inc/generator.php";
    var pars = "opcion=3&codigo="+codigo+"&registro="+registro+"&tabla="+tabla+"&marcado=1";
    ajaxPostRequest(url, pars, 'formulario', function() {
        campos_fecha(tabla);
    });
}
//***********************************************************************************************/
//FACTURACION SERVICIOS FIJOS
//***********************************************************************************************/

/**
 * Formulario de servicios fijos
 * @param cliente
 */
function frm_srv_fijo(cliente)
{
    var url = "inc/generator.php";
    var pars = "opcion=8&cliente="+cliente;
    ajaxPostRequest(url, pars, 'frm_srv_fijos');
}
/**
 * Muestra el servicio fijo
 * @param id
 */
function muestra_srv_fijo(id)
{
    var url = "inc/generator.php";
    var pars = "opcion=8&id="+id;
    ajaxPostRequest(url, pars, 'frm_srv_fijos');
}
/**
 * Segun el servicio que cargamos se carga su importe y su iva al lado
 */
function cambia_los_otros()
{
    var url ="inc/generator.php";
    var servicio = $('servicio').value;
    var pars = "opcion=9&servicio="+servicio;
    var myAjax = new Ajax.Request(
        url,
        {
            method:'post',
            parameters: pars,
            onComplete: function gen(t)
            {
                var valores = t.responseText;
                var lista  = valores.split(":");
                $('importe').value = lista[0];
                $('iva').value = lista[1];
            }
        }
    );
}
/**
 * Agrega un servicio Fijo
 */
function agrega_srv_fijos()
{
    var url ="inc/generator.php";
    var cliente = $F('id_Cliente');
    var pars = "opcion=10&"+ Form.serialize($('frm_srv_fijos'));
    muestra_debug();
    ajaxPostRequest(url, pars, 'debug', function() {
        timeOut = setTimeout(hideWindow('debug'), 2000);
        submenu(2);
        frm_srv_fijo(cliente);
    });
}
/**
 * Borra un servicio fijo
 * @param id
 */
function borra_srv_fijo(id)
{
    var url ="inc/generator.php";
    var pars = "opcion=11&id="+id;
    muestra_debug();
    ajaxPostRequest(url, pars, 'debug', function() {
        timeOut = setTimeout(hideWindow('debug'), 2000);
        submenu(2);
    });
}
/**
 * Actualiza un servicio fijo
 */
function actualiza_srv_fijos()
{
    var url ="inc/generator.php";
    var cliente = $F('id_Cliente');
    var pars = "opcion=12&"+ Form.serialize($('frm_srv_fijos'));
    muestra_debug();
    ajaxPostRequest(url, pars, 'debug', function() {
        timeOut = setTimeout(hideWindow('debug'), 2000);
        submenu(2);
        frm_srv_fijo(cliente);
    });
}
// FIXME: Continuar desde aqui
//***********************************************************************************************/
//PARTE DE LAS COPIAS DE SEGURIDAD
//***********************************************************************************************/
/**
 * Muestra el formulario de Modificación de Contraseña de acceso
 */
function nuevaPass() {
    var url="inc/datos_gestion.php";
    var pars="opcion=18";
    var myAjax = new Ajax.Request(url,
            {
                method: 'post',
                parameters: pars,
                onComplete: function gen(respuesta)
                {
                    $('listado_copias').innerHTML = respuesta.responseText;
                }
    });
}
/**
 * Manda el valor de la nueva pass y si todo es correcto actualiza
 */
function estableceNuevaPass(){
    var url='inc/datos_gestion.php';
    var pars="opcion=19&"+Form.serialize($('nuevaPass'));
    var myAjax = new Ajax.Request(url,
            {
                method: 'post',
                parameters: pars,
                onComplete: function gen(respuesta)
                {
                    $('resultadoNuevaPass').innerHTML = respuesta.responseText;
                }
            });
}

function lista_backup()
{
    var url="inc/datos_gestion.php";
    var pars="opcion=0";
    var myAjax = new Ajax.Request(url,
        {
            method: 'post',
            parameters: pars,
            onComplete: function gen(respuesta)
            {
                $('listado_copias').innerHTML = respuesta.responseText;
            }
        });
}
//***********************************************************************************************/
function hacer_backup()
{
    var respuesta = confirm("Hacer Copia de Seguridad?");
    if (respuesta==true)
    {
        var url="inc/datos_gestion.php";
        var pars="opcion=1";
        var myAjax = new Ajax.Request(url,
            {
                method: 'post',
                parameters: pars,
                onComplete: function gen(respuesta)
                {
                    $('estado_copia').innerHTML = respuesta.responseText;
                    lista_backup();
                },
                onRequest: $('estado_copia').innerHTML = "<center>Generando Copia...<p><img src='imagenes/loader.gif' alt='Generando Copia ...' /></center>"
            });
    }
}
//***********************************************************************************************/
function restaurar_backup(archivo)
{
    var respuesta = confirm("Restaurar Copia?");
    if (respuesta==true)
    {
        var url="inc/datos_gestion.php";
        var pars="opcion=2&archivo="+archivo;
        var myAjax = new Ajax.Request(url,
            {
                method: 'post',
                parameters: pars,
                onComplete: function gen(respuesta)
                {
                    $('estado_copia').innerHTML = respuesta.responseText;
                },
                onRequest: $('estado_copia').innerHTML = "<center>Restaurando Copia...<p><img src='imagenes/loader.gif' alt='Restaurando Copia ...' /></center>"
            });
    }
}
//***********************************************************************************************/
function borrar_backup(archivo)
{
    var respuesta = confirm("Borrar Copia?");
    if (respuesta==true)
    {
        var url="inc/datos_gestion.php";
        var pars="opcion=3&archivo="+archivo;
        var myAjax = new Ajax.Request(url,
            {
                method: 'post',
                parameters: pars,
                onComplete: function gen(respuesta)
                {
                    $('estado_copia').innerHTML = respuesta.responseText;
                    lista_backup();
                },
                onRequest: $('estado_copia').innerHTML = "<center>Borrando Copia...<p><img src='imagenes/loader.gif' alt='Borrando Copia ...' /></center>"
            });
    }
}
function revisar_tablas()
{
    var url="inc/datos_gestion.php";
    var pars="opcion=4";
    var myAjax = new Ajax.Request(url,
            {
                method: 'post',
                parameters: pars,
                onComplete: function gen(respuesta)
                {
                    $('status_tablas').innerHTML = respuesta.responseText;
                    lista_backup();
                },
                onRequest: $('status_tablas').innerHTML = "<center>Revisando Tablas...<p><img src='imagenes/indicator.gif' alt='Revisando Tablas ...' /></center>"
            });
}
function reparar_tablas()
{
    var url="inc/datos_gestion.php";
    var pars="opcion=5";
    var myAjax = new Ajax.Request(url,
            {
                method: 'post',
                parameters: pars,
                onComplete: function gen(respuesta)
                {
                    $('status_tablas').innerHTML = respuesta.responseText;
                    lista_backup();
                },
                onRequest: $('status_tablas').innerHTML = "<center>Reparando Tablas...<p><img src='imagenes/indicator.gif' alt='Reparando Tablas ...' /></center>"
            });
}
function optimizar_tablas()
{
    var url="inc/datos_gestion.php";
    var pars="opcion=5";
    var myAjax = new Ajax.Request(url,
            {
                method: 'post',
                parameters: pars,
                onComplete: function gen(respuesta)
                {
                    $('status_tablas').innerHTML = respuesta.responseText
                    lista_backup();
                },
                onRequest: $('status_tablas').innerHTML = "<center>Optimizando Tablas...<p><img src='imagenes/indicator.gif' alt='Optimizando Tablas ...' /></center>"
            });
}
//*****************************************
//listado_telefonos() -> Genera el listado de telefonos del centro
//******************************************
function listado_telefonos()
{
    var url="inc/datos_gestion.php";
    var pars="opcion=10";
    var myAjax = new Ajax.Request(url,
        {
            method: 'post',
            parameters: pars,
            onComplete: function gen(respuesta)
            {
            $('listado_copias').innerHTML = respuesta.responseText;
            }
        });
}
//*****************************************
//formulario_telefonos() -> Genera el formulario para agregar telefonos
//******************************************
function formulario_telefonos()
{
    var url="inc/datos_gestion.php";
    var pars="opcion=11";
    var myAjax = new Ajax.Request(url,
        {
            method: 'post',
            parameters: pars,
            onComplete: function gen(respuesta)
            {
            $('listado_copias').innerHTML = respuesta.responseText;
            },
        onCreate: $('listado_copias').innerHTML = "<center><p class='validacion'>Generando Listado...<br/><img src='imagenes/loader.gif' alt='Generando Listado' /></p></center>"

        });
}
function agrega_telefono()
{
    var url="inc/datos_gestion.php";
    var pars="opcion=12&"+Form.serialize($('frm_agrega_telefono'));
    var myAjax = new Ajax.Request(url,
        {
            method: 'post',
            parameters: pars,
            onComplete: function gen(respuesta)
            {
                $('mensajes_estado').innerHTML = respuesta.responseText;
                formulario_telefonos();
            }
        });
}
function consulta_especial()//Observaciones de clientes de despachos con codigos de negocio
{
    var url="inc/datos_gestion.php";
    var pars="opcion=13";
    var myAjax = new Ajax.Request(url,
        {
            method: 'post',
            parameters: pars,
            onComplete: function gen(respuesta)
            {
            $('listado_copias').innerHTML = respuesta.responseText;
            },
        onCreate: $('listado_copias').innerHTML = "<center><p class='validacion'>Generando Listado...<br/><img src='imagenes/loader.gif' alt='Generando Listado' /></p></center>"
        });
}
function cierra_listado_copias()
{
    $('listado_copias').innerHTML = "";
}
//Para gestion muestra el listado de los tipos de clientes que hemos seleccionado
function filtra_listado()
{
    var url='inc/datos_gestion.php';
    pars='opcion=14&tipo='+$('tipo_cliente').value;
    var myAjax = new Ajax.Request(url,
        {
        method:'post',
        parameters: pars,
        onComplete:function gen(respuesta)
            {
            $('listado_copias').innerHTML = respuesta.responseText;
            },
        onCreate:$('listado_copias').innerHTML = "Generando listado"
    });
}
//***********************************************************************************************/
function datos(dato) //funcion qui iniica el proceso de muestra de cumplea�os
{
    var estilo = $('datos_interesantes').style;
    //directamente tenemos que recibir el contenido del fichero, ho hacen falta parametros
    estilo.visibility = "visible";
    pars='dato='+dato;
    if(dato==2)
        url='inc/cumples.php';
    else
        url='inc/datins.php';
    var myAjax = new Ajax.Request(url,
    {
        method: 'post',
        parameters: pars,
        onComplete: function gen(respuesta)
        {
            $('datos_interesantes').innerHTML = respuesta.responseText;
        }
    });
}
//***********************************************************************************************/
function cierralo()
{
    var estilo = $('datos_interesantes').style;
    estilo.visibility = "hidden";
}
//***********************************************************************************************/
function popUp(URL)
{
        window.open(URL, '" + id + "','toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=1,resizable=0,width=900,height=700');
}
//***********************************************************************************************/
function categorias(categoria) //carga las categorias y las muestra por pantalla
{
    pars='opcion=7&categoria='+categoria;
    url='inc/datos_gestion.php';
    var myAjax = new Ajax.Request(url,
    {
        method: 'post',
        parameters: pars,
        onComplete: function gen(respuesta)
        {
            $('listado_copias').innerHTML = respuesta.responseText;
        }
    });
}
//***********************************************************************************************/
function editar_categoria(registro)
{
    pars='opcion=8&categoria='+$F('categoria')+'&registro='+registro;
    url='inc/datos_gestion.php';
    var myAjax = new Ajax.Request(url,
    {
        method: 'post',
        parameters: pars,
        onComplete: function gen(respuesta)
        {
            $('detalles_categoria').innerHTML = respuesta.responseText;
        }
    });
}
//******************************EXTRANET Y DESVIOS*********************************/
//***********************************************************************************************/
function actualiza_categoria()
{
    pars='opcion=9&'+Form.serialize($('formulario_categorias'));
    url='inc/datos_gestion.php';
    var myAjax = new Ajax.Request(url,
    {
        method: 'post',
        parameters: pars,
        onComplete: function gen(respuesta)
        {
            //categorias($F('categoria'))
            $('resultadoActCategoria').innerHTML = respuesta.responseText;
            var p=setTimeout("categorias($F('categoria'))",2000);
        }
    });
}
//************************************GESTION CRUD********************************************/
function ver_detalles(opcion,accion,tipo,cliente) //muestra el formulario
{
url='inc/detalles.php'; //vamos a separar cosas
if (opcion != 0)
observacion = encodeURI($F('detalles_obs'));
else
observacion = "";
pars='opcion='+opcion+'&accion='+accion+'&tipo='+tipo+'&cliente='+cliente+'&observacion='+observacion;
var myAjax = new Ajax.Request(url,
    {
        method: 'post',
        parameters: pars,
        onComplete: function gen(respuesta)
        {
            $('edicion_actividad').innerHTML = respuesta.responseText;
        }
    });
}
//**************************************CIERRE FORMULARIO*****************************************/
function cierra_frm_observaciones() //oculta el formulario
{
    $('edicion_actividad').innerHTML = "";
}
//***********************************************************************************************/
//*********************************PARTE NUEVA TELECOS INTEGRADA*********************************/
function muestra_campo()
{
    url='inc/telecos.php'; //continuamos separando cosas
    campo =encodeURI($('servicio').value);
    pars='opcion=0&campo='+campo;
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters: pars,
            onComplete: function gen(respuesta)
            {
                $('tipo_teleco').innerHTML = respuesta.responseText
            }
        });
}
//**************CHEQUEAMOS EL VALOR INTRODUCIDO********************************/
function chequea_valor()
{
    var url="inc/telecos.php";
    var valor = $F('valor');
    var campo =encodeURI($('servicio').value);
    var pars="opcion=1&campo="+campo+"&valor="+valor;
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters: pars,
            onComplete: function gen(respuesta)
            {
                var estilo = $('valor').style;
                estilo.background = respuesta.responseText;
                var boton = $('boton_envio').style;
                    if (respuesta.responseText == "#ff0000") //no funciona pero lo dejo
                        boton.visibility = "hidden"; //si no es correcto no se visualiza el boton
                    else
                        boton.visibility = "visible";
            }
        });
}
//********************BUSQUEDA AVANZADA********************************************/
function busqueda_avanzada()
{
    var url="inc/bavanzada.php";
    pars='opcion=0&'+Form.serialize($('busqueda_avanzada'));
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters: pars,
            onComplete: function gen(respuesta)
            {
                $('resultados_busqueda_avanzada').innerHTML = respuesta.responseText;
            }
        });
}

/**
 * Muestra o oculta el panel de avisos
 * @return void
 */
function panelAvisos()
{
    $('tablaAvisos').toggle();
}


//Parte del tablon de los telefonos del centro*******************************************************/
function cerrar_tablon_telefonos()
{
    $('tablon_telefonos').innerHTML = "<input type='button' onclick='ver_tablon_telefonos()' value='[^] Ver Telefonos' />";
    var estilo = $('tablon_telefonos').style;
    estilo.height = "18px";
    estilo.width = "115px";
    estilo.overflow = "hidden";
}

function ver_tablon_telefonos()
{
var url='inc/avisos.php';
pars='opcion=1';
var estilo = $('tablon_telefonos').style;
estilo.height = "600px";
estilo.width = "900px";
estilo.overflow = "auto";
var myAjax = new Ajax.Request(url,
    {
    method:'post',
    parameters: pars,
    onComplete: function gen(respuesta)
    {
        $('tablon_telefonos').innerHTML = respuesta.responseText;
    }
});
}
//PARA ESTABLECER LOS PARAMETROS DE LA FACTURA
function parametros_factura(cliente)
{
    var estilo = $('parametros_factura').style;
    estilo.visibility = "visible";
    estilo.display = "block";
    var url='inc/parametros_factura.php';
    pars='opcion=0&cliente='+cliente;
    var myAjax = new Ajax.Request(url,
        {
        method:'post',
        parameters: pars,
        onComplete: function gen(respuesta)
        {
            $('parametros_factura').innerHTML = respuesta.responseText;
        }
    });
}
function cerrar_parametros_factura()
{
    var estilo = $('parametros_factura').style;
    estilo.visibility = "hidden";
    estilo.display = "none";
    $('parametros_factura').innerHTML = "";
}
function establecer_fecha(cliente)
{
    var url='inc/parametros_factura.php';
    pars='opcion=1&cliente='+cliente+'&dia='+$F('fecha_facturacion');
    var myAjax = new Ajax.Request(url,
        {
        method:'post',
        parameters: pars,
        onComplete: parametros_factura(cliente)
    });
}
function agrupar_servicio(cliente)
{
    var url='inc/parametros_factura.php';
    pars='opcion=2&cliente='+cliente+'&servicio='+$('servicio').value;
    var myAjax = new Ajax.Request(url,
        {
        method:'post',
        parameters: pars,
        onComplete: parametros_factura(cliente)
    });
}
function quitar_agrupado(id,cliente)
{
    var url='inc/parametros_factura.php';
    pars='opcion=3&id='+id;
    var myAjax = new Ajax.Request(url,
        {
        method:'post',
        parameters: pars,
        onComplete: parametros_factura(cliente)
    });
}
function ver_extensiones()
{
    var url='inc/telecos.php';
    pars='opcion=2&despacho='+$('despacho').value;
    var myAjax = new Ajax.Request(url,
        {
        method:'post',
        parameters: pars,
        onComplete: function gen(t)
        {
            $('s_extensiones').innerHTML = t.responseText;
        }
    });
}

/*
 * Borra el telefono que se queda libre
 */
function borrar_telefono_asignado(telefono)
{
    var url='inc/datos_gestion.php';
    pars ='opcion=15&telefono='+telefono;
    var myAjax = new Ajax.Request(url,
    {
        method:'post',
        parameters: pars,
        onComplete: function gen(t)
        {
            alert(t.responseText);
            formulario_telefonos();
        }
    });
}
/*
 * Edicion de la descripcion del telefono libre
 * @param telefono
 */
function editar_telefono_asignado(telefono)
{
    var url='inc/datos_gestion.php';
    var pars = 'opcion=16&telefono='+telefono;
    ajaxPostRequest(url, pars, 'edicion_' + telefono);
}
/**
 * Actualiza la descripcion del telefono libre
 * @param telefono
 */
function actualiza_descripcion_telefono(telefono)
{
    var url='inc/datos_gestion.php';
    var descripcion = $F('descripcion_'+ telefono);
    var id = $F('identificador_'+ telefono);
    var pars = 'opcion=17&telefono='+telefono+'&descripcion='+descripcion+'&id='+id;
    console.log( pars );
    var myAjax = new Ajax.Request(url,
    {
        method:'post',
        parameters: pars,
        onComplete:function gen(t)
        {
            formulario_telefonos();
        }
    });
}
