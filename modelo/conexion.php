<?php

$conexion = new mysqli("localhost", "u636023223_sensorwatch", "SensorWatch99", "u636023223_invernadero");

// Configurar el conjunto de caracteres
$conexion->set_charset("utf8");

// Verificar si ocurrió un error en la conexión
if ($conexion->connect_error) {
    die("Error al conectar con la base de datos: " . $conexion->connect_error);
}

?>
