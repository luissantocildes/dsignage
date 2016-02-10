<?php

if (!defined('DVDS'))
    die ("I can't include file");

/************************************************************
 * listado_medios
 * Función que devuelve un listado de los medios disponibles
 * para un cliente en formato JSON
 *
 * Parámetros de entrada:
 *	- $cid: ID del cliente
 *	- $params: Demás parámetros enviados por el cliente
 ************************************************************/
function api_listado_medios ($cid) {

    global $bbdd;

    // Primero se comprueba que el cliente esté registrado
    if (existe_cliente ($bbdd, $cid)) {
	// El cliente tiene archivos disponibles
//	$sql = "SELECT * FROM
    }
}
?>