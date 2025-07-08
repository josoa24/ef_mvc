<?php
require_once __DIR__ . '/../models/InteretGraphique.php';

class InteretGraphiqueController {
    public static function getInterets() {
        $start = isset($_GET['start']) ? $_GET['start'] : null;
        $end = isset($_GET['end']) ? $_GET['end'] : null;

        $resultats = InteretGraphique::getInterets($start, $end);
        echo json_encode($resultats);
    }
}
