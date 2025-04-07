<?php
session_start();
if (empty($_SESSION["id_usuario"])) {
    header("location: auth/login.php");
}
?>
<!doctype html>
<html lang="en">

<head>
    <title>Home</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="img/iconologo.jpg" type="image/jpg">
    <style>
        .navbar-brand img {
            height: 20%;
            width: 40%;
            border-radius: 8px;
            margin-right: 40%;
        }

        .navbar-nav {
            display: flex;
            align-items: center;
            /* Alinea verticalmente los elementos */
        }

        .nav-item {
            margin-left: 10px;
            /* Espacio entre los elementos del menú */
        }

        .navbar-collapse {
            justify-content: flex-end;
            /* Alinea los elementos del menú a la derecha */
        }

        @media (max-width: 991.98px) {
            .navbar-nav {
                flex-direction: row !important;
                /* Mantiene los elementos en una fila */
                margin-top: -7%;
                margin-left: 80%;
                margin-right: 0;
            }

            .nav-item {
                margin-left: 0;
                /* Elimina el espacio entre los elementos */
            }

            .navbar-collapse {
                justify-content: flex-start;
                /* Alinea los elementos del menú a la izquierda */
            }

            .navbar-nav .dropdown .btn {
                font-size: 0.5em;
                /* Ajustar el tamaño de fuente para pantallas más pequeñas */
                padding: 0.3rem 0.5rem;
                /* Ajustar el padding para botones más pequeños */
            }
        }

        @media (max-width: 600px) {
            .navbar-nav {
                flex-direction: row !important;
                /* Mantiene los elementos en una fila */
                margin-top: -11%;
                margin-left: 50%;
            }

        }

        .dropdown-toggle::after {
            display: none;
        }

        .nav-item {
            height: 40px;
        }

        .navbar-nav .dropdown .btn {
            margin-right: 50px;
        }

        .navbar-nav {
            align-items: center;
        }

        .tranding-slide-img i {
            font-size: 80px;
            /* Tamaño del ícono */
            color: black;
            /* Color del ícono */
        }

        /* Ajustar el ancho del dropdown */
        #notificationDropdown {
            margin-left: -300px;
            width: 550px;
            /* Ajusta este valor según sea necesario */
            overflow-x: hidden;
        }

        .notification-close:hover {
            background-color: darkred;
        }
    </style>
</head>

