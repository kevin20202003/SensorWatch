<?php
include 'functions.php';  // Incluir el archivo con las funciones para obtener datos

// Obtener los datos del sensor de suelo
$soilData = get_soilSensor();

if ($soilData) {
    echo json_encode($soilData);  // Devolver los datos como JSON
} else {
    echo json_encode(null);  // Si no hay datos, devolver null
}
?>
