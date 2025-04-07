<?php

// Configuración de errores
ini_set('log_errors', 1);
ini_set('display_errors', 0);

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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Leer y decodificar la entrada JSON
        $data = json_decode(file_get_contents('php://input'), true);

        $id_usuario = $data['id_usuario'] ?? null;
        $humedad_min = $data['humedad_min'] ?? null;
        $humedad_max = $data['humedad_max'] ?? null;
        $temperatura_min = $data['temperatura_min'] ?? null;
        $temperatura_max = $data['temperatura_max'] ?? null;

        // Validación de entrada
        if (!$id_usuario || !$humedad_min || !$humedad_max || !$temperatura_min || !$temperatura_max) {
            echo json_encode(['success' => false, 'message' => 'Todos los campos son requeridos.']);
            exit;
        }

        // Verificar si existe un registro para el usuario
        $sqlCheck = "SELECT COUNT(*) AS count FROM umbral_suelo WHERE id_usuario = ?";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->bind_param("i", $id_usuario);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();
        $row = $resultCheck->fetch_assoc();

        if ($row['count'] > 0) {
            // Si ya existe, hacer UPDATE
            $sql = "UPDATE umbral_suelo SET temperatura_min = ?, temperatura_max = ?, humedad_min = ?, humedad_max = ? WHERE id_usuario = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ddddi", $temperatura_min, $temperatura_max, $humedad_min, $humedad_max, $id_usuario);
        } else {
            // Si no existe, hacer INSERT
            $sql = "INSERT INTO umbral_suelo (id_usuario, temperatura_min, temperatura_max, humedad_min, humedad_max)
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("idddd", $id_usuario, $temperatura_min, $temperatura_max, $humedad_min, $humedad_max);
        }

        // Ejecutar la consulta
        $result = $stmt->execute();

        // Verificar si la operación fue exitosa
        if ($result) {
            $message = ($row['count'] > 0) ? 'Registro actualizado exitosamente.' : 'Registro insertado exitosamente.';
            echo json_encode(['success' => true, 'message' => $message]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al procesar la solicitud.']);
        }

    } catch (mysqli_sql_exception $e) {
        error_log("Error en la base de datos: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error interno del servidor.']);
    } catch (Exception $e) {
        error_log("Error general: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error interno del servidor.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no permitido.']);
}
?>
