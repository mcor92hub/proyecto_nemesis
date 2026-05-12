<?php 
session_start();
require_once "bd.php";

$sentencia = "SELECT personaje1_id, personaje2_id FROM partida WHERE id_partida = " . $_SESSION['partida'] . ";";
$resultado = $bd->query($sentencia);

$idPersonaje1 = [];
$idPersonaje2 = [];
while ($ids = $resultado->fetch_assoc()) {
    array_push($idPersonaje1, $ids['personaje1_id']);
    array_push($idPersonaje2, $ids['personaje2_id']);
}

var_dump($idPersonaje1);
var_dump($idPersonaje2);

//añadir en una variable de js el val9or para poder mandarlo en el fecth tmb y asi ya tener el id del personaje para la partida y compararlo con la base de datos
?>