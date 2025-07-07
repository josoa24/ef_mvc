CREATE DATABASE IF NOT EXISTS ef_pret_db;
USE ef_pret_db;

-- Table admin
CREATE TABLE ef_pret_db_admin (
    id_admin INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(100) NOT NULL,
    identifiant_admin VARCHAR(50) UNIQUE NOT NULL
);

-- Table établissement financier
CREATE TABLE ef_pret_db_etablissement_financier (
    id_etablissement_financier INT AUTO_INCREMENT PRIMARY KEY,
    nom_etablissement_financier VARCHAR(100) NOT NULL,
    fonds_de_base DECIMAL(15, 2) DEFAULT 0.00
);

-- Table client
CREATE TABLE ef_pret_db_client (
    id_client INT AUTO_INCREMENT PRIMARY KEY,
    nom_client VARCHAR(100) NOT NULL,
    identifiant VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100),
    telephone VARCHAR(20)
);

-- Table ajout fonds
CREATE TABLE ef_pret_db_ajout_fonds (
    id_ajout_fonds INT AUTO_INCREMENT PRIMARY KEY,
    ef_id INT NOT NULL,
    client_id INT NOT NULL,
    montant DECIMAL(15, 2) NOT NULL,
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ef_id) REFERENCES ef_pret_db_etablissement_financier(id_etablissement_financier),
    FOREIGN KEY (client_id) REFERENCES ef_pret_db_client(id_client)
);

-- Table type prêt
CREATE TABLE ef_pret_db_type_pret (
    id_type_pret INT AUTO_INCREMENT PRIMARY KEY,
    nom_type_pret VARCHAR(100) NOT NULL
);

-- Table taux prêt
CREATE TABLE ef_pret_db_taux_pret (
    id_taux_pret INT AUTO_INCREMENT PRIMARY KEY,
    type_pret_id INT NOT NULL,
    taux DECIMAL(5, 2) NOT NULL,
    min_mois INT NOT NULL,
    max_mois INT NOT NULL,
    FOREIGN KEY (type_pret_id) REFERENCES ef_pret_db_type_pret(id_type_pret)
);

-- Table statut prêt
CREATE TABLE ef_pret_db_statut_pret (
    id_statut_pret INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL UNIQUE
);

-- Table prêt
CREATE TABLE ef_pret_db_pret (
    id_pret INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    taux_pret_id INT NOT NULL,
    montant DECIMAL(15, 2) NOT NULL,
    duree_mois INT NOT NULL,
    date_pret DATE DEFAULT CURRENT_DATE,
    statut_id INT NOT NULL,
    FOREIGN KEY (client_id) REFERENCES ef_pret_db_client(id_client),
    FOREIGN KEY (taux_pret_id) REFERENCES ef_pret_db_taux_pret(id_taux_pret),
    FOREIGN KEY (statut_id) REFERENCES ef_pret_db_statut_pret(id_statut_pret)
);

-- Table remboursement
CREATE TABLE ef_pret_db_remboursement (
    id_remboursement INT AUTO_INCREMENT PRIMARY KEY,
    pret_id INT NOT NULL,
    date_paiement DATE NOT NULL,
    FOREIGN KEY (pret_id) REFERENCES ef_pret_db_pret(id_pret)
);

-- Testez avec les valeurs exactes que vous essayez d'insérer
INSERT INTO ef_pret_db_remboursement (pret_id, date_paiement) 
VALUES (1, '04/07/2025');

-- Vérifiez ensuite
SELECT * FROM ef_pret_db_remboursement;