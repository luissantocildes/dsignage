<?php

include_once ("../include/config.php");
include_once ("../include/bbdd.php");
include_once ("../include/func.php");
include_once ("../include/plantilla.php");

$bbdd = new Database($config['bbdd_host'], $config['bbdd_user'], $config['bbdd_password'], $config['bbdd_name']);
if (!$bbdd->isConnected())
    error_html ("No se puede conectar a la base de datos", TRUE);

// Lee los parámetros enviados por POST, para determinar si se borra algun medio o
// se sube uno nuevo

$accion = valor_array($_POST, 'nueva', '') . valor_array($_POST, 'modificar', '') . valor_array($_POST, 'borrar', '');

$error = 0;
switch ($accion) {
    case 'delete': //Borra un playlist
	$ids = valor_array($_POST, 'playlist', '');
	foreach ($ids as $id) {
	    if (existe_playlist_id($bbdd, $id)) {
		borra_playlist($bbdd, $id);
	    }
	}
	
	break;

    case 'new': // Crea un playlist
	$nombre = valor_array($_POST, 'nombre', '');
	if (!existe_playlist($bbdd, $nombre)) {
	    crea_playlist($bbdd, $nombre, $_POST['medios'], $_POST['clientes']);
	} else { $error=1; }
	break;

    case 'modify': // Modifica un playlist
	$id = valor_array($_POST, 'playlist', '')[0];
	if (existe_playlist_id($bbdd, $id)) {
	    modifica_playlist($bbdd, $id, $_POST['nombre'], $_POST['medios'], $_POST['clientes']);
	}
	break;
}

// Obtiene la lista actual de medios
$playlists = listado_playlist($bbdd);
$medios = listado_medios($bbdd);
$clientes = listado_clientes($bbdd);

$bbdd->Disconnect();

?>
<html>
<head>
    <link href="dvds.css" rel="stylesheet" type="text/css">
    <script src="ajax.js"></script>
    <script>
	function mostrarPlaylist(datosPlayList) {
	    resultados = JSON.parse(datosPlayList);

	    // Pone el nombre
	    document.getElementById('nombre').value = playlist.options[playlist.selectedIndex].textContent;

	    // Marca los medios
	    lista = document.getElementById('medios');
	    lista.selectedIndex = -1;
	    for (c = 0; c < resultados.medios.length; c++) {
		for (d = 0; d < lista.options.length; d++)
		    if (lista.options[d].value == resultados.medios[c].idMedio)
			lista.options[d].selected = true;
	    }

	    // Marca los clientes
	    lista = document.getElementById('clientes');
	    lista.selectedIndex = -1;
	    for (c = 0; c < resultados.clientes.length; c++) {
		for (d = 0; d < lista.options.length; d++)
		    if (lista.options[d].value == resultados.clientes[c].idCliente)
			lista.options[d].selected = true;
	    }
	}

	function escogePlaylist(listado) {
	    if (listado.selectedIndex != -1) {
		aux = loadXMLDoc('http://srv.example.com/api/?f=playlist&id='+listado.options[listado.selectedIndex].value, function (a) {mostrarPlaylist(a);});
	    }
	}

	function compruebaDatos(idForm) {
	    formulario = document.getElementById(idForm);
	    if (formulario.nombre.value == '') {
		alert ("Escoja un nombre para la PlayList.");
		formulario.focus();
		return false;
	    }

	    selects = document.getElementById("medios");
	    if (selects.selectedIndex == -1) {
		alert ("Escoja uno o más médios para la PlayList.");
		selects.focus();
		return false;
	    }

	    selects = document.getElementById("clientes");
	    if (selects.selectedIndex == -1) {
		alert ("Escoja uno o más clientes para la PlayList.");
		selects.focus();
		return false;
	    }
	
	    return true;
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
    <h2>Playlists</h2>
    <form method="POST" action="" id="playlistForm" name="playlistForm">
    <table>
	<tr valign="top">
	    <td>
		<select multiple id=playlist name="playlist[]" size="20" width="250px" onchange="escogePlaylist(this);">
		<?php
		    foreach ($playlists as $elemento) {
			echo "<option value='{$elemento['id']}' class='optionPlay'>{$elemento['nombre']}</option>";
		    }
		?>
		</select>
	    </td>
	    <td>
		<button type="submit" name="nueva" id="nueva" value="new" onclick="if (compruebaDatos('playlistForm')) playlistForm.submit(); else return false;">&lt;- Nueva Playlist</button><br>
		<button type="submit" name="modificar" id="modificar" value="modify" onclick="if (compruebaDatos('playlistForm')) playlistForm.submit(); else return false;">&lt;- Modificar Playlist</button><br>
		<button type="submit" name="borrar" id="borrar" value="delete">Borrar Playlist</button>
	    </td>
	    <td>
		Nombre: <input type="text" id="nombre" name="nombre" value=""><br>
		Videos:
		<select multiple name="medios[]" id='medios' size="15" width="250px">
		<?php
		    foreach ($medios as $video) {
			echo "<option value='{$video['id']}' class='optionMedia'>{$video['nombre']}</option>";
		    }
		?>
		</select><br>
		Clientes:
		<select multiple name="clientes[]" id="clientes" size="15" width="250px">
		<?php
		    foreach ($clientes as $cliente) {
			echo "<option value='{$cliente['id']}' class='optionClient'>{$cliente['nombre']}</option>";
		    }
		?>
		</select><br>
	    </td>
	</tr>
    </table>
    </form>
    </div>
</div>
</body>