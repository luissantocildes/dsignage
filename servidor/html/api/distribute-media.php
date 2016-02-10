<?php

include "../include/config.php";

$bbdd = mysqli_connect ($config['bbdd_host'], $config['bbdd_user'], $config['bbdd_password'], $config['bbdd_name']);
if (mysqli_connect_error()) {
    die ('Error de Conexión (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
}

$uid = mysqli_real_escape_string ($bbdd, $_GET['uid']);
$ip = $_SERVER['REMOTE_ADDR'];

// verifica que el cliente exista
$sql = "SELECT * FROM clientes WHERE id = '".$uid."'";
$resultados = mysqli_query($bbdd, $sql);

if (mysqli_num_rows($resultados) == 1) { // el cliente existe
    // Comprobar si hay archivos para descargar
    $sql = "SELECT * FROM medios WHERE `id-cliente` = '$uid' AND `activo` = 1 ORDER BY id DESC";
    $resultados = mysqli_query ($bbdd, $sql);

    $total_archivos = mysqli_num_rows($resultados);
    if ($total_archivos) { // Uno o más archivos para descargar
	// De momento solo descarga el último archivo disponible
	$archivos = mysqli_fetch_array ($resultados, MYSQLI_ASSOC);

	$file = $config['media_path'] . $archivos['fichero'];
	if (file_exists($file)) {
	    header('Content-Description: File Transfer');
	    header('Content-Type: application/octet-stream');
	    header('Content-Disposition: attachment; filename='.basename($file));
	    header('Expires: 0');
	    header('Cache-Control: must-revalidate');
	    header('Pragma: public');
	    header('Content-Length: ' . filesize($file));
	    readfile($file);
	    exit;
	} else {
	    echo "1";
	}
    } else {
	echo "0";
    }
} else {
    echo "El cliente no existe";
}



mysqli_close ($bbdd);

?>
