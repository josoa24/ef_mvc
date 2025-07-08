<?php
require_once __DIR__ . '/../models/Client.php';

class ClientController
{
    public static function getAll()
    {
        $data = Client::getAll();
        Flight::json($data);
    }

    public static function getById($id)
    {
        $data = Client::getById($id);
        Flight::json($data);
    }

    public static function create()
    {
        $data = Flight::request()->data;
        $id = Client::create($data);
        Flight::json(['message' => 'Client ajouté', 'id' => $id]);
    }

    public static function update($id)
    {
        $data = Flight::request()->data;
        Client::update($id, $data);
        Flight::json(['message' => 'Client modifié']);
    }

    public static function delete($id)
    {
        Client::delete($id);
        Flight::json(['message' => 'Client supprimé']);
    }

    public static function getCount()
    {
        $count = Client::getCount();
        Flight::json([
            'success' => true,
            'total_clients' => $count
        ]);
    }


    public static function getIncrease()
    {
        $increaseData = Client::getLastMonthIncrease();
        $totalClients = Client::getCount();

        Flight::json([
            'success' => true,
            'total_clients' => $totalClients,
            'increase_percentage' => $increaseData['augmentation_pct']
        ]);
    }
}
