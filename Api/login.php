<?php
session_start(); // Iniciar sesión

header("Content-Type: application/json");
$conn = new mysqli("localhost", "u636023223_sensorwatch", "SensorWatch99", "u636023223_invernadero");
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$usuario = $_POST['nombre'];
$contrasena = $_POST['password'];

$sql = "SELECT id_usuario, password, estado FROM usuarios WHERE nombre='$usuario'"; // Asegúrate de que estado esté en la consulta
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if (password_verify($contrasena, $row['password'])) {
        // Establecer el ID de usuario en la sesión
        $_SESSION['id_usuario'] = $row['id_usuario'];

        // Obtener el estado de texto ("Activo" o "Inactivo")
        $estado = $row['estado']; 

        // Responder con el estado tal como está
        echo json_encode([
            "status" => "success",
            "id_usuario" => $row['id_usuario'],
            "estado" => $estado // Usar el valor de estado como texto
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Credenciales incorrectas"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Usuario no encontrado"]);
}

$conn->close();
?>
