<?php
require '../modelo/conexion.php';

session_start();

$id_usuario = $_SESSION['id_usuario'];

// Obtener las notificaciones no leídas
$sql_notificaciones = "SELECT * FROM notificaciones WHERE id_usuario = :id_usuario AND leida = FALSE ORDER BY fecha DESC";
$stmt_notificaciones = $pdo->prepare($sql_notificaciones);
$stmt_notificaciones->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt_notificaciones->execute();

$notificaciones = [];
if ($stmt_notificaciones->rowCount() > 0) {
    $notificaciones = $stmt_notificaciones->fetchAll(PDO::FETCH_ASSOC);
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
$pdo = null;
?>
