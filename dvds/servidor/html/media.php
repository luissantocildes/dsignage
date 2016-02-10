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
$action = valor_array($_POST, 'action', '');

switch ($action) {
    case "delete": // Borra un archivo
	$medialist = valor_array ($_POST, 'medialist', '');
	if (is_array($medialist))
	    foreach($medialist as $id_medio) { // Busca cada medio en la base de datos
		$datos_medio = datos_medio($bbdd, $id_medio);
		if ($datos_medio) { // si existe, borra el archivo y la base de datos
		    @unlink ($config['media_path'] . $datos_medio['fichero']);
		    borra_medio ($bbdd, $id_medio);
		}
	    }
		break;
    case "upload": // sube un archivo
	$tmpfolder = $config['tmp_path'];
	$upload = $config['media_path'] . basename($_FILES['fichero']['name']);

	if (move_uploaded_file($_FILES['fichero']['tmp_name'], $upload)) {
	    // Se ha movido el archivo a la carpeta de medios, ahora se añade a la base de datos
	    $nombre = valor_array($_POST, 'nombre', '');
	    $comentario = valor_array($_POST, 'comentario', '');
	    $size = filesize($upload);
	    $activo = 1;
	    $tipo = 'v';

	    $sql = "INSERT INTO medios (fichero, nombre, longitud, alta, activo, tipo, comentario) VALUES (?, ?, ?, now(), ?, ?, ?)";
	    $valores = Array(&$_FILES['fichero']['name'], &$nombre, &$size, &$activo, &$tipo, &$comentario);
	    $aux = $bbdd->execSQL ($sql, $valores, "ssiiss");
	} else {
	    echo "Error al mover el archivo";
	}
		break;
}

// Obtiene la lista actual de medios
$medios = listado_medios($bbdd);

$bbdd->Disconnect();

?>
<html>
<head>
    <link href="dvds.css" rel="stylesheet" type="text/css">
</head>
<body>
<script>
    function confirmaborrado(nombreLista) {
	lista = document.getElementsByName(nombreLista);
	if (lista[0].selectedIndex != -1) {
	    return confirm("\u00BFDesea eliminar los archivos seleccionados?");
	} else {
	    alert ("Escoja un archivo a borrar.");
	    return false;
	}
    }

    function subearchivo() {
	nombre = document.getElementById('nombre');
	if (nombre.value == '') {
	    alert ("Escriba el nombre del vídeo");
	    nombre.focus();
	    return false;
	}

	fichero = document.getElementById('fichero');
	if (fichero.value == '') {
	    alert ("Escoja el archivo a subir");
	    fichero.focus();
	    return false;
	}

	document.getElementById('capa-espera').style.visibility='visible';
	return true;
    }
</script>
<div id="menulateral">
    <div id="contenidoMenu">
    <?php
	echo plantilla_menu_principal();
    ?>
    </div>
</div>
<div id="panel-contenido">
    <div id="contenido">
    <h2>Lista de medios</h2>
    <form action="" method="POST" name="media"  enctype="multipart/form-data">
	<input type="hidden" name="action" value="">
    <table>
	<tr>
	    <td>Medios</td>
	    <td></td>
	    <td></td>
	</tr>
	<tr>
	    <td rowspan=2>
		<select name="medialist[]" size="20" multiple="multiple">
	    <?php
		foreach ($medios as $archivo) {
		    echo "<option value=\"".$archivo['id']."\">\r\n";
		    printf ("%s (%s - %d bytes)\r\n", $archivo['nombre'], $archivo['fichero'], $archivo['longitud']);
		    echo "</option>\r\n";
		}
	    ?>
		</select>
	    </td>
	    <td align="center" valign="top">
		<input type="button" value="Subir nuevo archivo" onclick="media.action.value='upload'; if (subearchivo()) media.submit();"><br>
		<input type="button" value="Borrar archivo" onclick="media.action.value='delete'; if (confirmaborrado('medialist[]')) media.submit();">
	    </td>
	    <td valign="top" style="border: solid 1px #c0c0c0;">
		<input type="hidden" name="MAX_FILE_SIZE" value="2000000000" />
		Nombre del v&iacute;deo: <input type="text" name="nombre" id="nombre"><br>
		Fichero: <input type="file" id="fichero" name="fichero"><br>
		Comentario: <textarea id="comentario"></textarea>
	    </td>
	</tr>
	<tr><td></td><td></td></tr>
    </table>
    </form>
    </div>
</div>
<div id="capa-espera">Subiendo un archivo... Espere, por favor</div>
</body>