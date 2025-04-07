<?php
session_start(); // Iniciar sesión

header("Content-Type: application/json");

// Conexión a la base de datos
$conn = new mysqli("localhost", "u636023223_sensorwatch", "SensorWatch99", "u636023223_invernadero");
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Conexión fallida: " . $conn->connect_error]));
}

// Verificar si el usuario está autenticado
if (empty($_SESSION['id_usuario'])) {
    echo json_encode(["status" => "error", "message" => "No has iniciado sesión"]);
    exit();
}

// Validar si se ha recibido la nueva contraseña
if (empty($_POST['newpassword'])) {
    echo json_encode(["status" => "error", "message" => "La nueva contraseña es obligatoria"]);
    exit();
}

$newPassword = $_POST['newpassword'];

// Obtener el ID del usuario desde la sesión
$id_usuario = $_SESSION['id_usuario'];

// Encriptar la nueva contraseña
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

// Actualizar la contraseña en la base de datos
$stmt = $conn->prepare("UPDATE usuarios SET password = ? WHERE id_usuario = ?");
$stmt->bind_param("si", $hashedPassword, $id_usuario);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Contraseña actualizada correctamente"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error al actualizar la contraseña"]);
}

// Cerrar la conexión
$stmt->close();
$conn->close();
?>
