<?php 
session_start();

if (!empty($_POST["btningresar"])) {
    if (!empty($_POST["usuario"]) && !empty($_POST["correo_electronico"]) && !empty($_POST["password"])) {
        $usuario = $_POST["usuario"];
        $correo = $_POST["correo_electronico"];
        $contrasena = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $estado = "Activo";

        // Verificar si el usuario ya existe
        $sql_verificar = $conexion->query("SELECT * FROM usuarios WHERE nombre = '$usuario' OR correo_electronico = '$correo'");
        if ($sql_verificar->num_rows > 0) {
            echo "<div class='alert alert-warning'>Usuario o correo electrónico ya existente por favor ingrese datos nuevos</div>";
        } else {
            // Si no existe, proceder con la inserción
            $sql_insertar = $conexion->query("INSERT INTO usuarios (nombre, correo_electronico, password, estado) VALUES ('$usuario', '$correo', '$contrasena', '$estado')");
            if ($sql_insertar) {
                header("location: login.php");
                exit; // Importante: salir del script después de redirigir
            } else {
                echo "<div class='alert alert-danger'>Error al registrar usuario: " . $conexion->error . "</div>";
            }
        }
        
    } else {
        echo "Campos vacíos";
    }
}

?>