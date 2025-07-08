<?php
require_once __DIR__ . '/../db.php';

class Montant {
    public static function getStatsParMois($start = null, $end = null) {
        $db = getDB();

        $sql = "
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
            WHERE 1
        ";

        $params = [];

        if ($start && $end) {
            $sql .= " AND STR_TO_DATE(CONCAT(y.annee, '-', LPAD(y.mois, 2, '0'), '-01'), '%Y-%m-%d') BETWEEN :start AND :end";
            $params['start'] = $start;
            $params['end'] = $end;
        }

        $sql .= " ORDER BY y.annee, y.mois";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calcul du reste disponible (cumulatif)
        $cumule_verse = 0;
        $cumule_emprunte = 0;
        $cumule_rembourse = 0;

        foreach ($results as &$row) {
            $cumule_verse += $row['montant_verse'];
            $cumule_emprunte += $row['montant_emprunte'];
            $cumule_rembourse += $row['montant_rembourse'];
            $row['reste_disponible'] = $cumule_verse - $cumule_emprunte + $cumule_rembourse;
        }

        return $results;
    }
}
