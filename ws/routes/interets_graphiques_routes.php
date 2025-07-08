<?php
require_once __DIR__ . '/../controllers/InteretGraphiqueController.php';

Flight::route('GET /interets', ['InteretGraphiqueController', 'getInterets']);
