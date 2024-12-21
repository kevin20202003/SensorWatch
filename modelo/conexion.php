<?php

$host = 'dpg-ctjgc29opnds73fpf86g-a'; // External o Internal URL
$port = '5432'; // Puerto de PostgreSQL
$dbname = 'invernadero_b4gl';
$user = 'invernadero_b4gl_user';
$password = 'y39YXOlTTfBs5Fs28iZrHV8Dj4DJwLYY';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexión exitosa a PostgreSQL.";
} catch (PDOException $e) {
    echo "Error al conectar a PostgreSQL: " . $e->getMessage();
}

?>