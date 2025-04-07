<?php
session_start();
if (empty($_SESSION["id_usuario"])) {
    header("location: ../auth/login.php");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Predicciones</title>
    <link rel="stylesheet" href="../css/menu.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" />
    <link rel="icon" href="../img/iconologo.jpg" type="image/jpg">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dropdown-toggle::after {
            display: none;
        }

        .nav-item {
            height: 40px;
        }


        .navbar-nav {
            align-items: center;
        }



        .navbar {
            margin-top: 8px;
        }

        .green-box {
            border-radius: 15px;
            height: 100px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 1.5em;
            position: relative;
            font-weight: bold;
            cursor: pointer;
        }

        .green-box img {
            height: 80px;
            width: 80px;
        }

        .number {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 0.8em;
            background: rgba(255, 255, 255, 0.7);
            padding: 5px;
            border-radius: 5px;
            color: black;
        }

        .modal {
            backdrop-filter: blur(55px);
        }

        /* Ajustar el ancho del dropdown */
        #notificationDropdown {
            width: auto;
            /* Ajusta este valor según sea necesario */
            overflow-x: hidden;
        }

        .dropdown-toggle {
            width: 25px;
            /* Ajusta este valor según el tamaño de la campana */
        }

        .notification-close:hover {
            background-color: darkred;
        }

        .header_img {
            display: flex;
            align-items: center;
            /* Centra verticalmente el contenido */
            background-color: black;
            color: white;
            padding: 0.5em 1em;
            /* Añade un padding para dar espacio interno */
            border-radius: 10px;
            /* Define qué tan redondeados serán los bordes */
            width: auto;
        }

        .header_img span,
        .header_img i {
            color: white;
            /* Asegura que el texto y el icono sean blancos */
            font-size: 1.5em;
        }

        .header_img span {
            margin-right: 0.5em;
            /* Añade un pequeño espacio entre el nombre y el ícono */
        }

        /* Asegúrate de que el contenedor tenga suficiente espacio para centrar los botones */
        .button-container {
            margin-top: 100px;
            text-align: center;
            justify-content: center;
            align-items: center;
        }

        /* Asegúrate de que los botones no se desborden y estén correctamente alineados */
        .button-container .btn {
            white-space: nowrap;
            /* Evita el ajuste de línea dentro de los botones */
        }

        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            /* Evita el desplazamiento horizontal */
        }

        body {
            min-height: 100vh;
            /* Esto asegura que el contenido sea suficientemente largo para provocar el scroll */
            overflow-y: auto;
            /* Habilita el desplazamiento vertical */
            position: relative;
        }
        
        
    </style>
</head>

