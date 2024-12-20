<?php
include('../modelo/conexion.php');

$response = array('success' => false, 'message' => '');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $contrasena = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Valores predeterminados
    $rol = 'Cliente';
    $estado = 'Activo';

    // Verificación de duplicidad de correo
    $queryCheck = "SELECT COUNT(*) AS count FROM usuarios WHERE correo_electronico = ?";
    $stmtCheck = $conexion->prepare($queryCheck);
    $stmtCheck->bind_param("s", $correo);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();
    $rowCheck = $resultCheck->fetch_assoc();

    if ($rowCheck['count'] > 0) {
        $response['message'] = 'El correo electrónico ya está registrado.';
    } else {
        // Inserción segura con consultas preparadas
        $query = "INSERT INTO usuarios (nombre, correo_electronico, password, rol, estado) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("sssss", $nombre, $correo, $contrasena, $rol, $estado);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Usuario agregado con éxito.';
        } else {
            $response['message'] = 'Error al agregar usuario: ' . $conexion->error;
        }

        $stmt->close();
    }

    $stmtCheck->close();
}

echo json_encode($response);
?>
