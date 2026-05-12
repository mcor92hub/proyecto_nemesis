<?php 
require_once "bd.php";

$sentencia = "SELECT personaje1_id, personaje2_id FROM partida WHERE id_partida = " . $_SESSION['partida'] . ";"

//añadir en una variable de js el val9or para poder mandarlo en el fecth tmb y asi ya tener el id del personaje para la partida y compararlo con la base de datos

?>