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

// Consulta SQL para obtener el historial del sensor del usuario agrupado por hora
$sql = "SELECT 
        DATE_FORMAT(date, '%Y-%m-%d %H:00:00') AS hora,
        AVG(temp) AS temp,
        AVG(humidity) AS humidity,
        AVG(pressure) AS pressure,
        AVG(wind_speed) AS wind_speed
    FROM 
        datos_meteorologicos
    GROUP BY 
        hora
    ORDER BY 
        hora DESC
    LIMIT 20";
        
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        "hora" => $row["hora"], // Cambiado de "timestamp" a "hora"
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
