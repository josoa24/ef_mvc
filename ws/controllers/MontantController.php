<?php
require_once __DIR__ . '/../models/MontantModel.php';

class MontantController {
    public static function getMontantsParMois() {
        try {
            $start = $_GET['start'] ?? null;
            $end = $_GET['end'] ?? null;

            $data = Montant::getStatsParMois($start, $end);
            header('Content-Type: application/json');
            echo json_encode($data);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
