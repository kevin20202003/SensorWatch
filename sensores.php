<?php
// Configuración de la base de datos
$host = "localhost";  // Cambia por la dirección de tu servidor
$usuario = "root";     // Cambia por tu usuario de base de datos
$contrasena = "";       // Cambia por tu contraseña de base de datos
$base_datos = "invernadero"; // Cambia por el nombre de tu base de datos

// Crear conexión a la base de datos
$conn = new mysqli($host, $usuario, $contrasena, $base_datos);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error al conectar con la base de datos: " . $conn->connect_error);
}

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

// Validar datos
if (is_null($temperaturaDHT) || is_null($humedadDHT) || is_null($luz) || is_null($temperaturaDS18B20) || is_null($humedadSuelo) || is_null($ph)) {
    http_response_code(400);
    echo json_encode(["error" => "Faltan datos necesarios"]);
    exit;
}

// Iniciar transacción
$conn->begin_transaction();

try {
    // Insertar datos en la tabla de datos ambientales
    $query_ambiente = "INSERT INTO datos_ambiente (temperatura_amb, humedad_amb, lux) VALUES (?, ?, ?)";
    $stmt_ambiente = $conn->prepare($query_ambiente);
    $stmt_ambiente->bind_param("ddd", $temperaturaDHT, $humedadDHT, $luz);
    $stmt_ambiente->execute();

    // Insertar datos en la tabla de datos de suelo
    $query_suelo = "INSERT INTO datos_suelo (temperatura, humedad, PH) VALUES (?, ?, ?)";
    $stmt_suelo = $conn->prepare($query_suelo);
    $stmt_suelo->bind_param("ddd", $temperaturaDS18B20, $humedadSuelo, $ph);
    $stmt_suelo->execute();

    // Confirmar transacción
    $conn->commit();

    // Respuesta de éxito
    http_response_code(200);
    echo json_encode(["mensaje" => "Datos insertados correctamente"]);
} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conn->rollback();
    http_response_code(500);
    echo json_encode(["error" => "Error al insertar datos: " . $e->getMessage()]);
}

// Cerrar la conexión
$conn->close();
?>
