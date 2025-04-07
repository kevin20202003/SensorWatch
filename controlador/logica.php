<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<style>
    /* Estilos CSS para centrar el mensaje y aumentar el tamaño del texto */
    .message-container {
        text-align: center;
        margin-top: 20px;
    }

    .message {
        font-size: 24px;
    }

    .loader {
        border: 4px solid rgba(255, 255, 255, 0.3);
        border-top: 4px solid #007bff;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        animation: spin 1s linear infinite;
        margin: 10px auto;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
</style>
<?php
session_start();
require("../modelo/conexion.php");
require("../modelo/confirmacion.php");

if (isset($_POST['validate_email'])) {
    $email = mysqli_real_escape_string($conexion, $_POST['correo_electronico']);

    // Consulta para verificar si el correo existe en la base de datos
    $consultaEmail = "SELECT * FROM usuarios WHERE correo_electronico = '$email'";
    $resultadoEmail = mysqli_query($conexion, $consultaEmail);

    if ($resultadoEmail && mysqli_num_rows($resultadoEmail) > 0) {
        echo "<div class='message-container alert alert-warning'>
                  <h1 class='message text-warning'>Verificando, por favor espere...</h1>
                  <div class='loader'></div>
              </div>
              <div style='display: none'; class='message-container alert alert-success'>
                  <h1 class='message text-success'>Correo electronico verificado satisfactoriamente </h1>
              </div>";

        echo "<script>setTimeout(function() {
            document.querySelector('.message-container.alert-warning').style.display = 'none';
            document.querySelector('.message-container.alert-success').style.display = 'block';
        }, 5000);</script>";

        echo "<script>setTimeout(function() {
            window.location.href = '../auth/cambio_contraseña.php'; 
        }, 8000);</script>";

        $_SESSION['reset_email'] = $email;
        exit();
    } else {
        echo "<div class='message-container alert alert-danger'>
                  <h1 class='message text-danger'>Error: Correo no encontrado</h1>
              </div>";
        echo "<script>setTimeout(function() {
            window.location.href = '../auth/verificar_correo.php'; 
        }, 3000);</script>";
    }
} elseif (isset($_POST['change_password'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password === $confirm_password) {
        $reset_email = $_SESSION['reset_email'];

        // Verificar si el correo está en sesión
        if (!$reset_email) {
            echo "<div class='message-container alert alert-danger'>
                      <h1 class='message text-danger'>Error: Sesion expirada. Por favor vuelva a intentarlo.</h1>
                  </div>";
            echo "<script>setTimeout(function() {
                window.location.href = '../auth/verificar_correo.php';
            }, 3000);</script>";
            exit();
        }

        // Encriptar la nueva contraseña
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        // Actualizar la contraseña en la base de datos
        $updatePasswordQuery = "UPDATE usuarios SET password = '$hashed_password' WHERE correo_electronico = '$reset_email'";
        $result = mysqli_query($conexion, $updatePasswordQuery);

        if ($result) {
            enviarNotificacionCambioContraseña($reset_email); // Llamada a la función
            echo "<div class='message-container alert alert-success'>
                      <h1 class='message text-success'>Contraseña cambiada satisfactoriamente</h1>
                  </div>";
            echo "<script>setTimeout(function() {
                window.location.href = '../auth/login.php';
            }, 3000);</script>";
            exit();
        } else {
            echo "<div class='message-container alert alert-danger'>
                      <h1 class='message text-danger'>Error: Hubo un problema al cambiar la contraseña</h1>
                  </div>";
            echo "<script>setTimeout(function() {
                window.location.href = '../auth/cambio_contraseña.php';
            }, 3000);</script>";
        }
    } else {
        echo "<div class='message-container alert alert-danger'>
                  <h1 class='message text-danger'>Error: Las contraseñas no coinciden</h1>
              </div>";
        echo "<script>setTimeout(function() {
            window.location.href = '../auth/cambio_contraseña.php';
        }, 3000);</script>";
    }
}

mysqli_close($conexion);
?>
