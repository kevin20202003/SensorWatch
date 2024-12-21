<?php
include "../modelo/conexion.php";
include "../controlador/controlador_registro.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>REGISTRO</title>
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

    .localidad select {
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
        font-size: 16px;
        width: 100%;
    }

    .localidad label {
        color: white;
    }
</style>

<body style="background-image: url('../img/Captura.PNG');">
    <section>
        <form method="post" action="">
            <h1 style="color: white;">Registro</h1>
            <div class="inputbox">
                <input type="text" name="usuario" required>
                <label for="">Usuario</label>
            </div>
            <div class="inputbox">
                <ion-icon name="mail-outline"></ion-icon>
                <input type="email" name="correo_electronico" required>
                <label for="">Correo Electrónico</label>
            </div>
            <div class="inputbox">
                <ion-icon name="lock-closed-outline"></ion-icon>
                <input type="password" name="password" required>
                <label for="">Contraseña</label>
            </div>

            <input class="btn" type="submit" value="Registrarse" name="btningresar">
            <div class="register">
                <p>Ya tienes cuenta? <a href="login.php">Iniciar Sesión</a></p>
            </div>
        </form>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>