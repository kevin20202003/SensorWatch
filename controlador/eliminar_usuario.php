<?php
include('../modelo/conexion.php');

$response = array('success' => false, 'message' => '');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = "DELETE FROM usuarios WHERE id = $id";

    if (mysqli_query($conexion, $query)) {
        $response['success'] = true;
        $response['message'] = 'Usuario eliminado con éxito';
    } else {
        $response['message'] = 'Error al eliminar usuario: ' . mysqli_error($conexion);
    }
} else {
    $response['message'] = 'ID de usuario no proporcionado';
}

echo json_encode($response);
?>
