<?php
include('../modelo/conexion.php');

$response = array('success' => false, 'data' => array());

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = "SELECT * FROM usuarios WHERE id = $id";
    $result = mysqli_query($conexion, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        $response['success'] = true;
        $response['data'] = mysqli_fetch_assoc($result);
    } else {
        $response['message'] = 'Usuario no encontrado';
    }
} else {
    $response['message'] = 'ID de usuario no proporcionado';
}

echo json_encode($response);
?>
