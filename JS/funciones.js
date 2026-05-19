// El siguiente script convierte el mapa de inventario y el mapa de estado del personaje1 en objetos para poder enviarlos por fetch a updatePersonajes.php y actualizar la base de datos con los cambios que se hayan producido en el combate, como el uso de objetos o la aplicacion de estados
// Usé IA para convertir el mapa en un array porque JSON no me admite los mapas

function mapaParaObjeto(map) {
    const obj = Object.fromEntries(map);
    for (let [key, value] of Object.entries(obj)) {
        if (value instanceof Map) {
            obj[key] = mapaParaObjeto(value);
        }
    }
    return obj;
}

function fetchUpdate(personaje) {
    let inventarioPersonaje = mapaParaObjeto(personaje.inventario);
    let estadosPersonaje = mapaParaObjeto(personaje.estado);
    switch (true) {
        case (personaje instanceof Arquero):
            inventarioPersonaje = mapaParaObjeto(personaje.inventario);
            estadosPersonaje = mapaParaObjeto(personaje.estado);
            console.log(JSON.stringify(inventarioPersonaje));
            console.log(JSON.stringify(estadosPersonaje));
            console.log(JSON.stringify(personaje));
            fetch("updatePersonaje.php", {
                method: "POST",
                credentials: "include",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    personaje,
                    inventarioPersonaje,
                    estadosPersonaje
                })
            })
            
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = "combate.php";
                    }
                });
        // .then(response => response.text())
        // .then(data => location.reload(), console.log("recargo"))
        // .catch(error => {
        //     console.error("Error en fetch:", error);
        // });
        // break;
        case (personaje instanceof Caballero):
            inventarioPersonaje = mapaParaObjeto(personaje.inventario);
            estadosPersonaje = mapaParaObjeto(personaje.estado);
            console.log(JSON.stringify(inventarioPersonaje));
            console.log(JSON.stringify(estadosPersonaje));
            console.log(JSON.stringify(personaje));
            fetch("updatePersonaje.php", {
                method: "POST",
                credentials: "include",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    personaje,
                    inventarioPersonaje,
                    estadosPersonaje
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = "combate.php";
                    }
                });
            // .then(response => response.text())
            // .then(data => location.reload(), console.log("recargo"))
            // .catch(error => {
            //     console.error("Error en fetch:", error);
            // });
            break;
        case (personaje instanceof Hechicero):
            inventarioPersonaje = mapaParaObjeto(personaje.inventario);
            estadosPersonaje = mapaParaObjeto(personaje.estado);
            auraPersonaje = mapaParaObjeto(personaje.aura);
            console.log(JSON.stringify(inventarioPersonaje));
            console.log(JSON.stringify(estadosPersonaje));
            console.log(JSON.stringify(auraPersonaje));
            console.log(JSON.stringify(personaje));
            fetch("updatePersonaje.php", {
                method: "POST",
                credentials: "include",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    personaje,
                    inventarioPersonaje,
                    estadosPersonaje,
                    auraPersonaje
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = "combate.php";
                        console.log("fetch personaje1 actualizado correctamente");
                    }
                });
            // .then(response => response.text())
            // .then(data => location.reload(), console.log("recargo"))
            // .catch(error => {
            //     console.error("Error en fetch:", error);
            // });
            break;
        case (personaje instanceof Druida):
            inventarioPersonaje = mapaParaObjeto(personaje.inventario);
            estadosPersonaje = mapaParaObjeto(personaje.estado);
            transformacionesPersonaje = mapaParaObjeto(personaje.posiblesTransformaciones);
            console.log(JSON.stringify(inventarioPersonaje));
            console.log(JSON.stringify(estadosPersonaje));
            console.log(JSON.stringify(transformacionesPersonaje));
            console.log(JSON.stringify(personaje));
            fetch("updatePersonaje.php", {
                method: "POST",
                credentials: "include",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    personaje,
                    inventarioPersonaje,
                    estadosPersonaje,
                    transformacionesPersonaje
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = "combate.php";
                        console.log("fetch personaje1 actualizado correctamente");
                    }
                });
            // .then(response => response.text())
            // .then(data => location.reload(), console.log("recargo"))
            // .catch(error => {
            //     console.error("Error en fetch:", error);
            // });
            break;
        default:
            break;
    }
}

function fetchTurno(turno) {
    fetch("updateTurno.php", {
        method: "POST",
        credentials: "include",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ turno })
    })
    //alert("fetch turno "+ turno);
    //alert("fetch turno", turno);
    // .then(response => response.json())
    // .then(data => {
    //     if (data.success) {
    //         window.location.href = "combate.php";
    //     }
    // });
}

function fetchConsultaTurno() {
    return fetch("consultaTurno.php")
        .then(response => {
            if (!response.ok) {
                throw new Error("Error en el response");
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                return data.turno;
            } else {
                console.log("No se encontró la partida");
            }
        });
}

function comprobarEstado(personaje1, personaje2) {

        let confundidoPersonaje1 = document.getElementById("confundidoPersonaje1");
        let quemadoPersonaje1 = document.getElementById("quemadoPersonaje1");
        let heridoGravePersonaje1 = document.getElementById("heridoGravePersonaje1");
        let heridoLevePersonaje1 = document.getElementById("heridoLevePersonaje1");
        let envenenadoPersonaje1 = document.getElementById("envenenadoPersonaje1");
        if (personaje1.estado.get("confundido")) {
            confundidoPersonaje1.style.opacity = "1";
        }
        if (personaje1.estado.get("quemado")) {
            quemadoPersonaje1.style.opacity = "1";
        }
        if (personaje1.estado.get("heridoGrave")) {
            heridoGravePersonaje1.style.opacity = "1";
        }
        if (personaje1.estado.get("heridoLeve")) {
            heridoLevePersonaje1.style.opacity = "1";
        }
        if (personaje1.estado.get("envenenado")) {
            envenenadoPersonaje1.style.opacity = "1";
        }

        let confundidoPersonaje2 = document.getElementById("confundidoPersonaje2");
        let quemadoPersonaje2 = document.getElementById("quemadoPersonaje2");
        let heridoGravePersonaje2 = document.getElementById("heridoGravePersonaje2");
        let heridoLevePersonaje2 = document.getElementById("heridoLevePersonaje2");
        let envenenadoPersonaje2 = document.getElementById("envenenadoPersonaje2");
        if (personaje2.estado.get("confundido")) {
            confundidoPersonaje2.style.opacity = "1";
        }
        if (personaje2.estado.get("quemado")) {
            quemadoPersonaje2.style.opacity = "1";
        }
        if (personaje2.estado.get("heridoGrave")) {
            heridoGravePersonaje2.style.opacity = "1";
        }
        if (personaje2.estado.get("heridoLeve")) {
            heridoLevePersonaje2.style.opacity = "1";
        }
        if (personaje2.estado.get("envenenado")) {
            envenenadoPersonaje2.style.opacity = "1";
        }
    
}