<?php
require 'modelo/conexion.php';

// Configurar la zona horaria a Ecuador
date_default_timezone_set('America/Guayaquil');

// Leer los datos enviados en formato JSON
$json = file_get_contents('php://input');
$datos = json_decode($json, true);

if (!$datos) {
    http_response_code(400); // Solicitud incorrecta
    echo json_encode(["error" => "Datos JSON no válidos"]);
    exit;
}

// Extraer datos del JSON
$temperaturaDHT = $datos['temperaturaDHT'] ?? null;
$humedadDHT = $datos['humedadDHT'] ?? null;
$luz = $datos['luz'] ?? null;
$temperaturaDS18B20 = $datos['temperaturaDS18B20'] ?? null;
$humedadSuelo = $datos['humedadSuelo'] ?? null;
$ph = $datos['ph'] ?? null;
$hora = $datos['hora'] ?? date('Y-m-d H:i:s'); // Usa la hora enviada o la actual

// Validar datos
if (is_null($temperaturaDHT) || is_null($humedadDHT) || is_null($luz) || is_null($temperaturaDS18B20) || is_null($humedadSuelo) || is_null($ph)) {
    http_response_code(400);
    echo json_encode(["error" => "Faltan datos necesarios"]);
    exit;
}

// Iniciar transacción
$conexion->begin_transaction();

try {
    // Insertar datos en la tabla de datos ambientales
    $query_ambiente = "INSERT INTO datos_ambiente (temperatura_amb, humedad_amb, lux, created_at) VALUES (?, ?, ?, ?)";
    $stmt_ambiente = $conexion->prepare($query_ambiente);
    $stmt_ambiente->bind_param("ddds", $temperaturaDHT, $humedadDHT, $luz, $hora);
    $stmt_ambiente->execute();

    // Insertar datos en la tabla de datos de suelo
    $query_suelo = "INSERT INTO datos_suelo (temperatura, humedad, PH, created_at) VALUES (?, ?, ?, ?)";
    $stmt_suelo = $conexion->prepare($query_suelo);
    $stmt_suelo->bind_param("ddds", $temperaturaDS18B20, $humedadSuelo, $ph, $hora);
    $stmt_suelo->execute();

    // Confirmar transacción
    $conexion->commit();

    // Respuesta de éxito
    http_response_code(200);
    echo json_encode(["mensaje" => "Datos insertados correctamente"]);
} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conexion->rollback();
    http_response_code(500);
    echo json_encode(["error" => "Error al insertar datos: " . $e->getMessage()]);
}

// Cerrar la conexión
$conexion->close();
?>
