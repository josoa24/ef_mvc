<?php
require 'vendor/autoload.php';
require 'db.php';
require 'routes/etudiant_routes.php';

Flight::start();
Flight::set('flight.base_url', '/Projets/Final_Exam_S4/ef_mvc');
