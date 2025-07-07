<?php
require_once __DIR__ . '/../models/Pret.php';

class PretController
{
    public static function getAll()
    {
        $data = Pret::getAll();
        Flight::json($data);
    }

    public static function getById($id)
    {
        $data = Pret::getById($id);
        if ($data) {
            Flight::json($data);
        } else {
            Flight::halt(404, json_encode(['error' => 'Prêt non trouvé']));
        }
    }

    public static function create()
    {
        $data = Flight::request()->data;

        $id_client = $data->id_client;
        $montant_pret = (float) $data->montant;

       
        $solde_disponible = AjoutFonds::getSoldeClient($id_client);

        
        if ($montant_pret > $solde_disponible) {
            Flight::json([
                'error' => "Solde insuffisant. Solde disponible : {$solde_disponible} Ar, montant demandé : {$montant_pret} Ar"
            ], 400); // 400 = Bad Request
            return;
        }

       
        $id = Pret::create($data);
        Flight::json(['message' => 'Prêt créé', 'id' => $id]);
    }
    public static function update($id)
    {
        $data = Flight::request()->data;
        Pret::update($id, $data);
        Flight::json(['message' => 'Prêt mis à jour']);
    }

    public static function delete($id)
    {
        Pret::delete($id);
        Flight::json(['message' => 'Prêt supprimé']);
    }
}
