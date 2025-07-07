<?php
require_once __DIR__ . '/../db.php';

class EtablissementFinancier
{
    public static function getAll()
    {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM ef_pret_db_etablissement_financier");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id)
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM ef_pret_db_etablissement_financier WHERE id_etablissement_financier = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data)
    {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO ef_pret_db_etablissement_financier (nom_etablissement_financier, fonds_de_base) VALUES (?, ?)");
        $stmt->execute([$data->nom_etablissement_financier, $data->fonds_de_base]);
        return $db->lastInsertId();
    }

    public static function update($id, $data)
    {
        $db = getDB();
        $stmt = $db->prepare("UPDATE ef_pret_db_etablissement_financier SET nom_etablissement_financier = ?, fonds_de_base = ? WHERE id_etablissement_financier = ?");
        $stmt->execute([$data->nom_etablissement_financier, $data->fonds_de_base, $id]);
    }

    public static function delete($id)
    {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM ef_pret_db_etablissement_financier WHERE id_etablissement_financier = ?");
        $stmt->execute([$id]);
    }
}
