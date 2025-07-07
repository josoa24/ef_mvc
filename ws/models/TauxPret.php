<?php
require_once __DIR__ . '/../db.php';

class TauxPret {
    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM ef_pret_db_taux_pret WHERE id_taux_pret = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
