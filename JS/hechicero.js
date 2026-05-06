class Hechicero extends Personaje {
    aura = new Map();
    listaBotones;
    listaFunciones;
    constructor(nombre, fuerza, armadura, nivel, vidaActual, vidaMaxima, estaminaActual, estaminaMaxima, puntosExperiencia, quemado, envenenado, confundido, heridoLeve, heridoGrave, inteligencia, fuego, veneno, enigmatico, pinchos, sombra) {
        super(nombre, fuerza, armadura, nivel, vidaActual, vidaMaxima, estaminaActual, estaminaMaxima, puntosExperiencia, quemado, envenenado, confundido, heridoLeve, heridoGrave);
        this.inteligencia = inteligencia;
        this.aura.set("fuego", fuego);
        this.aura.set("veneno", veneno);
        this.aura.set("enigmatico", enigmatico);
        this.aura.set("pinchos", pinchos);
        this.aura.set("sombra", sombra);
        this.listaBotones = ["Ataque Básico", "Farmear Aura", "Leer", "Rimbonbancia"];
        this.listaFunciones = ["ataqueSimple", "farmearAura", "leer", "rimbonbancia"];
    }

    asignarObjetos(desgasteVara, numPocion, numSuperPocion, numPocionMax, numPocionAguante, numSuperPocionAguante, numPocionAguanteMax) {
        let objetosArma = this.inventario.get("arma");
        let objetosCuracion = this.inventario.get("curacion");
        let objetosRestaurarAguante = this.inventario.get("restaurarEstamina");
        objetosArma.set("vara", desgasteVara);
        objetosCuracion.set("pocion", numPocion);
        objetosCuracion.set("superPocion", numSuperPocion);
        objetosCuracion.set("pocionMax", numPocionMax);
        objetosRestaurarAguante.set("pocionEstamina", numPocionAguante);
        objetosRestaurarAguante.set("superPocionEstamina", numSuperPocionAguante);
        objetosRestaurarAguante.set("pocionEstaminaMax", numPocionAguanteMax);
    }

    //el hechicero tendrá una habilidad elemental que usará para el ataque simple, que irá hacia los estados alterados, por ejemplo fuego para quemado, veneno para envenenado, enigmatico para confundirlo, cuchillas para heridoLeve y pedrolos para heridoGrave

    farmearAura() {
        let opcion = prompt("Escoge el aura del hechicero (fuego, veneno, enigmático, acero, sombrío o normal): ");
        let inteligenciaActual = this.inteligencia;
        let fuerzaActual = this.fuerza;
        let armaduraActual = this.armadura;
        switch (opcion) {
            case "fuego":
                if (this.estaminaActual >= 30) {
                    this.estaminaActual -= 30;
                    this.aura.set("fuego", 1);
                    this.aura.set("veneno", 0);
                    this.aura.set("enigmatico", 0);
                    this.aura.set("pinchos", 0);
                    this.aura.set("sombra", 0);
                    this.fuerza = 50;
                    this.armadura = 50;
                    this.inteligencia = inteligenciaActual;
                } else {
                    console.log(`${this.nombre} se ha quedado sin maná`);
                }
                break;
            case "veneno":
                if (this.estaminaActual >= 20) {
                    this.estaminaActual -= 20;
                    this.aura.set("fuego", 0);
                    this.aura.set("veneno", 1);
                    this.aura.set("enigmatico", 0);
                    this.aura.set("pinchos", 0);
                    this.aura.set("sombra", 0);
                    this.fuerza = fuerzaActual;
                    this.armadura = 60;
                    this.inteligencia = inteligenciaActual;
                } else {
                    console.log(`${this.nombre} se ha quedado sin maná`);
                }
                break;
            case "enigmático":
                if (this.estaminaActual >= 40) {
                    this.estaminaActual -= 40;
                    this.aura.set("fuego", 0);
                    this.aura.set("veneno", 0);
                    this.aura.set("enigmatico", 1);
                    this.aura.set("pinchos", 0);
                    this.aura.set("sombra", 0);
                    this.fuerza = fuerzaActual;
                    this.armadura = armaduraActual;
                    this.inteligencia = 50;
                } else {
                    console.log(`${this.nombre} se ha quedado sin maná`);
                }
                break;
            case "acero":
                if (this.estaminaActual >= 20) {
                    this.estaminaActual -= 20
                    this.aura.set("fuego", 0);
                    this.aura.set("veneno", 0);
                    this.aura.set("enigmatico", 0);
                    this.aura.set("pinchos", 1);
                    this.aura.set("sombra", 0);
                    this.fuerza = 60;
                    this.armadura = 50;
                    this.inteligencia = inteligenciaActual;
                } else {
                    console.log(`${this.nombre} se ha quedado sin maná`);
                }
                break;
            case "sombrío":
                if (this.estaminaActual >= 30) {
                    this.estaminaActual -= 30;
                    this.aura.set("fuego", 0);
                    this.aura.set("veneno", 0);
                    this.aura.set("enigmatico", 0);
                    this.aura.set("pinchos", 0);
                    this.aura.set("sombra", 1);
                    this.fuerza = 70;
                    this.armadura = 40;
                    this.inteligencia = inteligenciaActual;
                } else {
                    console.log(`${this.nombre} se ha quedado sin maná`);
                }
                break;
            case "normal":
                this.aura.set("fuego", 0);
                this.aura.set("veneno", 0);
                this.aura.set("enigmatico", 0);
                this.aura.set("pinchos", 0);
                this.aura.set("sombra", 0);
                break;
            default:
                this.aura.set("fuego", 0);
                this.aura.set("veneno", 0);
                this.aura.set("enigmatico", 0);
                this.aura.set("pinchos", 0);
                this.aura.set("sombra", 0);
                alert("solo se puede seleccionar las siguientes transformaciones: fuego, veneno, enigmático, acero o sombrío");
                break;
        }
        console.log(this);
    }

    ataqueSimple(objetivo) {
        let objetosArma = this.inventario.get("arma");
        switch (true) {
            case (this.aura.get("fuego") == 1):
                let posibleQuemadura = Math.floor((Math.random() * 8) + 1);
                if (posibleQuemadura == 8) {
                    objetivo.estado.set("quemado", true);
                    console.log("quemado");
                }
                if (objetosArma.has("vara")) {
                    super.ataqueSimple(objetivo);
                    let desgaste = objetosArma.get("vara");
                    if (desgaste == 5) {
                        objetosArma.delete("vara");
                    } else {
                        objetosArma.set("vara", desgaste - 5)
                    }
                } else {
                    console.log("No tienes vara para hacer ataques simples");
                }
                break;

            case (this.aura.get("veneno") == 1):
                let posibleEnvenenamiento = Math.floor((Math.random() * 6) + 1);
                if (posibleEnvenenamiento == 6) {
                    objetivo.estado.set("envenenado", true);
                    console.log("envenenado");
                }
                if (objetosArma.has("vara")) {
                    super.ataqueSimple(objetivo);
                    let desgaste = objetosArma.get("vara");
                    if (desgaste == 5) {
                        objetosArma.delete("vara");
                    } else {
                        objetosArma.set("vara", desgaste - 5)
                    }
                } else {
                    console.log("No tienes vara para hacer ataques simples");
                }
                break;

            case (this.aura.get("enigmatico") == 1):
                let posibleConfusion = Math.floor((Math.random() * 5) + 1);
                if (posibleConfusion == 5) {
                    objetivo.estado.set("confundido", true);
                    console.log("confuso");
                }
                if (objetosArma.has("vara")) {
                    super.ataqueSimple(objetivo);
                    let desgaste = objetosArma.get("vara");
                    if (desgaste == 5) {
                        objetosArma.delete("vara");
                    } else {
                        objetosArma.set("vara", desgaste - 5)
                    }
                } else {
                    console.log("No tienes vara para hacer ataques simples");
                }
                break;

            case (this.aura.get("pinchos") == 1):
                let posibleHeridoLeve = Math.floor((Math.random() * 5) + 1);
                if (posibleHeridoLeve == 5) {
                    objetivo.estado.set("heridoLeve", true);
                    console.log("herido leve")
                }
                if (objetosArma.has("vara")) {
                    super.ataqueSimple(objetivo);
                    let desgaste = objetosArma.get("vara");
                    if (desgaste == 5) {
                        objetosArma.delete("vara");
                    } else {
                        objetosArma.set("vara", desgaste - 5)
                    }
                } else {
                    console.log("No tienes vara para hacer ataques simples");
                }
                break;

            case (this.aura.get("sombra") == 1):
                let posibleHeridaGrave = Math.floor((Math.random() * 8) + 1);
                if (posibleHeridaGrave == 5) {
                    objetivo.estado.set("heridoGrave", true);
                    console.log("herido grave");
                }
                if (objetosArma.has("vara")) {
                    super.ataqueSimple(objetivo);
                    let desgaste = objetosArma.get("vara");
                    if (desgaste == 5) {
                        objetosArma.delete("vara");
                    } else {
                        objetosArma.set("vara", desgaste - 5)
                    }
                } else {
                    console.log("No tienes vara para hacer ataques simples");
                }
                break;

            default:
                if (objetosArma.has("vara")) {
                    super.ataqueSimple(objetivo);
                    let desgaste = objetosArma.get("vara");
                    if (desgaste == 5) {
                        objetosArma.delete("vara");
                    } else {
                        objetosArma.set("vara", desgaste - 5)
                    }
                } else {
                    console.log("No tienes vara para hacer ataques simples");
                }
                break;
        }
    }

    leer() {
        if (this.estaminaActual >= 20) {
            this.inteligencia += 10;
            this.estaminaActual -= 20;
        } else {
            console.log(`${this.nombre} se ha quedado sin maná`);
        }
        console.log(this);
    }

    rimbonbancia(objetivo) {
        if (this.estaminaActual >= 10) {
            objetivo.armadura -= 10;
            this.estaminaActual -= 10;
        } else {
            console.log(`${this.nombre} se ha quedado sin maná`);
        }
        console.log(objetivo);
    }
}