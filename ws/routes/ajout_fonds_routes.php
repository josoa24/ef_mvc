<?php
require_once __DIR__ . '/../controllers/AjoutFondsController.php';

Flight::route('GET /ajout_fonds', ['AjoutFondsController', 'getAll']);
Flight::route('GET /ajout_fonds/@id', ['AjoutFondsController', 'getById']);
Flight::route('POST /ajout_fonds', ['AjoutFondsController', 'create']);
Flight::route('PUT /ajout_fonds/@id', ['AjoutFondsController', 'update']);
Flight::route('DELETE /ajout_fonds/@id', ['AjoutFondsController', 'delete']);
