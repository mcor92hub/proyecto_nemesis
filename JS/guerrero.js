class Guerrero extends Personaje {
    aguanteActual;
    aguanteMax;

    constructor(nombre, fuerza, armadura, nivel, vidaActual, vidaMaxima, estaminaActual, estaminaMaxima, puntosExperiencia, aguanteActual, aguanteMax) {
        super(nombre, fuerza, armadura, nivel, vidaActual, vidaMaxima, estaminaActual, estaminaMaxima, puntosExperiencia);
        this.aguanteActual = aguanteActual;
        this.aguanteMax = aguanteMax;
    }

    ataqueSimple(objetivo) {
        if (objetivo instanceof Personaje) {
            //critico
            let critico = Math.floor((Math.random() * 10) + 1);
            let daño = this.fuerza / 5;
            if (critico == 10) {
                daño *= 1.5;
                console.log("CRITICO");
            }
            try {
                if (this.aguanteActual < 10) {
                    alert(`${this.nombre} está agotado`);
                    throw new Error("Estas agotado");
                } else {
                    //cálculo de armadura para luego restárselo al daño
                    let indiceDefensa = 1
                    if (objetivo.armadura <= 10) {
                        indiceDefensa = 1;
                    } else {
                        indiceDefensa = objetivo.armadura / 10;
                    }
                    objetivo.recibirDaño(daño - indiceDefensa);
                    //por cada ataque simple hay 1 entre 10 probabilidades de inflingir herida leve y 1 entre 20 de inflingir confusion
                    let posibleHerida = Math.floor((Math.random() * 10) + 1);
                    if (posibleHerida == 10) {
                        objetivo.estado.set("heridoLeve", true);
                        console.log("HERIDO LEVE");
                    }
                    let posibleConfusion = Math.floor((!Math.random() * 20) + 1);
                    if (posibleConfusion == 20) {
                        objetivo.estado.set("confundido", true);
                        console.log("CONFUNDIDO");
                    }
                    this.aguanteActual -= 10;
                    //si el objetivo muere se llama al método subirnivel de la clase personaje
                    if (objetivo.vidaActual <= 0) {
                        this.puntosExperiencia += objetivo.vidaMaxima;
                        this.subirNivel();
                    }
                }
            } catch (error) {
                console.log(error);
            }
            console.log(this);
        } else {
            console.log("El objetivo no es un personaje válido");
        }
    }

    restaurarAguante(objeto) {
        if (objeto instanceof Objetos) {
            // 1º comprobamos si el objeto es de la clase objetos y después si tiene el objeto en el inventario (en el mapa interior que pertenece a la clave restaurarEstamina)
            let objetosRestaurarAguante = this.inventario.get("restaurarEstamina");
            if (objetosRestaurarAguante.has(objeto.nombre)) {
                if (objeto.nombre == "restaurarEstaminaCompleta") {
                    this.aguanteActual = this.aguanteMax;
                    let cantidad = objetosRestaurarAguante.get(objeto.nombre);
                    if (cantidad - 1 == 0) {
                        objetosRestaurarAguante.delete(objeto.nombre);
                    } else {
                        objetosRestaurarAguante.set(objeto.nombre, cantidad - 1)
                    }
                } else {
                    if (this.aguanteActual + objeto.puntos >= this.aguanteMax) {
                        this.aguanteActual = this.aguanteMax;
                        let cantidad = objetosRestaurarAguante.get(objeto.nombre);
                        if (cantidad - 1 == 0) {
                            objetosRestaurarAguante.delete(objeto.nombre);
                        } else {
                            objetosRestaurarAguante.set(objeto.nombre, cantidad - 1)
                        }
                    } else {
                        this.aguanteActual += objeto.puntos;
                        let cantidad = objetosRestaurarAguante.get(objeto.nombre);
                        if (cantidad - 1 == 0) {
                            objetosRestaurarAguante.delete(objeto.nombre);
                        } else {
                            objetosRestaurarAguante.set(objeto.nombre, cantidad - 1)
                        }
                    }
                }
            } else {
                console.log(`No te quedan objetos ${objeto.nombre}`);
            }
        } else {
            console.log("No es un objeto de clase 'Objetos'");
        }
        console.log(`Vida de ${this.nombre}: ${this.vidaActual}`);
    }

}