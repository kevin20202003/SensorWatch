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

// Verificar si la solicitud es un POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos JSON enviados en la solicitud
    $data = json_decode(file_get_contents('php://input'), true);

    // Extraer los datos
    $humedad_min = $data['humedad_min'] ?? null;
    $humedad_max = $data['humedad_max'] ?? null;
    $temperatura_min = $data['temperatura_min'] ?? null;
    $temperatura_max = $data['temperatura_max'] ?? null;
    $presion_min = $data['presion_min'] ?? null;
    $presion_max = $data['presion_max'] ?? null;
    $id_usuario = $data['id_usuario'] ?? null;

    // Validar datos
    if (is_null($humedad_min) || is_null($humedad_max) || is_null($temperatura_min) || is_null($temperatura_max) || is_null($presion_min) || is_null($presion_max) || is_null($id_usuario)) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son requeridos.']);
        exit;
    }

    // Verificar si ya existe un registro para el usuario
    $sqlCheck = "SELECT COUNT(*) AS count FROM umbral_meteorologicos WHERE id_usuario = ?";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param("i", $id_usuario);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();
    $row = $resultCheck->fetch_assoc();

    if ($row['count'] > 0) {
        // Si ya existe, hacer UPDATE
        $sqlUpdate = "UPDATE umbral_meteorologicos 
                      SET humedad_min = ?, humedad_max = ?, temperatura_min = ?, temperatura_max = ?, presion_min = ?, presion_max = ? 
                      WHERE id_usuario = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("ddddddi", $humedad_min, $humedad_max, $temperatura_min, $temperatura_max, $presion_min, $presion_max, $id_usuario);
        $result = $stmtUpdate->execute();

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Alarma actualizada exitosamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar la alarma.']);
        }
    } else {
        // Si no existe, hacer INSERT
        $sqlInsert = "INSERT INTO umbral_meteorologicos (id_usuario, humedad_min, humedad_max, temperatura_min, temperatura_max, presion_min, presion_max)
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bind_param("idddddd", $id_usuario, $humedad_min, $humedad_max, $temperatura_min, $temperatura_max, $presion_min, $presion_max);
        $result = $stmtInsert->execute();

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Alarma guardada exitosamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al guardar la alarma.']);
        }
    }

    // Cerrar declaración
    $stmtCheck->close();
    if (isset($stmtUpdate)) $stmtUpdate->close();
    if (isset($stmtInsert)) $stmtInsert->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no permitido.']);
}

// Cerrar la conexión
$conn->close();
?>
