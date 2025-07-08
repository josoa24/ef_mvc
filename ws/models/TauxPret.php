<?php
require_once __DIR__ . '/../db.php';

class TauxPret
{

    public static function getById($id)
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM ef_pret_db_taux_pret WHERE id_taux_pret = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    private $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getAllTaux()
    {
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


    public function ajouter_pret_et_taux($type, $infMois, $infTaux, $mins, $maxs, $tauxs, $supMois, $supTaux)
    {
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

    public function getAllWidthType()
    {
        $sql = "
            SELECT t.id_type_pret, t.nom_type_pret, p.id_taux_pret, p.taux, p.min_mois, p.max_mois
            FROM ef_pret_db_type_pret t
            JOIN ef_pret_db_taux_pret p ON t.id_type_pret = p.id_type_pret
            ORDER BY t.nom_type_pret, p.min_mois
        ";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTypePretByIdTauxPret($id_taux_pret)
    {
        $db = getDB();
        $sql = "
        SELECT tp.id_type_pret, tp.nom_type_pret, t.taux, t.min_mois, t.max_mois
        FROM ef_pret_db_taux_pret t
        JOIN ef_pret_db_type_pret tp ON t.id_type_pret = tp.id_type_pret
        WHERE t.id_taux_pret = :id_taux_pret
        LIMIT 1
    ";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(":id_taux_pret", $id_taux_pret, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTauxByDuree($id_type_pret, $duree)
    {
        $db = getDB();
        $sql = "
        SELECT *
        FROM ef_pret_db_taux_pret
        WHERE id_type_pret = :id_type_pret
          AND :duree BETWEEN min_mois AND max_mois
        LIMIT 1
    ";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_type_pret', $id_type_pret, PDO::PARAM_INT);
        $stmt->bindParam(':duree', $duree, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
