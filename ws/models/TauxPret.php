<?php
require_once __DIR__ . '/../db.php';

class TauxPret {

    private $db;

    public function __construct() {
        $this->db = getDB(); 
    }

    public function getAllTaux() {
        $sql = "
            SELECT t.nom_type_pret, p.taux, p.min_mois, p.max_mois
            FROM ef_pret_db_type_pret t
            JOIN ef_pret_db_taux_pret p ON t.id_type_pret = p.id_type_pret
            ORDER BY t.nom_type_pret, p.min_mois
        ";
        $stmt = $this->db->query($sql);
        $taux = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $grouped = [];
        foreach ($taux as $row) {
            $type = $row['nom_type_pret'];
            if (!isset($grouped[$type])) {
                $grouped[$type] = [];
            }
            $grouped[$type][] = $row;
        }
    
        $output = [];
        foreach ($grouped as $type => $intervalles) {
            $inf = $intervalles[0];
            $sup = end($intervalles);
    
            $interv = array_slice($intervalles, 1, -1);
            $output[] = [
                "type" => $type,
                "inf_mois" => $inf['max_mois'],
                "inf_taux" => $inf['taux'],
                "sup_mois" => $sup['min_mois'] - 1,
                "sup_taux" => $sup['taux'],
                "intervalles" => array_map(function ($x) {
                    return $x['min_mois'] . "-" . $x['max_mois'] . ": " . $x['taux'] . "%";
                }, $interv)
            ];
        }
    
        return $output;
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
