<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "u636023223_sensorwatch";
$password = "SensorWatch99";
$dbname = "u636023223_invernadero";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$userId = isset($_GET['id_usuario']) ? intval($_GET['id_usuario']) : -1;

if ($userId == -1) {
    echo json_encode(["error" => "ID de usuario no válido"]);
    exit;
}

$sql = "SELECT temperatura_min, temperatura_max, humedad_min, humedad_max, presion_min, presion_max
        FROM umbral_meteorologicos
        WHERE id_usuario = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(["error" => "No se encontraron umbrales para el usuario"]);
    }
} else {
    echo json_encode(["error" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
