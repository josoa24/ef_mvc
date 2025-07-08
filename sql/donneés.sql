USE ef_pret_db;

INSERT INTO ef_pret_db_admin (user_name, identifiant_admin) VALUES
  ('Super Admin', 'ADMIN-001');

INSERT INTO ef_pret_db_etablissement_financier (nom_etablissement_financier, fonds_de_base) VALUES
  ('Banque ABC', 1000000.00);

INSERT INTO ef_pret_db_client (nom_client, identifiant, email, telephone) VALUES
  ('Jean', '123', 'jean@example.com', '+261341234567'),
  ('Sarah', 'CLI-002', 'sarah@example.com', '+261349876543');

INSERT INTO ef_pret_db_type_pret (nom_type_pret) VALUES
  ('Prêt personnel'),
  ('Prêt auto');

INSERT INTO ef_pret_db_taux_pret (type_pret_id, taux, min_mois, max_mois) VALUES
  (1, 10.00, 3, 12),
  (2, 8.50, 12, 60);

INSERT INTO ef_pret_db_statut_pret (libelle) VALUES
  ('en cours'),
  ('termine'),
  ('annule');

/* 7. Prêt de test : 10 000 Ar sur 5 mois, taux 10% */
INSERT INTO ef_pret_db_pret
  (client_id, taux_pret_id, montant, duree_mois, date_pret, statut_id)
VALUES
  (1, 1, 10000.00, 5, '2025-07-01', 1);

