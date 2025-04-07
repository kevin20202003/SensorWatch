<?php
require '../modelo/conexion.php';

session_start(); // Asegúrate de iniciar sesión

$id_usuario = $_SESSION['id_usuario']; // Asegúrate de tener la sesión iniciada

// Obtener las notificaciones no leídas
$sql_notificaciones = "SELECT * FROM notificaciones WHERE id_usuario = $id_usuario AND leida = FALSE ORDER BY fecha DESC";
$result_notificaciones = $conexion->query($sql_notificaciones);

$notificaciones = [];
if ($result_notificaciones->num_rows > 0) {
    while ($row = $result_notificaciones->fetch_assoc()) {
        $notificaciones[] = $row;
    }
}

// Contar notificaciones
$num_notificaciones = count($notificaciones);

// Mostrar las notificaciones en formato JSON
header('Content-Type: application/json');
echo json_encode([
    'num_notificaciones' => $num_notificaciones,
    'notificaciones' => $notificaciones
]);

// Cerrar conexión
$conexion->close();
?>
