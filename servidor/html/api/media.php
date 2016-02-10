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
	$sql = "select m.id, m.fichero, m.longitud, m.alta, c.start, c.stop from playlist p, clientePlaylist c, playMedio pm, medios m where c.idCliente = ? and p.id = c.idPlaylist and p.activa = 1 and p.id = pm.idPlaylist and pm.idMedio = m.id order by p.id, orden, start";
	$playlist = $bbdd->query($sql, Array(&$cid), "s");
	header('Content-Type: application/json');
        echo json_encode($playlist);
    }
}

function api_enviar_archivo ($cid, $params) {
    global $bbdd;
    global $config;

    // Comprueba que el cliente esté registrado y que exista el archivo a descargar
    $id = valor_array($params, 'id', -1);

    if (existe_cliente ($bbdd, $cid) && existe_medio_id ($bbdd, $id)) {
	$datos = datos_medio ($bbdd, $id);
	$archivo = $config['media_path'] . $datos['fichero'];
	if (file_exists($archivo)) {
/*	    http_send_content_disposition($datos['fichero'], true);
	    http_send_content_type("application/force-download");
	    http_throttle(0.1, 2048);
	    http_send_file($archivo);
*/
	    header('Content-Description: File Transfer');
	    header('Content-Type: application/force-download');
	    header('Content-Disposition: attachment; filename='.$datos['fichero']);
	    header('Expires: 0');
	    header('Cache-Control: must-revalidate');
	    header('Pragma: public');
	    header('Content-Length: ' . filesize($archivo));
	    readfile($archivo);
	} else {
	    http_response_code(404);
	    echo "Ok";
	}
    }
}
?>