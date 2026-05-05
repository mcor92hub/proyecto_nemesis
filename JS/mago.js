class Mago extends Personaje {
    manaActual;
    manaMaximo;
    inteligencia;
    constructor(nombre, fuerza, armadura, nivel, vidaActual, vidaMaxima, puntosExperiencia, manaActual, manaMaximo, inteligencia) {
        super(nombre, fuerza, armadura, nivel, vidaActual, vidaMaxima, puntosExperiencia);
        this.manaActual = manaActual;
        this.manaMaximo = manaMaximo;
        this.inteligencia = inteligencia;
    }

    ataqueSimple(objetivo) {
        //critico
        let critico = 0;
        let daño = this.fuerza / 5;
        switch (true) {
            //Por cada decena que suba la inteligencia se aumenta las probabilidades de impactar un crítico
            case (this.inteligencia <= 10):
                critico = Math.floor((Math.random() * 10) + 1);
                daño = this.fuerza / 5;
                if (critico == 10) {
                    daño *= 1.5;
                    console.log("CRITICO");
                }
                break;
            case (this.inteligencia <= 20 && this.inteligencia > 10):
                critico = Math.floor((Math.random() * 9) + 1);
                daño = this.fuerza / 5;
                if (critico == 9) {
                    daño *= 1.5;
                    console.log("CRITICO");
                }
                break;
            case (this.inteligencia <= 30 && this.inteligencia > 20):
                critico = Math.floor((Math.random() * 8) + 1);
                if (critico == 8) {
                    daño *= 1.5;
                    console.log("CRITICO");
                }
                break;
            case (this.inteligencia <= 40 && this.inteligencia > 30):
                critico = Math.floor((Math.random() * 7) + 1);
                if (critico == 7) {
                    daño *= 1.5;
                    console.log("CRITICO");
                }
                break;
            case (this.inteligencia <= 50 && this.inteligencia > 40):
                critico = Math.floor((Math.random() * 6) + 1);
                if (critico == 6) {
                    daño *= 1.5;
                    console.log("CRITICO");
                }
                break;
            default:
                critico = Math.floor((Math.random() * 5) + 1);
                if (critico == 5) {
                    daño *= 1.5;
                    console.log("CRITICO");
                }
                break;
        }
        try {
            if (this.manaActual < 10) {
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
                //en el caso de los magos no inflingen estados alterados para que lo hagan con sus transfomaciones y aura
                this.manaActual -= 10;
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
    }

    restaurarMana(objeto) {
        if (objeto instanceof Objetos) {
            // 1º comprobamos si el objeto es de la clase objetos y después si tiene el objeto en el inventario (en el mapa interior que pertenece a la clave restaurarEstamina)
            let objetosRestaurarMana = this.inventario.get("restaurarEstamina");
            if (objetosRestaurarMana.has(objeto.nombre)) {
                if (objeto.nombre == "restaurarEstaminaCompleta") {
                    this.manaActual = this.manaMaximo;
                    let cantidad = objetosRestaurarMana.get(objeto.nombre);
                    if (cantidad - 1 == 0) {
                        objetosRestaurarMana.delete(objeto.nombre);
                    } else {
                        objetosRestaurarMana.set(objeto.nombre, cantidad - 1)
                    }
                } else {
                    if (this.manaActual + objeto.puntos >= this.manaMaximo) {
                        this.manaActual = this.manaMaximo;
                        let cantidad = objetosRestaurarMana.get(objeto.nombre);
                        if (cantidad - 1 == 0) {
                            objetosRestaurarMana.delete(objeto.nombre);
                        } else {
                            objetosRestaurarMana.set(objeto.nombre, cantidad - 1)
                        }
                    } else {
                        this.manaActual += objeto.puntos;
                        let cantidad = objetosRestaurarMana.get(objeto.nombre);
                        if (cantidad - 1 == 0) {
                            objetosRestaurarMana.delete(objeto.nombre);
                        } else {
                            objetosRestaurarMana.set(objeto.nombre, cantidad - 1)
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