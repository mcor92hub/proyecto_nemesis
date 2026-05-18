<?php
session_start();
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
require_once "bd.php";

$consultaTurno = "SELECT * FROM partida WHERE id_partida = " . $_SESSION['partida'] . "";
$resultadoTurno = $bd->query($consultaTurno);
$lista = [];
while ($fila = $resultadoTurno->fetch_assoc()) {
    $turno = $fila['turno'];
    array_push($lista, $fila['personaje1_id']);
    array_push($lista, $fila['personaje2_id']);
}
$usuario1 = null;
$usuario2 = null;
if (isset($_SESSION['usuario1'])) {
    $usuario1 = true;
} elseif (isset($_SESSION['usuario2'])) {
    $usuario2 = true;
}

// NO SE QUE HACE LA SIGUIENTE LINEA ES PARA NO TENER PROBLEMAS DE CACHÉ
$cssVersion = @filemtime(__DIR__ . "/estilos/estilos.css") ?: time();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <link rel="stylesheet" href="estilos/estilosCombate.css?v=<?php echo time(); ?>">
    <link rel="shortcut icon" href="imgs/favicon-nemesis.png" type="image/x-icon">
    <title>Proyecto Némesis</title>
</head>

<body>
    <!-- PARA EVITAR PROBLEMAS DE CACHÉ está el echo time() -->
    <script src="JS/objetos.js?v=<?php echo time(); ?>"></script>
    <script src="JS/personaje.js?v=<?php echo time(); ?>"></script>
    <script src="JS/guerrero.js?v=<?php echo time(); ?>"></script>
    <script src="JS/arquero.js?v=<?php echo time(); ?>"></script>
    <script src="JS/caballero.js?v=<?php echo time(); ?>"></script>
    <script src="JS/mago.js?v=<?php echo time(); ?>"></script>
    <script src="JS/hechicero.js?v=<?php echo time(); ?>"></script>
    <script src="JS/druida.js?v=<?php echo time(); ?>"></script>
    <script src="JS/funciones.js?v=<?php echo time(); ?>"></script>
    <script src="JS/constructores.js?v=<?php echo time(); ?>"></script>

    <script>
        let turno = <?php echo json_encode($turno ?? null, JSON_UNESCAPED_UNICODE) ?>;
        let usuario1 = <?php echo json_encode($usuario1 ?? null, JSON_UNESCAPED_UNICODE) ?>;
        let usuario2 = <?php echo json_encode($usuario2 ?? null, JSON_UNESCAPED_UNICODE) ?>;
        console.log(usuario1);
        console.log(usuario2);
        console.log(turno);

        setInterval(() => {
            fetchConsultaTurno().then(turnoConsultado => {
                // Al cambiar el turno cuando se pulsa un botón entrará en el if y recargará la página con los valores de la BBDD correctos
                if (turno != turnoConsultado) {
                    window.location.reload();
                }
            });
        }, 1000);
        let claseBotonesPersonaje1 = document.getElementsByClassName("botonesPersonaje1");
        let claseBotonesPersonaje2 = document.getElementsByClassName("botonesPersonaje2");

        // Comprobamos que usuario somos en que turno estamos para habilitar o deshabilitar los botones
        setInterval(function() {
            if (usuario1 == true) {
                if (turno == 1) {
                    for (const element of claseBotonesPersonaje1) {
                        element.removeAttribute("disabled");
                        element.removeAttribute("style");
                    }
                } else if (turno == 2) {
                    for (const element of claseBotonesPersonaje1) {
                        element.setAttribute("disabled", "");
                        element.setAttribute("style", "opacity: 0.5;")
                    }
                }
            } else if (usuario2 == true) {
                if (turno == 2) {
                    for (const element of claseBotonesPersonaje2) {
                        element.removeAttribute("disabled");
                        element.removeAttribute("style");
                    }
                } else if (turno == 1) {
                    for (const element of claseBotonesPersonaje2) {
                        element.setAttribute("disabled", "");
                        element.setAttribute("style", "opacity: 0.5;")
                    }
                }
            }
        }, 1000);
    </script>
    <h3 id="h3Turno"></h3>
    <script>
        let h3Turno = document.getElementById("h3Turno");
        h3Turno.textContent = "Turno: " + turno;
    </script>
    <div id="campoBatalla">
        <div id="personaje1">
            <div id="estadosPersonaje1">
                <img src="imgs/iconos-estado/confundido.gif" id="confundidoPersonaje1">
                <img src="imgs/iconos-estado/fuego.gif" id="quemadoPersonaje1">
                <img src="imgs/iconos-estado/heridoGrave.gif" id="heridoGravePersonaje1">
                <img src="imgs/iconos-estado/heridoLeve.png" id="heridoLevePersonaje1">
                <img src="imgs/iconos-estado/envenenado.gif" id="envenenadoPersonaje1">
                <!-- El siguiente script mira los ids de los estados y cada segundo comprueba si el mapa de estado de personaje tiene algún elemento a true enseña el icono de estado  -->

            </div>
            <!-- Preparamos la consulta para sacar las características de los personajes que almacenamos en un array -->
            <?php
            $listaCaracteristicas = [];

            // La condición del switch da siempre 4 resultados que concuerdan con como están registrados los personajes en la BBDD junto con su tipo que guardamos para crear el personaje posteriormente con JS, pasándoselo por JSON 
            switch (intval($lista[0]) % 4) {
                case 1:
                    $consultaArquero = "SELECT * FROM partida pa 
                    JOIN personaje pe ON pa.usuario1_id=pe.usuario_id
                    JOIN arquero a ON pe.id_personaje=a.id_personaje
                    WHERE pa.id_partida = " . $_SESSION['partida'] . "";
                    $resultado = $bd->query($consultaArquero);
                    while ($fila = $resultado->fetch_assoc()) {
                        array_push($listaCaracteristicas, $fila);
                    }
                    $consultaarmaArquero = "SELECT i.nombre, ig.cantidad, i.desgaste 
                    FROM partida pa JOIN personaje pe ON pa.usuario1_id=pe.usuario_id
                    JOIN arquero a ON pe.id_personaje=a.id_personaje
					JOIN item_guardado ig ON a.id_personaje  = ig.personaje_id
                    JOIN item i ON ig.item_id = i.id_item
                    where tipo = 'arma';";
                    $resultado = $bd->query($consultaarmaArquero);
                    while ($fila = $resultado->fetch_assoc()) {
                        $listaCaracteristicas['arma'][$fila['nombre']] = $fila;
                    }
                    $consultaCuracionArquero = "SELECT i.nombre, ig.cantidad, i.desgaste 
                    FROM partida pa JOIN personaje pe ON pa.usuario1_id=pe.usuario_id
                    JOIN arquero a ON pe.id_personaje=a.id_personaje
					JOIN item_guardado ig ON a.id_personaje  = ig.personaje_id
                    JOIN item i ON ig.item_id = i.id_item
                    where tipo = 'curacion';";
                    $resultado = $bd->query($consultaCuracionArquero);
                    while ($fila = $resultado->fetch_assoc()) {
                        $listaCaracteristicas['curacion'][$fila['nombre']] = $fila;
                    }
                    $consultaEstaminaArquero = "SELECT i.nombre, ig.cantidad, i.desgaste 
                    FROM partida pa JOIN personaje pe ON pa.usuario1_id=pe.usuario_id
                    JOIN arquero a ON pe.id_personaje=a.id_personaje
					JOIN item_guardado ig ON a.id_personaje  = ig.personaje_id
                    JOIN item i ON ig.item_id = i.id_item
                    where tipo = 'restaurarEstamina';";
                    $resultado = $bd->query($consultaEstaminaArquero);
                    while ($fila = $resultado->fetch_assoc()) {
                        $listaCaracteristicas['estamina'][$fila['nombre']] = $fila;
                    }
                    $listaCaracteristicas["tipo"] = "Arquero";
                    break;
                case 2:
                    $consultaCaballero = "SELECT * FROM partida pa 
                    JOIN personaje pe ON pa.usuario1_id=pe.usuario_id
                    JOIN caballero c ON pe.id_personaje=c.id_personaje
                    WHERE pa.id_partida = " . $_SESSION['partida'] . "";
                    $resultado = $bd->query($consultaCaballero);
                    while ($fila = $resultado->fetch_assoc()) {
                        array_push($listaCaracteristicas, $fila);
                    }
                    $consultaarmaCaballero = "SELECT i.nombre, ig.cantidad, i.desgaste 
                    FROM partida pa JOIN personaje pe ON pa.usuario1_id=pe.usuario_id
                    JOIN caballero c ON pe.id_personaje=c.id_personaje
					JOIN item_guardado ig ON c.id_personaje  = ig.personaje_id
                    JOIN item i ON ig.item_id = i.id_item
                    where tipo = 'arma';";
                    $resultado = $bd->query($consultaarmaCaballero);
                    while ($fila = $resultado->fetch_assoc()) {
                        $listaCaracteristicas['arma'][$fila['nombre']] = $fila;
                    }
                    $consultaCuracionCaballero = "SELECT i.nombre, ig.cantidad, i.desgaste 
                    FROM partida pa JOIN personaje pe ON pa.usuario1_id=pe.usuario_id
                    JOIN caballero c ON pe.id_personaje=c.id_personaje
					JOIN item_guardado ig ON c.id_personaje  = ig.personaje_id
                    JOIN item i ON ig.item_id = i.id_item
                    where tipo = 'curacion';";
                    $resultado = $bd->query($consultaCuracionCaballero);
                    while ($fila = $resultado->fetch_assoc()) {
                        $listaCaracteristicas['curacion'][$fila['nombre']] = $fila;
                    }
                    $consultaEstaminaCaballero = "SELECT i.nombre, ig.cantidad, i.desgaste 
                    FROM partida pa JOIN personaje pe ON pa.usuario1_id=pe.usuario_id
                    JOIN caballero c ON pe.id_personaje=c.id_personaje
					JOIN item_guardado ig ON c.id_personaje  = ig.personaje_id
                    JOIN item i ON ig.item_id = i.id_item
                    where tipo = 'restaurarEstamina';";
                    $resultado = $bd->query($consultaEstaminaCaballero);
                    while ($fila = $resultado->fetch_assoc()) {
                        $listaCaracteristicas['estamina'][$fila['nombre']] = $fila;
                    }
                    $listaCaracteristicas["tipo"] = "Caballero";
                    break;
                case 3:
                    $consultaHechicero = "SELECT * FROM partida pa 
                    JOIN personaje pe ON pa.usuario1_id=pe.usuario_id
                    JOIN hechicero h ON pe.id_personaje=h.id_personaje
                    WHERE pa.id_partida = " . $_SESSION['partida'] . "";
                    $resultado = $bd->query($consultaHechicero);
                    while ($fila = $resultado->fetch_assoc()) {
                        array_push($listaCaracteristicas, $fila);
                    }
                    $consultaarmaHechicero = "SELECT i.nombre, ig.cantidad, i.desgaste 
                    FROM partida pa JOIN personaje pe ON pa.usuario1_id=pe.usuario_id
                    JOIN hechicero h ON pe.id_personaje=h.id_personaje
					JOIN item_guardado ig ON h.id_personaje  = ig.personaje_id
                    JOIN item i ON ig.item_id = i.id_item
                    where tipo = 'arma';";
                    $resultado = $bd->query($consultaarmaHechicero);
                    while ($fila = $resultado->fetch_assoc()) {
                        $listaCaracteristicas['arma'][$fila['nombre']] = $fila;
                    }
                    $consultaCuracionHechicero = "SELECT i.nombre, ig.cantidad, i.desgaste 
                    FROM partida pa JOIN personaje pe ON pa.usuario1_id=pe.usuario_id
                    JOIN hechicero h ON pe.id_personaje=h.id_personaje
					JOIN item_guardado ig ON h.id_personaje  = ig.personaje_id
                    JOIN item i ON ig.item_id = i.id_item
                    where tipo = 'curacion';";
                    $resultado = $bd->query($consultaCuracionHechicero);
                    while ($fila = $resultado->fetch_assoc()) {
                        $listaCaracteristicas['curacion'][$fila['nombre']] = $fila;
                    }
                    $consultaEstaminaHechicero = "SELECT i.nombre, ig.cantidad, i.desgaste 
                    FROM partida pa JOIN personaje pe ON pa.usuario1_id=pe.usuario_id
                    JOIN hechicero h ON pe.id_personaje=h.id_personaje
					JOIN item_guardado ig ON h.id_personaje  = ig.personaje_id
                    JOIN item i ON ig.item_id = i.id_item
                    where tipo = 'restaurarEstamina';";
                    $resultado = $bd->query($consultaEstaminaHechicero);
                    while ($fila = $resultado->fetch_assoc()) {
                        $listaCaracteristicas['estamina'][$fila['nombre']] = $fila;
                    }
                    $listaCaracteristicas["tipo"] = "Hechicero";
                    break;
                case 0:
                    $consultaDruida = "SELECT * FROM partida pa 
                    JOIN personaje pe ON pa.usuario1_id=pe.usuario_id
                    JOIN druida d ON pe.id_personaje=d.id_personaje
                    WHERE pa.id_partida = " . $_SESSION['partida'] . "";
                    $resultado = $bd->query($consultaDruida);
                    while ($fila = $resultado->fetch_assoc()) {
                        array_push($listaCaracteristicas, $fila);
                    }
                    $consultaarmaDruida = "SELECT i.nombre, ig.cantidad, i.desgaste 
                    FROM partida pa JOIN personaje pe ON pa.usuario1_id=pe.usuario_id
                    JOIN druida d ON pe.id_personaje=d.id_personaje
					JOIN item_guardado ig ON d.id_personaje  = ig.personaje_id
                    JOIN item i ON ig.item_id = i.id_item
                    where tipo = 'arma';";
                    $resultado = $bd->query($consultaarmaDruida);
                    while ($fila = $resultado->fetch_assoc()) {
                        $listaCaracteristicas['arma'][$fila['nombre']] = $fila;
                    }
                    $consultaCuracionDruida = "SELECT i.nombre, ig.cantidad, i.desgaste 
                    FROM partida pa JOIN personaje pe ON pa.usuario1_id=pe.usuario_id
                    JOIN druida d ON pe.id_personaje=d.id_personaje
					JOIN item_guardado ig ON d.id_personaje  = ig.personaje_id
                    JOIN item i ON ig.item_id = i.id_item
                    where tipo = 'curacion';";
                    $resultado = $bd->query($consultaCuracionDruida);
                    while ($fila = $resultado->fetch_assoc()) {
                        $listaCaracteristicas['curacion'][$fila['nombre']] = $fila;
                    }
                    $consultaEstaminaDruida = "SELECT i.nombre, ig.cantidad, i.desgaste 
                    FROM partida pa JOIN personaje pe ON pa.usuario1_id=pe.usuario_id
                    JOIN druida d ON pe.id_personaje=d.id_personaje
					JOIN item_guardado ig ON d.id_personaje  = ig.personaje_id
                    JOIN item i ON ig.item_id = i.id_item
                    where tipo = 'restaurarEstamina';";
                    $resultado = $bd->query($consultaEstaminaDruida);
                    while ($fila = $resultado->fetch_assoc()) {
                        $listaCaracteristicas['estamina'][$fila['nombre']] = $fila;
                    }
                    $listaCaracteristicas["tipo"] = "Druida";
                    break;
                default:
                    echo 'No se ha creado bien el personaje 1, entró en el default del switch';
                    break;
            }
            ?>

            <script>
                let caracteristicasPersonaje1 = <?php echo json_encode($listaCaracteristicas, JSON_UNESCAPED_UNICODE); ?>;
                console.log(caracteristicasPersonaje1);
                let divPersonaje1 = document.getElementById("personaje1");
                let imgPersonaje1 = document.createElement("img");
                let personaje1;
                switch (caracteristicasPersonaje1["tipo"]) {
                    case "Arquero":
                        personaje1 = constructorArquero(caracteristicasPersonaje1);
                        imgPersonaje1.setAttribute("src", "imgs/arqueroDerecha.gif");
                        imgPersonaje1.setAttribute("id", "imgPersonaje1");
                        break;
                    case "Caballero":
                        personaje1 = constructorCaballero(caracteristicasPersonaje1);
                        imgPersonaje1.setAttribute("src", "imgs/caballeroDerecha.gif");
                        imgPersonaje1.setAttribute("id", "imgPersonaje1");
                        break;
                    case "Hechicero":
                        personaje1 = constructorHechicero(caracteristicasPersonaje1);
                        switch (true) {
                            case (personaje1.aura.get("fuego") == 1):
                                imgPersonaje1.setAttribute("id", "imgPersonaje1");
                                imgPersonaje1.setAttribute("src", "imgs/Estados-hechicero/hechiceroFuegoDerecha.png");
                                break;
                            case (personaje1.aura.get("veneno") == 1):
                                imgPersonaje1.setAttribute("id", "imgPersonaje1");
                                imgPersonaje1.setAttribute("src", "imgs/Estados-hechicero/hechiceroVenenoDerecha.png");
                                break;
                            case (personaje1.aura.get("enigmatico") == 1):
                                imgPersonaje1.setAttribute("id", "imgPersonaje1");
                                imgPersonaje1.setAttribute("src", "imgs/Estados-hechicero/hechiceroEnigmaDerecha.png");
                                break;
                            case (personaje1.aura.get("pinchos") == 1):
                                imgPersonaje1.setAttribute("id", "imgPersonaje1");
                                imgPersonaje1.setAttribute("src", "imgs/Estados-hechicero/hechiceroPinchosDerecha.png");
                                break;
                            case (personaje1.aura.get("sombra") == 1):
                                imgPersonaje1.setAttribute("id", "imgPersonaje1");
                                imgPersonaje1.setAttribute("src", "imgs/Estados-hechicero/hechiceroSombrioDerecha.gif");
                                break;
                            default:
                                imgPersonaje1.setAttribute("id", "imgPersonaje1");
                                imgPersonaje1.setAttribute("src", "imgs/hechiceroDerecha.gif");
                                break;
                        }
                        break;
                    case "Druida":
                        personaje1 = constructorDruida(caracteristicasPersonaje1);

                        switch (true) {
                            case (personaje1.posiblesTransformaciones.get("oso") == 1):
                                imgPersonaje1.setAttribute("id", "imgPersonaje1");
                                imgPersonaje1.setAttribute("src", "imgs/Transformaciones-druida/osoDerecha.gif");
                                break;
                            case (personaje1.posiblesTransformaciones.get("serpiente") == 1):
                                imgPersonaje1.setAttribute("id", "imgPersonaje1");
                                imgPersonaje1.setAttribute("src", "imgs/Transformaciones-druida/serpienteDerecha.gif");
                                break;
                            case (personaje1.posiblesTransformaciones.get("zorro") == 1):
                                imgPersonaje1.setAttribute("id", "imgPersonaje1");
                                imgPersonaje1.setAttribute("src", "imgs/Transformaciones-druida/zorroDerecha.gif");
                                break;
                            case (personaje1.posiblesTransformaciones.get("águila") == 1):
                                imgPersonaje1.setAttribute("id", "imgPersonaje1");
                                imgPersonaje1.setAttribute("src", "imgs/Transformaciones-druida/aguilaDerecha.gif");
                                break;
                            default:
                                imgPersonaje1.setAttribute("id", "imgPersonaje1");
                                imgPersonaje1.setAttribute("src", "imgs/druidaDerecha.gif");
                                break;
                        }
                        break;
                    default:
                        break;
                }
                console.log(personaje1);
                divPersonaje1.appendChild(imgPersonaje1);
            </script>
            <div id="barraVidaPersonaje1">
                <div id="vidaRealPersonaje1">Vida</div>
            </div>
            <div id="barraEstaminaPersonaje1">
                <div id="estaminaRealPersonaje1"></div>
            </div>

            <script>
                //script que comprueba cada 100ms si ha variado tanto la vida como la estamina del personaje1
                let barraVidaPersonaje1 = document.getElementById("vidaRealPersonaje1");
                setInterval(function() {
                    barraVidaPersonaje1.style.width = personaje1.vidaActual + "%";
                    barraVidaPersonaje1.textContent = `Vida ${personaje1.vidaActual}`;
                }, 100);
                if (personaje1 instanceof Arquero || personaje1 instanceof Caballero) {
                    let estaminaRealPersonaje1 = document.getElementById("estaminaRealPersonaje1");
                    setInterval(function() {
                        estaminaRealPersonaje1.style.width = personaje1.estaminaActual + "%";
                        estaminaRealPersonaje1.textContent = `Aguante ${personaje1.estaminaActual}`;
                    }, 100);
                } else if (personaje1 instanceof Druida || personaje1 instanceof Hechicero) {
                    let estaminaRealPersonaje1 = document.getElementById("estaminaRealPersonaje1");
                    setInterval(function() {
                        estaminaRealPersonaje1.style.width = personaje1.estaminaActual + "%";
                        estaminaRealPersonaje1.textContent = `Maná ${personaje1.estaminaActual}`;
                    }, 100);
                }
            </script>


            <div id="nombrePersonaje1">
                <script>
                    let nombrePersonaje1 = document.createElement("p");
                    nombrePersonaje1.textContent = personaje1.nombre;
                    document.getElementById("nombrePersonaje1").appendChild(nombrePersonaje1);
                </script>
            </div>
            <div id="cuadroBotones1">
                <script>
                    let botonesPersonaje1 = document.getElementById("cuadroBotones1");
                    switch (true) {
                        case (personaje1 instanceof Arquero):
                            console.log("es un arquero");
                            for (let i = 0; i < 4; i++) {
                                let boton = document.createElement("button");
                                boton.textContent = personaje1.listaBotones[i];
                                boton.setAttribute("class", "botonesPersonaje1 btn-demon");
                                let accion = personaje1.listaFunciones[i];
                                if (i > 1) {
                                    boton.setAttribute("onclick", "personaje1." + accion + "()");
                                    boton.setAttribute("disabled", "");
                                    boton.setAttribute("style", "opacity: 0.5;");
                                    botonesPersonaje1.appendChild(boton);
                                } else {
                                    boton.setAttribute("onclick", "personaje1." + accion + "(personaje2)");
                                    boton.setAttribute("disabled", "");
                                    boton.setAttribute("style", "opacity: 0.5;");
                                    botonesPersonaje1.appendChild(boton);
                                }
                            }
                            break;

                        case (personaje1 instanceof Caballero):
                            console.log("Es un caballero");
                            for (let i = 0; i < 4; i++) {
                                let boton = document.createElement("button");
                                boton.textContent = personaje1.listaBotones[i];
                                boton.setAttribute("class", "botonesPersonaje1 btn-demon");
                                let accion = personaje1.listaFunciones[i];
                                if (i == 2) {
                                    boton.setAttribute("onclick", "personaje1." + accion + "()");
                                    boton.setAttribute("disabled", "");
                                    boton.setAttribute("style", "opacity: 0.5;");
                                    botonesPersonaje1.appendChild(boton);
                                } else {
                                    boton.setAttribute("onclick", "personaje1." + accion + "(personaje2)");
                                    boton.setAttribute("disabled", "");
                                    boton.setAttribute("style", "opacity: 0.5;");
                                    botonesPersonaje1.appendChild(boton);
                                }
                            }
                            break;

                        case (personaje1 instanceof Druida):
                            console.log("Es un Druida");
                            //Funcion que se llama al hacer la transformacion para cambiar la imagen segun la transformacion

                            for (let i = 0; i < 4; i++) {
                                let boton = document.createElement("button");
                                boton.textContent = personaje1.listaBotones[i];
                                boton.setAttribute("class", "botonesPersonaje1 btn-demon");
                                let accion = personaje1.listaFunciones[i];
                                if (i == 1) {
                                    boton.setAttribute("onclick", "personaje1.transformacion()");
                                    boton.setAttribute("disabled", "");
                                    boton.setAttribute("style", "opacity: 0.5;");
                                    botonesPersonaje1.appendChild(boton);
                                } else if (i == 2) {
                                    boton.setAttribute("onclick", "personaje1." + accion + "()");
                                    boton.setAttribute("disabled", "");
                                    boton.setAttribute("style", "opacity: 0.5;");
                                    botonesPersonaje1.appendChild(boton);
                                } else {
                                    boton.setAttribute("onclick", "personaje1." + accion + "(personaje2)");
                                    boton.setAttribute("disabled", "");
                                    boton.setAttribute("style", "opacity: 0.5;");
                                    botonesPersonaje1.appendChild(boton);
                                }
                            }
                            break;

                        case (personaje1 instanceof Hechicero):
                            console.log("Es un hechicero");
                            for (let i = 0; i < 4; i++) {
                                let boton = document.createElement("button");
                                boton.textContent = personaje1.listaBotones[i];
                                boton.setAttribute("class", "botonesPersonaje1 btn-demon");
                                let accion = personaje1.listaFunciones[i];
                                if (i == 1) {
                                    boton.setAttribute("onclick", "personaje1.farmearAura()");
                                    boton.setAttribute("disabled", "");
                                    boton.setAttribute("style", "opacity: 0.5;");
                                    botonesPersonaje1.appendChild(boton);
                                } else if (i == 2) {
                                    boton.setAttribute("onclick", "personaje1." + accion + "()");
                                    boton.setAttribute("disabled", "");
                                    boton.setAttribute("style", "opacity: 0.5;");
                                    botonesPersonaje1.appendChild(boton);
                                } else {
                                    boton.setAttribute("onclick", "personaje1." + accion + "(personaje2)");
                                    boton.setAttribute("disabled", "");
                                    boton.setAttribute("style", "opacity: 0.5;");
                                    botonesPersonaje1.appendChild(boton);
                                }
                            }
                            break;
                        default:
                            console.log("personaje1 no válido");
                            break;
                    }

                    let clavesCuracionPersonaje1 = personaje1.inventario.get("curacion").keys();
                    for (const element of clavesCuracionPersonaje1) {
                        let boton = document.createElement("button");
                        boton.textContent = element;
                        boton.setAttribute("class", "botonesPersonaje1 btn-demon");
                        boton.setAttribute("disabled", "");
                        boton.setAttribute("style", "opacity: 0.5;");
                        boton.setAttribute("onclick", "personaje1.curarVida('" + element + "')");
                        botonesPersonaje1.appendChild(boton);
                    }
                    let clavesEstaminaPersonaje1 = personaje1.inventario.get("restaurarEstamina").keys();
                    for (const element of clavesEstaminaPersonaje1) {
                        let boton = document.createElement("button");
                        boton.textContent = element;
                        boton.setAttribute("class", "botonesPersonaje1 btn-demon");
                        boton.setAttribute("disabled", "");
                        boton.setAttribute("style", "opacity: 0.5;");
                        boton.setAttribute("onclick", "personaje1.restaurarEstamina('" + element + "')");
                        botonesPersonaje1.appendChild(boton);
                    }

                    divPersonaje1.addEventListener("click", function(event) {
                        // Recalcular después de la acción
                        let evento = event.target;
                        if (evento instanceof HTMLButtonElement) {
                            fetchUpdate(personaje1);
                            fetchTurno(turno);
                            fetchUpdate(personaje2);
                            event.stopPropagation(); // Detener la propagación del evento para evitar conflictos con otros botones
                        }
                    });
                </script>
            </div>
        </div>

        <!-- PERSONAJE 2 -->
        <div id="personaje2">
            <div id="estadosPersonaje2">
                <img src="imgs/iconos-estado/confundido.gif" id="confundidoPersonaje2">
                <img src="imgs/iconos-estado/fuego.gif" id="quemadoPersonaje2">
                <img src="imgs/iconos-estado/heridoGrave.gif" id="heridoGravePersonaje2">
                <img src="imgs/iconos-estado/heridoLeve.png" id="heridoLevePersonaje2">
                <img src="imgs/iconos-estado/envenenado.gif" id="envenenadoPersonaje2">
                <!-- El siguiente script mira los ids de los estados y cada segundo comprueba si el mapa de estado de personaje tiene algún elemento a true enseña el icono de estado -->

            </div>
            <?php
            unset($listaCaracteristicas);
            $listaCaracteristicas = [];
            switch (intval($lista[1]) % 4) {
                case 1:
                    $consultaArquero = "SELECT * FROM partida pa 
                    JOIN personaje pe ON pa.usuario2_id=pe.usuario_id
                    JOIN arquero a ON pe.id_personaje=a.id_personaje
                    WHERE pa.id_partida = " . $_SESSION['partida'] . "";
                    $resultado = $bd->query($consultaArquero);
                    while ($fila = $resultado->fetch_assoc()) {
                        array_push($listaCaracteristicas, $fila);
                    }
                    $consultaarmaArquero = "SELECT i.nombre, ig.cantidad, i.desgaste 
                    FROM partida pa JOIN personaje pe ON pa.usuario2_id=pe.usuario_id
                    JOIN arquero a ON pe.id_personaje=a.id_personaje
					JOIN item_guardado ig ON a.id_personaje  = ig.personaje_id
                    JOIN item i ON ig.item_id = i.id_item
                    where tipo = 'arma';";
                    $resultado = $bd->query($consultaarmaArquero);
                    while ($fila = $resultado->fetch_assoc()) {
                        $listaCaracteristicas['arma'][$fila['nombre']] = $fila;
                    }
                    $consultaCuracionArquero = "SELECT i.nombre, ig.cantidad, i.desgaste 
                    FROM partida pa JOIN personaje pe ON pa.usuario2_id=pe.usuario_id
                    JOIN arquero a ON pe.id_personaje=a.id_personaje
					JOIN item_guardado ig ON a.id_personaje  = ig.personaje_id
                    JOIN item i ON ig.item_id = i.id_item
                    where tipo = 'curacion';";
                    $resultado = $bd->query($consultaCuracionArquero);
                    while ($fila = $resultado->fetch_assoc()) {
                        $listaCaracteristicas['curacion'][$fila['nombre']] = $fila;
                    }
                    $consultaEstaminaArquero = "SELECT i.nombre, ig.cantidad, i.desgaste 
                    FROM partida pa JOIN personaje pe ON pa.usuario2_id=pe.usuario_id
                    JOIN arquero a ON pe.id_personaje=a.id_personaje
					JOIN item_guardado ig ON a.id_personaje  = ig.personaje_id
                    JOIN item i ON ig.item_id = i.id_item
                    where tipo = 'restaurarEstamina';";
                    $resultado = $bd->query($consultaEstaminaArquero);
                    while ($fila = $resultado->fetch_assoc()) {
                        $listaCaracteristicas['estamina'][$fila['nombre']] = $fila;
                    }
                    $listaCaracteristicas["tipo"] = "Arquero";
                    break;
                case 2:
                    $consultaCaballero = "SELECT * FROM partida pa 
                    JOIN personaje pe ON pa.usuario2_id=pe.usuario_id
                    JOIN caballero c ON pe.id_personaje=c.id_personaje
                    WHERE pa.id_partida = " . $_SESSION['partida'] . "";
                    $resultado = $bd->query($consultaCaballero);
                    while ($fila = $resultado->fetch_assoc()) {
                        array_push($listaCaracteristicas, $fila);
                    }
                    $consultaarmaCaballero = "SELECT i.nombre, ig.cantidad, i.desgaste 
                    FROM partida pa JOIN personaje pe ON pa.usuario2_id=pe.usuario_id
                    JOIN caballero c ON pe.id_personaje=c.id_personaje
					JOIN item_guardado ig ON c.id_personaje  = ig.personaje_id
                    JOIN item i ON ig.item_id = i.id_item
                    where tipo = 'arma';";
                    $resultado = $bd->query($consultaarmaCaballero);
                    while ($fila = $resultado->fetch_assoc()) {
                        $listaCaracteristicas['arma'][$fila['nombre']] = $fila;
                    }
                    $consultaCuracionCaballero = "SELECT i.nombre, ig.cantidad, i.desgaste 
                    FROM partida pa JOIN personaje pe ON pa.usuario2_id=pe.usuario_id
                    JOIN caballero c ON pe.id_personaje=c.id_personaje
					JOIN item_guardado ig ON c.id_personaje  = ig.personaje_id
                    JOIN item i ON ig.item_id = i.id_item
                    where tipo = 'curacion';";
                    $resultado = $bd->query($consultaCuracionCaballero);
                    while ($fila = $resultado->fetch_assoc()) {
                        $listaCaracteristicas['curacion'][$fila['nombre']] = $fila;
                    }
                    $consultaEstaminaCaballero = "SELECT i.nombre, ig.cantidad, i.desgaste 
                    FROM partida pa JOIN personaje pe ON pa.usuario2_id=pe.usuario_id
                    JOIN caballero c ON pe.id_personaje=c.id_personaje
					JOIN item_guardado ig ON c.id_personaje  = ig.personaje_id
                    JOIN item i ON ig.item_id = i.id_item
                    where tipo = 'restaurarEstamina';";
                    $resultado = $bd->query($consultaEstaminaCaballero);
                    while ($fila = $resultado->fetch_assoc()) {
                        $listaCaracteristicas['estamina'][$fila['nombre']] = $fila;
                    }
                    $listaCaracteristicas["tipo"] = "Caballero";
                    break;
                case 3:
                    $consultaHechicero = "SELECT * FROM partida pa 
                    JOIN personaje pe ON pa.usuario2_id=pe.usuario_id
                    JOIN hechicero h ON pe.id_personaje=h.id_personaje
                    WHERE pa.id_partida = " . $_SESSION['partida'] . "";
                    $resultado = $bd->query($consultaHechicero);
                    while ($fila = $resultado->fetch_assoc()) {
                        array_push($listaCaracteristicas, $fila);
                    }
                    $consultaarmaHechicero = "SELECT i.nombre, ig.cantidad, i.desgaste 
                    FROM partida pa JOIN personaje pe ON pa.usuario2_id=pe.usuario_id
                    JOIN hechicero h ON pe.id_personaje=h.id_personaje
					JOIN item_guardado ig ON h.id_personaje  = ig.personaje_id
                    JOIN item i ON ig.item_id = i.id_item
                    where tipo = 'arma';";
                    $resultado = $bd->query($consultaarmaHechicero);
                    while ($fila = $resultado->fetch_assoc()) {
                        $listaCaracteristicas['arma'][$fila['nombre']] = $fila;
                    }
                    $consultaCuracionHechicero = "SELECT i.nombre, ig.cantidad, i.desgaste 
                    FROM partida pa JOIN personaje pe ON pa.usuario2_id=pe.usuario_id
                    JOIN hechicero h ON pe.id_personaje=h.id_personaje
					JOIN item_guardado ig ON h.id_personaje  = ig.personaje_id
                    JOIN item i ON ig.item_id = i.id_item
                    where tipo = 'curacion';";
                    $resultado = $bd->query($consultaCuracionHechicero);
                    while ($fila = $resultado->fetch_assoc()) {
                        $listaCaracteristicas['curacion'][$fila['nombre']] = $fila;
                    }
                    $consultaEstaminaHechicero = "SELECT i.nombre, ig.cantidad, i.desgaste 
                    FROM partida pa JOIN personaje pe ON pa.usuario2_id=pe.usuario_id
                    JOIN hechicero h ON pe.id_personaje=h.id_personaje
					JOIN item_guardado ig ON h.id_personaje  = ig.personaje_id
                    JOIN item i ON ig.item_id = i.id_item
                    where tipo = 'restaurarEstamina';";
                    $resultado = $bd->query($consultaEstaminaHechicero);
                    while ($fila = $resultado->fetch_assoc()) {
                        $listaCaracteristicas['estamina'][$fila['nombre']] = $fila;
                    }
                    $listaCaracteristicas["tipo"] = "Hechicero";
                    break;
                case 0:
                    $consultaDruida = "SELECT * FROM partida pa 
                    JOIN personaje pe ON pa.usuario2_id=pe.usuario_id
                    JOIN druida d ON pe.id_personaje=d.id_personaje
                    WHERE pa.id_partida = " . $_SESSION['partida'] . "";
                    $resultado = $bd->query($consultaDruida);
                    while ($fila = $resultado->fetch_assoc()) {
                        array_push($listaCaracteristicas, $fila);
                    }
                    $consultaarmaDruida = "SELECT i.nombre, ig.cantidad, i.desgaste 
                    FROM partida pa JOIN personaje pe ON pa.usuario2_id=pe.usuario_id
                    JOIN druida d ON pe.id_personaje=d.id_personaje
					JOIN item_guardado ig ON d.id_personaje  = ig.personaje_id
                    JOIN item i ON ig.item_id = i.id_item
                    where tipo = 'arma';";
                    $resultado = $bd->query($consultaarmaDruida);
                    while ($fila = $resultado->fetch_assoc()) {
                        $listaCaracteristicas['arma'][$fila['nombre']] = $fila;
                    }
                    $consultaCuracionDruida = "SELECT i.nombre, ig.cantidad, i.desgaste 
                    FROM partida pa JOIN personaje pe ON pa.usuario2_id=pe.usuario_id
                    JOIN druida d ON pe.id_personaje=d.id_personaje
					JOIN item_guardado ig ON d.id_personaje  = ig.personaje_id
                    JOIN item i ON ig.item_id = i.id_item
                    where tipo = 'curacion';";
                    $resultado = $bd->query($consultaCuracionDruida);
                    while ($fila = $resultado->fetch_assoc()) {
                        $listaCaracteristicas['curacion'][$fila['nombre']] = $fila;
                    }
                    $consultaEstaminaDruida = "SELECT i.nombre, ig.cantidad, i.desgaste 
                    FROM partida pa JOIN personaje pe ON pa.usuario2_id=pe.usuario_id
                    JOIN druida d ON pe.id_personaje=d.id_personaje
					JOIN item_guardado ig ON d.id_personaje  = ig.personaje_id
                    JOIN item i ON ig.item_id = i.id_item
                    where tipo = 'restaurarEstamina';";
                    $resultado = $bd->query($consultaEstaminaDruida);
                    while ($fila = $resultado->fetch_assoc()) {
                        $listaCaracteristicas['estamina'][$fila['nombre']] = $fila;
                    }
                    $listaCaracteristicas["tipo"] = "Druida";
                    break;
                default:
                    echo 'No se ha creado bien el personaje 2, entró en el default del switch';
                    break;
            }
            ?>
            <script>
                let caracteristicasPersonaje2 = <?php echo json_encode($listaCaracteristicas, JSON_UNESCAPED_UNICODE); ?>;
                console.log(caracteristicasPersonaje2);
                let personaje2;
                let divPersonaje2 = document.getElementById("personaje2");
                let imgPersonaje2 = document.createElement("img");
                switch (caracteristicasPersonaje2["tipo"]) {
                    case "Arquero":
                        personaje2 = constructorArquero(caracteristicasPersonaje2);
                        imgPersonaje2.setAttribute("src", "imgs/arqueroIzquierda.gif");
                        imgPersonaje2.setAttribute("id", "imgPersonaje2");
                        break;
                    case "Caballero":
                        personaje2 = constructorCaballero(caracteristicasPersonaje2);
                        imgPersonaje2.setAttribute("src", "imgs/caballeroIzquierda.gif");
                        imgPersonaje2.setAttribute("id", "imgPersonaje2");
                        break;
                    case "Hechicero":
                        personaje2 = constructorHechicero(caracteristicasPersonaje2);
                        switch (true) {
                            case (personaje2.aura.get("fuego") == 1):
                                imgPersonaje2.setAttribute("id", "imgPersonaje2");
                                imgPersonaje2.setAttribute("src", "imgs/Estados-hechicero/hechiceroFuegoIzquierda.png");
                                break;
                            case (personaje2.aura.get("veneno") == 1):
                                imgPersonaje2.setAttribute("id", "imgPersonaje2");
                                imgPersonaje2.setAttribute("src", "imgs/Estados-hechicero/hechiceroVenenoIzquierda.png");
                                break;
                            case (personaje2.aura.get("enigmatico") == 1):
                                imgPersonaje2.setAttribute("id", "imgPersonaje2");
                                imgPersonaje2.setAttribute("src", "imgs/Estados-hechicero/hechiceroEnigmaIzquierda.png");
                                break;
                            case (personaje2.aura.get("pinchos") == 1):
                                imgPersonaje2.setAttribute("id", "imgPersonaje2");
                                imgPersonaje2.setAttribute("src", "imgs/Estados-hechicero/hechiceroPinchosIzquierda.png");
                                break;
                            case (personaje2.aura.get("sombra") == 1):
                                imgPersonaje2.setAttribute("id", "imgPersonaje2");
                                imgPersonaje2.setAttribute("src", "imgs/Estados-hechicero/hechiceroSombrioIzquierda.gif");
                                break;
                            default:
                                imgPersonaje2.setAttribute("id", "imgPersonaje2");
                                imgPersonaje2.setAttribute("src", "imgs/hechiceroIzquierda.gif");
                                break;
                        }
                        break;
                    case "Druida":
                        personaje2 = constructorDruida(caracteristicasPersonaje2);
                        switch (true) {
                            case (personaje2.posiblesTransformaciones.get("oso") == 1):
                                imgPersonaje2.setAttribute("id", "imgPersonaje2");
                                imgPersonaje2.setAttribute("src", "imgs/Transformaciones-druida/osoIzquierda.gif");
                                break;
                            case (personaje2.posiblesTransformaciones.get("serpiente") == 1):
                                imgPersonaje2.setAttribute("id", "imgPersonaje2");
                                imgPersonaje2.setAttribute("src", "imgs/Transformaciones-druida/serpienteIzquierda.gif");
                                break;
                            case (personaje2.posiblesTransformaciones.get("zorro") == 1):
                                imgPersonaje2.setAttribute("id", "imgPersonaje2");
                                imgPersonaje2.setAttribute("src", "imgs/Transformaciones-druida/zorroIzquierda.gif");
                                break;
                            case (personaje2.posiblesTransformaciones.get("águila") == 1):
                                imgPersonaje2.setAttribute("id", "imgPersonaje2");
                                imgPersonaje2.setAttribute("src", "imgs/Transformaciones-druida/aguilaIzquierda.gif");
                                break;
                            default:
                                imgPersonaje2.setAttribute("id", "imgPersonaje2");
                                imgPersonaje2.setAttribute("src", "imgs/druidaIzquierda.gif");
                                break;
                        }
                        break;
                    default:
                        break;
                }
                divPersonaje2.appendChild(imgPersonaje2);
                console.log(personaje2);
            </script>


            <div id="barraVidaPersonaje2">
                <div id="vidaRealPersonaje2"></div>
            </div>
            <div id="barraEstaminaPersonaje2">
                <div id="estaminaRealPersonaje2"></div>
            </div>
            <script>
                //script que comprueba cada 100ms si ha variado tanto la vida como la estamina del personaje1
                let barraVidaPersonaje2 = document.getElementById("vidaRealPersonaje2");
                setInterval(function() {
                    barraVidaPersonaje2.style.width = personaje2.vidaActual + "%";
                    barraVidaPersonaje2.textContent = `Vida: ${personaje2.vidaActual}`;
                }, 100);
                if (personaje2 instanceof Arquero || personaje2 instanceof Caballero) {
                    let estaminaRealPersonaje2 = document.getElementById("estaminaRealPersonaje2");
                    setInterval(function() {
                        estaminaRealPersonaje2.style.width = personaje2.estaminaActual + "%";
                        estaminaRealPersonaje2.textContent = `Aguante: ${personaje2.estaminaActual}`;
                    }, 100);
                } else if (personaje2 instanceof Druida || personaje2 instanceof Hechicero) {
                    let estaminaRealPersonaje2 = document.getElementById("estaminaRealPersonaje2");
                    setInterval(function() {
                        estaminaRealPersonaje2.style.width = personaje2.estaminaActual + "%";
                        estaminaRealPersonaje2.textContent = `Maná: ${personaje2.estaminaActual}`;
                    }, 100);
                }
            </script>
            <div id="nombrePersonaje2">
                <script>
                    let nombrePersonaje2 = document.createElement("p");
                    nombrePersonaje2.textContent = personaje2.nombre;
                    document.getElementById("nombrePersonaje2").appendChild(nombrePersonaje2);
                </script>
            </div>
            <div id="cuadroBotones2">
                <script>
                    let botonesPersonaje2 = document.getElementById("cuadroBotones2");
                    switch (true) {
                        case (personaje2 instanceof Arquero):
                            console.log("es un arquero");
                            for (let i = 0; i < 4; i++) {
                                let boton = document.createElement("button");
                                boton.textContent = personaje2.listaBotones[i];
                                boton.setAttribute("class", "botonesPersonaje2 btn-demon");
                                let accion = personaje2.listaFunciones[i];
                                if (i > 1) {
                                    boton.setAttribute("onclick", "personaje2." + accion + "()");
                                    boton.setAttribute("disabled", "");
                                    boton.setAttribute("style", "opacity: 0.5;");
                                    botonesPersonaje2.appendChild(boton);
                                } else {
                                    boton.setAttribute("onclick", "personaje2." + accion + "(personaje1)");
                                    boton.setAttribute("disabled", "");
                                    boton.setAttribute("style", "opacity: 0.5;");
                                    botonesPersonaje2.appendChild(boton);
                                }
                            }
                            break;

                        case (personaje2 instanceof Caballero):
                            console.log("es un caballero");
                            for (let i = 0; i < 4; i++) {
                                let boton = document.createElement("button");
                                boton.textContent = personaje2.listaBotones[i];
                                boton.setAttribute("class", "botonesPersonaje2 btn-demon");
                                let accion = personaje2.listaFunciones[i];
                                if (i == 2) {
                                    boton.setAttribute("onclick", "personaje2." + accion + "()");
                                    boton.setAttribute("disabled", "");
                                    boton.setAttribute("style", "opacity: 0.5;");
                                    botonesPersonaje2.appendChild(boton);
                                } else {
                                    span.setAttribute("onclick", "personaje2." + accion + "(personaje1)");
                                    boton.setAttribute("disabled", "");
                                    boton.setAttribute("style", "opacity: 0.5;");
                                    botonesPersonaje2.appendChild(boton);
                                }
                            }
                            break;

                        case (personaje2 instanceof Druida):
                            console.log("Es un Druida");
                            for (let i = 0; i < 4; i++) {
                                let boton = document.createElement("button");
                                boton.textContent = personaje2.listaBotones[i];
                                boton.setAttribute("class", "botonesPersonaje2 btn-demon");
                                let accion = personaje2.listaFunciones[i];
                                if (i == 1) {
                                    boton.setAttribute("onclick", "personaje2.transformacion()");
                                    boton.setAttribute("disabled", "");
                                    boton.setAttribute("style", "opacity: 0.5;");
                                    botonesPersonaje2.appendChild(boton);
                                } else if (i == 2) {
                                    boton.setAttribute("onclick", "personaje2." + accion + "()");
                                    boton.setAttribute("disabled", "");
                                    boton.setAttribute("style", "opacity: 0.5;");
                                    botonesPersonaje2.appendChild(boton);
                                } else {
                                    boton.setAttribute("onclick", "personaje2." + accion + "(personaje1)");
                                    boton.setAttribute("disabled", "");
                                    boton.setAttribute("style", "opacity: 0.5;");
                                    botonesPersonaje2.appendChild(boton);
                                }
                            }
                            break;

                        case (personaje2 instanceof Hechicero):
                            console.log("Es un hechicero");
                            for (let i = 0; i < 4; i++) {
                                let boton = document.createElement("button");
                                boton.textContent = personaje2.listaBotones[i];
                                boton.setAttribute("class", "botonesPersonaje2 btn-demon");
                                let accion = personaje2.listaFunciones[i];
                                if (i == 1) {
                                    boton.setAttribute("onclick", "personaje2.farmearAura()");
                                    boton.setAttribute("disabled", "");
                                    boton.setAttribute("style", "opacity: 0.5;");
                                    botonesPersonaje2.appendChild(boton);
                                } else if (i == 2) {
                                    boton.setAttribute("onclick", "personaje2." + accion + "()");
                                    boton.setAttribute("disabled", "");
                                    boton.setAttribute("style", "opacity: 0.5;");
                                    botonesPersonaje2.appendChild(boton);
                                } else {
                                    boton.setAttribute("onclick", "personaje2." + accion + "(personaje1)");
                                    boton.setAttribute("disabled", "");
                                    boton.setAttribute("style", "opacity: 0.5;");
                                    botonesPersonaje2.appendChild(boton);
                                }
                            }
                            break;
                        default:
                            console.log("personaje2 no válido");
                            break;
                    }
                    let clavesCuracionPersonaje2 = personaje2.inventario.get("curacion").keys();
                    for (const element of clavesCuracionPersonaje2) {
                        let boton = document.createElement("button");
                        boton.textContent = element;
                        boton.setAttribute("class", "botonesPersonaje2 btn-demon");
                        boton.setAttribute("disabled", "");
                        boton.setAttribute("style", "opacity: 0.5;");
                        boton.setAttribute("onclick", `personaje2.curarVida(${element})`)
                        botonesPersonaje2.appendChild(boton);
                    }
                    let clavesEstaminaPersonaje2 = personaje2.inventario.get("restaurarEstamina").keys();
                    for (const element of clavesEstaminaPersonaje2) {
                        let boton = document.createElement("button");
                        boton.textContent = element;
                        boton.setAttribute("class", "botonesPersonaje2 btn-demon");
                        boton.setAttribute("disabled", "");
                        boton.setAttribute("style", "opacity: 0.5;");
                        boton.setAttribute("onclick", `personaje2.restaurarEstamina(${element})`)
                        botonesPersonaje2.appendChild(boton);
                    }
                </script>
            </div>
            <script>
                divPersonaje2.addEventListener("click", function(event) {
                    // Recalcular después de la acción
                    let evento = event.target;
                    if (evento instanceof HTMLButtonElement) {
                        fetchUpdate(personaje2);
                        fetchTurno(turno);
                        fetchUpdate(personaje1);
                        event.stopPropagation(); // Detener la propagación del evento para evitar conflictos con otros botones
                    }
                });

                setInterval(function() {
                    if (personaje2.estado.get("confundido") == true) {
                        for (const element of claseBotonesPersonaje2) {
                            element.textContent = "????"
                        }
                    }
                });
            </script>
        </div>
        <script>
            // Compruebo el estado y le cambio la opacidad a los iconos de estado de los dos personajes
            setInterval(comprobarEstado(personaje1, personaje2), 1000);
        </script>
    </div>
</body>

</html>