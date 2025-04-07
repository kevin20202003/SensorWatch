<?php
session_start(); // Asegúrate de que se llame al inicio de la sesión
header("Content-Type: application/json");

// Inicializa la variable global de conexión
$conexion = null;

function connect_db()
{
    global $conexion;
    if ($conexion === null) {
        $conexion = new mysqli("localhost", "u636023223_sensorwatch", "SensorWatch99", "u636023223_invernadero");
        if ($conexion->connect_error) {
            die("Connection failed: " . $conexion->connect_error);
        }
        $conexion->set_charset("utf8");
    }
    return $conexion;
}

function close_db()
{
    global $conexion;
    if ($conexion !== null) {
        $conexion->close();
        $conexion = null;
    }
}

// Verifica si se ha enviado una solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Conecta a la base de datos
    $conn = connect_db();

    // Obtiene el ID del usuario y el nuevo estado (activo o inactivo)
    $userId = isset($_POST['id_usuario']) ? intval($_POST['id_usuario']) : 0;
    $isActive = isset($_POST['is_active']) ? $_POST['is_active'] : ''; // Ahora is_active es un string ("Activo" o "Inactivo")

    // Validación básica
    if ($userId > 0 && ($isActive === 'Activo' || $isActive === 'Inactivo')) {
        // Prepara la consulta SQL para actualizar el estado del usuario
        $query = "UPDATE usuarios SET estado = ? WHERE id_usuario = ?";
        
        // Prepara la declaración
        if ($stmt = $conn->prepare($query)) {
            // Vincula los parámetros
            $stmt->bind_param('si', $isActive, $userId); // 'si' -> string y entero

            // Ejecuta la consulta
            if ($stmt->execute()) {
                // Consulta ejecutada correctamente
                echo json_encode(["status" => "success", "message" => "Estado del usuario actualizado."]);
            } else {
                // Error en la ejecución de la consulta
                echo json_encode(["status" => "error", "message" => "Error al actualizar el estado."]);
            }

            // Cierra la declaración
            $stmt->close();
        } else {
            // Error al preparar la declaración
            echo json_encode(["status" => "error", "message" => "Error al preparar la consulta."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "ID de usuario o estado inválido."]);
    }

    // Cierra la conexión
    close_db();
}
?>
