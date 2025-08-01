            
            SELECT t.id_taux_pret, t.taux
            FROM ef_pret_db_taux_pret t
            JOIN ef_pret_db_type_pret tp ON tp.id_type_pret = t.id_type_pret
            WHERE tp.id_type_pret = 1
            AND 12 BETWEEN t.min_mois AND t.max_mois
            LIMIT 1;

            SELECT 
                YEAR(r.date_paiement) AS annee,
                MONTH(r.date_paiement) AS mois,
                ROUND(SUM(p.montant * (t.taux/100/12)), 2) AS interets_mensuels
            FROM 
                ef_pret_db_pret p
            JOIN 
                ef_pret_db_taux_pret t ON p.id_taux_pret = t.id_taux_pret
            JOIN 
                ef_pret_db_remboursement r ON p.id_pret = r.id_pret
            GROUP BY 
                YEAR(r.date_paiement), MONTH(r.date_paiement)
            ORDER BY 
                annee, mois
            ;



SELECT 
    r.mois,
    r.annee,
    SUM(p.montant * (tp.taux / 100)) AS interet_total
FROM ef_pret_db_remboursement r
JOIN ef_pret_db_pret p ON r.id_pret = p.id_pret
JOIN ef_pret_db_taux_pret tp ON p.id_taux_pret = tp.id_taux_pret
WHERE 1
GROUP BY r.annee, r.mois
ORDER BY r.annee, r.mois;


SELECT
                y.mois,
                y.annee,
                IFNULL(f.montant_verse, 0) AS montant_verse,
                IFNULL(p.montant_emprunte, 0) AS montant_emprunte,
                IFNULL(r.montant_rembourse, 0) AS montant_rembourse
            FROM (
                SELECT mois, annee FROM (
                    SELECT MONTH(date_ajout) AS mois, YEAR(date_ajout) AS annee FROM ef_pret_db_ajout_fonds
                    UNION
                    SELECT MONTH(date_pret), YEAR(date_pret) FROM ef_pret_db_pret
                    UNION
                    SELECT mois, annee FROM ef_pret_db_remboursement
                ) AS dates
                GROUP BY mois, annee
            ) y
            LEFT JOIN (
                SELECT MONTH(date_ajout) AS mois, YEAR(date_ajout) AS annee, SUM(montant) AS montant_verse
                FROM ef_pret_db_ajout_fonds
                GROUP BY annee, mois
            ) f ON f.mois = y.mois AND f.annee = y.annee
            LEFT JOIN (
                SELECT MONTH(date_pret) AS mois, YEAR(date_pret) AS annee, SUM(montant) AS montant_emprunte
                FROM ef_pret_db_pret
                GROUP BY annee, mois
            ) p ON p.mois = y.mois AND p.annee = y.annee
            LEFT JOIN (
                SELECT r.mois, r.annee, SUM(p.montant * tp.taux / 100) AS montant_rembourse
                FROM ef_pret_db_remboursement r
                JOIN ef_pret_db_pret p ON p.id_pret = r.id_pret
                JOIN ef_pret_db_taux_pret tp ON tp.id_taux_pret = p.id_taux_pret
                GROUP BY r.annee, r.mois
            ) r ON r.mois = y.mois AND r.annee = y.annee
            WHERE 1;