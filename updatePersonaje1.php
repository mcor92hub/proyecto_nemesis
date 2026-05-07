<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    require_once "bd.php";
    $bd->autocommit(false);
    $json = file_get_contents('php://input');
    // TRABAJO PARA MÁXIMO
    error_log("JSON recibido: " . $json);
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
                            SET fuerza = '" . $caracteristicasPersonaje['personaje1']['fuerza'] . "',
                            armadura = '" . $caracteristicasPersonaje['personaje1']['armadura'] . "',
                            vidaActual = '" . $caracteristicasPersonaje['personaje1']['vidaActual'] . "',
                            vidaMaxima = '" . $caracteristicasPersonaje['personaje1']['vidaMaxima'] . "',
                            estaminaActual = '" . $caracteristicasPersonaje['personaje1']['estaminaActual'] . "',
                            estaminaMaxima = '" . $caracteristicasPersonaje['personaje1']['estaminaMaxima'] . "',
                            nivel = '" . $caracteristicasPersonaje['personaje1']['nivel'] . "',
                            puntosExperiencia = '" . $caracteristicasPersonaje['personaje1']['puntosExperiencia'] . "',
                            punteria = '" . $caracteristicasPersonaje['personaje1']['punteria'] . "',
                            envenenado = '" . $caracteristicasPersonaje['estadosPersonaje1']['envenenado'] . "',
                            quemado = '" . $caracteristicasPersonaje['estadosPersonaje1']['quemado'] . "',
                            heridoLeve = '" . $caracteristicasPersonaje['estadosPersonaje1']['heridoLeve'] . "',
                            heridoGrave = '" . $caracteristicasPersonaje['estadosPersonaje1']['heridoGrave'] . "',
                            confundido = '" . $caracteristicasPersonaje['estadosPersonaje1']['confundido'] . "'
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
                            SET desgaste = " . $caracteristicasPersonaje['inventarioPersonaje1']['arma']['arco'] . "
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
                            SET cantidad = " . $caracteristicasPersonaje['inventarioPersonaje1']['arma']['flechas'] . "
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
                            SET desgaste = " . $caracteristicasPersonaje['inventarioPersonaje1']['arma']['nunchakus'] . "
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
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje1']['curacion']['pocion'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de curacion simple";
            }
            $item_nombre = "superCuracion";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje1']['curacion']['superPocion'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de superCuracion";
            }
            $item_nombre = "curacionCompleta";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje1']['curacion']['pocionMax'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de curacionMax";
            }

            //UPDATES ESTAMINA 
            $item_nombre = "restaurarEstamina";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje1']['restaurarEstamina']['pocionEstamina'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de pocionEstamina";
            }
            $item_nombre = "restaurarMuchaEstamina";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje1']['restaurarEstamina']['superPocionEstamina'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de SuperPocionEstamina";
            }
            $item_nombre = "restaurarTodaEstamina";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje1']['restaurarEstamina']['pocionEstaminaMax'];
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
                            SET fuerza = '" . $caracteristicasPersonaje['personaje1']['fuerza'] . "',
                            armadura = '" . $caracteristicasPersonaje['personaje1']['armadura'] . "',
                            vidaActual = '" . $caracteristicasPersonaje['personaje1']['vidaActual'] . "',
                            vidaMaxima = '" . $caracteristicasPersonaje['personaje1']['vidaMaxima'] . "',
                            estaminaActual = '" . $caracteristicasPersonaje['personaje1']['estaminaActual'] . "',
                            estaminaMaxima = '" . $caracteristicasPersonaje['personaje1']['estaminaMaxima'] . "',
                            nivel = '" . $caracteristicasPersonaje['personaje1']['nivel'] . "',
                            puntosExperiencia = '" . $caracteristicasPersonaje['personaje1']['puntosExperiencia'] . "',
                            inteligencia = '" . $caracteristicasPersonaje['personaje1']['inteligencia'] . "',
                            envenenado = '" . $caracteristicasPersonaje['estadosPersonaje1']['envenenado'] . "',
                            quemado = '" . $caracteristicasPersonaje['estadosPersonaje1']['quemado'] . "',
                            heridoLeve = '" . $caracteristicasPersonaje['estadosPersonaje1']['heridoLeve'] . "',
                            heridoGrave = '" . $caracteristicasPersonaje['estadosPersonaje1']['heridoGrave'] . "',
                            confundido = '" . $caracteristicasPersonaje['estadosPersonaje1']['confundido'] . "'
                            WHERE pa.id_partida = " . $_SESSION['partida'] . "";
            $resultado = $bd->query($updateCaballero);
            if ($bd->errno) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update del caballero";
            }
            // UPDATE espada
            $updateEspada = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = pa.personaje1_id
                            JOIN caballero c ON c.id_personaje = pa.personaje1_id
                            JOIN item_guardado ig ON c.id_personaje = ig.personaje_id
                            JOIN item i ON ig.item_id = i.id_item
                            SET desgaste = " . $caracteristicasPersonaje['inventarioPersonaje1']['arma']['espada'] . "
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
                            SET desgaste = " . $caracteristicasPersonaje['inventarioPersonaje1']['arma']['mazo'] . "
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
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje1']['curacion']['pocion'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de curacion simple";
            }
            $item_nombre = "superCuracion";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje1']['curacion']['superPocion'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de superCuracion";
            }
            $item_nombre = "curacionCompleta";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje1']['curacion']['pocionMax'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de curacionMax";
            }

            //UPDATES ESTAMINA 
            $item_nombre = "restaurarEstamina";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje1']['restaurarEstamina']['pocionEstamina'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de pocionEstamina";
            }
            $item_nombre = "restaurarMuchaEstamina";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje1']['restaurarEstamina']['superPocionEstamina'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de SuperPocionEstamina";
            }
            $item_nombre = "restaurarTodaEstamina";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje1']['restaurarEstamina']['pocionEstaminaMax'];
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
                            SET fuerza = '" . $caracteristicasPersonaje['personaje1']['fuerza'] . "',
                            armadura = '" . $caracteristicasPersonaje['personaje1']['armadura'] . "',
                            vidaActual = '" . $caracteristicasPersonaje['personaje1']['vidaActual'] . "',
                            vidaMaxima = '" . $caracteristicasPersonaje['personaje1']['vidaMaxima'] . "',
                            estaminaActual = '" . $caracteristicasPersonaje['personaje1']['estaminaActual'] . "',
                            estaminaMaxima = '" . $caracteristicasPersonaje['personaje1']['estaminaMaxima'] . "',
                            nivel = '" . $caracteristicasPersonaje['personaje1']['nivel'] . "',
                            puntosExperiencia = '" . $caracteristicasPersonaje['personaje1']['puntosExperiencia'] . "',
                            envenenado = '" . $caracteristicasPersonaje['estadosPersonaje1']['envenenado'] . "',
                            quemado = '" . $caracteristicasPersonaje['estadosPersonaje1']['quemado'] . "',
                            heridoLeve = '" . $caracteristicasPersonaje['estadosPersonaje1']['heridoLeve'] . "',
                            heridoGrave = '" . $caracteristicasPersonaje['estadosPersonaje1']['heridoGrave'] . "',
                            confundido = '" . $caracteristicasPersonaje['estadosPersonaje1']['confundido'] . "',
                            fuego = '" . $caracteristicasPersonaje['auraPersonaje1']['fuego'] . "',
                            veneno = '" . $caracteristicasPersonaje['auraPersonaje1']['veneno'] . "',
                            enigmatico = '" . $caracteristicasPersonaje['auraPersonaje1']['enigmatico'] . "',
                            pinchos = '" . $caracteristicasPersonaje['auraPersonaje1']['pinchos'] . "',
                            sombra = '" . $caracteristicasPersonaje['auraPersonaje1']['sombra'] . "'
                            WHERE pa.id_partida = " . $_SESSION['partida'] . "";
            $resultado = $bd->query($updateHechicero);
            if ($bd->errno) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update del hechicero";
            }
            // UPDATE vara
            $updateVara = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = pa.personaje1_id
                            JOIN hechicero h ON h.id_personaje = pa.personaje1_id
                            JOIN item_guardado ig ON h.id_personaje = ig.personaje_id
                            JOIN item i ON ig.item_id = i.id_item
                            SET desgaste = " . $caracteristicasPersonaje['inventarioPersonaje1']['arma']['vara'] . "
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
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje1']['curacion']['pocion'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de curacion simple";
            }
            $item_nombre = "superCuracion";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje1']['curacion']['superPocion'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de superCuracion";
            }
            $item_nombre = "curacionCompleta";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje1']['curacion']['pocionMax'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de curacionMax";
            }

            //UPDATES ESTAMINA 
            $item_nombre = "restaurarEstamina";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje1']['restaurarEstamina']['pocionEstamina'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de pocionEstamina";
            }
            $item_nombre = "restaurarMuchaEstamina";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje1']['restaurarEstamina']['superPocionEstamina'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de SuperPocionEstamina";
            }
            $item_nombre = "restaurarTodaEstamina";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje1']['restaurarEstamina']['pocionEstaminaMax'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de pocionEstaminaMax";
            }
            break;
        case 0:
            $updateDruida = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = pa.personaje1_id
                            JOIN druida d ON d.id_personaje = pa.personaje1_id
                            SET fuerza = '" . $caracteristicasPersonaje['personaje1']['fuerza'] . "',
                            armadura = '" . $caracteristicasPersonaje['personaje1']['armadura'] . "',
                            vidaActual = '" . $caracteristicasPersonaje['personaje1']['vidaActual'] . "',
                            vidaMaxima = '" . $caracteristicasPersonaje['personaje1']['vidaMaxima'] . "',
                            estaminaActual = '" . $caracteristicasPersonaje['personaje1']['estaminaActual'] . "',
                            estaminaMaxima = '" . $caracteristicasPersonaje['personaje1']['estaminaMaxima'] . "',
                            nivel = '" . $caracteristicasPersonaje['personaje1']['nivel'] . "',
                            puntosExperiencia = '" . $caracteristicasPersonaje['personaje1']['puntosExperiencia'] . "',
                            envenenado = '" . $caracteristicasPersonaje['estadosPersonaje1']['envenenado'] . "',
                            quemado = '" . $caracteristicasPersonaje['estadosPersonaje1']['quemado'] . "',
                            heridoLeve = '" . $caracteristicasPersonaje['estadosPersonaje1']['heridoLeve'] . "',
                            heridoGrave = '" . $caracteristicasPersonaje['estadosPersonaje1']['heridoGrave'] . "',
                            confundido = '" . $caracteristicasPersonaje['estadosPersonaje1']['confundido'] . "',
                            oso = '" . $caracteristicasPersonaje['transformacionesPersonaje1']['oso'] . "',
                            serpiente = '" . $caracteristicasPersonaje['transformacionesPersonaje1']['serpiente'] . "',
                            zorro = '" . $caracteristicasPersonaje['transformacionesPersonaje1']['zorro'] . "',
                            aguila = '" . $caracteristicasPersonaje['transformacionesPersonaje1']['águila'] . "'   
                            WHERE pa.id_partida = " . $_SESSION['partida'] . "";
            $resultado = $bd->query($updateDruida);
            if ($bd->errno) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update del druida";
            }
            // UPDATE vara
            $updateDaga = "UPDATE personaje pe JOIN partida pa ON pe.id_personaje = pa.personaje1_id
                            JOIN druida d ON d.id_personaje = pa.personaje1_id
                            JOIN item_guardado ig ON d.id_personaje = ig.personaje_id
                            JOIN item i ON ig.item_id = i.id_item
                            SET desgaste = " . $caracteristicasPersonaje['inventarioPersonaje1']['arma']['daga'] . "
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
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje1']['curacion']['pocion'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de curacion simple";
            }
            $item_nombre = "superCuracion";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje1']['curacion']['superPocion'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de superCuracion";
            }
            $item_nombre = "curacionCompleta";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje1']['curacion']['pocionMax'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de curacionMax";
            }

            //UPDATES ESTAMINA 
            $item_nombre = "restaurarEstamina";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje1']['restaurarEstamina']['pocionEstamina'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de pocionEstamina";
            }
            $item_nombre = "restaurarMuchaEstamina";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje1']['restaurarEstamina']['superPocionEstamina'];
            $stmt->bind_param("is", $cantidad, $item_nombre);
            //esta linea ejecuta el update preparado y si esta mal salta error
            if (!$stmt->execute()) {
                $errorUpdateConsulta = true;
                $descripcionError = "error en el update preparado de SuperPocionEstamina";
            }
            $item_nombre = "restaurarTodaEstamina";
            $cantidad = (int) $caracteristicasPersonaje['inventarioPersonaje1']['restaurarEstamina']['pocionEstaminaMax'];
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
        header("location: combate.php");
    }



    // NO SE QUE HACE LA SIGUIENTE LINEA ES PARA NO TENER PROBLEMAS DE CACHÉ
    $cssVersion = @filemtime(__DIR__ . "/estilos/estilos.css") ?: time();
} else {
    echo "no entro";
}
