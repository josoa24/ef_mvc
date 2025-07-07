<?php
require_once __DIR__ . '/../db.php';

class AjoutFonds
{
    public static function getAll()
    {
        $db = getDB();
        $sql = "
        SELECT 
            af.id_ajout_fonds,
            af.id_etablissement_financier,
            ef.nom_etablissement_financier,
            af.id_client,
            c.nom_client,
            af.montant,
            af.date_ajout
        FROM ef_pret_db_ajout_fonds af
        INNER JOIN ef_pret_db_etablissement_financier ef
            ON af.id_etablissement_financier = ef.id_etablissement_financier
        INNER JOIN ef_pret_db_client c
            ON af.id_client = c.id_client
        ORDER BY af.date_ajout DESC
    ";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function getById($id)
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM ef_pret_db_ajout_fonds WHERE id_ajout_fonds = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data)
    {
        $db = getDB();
        $stmt = $db->prepare("
            INSERT INTO ef_pret_db_ajout_fonds (id_etablissement_financier, id_client, montant, date_ajout)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $data->id_etablissement_financier,
            $data->id_client,
            $data->montant,
            $data->date_ajout
        ]);
        return $db->lastInsertId();
    }

    public static function update($id, $data)
    {
        $db = getDB();
        $stmt = $db->prepare("
            UPDATE ef_pret_db_ajout_fonds
            SET id_etablissement_financier = ?, id_client = ?, montant = ?, date_ajout = ?
            WHERE id_ajout_fonds = ?
        ");
        $stmt->execute([
            $data->id_etablissement_financier,
            $data->id_client,
            $data->montant,
            $data->date_ajout,
            $id
        ]);
    }

    public static function delete($id)
    {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM ef_pret_db_ajout_fonds WHERE id_ajout_fonds = ?");
        $stmt->execute([$id]);
    }
}
