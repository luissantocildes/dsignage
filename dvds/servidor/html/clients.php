<?php

include_once ("../include/config.php");
include_once ("../include/bbdd.php");
include_once ("../include/func.php");
include_once ("../include/plantilla.php");

// Conecta con la base de datos
$bbdd = new Database($config['bbdd_host'], $config['bbdd_user'], $config['bbdd_password'], $config['bbdd_name']);
if (!$bbdd->isConnected())
    error_html ("No se puede conectar a la base de datos", TRUE);

// Lee los parámetros enviados por POST, para determinar si se borra algun medio o
// se sube uno nuevo
$accion = valor_array($_POST, 'delete', valor_array($_POST, 'lock', ''));
switch ($accion) {
    case 'delete':
	$ids = valor_array($_POST, 'cliente', '');
	if (is_array($ids)) {
	    foreach ($ids as $id)
		borra_cliente ($bbdd, $id);
	}
	break;

    case 'lock': // Bloquea un cliente
	$ids = valor_array($_POST, 'cliente', '');
	if (is_Array($ids)) {
	    foreach ($ids as $id) {
		if (existe_cliente ($bbdd, $id)) {
		    $cliente = datos_cliente($bbdd, $id);
		    bloquea_cliente ($bbdd, $id, $cliente['estado'] == 1 ? FALSE : TRUE);
		}
	    }
	}
	break;
}

// Obtiene la lista actual de medios
$clientes = listado_clientes($bbdd);

$bbdd->Disconnect();

?>
<html>
<head>
    <link href="dvds.css" rel="stylesheet" type="text/css">
    <script src="ajax.js"></script>
    <script>
	function cambia_estado(origen, nombreClase) {
	    objetos = document.getElementsByClassName(nombreClase);
	    for (c = 0; c < objetos.length; c++) {
		objetos[c].checked = origen.checked;
	    }
	}

	function confirmar(texto, nombreClase) {
	    objetos = document.getElementsByClassName(nombreClase);
	    total = 0
	    for (c = 0; c < objetos.length; c++) {
		if (objetos[c].checked) total++;
	    }
	    if (total) {
        	return confirm("\u00BFDesea "+texto+" los clientes seleccionados?");
    	    } else {
        	alert ("Escoja un cliente para "+texto+".");
        	return false;
    	    }
    }
    </script>
</head>
<body>
<div id="menulateral">
    <div id="contenidoMenu">
    <?php
	echo plantilla_menu_principal();
    ?>
    </div>
</div>
<div id="panel-contenido">
    <div id="contenido">
    <h2>Clientes</h2>
    <form method="POST" action="" name="clientes">
    <table id="tabla">
	<tr>
	    <th><input type='checkbox' id='selectorCliente' name='selectorCliente' onchange='cambia_estado(this, "checkcliente");'></th>
	    <th>Nombre</th>
	    <th>Id</th>
	    <th>Ubicaci&oacute;n</th>
	    <th>Fecha Alta</th>
	    <th>&Uacute;ltima conexi&oacute;n</th>
	    <th>Comentario</th>
	</tr>
	<?php
	    foreach ($clientes as $datoCliente) {
		if ($datoCliente['estado'] == 0)
		    echo "<tr style='background-color: #FFe0E0;'>";
		else
		    echo "<tr>";
		echo "<td><input type='checkbox' class='checkcliente' id='cliente[]' name='cliente[]' value='".$datoCliente['id']."'></td>";
		echo "<td>{$datoCliente['nombre']}</td>";
		echo "<td>{$datoCliente['id']}</td>";
		echo "<td>{$datoCliente['ubicacion']}</td>";
		echo "<td>{$datoCliente['alta']}</td>";
		echo "<td>{$datoCliente['ultimaConexion']}</td>";
		echo "<td>{$datoCliente['comentario']}</td>";
		echo "</tr>";
	    }
	?>
	<tr>
	    <td colspan=7><img src="img/arrow_ltr.png">Para los elementos seleccionados:
		<button name="delete" value="delete" type="submit" onclick="if (confirmar('borrar', 'checkcliente')) {clientes.submit();} else return false;">Eliminar</button>
		<button name="lock" value="lock" type="submit" onclick="if (confirmar('bloquear', 'checkcliente')) {clientes.submit();} else return false;">Bloquear/Desbloquear</button>
	    </td>
	</tr>
    </table>
    </form>
    </div>
</div>
</body>