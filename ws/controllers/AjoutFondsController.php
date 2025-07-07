<?php
require_once __DIR__ . '/../models/AjoutFonds.php';

class AjoutFondsController
{
    public static function getAll()
    {
        $data = AjoutFonds::getAll();
        Flight::json($data);
    }

    public static function getById($id)
    {
        $data = AjoutFonds::getById($id);
        Flight::json($data);
    }

    public static function create()
    {
        $data = Flight::request()->data;
        $id = AjoutFonds::create($data);
        Flight::json(['message' => 'Ajout de fonds enregistré', 'id' => $id]);
    }

    public static function update($id)
    {
        $data = Flight::request()->data;
        AjoutFonds::update($id, $data);
        Flight::json(['message' => 'Ajout de fonds mis à jour']);
    }

    public static function delete($id)
    {
        AjoutFonds::delete($id);
        Flight::json(['message' => 'Ajout de fonds supprimé']);
    }
}
