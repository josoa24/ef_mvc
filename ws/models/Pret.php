<?php
require_once __DIR__ . '/../db.php';

class Pret {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function calculerRemboursement($id_client, $id_type_pret, $montant, $mois) {
        // Récupération du taux correspondant
        $stmt = $this->db->prepare("
            SELECT t.id_taux_pret, t.taux
            FROM ef_pret_db_taux_pret t
            JOIN ef_pret_db_type_pret tp ON tp.id_type_pret = t.id_type_pret
            WHERE tp.id_type_pret = ?
            AND ? BETWEEN t.min_mois AND t.max_mois
            LIMIT 1
        ");
        $stmt->execute([$id_type_pret, $mois]);
        $row = $stmt->fetch();

        if (!$row) throw new Exception("Aucun taux trouvé pour cette durée et ce type de prêt");

        $taux = $row['taux'];

        // Mensualité calculée : formule d’annuité constante
        // $r = $taux / 100 / 12;
        // $mensualite = ($montant * $r) / (1 - pow(1 + $r, -$mois));
        // $mensualite = round($mensualite, 2);
        $totalInteret = ($montant * $taux) / 100;
        $mensualite = ($montant + $totalInteret) / $mois;

        return [
            "taux" => $taux,
            "mensualite" => $mensualite
        ];
    }
}
