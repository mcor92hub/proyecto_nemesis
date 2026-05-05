DROP SCHEMA IF EXISTS proyecto_nemesis;

CREATE SCHEMA proyecto_nemesis CHARACTER SET UTF8;

USE proyecto_nemesis;

CREATE TABLE usuario(
	id_usuario INTEGER AUTO_INCREMENT NOT NULL PRIMARY KEY,
	email VARCHAR(100) NOT NULL UNIQUE,
	nick VARCHAR(50) NOT NULL UNIQUE,
	contraseña VARCHAR(255) NOT NULL,
	dinero INT NOT NULL DEFAULT 100
)ENGINE InnoDB;

CREATE TABLE partida(
	id_partida INTEGER AUTO_INCREMENT NOT NULL PRIMARY KEY,
	usuario1_id INTEGER NOT NULL,
    personaje1_id INTEGER NOT NULL,
	usuario2_id INTEGER,
    personaje2_id INTEGER,
	estado VARCHAR(20),
    turno INTEGER, 
    ultima_actividad_usuario1 TIMESTAMP
)ENGINE InnoDB;

CREATE TABLE mensaje(
	id_mensaje INTEGER AUTO_INCREMENT NOT NULL PRIMARY KEY,
	contenido TEXT NOT NULL,
	usuario_id INTEGER NOT NULL,
	partida_id INTEGER NOT NULL
)ENGINE InnoDB;

CREATE TABLE personaje(
	id_personaje INTEGER AUTO_INCREMENT NOT NULL PRIMARY KEY,
	nombre VARCHAR(50) NOT NULL,
	fuerza INTEGER NOT NULL,
	armadura INTEGER NOT NULL,
	vidaActual INTEGER NOT NULL DEFAULT 100,
	vidaMaxima INTEGER NOT NULL DEFAULT 100,
    estaminaActual INTEGER NOT NULL DEFAULT 100,
    estaminaMaxima INTEGER NOT NULL DEFAULT 100,
	nivel INTEGER NOT NULL DEFAULT 1,
	puntosExperiencia INTEGER NOT NULL DEFAULT 0,
	envenenado BOOLEAN NOT NULL DEFAULT FALSE,
	quemado BOOLEAN NOT NULL DEFAULT FALSE,
	heridoLeve BOOLEAN NOT NULL DEFAULT FALSE,
	heridoGrave BOOLEAN NOT NULL DEFAULT FALSE,
	confundido BOOLEAN NOT NULL DEFAULT FALSE,
    victorias INTEGER NOT NULL DEFAULT 0,
    derrotas INTEGER NOT NULL DEFAULT 0,
    golpesCriticos INTEGER NOT NULL DEFAULT 0,
	usuario_id INTEGER NOT NULL
)ENGINE InnoDB;

CREATE TABLE arquero(
	id_personaje INTEGER AUTO_INCREMENT NOT NULL PRIMARY KEY,
	punteria INTEGER NOT NULL DEFAULT 1,
	FOREIGN KEY (id_personaje) REFERENCES personaje(id_personaje) ON DELETE CASCADE ON UPDATE RESTRICT
)ENGINE InnoDB;

CREATE TABLE caballero(
	id_personaje INTEGER AUTO_INCREMENT NOT NULL PRIMARY KEY,
	FOREIGN KEY (id_personaje) REFERENCES personaje(id_personaje) ON DELETE CASCADE ON UPDATE RESTRICT
)ENGINE InnoDB;

CREATE TABLE hechicero(
	id_personaje INTEGER AUTO_INCREMENT NOT NULL PRIMARY KEY,
	inteligencia INTEGER NOT NULL DEFAULT 20,
	sombra BOOLEAN NOT NULL DEFAULT FALSE,
	pinchos BOOLEAN NOT NULL DEFAULT FALSE,
	enigmatico BOOLEAN NOT NULL DEFAULT FALSE,
	veneno BOOLEAN NOT NULL DEFAULT FALSE,
	fuego BOOLEAN NOT NULL DEFAULT FALSE,
	FOREIGN KEY (id_personaje) REFERENCES personaje(id_personaje) ON DELETE CASCADE ON UPDATE RESTRICT
)ENGINE InnoDB;

