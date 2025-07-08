<?php
require_once __DIR__ . '/../db.php';

class InteretGraphique {
    public static function getInterets($start = null, $end = null) {
        $db = getDB();

        $sql = "
            SELECT 
                r.mois,
                r.annee,
                SUM(p.montant * (tp.taux / 100)) AS interet_total
            FROM ef_pret_db_remboursement r
            JOIN ef_pret_db_pret p ON r.id_pret = p.id_pret
            JOIN ef_pret_db_taux_pret tp ON p.id_taux_pret = tp.id_taux_pret
            WHERE 1
        ";

        $params = [];

        if ($start && $end) {
            $sql .= " AND CONCAT(r.annee, '-', LPAD(r.mois, 2, '0')) BETWEEN :start AND :end";
            $params['start'] = $start;
            $params['end'] = $end;
        }

        $sql .= " GROUP BY r.annee, r.mois ORDER BY r.annee, r.mois";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
