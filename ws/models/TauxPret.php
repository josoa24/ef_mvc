<?php
require_once __DIR__ . '/../db.php';

class TauxPret {

    private $db;

    public function __construct() {
        $this->db = getDB(); 
    }

    public function ajouter_pret_et_taux($type, $infMois, $infTaux, $mins, $maxs, $tauxs, $supMois, $supTaux) {
        $this->db->beginTransaction();

        $stmt = $this->db->prepare("INSERT INTO ef_pret_db_type_pret(nom_type_pret) VALUES (?)");
        $stmt->execute([$type]);
        $id_type = $this->db->lastInsertId();

        $stmt = $this->db->prepare("INSERT INTO ef_pret_db_taux_pret(id_type_pret, taux, min_mois, max_mois) VALUES (?, ?, ?, ?)");
        $stmt->execute([$id_type, $infTaux, 0, $infMois]);

        for ($i = 0; $i < count($mins); $i++) {
            $stmt->execute([$id_type, $tauxs[$i], $mins[$i], $maxs[$i]]);
        }

        $stmt->execute([$id_type, $supTaux, $supMois + 1, 1000]);

        $this->db->commit();
    }
}
