<?php
require_once __DIR__ . '/../controllers/MontantController.php';

Flight::route('GET /montants', ['MontantController', 'getMontantsParMois']);
