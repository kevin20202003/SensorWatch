<?php 
require '../modelo/conexion.php';
session_start();

if (!empty($_POST["btningresar"])) {
    if (!empty($_POST["usuario"]) && !empty($_POST["password"])) {
        $usuario = $_POST["usuario"];
        $password = $_POST["password"];

        // Consulta preparada para evitar inyección SQL
        $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE nombre = :usuario");
        $stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_OBJ);

        if ($resultado) {
            // Verificar si el estado del usuario es inactivo
            if ($resultado->estado == 'Inactivo') {
                echo "<div class='alert alert-danger'>Usuario inactivo. No tienes permiso para acceder.</div>";
            } else {
                // Verificar la contraseña ingresada contra el hash almacenado
                if (password_verify($password, $resultado->password)) {
                    // Almacenar la ID del usuario en sesión para validar posteriormente
                    $_SESSION["id_usuario"] = $resultado->id_usuario;
                    
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
