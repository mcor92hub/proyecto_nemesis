class Personaje {
    nombre;
    fuerza;
    armadura;
    vidaActual;
    vidaMaxima;
    estaminaActual;
    estaminaMaxima;
    nivel;
    puntosExperiencia;
    estado = new Map();
    inventario = new Map();

    constructor(nombre, fuerza, armadura, nivel, vidaActual, vidaMaxima, estaminaActual, estaminaMaxima, puntosExperiencia, quemado, envenenado, confundido, heridoLeve, heridoGrave) {
        this.nombre = nombre;
        this.fuerza = fuerza;
        this.armadura = armadura;
        this.nivel = nivel;
        this.vidaActual = vidaActual;
        this.vidaMaxima = vidaMaxima;
        this.estaminaActual = estaminaActual;
        this.estaminaMaxima = estaminaMaxima;
        this.puntosExperiencia = puntosExperiencia;
        this.estado.set("quemado", quemado);
        this.estado.set("envenenado", envenenado);
        this.estado.set("confundido", confundido);
        this.estado.set("heridoLeve", heridoLeve);
        this.estado.set("heridoGrave", heridoGrave);
        this.inventario.set("arma", new Map());
        this.inventario.set("curacion", new Map());
        this.inventario.set("restaurarEstamina", new Map());
    }

    subirNivel() {
        //Para que un personaje MAGO suba de nivel necesita llegar a la suma de su vida maxima y su maná
        //CAMBIAR ESTE MÉTODO PARA HACER CON "manaMaximo" y "estaminaMaximaimo"
        if (this instanceof Mago) {
            if (this.puntosExperiencia >= this.vidaMaxima + this.manaMaximo) {
                this.nivel++;
                //Al subir el nivel se reinician los puntos de experiencia y suma un 10% todos sus atributos
                this.puntosExperiencia = 0;
                this.vidaMaxima += (this.vidaMaxima * 0.1);
                this.manaMaximo += (this.manaMaximo * 0.1);
                this.inteligencia += (this.inteligencia * 0.1);
                console.log(`Nivel de ${this.nombre}: ${this.nivel}`);
            } else {
                console.log(`Puntos de experiencia sumados por ${this.nombre}: ${this.puntosExperiencia}`);
            }
        }
        //Para que un personaje GUERRERO suba de nivel necesita llegar a la suma de su vida maxima y su fuerza
        if (this instanceof Guerrero) {
            if (this.puntosExperiencia >= this.vidaMaxima + this.fuerza) {
                this.nivel++;
                this.puntosExperiencia = 0;
                this.vidaMaxima += (this.vidaMaxima * 0.1);
                this.estaminaMaxima += (this.estaminaMaxima * 0.1);
                this.fuerza += (this.fuerza * 0.1);
                console.log(`Nivel de ${this.nombre}: ${this.nivel}`);
            } else {
                console.log(`Puntos de experiencia sumados por ${this.nombre}: ${this.puntosExperiencia}`);
            }
        }
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
                if (this.estaminaActual < 10) {
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
                    this.estaminaActual -= 10;
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

    recibirDaño(daño) {
        if (this.vidaActual - daño <= 0) {
            this.vidaActual = 0;
            //En caso de muerte sumas un poco de experiencia, el total del daño del último ataque
            this.puntosExperiencia += daño;
            this.subirNivel();
            //Los estados alterados vuelven a false para no empezar la siguiente partida con alguna desventaja
            this.estado.set("quemado", false);
            this.estado.set("envenenado", false);
            this.estado.set("confundido", false);
            this.estado.set("heridoLeve", false);
            this.estado.set("heridoGrave", false);
            alert(`${this.nombre} ha muerto`)
        } else {
            this.vidaActual -= daño;
        }
        console.log(this);
    }

    curarVida(objeto) {
        let objetosCuracion = this.inventario.get("curacion");
        if (objetosCuracion.has(objeto)) {
            let cantidad;
            switch (objeto) {
                case "pocion":
                    if (this.vidaActual + 20 >= this.vidaMaxima) {
                        this.vidaActual = this.vidaMaxima;
                    } else {
                        this.vidaActual += 20;
                    }
                    cantidad = objetosCuracion.get(objeto);
                    if (cantidad - 1 == 0) {
                        objetosCuracion.delete(objeto);
                    } else {
                        objetosCuracion.set(objeto, cantidad - 1)
                    }
                    break;
                case "superPocion":
                    if (this.vidaActual + 50 >= this.vidaMaxima) {
                        this.vidaActual = this.vidaMaxima;
                    } else {
                        this.vidaActual += 50;
                    }
                    cantidad = objetosCuracion.get(objeto);
                    if (cantidad - 1 == 0) {
                        objetosCuracion.delete(objeto);
                    } else {
                        objetosCuracion.set(objeto, cantidad - 1)
                    }
                    break;
                case "pocionMax":
                    this.vidaActual = this.vidaMaxima;
                    cantidad = objetosCuracion.get(objeto);
                    if (cantidad - 1 == 0) {
                        objetosCuracion.delete(objeto);
                    } else {
                        objetosCuracion.set(objeto, cantidad - 1)
                    }
                    break;
                default:
                    break;
            }

        } else {
            console.log(`No te quedan objetos ${objeto}`);
        }
        console.log(`Vida de ${this.nombre}: ${this.vidaActual}`);
    }

    restaurarEstamina(objeto) {
        let objetosRestaurarEstamina = this.inventario.get("restaurarEstamina");
        if (objetosRestaurarEstamina.has(objeto)) {
            let cantidad;
            console.log(objeto);
            switch (objeto) {
                case "pocionEstamina":
                    if (this.estaminaActual + 20 >= this.estaminaMaxima) {
                        this.estaminaActual = this.estaminaMaxima;
                    } else {
                        this.estaminaActual += 20;
                    }
                    cantidad = objetosRestaurarEstamina.get(objeto);
                    if (cantidad - 1 <= 0) {
                        objetosRestaurarEstamina.delete(objeto);
                    } else {
                        objetosRestaurarEstamina.set(objeto, cantidad - 1)
                    }
                    break;
                case "superPocionEstamina":
                    if (this.estaminaActual + 50 >= this.estaminaMaxima) {
                        this.estaminaActual = this.estaminaMaxima;
                    } else {
                        this.estaminaActual += 50;
                    }
                    cantidad = objetosRestaurarEstamina.get(objeto);
                    if (cantidad - 1 <= 0) {
                        objetosRestaurarEstamina.delete(objeto);
                    } else {
                        objetosRestaurarEstamina.set(objeto, cantidad - 1)
                    }
                    break;
                case "pocionEstaminaMax":
                    this.estaminaActual = this.estaminaMaxima;
                    cantidad = objetosRestaurarEstamina.get(objeto);
                    if (cantidad - 1 <= 0) {
                        objetosRestaurarEstamina.delete(objeto);
                    } else {
                        objetosRestaurarEstamina.set(objeto, cantidad - 1)
                    }
                    break;
                default:
                    break;
            }
        }else {
            console.log(`No te quedan objetos ${objeto}`);
        }
        console.log(`Estamina de ${this.nombre}: ${this.estaminaActual}`);
    }

    añadirAInventario(objeto) {
        if (objeto instanceof Objetos) {
            if (!this.inventario.has(objeto.nombre)) {
                this.inventario.set(objeto.nombre, 1);
            } else {
                let cantidad = this.inventario.get(objeto.nombre);
                this.inventario.set(objeto.nombre, cantidad + 1);
            }
        } else {
            console.log("El objeto no es válido");
        }
    }

    mostrarInventario() {
        //idea dos selects, uno para curativos y otro para restauracion
    }



    rendirse(objetivo) {
        if (objetivo instanceof Personaje) {
            this.vidaActual = 0;
            objetivo.subirNivel();
        } else {
            console.log("El objetivo no es correcto");
        }
    }
}