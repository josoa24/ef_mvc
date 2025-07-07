<?php
require_once __DIR__ . '/../controllers/RemboursementController.php';
// Routes à ajouter dans ton fichier routes ou index.php

Flight::route('GET /remboursements/clients', ['RemboursementController', 'getClientsAvecPret']);
Flight::route('POST /remboursements', ['RemboursementController', 'create']);
