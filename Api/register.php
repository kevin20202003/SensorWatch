<?php
header("Content-Type: application/json");
$conn = new mysqli("localhost", "u636023223_sensorwatch", "SensorWatch99", "u636023223_invernadero");

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$nombre_usuario = $_POST['nombre'];
$correo_electronico = $_POST['correo_electronico'];
$contrasena = password_hash($_POST['password'], PASSWORD_BCRYPT);

// Verificar si el correo o el nombre de usuario ya existen
$sql_check = "SELECT * FROM usuarios WHERE correo_electronico='$correo_electronico' OR nombre='$nombre_usuario'";
$result_check = $conn->query($sql_check);

if ($result_check->num_rows > 0) {
    // Usuario o correo ya existe
    echo json_encode(["status" => "error", "message" => "Usuario o correo ya existente"]);
} else {
    // Insertar el nuevo usuario
    $sql = "INSERT INTO usuarios (nombre, correo_electronico, password) VALUES ('$nombre_usuario', '$correo_electronico', '$contrasena')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error en el registro: " . $conn->error]);
    }
}

$conn->close();
?>
