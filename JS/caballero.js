class Caballero extends Personaje {
    listaBotones;
    listaFunciones;
    constructor(nombre, fuerza, armadura, nivel, vidaActual, vidaMaxima, estaminaActual, estaminaMaxima, puntosExperiencia) {
        super(nombre, fuerza, armadura, nivel, vidaActual, vidaMaxima, estaminaActual, estaminaMaxima, puntosExperiencia);
        this.listaBotones=["Ataque Básico", "Hostión", "Rugido", "Intimidación"];
        this.listaFunciones = ["ataqueSimple", "hostion", "rugido", "intimidacion"];
    }

    asignarObjetos(desgasteEspada, desgasteMazo, numPocion, numSuperPocion, numPocionMax, numPocionEstamina, numSuperPocionEstamina, numPocionEstaminaMax){
        let objetosArma = this.inventario.get("arma");
        let objetosCuracion = this.inventario.get("curacion");
        let objetosRestaurarEstamina = this.inventario.get("restaurarEstamina");
        objetosArma.set("espada", desgasteEspada);
        objetosArma.set("mazo", desgasteMazo);
        objetosCuracion.set("pocion", numPocion);
        objetosCuracion.set("superPocion", numSuperPocion);
        objetosCuracion.set("pocionMax", numPocionMax);
        objetosRestaurarEstamina.set("pocionEstamina", numPocionEstamina);
        objetosRestaurarEstamina.set("superPocionEstamina", numSuperPocionEstamina);
        objetosRestaurarEstamina.set("pocionEstaminaMax", numPocionEstaminaMax);
    }

    ataqueSimple(objetivo) {
        let objetosArma = this.inventario.get("arma");
        if (objetosArma.has("espada")) {
            super.ataqueSimple(objetivo);
            let desgaste = objetosArma.get("espada");
            if (desgaste == 5) {
                objetosArma.delete("espada");
            } else {
                objetosArma.set("espada", desgaste - 5)
            }
        } else {
            console.log("No tienes espada para realizar ataques simples");
        }
    }

    hostion(objetivo) {
        let daño = this.fuerza / 4;
        let critico = Math.floor((Math.random() * 10) + 1);
        if (critico == 10) {
            daño *= 1.5;
            console.log("CRITICO");
        }
        let objetosArma = this.inventario.get("arma");
        if (objetosArma.has("mazo") && this.estaminaActual >= 20) {
            let indiceDefensa = 1
            if (objetivo.armadura <= 10) {
                indiceDefensa = 1;
            } else {
                indiceDefensa = objetivo.armadura / 10;
            }
            objetivo.recibirDaño(daño - indiceDefensa);
            //despues de recibir daño se calculan probabilidades para inflingir estados alterados
            let posibleHeridaGrave = Math.floor((Math.random() * 10) + 1);
            if (posibleHeridaGrave == 10) {
                objetivo.estado.set("heridoGrave", true)
            }
            let posibleConfusion = Math.floor((!Math.random() * 20) + 1);
            if (posibleConfusion == 20) {
                objetivo.estado.set("confundido", true);
                console.log("CONFUNDIDO");
            }
            this.estaminaActual -= 20;
            let desgaste = objetosArma.get("mazo");
            if (desgaste == 5) {
                objetosArma.delete("mazo");
            } else {
                objetosArma.set("mazo", desgaste - 5)
            }
            if (objetivo.vidaActual <= 0) {
                this.puntosExperiencia += objetivo.vidaMaxima;
                this.subirNivel();
            }
        } else {
            if (!objetosArma.has("mazo")) {
                console.log("No tienes un mazo para dar un hostión");
            }
            if (this.estaminaActual < 20) {
                console.log("Estás demasiado cansado para dar un hostión")
            }
        }
    }

    rugido() {
        if (this.estaminaActual >= 20) {
            this.estaminaActual -= 30;
            this.fuerza += 20;
        } else {
            console.log(`${this.nombre} se ha quedado sin estamina`);
        }
        console.log(this);
    }

    intimidacion(objetivo) {
        if (this.estaminaActual >= 20) {
            objetivo.armadura -= 10;
            this.estaminaActual -= 20;
        } else {
            console.log(`${this.nombre} se ha quedado sin estamina`);
        }
        console.log(objetivo);
    }
}