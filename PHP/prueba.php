<?php
var_dump($_POST['nick']);
// El 3er parámetro de mysqli es la contraseña de MySQL. Si está en "" MySQL ve (using password: NO).
$db_host = "localhost";
$db_user = "root";
$db_pass = ""; // Pon aquí la misma contraseña que usa root en WAMP (phpMyAdmin / consola MySQL).
$db_name = "proyecto_nemesis";
$bd = new mysqli($db_host, $db_user, $db_pass, $db_name);
$error = $bd->connect_error;
if ($error) {
    echo "error de conexion";
} else {
    echo "conexion bien";
}

$usuario1 = "INSERT INTO usuario(nick, contraseña) VALUES('" . $_POST['nick'] . "', " . $_POST['passRegistro'] . ");";
// $usuario2 = "INSERT INTO usuario(nick, contraseña) VALUES('usuario2', 'abc123.');";

// Crea un usuario y si no da error crea los 4 personajes con el id del usuario recién creado
$bd->query($usuario1);
if (!$bd->errno) {
    echo "inserccion de usuario1 correcta";
    $idUsuario1 = "SELECT id_usuario FROM usuario WHERE nick = '" . $_POST['nick'] . "'";
    $personaje1 = "INSERT INTO personaje(nombre, fuerza, armadura, usuario_id) VALUES('" . $_POST['arquero'] . "',60,60,(" . $idUsuario1 . "));";
    $personaje2 = "INSERT INTO personaje(nombre, fuerza, armadura, usuario_id) VALUES('" . $_POST['caballero'] . "',100,100,(" . $idUsuario1 . "));";
    $personaje3 = "INSERT INTO personaje(nombre, fuerza, armadura, usuario_id) VALUES('" . $_POST['druida'] . "',50,30,(" . $idUsuario1 . "));";
    $personaje4 = "INSERT INTO personaje(nombre, fuerza, armadura, usuario_id) VALUES('" . $_POST['hechicero'] . "',50,30,(" . $idUsuario1 . "));";
    // insertamos los 4 personajes y después saco el id de cada 1 
    $bd->query($personaje1);
    $bd->query($personaje2);
    $bd->query($personaje3);
    $bd->query($personaje4);
    $idArquero1 = "SELECT id_personaje FROM personaje WHERE id_personaje = (" . $idUsuario1 . ")";
    $consultaArquero1 = $bd->query($idArquero1);
    $filaIdPersonaje1 = $consultaArquero1->fetch_assoc();
    $arquero1 = "INSERT INTO arquero(id_personaje) VALUES(" . $filaIdPersonaje1['id_personaje'] . ")";
    $caballero1 = "INSERT INTO caballero(id_personaje) VALUES(" . $filaIdPersonaje1['id_personaje'] . ")";
    $druida1 = "INSERT INTO druida(id_personaje) VALUES(" . $filaIdPersonaje1['id_personaje'] . ")";
    $hechicero1 = "INSERT INTO hechicero(id_personaje) VALUES(" . $filaIdPersonaje1['id_personaje'] . ")";
    $bd->query($arquero1);
    $bd->query($caballero1);
    $bd->query($druida1);
    $bd->query($hechicero1);
} else {
    echo "inserccion de usuario1 incorrecta";
}


// $bd->query($usuario2);
// if (!$bd->errno) {
//     echo "inserccion de usuario2 correcta";
//     $idUsuario2 = "SELECT id_usuario FROM usuario WHERE nick = 'usuario2'";
//     $personaje1 = "INSERT INTO personaje(nombre, fuerza, armadura, usuario_id) VALUES('arquero2',60,60,(" . $idUsuario2 . "));";
//     $personaje2="INSERT INTO personaje(nombre, fuerza, armadura, usuario_id) VALUES('caballero2',100,100,(" . $idUsuario2 . "));";
//     $personaje3="INSERT INTO personaje(nombre, fuerza, armadura, usuario_id) VALUES('druida2',50,30,(" . $idUsuario2 . "));";
//     $personaje4 = "INSERT INTO personaje(nombre, fuerza, armadura, usuario_id) VALUES('hechicero2',50,30,(" . $idUsuario2 . "));";
//     $bd->query($personaje1);
//     $bd->query($personaje2);
//     $bd->query($personaje3);
//     $bd->query($personaje4);
//     $idArquero2 = "SELECT id_personaje FROM personaje WHERE id_personaje = (".$idUsuario2.")";
//     $consultaArquero2 = $bd->query($idArquero2);
//     $filaIdPersonaje2 = $consultaArquero2->fetch_assoc();
//     $arquero2 = "INSERT INTO arquero(id_personaje) VALUES(".$filaIdPersonaje2['id_personaje'] .")";
//     $caballero2 = "INSERT INTO caballero(id_personaje) VALUES(".$filaIdPersonaje2['id_personaje'] .")";
//     $druida2 = "INSERT INTO druida(id_personaje) VALUES(".$filaIdPersonaje2['id_personaje'] .")";
//     $hechicero2 = "INSERT INTO hechicero(id_personaje) VALUES(".$filaIdPersonaje2['id_personaje'] .")";
//     $bd->query($arquero2);
//     $bd->query($caballero2);
//     $bd->query($druida2);
//     $bd->query($hechicero2);
// } else {
//     echo "inserccion de usuario2 incorrecta";
// }