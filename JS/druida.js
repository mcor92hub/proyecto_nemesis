class Druida extends Personaje {
    posiblesTransformaciones = new Map();
    //posible transformacion en puercoespin que aumenta su armadura
    listaBotones;
    listaFunciones;
    constructor(nombre, fuerza, armadura, nivel, vidaActual, vidaMaxima, estaminaActual, estaminaMaxima, puntosExperiencia, inteligencia, quemado, envenenado, confundido, heridoLeve, heridoGrave) {
        super(nombre, fuerza, armadura, nivel, vidaActual, vidaMaxima, estaminaActual, estaminaMaxima, puntosExperiencia, quemado, envenenado, confundido, heridoLeve, heridoGrave);
        this.inteligencia = inteligencia;
        this.posiblesTransformaciones.set("oso", false);
        this.posiblesTransformaciones.set("serpiente", false);
        this.posiblesTransformaciones.set("zorro", false);
        this.posiblesTransformaciones.set("águila", false);
        this.listaBotones = ["Ataque Básico", "Transformacion", "Piel de Carballo", "Enredaderas"];
        this.listaFunciones = ["ataqueSimple", "transformacion", "pielDeCarballo", "enredaderas"];
    }

    asignarObjetos(desgasteDaga, numPocion, numSuperPocion, numPocionMax, numPocionEstamina, numSuperPocionEstamina, numPocionEstaminaMax){
        let objetosArma = this.inventario.get("arma");
        let objetosCuracion = this.inventario.get("curacion");
        let objetosRestaurarAguante = this.inventario.get("restaurarEstamina");
        objetosArma.set("daga", desgasteDaga);
        objetosCuracion.set("pocion", numPocion);
        objetosCuracion.set("superPocion", numSuperPocion);
        objetosCuracion.set("pocionMax", numPocionMax);
        objetosRestaurarAguante.set("pocionEstamina", numPocionEstamina);
        objetosRestaurarAguante.set("superPocionEstamina", numSuperPocionEstamina);
        objetosRestaurarAguante.set("pocionEstaminaMax", numPocionEstaminaMax);
    }

    transformacion() {
        if (this.estaminaActual >= 20) {
            this.estaminaActual -= 20;
            let inteligenciaActual = this.inteligencia;
            let armaduraActual = this.armadura;
            let azar = Math.floor(Math.random() * 4);
            switch (azar) {
                // Al hacer cada transformación mandamos las otras a false para que no haya transformaciones múltiples
                case 0:
                    this.posiblesTransformaciones.set("oso", true);
                    this.posiblesTransformaciones.set("serpiente", false);
                    this.posiblesTransformaciones.set("zorro", false);
                    this.posiblesTransformaciones.set("águila", false);
                    this.fuerza = 80;
                    this.armadura = 80;
                    this.inteligencia = inteligenciaActual;
                    console.log("OSO");
                    break;
                case 1:
                    this.posiblesTransformaciones.set("oso", false);
                    this.posiblesTransformaciones.set("serpiente", true);
                    this.posiblesTransformaciones.set("zorro", false);
                    this.posiblesTransformaciones.set("águila", false);
                    this.fuerza = 60;
                    this.armadura = armaduraActual;
                    this.inteligencia = inteligenciaActual;
                    console.log("SERPIENTE");
                    break;
                case 2:
                    this.posiblesTransformaciones.set("oso", false);
                    this.posiblesTransformaciones.set("serpiente", false);
                    this.posiblesTransformaciones.set("zorro", true);
                    this.posiblesTransformaciones.set("águila", false);
                    this.inteligencia = 50;
                    this.fuerza = 30;
                    this.armadura = 20;
                    console.log("ZORRO");
                    break;
                case 3:
                    this.posiblesTransformaciones.set("oso", false);
                    this.posiblesTransformaciones.set("serpiente", false);
                    this.posiblesTransformaciones.set("zorro", false);
                    this.posiblesTransformaciones.set("águila", true);
                    this.fuerza = 60;
                    this.armadura = armaduraActual;
                    this.inteligencia = inteligenciaActual;
                    console.log("ÁGUILA");
                    break;
            }
        } else {
            console.log("No tienes maná suficiente");
        }
        console.log(this);
    }

    //reescribo el recibirDaño() porque al ser águila puede esquivar ataques volando pero le cuesta maná, si no tiene maná o si no es águila se ejecuta el método igual que en la clase Personaje
    recibirDaño(daño) {
        if (this.posiblesTransformaciones.get("águila") == true) {
            if (this.estaminaActual >= 20) {
                let azar = Math.floor((Math.random() * 2) + 1);
                if (azar == 1) {
                    daño = 0
                    this.estaminaActual -= 20
                    console.log("ataque esquivado volando");
                }
                super.recibirDaño(daño);
            } else {
                super.recibirDaño(daño);
            }
        } else {
            super.recibirDaño(daño);
        }
    }

    ataqueSimple(objetivo) {
        switch (true) {
            case (this.posiblesTransformaciones.get("oso") == true):
                let posibleHeridaGrave = Math.floor((Math.random() * 10) + 1);
                if (posibleHeridaGrave == 10) {
                    objetivo.estado.set("heridoGrave", true)
                }
                super.ataqueSimple(objetivo);
                break;

            case (this.posiblesTransformaciones.get("serpiente") == true):
                let posibleEnvenenamiento = Math.floor((Math.random() * 5) + 1);
                if (posibleEnvenenamiento == 5) {
                    objetivo.estado.set("envenenado", true);
                }
                super.ataqueSimple(objetivo);
                break;

            case (this.posiblesTransformaciones.get("zorro") == true):
                //el zorro zorrea
                let posibleConfusion = Math.floor((Math.random() * 5) + 1);
                if (posibleConfusion == 5) {
                    objetivo.estado.set("confundido", true);
                }
                super.ataqueSimple(objetivo);
                break;
            case (this.posiblesTransformaciones.get("águila") == true):
                let posibleHeridoLeve = Math.floor((Math.random() * 5) + 1);
                if (posibleHeridoLeve == 5) {
                    objetivo.estado.set("heridoLeve", true);
                }
                super.ataqueSimple(objetivo);
                break;

            default:
                //si no está transformado ataca con la daga y es un ataque normal que desgasta el arma
                let objetosArma = this.inventario.get("arma");
                if (objetosArma.has("daga")) {
                    super.ataqueSimple(objetivo);
                    let desgaste = objetosArma.get("daga");
                    if (desgaste == 5) {
                        objetosArma.delete("daga");
                    } else {
                        objetosArma.set("daga", desgaste - 5)
                    }
                } else {
                    console.log("No tienes daga para hacer ataques simples");
                }
                break;
        }
    }

    pielDeCarballo() {
        if (this.estaminaActual >= 20) {
            this.estaminaActual -= 20;
            this.armadura += 20;
        } else {
            console.log(`${this.nombre} se ha quedado sin maná`);
        }
        console.log(this);
    }

    enredaderas(objetivo) {
        if (this.estaminaActual >= 20) {
            objetivo.fuerza -= 20;
            this.estaminaActual -= 20;
        } else {
            console.log(`${this.nombre} se ha quedado sin maná`);
        }
        console.log(objetivo);
    }
}