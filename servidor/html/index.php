<?php

include_once ("../include/config.php");
include_once ("../include/bbdd.php");
include_once ("../include/func.php");
include_once ("../include/plantilla.php");

// Conecta con la base de datos
$bbdd = new Database($config['bbdd_host'], $config['bbdd_user'], $config['bbdd_password'], $config['bbdd_name']);
if (!$bbdd->isConnected())
    error_html ("No se puede conectar a la base de datos", TRUE);

$bbdd->Disconnect();

?>
<html>
<head>
    <link href="dvds.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="menulateral">
    <div id="contenidoMenu">
    <?php
	echo plantilla_menu_principal();
    ?>
    </div>
</div>
<div id="panel-contenido">
    <div id="contenido">
    Hola Caracola
    </div>
</div>
</body>