<body style="background-image: url(img/fondo7.jpg); background-repeat: no-repeat; background-size: cover; background-position: center; background-attachment: fixed; min-height: 100vh; margin: 0;">
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="img/logo_proyecto.png" alt="Logo">
            </a>

            <div class="navbar-collapse justify-content-end">
                <ul class="navbar-nav">
                    <!-- Dropdown de notificaciones -->
                    <li class="nav-item dropdown">
                        <button class="btn btn-white dropdown-toggle position-relative" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell" style="font-size: 2.5em;"></i>
                            <span id="notificationCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 1.2em; padding: 0.5em 0.7em;">0</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="dropdownMenuButton1" id="notificationDropdown">
                            <li><a class="dropdown-item" href="#" style="font-size: 1.5em;">No hay notificaciones</a></li>
                        </ul>
                    </li>

                    <script>
                        function loadNotifications() {
                            fetch('controlador/get_notifications.php')
                                .then(response => response.json())
                                .then(data => {
                                    const notificationDropdown = document.getElementById('notificationDropdown');
                                    const notificationCount = document.getElementById('notificationCount');

                                    // Actualizar el número de notificaciones
                                    notificationCount.textContent = data.num_notificaciones;

                                    // Limpiar el contenido actual del dropdown
                                    notificationDropdown.innerHTML = '';

                                    // Agregar notificaciones al dropdown
                                    if (data.num_notificaciones > 0) {
                                        data.notificaciones.forEach(notification => {
                                            const li = document.createElement('li');
                                            li.innerHTML = `
                            <a class="dropdown-item" href="#" style="font-size: 1.5em;">
                                ${notification.mensaje}
                                <i class="fa-solid fa-trash notification-close" data-id="${notification.id}" style="color: red; cursor: pointer;"></i>
                            </a>`;
                                            notificationDropdown.appendChild(li);
                                        });

                                        // Agregar evento para cerrar notificaciones
                                        document.querySelectorAll('.notification-close').forEach(button => {
                                            button.addEventListener('click', function(event) {
                                                event.preventDefault(); // Evitar el comportamiento por defecto del enlace

                                                const id = this.getAttribute('data-id');

                                                fetch('controlador/delete_notification.php', {
                                                        method: 'POST',
                                                        headers: {
                                                            'Content-Type': 'application/json'
                                                        },
                                                        body: JSON.stringify({
                                                            id: id
                                                        })
                                                    })
                                                    .then(response => response.json())
                                                    .then(data => {
                                                        if (data.success) {
                                                            // Eliminar la notificación del dropdown
                                                            this.parentElement.parentElement.remove();
                                                            // Actualizar el número de notificaciones
                                                            const remainingCount = parseInt(notificationCount.textContent) - 1;
                                                            notificationCount.textContent = remainingCount;

                                                            // Mostrar mensaje si no hay más notificaciones
                                                            if (remainingCount === 0) {
                                                                notificationDropdown.innerHTML = '<li><a class="dropdown-item" href="#" style="font-size: 1.5em;">No hay notificaciones</a></li>';
                                                            }
                                                        } else {
                                                            console.error('Error al eliminar la notificación');
                                                        }
                                                    });
                                            });
                                        });
                                    } else {
                                        notificationDropdown.innerHTML = '<li><a class="dropdown-item" href="#" style="font-size: 1.5em;">No hay notificaciones</a></li>';
                                    }
                                })
                                .catch(error => console.error('Error al obtener notificaciones:', error));
                        }

                        document.addEventListener('DOMContentLoaded', loadNotifications);
                    </script>
                    <li class="nav-item dropdown">
                        <button class="btn btn-dark dropdown-toggle" type="button" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
                            <span style="font-size: 2em;"><?php echo $_SESSION["nombre"]; ?></span>
                            <i class="fas fa-user" style="font-size: 2em;"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="dropdownMenuButton2">
                            <li><a class="dropdown-item" href="cambiar_contraseña.php" style="font-size: 1.5em;">Cambiar Contraseña</a></li>
                            <li><a class="dropdown-item" href="controlador/controlador_cerrar_sesion.php" style="font-size: 1.5em;">Cerrar Sesión</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Slider de opciones -->
    <section id="tranding">
        <div class="container">
            <h1 class="text-center section-subheading">MONITOREO INTELIGENTE DE SENSORES AMBIENTALES</h1>
        </div>
        <br><br><br>
        <div class="container">
            <div class="swiper tranding-slider">
                <div class="swiper-wrapper">
                    <!-- Slide-start -->
                    <div class="swiper-slide tranding-slide">
                        <a href="vistas/suelo.php" style="text-decoration: none;">
                            <div class="tranding-slide-img" style="height: 200px; width: 200px;">
                                <i class="fa-solid fa-seedling"></i>
                            </div>
                            <div class="tranding-slide-content">
                                <div class="tranding-slide-content-bottom">
                                </div>
                            </div>
                        </a>
                        <h1 class="food-name text-center">
                            Suelo
                        </h1>
                    </div>
                    <!-- Slide-end -->
                    <!-- Slide-start -->
                    <div class="swiper-slide tranding-slide">
                        <a href="vistas/ambiente.php" style="text-decoration: none;">
                            <div class="tranding-slide-img" style="height: 200px; width: 200px;">
                                <i class="fa-solid fa-cloud"></i>
                            </div>
                            <div class="tranding-slide-content">
                                <div class="tranding-slide-content-bottom">
                                </div>
                            </div>
                        </a>
                        <h1 class="food-name text-center">
                            Ambiente
                        </h1>
                    </div>
                    <!-- Slide-end -->
                    <!-- Slide-start -->
                    <div class="swiper-slide tranding-slide">
                        <a href="vistas/clima.php" style="text-decoration: none;">
                            <div class="tranding-slide-img" style="height: 200px; width: 200px;">
                                <i class="fa-solid fa-cloud-sun-rain"></i>
                            </div>
                            <div class="tranding-slide-content">
                                <div class="tranding-slide-content-bottom">
                                </div>
                            </div>
                        </a>
                        <h1 class="food-name text-center">
                            Estación <br>
                            Meteorológica
                        </h1>
                    </div>
                    <!-- Slide-end -->
                    <!-- Slide-start -->
                    <div class="swiper-slide tranding-slide">
                        <a href="vistas/umbral.php" style="text-decoration: none;">
                            <div class="tranding-slide-img" style="height: 200px; width: 200px;">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                            <div class="tranding-slide-content">
                                <div class="tranding-slide-content-bottom">
                                </div>
                            </div>
                        </a>
                        <h1 class="food-name text-center">
                            Alarmas
                        </h1>
                    </div>
                    <!-- Slide-end -->
                    <!-- Slide-start -->
                    <div class="swiper-slide tranding-slide">
                        <a href="vistas/predicciones.php" style="text-decoration: none;">
                            <div class="tranding-slide-img" style="height: 200px; width: 200px;">
                                <i class="fa-solid fa-calendar"></i>
                            </div>
                            <div class="tranding-slide-content">
                                <div class="tranding-slide-content-bottom">
                                </div>
                            </div>
                        </a>
                        <h1 class="food-name text-center">
                            Predicciones
                        </h1>
                    </div>
                    <!-- Slide-end -->
                </div>

                <div class="tranding-slider-control">
                    <div class="swiper-button-prev slider-arrow">
                        <ion-icon name="arrow-back-outline"></ion-icon>
                    </div>
                    <div class="swiper-button-next slider-arrow">
                        <ion-icon name="arrow-forward-outline"></ion-icon>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </div>
    </section>
    <!-- Footer -->
    <footer>
        <div class="left">
            <p>&copy; Proyecto desarrollado por Kevin Chala <br> &copy; Instituto Tecnológico Quito 2025</p>
        </div>
        <div class="right">
            <ul>
                <li class="item">
                    <a href="https://www.instagram.com/kchala900/">
                        <i class="fa-brands fa-instagram icon"></i>
                    </a>
                </li>
                <li class="item">
                    <a href="https://www.linkedin.com/in/kevin-chal%C3%A1-74a55a28a/">
                        <i class="fa-brands fa-linkedin icon"></i>
                    </a>
                </li>
                <li class="item">
                    <a href="https://www.facebook.com/kevin.chala.501">
                        <i class="fa-brands fa-facebook icon"></i>
                    </a>
                </li>
                <li class="item">
                    <a href="https://x.com/KevinChal10">
                        <i class="fa-brands fa-x-twitter icon"></i>
                    </a>
                </li>
            </ul>
        </div>
    </footer>

    <script src="https://kit.fontawesome.com/e9f58d382f.js" crossorigin="anonymous"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
    <script src="js/script.js"></script>
    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js" integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz" crossorigin="anonymous">
    </script>
</body>

</html>