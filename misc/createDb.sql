create database if not exists bourseauxjouets;

use bourseauxjouets;

/*-------- Participant ----------------*/
CREATE TABLE `participant` (
 `PK` bigint(20) NOT NULL AUTO_INCREMENT,
 `NOM` varchar(255) NOT NULL,
 `PRENOM` varchar(255) NOT NULL,
 `ADRESSE` varchar(255) DEFAULT NULL,
 `CP` char(5) DEFAULT NULL,
 `VILLE` varchar(255) DEFAULT NULL,
 `EMAIL` varchar(255) DEFAULT NULL,
 `TEL` varchar(30) DEFAULT NULL,
 `TYPE` enum('VENDEUR','ACHETEUR','REDACTEUR') NOT NULL,
 `REF` bigint(20),
 PRIMARY KEY (`PK`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*--------- Objet ----------------------*/
CREATE TABLE `objet` (
 `PK` bigint(20) NOT NULL AUTO_INCREMENT,
 `DESIGNATION` varchar(1000) NOT NULL,
 `PRIX` decimal(10,2) NOT NULL,
 `DATE_BAJ` date DEFAULT NULL COMMENT 'date de la bourse aux jouets',
 `DATE_DEPOT` datetime DEFAULT NULL,
 `DATE_VENTE` datetime DEFAULT NULL,
 `DATE_RESTITUTION` datetime DEFAULT NULL,
 `vendeur_PK` bigint(20) NOT NULL,
 `acheteur_PK` bigint(20) DEFAULT NULL,
 `redacteurDepot_PK` bigint(20) DEFAULT NULL,
 `redacteurVente_PK` bigint(20) DEFAULT NULL,
 `redacteurRestitution_PK` bigint(20) DEFAULT NULL,
 `id` bigint(20) DEFAULT NULL,
 `letter` varchar(1) NOT NULL,
 `ref` varchar(20),
 PRIMARY KEY (`PK`),
 KEY `vendeur_PK` (`vendeur_PK`),
 KEY `acheteur_PK` (`acheteur_PK`),
 KEY `redacteurDepot_PK` (`redacteurDepot_PK`),
 KEY `redacteurVente_PK` (`redacteurVente_PK`),
 KEY `redacteurRestitution_PK` (`redacteurRestitution_PK`),
 CONSTRAINT `acheteur_PK` FOREIGN KEY (`acheteur_PK`) REFERENCES `participant` (`PK`) ON DELETE CASCADE ON UPDATE CASCADE,
 CONSTRAINT `redacteurDepot_PK` FOREIGN KEY (`redacteurDepot_PK`) REFERENCES `participant` (`PK`) ON DELETE CASCADE ON UPDATE CASCADE,
 CONSTRAINT `redacteurRestitution_PK` FOREIGN KEY (`redacteurRestitution_PK`) REFERENCES `participant` (`PK`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*-------- User ----------------*/
CREATE TABLE `user` (
 `PK` bigint(20) NOT NULL AUTO_INCREMENT,
 `login` varchar(255) NOT NULL,
 `password` varchar(255) NOT NULL,
 `isAdmin` boolean DEFAULT false,
 `isDepot` boolean DEFAULT true,
 `isVente` boolean DEFAULT true,
 `isRestitution` boolean DEFAULT false,
 `participant_PK` bigint(20) DEFAULT NULL,
 PRIMARY KEY (`PK`),
 KEY `participant_PK` (`participant_PK`),
CONSTRAINT `participant_PK` FOREIGN KEY (`participant_PK`) REFERENCES `participant` (`PK`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE bill(
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
    `type` varchar(15) NOT NULL,
    `cash` int NOT NULL,
    `check` int NOT NULL,
    `creditCard` int NOT NULL,
    `total` int NOT NULL,
    `active` bool DEFAULT TRUE,
    `new_id` bigint(20),
    `date` date NOT NULL,
    `customer_pk` bigint(20) NOT NULL,
    `redactor_pk` bigint(20) NOT NULL,
    `tax` int NOT NULL,
    `letter` varchar(1) NOT NULL,
    `observations` varchar(4000) DEFAULT NULL,
    primary key (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE bill_objects(
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
    `bill_id` bigint(20) NOT NULL,
    `object_id` bigint(20) NOT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `bill_PK` FOREIGN KEY (`bill_id`) REFERENCES `bill` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `object_PK` FOREIGN KEY (`object_id`) REFERENCES `objet` (`PK`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE pc(
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
    `ip` varchar(64) NOT NULL,
    `letter` varchar(1) NOT NULL,
    `counter` bigint(20) NOT NULL DEFAULT 1,
    primary key(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE server_vars(
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
    `key` varchar(256) NOT NULL,
    `value` varchar(256) NOT NULL,
    primary key(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;