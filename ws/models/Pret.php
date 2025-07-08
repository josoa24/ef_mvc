<?php
require_once __DIR__ . '/../db.php';


class Pret
{
    public static function getAll()
    {
        $db = getDB();
        $sql = "
        SELECT 
            p.id_pret,
            p.id_client,
            c.nom_client,
            p.id_taux_pret,
            t.taux,
            p.montant,
            p.duree_mois,
            p.date_pret,
            p.id_statut_pret,
            s.libelle AS statut
        FROM ef_pret_db_pret p
        JOIN ef_pret_db_client c ON p.id_client = c.id_client
        JOIN ef_pret_db_taux_pret t ON p.id_taux_pret = t.id_taux_pret
        JOIN ef_pret_db_statut_pret s ON p.id_statut_pret = s.id_statut_pret
        ORDER BY p.date_pret DESC
    ";

        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id)
    {
        $db = getDB();
        $sql = "
            SELECT p.*, c.nom_client, t.taux, s.libelle AS statut
            FROM ef_pret_db_pret p
            JOIN ef_pret_db_client c ON p.id_client = c.id_client
            JOIN ef_pret_db_taux_pret t ON p.id_taux_pret = t.id_taux_pret
            JOIN ef_pret_db_statut_pret s ON p.id_statut_pret = s.id_statut_pret
            WHERE p.id_pret = ?
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public static function getAllWithClient()
    {
        $db = getDB();
        $stmt = $db->query("SELECT p.id_pret, p.montant, c.nom_client 
                        FROM ef_pret_db_pret p 
                        JOIN ef_pret_db_client c ON p.client_id = c.id_client
                        WHERE p.statut_id = 1"); // par ex: prêts "en cours"
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function create($data)
    {
        $db = getDB();
        $stmt = $db->prepare("
            INSERT INTO ef_pret_db_pret (id_client, id_taux_pret, montant, duree_mois, date_pret, id_statut_pret)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data->id_client,
            $data->id_taux_pret,
            $data->montant,
            $data->duree_mois,
            $data->date_pret ?? date('Y-m-d'),
            $data->id_statut_pret,
        ]);
        return $db->lastInsertId();
    }

    public static function update($id, $data)
    {
        $db = getDB();
        $stmt = $db->prepare("
            UPDATE ef_pret_db_pret
            SET id_client = ?, id_taux_pret = ?, montant = ?, duree_mois = ?, date_pret = ?, id_statut_pret = ?
            WHERE id_pret = ?
        ");
        $stmt->execute([
            $data->id_client,
            $data->id_taux_pret,
            $data->montant,
            $data->duree_mois,
            $data->date_pret,
            $data->id_statut_pret,
            $id,
        ]);
    }

    public static function delete($id)
    {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM ef_pret_db_pret WHERE id_pret = ?");
        $stmt->execute([$id]);
    }
    public function calculerRemboursement($id_client, $id_type_pret, $montant, $mois)
    {

        $db = getDB();
        $stmt = $db->prepare("
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
        $totalInteret = ($montant * $taux) / 100;
        $mensualite = ($montant + $totalInteret) / $mois;

        return [
            "taux" => $taux,
            "mensualite" => $mensualite
        ];
    }
}
