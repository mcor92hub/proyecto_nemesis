<?php
session_start();
// Con esta página actualizamos la columna "ultima_actividad_usuario1" por si en algún momento se desconectara de la partida
$bd = new mysqli("localhost", "root", "", "proyecto_nemesis");

$consulta = "UPDATE partida 
             SET ultima_actividad_usuario1 = CURRENT_TIMESTAMP 
             WHERE usuario1_id = " . $_SESSION['id_usuario'] . " 
             AND estado = 'en proceso'";

$resultado = $bd->query($consulta);