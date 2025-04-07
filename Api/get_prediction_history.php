<?php
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

$data = [];

// Primera consulta (7 últimos datos para suelo)
$sql = "SELECT 
        DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00') AS hora,
        AVG(temperatura) AS temperatura,
        AVG(humedad) AS humedad,
        AVG(ph) AS ph
    FROM 
        datos_suelo_predicciones
    GROUP BY 
        hora
    LIMIT 7";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$data['prediccion'] = [];
while ($row = $result->fetch_assoc()) {
    $data['prediccion'][] = [
        "hora" => $row["hora"],
        "temperatura" => floatval($row["temperatura"]),
        "humedad" => floatval($row["humedad"]),
        "ph" => floatval($row["ph"])
    ];
}

// Segunda consulta (7 últimos datos para ambiente)
$sql = "SELECT 
        DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00') AS hora,
        AVG(temperatura_amb) AS temperatura_amb,
        AVG(humedad_amb) AS humedad_amb,
        AVG(lux) AS lux
    FROM 
        datos_ambiente_predicciones
    GROUP BY 
        hora
    LIMIT 7";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$data['prediccion2'] = [];
while ($row = $result->fetch_assoc()) {
    $data['prediccion2'][] = [
        "hora" => $row["hora"],
        "temperatura_amb" => floatval($row["temperatura_amb"]),
        "humedad_amb" => floatval($row["humedad_amb"]),
        "lux" => floatval($row["lux"])
    ];
}

// Tercera consulta (30 últimos datos para clima)
$sql = "SELECT 
        DATE_FORMAT(date, '%Y-%m-%d %H:00:00') AS hora,
        AVG(temp) AS temp,
        AVG(humidity) AS humidity,
        AVG(pressure) AS pressure,
        AVG(wind_speed) AS wind_speed
    FROM 
        datos_meteorologicos_predicciones
    GROUP BY 
        hora
    LIMIT 30";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$data['prediccion3'] = [];
while ($row = $result->fetch_assoc()) {
    $data['prediccion3'][] = [
        "hora" => $row["hora"],
        "temp" => floatval($row["temp"]),
        "humidity" => floatval($row["humidity"]),
        "pressure" => floatval($row["pressure"]),
        "wind_speed" => floatval($row["wind_speed"])
    ];
}

// Retornar los datos como JSON
header('Content-Type: application/json');
echo json_encode($data);

// Cerrar conexión
$stmt->close();
$conn->close();
?>
