<?php
require '../modelo/conexion.php';
require '../modelo/email.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["id_usuario"])) {
    header("location: ../login.php");
    exit;
}

// Verificar si el código ya ha sido enviado
if (!isset($_SESSION['codigo_enviado'])) {
    // Enviar el código de verificación si no se ha enviado antes
    $id_usuario = $_SESSION["id_usuario"];
    $sql = $conexion->query("SELECT correo_electronico FROM usuarios WHERE id_usuario=$id_usuario");
    $usuario = $sql->fetch_object();

    if ($usuario) {
        $correo_usuario = $usuario->correo_electronico;
        $codigo_verificacion = rand(10000, 99999); // Generar nuevo código
        $_SESSION['codigo_enviado'] = true; // Marcar que se ha enviado el código
        $_SESSION['codigo_verificacion'] = $codigo_verificacion; // Guardar el código en la sesión

        // Obtener la fecha y hora actuales
        $codigo_timestamp = date('Y-m-d H:i:s');

        // Enviar el código al correo del usuario
        enviarCodigoVerificacion($correo_usuario, $codigo_verificacion);

        // Actualizar el código y la marca de tiempo en la base de datos
        $conexion->query("UPDATE usuarios SET codigo_verificacion='$codigo_verificacion', codigo_timestamp='$codigo_timestamp', intentos_fallidos=0 WHERE id_usuario=$id_usuario");
    }
}

$alertMessage = ''; // Inicializar mensaje de alerta

if (!empty($_POST["btnverificar"])) {
    $codigo_ingresado = $_POST["codigo"];
    $id_usuario = $_SESSION["id_usuario"];

    // Consultar el usuario con la sesión iniciada
    $sql = $conexion->query("SELECT * FROM usuarios WHERE id_usuario=$id_usuario");
    $datos = $sql->fetch_object();

    if ($datos) {
        $_SESSION["nombre"] = $datos->nombre; // Asignar el nombre del usuario a la sesión
        // Verificar si han pasado más de 5 minutos desde que se generó el código
        $fecha_hora_actual = date('Y-m-d H:i:s');
        $diferencia_tiempo = strtotime($fecha_hora_actual) - strtotime($datos->codigo_timestamp);

        if ($diferencia_tiempo > 300) { // 300 segundos = 5 minutos
            $alertMessage = "<div class='alert alert-danger'>El código ha expirado. Por favor, solicita un nuevo código.</div>";
            $_SESSION['codigo_enviado'] = false; // Permitir enviar un nuevo código
        } else {
            // Verificar si los intentos fallidos son mayores a 3
            if ($datos->intentos_fallidos >= 3) {
                $alertMessage = "<div class='alert alert-danger'>Has excedido el número de intentos permitidos. Tu cuenta ha sido desactivada.</div>";
            } else {
                // Verificar si el código coincide
                if ($datos->codigo_verificacion == $codigo_ingresado) {
                    header("location: ../index.php");
                    exit;
                } else {
                    // Incrementar el número de intentos fallidos
                    $intentos = $datos->intentos_fallidos + 1;
                    $conexion->query("UPDATE usuarios SET intentos_fallidos=$intentos WHERE id_usuario=$id_usuario");

                    // Si los intentos llegan a 3, desactivar al usuario
                    if ($intentos >= 3) {
                        $conexion->query("UPDATE usuarios SET estado='Inactivo' WHERE id_usuario=$id_usuario");
                        $alertMessage = "<div class='alert alert-danger'>Has excedido el número de intentos permitidos. Tu cuenta ha sido desactivada.</div>";
                    } else {
                        $alertMessage = "<div class='alert alert-danger'>Código incorrecto. Intentos restantes: " . (3 - $intentos) . "</div>";
                    }
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Verificación de Código</title>
    <link rel="icon" href="img/logo_proyecto.png" type="image/png">
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

<body style="background-image: url('../img/Captura.PNG');">
    <section>
        <form method="post" action="">
            <h1 style="color: white;">Verificación</h1>
            <div class="alert-container">
                <?php echo $alertMessage; // Mostrar mensaje de alerta 
                ?>
            </div>
            <div class="inputbox">
                <input type="text" name="codigo" required>
                <label for="">Ingresa tu código</label>
                <span class="icon"><i class="fa-solid fa-keyboard"></i></span>
            </div>
            <input class="btn" type="submit" value="Verificar" name="btnverificar">
            <br><br>
            <a href="../controlador/controlador_cerrar_sesion.php" class="btn">Regresar</a>
        </form>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>