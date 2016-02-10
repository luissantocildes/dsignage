<?php

/****************************************************
 * Procesa las llamadas a la API del sistema de distribucion de video
 ****************************************************/

define ('DVDS', 1);

require_once ('../../include/config.php');
require_once ($config['include_path'] . 'func.php');
require_once ($config['include_path'] . 'bbdd.php');
include_once ($config['api_path'] . 'register.php');
include_once ($config['api_path'] . 'media.php');
include_once ($config['api_path'] . 'playlist.php');

// Conecta con la base de datos
$bbdd = new Database($config['bbdd_host'], $config['bbdd_user'], $config['bbdd_password'], $config['bbdd_name']);
if (!$bbdd->isConnected())
    error_html ("No se puede conectar a la base de datos", TRUE);

// Lee la función solicitada a la API
$funcion = valor_array($_GET, 'f', '');

// Recupera el ID del cliente
$cid = valor_array($_GET, 'cid', '');

// Recupera el resto de parámetros
$params = $_GET;

// Ejecuta la función solicitada
switch ($funcion) {
    case 'register': // registrar un cliente
	api_registrar_cliente($params);
	break;

    case 'media-list': // Obtener la lista de archivos disponibles
	api_listado_medios($cid);
	break;

    case 'get-media': // Obtener un archivo
	api_enviar_archivo($cid, $params);
	break;

    case 'playlist': // Obtiene los datos de un playlist determinado
	api_enviar_playlist($params);
	break;

    default: // devuelve un mensaje de error
	error_html("Función '$funcion' no disponible");
	break;
}

$bbdd->Disconnect();

?>