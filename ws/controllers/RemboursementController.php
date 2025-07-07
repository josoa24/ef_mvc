<?php
require_once __DIR__ . '/../models/Remboursement.php';
require_once __DIR__ . '/../helpers/Utils.php';

class RemboursementController
{

    // Obtenir la liste des clients ayant des prêts en cours
    public static function getClientsAvecPret()
    {
        $clients = Remboursement::getClientsAvecPret();
        Flight::json($clients);
    }

    public static function create()
    {
        // Debug: Log toutes les données reçues
        error_log("[DEBUG] Données POST reçues: " . print_r($_POST, true));

        $pret_id = $_POST['pret_id'] ?? null;
        $date_input = $_POST['date_paiement'] ?? null;

        // Debug: Log des valeurs brutes
        error_log("[DEBUG] pret_id: $pret_id | date_input: $date_input");

        // Validation des champs
        if (!$pret_id || !$date_input) {
            $error = 'Champs manquants (pret_id: ' . ($pret_id ? 'ok' : 'manquant') . ', date_paiement: ' . ($date_input ? 'ok' : 'manquant') . ')';
            error_log("[ERROR] $error");
            Flight::json(['error' => $error], 400);
            return;
        }

        // Conversion de la date
        $date_obj = DateTime::createFromFormat('d/m/Y', $date_input);
        if (!$date_obj) {
            $error = "Format de date invalide ($date_input). Essayez JJ/MM/AAAA";
            error_log("[ERROR] $error");
            Flight::json(['error' => $error], 400);
            return;
        }
        $date_paiement = $date_obj->format('Y-m-d');
        error_log("[DEBUG] Date convertie: $date_paiement");

        // Vérification du prêt
        require_once __DIR__ . '/../models/Pret.php';
        $pret = Pret::getById($pret_id);
        if (!$pret) {
            $error = "Prêt $pret_id non trouvé";
            error_log("[ERROR] $error");
            Flight::json(['error' => $error], 404);
            return;
        }
        error_log("[DEBUG] Pret trouvé: " . print_r($pret, true));

        // Vérification du taux
        require_once __DIR__ . '/../models/TauxPret.php';
        $tauxInfo = TauxPret::getById($pret['taux_pret_id']);
        if (!$tauxInfo) {
            $error = "Taux pour prêt $pret_id introuvable";
            error_log("[ERROR] $error");
            Flight::json(['error' => $error], 404);
            return;
        }
        error_log("[DEBUG] Taux trouvé: " . print_r($tauxInfo, true));

        try {
            error_log("[DEBUG] Tentative d'insertion: pret_id=$pret_id, date_paiement=$date_paiement");

            $id = Remboursement::enregistrerRemboursement($pret_id, $date_paiement);

            error_log("[SUCCESS] Insertion réussie. ID: $id");

            Flight::json([
                'success' => true,
                'id' => $id,
                'details' => [
                    'pret_id' => $pret_id,
                    'date_soumise' => $date_input,
                    'date_enregistree' => $date_paiement
                ]
            ]);
        } catch (PDOException $e) {
            $error_msg = "Erreur PDO: " . $e->getMessage();
            error_log("[CRITICAL] $error_msg");

            // Récupération des infos supplémentaires d'erreur SQL
            $error_info = $e->errorInfo ?? [];
            error_log("[CRITICAL] Détails SQL: " . print_r($error_info, true));

            Flight::json([
                'error' => "Erreur technique",
                'details' => [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'sql_state' => $error_info[0] ?? null,
                    'driver_code' => $error_info[1] ?? null,
                    'driver_message' => $error_info[2] ?? null
                ]
            ], 500);
        }
    }
}
