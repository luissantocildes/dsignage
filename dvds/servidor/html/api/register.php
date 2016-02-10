<?php

if (!defined('DVDS'))
    die ("I can't include file");

/*********************************
 * Función que genera un id único para un nuevo cliente, lo registra en la base de datos
 * y lo envía al cliente
 *********************************/
function api_registrar_cliente ($params) {

    global $bbdd;

    // Genera un id y comprueba que no exista en la base de datos
    do {
	$id = uniqid ('DVDS');
	$sql = "SELECT id FROM clientes WHERE id = '$id'";
	$ids = $bbdd->query($sql);
    } while (count($ids));

    // Lee los datos del cliente
    $nombre = valor_array ($params, 'nombre', '');
    $ubicacion = valor_array ($params, 'ubicacion', '');
    $comentario = valor_array ($params, 'comentario', '');

    if ($nombre == '')
	die ("Falta nombre");
    if ($ubicacion == '')
	die ("Falta ubicacion");

    $cliente = $bbdd->execSQL ("INSERT INTO clientes (id, nombre, ubicacion, alta, estado, comentario) VALUES (?, ?, ?, now(), 1, ?)",
			    array(&$id, &$nombre, &$ubicacion, &$comentario), "ssss");

    echo $id;
}

?>
