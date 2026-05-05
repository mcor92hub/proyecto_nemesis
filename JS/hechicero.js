class Hechicero extends Personaje {
    aura = new Map();
    listaBotones;
    listaFunciones;
    constructor(nombre, fuerza, armadura, nivel, vidaActual, vidaMaxima, estaminaActual, estaminaMaxima, puntosExperiencia, inteligencia, quemado, envenenado, confundido, heridoLeve, heridoGrave) {
        super(nombre, fuerza, armadura, nivel, vidaActual, vidaMaxima, estaminaActual, estaminaMaxima, puntosExperiencia, quemado, envenenado, confundido, heridoLeve, heridoGrave);
        this.inteligencia = inteligencia;
        this.aura.set("fuego", false);
        this.aura.set("pantanoso", false);
        this.aura.set("enigma", false);
        this.aura.set("pinchos", false);
        this.aura.set("sombra", false);
        this.listaBotones = ["Ataque Básico", "Farmear Aura", "Leer", "Rimbonbancia"];
        this.listaFunciones = ["ataqueSimple", "farmearAura", "leer", "rimbonbancia"];
    }

    asignarObjetos(desgasteVara, numPocion, numSuperPocion, numPocionMax, numPocionAguante, numSuperPocionAguante, numPocionAguanteMax){
        let objetosArma = this.inventario.get("arma");
        let objetosCuracion = this.inventario.get("curacion");
        let objetosRestaurarAguante = this.inventario.get("restaurarEstamina");
        objetosArma.set("vara", desgasteVara);
        objetosCuracion.set("pocion", numPocion);
        objetosCuracion.set("superPocion", numSuperPocion);
        objetosCuracion.set("pocionMax", numPocionMax);
        objetosRestaurarAguante.set("pocionAguante", numPocionAguante);
        objetosRestaurarAguante.set("superPocionAguante", numSuperPocionAguante);
        objetosRestaurarAguante.set("pocionAguanteMax", numPocionAguanteMax);
    }

    //el hechicero tendrá una habilidad elemental que usará para el ataque simple, que irá hacia los estados alterados, por ejemplo fuego para quemado, veneno para envenenado, enigma para confundirlo, cuchillas para heridoLeve y pedrolos para heridoGrave

    farmearAura() {
        let opcion = prompt("Escoge el aura del hechicero (fuego, veneno, enigmático, acero o sombrío): ")
        let inteligenciaActual = this.inteligencia;
        let fuerzaActual = this.fuerza;
        let armaduraActual = this.armadura;
        switch (opcion) {
            case "fuego":
                if (this.estaminaActual >= 30) {
                    this.estaminaActual -= 30;
                    this.aura.set("fuego", true);
                    this.aura.set("pantanoso", false);
                    this.aura.set("enigma", false);
                    this.aura.set("pinchos", false);
                    this.aura.set("sombra", false);
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
                    this.aura.set("fuego", false);
                    this.aura.set("pantanoso", true);
                    this.aura.set("enigma", false);
                    this.aura.set("pinchos", false);
                    this.aura.set("sombra", false);
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
                    this.aura.set("fuego", false);
                    this.aura.set("pantanoso", false);
                    this.aura.set("enigma", true);
                    this.aura.set("pinchos", false);
                    this.aura.set("sombra", false);
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
                    this.aura.set("fuego", false);
                    this.aura.set("pantanoso", false);
                    this.aura.set("enigma", false);
                    this.aura.set("pinchos", true);
                    this.aura.set("sombra", false);
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
                    this.aura.set("fuego", false);
                    this.aura.set("pantanoso", false);
                    this.aura.set("enigma", false);
                    this.aura.set("pinchos", false);
                    this.aura.set("sombra", true);
                    this.fuerza = 70;
                    this.armadura = 40;
                    this.inteligencia = inteligenciaActual;
                } else {
                    console.log(`${this.nombre} se ha quedado sin maná`);
                }
                break;
            default:
                this.aura.set("fuego", false);
                this.aura.set("pantanoso", false);
                this.aura.set("enigma", false);
                this.aura.set("pinchos", false);
                this.aura.set("sombra", false);
                alert("solo se puede seleccionar las siguientes transformaciones: fuego, veneno, enigmático, acero o sombrío");
                break;
        }
        console.log(this);
    }

    ataqueSimple(objetivo) {
        let objetosArma = this.inventario.get("arma");
        switch (true) {
            case (this.aura.get("fuego") == true):
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

            case (this.aura.get("pantanoso") == true):
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

            case (this.aura.get("enigma") == true):
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

            case (this.aura.get("pinchos") == true):
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

            case (this.aura.get("sombra") == true):
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