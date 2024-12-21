<?php
header("Content-Type: application/json");

// Incluir el archivo de conexión
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
$limite = $tabla === 'meteorologico' ? 30 : 7;

// Preparar la consulta usando consultas preparadas
try {
    $stmt = $pdo->prepare("SELECT * FROM {$tabla_predicciones} ORDER BY {$columna_fecha} DESC LIMIT :limite");
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->execute();
    
    $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($datos)) {
        echo json_encode(["status" => "error", "message" => "No hay datos disponibles en la tabla seleccionada."]);
    } else {
        echo json_encode(["status" => "success", "data" => $datos]);
    }
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Error al ejecutar la consulta: " . $e->getMessage()]);
}

$pdo = null;
?>
