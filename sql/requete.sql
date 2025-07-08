            
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
