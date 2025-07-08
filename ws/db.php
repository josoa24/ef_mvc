<?php
function getDB()
{
    $host = 'localhost';
    $dbname = 'ef_pret_db';
    $username = 'root';
    $password = 'ETU003234M.';

    // $host = '172.60.0.17';
    // $password = '1OzWoFzX';
    // $username = 'ETU003213';
    // $dbname = 'db_s2_ETU003213';


    try {
        return new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    } catch (PDOException $e) {
        die(json_encode(['error' => $e->getMessage()]));
    }
}
