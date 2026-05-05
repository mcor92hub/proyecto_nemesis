class Objetos {
    tipo;
    nombre;
    puntos;
    constructor(tipo, nombre) {
        switch (tipo) {
            case "arma":
                this.tipo = tipo;
                switch (nombre) {
                    case "espada":
                        this.nombre = nombre;
                        break;
                    case "mazo":
                        this.nombre = nombre;
                        break;
                    case "daga":
                        this.nombre = nombre;
                        break;
                    case "arco":
                        this.nombre = nombre;
                        break;
                    case "flechas":
                        this.nombre = nombre;
                        break;
                    case "vara":
                        this.nombre = nombre;
                        break;
                    case "nunchakus":
                        this.nombre = nombre;
                        break;
                    default:
                        console.log("objeto de tipo arma no válido");
                        break;
                }
                break;
            case "curacion":
                this.tipo = tipo;
                switch (nombre) {
                    case "curacionSimple":
                        this.nombre = nombre;
                        this.puntos = 20;
                        break;
                    case "superCuracion":
                        this.nombre = nombre;
                        this.puntos = 50;
                        break;
                    case "curacionCompleta":
                        this.nombre = nombre;
                        this.puntos = 100;
                        break;
                    default:
                        console.log("objeto de tipo curacion no válido")
                        break;
                }
                break;
            case "restaurarEstamina":
                switch (nombre) {
                    case "restaurarEstamina":
                        this.nombre = nombre;
                        this.puntos = 20;
                        break;
                    case "restaurarMuchaEstamina":
                        this.nombre = nombre;
                        this.puntos = 50;
                        break;
                    case "restaurarTodaEstamina":
                        this.nombre = nombre;
                        this.puntos = 100;
                        break;
                    default:
                        console.log("objeto de tipo restaurarEstamina no válido");
                        break;
                }
                break;
            default:
                console.log("tipo no válido")
                break;

        }

    }


}
