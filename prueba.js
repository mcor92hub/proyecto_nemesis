case "Arquero":
                    personaje2 = new Arquero(caracteristicasPersonaje2[0]['nombre'], caracteristicasPersonaje2[0]['fuerza'], caracteristicasPersonaje2[0]['armadura'], caracteristicasPersonaje2[0]['vidaActual'], caracteristicasPersonaje2[0]['vidaMaxima'], caracteristicasPersonaje2[0]['nivel'], caracteristicasPersonaje2[0]['puntosExperiencia'], caracteristicasPersonaje2[0]['aguanteActual'], caracteristicasPersonaje2[0]['aguanteMaximo'], caracteristicasPersonaje2[0]['agudeza']);

                    break;
                case "Caballero":
                    personaje2 = new Caballero(caracteristicasPersonaje2[0]['nombre'], caracteristicasPersonaje2[0]['fuerza'], caracteristicasPersonaje2[0]['armadura'], caracteristicasPersonaje2[0]['vidaActual'], caracteristicasPersonaje2[0]['vidaMaxima'], caracteristicasPersonaje2[0]['nivel'], caracteristicasPersonaje2[0]['puntosExperiencia'], caracteristicasPersonaje2[0]['aguanteActual'], caracteristicasPersonaje2[0]['aguanteMaximo']);
                    break;
                case "Hechicero":
                    personaje2 = new Hechicero(caracteristicasPersonaje2[0]['nombre'], caracteristicasPersonaje2[0]['fuerza'], caracteristicasPersonaje2[0]['armadura'], caracteristicasPersonaje2[0]['vidaActual'], caracteristicasPersonaje2[0]['vidaMaxima'], caracteristicasPersonaje2[0]['nivel'], caracteristicasPersonaje2[0]['puntosExperiencia'], caracteristicasPersonaje2[0]['manaActual'], caracteristicasPersonaje2[0]['manaMaximo'], caracteristicasPersonaje2[0]['inteligencia']);
                    break;
                case "Druida":
                    personaje2 = new Druida(caracteristicasPersonaje2[0]['nombre'], caracteristicasPersonaje2[0]['fuerza'], caracteristicasPersonaje2[0]['armadura'], caracteristicasPersonaje2[0]['vidaActual'], caracteristicasPersonaje2[0]['vidaMaxima'], caracteristicasPersonaje2[0]['nivel'], caracteristicasPersonaje2[0]['puntosExperiencia'], caracteristicasPersonaje2[0]['manaActual'], caracteristicasPersonaje2[0]['manaMaximo'], caracteristicasPersonaje2[0]['inteligencia']);
                    break;