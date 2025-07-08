<?php
require_once __DIR__ . '/../models/Remboursement.php';
require_once __DIR__ . '/../helpers/Utils.php';

class RemboursementController
{

    public static function getClientsAvecPret()
    {
        $clients = Remboursement::getClientsAvecPret();
        Flight::json($clients);
    }

    public static function create()
    {
        $pret_id = $_POST['pret_id'] ?? null;
        $date_input = $_POST['date_paiement'] ?? null;

        if (!$pret_id || !$date_input) {
            Flight::json(['error' => 'Champs manquants (pret_id, date_paiement)'], 400);
            return;
        }

        $date_obj = DateTime::createFromFormat('Y-m-d', $date_input);
        if (!$date_obj) {
            Flight::json(['error' => 'Format de date invalide. Utilisez JJ/MM/AAAA'], 400);
            return;
        }
        $date_paiement = $date_obj->format('Y-m-d');

        require_once __DIR__ . '/../models/Pret.php';
        $pret = Pret::getById($pret_id);
        if (!$pret) {
            Flight::json(['error' => 'Prêt non trouvé'], 404);
            return;
        }

        require_once __DIR__ . '/../models/TauxPret.php';
        $tauxInfo = TauxPret::getById($pret['taux_pret_id']);
        if (!$tauxInfo) {
            Flight::json(['error' => 'Taux de prêt introuvable'], 404);
            return;
        }

        $montant = $pret['montant'];
        $duree = $pret['duree_mois'];
        $taux_annuel = $tauxInfo['taux'] / 100;

        require_once __DIR__ . '/../helpers/Utils.php';
        $annuite = Utils::calculerAnnuite($montant, $taux_annuel, $duree);

        try {
            $id = Remboursement::enregistrerRemboursement($pret_id, $date_paiement);

            // Log pour débogage
            error_log("Remboursement inséré - ID: $id, Pret: $pret_id, Date: $date_paiement");

            Flight::json([
                'message' => 'Remboursement enregistré',
                'id_remboursement' => $id,
                'annuite_attendue' => $annuite,
                'date_soumission' => $date_input, 
                'date_enregistree' => $date_paiement // Montre le format enregistré
            ]);
        } catch (PDOException $e) {
            error_log("Erreur d'insertion - Pret: $pret_id, Date: $date_paiement. Erreur: " . $e->getMessage());

            Flight::json([
                'error' => 'Erreur lors de l\'enregistrement',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
