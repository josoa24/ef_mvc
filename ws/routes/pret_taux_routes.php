<?php
require_once __DIR__ . '/../controllers/TauxPretController.php';


Flight::route('GET /ajouter', ['TauxPretController', 'ajouter']);
