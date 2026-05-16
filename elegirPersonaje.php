<?php
session_start();
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
require_once "bd.php";
// NO SE QUE HACE LA SIGUIENTE LINEA ES PARA NO TENER PROBLEMAS DE CACHÉ
$cssVersion = @filemtime(__DIR__ . "/estilos/estilos.css") ?: time();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <script src="bootstrap-5.3.8-dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="estilos/estilos.css?v=<?php echo $cssVersion; ?>">
    <link rel="shortcut icon" href="imgs/favicon-nemesis.png" type="image/x-icon">
    <title>Elegir Personaje</title>
</head>

<body>
    <script>
        let azar = Math.floor(Math.random() * 9);
        let listaFondos = ["fondoBosqueAtardecer.gif", "fondoBosqueOscuro.gif", "fondoCascadaLago.gif", "fondoCascadas.gif", "fondoEspada.gif", "fondoHoguera.gif", "fondoLluviaCueva.gif", "fondoLuna.gif", "fondoNocheLago.gif", "fondoNocheMontaña.gif"];
        document.body.style.backgroundImage = "url(imgs/" + listaFondos[azar] + ")";
    </script>
    <header class="swing-in-top-fwd">
        <div id="headerDiv1">
            <img src="imgs/favicon-nemesis.png" alt="">
        </div>
        <div id="headerDiv2">
            <p>PROYECTO NÉMESIS</p>
        </div>
    </header>
    <main id="mainElegirPersonaje">
        <div>
            <?php
            // ESTO ERA PARA MOSTRAR EL NOMBRE DEL ADVERSARIO PERO YA NO ME VALE PARA NADA

            // $consulta = "SELECT usuario1_id FROM partida WHERE usuario1_id= " . $_SESSION['id_usuario'] . ";";
            // $resultado = $bd->query($consulta);
            // if ($resultado->num_rows > 0) {
            //     $consultaNickJugador2 = "SELECT u.nick FROM usuario u JOIN partida p ON u.id_usuario = p.usuario2_id WHERE usuario1_id= " . $_SESSION['id_usuario'] . ";";
            //     $resultadoConsultaNickJugador2 = $bd->query($consultaNickJugador2);
            //     while ($resultadoDeVerdadNickJugador2 = $resultadoConsultaNickJugador2->fetch_assoc()) {
            //         echo "<h1>" . $_SESSION['nick'] . "</h1>";
            //         echo "<h3>Vas a luchar contra " . $resultadoDeVerdadNickJugador2['nick'] . "</h3>";
            //     }
            // } else {
            //     $consultaNickJugador1 = "SELECT u.nick FROM usuario u JOIN partida p ON u.id_usuario = p.usuario1_id WHERE usuario2_id= " . $_SESSION['id_usuario'] . ";";
            //     $resultadoConsultaNickJugador1 = $bd->query($consultaNickJugador1);
            //     while ($resultadoDeVerdadNickJugador1 = $resultadoConsultaNickJugador1->fetch_assoc()) {
            //         echo "<h1>" . $_SESSION['nick'] . "</h1>";
            //         echo "<h3>Vas a luchar contra " . $resultadoDeVerdadNickJugador1['nick'] . "</h3>";
            //     }
            // }

            $consultaPersonajes = "SELECT id_personaje, nombre FROM personaje WHERE usuario_id=" . $_SESSION['id_usuario'] . ";";
            $resultadoPersonaje = $bd->query($consultaPersonajes);
            $contador = 0;
            $listaImagenes = ["imgs/arqueroDerecha.gif", "imgs/caballeroDerecha.gif", "imgs/hechiceroDerecha.gif", "imgs/druidaDerecha.gif"];
            while ($resultadoDeVerdadPersonaje = $resultadoPersonaje->fetch_assoc()) {
                echo "<a href='sala.php?personajeElegido=" . $resultadoDeVerdadPersonaje['id_personaje'] . "'>";
                echo "<div>";
                echo "<img src='" . $listaImagenes[$contador] . "'>";
                echo "<p>" . $resultadoDeVerdadPersonaje['nombre'] . "</p>";
                echo "</div>";
                echo "</a>";
                $contador++;
            }
            ?>
            
        </div>
    </main>
    <footer>
        <div>
            <a href="#">Politica de privacidad</a>
            <a href="#">Condiciones de uso</a>
            <a href="#">Preferencias de cookies</a>
        </div>
        <div id="logoFooter">
            <img src="imgs/favicon-nemesis.png" alt="">
            <div>
                <a href="#"><i class="fa-brands fa-instagram"></i></a>
                <a href="#"><i class="fa-brands fa-discord"></i></a>
                <a href="#"><i class="fa-brands fa-youtube"></i></a>
                <a href="#"><i class="fa-brands fa-twitch"></i></a>
            </div>
        </div>
        <div>
            <a href="#">Comunidad</a>
            <a href="#">Contacto</a>
            <a href="#">Finánciame ;)</a>
        </div>
    </footer>
</body>

</html>