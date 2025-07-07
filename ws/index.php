<?php
require 'vendor/autoload.php';
require 'db.php';


require 'routes/etudiant_routes.php';
require 'routes/etablissement_financier.php';
require 'routes/client.php';
require 'routes/ajout_fonds.php';

Flight::start();
