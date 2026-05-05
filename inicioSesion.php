<?php
$bd = new mysqli("localhost", "root", "", "proyecto_nemesis");
if ($bd->connect_error) {
    echo "error de conexion";
} else {
    echo "conexion bien";
}

$consultaUsuario = "SELECT * FROM usuario WHERE nick = '" . $_POST['usuario'] . "'";
$resultadoConsulta = $bd->query($consultaUsuario);

if ($resultadoConsulta->num_rows > 0) {
    session_start();
    while ($resutadoEsteSiConsulta = $resultadoConsulta->fetch_assoc()) {
        $_SESSION['nick'] = $resutadoEsteSiConsulta['nick'];
        $_SESSION['id_usuario'] = $resutadoEsteSiConsulta['id_usuario'];
    }
    header("Location:  http://localhost/corlosmig/proyecto_nemesis/index2.php");
} else {
    // LA SIGUIENTE LINEA NO FUNCIONA
    echo "<script>alert('Nombre de usuario o contraseña incorrectos')</script>";
    header("Location:  http://localhost/corlosmig/proyecto_nemesis/index.php");
}
?>