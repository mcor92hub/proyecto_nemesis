<?php
session_start();
?>

<!-- PHP DE INICIO DE SESIÓN -->
<?php
require_once "bd.php";
if (isset($_POST['usuario'], $_POST['pass'])) {
    $consultaUsuario = "SELECT * FROM usuario WHERE nick = '" . $_POST['usuario'] . "'";
    $resultadoConsulta = $bd->query($consultaUsuario);
    while ($resultadoDeVerdad = $resultadoConsulta->fetch_assoc()) {
        if (password_verify($_POST['pass'], $resultadoDeVerdad['contraseña'])) {
            $_SESSION['id_usuario'] = $resultadoDeVerdad['id_usuario'];
            $_SESSION['nick'] = $resultadoDeVerdad['nick'];
            header("Location:  main.php");
        }
    }
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
    <title>Inicio</title>
</head>

<body>
    <!-- ESTE BLOQUE HACE QUE CHROME NO GUARDE CACHE -->
    <?php
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");
    ?>
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
        <!-- <div>
            <button id="botonInicioSesion">Iniciar Sesión</button>
            <button id="botonRegistro"><a href="registro.php">Registrarse</a></button>
        </div> -->
    </header>
    <main id="mainPrimeraPagina">
        <!-- INICIO SESIÓN -->
        <div class="bounce-in-fwd container" id="divInicioSesion">
            <div class="modal-dialog modal-dialog-centered row justify-content-center">
                <div class=" col-sm-12 col-md-10 col-lg-8">
                    <div class="modal-content divFormulario ">
                        <div class="modal-header d-flex justify-content-between">
                            <h1 class="modal-title fs-3" id="divInicioSesionLabel">Iniciar Sesión</h1>
                        </div>
                        <div class="modal-body w-100 h-100">
                            <form action="index.php" method="post">
                                <div>
                                    <div>
                                        <label for="usuario">Usuario: </label>
                                        <input type="text" name="usuario" id="usuario">
                                    </div>
                                    <div>
                                        <label for="pass">Contraseña: </label>
                                        <input type="password" name="pass" id="pass">
                                    </div>
                                </div>
                                <button type="submit">Enviar</button>
                            </form>
                        </div>
                        <div class="modal-footer d-flex flex-column">
                            <a href="#">¿Has olvidado tu contraseña?</a>
                            <p>¿No tienes cuenta? <a href="registro.php">Registrate AQUÍ</a></p>
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