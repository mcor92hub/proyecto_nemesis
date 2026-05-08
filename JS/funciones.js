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
        case (personaje instanceof Arquero || personaje instanceof Caballero):
            inventarioPersonaje = mapaParaObjeto(personaje.inventario);
            estadosPersonaje = mapaParaObjeto(personaje.estado);
            console.log(JSON.stringify(inventarioPersonaje));
            console.log(JSON.stringify(estadosPersonaje));
            console.log(JSON.stringify(personaje));
            fetch("updatePersonaje1.php", {
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
            fetch("updatePersonaje1.php", {
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
            fetch("updatePersonaje1.php", {
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
        body: JSON.stringify({turno})
    })
    alert("fetch turno "+ turno);
    //alert("fetch turno", turno);
        // .then(response => response.json())
        // .then(data => {
        //     if (data.success) {
        //         window.location.href = "combate.php";
        //     }
        // });
}
