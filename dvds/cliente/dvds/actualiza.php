<?php

/***************************
 * Descarga los archivos nuevos en la carpeta download
 ***************************/

include ("/opt/dvds/config.php");

ignore_user_abort(true);
set_time_limit(0);

$config = parse_ini_file($baseFolder."dvds.conf");

$file = fopen ("/tmp/actualiza-dvds.lock", "w+");
if ($file) {
    if (flock ($file, LOCK_EX)) {
	fprintf ($file, "%d", getmypid());
	fflush ($file);
	flock ($file, LOCK_UN);
    }
    fclose ($file);
}

/*********************************
 * Conecta con el servidor y descarga la lista de videos
 *********************************/
$url = sprintf ("http://%s:%s/api/?f=media-list&cid=%s", $config['SERVIDOR'], $config['PUERTO'], $config['ID']);
$f = fopen ($url, 'r');
if ($f) {
    $playlist = json_decode (fgets($f), true);
    
    fclose($f);

    if (count($playlist)) {

    // Ahora se descargan los vídeos, si no están ya en la base de datos
    $bbdd = new SQLite3('/opt/dvds/dvds.db');
    foreach ($playlist as $video) {
	$sql = sprintf ("SELECT * FROM playlist WHERE id = %s", $video['id']);
	$resultado = $bbdd->query($sql);
	$datos = $resultado->fetchArray(SQLITE3_ASSOC);
	if ($datos == FALSE) { // El video no existe en la base de datos, se descarga y se guarda
	    $url = sprintf ("http://%s:%s/api/?f=get-media&cid=%s&id=%s", $config['SERVIDOR'], $config['PUERTO'], $config['ID'], $video['id']);
	    $destino = $downloadFolder . $video['fichero'];
	    // Descarga el archivo y comprueba que no hayan ocurrido errores
	    echo $destino."\r\n";
	    if (file_put_contents($destino, fopen($url, 'r')) !== FALSE) {
		$sql = sprintf ("INSERT INTO playlist (id, file, playorder, alta, start, stop) VALUES (%d, '%s', %d, '%s', '%s', '%s')",
				$video['id'], $video['fichero'], 0, $video['alta'], $video['start'], $video['stop']);
		$aux = $bbdd->exec($sql);
	    }
	}
    }

    // Una vez descargados los archivos, detiene el script de reproduccion y mueve los nuevos archivos a la
    // carpeta definitiva
    $cmd = $baseFolder . "detiene_dvds.sh";
    system($cmd);

    foreach ($playlist as $video) {
	$sql = sprintf ("SELECT * FROM playlist WHERE id = %s", $video['id']);
	$resultado = $bbdd->query($sql);
	$datos = $resultado->fetchArray(SQLITE3_ASSOC);
	if ($datos != FALSE) { // El video existe en la base de datos, se mueve
	    $origen = $downloadFolder . $video['fichero'];
	    $destino = $mediaFolder . $video['fichero'];
	    if (file_exists($origen))
		rename ($origen, $destino);
	}
    }

    system("/usr/bin/php $baseFolder/dvds.php > /dev/null &");

    }

} else {
    die ("No se puede conectar\r\n");
}

unlink ("/tmp/actualiza-dvds.lock");

?>