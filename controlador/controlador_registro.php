<?php 
session_start();
require '../modelo/conexion.php'; // Asegúrate de incluir la conexión PDO

if (!empty($_POST["btningresar"])) {
    if (!empty($_POST["usuario"]) && !empty($_POST["correo_electronico"]) && !empty($_POST["password"])) {
        $usuario = $_POST["usuario"];
        $correo = $_POST["correo_electronico"];
        $contrasena = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $rol = "Cliente";
        $estado = "Activo";

        // Verificar si el usuario ya existe
        $sql_verificar = $conexion->prepare("SELECT * FROM usuarios WHERE nombre = :usuario OR correo_electronico = :correo");
        $sql_verificar->bindParam(':usuario', $usuario, PDO::PARAM_STR);
        $sql_verificar->bindParam(':correo', $correo, PDO::PARAM_STR);
        $sql_verificar->execute();

        if ($sql_verificar->rowCount() > 0) {
            echo "<div class='alert alert-warning'>Usuario o correo electrónico ya existente, por favor ingrese datos nuevos.</div>";
        } else {
            // Si no existe, proceder con la inserción
            $sql_insertar = $conexion->prepare("INSERT INTO usuarios (nombre, correo_electronico, password, rol, estado) VALUES (:usuario, :correo, :contrasena, :rol, :estado)");
            $sql_insertar->bindParam(':usuario', $usuario, PDO::PARAM_STR);
            $sql_insertar->bindParam(':correo', $correo, PDO::PARAM_STR);
            $sql_insertar->bindParam(':contrasena', $contrasena, PDO::PARAM_STR);
            $sql_insertar->bindParam(':rol', $rol, PDO::PARAM_STR);
            $sql_insertar->bindParam(':estado', $estado, PDO::PARAM_STR);

            if ($sql_insertar->execute()) {
                header("location: login.php");
                exit; // Importante: salir del script después de redirigir
            } else {
                echo "<div class='alert alert-danger'>Error al registrar usuario.</div>";
            }
        }
    } else {
        echo "<div class='alert alert-danger'>Campos vacíos</div>";
    }
}
?>
