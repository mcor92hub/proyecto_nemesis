<?php
/**
 * Redirige con header("Location: ...") solo a URLs (http://localhost/...), nunca a rutas de disco (E:\...).
 * No imprimir nada antes de header() si quieres que el redirect funcione.
 */
$bd = new mysqli("localhost", "root", "TU_CONTRASEÑA_MYSQL", "proyecto_nemesis");
if ($bd->connect_error) {
    die("error de conexion");
}

$usuario = $_POST['usuario'] ?? '';

$sql = "SELECT id_usuario FROM usuario WHERE nick = ? LIMIT 1";
$stmt = $bd->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    header("Location: http://localhost/corlosmig/proyecto_nemesis/index2.html");
} else {
    header("Location: http://localhost/corlosmig/proyecto_nemesis/index.html");
}
exit;
