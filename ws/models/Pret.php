<?php
require_once __DIR__ . '/../db.php';

class Pret
{
    public static function getAll()
    {
        $db = getDB();
        $sql = "
            SELECT p.*, c.nom_client, t.taux, s.libelle AS statut
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
}
