<?php
    // Incluir el archivo de conexión
    require 'modelo/conexion.php';

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

    
    try {
        // Iniciar transacción
        $pdo->beginTransaction();

        // Insertar datos en la tabla de datos ambientales
        $query_ambiente = "INSERT INTO datos_ambiente (temperatura_amb, humedad_amb, lux) VALUES (:temperaturaDHT, :humedadDHT, :luz)";
        $stmt_ambiente = $pdo->prepare($query_ambiente);
        $stmt_ambiente->bindParam(':temperaturaDHT', $temperaturaDHT, PDO::PARAM_STR);
        $stmt_ambiente->bindParam(':humedadDHT', $humedadDHT, PDO::PARAM_STR);
        $stmt_ambiente->bindParam(':luz', $luz, PDO::PARAM_STR);
        $stmt_ambiente->execute();

        // Insertar datos en la tabla de datos de suelo
        $query_suelo = "INSERT INTO datos_suelo (temperatura, humedad, PH) VALUES (:temperaturaDS18B20, :humedadSuelo, :ph)";
        $stmt_suelo = $pdo->prepare($query_suelo);
        $stmt_suelo->bindParam(':temperaturaDS18B20', $temperaturaDS18B20, PDO::PARAM_STR);
        $stmt_suelo->bindParam(':humedadSuelo', $humedadSuelo, PDO::PARAM_STR);
        $stmt_suelo->bindParam(':ph', $ph, PDO::PARAM_STR);
        $stmt_suelo->execute();

        // Confirmar transacción
        $pdo->commit();

        // Respuesta de éxito
        http_response_code(200);
        echo json_encode(["mensaje" => "Datos insertados correctamente"]);
    } catch (PDOException $e) {
        // Revertir transacción en caso de error
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(["error" => "Error al insertar datos: " . $e->getMessage()]);
    }

    // Cerrar la conexión
    $pdo = null;
?>
