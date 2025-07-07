<?php
require_once __DIR__ . '/../controllers/PretController.php';


Flight::route('POST /pret/creerpdf', ['PretController', 'creerpdf']);

