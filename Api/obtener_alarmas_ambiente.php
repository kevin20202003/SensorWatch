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

function get_email()
{
    global $conexion;

    // Verifica si id_usuario está en la sesión
    if (isset($_SESSION['id_usuario'])) {
        $id_usuario = $_SESSION['id_usuario'];
    } else {
        die(json_encode(['error' => 'Usuario no autenticado o sesión expirada.']));
    }

    // Conectar a la base de datos
    connect_db();

    // Preparar y ejecutar la consulta SELECT para obtener el último registro
    $stmt = $conexion->prepare("SELECT * FROM umbral_ambiente WHERE id_usuario = ?");
    if (!$stmt) {
        echo json_encode(array('error' => 'Error en la preparación de la consulta: ' . $conexion->error));
        return;
    }

    $stmt->bind_param("i", $id_usuario);

    if (!$stmt->execute()) {
        echo json_encode(array('error' => 'Error en la ejecución de la consulta: ' . $stmt->error));
        return;
    }

    $result = $stmt->get_result();
    $data = $result->fetch_assoc(); // Obtener el último registro

    $stmt->close();
    close_db();

    // Retornar el último registro obtenido
    if ($data) {
        echo json_encode($data);
    } else {
        echo json_encode(array('message' => 'No se encontraron datos para este usuario.'));
    }
}

// Ejecutar la función para obtener el último registro de datos del sensor
get_email();
?>
