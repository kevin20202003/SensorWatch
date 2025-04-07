<?php
session_start();

header("Content-Type: application/json");

$conexion = null;

function connect_db()
{
    global $conexion;
    if ($conexion === null) {
        $conexion = new mysqli("localhost", "u636023223_sensorwatch", "SensorWatch99", "u636023223_invernadero");
        if ($conexion->connect_error) {
            die(json_encode(['error' => 'Connection failed: ' . $conexion->connect_error]));
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

function eliminar_alarma()
{
    global $conexion;

    // Verificar autenticación
    if (!isset($_SESSION['id_usuario'])) {
        die(json_encode(['error' => 'Usuario no autenticado o sesión expirada.']));
    }

    // Verificar el parámetro id_alarma
    if (!isset($_POST['id_alarma'])) {
        die(json_encode(['error' => 'ID de alarma no proporcionado.']));
    }

    $id_alarma = intval($_POST['id_alarma']);

    connect_db();

    // Eliminar la alarma
    $stmt = $conexion->prepare("DELETE FROM umbral_meteorologicos WHERE id = ?");
    if (!$stmt) {
        echo json_encode(['error' => 'Error en la preparación de la consulta: ' . $conexion->error]);
        return;
    }

    $stmt->bind_param("i", $id_alarma);

    if ($stmt->execute()) {
        echo json_encode(['success' => 'Alarma eliminada correctamente.']);
    } else {
        echo json_encode(['error' => 'Error al eliminar la alarma: ' . $stmt->error]);
    }

    $stmt->close();
    close_db();
}

eliminar_alarma();
?>
