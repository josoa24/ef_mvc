<?php
require_once __DIR__ . '/../controllers/TauxPretController.php';


Flight::route('POST /taux_pret/ajouter', ['TauxPretController', 'ajouter']);
Flight::route('GET /taux_pret', ['TauxPretController', 'getAll']);
Flight::route('GET /getAllWidthType', ['TauxPretController', 'getAllWidthType']);
Flight::route('GET /taux_pret/duree/@id_type_pret/@duree', ['TauxPretController', 'getTauxByDuree']);
