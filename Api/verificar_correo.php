<?php
session_start(); // Iniciar sesión

header("Content-Type: application/json");

// Conexión a la base de datos
$conn = new mysqli("localhost", "u636023223_sensorwatch", "SensorWatch99", "u636023223_invernadero");
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Conexión fallida: " . $conn->connect_error]));
}

// Validar que el correo haya sido enviado
if (empty($_POST['correo_electronico'])) {
    echo json_encode(["status" => "error", "message" => "El correo electrónico es requerido"]);
    exit();
}

$correo = $_POST['correo_electronico'];

// Consulta segura usando consultas preparadas
$stmt = $conn->prepare("SELECT id_usuario, correo_electronico FROM usuarios WHERE correo_electronico = ?");
$stmt->bind_param("s", $correo);
$stmt->execute();
$result = $stmt->get_result();

// Verificar si se encontró el correo
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $_SESSION['id_usuario'] = $row['id_usuario']; // Guardar el id_usuario en sesión

    // Respuesta de éxito
    echo json_encode([
        "status" => "success",
        "message" => "Correo electrónico encontrado",
        "id_usuario" => $row['id_usuario']
    ]);
} else {
    // Respuesta de error si no se encuentra el correo
    echo json_encode(["status" => "error", "message" => "Correo no encontrado"]);
}

// Cerrar conexión
$stmt->close();
$conn->close();
?>
