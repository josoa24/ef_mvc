<?php
require_once __DIR__ . '/../controllers/RemboursementController.php';

Flight::route('GET /remboursements/prets', ['RemboursementController', 'getPrets']);
Flight::route('POST /remboursements', ['RemboursementController', 'ajouter']);
Flight::route('GET /remboursements/prets', ['RemboursementController', 'getPrets']);
