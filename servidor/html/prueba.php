<?php
$clave = ini_get("session.upload_progress.prefix") . $_POST[ini_get("session.upload_progress.name")];
echo "$clave<br>";
var_dump($_SESSION[$clave]);
?>
