CREATE DATABASE IF NOT EXISTS ef_pret_db USE ef_pret_db;

CREATE TABLE ef_pret_db_etablissement_financier (
    id_etablissement_financier INT AUTO_INCREMENT PRIMARY KEY,
    nom_etablissement_financier VARCHAR(100) NOT NULL,
    fonds_de_base DECIMAL(15, 2) DEFAULT 0.00
);

CREATE TABLE ef_pret_db_client (
    id_client INT AUTO_INCREMENT PRIMARY KEY,
    nom_client VARCHAR(100) NOT NULL,
    identifiant VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100),
    telephone VARCHAR(20)
);

CREATE TABLE ef_pret_db_ajout_fonds (
    id_ajout_fonds INT AUTO_INCREMENT PRIMARY KEY,
    ef_id INT NOT NULL,
    client_id INT NOT NULL,
    montant DECIMAL(15, 2) NOT NULL,
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ef_id) REFERENCES etablissement_financier (id),
    FOREIGN KEY (client_id) REFERENCES client (id)
);

CREATE TABLE ef_pret_db_type_pret (
    id_type_pret INT AUTO_INCREMENT PRIMARY KEY,
    nom_type_pret VARCHAR(100) NOT NULL
);

CREATE TABLE ef_pret_db_taux_pret (
    id_taux_pret INT AUTO_INCREMENT PRIMARY KEY,
    type_pret_id INT NOT NULL,
    taux DECIMAL(5, 2) NOT NULL,
    min_mois INT NOT NULL,
    max_mois INT NOT NULL,
    FOREIGN KEY (type_pret_id) REFERENCES type_pret (id)
);

CREATE TABLE ef_pret_db_statut_pret (
    id_statut_pret INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE ef_pret_db_pret (
    id_pret INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    taux_pret_id INT NOT NULL,
    montant DECIMAL(15, 2) NOT NULL,
    duree_mois INT NOT NULL,
    date_pret DATE DEFAULT CURRENT_DATE,
    statut_id INT NOT NULL,
    FOREIGN KEY (client_id) REFERENCES client (id),
    FOREIGN KEY (taux_pret_id) REFERENCES taux_pret (id),
    FOREIGN KEY (statut_id) REFERENCES statut_pret (id)
);