<?php
function get_soilSensor() {
    require 'modelo/conexion.php';

    // Consultar los últimos datos del sensor de suelo
    $sql = "SELECT * FROM datos_suelo ORDER BY id DESC LIMIT 1";
    $result = $conexion->query($sql);

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
    } else {
        $data = null;
    }

    $conexion->close();
    return $data;
}

function get_environment($condition) {
    require 'modelo/conexion.php';

    // Consultar los últimos datos del sensor ambiental
    $sql = "SELECT * FROM datos_ambiente ORDER BY id DESC LIMIT 1";
    $result = $conexion->query($sql);

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
    } else {
        $data = null;
    }

    $conexion->close();
    return $data;
}
?>
