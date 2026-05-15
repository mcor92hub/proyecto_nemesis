<?php
session_start();
require_once "bd.php";
header("Content-Type: application/json");
$consulta = "SELECT turno FROM partida WHERE id_partida = " . $_SESSION['partida'] . ";";
$resultado = $bd->query($consulta);

while ($fila = $resultado->fetch_assoc()) {
    echo json_encode([
        "success" => true,
        "turno" => $fila['turno']
    ]);
}
$bd->close();