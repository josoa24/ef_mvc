<?php
require_once __DIR__ . '/../models/EtablissementFinancier.php';

class EtablissementFinancierController
{
    public static function getAll()
    {
        $data = EtablissementFinancier::getAll();
        Flight::json($data);
    }

    public static function getById($id)
    {
        $data = EtablissementFinancier::getById($id);
        Flight::json($data);
    }

    public static function create()
    {
        $data = Flight::request()->data;
        $id = EtablissementFinancier::create($data);
        Flight::json(['message' => 'Établissement financier ajouté', 'id' => $id]);
    }

    public static function update($id)
    {
        $data = Flight::request()->data;
        EtablissementFinancier::update($id, $data);
        Flight::json(['message' => 'Établissement financier modifié']);
    }

    public static function delete($id)
    {
        EtablissementFinancier::delete($id);
        Flight::json(['message' => 'Établissement financier supprimé']);
    }

    public static function getMontantsDisponiblesParMois()
    {
        $request = Flight::request();
        $dateDebut = $request->query['dateDebut'] ?? null;
        $dateFin = $request->query['dateFin'] ?? null;

        if (!$dateDebut || !$dateFin) {
            Flight::json(['success' => false, 'message' => 'Paramètres dateDebut et dateFin requis'], 400);
            return;
        }

        try {
            $result = EtablissementFinancier::calculMontantsDisponiblesParMois($dateDebut, $dateFin);
            Flight::json($result);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
