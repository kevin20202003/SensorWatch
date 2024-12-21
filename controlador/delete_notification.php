<?php
// Incluir el archivo de conexión
require '../modelo/conexion.php';

// Obtener ID de la notificación a eliminar
$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];

// Verificar que el ID esté presente
if (!isset($id)) {
    echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
    exit;
}

// Eliminar la notificación utilizando una consulta preparada
$sql_delete = "DELETE FROM notificaciones WHERE id = :id";
$stmt = $pdo->prepare($sql_delete);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);

// Ejecutar la consulta y verificar si se realizó con éxito
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}

// Cerrar la conexión
$pdo = null;
?>
