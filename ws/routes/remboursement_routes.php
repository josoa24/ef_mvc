<?php
require_once __DIR__ . '/../controllers/RemboursementController.php';

Flight::route('GET /remboursements/clients', ['RemboursementController', 'getClientsAvecPret']);
Flight::route('POST /remboursements', ['RemboursementController', 'create']);
