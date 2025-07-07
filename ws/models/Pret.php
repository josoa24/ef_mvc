<?php
require_once __DIR__ . '/../db.php';

class Pret {
    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM ef_pret_db_pret WHERE id_pret = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getAllWithClient() {
    $db = getDB();
    $stmt = $db->query("SELECT p.id_pret, p.montant, c.nom_client 
                        FROM ef_pret_db_pret p 
                        JOIN ef_pret_db_client c ON p.client_id = c.id_client
                        WHERE p.statut_id = 1"); // par ex: prêts "en cours"
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
