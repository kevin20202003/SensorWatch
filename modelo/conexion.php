<?php

$host = 'dpg-ctj33hjtq21c73dvtdj0-a'; // External o Internal URL
$port = '5432'; // Puerto de PostgreSQL
$dbname = 'invernadero';
$user = 'invernadero_user';
$password = 'A01znTdBGmkNia7JhUEmgyMabE90NdCp';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexión exitosa a PostgreSQL.";
} catch (PDOException $e) {
    echo "Error al conectar a PostgreSQL: " . $e->getMessage();
}

?>