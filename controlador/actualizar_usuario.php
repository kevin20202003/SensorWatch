<?php
include('../modelo/conexion.php');

$response = array('success' => false, 'message' => '');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id_usuario'];
    $nombre = $_POST['nombre_usuario'];
    $correo = $_POST['correo'];
    $contraseña = $_POST['contraseña'];
    $rol = $_POST['rol'];
    $estado = $_POST['estado'];

    $query = "UPDATE usuarios SET nombre = '$nombre', correo_electronico = '$correo', contraseña = '$contraseña', rol = '$rol', estado = '$estado' WHERE id = $id";

    if (mysqli_query($conexion, $query)) {
        $response['success'] = true;
        $response['message'] = 'Usuario actualizado con éxito';
    } else {
        $response['message'] = 'Error al actualizar usuario: ' . mysqli_error($conexion);
    }
}

echo json_encode($response);
?>
