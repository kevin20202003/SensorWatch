<?php
require '../modelo/conexion.php';

// Obtener ID de la notificación a eliminar
$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];

// Eliminar la notificación
$sql_delete = "DELETE FROM notificaciones WHERE id = $id";
$result_delete = $conexion->query($sql_delete);

if ($result_delete) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}

// Cerrar conexión
$conexion->close();
?>
