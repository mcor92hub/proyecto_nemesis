<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    require_once "bd.php";
    $bd->autocommit(false);
    $json = file_get_contents('php://input');
    // TRABAJO PARA MÁXIMO
    error_log("JSON recibido: " . $json);
    $caracteristicasPersonaje1 = json_decode($json, true);

    $errorUpdateConsulta = false;
    $descripcionError = "";
    $consultaTurno = "SELECT * FROM partida WHERE id_partida = " . $_SESSION['partida'] . "";
    $resultadoTurno = $bd->query($consultaTurno);
    if ($bd->errno) {
        $errorUpdateConsulta = true;
        $descripcionError = "error en el select de la partida";
    }
    $lista = [];
    while ($fila = $resultadoTurno->fetch_assoc()) {
        if ($fila['turno'] == 1) {
            $updatePartida = "UPDATE partida SET turno = 2 WHERE id_partida = " . $_SESSION['partida'] . "";
            $bd->query($updatePartida);
        } else {
            $updatePartida = "UPDATE partida SET turno = 1 WHERE id_partida = " . $_SESSION['partida'] . "";
            $bd->query($updatePartida);
        }
        array_push($lista, $fila['personaje1_id']);
        array_push($lista, $fila['personaje2_id']);
    }

    //AQUÍ HAY QUE CAMBIAR LOS SELECTS POR UPDATES
    switch (intval($lista[0]) % 4) {
        case 1:
            $updateArquero = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = pa.personaje1_id
                            JOIN arquero a ON a.id_personaje = pa.personaje1_id
                            SET turno = 2,
                            fuerza = '" . $caracteristicasPersonaje1['personaje1']['fuerza'] . "',
                            armadura = '" . $caracteristicasPersonaje1['personaje1']['armadura'] . "',
                            vidaActual = '" . $caracteristicasPersonaje1['personaje1']['vidaActual'] . "',
                            vidaMaxima = '" . $caracteristicasPersonaje1['personaje1']['vidaMaxima'] . "',
                            estaminaActual = '" . $caracteristicasPersonaje1['personaje1']['estaminaActual'] . "',
                            estaminaMaxima = '" . $caracteristicasPersonaje1['personaje1']['estaminaMaxima'] . "',
                            nivel = '" . $caracteristicasPersonaje1['personaje1']['nivel'] . "',
                            puntosExperiencia = '" . $caracteristicasPersonaje1['personaje1']['puntosExperiencia'] . "',
                            punteria = '" . $caracteristicasPersonaje1['personaje1']['punteria'] . "',
                            envenenado = '" . $caracteristicasPersonaje1['estadosPersonaje1']['envenenado'] . "',
                            quemado = '" . $caracteristicasPersonaje1['estadosPersonaje1']['quemado'] . "',
                            heridoLeve = '" . $caracteristicasPersonaje1['estadosPersonaje1']['heridoLeve'] . "',
                            heridoGrave = '" . $caracteristicasPersonaje1['estadosPersonaje1']['heridoGrave'] . "',
                            confundido = '" . $caracteristicasPersonaje1['estadosPersonaje1']['confundido'] . "'
                            WHERE pa.id_partida = " . $_SESSION['partida'] . "";
            $resultado = $bd->query($updateArquero);
            if ($bd->errno) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update del arquero";
            }
            $updateArco = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = pa.personaje1_id
                            JOIN arquero a ON a.id_personaje = pa.personaje1_id
                            JOIN item_guardado ig ON a.id_personaje = ig.personaje_id
                            JOIN item i ON ig.item_id = i.id_item
                            SET desgaste = " . $caracteristicasPersonaje1['inventarioPersonaje1']['arma']['arco'] . "
                            WHERE pa.id_partida = " . $_SESSION['partida'] . " AND i.nombre = 'arco';";
            $resultado = $bd->query($updateArco);
            if ($bd->errno) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update del arco";
            }
            $updateFlechas = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = pa.personaje1_id
                            JOIN arquero a ON a.id_personaje = pa.personaje1_id
                            JOIN item_guardado ig ON a.id_personaje = ig.personaje_id
                            JOIN item i ON ig.item_id = i.id_item
                            SET cantidad = " . $caracteristicasPersonaje1['inventarioPersonaje1']['arma']['flechas'] . "
                            WHERE pa.id_partida = " . $_SESSION['partida'] . " AND i.nombre = 'flecha';";
            $resultado = $bd->query($updateFlechas);
            if ($bd->errno) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update de las flechas";
            }
            $updateNunchakus = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = pa.personaje1_id
                            JOIN arquero a ON a.id_personaje = pa.personaje1_id
                            JOIN item_guardado ig ON a.id_personaje = ig.personaje_id
                            JOIN item i ON ig.item_id = i.id_item
                            SET desgaste = " . $caracteristicasPersonaje1['inventarioPersonaje1']['arma']['nunchakus'] . "
                            WHERE pa.id_partida = " . $_SESSION['partida'] . " AND i.nombre = 'nunchakus';";
            $resultado = $bd->query($updateNunchakus);
            if ($bd->errno) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update de los nunchakus";
            }
            //PREPARACIÓN DE CONSULTA
            $updatePociones = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = pa.personaje1_id
                            JOIN arquero a ON a.id_personaje = pa.personaje1_id
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
            $cantidad = (int) $caracteristicasPersonaje1['inventarioPersonaje1']['curacion']['pocion'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de curacion simple";
            }
            $item_nombre = "superCuracion";
            $cantidad = (int) $caracteristicasPersonaje1['inventarioPersonaje1']['curacion']['superPocion'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de superCuracion";
            }
            $item_nombre = "curacionCompleta";
            $cantidad = (int) $caracteristicasPersonaje1['inventarioPersonaje1']['curacion']['pocionMax'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de curacionMax";
            }

            //UPDATES ESTAMINA 
            $item_nombre = "restaurarEstamina";
            $cantidad = (int) $caracteristicasPersonaje1['inventarioPersonaje1']['restaurarEstamina']['pocionEstamina'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de pocionEstamina";
            }
            $item_nombre = "restaurarMuchaEstamina";
            $cantidad = (int) $caracteristicasPersonaje1['inventarioPersonaje1']['restaurarEstamina']['superPocionEstamina'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de SuperPocionEstamina";
            }
            $item_nombre = "restaurarTodaEstamina";
            $cantidad = (int) $caracteristicasPersonaje1['inventarioPersonaje1']['restaurarEstamina']['pocionEstaminaMax'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de pocionEstaminaMax";
            }
            break;
        case 2:
            $updateCaballero = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = pa.personaje1_id
                            JOIN caballero c ON c.id_personaje = pa.personaje1_id
                            SET turno = 2,
                            fuerza = '" . $caracteristicasPersonaje1['personaje1']['fuerza'] . "',
                            armadura = '" . $caracteristicasPersonaje1['personaje1']['armadura'] . "',
                            vidaActual = '" . $caracteristicasPersonaje1['personaje1']['vidaActual'] . "',
                            vidaMaxima = '" . $caracteristicasPersonaje1['personaje1']['vidaMaxima'] . "',
                            estaminaActual = '" . $caracteristicasPersonaje1['personaje1']['estaminaActual'] . "',
                            estaminaMaxima = '" . $caracteristicasPersonaje1['personaje1']['estaminaMaxima'] . "',
                            nivel = '" . $caracteristicasPersonaje1['personaje1']['nivel'] . "',
                            puntosExperiencia = '" . $caracteristicasPersonaje1['personaje1']['puntosExperiencia'] . "',
                            inteligencia = '" . $caracteristicasPersonaje1['personaje1']['inteligencia'] . "',
                            envenenado = '" . $caracteristicasPersonaje1['estadosPersonaje1']['envenenado'] . "',
                            quemado = '" . $caracteristicasPersonaje1['estadosPersonaje1']['quemado'] . "',
                            heridoLeve = '" . $caracteristicasPersonaje1['estadosPersonaje1']['heridoLeve'] . "',
                            heridoGrave = '" . $caracteristicasPersonaje1['estadosPersonaje1']['heridoGrave'] . "',
                            confundido = '" . $caracteristicasPersonaje1['estadosPersonaje1']['confundido'] . "'
                            WHERE pa.id_partida = " . $_SESSION['partida'] . "";
            $resultado = $bd->query($updateCaballero);
            if ($bd->errno) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update del arquero";
            }
            // UPDATE espada
            $updateEspada = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = pa.personaje1_id
                            JOIN caballero c ON c.id_personaje = pa.personaje1_id
                            JOIN item_guardado ig ON c.id_personaje = ig.personaje_id
                            JOIN item i ON ig.item_id = i.id_item
                            SET desgaste = " . $caracteristicasPersonaje1['inventarioPersonaje1']['arma']['espada'] . "
                            WHERE pa.id_partida = " . $_SESSION['partida'] . " AND i.nombre = 'espada';";
            $resultado = $bd->query($updateEspada);
            if ($bd->errno) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update de la espada";
            }
            $updateMazo = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = pa.personaje1_id
                            JOIN caballero c ON c.id_personaje = pa.personaje1_id
                            JOIN item_guardado ig ON c.id_personaje = ig.personaje_id
                            JOIN item i ON ig.item_id = i.id_item
                            SET desgaste = " . $caracteristicasPersonaje1['inventarioPersonaje1']['arma']['mazo'] . "
                            WHERE pa.id_partida = " . $_SESSION['partida'] . " AND i.nombre = 'mazo';";
            $resultado = $bd->query($updateMazo);
            if ($bd->errno) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update del mazo";
            }
            // PREPARACIÓN DE CONSULTA
            $updatePociones = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = pa.personaje1_id
                            JOIN caballero c ON c.id_personaje = pa.personaje1_id
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
            $cantidad = (int) $caracteristicasPersonaje1['inventarioPersonaje1']['curacion']['pocion'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de curacion simple";
            }
            $item_nombre = "superCuracion";
            $cantidad = (int) $caracteristicasPersonaje1['inventarioPersonaje1']['curacion']['superPocion'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de superCuracion";
            }
            $item_nombre = "curacionCompleta";
            $cantidad = (int) $caracteristicasPersonaje1['inventarioPersonaje1']['curacion']['pocionMax'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de curacionMax";
            }

            //UPDATES ESTAMINA 
            $item_nombre = "restaurarEstamina";
            $cantidad = (int) $caracteristicasPersonaje1['inventarioPersonaje1']['restaurarEstamina']['pocionEstamina'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de pocionEstamina";
            }
            $item_nombre = "restaurarMuchaEstamina";
            $cantidad = (int) $caracteristicasPersonaje1['inventarioPersonaje1']['restaurarEstamina']['superPocionEstamina'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de SuperPocionEstamina";
            }
            $item_nombre = "restaurarTodaEstamina";
            $cantidad = (int) $caracteristicasPersonaje1['inventarioPersonaje1']['restaurarEstamina']['pocionEstaminaMax'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de pocionEstaminaMax";
            }
            break;
        case 3:
            $updateHechicero = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = pa.personaje1_id
                            JOIN hechicero h ON h.id_personaje = pa.personaje1_id
                            SET turno = 2,
                            fuerza = '" . $caracteristicasPersonaje1['personaje1']['fuerza'] . "',
                            armadura = '" . $caracteristicasPersonaje1['personaje1']['armadura'] . "',
                            vidaActual = '" . $caracteristicasPersonaje1['personaje1']['vidaActual'] . "',
                            vidaMaxima = '" . $caracteristicasPersonaje1['personaje1']['vidaMaxima'] . "',
                            estaminaActual = '" . $caracteristicasPersonaje1['personaje1']['estaminaActual'] . "',
                            estaminaMaxima = '" . $caracteristicasPersonaje1['personaje1']['estaminaMaxima'] . "',
                            nivel = '" . $caracteristicasPersonaje1['personaje1']['nivel'] . "',
                            puntosExperiencia = '" . $caracteristicasPersonaje1['personaje1']['puntosExperiencia'] . "',
                            envenenado = '" . $caracteristicasPersonaje1['estadosPersonaje1']['envenenado'] . "',
                            quemado = '" . $caracteristicasPersonaje1['estadosPersonaje1']['quemado'] . "',
                            heridoLeve = '" . $caracteristicasPersonaje1['estadosPersonaje1']['heridoLeve'] . "',
                            heridoGrave = '" . $caracteristicasPersonaje1['estadosPersonaje1']['heridoGrave'] . "',
                            confundido = '" . $caracteristicasPersonaje1['estadosPersonaje1']['confundido'] . "',
                            fuego = '" . $caracteristicasPersonaje1['auraPersonaje1']['fuego'] . "',
                            veneno = '" . $caracteristicasPersonaje1['auraPersonaje1']['veneno'] . "',
                            enigmatico = '" . $caracteristicasPersonaje1['auraPersonaje1']['enigmatico'] . "',
                            pinchos = '" . $caracteristicasPersonaje1['auraPersonaje1']['pinchos'] . "',
                            sombra = '" . $caracteristicasPersonaje1['auraPersonaje1']['sombra'] . "'
                            WHERE pa.id_partida = " . $_SESSION['partida'] . "";
            $resultado = $bd->query($updateHechicero);
            if ($bd->errno) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update del arquero";
            }
            // UPDATE vara
            $updateVara = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = pa.personaje1_id
                            JOIN hechicero h ON h.id_personaje = pa.personaje1_id
                            JOIN item_guardado ig ON h.id_personaje = ig.personaje_id
                            JOIN item i ON ig.item_id = i.id_item
                            SET desgaste = " . $caracteristicasPersonaje1['inventarioPersonaje1']['arma']['vara'] . "
                            WHERE pa.id_partida = " . $_SESSION['partida'] . " AND i.nombre = 'vara';";
            $resultado = $bd->query($updateVara);
            if ($bd->errno) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update de la vara";
            }
            // PREPARACIÓN DE CONSULTA
            $updatePociones = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = pa.personaje1_id
                            JOIN hechicero h ON h.id_personaje = pa.personaje1_id
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
            $cantidad = (int) $caracteristicasPersonaje1['inventarioPersonaje1']['curacion']['pocion'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de curacion simple";
            }
            $item_nombre = "superCuracion";
            $cantidad = (int) $caracteristicasPersonaje1['inventarioPersonaje1']['curacion']['superPocion'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de superCuracion";
            }
            $item_nombre = "curacionCompleta";
            $cantidad = (int) $caracteristicasPersonaje1['inventarioPersonaje1']['curacion']['pocionMax'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de curacionMax";
            }

            //UPDATES ESTAMINA 
            $item_nombre = "restaurarEstamina";
            $cantidad = (int) $caracteristicasPersonaje1['inventarioPersonaje1']['restaurarEstamina']['pocionEstamina'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de pocionEstamina";
            }
            $item_nombre = "restaurarMuchaEstamina";
            $cantidad = (int) $caracteristicasPersonaje1['inventarioPersonaje1']['restaurarEstamina']['superPocionEstamina'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de SuperPocionEstamina";
            }
            $item_nombre = "restaurarTodaEstamina";
            $cantidad = (int) $caracteristicasPersonaje1['inventarioPersonaje1']['restaurarEstamina']['pocionEstaminaMax'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de pocionEstaminaMax";
            }

            break;
        case 0:
            $updateDruida= "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = pa.personaje1_id
                            JOIN druida d ON d.id_personaje = pa.personaje1_id
                            SET turno = 2,
                            fuerza = '" . $caracteristicasPersonaje1['personaje1']['fuerza'] . "',
                            armadura = '" . $caracteristicasPersonaje1['personaje1']['armadura'] . "',
                            vidaActual = '" . $caracteristicasPersonaje1['personaje1']['vidaActual'] . "',
                            vidaMaxima = '" . $caracteristicasPersonaje1['personaje1']['vidaMaxima'] . "',
                            estaminaActual = '" . $caracteristicasPersonaje1['personaje1']['estaminaActual'] . "',
                            estaminaMaxima = '" . $caracteristicasPersonaje1['personaje1']['estaminaMaxima'] . "',
                            nivel = '" . $caracteristicasPersonaje1['personaje1']['nivel'] . "',
                            puntosExperiencia = '" . $caracteristicasPersonaje1['personaje1']['puntosExperiencia'] . "',
                            envenenado = '" . $caracteristicasPersonaje1['estadosPersonaje1']['envenenado'] . "',
                            quemado = '" . $caracteristicasPersonaje1['estadosPersonaje1']['quemado'] . "',
                            heridoLeve = '" . $caracteristicasPersonaje1['estadosPersonaje1']['heridoLeve'] . "',
                            heridoGrave = '" . $caracteristicasPersonaje1['estadosPersonaje1']['heridoGrave'] . "',
                            confundido = '" . $caracteristicasPersonaje1['estadosPersonaje1']['confundido'] . "',
                            oso = '" . $caracteristicasPersonaje1['transformacionesPersonaje1']['oso'] . "',
                            serpiente = '" . $caracteristicasPersonaje1['transformacionesPersonaje1']['serpiente'] . "',
                            zorro = '" . $caracteristicasPersonaje1['transformacionesPersonaje1']['zorro'] . "',
                            aguila = '" . $caracteristicasPersonaje1['transformacionesPersonaje1']['aguila'] . "'   
                            WHERE pa.id_partida = " . $_SESSION['partida'] . "";

            // UPDATE vara
            $updateDaga = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = pa.personaje1_id
                            JOIN druida d ON d.id_personaje = pa.personaje1_id
                            JOIN item_guardado ig ON d.id_personaje = ig.personaje_id
                            JOIN item i ON ig.item_id = i.id_item
                            SET desgaste = " . $caracteristicasPersonaje1['inventarioPersonaje1']['arma']['daga'] . "
                            WHERE pa.id_partida = " . $_SESSION['partida'] . " AND i.nombre = 'daga';";
            $resultado = $bd->query($updateDaga);
            if ($bd->errno) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update de la daga";
            }
            // PREPARACIÓN DE CONSULTA
            $updatePociones = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = pa.personaje1_id
                            JOIN druida d ON d.id_personaje = pa.personaje1_id
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
            $cantidad = (int) $caracteristicasPersonaje1['inventarioPersonaje1']['curacion']['pocion'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de curacion simple";
            }
            $item_nombre = "superCuracion";
            $cantidad = (int) $caracteristicasPersonaje1['inventarioPersonaje1']['curacion']['superPocion'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de superCuracion";
            }
            $item_nombre = "curacionCompleta";
            $cantidad = (int) $caracteristicasPersonaje1['inventarioPersonaje1']['curacion']['pocionMax'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de curacionMax";
            }

            //UPDATES ESTAMINA 
            $item_nombre = "restaurarEstamina";
            $cantidad = (int) $caracteristicasPersonaje1['inventarioPersonaje1']['restaurarEstamina']['pocionEstamina'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de pocionEstamina";
            }
            $item_nombre = "restaurarMuchaEstamina";
            $cantidad = (int) $caracteristicasPersonaje1['inventarioPersonaje1']['restaurarEstamina']['superPocionEstamina'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de SuperPocionEstamina";
            }
            $item_nombre = "restaurarTodaEstamina";
            $cantidad = (int) $caracteristicasPersonaje1['inventarioPersonaje1']['restaurarEstamina']['pocionEstaminaMax'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de pocionEstaminaMax";
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
    // switch (intval($lista[1])%4) {
    //     case 1:
    //         $updateArquero = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = pa.personaje2_id
    //                         JOIN arquero a ON a.id_personaje = pa.personaje2_id
    //                         SET turno = 2,
    //                         fuerza = '" . $caracteristicasPersonaje2['personaje1']['fuerza'] . "',
    //                         armadura = '" . $caracteristicasPersonaje1['personaje1']['armadura'] . "',
    //                         vidaActual = '" . $caracteristicasPersonaje2['personaje1']['vidaActual'] . "',
    //                         vidaMaxima = '" . $caracteristicasPersonaje2['personaje1']['vidaMaxima'] . "',
    //                         estaminaActual = '" . $caracteristicasPersonaje2['personaje1']['estaminaActual'] . "',
    //                         estaminaMaxima = '" . $caracteristicasPersonaje2['personaje1']['estaminaMaxima'] . "',
    //                         nivel = '" . $caracteristicasPersonaje2['personaje1']['nivel'] . "',
    //                         puntosExperiencia = '" . $caracteristicasPersonaje2['personaje1']['puntosExperiencia'] . "',
    //                         punteria = '" . $caracteristicasPersonaje2['personaje1']['punteria'] . "',
    //                         envenenado = '" . $caracteristicasPersonaje2['estadosPersonaje1']['envenenado'] . "',
    //                         quemado = '" . $caracteristicasPersonaje2['estadosPersonaje1']['quemado'] . "',
    //                         heridoLeve = '" . $caracteristicasPersonaje2['estadosPersonaje1']['heridoLeve'] . "',
    //                         heridoGrave = '" . $caracteristicasPersonaje2['estadosPersonaje1']['heridoGrave'] . "',
    //                         confundido = '" . $caracteristicasPersonaje2['estadosPersonaje1']['confundido'] . "'
    //                         WHERE pa.id_partida = " . $_SESSION['partida'] . "";
    //         $resultado = $bd->query($updateArquero);
    //         if ($bd->errno) {
    //             $errorUpdateConsulta = true;
    //             $descripcionError = "error en el update del arquero";
    //         }
    //         $updateArco = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = pa.personaje2_id
    //                         JOIN arquero a ON a.id_personaje = pa.personaje2_id
    //                         JOIN item_guardado ig ON a.id_personaje = ig.personaje_id
    //                         JOIN item i ON ig.item_id = i.id_item
    //                         SET desgaste = " . $caracteristicasPersonaje2['inventarioPersonaje2']['arma']['arco'] . "
    //                         WHERE pa.id_partida = " . $_SESSION['partida'] . " AND i.nombre = 'arco';";
    //         $resultado = $bd->query($updateArco);
    //         if ($bd->errno) {
    //             $errorUpdateConsulta = true;
    //             $descripcionError = "error en el update del arco";
    //         }
    //         $updateFlechas = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = pa.personaje2_id
    //                         JOIN arquero a ON a.id_personaje = pa.personaje2_id
    //                         JOIN item_guardado ig ON a.id_personaje = ig.personaje_id
    //                         JOIN item i ON ig.item_id = i.id_item
    //                         SET cantidad = " . $caracteristicasPersonaje2['inventarioPersonaje2']['arma']['flechas'] . "
    //                         WHERE pa.id_partida = " . $_SESSION['partida'] . " AND i.nombre = 'flecha';";
    //         $resultado = $bd->query($updateFlechas);
    //         if ($bd->errno) {
    //             $errorUpdateConsulta = true;
    //             $descripcionError = "error en el update de las flechas";
    //         }
    //         $updateNunchakus = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = pa.personaje2_id
    //                         JOIN arquero a ON a.id_personaje = pa.personaje2_id
    //                         JOIN item_guardado ig ON a.id_personaje = ig.personaje_id
    //                         JOIN item i ON ig.item_id = i.id_item
    //                         SET desgaste = " . $caracteristicasPersonaje2['inventarioPersonaje2']['arma']['nunchakus'] . "
    //                         WHERE pa.id_partida = " . $_SESSION['partida'] . " AND i.nombre = 'nunchakus';";
    //         $resultado = $bd->query($updateNunchakus);
    //         if ($bd->errno) {
    //             $errorUpdateConsulta = true;
    //             $descripcionError = "error en el update de los nunchakus";
    //         }
    //         //PREPARACIÓN DE CONSULTA
    //         $updatePociones = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = pa.personaje2_id
    //                         JOIN arquero a ON a.id_personaje = pa.personaje2_id
    //                         JOIN item_guardado ig ON a.id_personaje = ig.personaje_id
    //                         JOIN item i ON ig.item_id = i.id_item 
    //                         SET cantidad = ?
    //                         WHERE pa.id_partida = " . $_SESSION['partida'] . "
    //                         AND i.nombre = ?";
    //         $stmt = $bd->prepare($updatePociones);
    //         if (!$stmt) {
    //             $errorUpdateConsulta = true;
    //             $descripcionError = "error en la preparación del update preparado";
    //         }
    //         // UPDATES CURACION
    //         $item_nombre = "curacionSimple";
    //         // El JSON me devuelve los mapas como strings
    //         $cantidad = (int) $caracteristicasPersonaje2['inventarioPersonaje2']['curacion']['pocion'];
    //         $stmt->bind_param("is", $cantidad, $item_nombre);
    //         //esta linea ejecuta el update preparado y si esta mal salta error
    //         if (!$stmt->execute()) {
    //             $errorUpdateConsulta = true;
    //             $descripcionError = "error en el update preparado de curacion simple";
    //         }
    //         $item_nombre = "superCuracion";
    //         $cantidad = (int) $caracteristicasPersonaje2['inventarioPersonaje2']['curacion']['superPocion'];
    //         $stmt->bind_param("is", $cantidad, $item_nombre);
    //         //esta linea ejecuta el update preparado y si esta mal salta error
    //         if (!$stmt->execute()) {
    //             $errorUpdateConsulta = true;
    //             $descripcionError = "error en el update preparado de superCuracion";
    //         }
    //         $item_nombre = "curacionCompleta";
    //         $cantidad = (int) $caracteristicasPersonaje2['inventarioPersonaje2']['curacion']['pocionMax'];
    //         $stmt->bind_param("is", $cantidad, $item_nombre);
    //         //esta linea ejecuta el update preparado y si esta mal salta error
    //         if (!$stmt->execute()) {
    //             $errorUpdateConsulta = true;
    //             $descripcionError = "error en el update preparado de curacionMax";
    //         }

    //         //UPDATES ESTAMINA 
    //         $item_nombre = "restaurarEstamina";
    //         $cantidad = (int) $caracteristicasPersonaje2['inventarioPersonaje2']['restaurarEstamina']['pocionEstamina'];
    //         $stmt->bind_param("is", $cantidad, $item_nombre);
    //         //esta linea ejecuta el update preparado y si esta mal salta error
    //         if (!$stmt->execute()) {
    //             $errorUpdateConsulta = true;
    //             $descripcionError = "error en el update preparado de pocionEstamina";
    //         }
    //         $item_nombre = "restaurarMuchaEstamina";
    //         $cantidad = (int) $caracteristicasPersonaje2['inventarioPersonaje2']['restaurarEstamina']['superPocionEstamina'];
    //         $stmt->bind_param("is", $cantidad, $item_nombre);
    //         //esta linea ejecuta el update preparado y si esta mal salta error
    //         if (!$stmt->execute()) {
    //             $errorUpdateConsulta = true;
    //             $descripcionError = "error en el update preparado de SuperPocionEstamina";
    //         }
    //         $item_nombre = "restaurarTodaEstamina";
    //         $cantidad = (int) $caracteristicasPersonaje2['inventarioPersonaje2']['restaurarEstamina']['pocionEstaminaMax'];
    //         $stmt->bind_param("is", $cantidad, $item_nombre);
    //         //esta linea ejecuta el update preparado y si esta mal salta error
    //         if (!$stmt->execute()) {
    //             $errorUpdateConsulta = true;
    //             $descripcionError = "error en el update preparado de pocionEstaminaMax";
    //         }
    //         break;
    //     case 2:
    //         # code...
    //         break;
    //     case 3:
    //         # code...
    //         break;
    //     case 0:
    //         # code...
    //         break;
    //     default:
    //         echo 'No se ha creado bien el personaje 2, entró en el default del switch';
    //         break;
    // }
    if ($errorUpdateConsulta == true) {
        echo "Error en el update: " . $bd->error;
        echo "<br>";
        echo $descripcionError;
        $bd->rollback();
    } else {
        $bd->commit();
        header("location: combate.php");
    }



    // NO SE QUE HACE LA SIGUIENTE LINEA ES PARA NO TENER PROBLEMAS DE CACHÉ
    $cssVersion = @filemtime(__DIR__ . "/estilos/estilos.css") ?: time();
} else {
    echo "no entro";
}
