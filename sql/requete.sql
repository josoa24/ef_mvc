            
            SELECT t.id_taux_pret, t.taux
            FROM ef_pret_db_taux_pret t
            JOIN ef_pret_db_type_pret tp ON tp.id_type_pret = t.id_type_pret
            WHERE tp.id_type_pret = 1
            AND 12 BETWEEN t.min_mois AND t.max_mois
            LIMIT 1;