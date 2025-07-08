
INSERT INTO ef_pret_db_admin (user_name, identifiant_admin) VALUES
('Admin Principal', 'ADMIN-001');

INSERT INTO ef_pret_db_etablissement_financier (nom_etablissement_financier, fonds_de_base) VALUES
('MicroFinance Mada', 100000000.00),
('Banque Solidaire', 50000000.00);

INSERT INTO ef_pret_db_client (nom_client, identifiant, email, telephone) VALUES
('Rasolofoniaina Jean', 'CLI-001', 'jean.rasolo@example.com', '0321234567'),
('Randriamampionona Sarah', 'CLI-002', 'sarah.rand@example.com', '0349876543'),
('Rakoto Andry', 'CLI-003', 'andry.rakoto@example.com', '0337654321');

INSERT INTO ef_pret_db_ajout_fonds (id_etablissement_financier, id_client, montant) VALUES
(1, 1, 150000.00),
(1, 2, 200000.00),
(2, 3, 100000.00);

INSERT INTO ef_pret_db_statut_pret (libelle) VALUES
('en cours'),
('remboursé'),
('en retard');

INSERT INTO ef_pret_db_type_pret (nom_type_pret) VALUES
('Crédit consommation'),
('Crédit logement');

INSERT INTO ef_pret_db_taux_pret (id_type_pret, taux, min_mois, max_mois) VALUES
(1, 5.00, 0, 6),
(1, 6.50, 7, 12),
(1, 7.00, 13, 24),
(2, 4.00, 0, 12),
(2, 5.50, 13, 36);

INSERT INTO ef_pret_db_pret (id_client, id_taux_pret, montant, duree_mois, id_statut_pret) VALUES
(1, 1, 500000.00, 6, 1), -- Jean, taux 5%, en cours
(2, 2, 800000.00, 12, 1), -- Sarah, taux 6.5%, en cours
(3, 3, 1000000.00, 24, 2); -- Andry, taux 7%, remboursé

INSERT INTO ef_pret_db_remboursement (id_pret, date_paiement, mois, annee) VALUES
(1, '2025-06-10', 6, 2025),
(1, '2025-07-05', 7, 2025),
(2, '2025-07-08', 7, 2025);