CREATE TABLE druida(
	id_personaje INTEGER AUTO_INCREMENT NOT NULL PRIMARY KEY,
	inteligencia INTEGER NOT NULL DEFAULT 20,
	oso BOOLEAN NOT NULL DEFAULT FALSE,
	zorro BOOLEAN NOT NULL DEFAULT FALSE,
	aguila BOOLEAN NOT NULL DEFAULT FALSE,
	serpiente BOOLEAN NOT NULL DEFAULT FALSE,
	FOREIGN KEY (id_personaje) REFERENCES personaje(id_personaje) ON DELETE CASCADE ON UPDATE RESTRICT
)ENGINE InnoDB;

CREATE TABLE item(
	id_item INTEGER AUTO_INCREMENT PRIMARY KEY NOT NULL,
	tipo VARCHAR(100) NOT NULL,
	nombre VARCHAR(100) NOT NULL UNIQUE,
    desgaste INTEGER DEFAULT 100
)ENGINE InnoDB;

CREATE TABLE item_guardado(
	item_id INTEGER NOT NULL,
	personaje_id INTEGER NOT NULL,
    cantidad INTEGER NOT NULL DEFAULT 0,
	PRIMARY KEY(item_id, personaje_id),
	FOREIGN KEY (item_id) REFERENCES item(id_item) ON DELETE RESTRICT ON UPDATE RESTRICT,
	FOREIGN KEY (personaje_id) REFERENCES personaje(id_personaje) ON DELETE CASCADE ON UPDATE RESTRICT
)ENGINE InnoDB;

ALTER TABLE partida
	ADD FOREIGN KEY (usuario1_id) REFERENCES usuario(id_usuario) ON DELETE RESTRICT ON UPDATE RESTRICT,
	ADD FOREIGN KEY (usuario2_id) REFERENCES usuario(id_usuario) ON DELETE SET NULL ON UPDATE RESTRICT;
	
ALTER TABLE mensaje
	ADD FOREIGN KEY (usuario_id) REFERENCES usuario(id_usuario) ON DELETE RESTRICT ON UPDATE RESTRICT,
	ADD FOREIGN KEY (partida_id) REFERENCES partida(id_partida) ON DELETE RESTRICT ON UPDATE RESTRICT;
	
ALTER TABLE personaje
	ADD FOREIGN KEY (usuario_id) REFERENCES usuario(id_usuario) ON DELETE RESTRICT ON UPDATE RESTRICT;

INSERT INTO item(tipo, nombre) VALUES('arma', 'arco');
INSERT INTO item(tipo, nombre) VALUES('arma', 'flecha'); 
INSERT INTO item(tipo, nombre) VALUES('arma', 'nunchakus');
INSERT INTO item(tipo, nombre) VALUES('arma', 'espada');
INSERT INTO item(tipo, nombre) VALUES('arma', 'mazo');
INSERT INTO item(tipo, nombre) VALUES('arma', 'daga');
INSERT INTO item(tipo, nombre) VALUES('arma', 'vara');
INSERT INTO item(tipo, nombre, desgaste) VALUES('curacion', 'curacionSimple', NULL);
INSERT INTO item(tipo, nombre, desgaste) VALUES('curacion', 'superCuracion', NULL);
INSERT INTO item(tipo, nombre, desgaste) VALUES('curacion', 'curacionCompleta', NULL);
INSERT INTO item(tipo, nombre, desgaste) VALUES('restaurarEstamina', 'restaurarEstamina', NULL);
INSERT INTO item(tipo, nombre, desgaste) VALUES('restaurarEstamina', 'restaurarMuchaEstamina', NULL);
INSERT INTO item(tipo, nombre, desgaste) VALUES('restaurarEstamina', 'restaurarTodaEstamina', NULL);

