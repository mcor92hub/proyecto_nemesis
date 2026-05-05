<?php
//QUITA O PON LA PUTA CONTRASEÑA
session_start();
require_once "bd.php";
$consulta = "SELECT usuario1_id, usuario2_id FROM partida WHERE usuario1_id = " . $_SESSION['id_usuario'] . " AND usuario2_id IS NOT NULL AND id_partida = ".$_SESSION['partida'].";";
$resultado = $bd->query($consulta);
if ($resultado->num_rows > 0) {
    echo "REDIRECT";
}
