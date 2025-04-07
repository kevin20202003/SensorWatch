<?php
include "../modelo/conexion.php";
include "../controlador/controlador_login.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>LOGIN</title>
    <link rel="icon" href="../img/logo_proyecto.png" type="image/png">
</head>
<style>
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

<body style="background-image: url('../img/Captura.PNG');">
    <section>
        <form method="post" action="">
            <h1 style="color: white;">Login</h1>
            <div class="inputbox">
                <input type="text" name="usuario" required>
                <label for="">Usuario</label>
                <span class="icon"><i class="fa-solid fa-user"></i></span>
            </div>
            <div class="inputbox">
                <input type="password" name="password" required>
                <label for="">Contraseña</label>
                <span class="icon"><i class="fa-solid fa-lock"></i></span>
            </div>
            <div class="forget">
                <label for=""><input type="checkbox">Recordar</label>
                <a href="verificar_correo.php">Olvidé la Contraseña</a>
            </div>
            <input class="btn" type="submit" value="Iniciar sesion" name="btningresar">
            <div class="register">
                <p>No tienes cuenta? <a href="registro.php">Registrarse</a></p>
            </div>
        </form>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        // Deshabilitar el mensaje de confirmación de reenvío de formulario
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
    <script src="https://kit.fontawesome.com/e9f58d382f.js" crossorigin="anonymous"></script>
</body>

</html>