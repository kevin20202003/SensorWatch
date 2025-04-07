<?php
function obtenerDatosClimaticos() {
    // Configurar la zona horaria a Ecuador
    date_default_timezone_set('America/Guayaquil');

    // URL de la API de OpenWeatherMap
    $apiUrl = "https://api.openweathermap.org/data/2.5/weather?q=Quito,EC&appid=ec1598b98465fdda816b44ebf0155de0&units=metric";

    // Hacer la solicitud a la API
    $response = file_get_contents($apiUrl);

    // Manejo de errores si la API no responde
    if ($response === false) {
        return [
            "error" => "No se pudo obtener datos de la API. Verifica tu conexión o la disponibilidad del servicio."
        ];
    }

    // Decodificar la respuesta JSON
    $data = json_decode($response, true);

    // Verificar si la API devolvió datos válidos
    if (!isset($data['main']) || !isset($data['wind'])) {
        return [
            "error" => "La respuesta de la API no contiene los datos esperados."
        ];
    }

    // Extraer las variables necesarias
    $temperatura = $data['main']['temp'] ?? "N/A";
    $humedad = $data['main']['humidity'] ?? "N/A";
    $presion = $data['main']['pressure'] ?? "N/A";
    $viento = $data['wind']['speed'] ?? "N/A";

    // Guardar en la base de datos
    guardarDatosEnBaseDeDatos($temperatura, $humedad, $presion, $viento);

    // Devolver los datos en un array como JSON
    return [
        "temperatura" => $temperatura,
        "humedad" => $humedad,
        "presion" => $presion,
        "viento" => $viento
    ];
}

function guardarDatosEnBaseDeDatos($temperatura, $humedad, $presion, $viento) {
    require 'modelo/conexion.php';

    // Obtener la fecha y hora actual en Ecuador
    $fechaHora = date('Y-m-d H:i:s');

    // Preparar la consulta SQL
    $sql = "INSERT INTO datos_meteorologicos (date, temp, wind_speed, pressure, humidity) 
            VALUES ('$fechaHora', '$temperatura', '$viento', '$presion', '$humedad')";

    // Ejecutar la consulta
    if ($conexion->query($sql) !== TRUE) {
        // Asegúrate de que no haya ningún mensaje de error en la respuesta
        echo json_encode(["error" => "Error al guardar los datos: " . $conexion->error]);
        exit();
    }

    // Cerrar la conexión
    $conexion->close();
}

// Llamar a la función y devolver los datos como JSON
header('Content-Type: application/json');
echo json_encode(obtenerDatosClimaticos());
?>
