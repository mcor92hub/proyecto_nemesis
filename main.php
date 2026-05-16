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
    <title>Inicio</title>
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


    <!-- EN ESTE ENLACE ESTAN LOS BLOQUES POR PESTAÑAS PARA EL PLAN DE CELIA DE NAVEGAR POR PESATAÑAS -->
    <!-- https://getbootstrap.com/docs/5.3/components/navs-tabs/#javascript-behavior -->

    <main id="mainSegundaPagina">
        <aside>
            <h4>Puta que ofertón</h4>
            <div>
                <img src="imgs/pocionSimple.png" alt="" class="pociones">
                <img src="imgs/mesaMedieval.png" alt="" class="mesa">
            </div>
        </aside>

        <section>
            <ul class="nav nav-tabs w-100" id="myTab" role="tablist" id="bloquePestañas">
                <li class="nav-item pestaña" role="presentation">
                    <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Combate</button>
                </li>
                <li class="nav-item pestaña" role="presentation">
                    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Personajes</button>
                </li>
                <li class="nav-item pestaña" role="presentation">
                    <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact-tab-pane" type="button" role="tab" aria-controls="contact-tab-pane" aria-selected="false">Usuario</button>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active slide-in-right" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                    <a href="elegirPersonaje.php"><img src="imgs/combate.png" alt=""></a>
                </div>
                <div class="tab-pane fade slide-in-right" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                    <h3>Mis personajes</h3>
                    <?php
                    $imgs = ["imgs/arqueroDerecha.gif", "imgs/caballeroDerecha.gif", "imgs/hechiceroDerecha.gif", "imgs/druidaDerecha.gif"];
                    $consultaPersonajes = "SELECT * FROM personaje WHERE usuario_id=" . $_SESSION['id_usuario'] . "";
                    $resultado = $bd->query($consultaPersonajes);
                    $i = 0;
                    while ($fila = $resultado->fetch_assoc()) {
                        echo "<div>";
                        echo "<img src='" . $imgs[$i] . "'>";
                        echo "<p>" . $fila['nombre'] . "</p>";
                        echo "</div>";
                        $i++;
                    }
                    ?>

                </div>
                <div class="tab-pane fade slide-in-right" id="contact-tab-pane" role="tabpanel" aria-labelledby="contact-tab" tabindex="0">
                    <h3>
                        <?php
                        echo $_SESSION['nick'];
                        ?>
                    </h3>
                    <?php
                    $resultado = $bd->query($consultaPersonajes);
                    while ($fila = $resultado->fetch_assoc()) {
                        echo "<div>";
                        echo "<p>Estadísticas de: " . $fila['nombre'] . "</p>";
                        echo "<ul>";
                        echo "<li>Victorias: " . $fila['victorias'] . "</li>";
                        echo "<li>Derrotas: " . $fila['derrotas'] . "</li>";
                        echo "<li>Golpes críticos dados: " . $fila['golpesCriticos'] . "</li>";
                        echo "</ul>";
                        echo "</div>";
                        $i++;
                    }
                    ?>
                </div>
            </div>
        </section>

        <aside>
            <h4>Puta que ofertón</h4>
            <div>
                <img src="imgs/curacionTotal.png" alt="" class="pociones">
                <img src="imgs/mesaMedieval.png" alt="" class="mesa">
            </div>
        </aside>
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