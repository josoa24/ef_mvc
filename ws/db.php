<?php
function getDB()
{
    $host = 'localhost';
    $dbname = 'ef_pret_db';
    $username = 'root';
    $password = 'ETU003234M.';

    try {
        return new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    } catch (PDOException $e) {
        die(json_encode(['error' => $e->getMessage()]));
    }
}
