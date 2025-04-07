<?php
include 'functions.php';  // Incluir el archivo con las funciones para obtener datos

// Obtener los datos del sensor ambiental
$environmentData = get_environment('current');

if ($environmentData) {
    echo json_encode($environmentData);  // Devolver los datos como JSON
} else {
    echo json_encode(null);  // Si no hay datos, devolver null
}
?>
