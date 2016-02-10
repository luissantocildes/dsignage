<?php

function error_html ($cadena_error, $die = FALSE) {

echo "<pre>";
echo htmlentities ($cadena_error);
echo "</pre>";

if ($die)
    die ("");
}

function valor_array ($array, $clave, $defecto = '') {
    if (isset($array[$clave]))
	return $array[$clave];
    else
	return $defecto;
}

function existe_cliente ($bbdd, $cid) {
    $sql = "SELECT id FROM clientes WHERE id = '$cid'";
    $cliente = $bbdd->query($sql);
    return count($cliente) > 0;
}

function datos_cliente ($bbdd, $cid) {
    $sql = "SELECT * FROM clientes WHERE id = '$cid'";
    $cliente = $bbdd->query($sql);
    return $cliente[0];
}

function listado_clientes ($bbdd) {
    $sql = "SELECT * FROM clientes ORDER BY nombre";
    $clientes = $bbdd->query($sql);
    return $clientes;
}

function bloquea_cliente ($bbdd, $cid, $estado = TRUE) {
    $sql = "UPDATE clientes SET estado = ? WHERE id = ?";
    if ($estado) $aux = 1;
    else $aux = 0;
    echo $aux;
    $resultado = $bbdd->execSQL($sql, Array (&$aux, &$cid), "is");
    return $resultado;
}

function borra_cliente ($bbdd, $cid) {
    $sql = "DELETE FROM clientes WHERE id = ?";
    $resultado = $bbdd->execSQL($sql, Array (&$cid), "s");
    return $resultado;
}

function listado_medios ($bbdd) {
    $sql = "SELECT * FROM medios ORDER BY nombre";
    $medios = $bbdd->query($sql);
    return $medios;
}

function datos_medio ($bbdd, $id) {
    $sql = "SELECT * FROM medios WHERE id = $id";
    $medio = $bbdd->query($sql);
    if (count($medio))
	return $medio[0];
    else return FALSE;
}

function existe_medio ($bbdd, $nombre) {
    $sql = "SELECT id FROM medios WHERE nombre = '$nombre'";
    $medios = $bbdd->query($sql);
    return count($medios) > 0;
}

function borra_medio ($bbdd, $id) {
    $sql = "DELETE FROM playlist WHERE idMedio = $id";
    $ok1 = $bbdd->query($sql);
    $sql = "DELETE FROM medios WHERE id = $id";
    $ok2 = $bbdd->query($sql);
    return $ok1 & $ok2;
}

function listado_playlist ($bbdd) {
    $sql = "SELECT * FROM playlist ORDER BY nombre";
    $playlist = $bbdd->query($sql);
    return $playlist;
}

function existe_playlist ($bbdd, $nombre) {
    $sql = "SELECT id FROM playlist WHERE nombre = '$nombre'";
    $playlist = $bbdd->query($sql);
    return count($playlist) > 0;
}

function existe_playlist_id ($bbdd, $id) {
    $sql = "SELECT id FROM playlist WHERE id = ?";
    $playlist = $bbdd->query($sql, Array(&$id), "i");
    return count($playlist) > 0;
}

function crea_playlist ($bbdd, $nombre, $medios, $clientes) {
    $sql = "INSERT INTO playlist (nombre, alta) VALUES (?, now())";
    $resultado = $bbdd->query($sql, Array(&$nombre), "s");
    if (!$resultado) {
	$sql = "SELECT id FROM playlist WHERE nombre = ?";
	$resultado = $bbdd->query($sql, Array(&$nombre), "s");
	$id = $resultado[0]['id'];
	$sql = "INSERT INTO playMedio VALUES (?, ?, ?)";
	$orden = 0;
	foreach ($medios as $medio) {
	    $resultado = $bbdd->query($sql, Array(&$id, &$medio, &$orden), "iii");
	    $orden++;
	}
	$sql = "INSERT INTO clientePlaylist (idPlaylist, idCliente) VALUES (?, ?)";
	foreach ($clientes as $idCliente)
	    $resultado = $bbdd->query($sql, Array(&$id, &$idCliente), "is");
    } else { echo "Error"; };
}

function modifica_playlist ($bbdd, $id, $nombre, $medios, $clientes) {
    $sql = "UPDATE playlist set nombre=? WHERE id = ?";
    $resultado = $bbdd->query($sql, Array(&$nombre, &$id), "si");
    if (!$resultado) {
	$sql = "DELETE FROM playMedio WHERE idPlaylist = ?";
	$resultado = $bbdd->query($sql, Array(&$id), "i");
	$sql = "INSERT INTO playMedio VALUES (?, ?, ?)";
	$orden = 0;
	foreach ($medios as $medio) {
	    $resultado = $bbdd->query($sql, Array(&$id, &$medio, &$orden), "iii");
	    $orden++;
	}

	$sql = "DELETE FROM clientePlaylist WHERE idPlaylist = ?";
	$resultado = $bbdd->query ($sql, Array(&$id), "i");
	$sql = "INSERT INTO clientePlaylist (idPlaylist, idCliente) VALUES (?, ?)";
	foreach ($clientes as $idCliente)
	    $resultado = $bbdd->query($sql, Array(&$id, &$idCliente), "is");
    } else { echo "Error"; };
}

function borra_playlist ($bbdd, $id) {
    $sql = "DELETE FROM playlist WHERE id = ?";
    $resultado = $bbdd->query($sql, Array(&$id), "i");
    if (!$resultado) {
	$sql = "DELETE FROM playMedio WHERE idPlaylist = ?";
	$resultado = $bbdd->query($sql, Array(&$id), "i");

	$sql = "DELETE FROM clientePlaylist WHERE idPlaylist = ?";
	$resultado = $bbdd->query ($sql, Array(&$id), "i");
    } else { echo "Error"; };
}

function playlist_medios ($bbdd, $idPlaylist) {
    $sql = "SELECT * FROM playMedio WHERE idPlaylist = ? ORDER BY orden";
    $resultados = $bbdd->query($sql, Array(&$idPlaylist), "i");
    return $resultados;
}

function playlist_clientes ($bbdd, $idPlaylist) {
    $sql = "SELECT * FROM clientePlaylist WHERE idPlaylist = ?";
    $resultados = $bbdd->query($sql, Array(&$idPlaylist), "i");
    return $resultados;
}
?>