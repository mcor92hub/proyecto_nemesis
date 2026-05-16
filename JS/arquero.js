class Arquero extends Personaje {
    punteria;
    listaBotones;
    listaFunciones;
    constructor(nombre, fuerza, armadura, nivel, vidaActual, vidaMaxima, estaminaActual, estaminaMaxima, puntosExperiencia, quemado, envenenado, confundido, heridoLeve, heridoGrave, punteria) {
        super(nombre, fuerza, armadura, nivel, vidaActual, vidaMaxima, estaminaActual, estaminaMaxima, puntosExperiencia, quemado, envenenado, confundido, heridoLeve, heridoGrave);
        this.punteria = punteria;
        this.listaBotones = ["Ataque Básico", "Flechazo", "Hacerse Bolita", "Concentración"];
        this.listaFunciones = ["ataqueSimple", "flechazo", "hacerseBolita", "concentracion"];
    }

    asignarObjetos(desgasteArco, numFlechas, desgasteNunchakus, numPocion, numSuperPocion, numPocionMax, numPocionEstamina, numSuperPocionEstamina, numPocionEstaminaMax){
        let objetosArma = this.inventario.get("arma");
        let objetosCuracion = this.inventario.get("curacion");
        let objetosRestaurarEstamina = this.inventario.get("restaurarEstamina");
        objetosArma.set("arco", desgasteArco);
        objetosArma.set("flechas", numFlechas);
        objetosArma.set("nunchakus", desgasteNunchakus);
        objetosCuracion.set("pocion", numPocion);
        objetosCuracion.set("superPocion", numSuperPocion);
        objetosCuracion.set("pocionMax", numPocionMax);
        objetosRestaurarEstamina.set("pocionEstamina", numPocionEstamina);
        objetosRestaurarEstamina.set("superPocionEstamina", numSuperPocionEstamina);
        objetosRestaurarEstamina.set("pocionEstaminaMax", numPocionEstaminaMax);
    }

    ataqueSimple(objetivo) {
        //El arma se desgasta 5 puntos con cada ataque
        let objetosArma = this.inventario.get("arma");
        if (objetosArma.has("nunchakus")) {
            super.ataqueSimple(objetivo);
            let desgaste = objetosArma.get("nunchakus");
            if (desgaste == 5) {
                objetosArma.delete("nunchakus");
            } else {
                objetosArma.set("nunchakus", desgaste - 5)
            }
        } else {
            console.log("No tienes nunchakus para hacer ataques simples");
        }
    }

    flechazo(objetivo) {
        let daño = (this.fuerza / 5) * (this.punteria * 0.5);
        //critico
        let critico = Math.floor((Math.random() * 10) + 1);
        if (critico == 10) {
            daño *= 1.5;
            console.log("CRITICO");
        }
        let objetosArma = this.inventario.get("arma");
        if (objetosArma.has("flechas") && objetosArma.has("arco") && this.estaminaActual >= 20) {
            let indiceDefensa = 1
            if (objetivo.armadura <= 10) {
                indiceDefensa = 1;
            } else {
                indiceDefensa = objetivo.armadura / 10;
            }
            objetivo.recibirDaño(daño - indiceDefensa);
            console.log((this.fuerza / 5));
            //despues de recibir daño se calculan probabilidades para inflingir estados alterados
            let posibleEnvenenamiento = Math.floor((Math.random() * 10) + 1);
            if (posibleEnvenenamiento == 10) {
                objetivo.estado.set("envenenado", true);
                console.log("ENVENENADO");
            }
            this.estaminaActual -= 20;
            let cantidad = objetosArma.get("flechas");
            let desgaste = objetosArma.get("arco");
            if (desgaste == 5) {
                objetosArma.delete("arco");
            } else {
                objetosArma.set("arco", desgaste - 5)
            }
            if (cantidad - 1 == 0) {
                objetosArma.delete("flechas");
            } else {
                objetosArma.set("flechas", cantidad - 1);
            }
        } else {
            if (!objetosArma.has("flechas")) {
                console.log("No tienes flechas");
            }
            if (!objetosArma.has("arco")) {
                console.log("no tienes flechas o el arco se ha roto");
            }
            if (this.estaminaActual < 20) {
                console.log("No tienes estamina suficiente para un flechazo");
            }
        }
    }

    hacerseBolita() {
        if (this.estaminaActual >= 20) {
            this.estaminaActual -= 20;
            this.armadura += 20;
        } else {
            console.log(`${this.nombre} se ha quedado sin estamina`);
        }
        console.log(this);
    }

    concentracion() {
        if (this.estaminaActual >= 10) {
            this.punteria++;
            this.estaminaActual -= 10;
        } else {
            console.log(`${this.nombre} se ha quedado sin estamina`);
        }
        console.log(this);
    }
}