<?php
function get_soilSensor() {
    // Incluir el archivo de conexión
    require 'modelo/conexion.php';

    // Consultar los últimos datos del sensor de suelo
    $sql = "SELECT * FROM datos_suelo ORDER BY id DESC LIMIT 1";
    
    // Preparar y ejecutar la consulta
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error en la consulta: " . $e->getMessage();
        $data = null;
    }

    return $data;
}

function get_environment($condition) {
    // Incluir el archivo de conexión
    require 'modelo/conexion.php';

    // Consultar los últimos datos del sensor ambiental
    $sql = "SELECT * FROM datos_ambiente ORDER BY id DESC LIMIT 1";
    
    // Preparar y ejecutar la consulta
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error en la consulta: " . $e->getMessage();
        $data = null;
    }

    return $data;
}
?>