<body id="body-pd" style="background-image: url(../img/fondo7.jpg); background-repeat: no-repeat; background-size: cover; margin: 0; padding: 0; height: 100%; width: 100%; position: relative;">
    <header class="header" id="header">
        <div class="header_toggle">
            <i class="bx bx-menu" id="header-toggle"></i>
        </div>
        <div class="header_camp">
            <!-- Dropdown de notificaciones -->
            <li class="nav-item dropdown" style="list-style: none; margin-top: 20px; margin-right: 60px; position: relative;">
                <button class="btn btn-white dropdown-toggle position-relative" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-bell" style="font-size: 1.5em;"></i>
                    <span id="notificationCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.7em; padding: 0.5em 0.7em;">0</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="dropdownMenuButton1" id="notificationDropdown" style="width: 300px; max-height: 400px; overflow-y: auto; padding-right: 80px;">
                    <li><a class="dropdown-item" href="#" style="font-size: 0.9em;">No hay notificaciones</a></li>
                </ul>
            </li>

            <script>
                function loadNotifications() {
                    fetch('../controlador/get_notifications.php')
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
                                    li.classList.add('dropdown-item');
                                    li.style.fontSize = '0.9em';
                                    li.style.whiteSpace = 'normal'; // Permitir múltiples líneas
                                    li.style.wordWrap = 'break-word'; // Romper palabras largas
                                    li.style.marginLeft = '10px'; // Ajusta el margen izquierdo según sea necesario

                                    li.innerHTML = `
                            ${notification.mensaje}
                            <i class="fa-solid fa-trash notification-close" data-id="${notification.id}" style="color: red; cursor: pointer;"></i>
                        `;
                                    notificationDropdown.appendChild(li);
                                });

                                // Agregar evento para cerrar notificaciones
                                document.querySelectorAll('.notification-close').forEach(button => {
                                    button.addEventListener('click', function(event) {
                                        event.preventDefault(); // Evitar el comportamiento por defecto del enlace

                                        const id = this.getAttribute('data-id');

                                        fetch('../controlador/delete_notification.php', {
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
                                                    this.parentElement.remove();
                                                    // Actualizar el número de notificaciones
                                                    const remainingCount = parseInt(notificationCount.textContent) - 1;
                                                    notificationCount.textContent = remainingCount;

                                                    // Mostrar mensaje si no hay más notificaciones
                                                    if (remainingCount === 0) {
                                                        notificationDropdown.innerHTML = '<li><a class="dropdown-item" href="#" style="font-size: 0.9em;">No hay notificaciones</a></li>';
                                                    }
                                                } else {
                                                    console.error('Error al eliminar la notificación');
                                                }
                                            });
                                    });
                                });
                            } else {
                                notificationDropdown.innerHTML = '<li><a class="dropdown-item" href="#" style="font-size: 0.9em;">No hay notificaciones</a></li>';
                            }
                        })
                        .catch(error => console.error('Error al obtener notificaciones:', error));
                }

                document.addEventListener('DOMContentLoaded', loadNotifications);
            </script>
        </div>
        <div class="header_img">
            <span><?php echo $_SESSION["nombre"]; ?></span>
            <i class="fas fa-user"></i>
        </div>
    </header>
    <div class="l-navbar" id="nav-bar">
        <nav class="nav">
            <div>
                <a href="../index.php" class="nav_logo" data-toggle="tooltip" data-placement="right" title="Inicio">
                    <i class="fa-solid fa-desktop nav_logo-icon"></i>
                    <span class="nav_logo-name">SensorWatch</span>
                </a>
                <div class="nav_list">
                    <a href="suelo.php" class="nav_link" data-toggle="tooltip" data-placement="right" title="Interfaz de Suelo">
                        <i class="fa-solid fa-seedling nav_logo-icon"></i>
                        <span class="nav_name">Suelo</span>
                    </a>
                    <a href="ambiente.php" class="nav_link" data-toggle="tooltip" data-placement="right" title="Interfaz de Ambiente">
                        <i class="fa-solid fa-cloud nav_logo-icon"></i>
                        <span class="nav_name">Ambiente</span>
                    </a>
                    <a href="clima.php" class="nav_link" data-toggle="tooltip" data-placement="right" title="Interfaz Meteorológica">
                        <i class="fa-solid fa-cloud-sun-rain nav_logo-icon"></i>
                        <span class="nav_name">Estación <br>Meteorológica</span>
                    </a>
                    <a href="predicciones.php" class="nav_link active" data-toggle="tooltip" data-placement="right" title="Interfaz de Predicciones">
                        <i class="fa-solid fa-calendar nav_logo-icon"></i>
                        <span class="nav_name">Predicciones</span>
                    </a>
                    <a href="umbral.php" class="nav_link" data-toggle="tooltip" data-placement="right" title="Interfaz de Alarmas">
                        <i class="fa-solid fa-triangle-exclamation nav_logo-icon"></i>
                        <span class="nav_name">Alarmas</span>
                    </a>
                </div>
            </div>
            <a href="../controlador/controlador_cerrar_sesion.php" class="nav_link" data-toggle="tooltip" data-placement="right" title="Cerrar Sesión">
                <i class="bx bx-log-out nav_icon"></i>
                <span class="nav_name">Cerrar Sesion</span>
            </a>
        </nav>
    </div>
    <!--Container Main start-->
    <div class="height-100">
        <div class="container mt-4" style="align-items: center; text-align: center; justify-content: center;">
            <br><br>
            <h1>Predicciones de Datos</h1>
            <br>
            <p>El aplicativo utiliza datos obtenidos de sensores ambientales, de suelo y meteorológicos para realizar predicciones precisas que permiten anticipar cambios en el entorno, optimizar recursos y prevenir riesgos. Gracias al análisis de tendencias históricas y datos en tiempo real, ofrecemos información clave para tomar decisiones informadas de manera eficiente.</p>
            <br>
            <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary mx-2" data-bs-toggle="modal" data-bs-target="#exampleModal">
        Descargar Reportes PDF
    </button>


