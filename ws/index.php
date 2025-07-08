<?php
require 'vendor/autoload.php';
require 'db.php';

require 'routes/etudiant_routes.php';
require 'routes/remboursement_routes.php';
require 'routes/etablissement_financier_routes.php';
require 'routes/client_routes.php';
require 'routes/ajout_fonds_routes.php';
require 'routes/pret_routes.php';
require 'routes/taux_pret_routes.php';

Flight::start();
