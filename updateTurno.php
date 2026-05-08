<?php
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
session_start();
require_once "bd.php";
// $bd->autocommit(false);
$json = file_get_contents('php://input');
// TRABAJO PARA MÁXIMO
error_log("Turno: " . $json);
$turno = json_decode($json, true);
$turno = $turno['turno'] ?? null;

if ($turno == 1) {
    $updatePartida = "UPDATE partida SET turno = 2 WHERE id_partida = " . $_SESSION['partida'] . "";
    $bd->query($updatePartida);
    echo "update";
} else {
    $updatePartida = "UPDATE partida SET turno = 1 WHERE id_partida = " . $_SESSION['partida'] . "";
    $bd->query($updatePartida);
    echo "update";
}
//}
