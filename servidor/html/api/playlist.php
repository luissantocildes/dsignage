<?php

if (!defined('DVDS'))
    die ("I can't include file");

/************************************************************
 * enviar_playlist
 * Función que devuelve un listado de los medios disponibles
 * para un playlist en formato JSON
 *
 * Parámetros de entrada:
 *	- $cid: ID del cliente
 *	- $params: Demás parámetros enviados por el cliente
 ************************************************************/
function api_enviar_playlist ($params) {

    global $bbdd;

    // Lee el id pasado y solicita los datos del playlist
    $id = valor_array($params, 'id', 0);
    if ($id && existe_playlist_id($bbdd, $id)) {
	$resultado['medios'] = playlist_medios($bbdd, $id);
	$resultado['clientes'] = playlist_clientes($bbdd, $id);
	header('Content-Type: application/json');
	echo json_encode($resultado);
    }

}
?>