<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Escoge el reporte que deseas descargar</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <a href="fpdf/reportepresuelo.php" class="btn btn-danger btn-lg me-2" target="_blank">Reporte Suelo <i class="fa-solid fa-file-pdf"></i></a>
        <a href="fpdf/reportepreambiente.php" class="btn btn-danger btn-lg me-2" target="_blank">Reporte Ambiente <i class="fa-solid fa-file-pdf"></i></a><br><br>
        <a href="fpdf/reportepreclima.php" class="btn btn-danger btn-lg me-2" target="_blank">Reporte Meteorológico <i class="fa-solid fa-file-pdf"></i></a>
      </div>
    </div>
  </div>
</div>
<br><br>
            <select onchange="cargarDatos()">
                <option value="suelo">Datos del Suelo</option>
                <option value="ambiente">Datos del Ambiente</option>
                <option value="meteorologico">Datos Meteorológicos</option>
            </select>
            <br><br>
            <canvas id="chart" width="400" height="200"></canvas>

        </div>
    </div>
    <script>
        let grafico = null; // Variable global para almacenar la instancia del gráfico

        async function cargarDatos() {
            const tabla = document.querySelector('select').value; // Obtiene la opción seleccionada
            const response = await fetch(`../api.php?tabla=${tabla}`);
            const data = await response.json();

            if (data.status === "success") {
                const fechas = data.data.map(d => d.created_at || d.date); // Manejar las columnas de fecha dinámicamente
                const columnas = Object.keys(data.data[0]).filter(key => key !== 'created_at' && key !== 'date'); // Filtrar fecha de las columnas

                // Usamos un objeto para almacenar las fechas únicas y acumular los valores
                const fechasUnicas = {};

                // Recorremos los datos y acumulamos los valores por fecha
                data.data.forEach(d => {
                    const fecha = d.created_at || d.date;

                    // Si la fecha no está en el objeto, la inicializamos
                    if (!fechasUnicas[fecha]) {
                        fechasUnicas[fecha] = {};
                        columnas.forEach(columna => {
                            // Inicializamos para cada columna un objeto con sum y count
                            fechasUnicas[fecha][columna] = {
                                sum: 0,
                                count: 0
                            };
                        });
                    }

                    // Acumulamos los valores para la fecha
                    columnas.forEach(columna => {
                        const valor = parseFloat(d[columna] || 0);
                        fechasUnicas[fecha][columna].sum += valor;
                        fechasUnicas[fecha][columna].count += 1;
                    });
                });

                // Convertimos las fechas únicas a un array ordenado
                const fechasOrdenadas = Object.keys(fechasUnicas).sort();

                // Creamos los datasets para el gráfico con los promedios
                const datasets = columnas.map(columna => {
                    return {
                        label: columna,
                        data: fechasOrdenadas.map(fecha => {
                            // Calculamos el promedio
                            const promedio = fechasUnicas[fecha][columna].sum / fechasUnicas[fecha][columna].count;
                            return promedio;
                        }),
                        borderColor: getRandomColor(),
                        borderWidth: 2
                    };
                });

                // Mostrar el gráfico con fechas únicas y datos promedio
                mostrarGrafico(fechasOrdenadas, datasets);
            } else {
                alert(data.message || "Error al cargar los datos");
            }
        }

        // Función para generar colores aleatorios para cada columna
        function getRandomColor() {
            const letters = '0123456789ABCDEF';
            let color = '#';
            for (let i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }

        function mostrarGrafico(fechas, datasets) {
            const ctx = document.getElementById('chart').getContext('2d');

            // Destruir el gráfico existente si existe
            if (grafico) {
                grafico.destroy();
            }

            // Crear un nuevo gráfico
            grafico = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: fechas,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Fecha'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Valor'
                            }
                        }
                    }
                }
            });
        }

        // Cargar datos iniciales
        cargarDatos();
    </script>
    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://kit.fontawesome.com/e9f58d382f.js" crossorigin="anonymous"></script>
    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="../js/menu.js"></script>
</body>

</html>