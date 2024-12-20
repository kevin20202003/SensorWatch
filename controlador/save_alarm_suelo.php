<?php
// save_alarm_suelo.php
header('Content-Type: application/json');
require '../modelo/conexion.php'; // Asegúrate de incluir tu archivo de conexión a la base de datos

// Verificar si la solicitud es un POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos JSON enviados en la solicitud
    $data = json_decode(file_get_contents('php://input'), true);

    // Extraer los datos
    $humedad_min = isset($data['humedad_min']) ? $data['humedad_min'] : null;
    $humedad_max = isset($data['humedad_max']) ? $data['humedad_max'] : null;
    $temperatura_min = isset($data['temperatura_min']) ? $data['temperatura_min'] : null;
    $temperatura_max = isset($data['temperatura_max']) ? $data['temperatura_max'] : null;
    $id_usuario = isset($data['id_usuario']) ? $data['id_usuario'] : null;

    // Validar datos
    if (is_null($humedad_min) || is_null($humedad_max) || is_null($temperatura_min) || is_null($temperatura_max) || is_null($id_usuario)) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son requeridos.']);
        exit;
    }

    // Insertar datos en la base de datos
    $sql = "INSERT INTO umbral_suelo (id_usuario, humedad_min, humedad_max, temperatura_min, temperatura_max)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conexion->prepare($sql);
    $result = $stmt->execute([$id_usuario, $humedad_min, $humedad_max, $temperatura_min, $temperatura_max]);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Alarma guardada exitosamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar la alarma.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no permitido.']);
}
?>
