<?php
function get_soilSensor() {
    // Conectar a la base de datos
    $conn = new mysqli("localhost", "root", "", "invernadero");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Consultar los últimos datos del sensor de suelo
    $sql = "SELECT * FROM datos_suelo ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
    } else {
        $data = null;
    }

    $conn->close();
    return $data;
}

function get_environment($condition) {
    // Conectar a la base de datos
    $conn = new mysqli("localhost", "root", "", "invernadero");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Consultar los últimos datos del sensor ambiental
    $sql = "SELECT * FROM datos_ambiente ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
    } else {
        $data = null;
    }

    $conn->close();
    return $data;
}
?>
