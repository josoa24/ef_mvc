<?php
function getDB()
{
    $host = 'localhost';
    $dbname = 'ef_pret_db';
    $username = 'root';
    $password = '';

    // $host = '172.60.0.17';
    // $password = 'j2xLYzj8';
    // $username = 'ETU003234';
    // $dbname = 'db_s2_ETU003234';


    try {
        return new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    } catch (PDOException $e) {
        die(json_encode(['error' => $e->getMessage()]));
    }
}
