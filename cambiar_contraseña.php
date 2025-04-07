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
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir conexión a la base de datos
require("modelo/conexion.php");

// Obtener el id_usuario desde la sesión
$id_usuario = $_SESSION["id_usuario"];

// Obtener el correo electrónico del usuario desde la base de datos
$sql = $conexion->query("SELECT correo_electronico FROM usuarios WHERE id_usuario=$id_usuario");
$usuario = $sql->fetch_object();
$correo_electronico = $usuario->correo_electronico;

// Configuración de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Función para enviar el correo de notificación sobre el cambio de contraseña
function enviarNotificacionCambioContraseña($correo) {
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'sensorwatch99@gmail.com'; // Cambia a tu correo
        $mail->Password   = 'yrwk zuzt jifl tnhs';       // Cambia a tu contraseña
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Configuración SSL opcional
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Remitente y destinatario
        $mail->setFrom('sensorwatch99@gmail.com', 'SensorWatch');
        $mail->addAddress($correo);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'Cambio de contraseña realizado';
        $mail->Body    = "
            <h1>Cambio de contraseña exitoso</h1>
            <p>Hola,</p>
            <p>Te informamos que tu contraseña ha sido cambiada exitosamente.</p>
            <p>Si no realizaste este cambio, por favor, ponte en contacto con el administrador del sistema o con el autor del programa a la brevedad.</p>
            <p>Atentamente,<br>El equipo de SensorWatch</p>
        ";

        $mail->send();
    } catch (Exception $e) {
        echo "Error al enviar la notificación: {$mail->ErrorInfo}";
    }
}

// Verificar si se ha enviado el formulario
if (isset($_POST['change_password'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Verificar que las contraseñas coinciden
    if ($new_password === $confirm_password) {
        $id_usuario = $_SESSION['id_usuario'];  // Usamos 'id_usuario' en vez de 'reset_email'

        // Verificar si el id_usuario está en sesión
        if (!$id_usuario) {
            echo "<div class='message-container alert alert-danger'>
                      <h1 class='message text-danger'>Error: Sesión expirada. Por favor vuelva a intentarlo.</h1>
                  </div>";
            echo "<script>setTimeout(function() {
                window.location.href = 'auth/login.php';  // Redirigir al login
            }, 3000);</script>";
            exit();
        }

        // Encriptar la nueva contraseña
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        // Actualizar la contraseña en la base de datos
        $updatePasswordQuery = "UPDATE usuarios SET password = '$hashed_password' WHERE id_usuario = '$id_usuario'";  // Cambié el filtro de correo a id_usuario
        $result = mysqli_query($conexion, $updatePasswordQuery);

        if ($result) {
            // Ahora pasamos el correo electrónico del usuario a la función
            enviarNotificacionCambioContraseña($correo_electronico); // Llamada a la función para notificar el cambio
            echo "<div class='message-container alert alert-success'>
                      <h1 class='message text-success'>Contraseña cambiada satisfactoriamente</h1>
                  </div>";
            echo "<script>setTimeout(function() {
                window.location.href = 'index.php';  // Redirigir al home
            }, 3000);</script>";
            exit();
        } else {
            echo "<div class='message-container alert alert-danger'>
                      <h1 class='message text-danger'>Error: Hubo un problema al cambiar la contraseña</h1>
                  </div>";
            echo "<script>setTimeout(function() {
                window.location.href = 'cambiar_contraseña.php';  // Redirigir de nuevo a la página de cambio de contraseña
            }, 3000);</script>";
        }
    } else {
        echo "<div class='message-container alert alert-danger'>
                  <h1 class='message text-danger'>Error: Las contraseñas no coinciden</h1>
              </div>";
        echo "<script>setTimeout(function() {
            window.location.href = 'cambiar_contraseña.php';  // Redirigir de nuevo a la página de cambio de contraseña
        }, 3000);</script>";
    }
}

mysqli_close($conexion);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Cambio Contraseña</title>
    <link rel="icon" href="img/iconologo.jpg" type="image/jpg">
    <style>
        .alert-container {
            margin-bottom: 15px;
            /* Espacio entre el mensaje y el input */
        }

        .btn {
            width: 100%;
            height: 40px;
            border-radius: 40px;
            background-color: rgb(255, 255, 255, 1);
            border: none;
            outline: none;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.4s ease;
        }

        .btn:hover {
            background-color: rgb(255, 255, 255, 0.5);
        }

        .inputbox {
            position: relative;
            margin: 30px 0;
            max-width: 310px;
            border-bottom: 2px solid #fff;
        }

        .inputbox label {
            position: absolute;
            top: 50%;
            left: 5px;
            transform: translateY(-50%);
            color: #fff;
            font-size: 1rem;
            pointer-events: none;
            transition: all 0.5s ease-in-out;
        }

        .inputbox input:focus~label,
        .inputbox input:valid~label {
            top: -5px;
        }

        .inputbox input {
            width: 100%;
            height: 60px;
            background: transparent;
            border: none;
            outline: none;
            font-size: 1rem;
            padding: 0 35px 0 5px;
            /* Ajustar el espacio para el ícono */
            color: #fff;
        }

        .inputbox .icon {
            position: absolute;
            top: 50%;
            right: 0;
            /* Posicionar a la derecha */
            transform: translateY(-50%);
            font-size: 1.2rem;
            /* Tamaño del ícono */
            color: #fff;
            /* Color del ícono */
            pointer-events: none;
            /* Evitar interacción */
        }
    </style>
</head>

<body style="background-image: url('img/fondo7.jpg');">
    <section>
        <form method="post" action="">
            <h1 style="color: white;">Contraseña nueva</h1>
            <div class="inputbox">
                <input type="password" name="new_password" id="new_password" required>
                <label for="">Ingresa tu nueva contraseña</label>
                <span class="icon"><i class="fa-solid fa-lock"></i></span>
            </div>
            <div class="inputbox">
                <input type="password" name="confirm_password" id="confirm_password" required>
                <label for="">Repite la contraseña</label>
                <span class="icon"><i class="fa-solid fa-lock"></i></span>
            </div>
            <input class="btn" type="submit" value="Confirmar" name="change_password">
            <br><br>
            <a href="index.php" class="btn">Regresar</a>
        </form>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Deshabilitar el mensaje de confirmación de reenvío de formulario
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
    <script src="https://kit.fontawesome.com/e9f58d382f.js" crossorigin="anonymous"></script>
</body>

</html>
