<?php
session_start();
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
require_once "bd.php";
if (isset($_POST['email'], $_POST['nick'], $_POST['passRegistro'], $_POST['passConfirmacion'], $_POST['arquero'], $_POST['caballero'], $_POST['hechicero'], $_POST['druida'])) {
    try {
        $bd->autocommit(false);
        if ($_POST['passRegistro'] != $_POST['passConfirmacion']) {
            $errorContraseña = "Las contraseñas no coinciden";
        } else {
            $passHasheada = password_hash($_POST['passRegistro'], PASSWORD_DEFAULT);
            // PASSWORD_DEFAULT usa el algoritmo por defecto de PHP en ese momento
            $usuario = "INSERT INTO usuario(email, nick, contraseña) VALUES('" . $_POST['email'] . "','" . $_POST['nick'] . "', '" . $passHasheada . "');";

            // Crea un usuario y si no da error crea los 4 personajes con el id del usuario recién creado
            $bd->query($usuario);
            if (!$bd->errno) {
                $idUsuario = "SELECT id_usuario FROM usuario WHERE nick = '" . $_POST['nick'] . "'";
                $personaje1 = "INSERT INTO personaje(nombre, fuerza, armadura, usuario_id) VALUES('" . $_POST['arquero'] . "',60,60,(" . $idUsuario . "));";
                $personaje2 = "INSERT INTO personaje(nombre, fuerza, armadura, usuario_id) VALUES('" . $_POST['caballero'] . "',100,100,(" . $idUsuario . "));";
                $personaje3 = "INSERT INTO personaje(nombre, fuerza, armadura, usuario_id) VALUES('" . $_POST['hechicero'] . "',50,30,(" . $idUsuario . "));";
                $personaje4 = "INSERT INTO personaje(nombre, fuerza, armadura, usuario_id) VALUES('" . $_POST['druida'] . "',50,30,(" . $idUsuario . "));";
                $bd->query($personaje1);
                $bd->query($personaje2);
                $bd->query($personaje3);
                $bd->query($personaje4);

                // Consulto los id de los 4 personajes para luego usarlos para crear un personaje de cada clase
                $idPersonaje = "SELECT id_personaje FROM personaje WHERE usuario_id = (" . $idUsuario . ")";
                $consultaIdPersonaje = $bd->query($idPersonaje);
                $contador = 1;

                // Estas consultas se van a repetir despues
                $consultaCuracion = "SELECT id_item FROM item WHERE nombre = 'curacionSimple' LIMIT 1";
                $consultaSuperCuracion = "SELECT id_item FROM item WHERE nombre = 'superCuracion' LIMIT 1";
                $consultaCuracionCompleta = "SELECT id_item FROM item WHERE nombre = 'curacionCompleta' LIMIT 1";
                $consultaRestaurarEstamina = "SELECT id_item FROM item WHERE nombre = 'restaurarEstamina' LIMIT 1";
                $consultaRestaurarMuchaEstamina =  "SELECT id_item FROM item WHERE nombre = 'restaurarMuchaEstamina' LIMIT 1";
                $consultaRestaurarTodaEstamina = "SELECT id_item FROM item WHERE nombre = 'restaurarTodaEstamina' LIMIT 1";
                while ($fila = $consultaIdPersonaje->fetch_assoc()) {
                    // Hago un switch de contador para que en las 4 vueltas del bucle que hace por los 4 personajes entra en uno de los case y hace cada insert por separado
                    switch ($contador) {
                        case 1:
                            // INSERT del personaje tipo "arquero"
                            $arquero = "INSERT INTO arquero(id_personaje) VALUES(" . $fila['id_personaje'] . ")";
                            $bd->query($arquero);
                            // INSERT de las armas del arquero
                            $consultaArco = "SELECT id_item FROM item WHERE nombre = 'arco' LIMIT 1";
                            $arco = "INSERT INTO item_guardado(item_id, personaje_id, cantidad) VALUES ((" . $consultaArco . "), " . (int)$fila['id_personaje'] . ", 1);";
                            $bd->query($arco);
                            $consultaFlechas = "SELECT id_item FROM item WHERE nombre = 'flecha' LIMIT 1";
                            $flechas = "INSERT INTO item_guardado(item_id, personaje_id, cantidad) VALUES ((" . $consultaFlechas . "), " . (int)$fila['id_personaje'] . ", 20);";
                            $bd->query($flechas);
                            $consultaNunchakus = "SELECT id_item FROM item WHERE nombre = 'nunchakus' LIMIT 1";
                            $nunchakus = "INSERT INTO item_guardado(item_id, personaje_id, cantidad) VALUES ((" . $consultaNunchakus . "), " . (int)$fila['id_personaje'] . ", 1);";
                            $bd->query($nunchakus);

                            // INSERT de las pociones de curacion y de recuperar estamina
                            $curacion = "INSERT INTO item_guardado(item_id, personaje_id, cantidad) VALUES ((" . $consultaCuracion . "), " . (int)$fila['id_personaje'] . ", 5);";
                            $bd->query($curacion);
                            $superCuracion = "INSERT INTO item_guardado(item_id, personaje_id, cantidad) VALUES ((" . $consultaSuperCuracion . "), " . (int)$fila['id_personaje'] . ", 3);";
                            $bd->query($superCuracion);
                            $curacionCompleta = "INSERT INTO item_guardado(item_id, personaje_id, cantidad) VALUES ((" . $consultaCuracionCompleta . "), " . (int)$fila['id_personaje'] . ", 1);";
                            $bd->query($curacionCompleta);
                            $restaurarEstamina = "INSERT INTO item_guardado(item_id, personaje_id, cantidad) VALUES ((" . $consultaRestaurarEstamina . "), " . (int)$fila['id_personaje'] . ", 5);";
                            $bd->query($restaurarEstamina);
                            $restaurarMuchaEstamina = "INSERT INTO item_guardado(item_id, personaje_id, cantidad) VALUES ((" . $consultaRestaurarMuchaEstamina . "), " . (int)$fila['id_personaje'] . ", 3);";
                            $bd->query($restaurarMuchaEstamina);
                            $restaurarTodaEstamina = "INSERT INTO item_guardado(item_id, personaje_id, cantidad) VALUES ((" . $consultaRestaurarTodaEstamina . "), " . (int)$fila['id_personaje'] . ", 1);";
                            $bd->query($restaurarTodaEstamina);

                            break;
                        case 2:
                            // INSERT del personaje tipo "caballero"
                            $caballero = "INSERT INTO caballero(id_personaje) VALUES(" . $fila['id_personaje'] . ")";
                            $bd->query($caballero);
                            // INSERT de las armas del caballero
                            $consultaEspada = "SELECT id_item FROM item WHERE nombre = 'espada' LIMIT 1";
                            $espada = "INSERT INTO item_guardado(item_id, personaje_id, cantidad) VALUES ((" . $consultaEspada . "), " . (int)$fila['id_personaje'] . ", 1);";
                            $bd->query($espada);
                            $consultaMazo = "SELECT id_item FROM item WHERE nombre = 'mazo' LIMIT 1";
                            $mazo = "INSERT INTO item_guardado(item_id, personaje_id, cantidad) VALUES ((" . $consultaMazo . "), " . (int)$fila['id_personaje'] . ", 1);";
                            $bd->query($mazo);

                            // INSERT de las pociones de curacion y de recuperar estamina
                            $curacion = "INSERT INTO item_guardado(item_id, personaje_id, cantidad) VALUES ((" . $consultaCuracion . "), " . (int)$fila['id_personaje'] . ", 5);";
                            $bd->query($curacion);
                            $superCuracion = "INSERT INTO item_guardado(item_id, personaje_id, cantidad) VALUES ((" . $consultaSuperCuracion . "), " . (int)$fila['id_personaje'] . ", 3);";
                            $bd->query($superCuracion);
                            $curacionCompleta = "INSERT INTO item_guardado(item_id, personaje_id, cantidad) VALUES ((" . $consultaCuracionCompleta . "), " . (int)$fila['id_personaje'] . ", 1);";
                            $bd->query($curacionCompleta);
                            $restaurarEstamina = "INSERT INTO item_guardado(item_id, personaje_id, cantidad) VALUES ((" . $consultaRestaurarEstamina . "), " . (int)$fila['id_personaje'] . ", 5);";
                            $bd->query($restaurarEstamina);
                            $restaurarMuchaEstamina = "INSERT INTO item_guardado(item_id, personaje_id, cantidad) VALUES ((" . $consultaRestaurarMuchaEstamina . "), " . (int)$fila['id_personaje'] . ", 3);";
                            $bd->query($restaurarMuchaEstamina);
                            $restaurarTodaEstamina = "INSERT INTO item_guardado(item_id, personaje_id, cantidad) VALUES ((" . $consultaRestaurarTodaEstamina . "), " . (int)$fila['id_personaje'] . ", 1);";
                            $bd->query($restaurarTodaEstamina);
                            break;

                        case 3:
                            // INSERT del personaje tipo "hechicero"
                            $hechicero = "INSERT INTO hechicero(id_personaje) VALUES(" . $fila['id_personaje'] . ")";
                            $bd->query($hechicero);
                            // INSERT del arma del hechicero
                            $consultaVara = "SELECT id_item FROM item WHERE nombre = 'vara' LIMIT 1";
                            $vara = "INSERT INTO item_guardado(item_id, personaje_id, cantidad) VALUES ((" . $consultaVara . "), " . (int)$fila['id_personaje'] . ", 1);";
                            $bd->query($vara);

                            // INSERT de las pociones de curacion y de recuperar estamina
                            $curacion = "INSERT INTO item_guardado(item_id, personaje_id, cantidad) VALUES ((" . $consultaCuracion . "), " . (int)$fila['id_personaje'] . ", 5);";
                            $bd->query($curacion);
                            $superCuracion = "INSERT INTO item_guardado(item_id, personaje_id, cantidad) VALUES ((" . $consultaSuperCuracion . "), " . (int)$fila['id_personaje'] . ", 3);";
                            $bd->query($superCuracion);
                            $curacionCompleta = "INSERT INTO item_guardado(item_id, personaje_id, cantidad) VALUES ((" . $consultaCuracionCompleta . "), " . (int)$fila['id_personaje'] . ", 1);";
                            $bd->query($curacionCompleta);
                            $restaurarEstamina = "INSERT INTO item_guardado(item_id, personaje_id, cantidad) VALUES ((" . $consultaRestaurarEstamina . "), " . (int)$fila['id_personaje'] . ", 5);";
                            $bd->query($restaurarEstamina);
                            $restaurarMuchaEstamina = "INSERT INTO item_guardado(item_id, personaje_id, cantidad) VALUES ((" . $consultaRestaurarMuchaEstamina . "), " . (int)$fila['id_personaje'] . ", 3);";
                            $bd->query($restaurarMuchaEstamina);
                            $restaurarTodaEstamina = "INSERT INTO item_guardado(item_id, personaje_id, cantidad) VALUES ((" . $consultaRestaurarTodaEstamina . "), " . (int)$fila['id_personaje'] . ", 1);";
                            $bd->query($restaurarTodaEstamina);
                            break;

                        case 4:
                            // INSERT del personaje tipo "druida"
                            $druida = "INSERT INTO druida(id_personaje) VALUES(" . $fila['id_personaje'] . ")";
                            $bd->query($druida);
                            // INSERT del arma del druida
                            $consultaDaga = "SELECT id_item FROM item WHERE nombre = 'daga' LIMIT 1";
                            $daga = "INSERT INTO item_guardado(item_id, personaje_id, cantidad) VALUES ((" . $consultaDaga . "), " . (int)$fila['id_personaje'] . ", 1);";
                            $bd->query($daga);

                            // INSERT de las pociones de curacion y de recuperar estamina
                            $curacion = "INSERT INTO item_guardado(item_id, personaje_id, cantidad) VALUES ((" . $consultaCuracion . "), " . (int)$fila['id_personaje'] . ", 5);";
                            $bd->query($curacion);
                            $superCuracion = "INSERT INTO item_guardado(item_id, personaje_id, cantidad) VALUES ((" . $consultaSuperCuracion . "), " . (int)$fila['id_personaje'] . ", 3);";
                            $bd->query($superCuracion);
                            $curacionCompleta = "INSERT INTO item_guardado(item_id, personaje_id, cantidad) VALUES ((" . $consultaCuracionCompleta . "), " . (int)$fila['id_personaje'] . ", 1);";
                            $bd->query($curacionCompleta);
                            $restaurarEstamina = "INSERT INTO item_guardado(item_id, personaje_id, cantidad) VALUES ((" . $consultaRestaurarEstamina . "), " . (int)$fila['id_personaje'] . ", 5);";
                            $bd->query($restaurarEstamina);
                            $restaurarMuchaEstamina = "INSERT INTO item_guardado(item_id, personaje_id, cantidad) VALUES ((" . $consultaRestaurarMuchaEstamina . "), " . (int)$fila['id_personaje'] . ", 3);";
                            $bd->query($restaurarMuchaEstamina);
                            $restaurarTodaEstamina = "INSERT INTO item_guardado(item_id, personaje_id, cantidad) VALUES ((" . $consultaRestaurarTodaEstamina . "), " . (int)$fila['id_personaje'] . ", 1);";
                            $bd->query($restaurarTodaEstamina);
                            break;

                        default:
                            echo "no deberia entrar aqui";
                            break;
                    }
                    $contador++;
                }
            }
        }
    } catch (Exception $e) {
        echo $e;
        $bd->rollback();
    }
    $bd->commit();
    $bd->autocommit(true);
    $bd->close();
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <script src="bootstrap-5.3.8-dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="estilos/estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="shortcut icon" href="imgs/favicon-nemesis.png" type="image/x-icon">
    <title>Registro</title>
</head>

<body>
    <!-- ESTE BLOQUE HACE QUE CHROME NO GUARDE CACHE -->

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
    <main id="mainPrimeraPagina">

        <!-- REGISTRO -->
        <div class="bounce-in-fwd container" id="divRegistro">
            <div class="modal-dialog modal-dialog-centered row justify-content-center">
                <div class=" col-sm-12 col-md-10 col-lg-8">
                    <div class="modal-content divFormulario ">
                        <div class="modal-header d-flex justify-content-between">
                            <h1 class="modal-title fs-3" id="divRegistroLabel">Registro</h1>
                            <a class="cursor-pointer" id="botonCerrarRegistro"
                                onclick="document.getElementById('divRegistro').style.display = 'none';">X</a>
                        </div>
                        <div class="modal-body w-100 h-100">
                            <form action="registro.php" method="post">
                                <div>
                                    <div>
                                        <label for="email">e-mail: </label>
                                        <input type="email" name="email" id="email" required>
                                    </div>
                                    <div>
                                        <label for="nick">Nombre de usuario: </label>
                                        <input type="text" name="nick" id="nick" required>
                                    </div>
                                    <?php
                                    if (isset($errorContraseña)) {
                                        echo "<div>
                                        <p>" . $errorContraseña . "</p>
                                        </div>";
                                    }
                                    ?>
                                    <div>
                                        <label for="pass">Contraseña: </label>
                                        <input type="password" name="passRegistro" id="passRegistro" required minlength="8">
                                    </div>
                                    <div>
                                        <label for="passConfirmacion">Confirmar contraseña: </label>
                                        <input type="password" name="passConfirmacion" id="passConfirmacion" required>
                                    </div>
                                    <div>
                                        <label for="arquero">Nombre de tu arquero: </label>
                                        <input type="text" name="arquero" id="arquero" required>
                                    </div>
                                    <div>
                                        <label for="caballero">Nombre de tu caballero: </label>
                                        <input type="text" name="caballero" id="caballero" required>
                                    </div>
                                    <div>
                                        <label for="hechicero">Nombre de tu hechicero: </label>
                                        <input type="text" name="hechicero" id="hechicero" required>
                                    </div>
                                    <div>
                                        <label for="druida">Nombre de tu druida: </label>
                                        <input type="text" name="druida" id="druida" required>
                                    </div>
                                </div>
                                <button type="submit">Enviar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
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
                <a href="https://www.instagram.com/"><i class="fa-brands fa-instagram"></i></a>
                <a href="https://discord.com/"><i class="fa-brands fa-discord"></i></a>
                <a href="https://www.youtube.com/"><i class="fa-brands fa-youtube"></i></a>
                <a href="https://www.twitch.tv/"><i class="fa-brands fa-twitch"></i></a>
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