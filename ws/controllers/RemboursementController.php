<?php

require_once __DIR__ . '/../models/Remboursement.php';

class RemboursementController {

    public static function ajouter() {
        $data = Flight::request()->data;

        try {
            $model = new Remboursement();
            $success = $model->insertRemboursement(
                $data['id_pret'],
                $data['date_paiement'],
                intval($data['mois']),
                intval($data['annee'])
            );
            
            if ($success) {
                Flight::json(["success" => true, "message" => "Remboursement ajouté avec succès."]);
            } else {
                Flight::json(["success" => false, "message" => "Échec de l'enregistrement du remboursement."]);
            }

        } catch (Exception $e) {
            Flight::json(["success" => false, "message" => $e->getMessage()]);
        }
    }

    public static function getPrets() {
        try {
            $model = new Remboursement();
            $prets = $model->getPretsEnCours();
            Flight::json($prets);
        } catch (Exception $e) {
            Flight::json(["success" => false, "message" => $e->getMessage()]);
        }
    }
}
