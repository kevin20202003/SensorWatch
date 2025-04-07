<?php
header("Content-Type: application/json");

require 'modelo/conexion.php';

// Validar tabla recibida en la solicitud
$tabla = $_GET['tabla'] ?? '';
$tablas_validas = [
    'suelo' => ['tabla' => 'datos_suelo_predicciones', 'columna_fecha' => 'created_at'],
    'ambiente' => ['tabla' => 'datos_ambiente_predicciones', 'columna_fecha' => 'created_at'],
    'meteorologico' => ['tabla' => 'datos_meteorologicos_predicciones', 'columna_fecha' => 'date']
];

if (!array_key_exists($tabla, $tablas_validas)) {
    echo json_encode(["status" => "error", "message" => "Tabla inválida"]);
    exit;
}

$tabla_predicciones = $tablas_validas[$tabla]['tabla'];
$columna_fecha = $tablas_validas[$tabla]['columna_fecha'];

// Establecer el límite de registros según la tabla seleccionada
$limite = 7; // Por defecto, mostrar los últimos 7 registros

if ($tabla == 'meteorologico') {
    $limite = 30; // Para la tabla meteorológica, mostrar los últimos 30 registros
}

// Consultar los últimos registros de la tabla correspondiente
$query = "SELECT * FROM `$tabla_predicciones` LIMIT $limite";
$resultado = $conexion->query($query);

if (!$resultado) {
    echo json_encode(["status" => "error", "message" => "Error al ejecutar la consulta: " . $conexion->error]);
    exit;
}

// Formatear resultados en JSON
$datos = [];
while ($fila = $resultado->fetch_assoc()) {
    $datos[] = $fila;
}

if (empty($datos)) {
    echo json_encode(["status" => "error", "message" => "No hay datos disponibles en la tabla seleccionada."]);
} else {
    echo json_encode(["status" => "success", "data" => $datos]);
}

$conexion->close();
?>
