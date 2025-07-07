<?php
require_once __DIR__ . '/../db.php';

class Remboursement
{
    public static function getClientsAvecPret()
    {
        $db = getDB();
        $stmt = $db->query("SELECT
            p.id_pret,
            c.id_client,
            c.nom_client
        FROM ef_pret_db_pret p
        INNER JOIN ef_pret_db_client c ON p.client_id = c.id_client
        WHERE p.statut_id IN (
            SELECT id_statut_pret FROM ef_pret_db_statut_pret WHERE libelle = 'en cours'
        );)
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function enregistrerRemboursement($pret_id, $date_paiement) {
    $db = getDB();
    
    // Debug: Vérification de la connexion
    if(!$db) {
        throw new PDOException("Aucune connexion à la base de données");
    }
    
    $sql = "INSERT INTO ef_pret_db_remboursement (pret_id, date_paiement) VALUES (?, ?)";
    error_log("[SQL] Requête préparée: $sql");
    error_log("[SQL] Paramètres: [$pret_id, $date_paiement]");
    
    $stmt = $db->prepare($sql);
    if(!$stmt) {
        $error = $db->errorInfo();
        throw new PDOException("Erreur de préparation: " . ($error[2] ?? "Unknown error"));
    }
    
    $success = $stmt->execute([$pret_id, $date_paiement]);
    if(!$success) {
        $error = $stmt->errorInfo();
        throw new PDOException("Erreur d'exécution: " . ($error[2] ?? "Unknown error"));
    }
    
    return $db->lastInsertId();
}
}
