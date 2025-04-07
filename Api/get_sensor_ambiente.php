<?php
session_start(); // Asegúrate de que se llame al inicio de la sesión

header("Content-Type: application/json");

function get_soilSensor() {
    // Conectar a la base de datos
    $conn = new mysqli("localhost", "u636023223_sensorwatch", "SensorWatch99", "u636023223_invernadero");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Consultar los últimos datos del sensor de suelo
    $sql = "SELECT * FROM datos_ambiente ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
    } else {
        $data = null;
    }

    $conn->close();
    return $data;
}

// Ejecutar todas las funciones y combinar resultados
$response = [
    'soilSensor' => get_soilSensor()
];

echo json_encode($response);
?>
