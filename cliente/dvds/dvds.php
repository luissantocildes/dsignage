<?php

/**************************************
 * Script que lanza el reproductor omxplayer, reproduciendo los
 * vídeos en el orden indicado en la base de datos
 **************************************/
declare(ticks = 1); 
$terminar = FALSE;

/*************************************
 * Función que captura y procesa una señal
 *************************************/
function signal_handler ($signal) {
    global $terminar;

    switch ($signal) {
	case SIGUSR1:
	    $terminar = TRUE;
	    break;
    }
}

$mediaFolder = '/opt/dvds/media/';
$omxplayer = '/usr/bin/omxplayer';

// Verifica que el script no esté funcionando
if (file_exists('/tmp/dvds.lock'))
    die ('El script ya está funcionando');

// si el script no está funcionando, crea el archivo de bloqueo y guarda el PID del proceso
$file = fopen ("/tmp/dvds.lock", "w+");
if ($file) {
    if (flock ($file, LOCK_EX)) {
	fprintf ($file, "%d", getmypid());
	fflush ($file);
	flock ($file, LOCK_UN);
    }
    fclose ($file);
}

// Configura el nuevo handler de las señales
pcntl_sigprocmask(SIG_UNBLOCK, array(SIGUSR1));
if (!pcntl_signal (SIGUSR1, "signal_handler"))
    echo "Error al instalar la funcion\r\n";

// Conecta con la base de datos
$bbdd = new SQLite3('/opt/dvds/dvds.db');

while (!$terminar) {
    // Obtiene los ficheros que se han de reproducir
    $resultado = $bbdd->query ("select * from playlist where start <= datetime('now') and (stop is null or stop >= datetime('now', 'localtime'))");
    while (!$terminar && $fichero = $resultado->fetchArray(SQLITE3_ASSOC)) {
	print_r ($terminar);
	$cmd = $omxplayer . ' --win 0,0,200,200 ' . $mediaFolder . $fichero['file'];
	system ($cmd);
    }
}

// Elimina el archivo de bloqueo
unlink ("/tmp/dvds.lock");

?>