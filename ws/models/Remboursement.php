<?php
require_once __DIR__ . '/../db.php';

class Remboursement
{
    public static function getClientsAvecPret()
    {
        $db = getDB();
        $stmt = $db->query("
            SELECT
                p.id_pret,
                c.id_client,
                c.nom_client
            FROM ef_pret_db_pret p
            INNER JOIN ef_pret_db_client c ON p.client_id = c.id_client
            WHERE p.statut_id IN (
                SELECT id_statut_pret FROM ef_pret_db_statut_pret WHERE libelle = 'en cours'
            )
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function enregistrerRemboursement($pret_id, $date_paiement)
    {
        $db = getDB();
        $stmt = $db->prepare("
            INSERT INTO ef_pret_db_remboursement (pret_id, date_paiement)
            VALUES (?, ?)
        ");
        $stmt->execute([$pret_id, $date_paiement]);
        return $db->lastInsertId();
    }
}
