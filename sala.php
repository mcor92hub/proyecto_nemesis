<!-- Cuando encuentre usuario mostrar o redirigir a otra página donde seleccione al personaje con el que jugar -->

<?php
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
session_start();
require_once "bd.php";
if (isset($_GET['personajeElegido'])) {
    $consultaPartida =
        "SELECT id_partida FROM partida 
        WHERE estado = 'en proceso' AND 
        usuario2_id IS NULL 
        AND usuario1_id != " . $_SESSION['id_usuario'] . "
        AND ultima_actividad_usuario1 >= CURRENT_TIMESTAMP - INTERVAL 30 SECOND 
        LIMIT 1";
    $resultadoConsultaPartida = $bd->query($consultaPartida);
    if ($resultadoConsultaPartida->num_rows == 0) {
        $consultaPartida2 =
            "SELECT id_partida FROM partida 
            WHERE estado = 'en proceso' AND 
            usuario2_id IS NULL 
            AND usuario1_id = " . $_SESSION['id_usuario'] . "
            AND ultima_actividad_usuario1 >= CURRENT_TIMESTAMP - INTERVAL 30 SECOND 
            LIMIT 1";
        $resultadoConsultaPartida2 = $bd->query($consultaPartida2);
        if ($resultadoConsultaPartida2->num_rows == 0) {
            $numero = random_int(1, 2);
            $insertPartidaNueva = "INSERT INTO partida (usuario1_id, personaje1_id, estado, turno, ultima_actividad_usuario1) VALUES(" . $_SESSION['id_usuario'] . "," . $_GET['personajeElegido'] . " ,'en proceso', " . $numero . ",CURRENT_TIMESTAMP)";
            $resultadoInsertPartidaNueva = $bd->query($insertPartidaNueva);
            $_SESSION['partida'] = $bd->insert_id;
            $_SESSION['usuario1'] = true;
        } else {
            var_dump("borrar");
            $numero = random_int(1, 2);
            $borraPartidas = "DELETE FROM partida WHERE usuario1_id=" . $_SESSION['id_usuario'] . "";
            $bd->query($borraPartidas);
            $insertPartidaNueva = "INSERT INTO partida (usuario1_id, personaje1_id, estado, turno, ultima_actividad_usuario1) VALUES(" . $_SESSION['id_usuario'] . "," . $_GET['personajeElegido'] . " ,'en proceso', " . $numero . ",CURRENT_TIMESTAMP)";
            $resultadoInsertPartidaNueva = $bd->query($insertPartidaNueva);
            $_SESSION['partida'] = $bd->insert_id;
            $_SESSION['usuario1'] = true;
        }
    } else {
        $consultaIdPartida = "SELECT id_partida FROM partida WHERE estado = 'en proceso' LIMIT 1";
        $resultadoConsultaIdPartida = $bd->query($consultaIdPartida);
        while ($idPartidaEspera = $resultadoConsultaIdPartida->fetch_assoc()) {
            $updatePartida =
                "UPDATE partida
                SET usuario2_id = " . $_SESSION['id_usuario'] . ",
                personaje2_id = " . $_GET['personajeElegido'] . ",
                estado = 'jugando'
                WHERE id_partida = (" . $idPartidaEspera['id_partida'] . ")
                AND usuario2_id IS NULL
                AND usuario1_id != " . $_SESSION['id_usuario'] . "
                AND ultima_actividad_usuario1 >= CURRENT_TIMESTAMP - INTERVAL 30 SECOND;";
            $bd->query($updatePartida);
            $_SESSION['partida'] = $idPartidaEspera['id_partida'];
            $_SESSION['usuario2'] = true;
            header("Location: combate.php");
        }
    }
} else {
    echo "falla el $_GET";
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
    <title>Sala de espera de partidas</title>
</head>

<body>
    <!-- Usamos este setInterval para llamar y ejecutar la página de PHP cada 5 segundos (hecho por IA)-->
    <script>
        setInterval(() => {
            fetch("siguesAhi.php");
        }, 5000);
    </script>

    <!-- Uso este script para comprobar si el jugador1 encontró a un usuario para jugar una partida (intenté hacerlo yo, pero no lo conseguí, hecho con IA)-->
    <script>
        setInterval(() => {
            fetch("irAPartida.php")
                .then(res => res.text())
                .then(data => {
                    if (data === "REDIRECT") {
                        window.location.href = "combate.php";
                    }
                });
        }, 5000);
    </script>

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
    <main id="mainSala">
        <div>
            <img src="imgs/fondoGameCube.gif" alt="">
            <p id="indexFrases"></p>
        </div>
        <!-- script para generar frases -->
        <script>
            let frases = ['Esperando por el adversario que se le ha puesto malo el basilisco.', 'Esperando por el adversario que tiene que llevar el caballo a pasar la ITV.', 'Esperando por el adversario que justo justo justo le ha llamado lord Roca para hacer una gestión.', 'Esperando por el adversario que saque un 20 para que le funcione el WiFi.', 'Esperando por el adversario (este es bueno, te va a romper las cachas) .'];

            //Posición en el array
            let frase = 0;

            //Posición de la letra en la palabra
            let letra = 0;
            let borrando = false;
            let frasesHTML = document.getElementById("indexFrases");

            function efectoEscritura() {
                let fraseActual = frases[frase];
                let velocidad = 100;

                if (borrando) {
                    letra--;
                } else {
                    letra++;
                }
                frasesHTML.textContent = fraseActual.substring(0, letra);

                if (borrando) {
                    velocidad = 100;
                }

                //Si llega al final de la frase, para unos segudos y luego borra
                if (!borrando && letra == fraseActual.length) {
                    velocidad = 2700;
                    borrando = true;
                    //Sino empieza otra frase
                } else if (borrando && letra == 0) {
                    borrando = false;
                    frase++;
                    //O vuelve a empezar a recorrer el array
                    if (frase == frases.length) {
                        frase = 0;
                    }
                }
                setTimeout(efectoEscritura, velocidad);

            }
            efectoEscritura();
        </script>

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