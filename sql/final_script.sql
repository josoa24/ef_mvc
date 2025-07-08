
DROP TABLE IF EXISTS ef_pret_db_remboursement;
DROP TABLE IF EXISTS ef_pret_db_pret;
DROP TABLE IF EXISTS ef_pret_db_statut_pret;
DROP TABLE IF EXISTS ef_pret_db_taux_pret;
DROP TABLE IF EXISTS ef_pret_db_type_pret;
DROP TABLE IF EXISTS ef_pret_db_ajout_fonds;
DROP TABLE IF EXISTS ef_pret_db_client;
DROP TABLE IF EXISTS ef_pret_db_etablissement_financier;
DROP TABLE IF EXISTS ef_pret_db_admin;

CREATE TABLE ef_pret_db_admin (
    id_admin INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(100) NOT NULL,
    identifiant_admin VARCHAR(50) UNIQUE NOT NULL
);


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
    telephone VARCHAR(20),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE ef_pret_db_ajout_fonds (
    id_ajout_fonds INT AUTO_INCREMENT PRIMARY KEY,
    id_etablissement_financier INT NOT NULL,
    id_client INT NOT NULL,
    montant DECIMAL(15, 2) NOT NULL,
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_etablissement_financier) REFERENCES ef_pret_db_etablissement_financier (id_etablissement_financier),
    FOREIGN KEY (id_client) REFERENCES ef_pret_db_client (id_client)
);

CREATE TABLE ef_pret_db_type_pret (
    id_type_pret INT AUTO_INCREMENT PRIMARY KEY,
    nom_type_pret VARCHAR(100) NOT NULL
);

CREATE TABLE ef_pret_db_taux_pret (
    id_taux_pret INT AUTO_INCREMENT PRIMARY KEY,
    id_type_pret INT NOT NULL,
    taux DECIMAL(5, 2) NOT NULL,
    min_mois INT NOT NULL,
    max_mois INT NOT NULL,
    FOREIGN KEY (id_type_pret) REFERENCES ef_pret_db_type_pret (id_type_pret)
);

CREATE TABLE ef_pret_db_statut_pret (
    id_statut_pret INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL UNIQUE
);


CREATE TABLE ef_pret_db_pret (
    id_pret INT AUTO_INCREMENT PRIMARY KEY,
    id_client INT NOT NULL,
    id_taux_pret INT NOT NULL,
    montant DECIMAL(15, 2) NOT NULL,
    duree_mois INT NOT NULL,
    date_pret DATE,
    id_statut_pret INT NOT NULL,
    FOREIGN KEY (id_client) REFERENCES ef_pret_db_client (id_client),
    FOREIGN KEY (id_taux_pret) REFERENCES ef_pret_db_taux_pret (id_taux_pret),
    FOREIGN KEY (id_statut_pret) REFERENCES ef_pret_db_statut_pret (id_statut_pret)
);


CREATE TABLE ef_pret_db_remboursement (
    id_remboursement INT AUTO_INCREMENT PRIMARY KEY,
    id_pret INT NOT NULL,
    date_paiement DATE NOT NULL,
    mois INT NOT NULL,
    annee INT NOT NULL,
    FOREIGN KEY (id_pret) REFERENCES ef_pret_db_pret (id_pret)
);
