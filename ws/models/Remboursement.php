<?php
require_once __DIR__ . '/../db.php';

class Remboursement {

    private $db;

    public function __construct() {
        $this->db = getDB();
    }

public function insertRemboursement($id_pret, $date_paiement, $mois, $annee) {
    $stmt = $this->db->prepare("SELECT COUNT(*) FROM ef_pret_db_remboursement WHERE id_pret = ? AND mois = ? AND annee = ?");
    $stmt->execute([$id_pret, $mois, $annee]);
    $existe = $stmt->fetchColumn();

    if ($existe > 0) {
        throw new Exception("Un remboursement pour ce mois et cette année a déjà été effectué.");
    }

    $stmt = $this->db->prepare("SELECT COUNT(*) AS total_remb, duree_mois FROM ef_pret_db_pret p
                                LEFT JOIN ef_pret_db_remboursement r ON p.id_pret = r.id_pret
                                WHERE p.id_pret = ?
                                GROUP BY p.duree_mois");
    $stmt->execute([$id_pret]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && $row['total_remb'] >= $row['duree_mois']) {
        throw new Exception("Tous les remboursements ont déjà été effectués pour ce prêt.");
    }

    $stmt = $this->db->prepare("INSERT INTO ef_pret_db_remboursement (id_pret, date_paiement, mois, annee) 
                                VALUES (?, ?, ?, ?)");
    return $stmt->execute([$id_pret, $date_paiement, $mois, $annee]);
}


    public function getPretsEnCours() {
        $sql = "SELECT p.id_pret, c.nom_client
                FROM ef_pret_db_pret p
                JOIN ef_pret_db_client c ON p.id_client = c.id_client
                JOIN ef_pret_db_statut_pret s ON p.id_statut_pret = s.id_statut_pret
                WHERE s.libelle = 'en cours'";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
