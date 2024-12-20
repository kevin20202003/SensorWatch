<?php 
require '../modelo/conexion.php';
session_start();

if (!empty($_POST["btningresar"])) {
    if (!empty($_POST["usuario"]) && !empty($_POST["password"])) {
        $usuario = $_POST["usuario"];
        $password = $_POST["password"];

        // Consulta preparada para evitar inyección SQL
        $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE nombre = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado && $resultado->num_rows > 0) {
            $datos = $resultado->fetch_object();

            // Verificar si el estado del usuario es inactivo
            if ($datos->estado == 'Inactivo') {
                echo "<div class='alert alert-danger'>Usuario inactivo. No tienes permiso para acceder.</div>";
            } else {
                // Verificar la contraseña ingresada contra el hash almacenado
                if (password_verify($password, $datos->password)) {
                    // Almacenar la ID del usuario en sesión para validar posteriormente
                    $_SESSION["id_usuario"] = $datos->id_usuario;
                    
                    // Redirigir al usuario a la página de verificación de código
                    header("location: ../auth/verificar_codigo.php");
                    exit;
                } else {
                    echo "<div class='alert alert-danger'>Contraseña incorrecta</div>";
                }
            }
        } else {
            echo "<div class='alert alert-danger'>Usuario no encontrado</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Campos vacíos</div>";
    }
}
?>
