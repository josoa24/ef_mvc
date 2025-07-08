<?php
require_once __DIR__ . '/../db.php';

class Client
{
    public static function getAll()
    {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM ef_pret_db_client");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id)
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM ef_pret_db_client WHERE id_client = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data)
    {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO ef_pret_db_client (nom_client, identifiant, email, telephone) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $data->nom_client,
            $data->identifiant,
            $data->email,
            $data->telephone
        ]);
        return $db->lastInsertId();
    }

    public static function update($id, $data)
    {
        $db = getDB();
        $stmt = $db->prepare("UPDATE ef_pret_db_client SET nom_client = ?, identifiant = ?, email = ?, telephone = ? WHERE id_client = ?");
        $stmt->execute([
            $data->nom_client,
            $data->identifiant,
            $data->email,
            $data->telephone,
            $id
        ]);
    }

    public static function delete($id)
    {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM ef_pret_db_client WHERE id_client = ?");
        $stmt->execute([$id]);
    }

    public static function getLastMonthIncrease()
    {
        $db = getDB();

        $stmt = $db->prepare("
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') AS mois, 
            COUNT(*) AS nb_clients
        FROM ef_pret_db_client
        WHERE created_at >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 2 MONTH), '%Y-%m-01')
        GROUP BY mois
        ORDER BY mois DESC
        LIMIT 2
    ");
        $stmt->execute();
        $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($resultats) < 2) {
           
            return [
                'mois_recent' => $resultats[0]['mois'] ?? null,
                'augmentation_pct' => null,
                'message' => 'Pas assez de données pour calculer l’augmentation.'
            ];
        }

      
        $mois_recent = $resultats[0]['mois'];
        $clients_recent = (int)$resultats[0]['nb_clients'];

        $mois_precedent = $resultats[1]['mois'];
        $clients_precedent = (int)$resultats[1]['nb_clients'];

        if ($clients_precedent == 0) {
            $augmentation_pct = null; 
        } else {
            $augmentation_pct = (($clients_recent - $clients_precedent) / $clients_precedent) * 100;
        }

        return [
            'mois_recent' => $mois_recent,
            'augmentation_pct' => round($augmentation_pct, 2)
        ];
    }

        public static function getCount()
    {
        $db = getDB();
        $stmt = $db->query("SELECT COUNT(*) as total_clients FROM ef_pret_db_client");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total_clients'];
    }

}
