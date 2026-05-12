<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    require_once "bd.php";
    $bd->autocommit(false);
    $json = file_get_contents('php://input');
    // TRABAJO PARA MÁXIMO
    // error_log("JSON recibido: " . $json);
    $caracteristicasPersonaje = json_decode($json, true);

    $errorUpdateConsulta = false;
    $descripcionError = "";
    $consultaTurno = "SELECT * FROM partida WHERE id_partida = " . $_SESSION['partida'] . "";
    $resultadoTurno = $bd->query($consultaTurno);
    if ($bd->errno) {
        $errorUpdateConsulta = true;
        $descripcionError = "error en el select de la partida";
    }
    $lista = [];
    $personajeId = "";
    
    if ($fila = $resultadoTurno->fetch_assoc()) {
        //AQUÍ ESTA TODO EL PUTO PROBLEMA, HAY QUE PONERLE 4 OPCIONES DE MANERA QUE ENTRE EN UNA U OTRA DEPENDIENDO DE LA INFORMACIÓN QUE MANDAMOS, HAY QUE PONERLE UN $_SESSION DE PERSONAJE PARA COMPROBAR Y VER SI ESTAMOS MANDANDO LA INFO QUE ES
        error_log("turno en la consulta: " . $fila['turno']);
        if ($fila['turno'] == 1 && $fila['usuario1_id'] == $_SESSION['usuarioPartida']) {
            $personajeId = "pa.personaje1_id";
            $numLista = 0;
            error_log(print_r("turno 1, usuario1", true));
        } elseif ($fila['turno'] == 1 && $fila['usuario2_id'] == $_SESSION['usuarioPartida']) {
            $personajeId = "pa.personaje1_id";
            $numLista = 0;
            error_log(print_r("turno 1, usuario2", true));
        } elseif ($fila['turno'] == 2 && $fila['usuario2_id'] == $_SESSION['usuarioPartida']) {
            $personajeId = "pa.personaje2_id";
            $numLista = 1;
            error_log(print_r("turno 2, usuario2", true));
        }elseif ($fila['turno'] == 2 && $fila['usuario1_id'] == $_SESSION['usuarioPartida']) {
            $personajeId = "pa.personaje2_id";
            $numLista = 1;
            error_log(print_r("turno 2, usuario1", true));
        }
        // error_log("personajeId: " . $personajeId. "variable de sesion".$_SESSION['usuarioPartida']);

        array_push($lista, $fila['personaje1_id']);
        array_push($lista, $fila['personaje2_id']);
    }
    error_log("lista: ".print_r($lista[$numLista], true));
    
    // error_log("arco: " . print_r($caracteristicasPersonaje['inventarioPersonaje']['arma']['arco'], true));
    // error_log("espada: " . print_r($caracteristicasPersonaje['inventarioPersonaje']['arma']['espada'], true));

    error_log("valor del switch: " . print_r($lista[$numLista] % 4, true));
    error_log("JSON recibido: " . print_r($json, true));
    switch (intval($lista[$numLista]) % 4) {
        case 1:
            error_log("ENTRÓ EN EL CASE 1");
            $updateArquero = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = " . $personajeId . "
                            JOIN arquero a ON a.id_personaje = " . $personajeId . "
                            SET fuerza = '" . $caracteristicasPersonaje['personaje']['fuerza'] . "',
                            armadura = '" . $caracteristicasPersonaje['personaje']['armadura'] . "',
                            vidaActual = '" . $caracteristicasPersonaje['personaje']['vidaActual'] . "',
                            vidaMaxima = '" . $caracteristicasPersonaje['personaje']['vidaMaxima'] . "',
                            estaminaActual = '" . $caracteristicasPersonaje['personaje']['estaminaActual'] . "',
                            estaminaMaxima = '" . $caracteristicasPersonaje['personaje']['estaminaMaxima'] . "',
                            nivel = '" . $caracteristicasPersonaje['personaje']['nivel'] . "',
                            puntosExperiencia = '" . $caracteristicasPersonaje['personaje']['puntosExperiencia'] . "',
                            envenenado = '" . $caracteristicasPersonaje['estadosPersonaje']['envenenado'] . "',
                            quemado = '" . $caracteristicasPersonaje['estadosPersonaje']['quemado'] . "',
                            heridoLeve = '" . $caracteristicasPersonaje['estadosPersonaje']['heridoLeve'] . "',
                            heridoGrave = '" . $caracteristicasPersonaje['estadosPersonaje']['heridoGrave'] . "',
                            confundido = '" . $caracteristicasPersonaje['estadosPersonaje']['confundido'] . "'
                            WHERE pa.id_partida = " . $_SESSION['partida'] . "";
            //error_log($updateArquero);
            $resultado = $bd->query($updateArquero);
            if ($bd->errno) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update del arquero";
            }
            $updateArco = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = " . $personajeId . "
                            JOIN arquero a ON a.id_personaje = " . $personajeId . "
                            JOIN item_guardado ig ON a.id_personaje = ig.personaje_id
                            JOIN item i ON ig.item_id = i.id_item
                            SET desgaste = " . $caracteristicasPersonaje['inventarioPersonaje']['arma']['arco'] . "
                            WHERE pa.id_partida = " . $_SESSION['partida'] . " AND i.nombre = 'arco';";
            //error_log($updateArco);
            $resultado = $bd->query($updateArco);
            if ($bd->errno) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update del arco";
            }
            $updateFlechas = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = " . $personajeId . "
                            JOIN arquero a ON a.id_personaje = " . $personajeId . "
                            JOIN item_guardado ig ON a.id_personaje = ig.personaje_id
                            JOIN item i ON ig.item_id = i.id_item
                            SET cantidad = " . $caracteristicasPersonaje['inventarioPersonaje']['arma']['flechas'] . "
                            WHERE pa.id_partida = " . $_SESSION['partida'] . " AND i.nombre = 'flecha';";
            $resultado = $bd->query($updateFlechas);
            if ($bd->errno) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update de las flechas";
            }
            $updateNunchakus = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = " . $personajeId . "
                            JOIN arquero a ON a.id_personaje = " . $personajeId . "
                            JOIN item_guardado ig ON a.id_personaje = ig.personaje_id
                            JOIN item i ON ig.item_id = i.id_item
                            SET desgaste = " . $caracteristicasPersonaje['inventarioPersonaje']['arma']['nunchakus'] . "
                            WHERE pa.id_partida = " . $_SESSION['partida'] . " AND i.nombre = 'nunchakus';";
            $resultado = $bd->query($updateNunchakus);
            if ($bd->errno) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update de los nunchakus";
            }
            //PREPARACIÓN DE CONSULTA
            $updatePociones = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = " . $personajeId . "
                            JOIN arquero a ON a.id_personaje = " . $personajeId . "
                            JOIN item_guardado ig ON a.id_personaje = ig.personaje_id
                            JOIN item i ON ig.item_id = i.id_item 
                            SET cantidad = ?
                            WHERE pa.id_partida = " . $_SESSION['partida'] . "
                            AND i.nombre = ?";
            $stmt = $bd->prepare($updatePociones);
            if (!$stmt) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en la preparación del update preparado";
            }
            // UPDATES CURACION
            $item_nombre = "curacionSimple";
            // El JSON me devuelve los mapas como strings
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje']['curacion']['pocion'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de curacion simple";
            }
            $item_nombre = "superCuracion";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje']['curacion']['superPocion'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de superCuracion";
            }
            $item_nombre = "curacionCompleta";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje']['curacion']['pocionMax'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de curacionMax";
            }

            //UPDATES ESTAMINA 
            $item_nombre = "restaurarEstamina";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje']['restaurarEstamina']['pocionEstamina'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de pocionEstamina";
            }
            $item_nombre = "restaurarMuchaEstamina";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje']['restaurarEstamina']['superPocionEstamina'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de SuperPocionEstamina";
            }
            $item_nombre = "restaurarTodaEstamina";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje']['restaurarEstamina']['pocionEstaminaMax'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de pocionEstaminaMax";
            }
            break;
        case 2:
            error_log("ENTRÓ EN EL CASE 2");
            $updateCaballero = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = " . $personajeId . "
                            JOIN caballero c ON c.id_personaje = " . $personajeId . "
                            SET fuerza = '" . $caracteristicasPersonaje['personaje']['fuerza'] . "',
                            armadura = '" . $caracteristicasPersonaje['personaje']['armadura'] . "',
                            vidaActual = '" . $caracteristicasPersonaje['personaje']['vidaActual'] . "',
                            vidaMaxima = '" . $caracteristicasPersonaje['personaje']['vidaMaxima'] . "',
                            estaminaActual = '" . $caracteristicasPersonaje['personaje']['estaminaActual'] . "',
                            estaminaMaxima = '" . $caracteristicasPersonaje['personaje']['estaminaMaxima'] . "',
                            nivel = '" . $caracteristicasPersonaje['personaje']['nivel'] . "',
                            puntosExperiencia = '" . $caracteristicasPersonaje['personaje']['puntosExperiencia'] . "',
                            envenenado = '" . $caracteristicasPersonaje['estadosPersonaje']['envenenado'] . "',
                            quemado = '" . $caracteristicasPersonaje['estadosPersonaje']['quemado'] . "',
                            heridoLeve = '" . $caracteristicasPersonaje['estadosPersonaje']['heridoLeve'] . "',
                            heridoGrave = '" . $caracteristicasPersonaje['estadosPersonaje']['heridoGrave'] . "',
                            confundido = '" . $caracteristicasPersonaje['estadosPersonaje']['confundido'] . "'
                            WHERE pa.id_partida = " . $_SESSION['partida'] . "";
            $resultado = $bd->query($updateCaballero);
            if ($bd->errno) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update del caballero";
            }
            // UPDATE espada
            $updateEspada = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = " . $personajeId . "
                            JOIN caballero c ON c.id_personaje = " . $personajeId . "
                            JOIN item_guardado ig ON c.id_personaje = ig.personaje_id
                            JOIN item i ON ig.item_id = i.id_item
                            SET desgaste = " . $caracteristicasPersonaje['inventarioPersonaje']['arma']['espada'] . "
                            WHERE pa.id_partida = " . $_SESSION['partida'] . " AND i.nombre = 'espada'";
            error_log($updateEspada);
            $resultado = $bd->query($updateEspada);
            if ($bd->errno) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update de la espada";
            }
            $updateMazo = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = " . $personajeId . "
                            JOIN caballero c ON c.id_personaje = " . $personajeId . "
                            JOIN item_guardado ig ON c.id_personaje = ig.personaje_id
                            JOIN item i ON ig.item_id = i.id_item
                            SET desgaste = " . $caracteristicasPersonaje['inventarioPersonaje']['arma']['mazo'] . "
                            WHERE pa.id_partida = " . $_SESSION['partida'] . " AND i.nombre = 'mazo';";
            $resultado = $bd->query($updateMazo);
            if ($bd->errno) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update del mazo";
            }
            // PREPARACIÓN DE CONSULTA
            $updatePociones = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = " . $personajeId . "
                            JOIN caballero c ON c.id_personaje = " . $personajeId . "
                            JOIN item_guardado ig ON c.id_personaje = ig.personaje_id
                            JOIN item i ON ig.item_id = i.id_item 
                            SET cantidad = ?
                            WHERE pa.id_partida = " . $_SESSION['partida'] . "
                            AND i.nombre = ?";
            $stmt = $bd->prepare($updatePociones);
            if (!$stmt) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en la preparación del update preparado";
            }
            // UPDATES CURACION
            $item_nombre = "curacionSimple";
            // El JSON me devuelve los mapas como strings
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje']['curacion']['pocion'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de curacion simple";
            }
            $item_nombre = "superCuracion";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje']['curacion']['superPocion'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de superCuracion";
            }
            $item_nombre = "curacionCompleta";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje']['curacion']['pocionMax'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de curacionMax";
            }

            //UPDATES ESTAMINA 
            $item_nombre = "restaurarEstamina";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje']['restaurarEstamina']['pocionEstamina'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de pocionEstamina";
            }
            $item_nombre = "restaurarMuchaEstamina";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje']['restaurarEstamina']['superPocionEstamina'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de SuperPocionEstamina";
            }
            $item_nombre = "restaurarTodaEstamina";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje']['restaurarEstamina']['pocionEstaminaMax'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de pocionEstaminaMax";
            }
            break;
        case 3:
            error_log("ENTRÓ EN EL CASE 3");
            $updateHechicero = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = " . $personajeId . "
                            JOIN hechicero h ON h.id_personaje = " . $personajeId . "
                            SET fuerza = '" . $caracteristicasPersonaje['personaje']['fuerza'] . "',
                            armadura = '" . $caracteristicasPersonaje['personaje']['armadura'] . "',
                            vidaActual = '" . $caracteristicasPersonaje['personaje']['vidaActual'] . "',
                            vidaMaxima = '" . $caracteristicasPersonaje['personaje']['vidaMaxima'] . "',
                            estaminaActual = '" . $caracteristicasPersonaje['personaje']['estaminaActual'] . "',
                            estaminaMaxima = '" . $caracteristicasPersonaje['personaje']['estaminaMaxima'] . "',
                            nivel = '" . $caracteristicasPersonaje['personaje']['nivel'] . "',
                            puntosExperiencia = '" . $caracteristicasPersonaje['personaje']['puntosExperiencia'] . "',
                            envenenado = '" . $caracteristicasPersonaje['estadosPersonaje']['envenenado'] . "',
                            quemado = '" . $caracteristicasPersonaje['estadosPersonaje']['quemado'] . "',
                            heridoLeve = '" . $caracteristicasPersonaje['estadosPersonaje']['heridoLeve'] . "',
                            heridoGrave = '" . $caracteristicasPersonaje['estadosPersonaje']['heridoGrave'] . "',
                            confundido = '" . $caracteristicasPersonaje['estadosPersonaje']['confundido'] . "',
                            fuego = '" . $caracteristicasPersonaje['auraPersonaje']['fuego'] . "',
                            inteligencia = '" . $caracteristicasPersonaje['personaje']['inteligencia'] . "',
                            veneno = '" . $caracteristicasPersonaje['auraPersonaje']['veneno'] . "',
                            enigmatico = '" . $caracteristicasPersonaje['auraPersonaje']['enigmatico'] . "',
                            pinchos = '" . $caracteristicasPersonaje['auraPersonaje']['pinchos'] . "',
                            sombra = '" . $caracteristicasPersonaje['auraPersonaje']['sombra'] . "'
                            WHERE pa.id_partida = " . $_SESSION['partida'] . "";
            error_log($updateHechicero);
            $resultado = $bd->query($updateHechicero);
            if ($bd->errno) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update del hechicero";
            }
            // UPDATE vara
            $updateVara = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = " . $personajeId . "
                            JOIN hechicero h ON h.id_personaje = " . $personajeId . "
                            JOIN item_guardado ig ON h.id_personaje = ig.personaje_id
                            JOIN item i ON ig.item_id = i.id_item
                            SET desgaste = " . $caracteristicasPersonaje['inventarioPersonaje']['arma']['vara'] . "
                            WHERE pa.id_partida = " . $_SESSION['partida'] . " AND i.nombre = 'vara';";
            $resultado = $bd->query($updateVara);
            if ($bd->errno) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update de la vara";
            }
            // PREPARACIÓN DE CONSULTA
            $updatePociones = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = " . $personajeId . "
                            JOIN hechicero h ON h.id_personaje = " . $personajeId . "
                            JOIN item_guardado ig ON h.id_personaje = ig.personaje_id
                            JOIN item i ON ig.item_id = i.id_item 
                            SET cantidad = ?
                            WHERE pa.id_partida = " . $_SESSION['partida'] . "
                            AND i.nombre = ?";
            $stmt = $bd->prepare($updatePociones);
            if (!$stmt) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en la preparación del update preparado";
            }
            // UPDATES CURACION
            $item_nombre = "curacionSimple";
            // El JSON me devuelve los mapas como strings
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje']['curacion']['pocion'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de curacion simple";
            }
            $item_nombre = "superCuracion";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje']['curacion']['superPocion'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de superCuracion";
            }
            $item_nombre = "curacionCompleta";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje']['curacion']['pocionMax'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de curacionMax";
            }

            //UPDATES ESTAMINA 
            $item_nombre = "restaurarEstamina";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje']['restaurarEstamina']['pocionEstamina'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de pocionEstamina";
            }
            $item_nombre = "restaurarMuchaEstamina";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje']['restaurarEstamina']['superPocionEstamina'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de SuperPocionEstamina";
            }
            $item_nombre = "restaurarTodaEstamina";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje']['restaurarEstamina']['pocionEstaminaMax'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de pocionEstaminaMax";
            }
            break;
        case 0:
            error_log("ENTRÓ EN EL CASE 0");
            $updateDruida = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = " . $personajeId . "
                            JOIN druida d ON d.id_personaje = " . $personajeId . "
                            SET fuerza = '" . $caracteristicasPersonaje['personaje']['fuerza'] . "',
                            armadura = '" . $caracteristicasPersonaje['personaje']['armadura'] . "',
                            vidaActual = '" . $caracteristicasPersonaje['personaje']['vidaActual'] . "',
                            vidaMaxima = '" . $caracteristicasPersonaje['personaje']['vidaMaxima'] . "',
                            estaminaActual = '" . $caracteristicasPersonaje['personaje']['estaminaActual'] . "',
                            estaminaMaxima = '" . $caracteristicasPersonaje['personaje']['estaminaMaxima'] . "',
                            nivel = '" . $caracteristicasPersonaje['personaje']['nivel'] . "',
                            puntosExperiencia = '" . $caracteristicasPersonaje['personaje']['puntosExperiencia'] . "',
                            envenenado = '" . $caracteristicasPersonaje['estadosPersonaje']['envenenado'] . "',
                            quemado = '" . $caracteristicasPersonaje['estadosPersonaje']['quemado'] . "',
                            heridoLeve = '" . $caracteristicasPersonaje['estadosPersonaje']['heridoLeve'] . "',
                            heridoGrave = '" . $caracteristicasPersonaje['estadosPersonaje']['heridoGrave'] . "',
                            confundido = '" . $caracteristicasPersonaje['estadosPersonaje']['confundido'] . "',
                            inteligencia = '" . $caracteristicasPersonaje['personaje']['inteligencia'] . "',
                            oso = '" . $caracteristicasPersonaje['transformacionesPersonaje']['oso'] . "',
                            serpiente = '" . $caracteristicasPersonaje['transformacionesPersonaje']['serpiente'] . "',
                            zorro = '" . $caracteristicasPersonaje['transformacionesPersonaje']['zorro'] . "',
                            aguila = '" . $caracteristicasPersonaje['transformacionesPersonaje']['águila'] . "'   
                            WHERE pa.id_partida = " . $_SESSION['partida'] . "";
            $resultado = $bd->query($updateDruida);
            if ($bd->errno) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update del druida";
            }
            // UPDATE vara
            $updateDaga = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = " . $personajeId . "
                            JOIN druida d ON d.id_personaje = " . $personajeId . "
                            JOIN item_guardado ig ON d.id_personaje = ig.personaje_id
                            JOIN item i ON ig.item_id = i.id_item
                            SET desgaste = " . $caracteristicasPersonaje['inventarioPersonaje']['arma']['daga'] . "
                            WHERE pa.id_partida = " . $_SESSION['partida'] . " AND i.nombre = 'daga';";
            $resultado = $bd->query($updateDaga);
            if ($bd->errno) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update de la daga";
            }
            // PREPARACIÓN DE CONSULTA
            $updatePociones = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = " . $personajeId . "
                            JOIN druida d ON d.id_personaje = " . $personajeId . "
                            JOIN item_guardado ig ON d.id_personaje = ig.personaje_id
                            JOIN item i ON ig.item_id = i.id_item 
                            SET cantidad = ?
                            WHERE pa.id_partida = " . $_SESSION['partida'] . "
                            AND i.nombre = ?";
            $stmt = $bd->prepare($updatePociones);
            if (!$stmt) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en la preparación del update preparado";
            }
            // UPDATES CURACION
            $item_nombre = "curacionSimple";
            // El JSON me devuelve los mapas como strings
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje']['curacion']['pocion'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de curacion simple";
            }
            $item_nombre = "superCuracion";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje']['curacion']['superPocion'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de superCuracion";
            }
            $item_nombre = "curacionCompleta";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje']['curacion']['pocionMax'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de curacionMax";
            }

            //UPDATES ESTAMINA 
            $item_nombre = "restaurarEstamina";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje']['restaurarEstamina']['pocionEstamina'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de pocionEstamina";
            }
            $item_nombre = "restaurarMuchaEstamina";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje']['restaurarEstamina']['superPocionEstamina'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de SuperPocionEstamina";
            }
            $item_nombre = "restaurarTodaEstamina";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje']['restaurarEstamina']['pocionEstaminaMax'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de pocionEstaminaMax";
            }

            break;
        default:
            echo 'No se ha creado bien el personaje 1, entró en el default del switch';
            break;
    }

    if ($errorUpdateConsulta == true) {
        echo "Error en el update: " . $bd->error;
        echo "<br>";
        echo $descripcionError;
        $bd->rollback();
    } else {
        $bd->commit();
        echo json_encode(['success' => true]);
        // header("location: combate.php");
    }



    // NO SE QUE HACE LA SIGUIENTE LINEA ES PARA NO TENER PROBLEMAS DE CACHÉ
    $cssVersion = @filemtime(__DIR__ . "/estilos/estilos.css") ?: time();
}
