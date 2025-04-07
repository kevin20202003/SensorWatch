<?php
header('Content-Type: application/json');

// Conexión a la base de datos
$servername = "localhost";
$username = "u636023223_sensorwatch";
$password = "SensorWatch99";
$dbname = "u636023223_invernadero";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtiene los datos del POST
$id_usuario = $_POST['id_usuario'];
$temperature_min = $_POST['temperature_min'];
$temperature_max = $_POST['temperature_max'];
$humidity_min = $_POST['humidity_min'];
$humidity_max = $_POST['humidity_max'];

// Verifica si ya existe una entrada para el usuario
$sqlCheck = "SELECT COUNT(*) AS count FROM umbral_suelo WHERE id_usuario = ?";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("i", $id_usuario);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();
$row = $resultCheck->fetch_assoc();

if ($row['count'] > 0) {
    // Si ya existe, hacer UPDATE
    $sql = "UPDATE umbral_suelo SET temperatura_min = ?, temperatura_max = ?, humedad_min = ?, humedad_max = ? WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ddddi", $temperature_min, $temperature_max, $humidity_min, $humidity_max, $id_usuario);
} else {
    // Si no existe, hacer INSERT
    $sql = "INSERT INTO umbral_suelo (id_usuario, temperatura_min, temperatura_max, humedad_min, humedad_max)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("idddd", $id_usuario, $temperature_min, $temperature_max, $humidity_min, $humidity_max);
}

if ($stmt->execute()) {
    echo json_encode(array("success" => true));
} else {
    echo json_encode(array("success" => false, "error" => $stmt->error));
}

$stmt->close();
$conn->close();
?>
