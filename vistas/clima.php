<?php
session_start();
if (empty($_SESSION["id_usuario"])) {
    header("location: ../auth/login.php");
}
?>

<?php
require '../modelo/conexion.php';

$id_usuario = $_SESSION['id_usuario'];

// Obtener los datos del umbral para el usuario actual
$sql_umbral = "SELECT * FROM umbral_meteorologicos WHERE id_usuario = $id_usuario";
$result_umbral = $conexion->query($sql_umbral);

// Procesar cada umbral
while ($umbral = $result_umbral->fetch_assoc()) {
    $humedad_min = $umbral['humedad_min'];
    $humedad_max = $umbral['humedad_max'];
    $temperatura_min = $umbral['temperatura_min'];
    $temperatura_max = $umbral['temperatura_max'];
    $presion_min = $umbral['presion_min'];
    $presion_max = $umbral['presion_max'];

    // Obtener los datos más recientes de la tabla datos_suelo
    $sql_datos = "SELECT * FROM datos_meteorologicos ORDER BY date DESC LIMIT 1";
    $result_datos = $conexion->query($sql_datos);

    if ($result_datos->num_rows > 0) {
        $datos = $result_datos->fetch_assoc();
        $humedad = $datos['humidity'];
        $temperatura = $datos['temp'];
        $presion = $datos['pressure'];

        // Verificar los umbrales y preparar notificaciones
        $notificaciones = [];
        if ($humedad < $humedad_min) {
            $notificaciones[] = "Swal.fire({icon: 'warning', title: 'Humedad Baja', text: 'La humedad está por debajo del umbral mínimo.'})";
            $mensaje = "La humedad está por debajo del umbral mínimo. (Sensor clima)";
            $sql_insert = "INSERT INTO notificaciones (id_usuario, mensaje) VALUES ('$id_usuario', '$mensaje')";
            $conexion->query($sql_insert);
        }
        if ($humedad > $humedad_max) {
            $notificaciones[] = "Swal.fire({icon: 'warning', title: 'Humedad Alta', text: 'La humedad está por encima del umbral máximo.'})";
            $mensaje = "La humedad está por encima del umbral máximo. (Sensor clima)";
            $sql_insert = "INSERT INTO notificaciones (id_usuario, mensaje) VALUES ('$id_usuario', '$mensaje')";
            $conexion->query($sql_insert);
        }
        if ($temperatura < $temperatura_min) {
            $notificaciones[] = "Swal.fire({icon: 'warning', title: 'Temperatura Baja', text: 'La temperatura está por debajo del umbral mínimo.'})";
            $mensaje = "La temperatura está por debajo del umbral mínimo. (Sensor clima)";
            $sql_insert = "INSERT INTO notificaciones (id_usuario, mensaje) VALUES ('$id_usuario', '$mensaje')";
            $conexion->query($sql_insert);
        }
        if ($temperatura > $temperatura_max) {
            $notificaciones[] = "Swal.fire({icon: 'warning', title: 'Temperatura Alta', text: 'La temperatura está por encima del umbral máximo.'})";
            $mensaje = "La temperatura está por encima del umbral máximo. (Sensor clima)";
            $sql_insert = "INSERT INTO notificaciones (id_usuario, mensaje) VALUES ('$id_usuario', '$mensaje')";
            $conexion->query($sql_insert);
        }
        if ($presion < $presion_min) {
            $notificaciones[] = "Swal.fire({icon: 'warning', title: 'Presion Baja', text: 'La presion está por debajo del umbral mínimo.'});";
            $mensaje = "La presion está por debajo del umbral mínimo. (Sensor clima)";
            $sql_insert = "INSERT INTO notificaciones (id_usuario, mensaje) VALUES ('$id_usuario', '$mensaje')";
            $conexion->query($sql_insert);
        }
        if ($presion > $presion_max) {
            $notificaciones[] = "Swal.fire({icon: 'warning', title: 'Presion Alta', text: 'La presion está por encima del umbral máximo.'});";
            $mensaje = "La presion está por encima del umbral máximo. (Sensor clima)";
            $sql_insert = "INSERT INTO notificaciones (id_usuario, mensaje) VALUES ('$id_usuario', '$mensaje')";
            $conexion->query($sql_insert);
        }

        // Generar el script de notificaciones encadenadas
        $script = "";
        if (!empty($notificaciones)) {
            $script .= "document.addEventListener('DOMContentLoaded', function() {";
            $script .= "function mostrarNotificaciones(index) {";
            $script .= "if (index < notificaciones.length) {";
            $script .= "eval(notificaciones[index]).then(function() {";
            $script .= "mostrarNotificaciones(index + 1);";
            $script .= "});";
            $script .= "}";
            $script .= "}";
            $script .= "var notificaciones = " . json_encode($notificaciones) . ";";
            $script .= "mostrarNotificaciones(0);";
            $script .= "});";
        }

        // Mostrar las notificaciones si es necesario
        if ($script != "") {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>$script</script>";
        }
    }
}


?>

<?php
// Función para calcular la varianza
function calcularVarianza($datos)
{
    $n = count($datos);
    if ($n === 0) {
        return null;
    }

    $media = array_sum($datos) / $n;
    $sumaCuadrados = 0;

    foreach ($datos as $valor) {
        $sumaCuadrados += pow($valor - $media, 2);
    }

    $varianza = $sumaCuadrados / $n;
    return $varianza;
}

// Obtener los datos reales para los últimos 20 registros
$stmt = $conexion->prepare("
    SELECT 
        DATE_FORMAT(date, '%Y-%m-%d %H:00:00') AS hora,
        AVG(temp) AS temp,
        AVG(humidity) AS humidity,
        AVG(pressure) AS pressure,
        AVG(wind_speed) AS wind_speed
    FROM 
        datos_meteorologicos
    GROUP BY 
        hora
    ORDER BY 
        hora DESC
    LIMIT 20
");

$stmt->execute();
$result = $stmt->get_result();

$temperatures = array();
$humidities = array();
$pressures = array();
$uvs = array();
$labels = array();

while ($row = $result->fetch_assoc()) {
    $temperatures[] = $row['temp'];
    $humidities[] = $row['humidity'];
    $pressures[] = $row['pressure'];
    $uvs[] = $row['wind_speed'];
    $labels[] = $row['hora']; // Formatea la hora (por ejemplo, "10:00")
}

$stmt->close();

// Calcular varianza para cada conjunto de datos
$varianza_temperaturas = calcularVarianza($temperatures);
$varianza_humedades = calcularVarianza($humidities);
$varianza_pressures = calcularVarianza($pressures);
$varianza_uvs = calcularVarianza($uvs);

// Cerrar la conexión
$conexion->close();

// Convertir los datos en formato JSON para ser utilizados en el JavaScript
$datos = [
    'temperatures' => $temperatures,
    'humidities' => $humidities,
    'pressures' => $pressures,
    'uvs' => $uvs,
    'labels' => $labels,
    'varianza_temperaturas' => $varianza_temperaturas,
    'varianza_humedades' => $varianza_humedades,
    'varianza_pressures' => $varianza_pressures,
    'varianza_uvs' => $varianza_uvs
];

echo "<script> var chartData = " . json_encode($datos) . ";</script>";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Meteorologico</title>
    <link rel="stylesheet" href="../css/menu.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" />
    <link rel="icon" href="../img/iconologo.jpg" type="image/jpg">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Función para obtener los datos de la API PHP y actualizar la pantalla
        function obtenerDatosClimaticos() {
            fetch('../obtener_datos_climaticos.php') // Llamada al archivo PHP que obtiene los datos
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                    } else {
                        // Actualizar los elementos HTML con los nuevos datos
                        document.getElementById('temperatura').innerText = data.temperatura + " °C";
                        document.getElementById('humedad').innerText = data.humedad + " %";
                        document.getElementById('presion').innerText = data.presion + " hPa";
                        document.getElementById('viento').innerText = data.viento + " m/s";
                    }
                })
                .catch(error => {
                    console.error('Error al obtener los datos:', error);
                });
        }

        // Llamar a la función de obtener datos cada 20 segundos
        setInterval(obtenerDatosClimaticos, 20000); // Cada 20 segundos

        // Llamar la función de inmediato cuando cargue la página
        window.onload = obtenerDatosClimaticos;
    </script>

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
            height: 150px;
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
            height: 72px;
            width: 72px;
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

        /* Ocultar scrollbar en navegadores WebKit (Chrome, Safari) */
        .content::-webkit-scrollbar {
            width: 0;
            /* Ancho del scrollbar */
            background: transparent;
            /* Fondo transparente */
        }

        /* Ocultar scrollbar en Firefox */
        .content {
            scrollbar-width: none;
            /* Oculta scrollbar en Firefox */
            -ms-overflow-style: none;
            /* Oculta scrollbar en IE y Edge */
        }

        /* Asegurar que el contenido siga siendo desplazable */
        .content {
            overflow: auto;
            /* Permitir desplazamiento */
            max-width: 100%;
            max-height: 100%;
            padding: 20px;
            box-sizing: border-box;
            width: 100%;
        }
    </style>
</head>

<body id="body-pd" style="background-image: url(../img/fondo7.jpg); background-repeat: no-repeat;
    background-size: cover;
    background-position: center;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    overflow: hidden;
    width: 100%;">
    <div class="content">
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
                    <a href="clima.php" class="nav_link active" data-toggle="tooltip" data-placement="right" title="Interfaz Meteorológica">
                        <i class="fa-solid fa-cloud-sun-rain nav_logo-icon"></i>
                        <span class="nav_name">Estación <br>Meteorológica</span>
                    </a>
                    <a href="predicciones.php" class="nav_link" data-toggle="tooltip" data-placement="right" title="Interfaz de Predicciones">
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
            <div class="container mt-4">
                <!-- Botón para abrir el modal -->
                <div class="button-container">
                    <button type="button" class="btn btn-primary btn-lg me-2" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        Definir alarma <i class="fa-solid fa-clock"></i>
                    </button>
                    <a href="fpdf/reporteclima.php" class="btn btn-danger btn-lg me-2" target="_blank">Generar Reporte <i class="fa-solid fa-file-pdf"></i></a>
                    <a href="../vendor/reporteexcel3.php" class="btn btn-success btn-lg me-2" target="_blank">Generar Reporte <i class="fa-solid fa-file-excel"></i></a>
                </div>
                <br><br>
                <div class="row" style="align-items: center; text-align: center; justify-content: center;">
                    <div class="col-md-3">
                        <div class="green-box" data-bs-toggle="modal" data-bs-target="#chartModalLuz" data-variable="Luz">
                            <img src="../img/viento.png" alt="">
                            <span class="number" id="viento">Cargando...</span>
                        </div>
                        <br>
                        <span style="font-weight: bold; font-size: large;">Velocidad del viento</span>
                    </div>
                    <div class="col-md-3">
                        <div class="green-box" data-bs-toggle="modal" data-bs-target="#chartModalHumedad" data-variable="Humedad">
                            <img src="../img/humedad.png" alt="">
                            <span class="number" id="humedad">Cargando...</span>
                        </div>
                        <br>
                        <span style="font-weight: bold; font-size: large;">Nivel de humedad externa</span>
                    </div>
                    <div class="col-md-3">
                        <div class="green-box" data-bs-toggle="modal" data-bs-target="#chartModalTemperatura" data-variable="Temperatura">
                            <img src="../img/temperatura.png" alt="">
                            <span class="number" id="temperatura">Cargando...</span>
                        </div>
                        <br>
                        <span style="font-weight: bold; font-size: large;">Temperatura externa</span>
                    </div>
                    <div class="col-md-3">
                        <div class="green-box" data-bs-toggle="modal" data-bs-target="#chartModalPresion" data-variable="Presion">
                            <img src="../img/presion.png" alt="">
                            <span class="number" id="presion">Cargando...</span>
                        </div>
                        <br>
                        <span style="font-weight: bold; font-size: large;">Presión atmosferica</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- Modales para mostrar gráficos -->
    <!-- Modal para Viento -->
    <div class="modal fade" id="chartModalLuz" tabindex="-1" aria-labelledby="chartModalLuzLabel" aria-hidden="true" data-bs-backdrop="false">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="chartModalLuzLabel">Gráfico de Viento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <canvas id="chartCanvasLuz"></canvas>
                    <br>
                    <p id="varianzaLuz" style="font-weight: bold; font-size: large;"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Humedad -->
    <div class="modal fade" id="chartModalHumedad" tabindex="-1" aria-labelledby="chartModalHumedadLabel" aria-hidden="true" data-bs-backdrop="false">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="chartModalHumedadLabel">Gráfico de Humedad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <canvas id="chartCanvasHumedad"></canvas>
                    <br>
                    <p id="varianzaHumedad" style="font-weight: bold; font-size: large;"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Temperatura -->
    <div class="modal fade" id="chartModalTemperatura" tabindex="-1" aria-labelledby="chartModalTemperaturaLabel" aria-hidden="true" data-bs-backdrop="false">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="chartModalTemperaturaLabel">Gráfico de Temperatura</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <canvas id="chartCanvasTemperatura"></canvas>
                    <br>
                    <p id="varianzaTemperatura" style="font-weight: bold; font-size: large;"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Presion -->
    <div class="modal fade" id="chartModalPresion" tabindex="-1" aria-labelledby="chartModalPresionLabel" aria-hidden="true" data-bs-backdrop="false">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="chartModalPresionLabel">Gráfico de Presion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <canvas id="chartCanvasPresion"></canvas>
                    <br>
                    <p id="varianzaPresion" style="font-weight: bold; font-size: large;"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal para definir alarma -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Definir Alarma</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="alarmForm">
                        <div class="mb-3">
                            <label for="humedad_min" class="form-label">Humedad Mínima (%)</label>
                            <input type="number" class="form-control" id="humedad_min" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="humedad_max" class="form-label">Humedad Máxima (%)</label>
                            <input type="number" class="form-control" id="humedad_max" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="temperatura_min" class="form-label">Temperatura Mínima (°C)</label>
                            <input type="number" class="form-control" id="temperatura_min" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="temperatura_max" class="form-label">Temperatura Máxima (°C)</label>
                            <input type="number" class="form-control" id="temperatura_max" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="presion_min" class="form-label">Presion Mínima (°C)</label>
                            <input type="number" class="form-control" id="presion_min" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="presion_max" class="form-label">Presion Máxima (°C)</label>
                            <input type="number" class="form-control" id="presion_max" step="0.01" required>
                        </div>
                        <input type="hidden" id="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary" id="saveAlarm">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('saveAlarm').addEventListener('click', function() {
            var humedad_min = document.getElementById('humedad_min').value;
            var humedad_max = document.getElementById('humedad_max').value;
            var temperatura_min = document.getElementById('temperatura_min').value;
            var temperatura_max = document.getElementById('temperatura_max').value;
            var presion_min = document.getElementById('presion_min').value;
            var presion_max = document.getElementById('presion_max').value;
            var id_usuario = document.getElementById('id_usuario').value;

            // Validar campos
            if (humedad_min === '' || humedad_max === '' || temperatura_min === '' || temperatura_max === '' || presion_min === '' || presion_max === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Todos los campos deben ser llenados!',
                });
                return;
            }

            // Enviar datos al servidor
            fetch('../controlador/save_alarm_clima.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        humedad_min: humedad_min,
                        humedad_max: humedad_max,
                        temperatura_min: temperatura_min,
                        temperatura_max: temperatura_max,
                        presion_min: presion_min,
                        presion_max: presion_max,
                        id_usuario: id_usuario
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Guardado',
                            text: 'La alarma se ha definido correctamente!',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Recargar la página si el usuario hace clic en OK
                                location.reload();
                            }
                        });
                        // Cerrar modal
                        var myModal = new bootstrap.Modal(document.getElementById('exampleModal'));
                        myModal.hide();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Hubo un problema al guardar la alarma.',
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Hubo un problema con la conexión.',
                    });
                });
        });
    </script>

    <!-- Graficas -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Modal y gráficos para todas las categorías (Luz, Humedad, Temperatura, Presión)
            const graphData = {
                'Luz': {
                    id: 'chartModalLuz',
                    canvasId: 'chartCanvasLuz',
                    varianzaId: 'varianzaLuz',
                    dataKey: 'uvs',
                    label: 'Viento',
                    varianzaKey: 'varianza_uvs'
                },
                'Humedad': {
                    id: 'chartModalHumedad',
                    canvasId: 'chartCanvasHumedad',
                    varianzaId: 'varianzaHumedad',
                    dataKey: 'humidities',
                    label: 'Humedad',
                    varianzaKey: 'varianza_humedades'
                },
                'Temperatura': {
                    id: 'chartModalTemperatura',
                    canvasId: 'chartCanvasTemperatura',
                    varianzaId: 'varianza_temperaturas',
                    dataKey: 'temperatures',
                    label: 'Temperatura',
                    varianzaKey: 'varianza_temperaturas'
                },
                'Presion': {
                    id: 'chartModalPresion',
                    canvasId: 'chartCanvasPresion',
                    varianzaId: 'varianzaPresion',
                    dataKey: 'pressures',
                    label: 'Presion',
                    varianzaKey: 'varianza_pressures'
                }
            };

            // Variable global para los gráficos
            var charts = {};

            // Función para configurar y mostrar el gráfico
            function setupAndShowChart(data, labels, varianza, chartConfig) {
                // Destruir el gráfico anterior si existe
                if (charts[chartConfig.label]) {
                    charts[chartConfig.label].destroy();
                }

                // Configurar el nuevo gráfico
                var ctx = document.getElementById(chartConfig.canvasId).getContext('2d');
                charts[chartConfig.label] = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: chartConfig.label,
                            data: data,
                            borderColor: 'rgb(75, 192, 192)',
                            tension: 0.1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Mostrar la varianza en el modal
                var varianzaElement = document.getElementById(chartConfig.varianzaId);
                if (varianzaElement) {
                    varianzaElement.textContent = 'Varianza: ' + varianza.toFixed(2); // Ajusta el formato de la varianza según sea necesario
                }
            }

            // Asignar evento clic a todos los contenedores green-box
            var greenBoxes = document.querySelectorAll('.green-box');
            greenBoxes.forEach(function(box) {
                box.addEventListener('click', function() {
                    var modalTarget = this.getAttribute('data-bs-target');
                    var modal = new bootstrap.Modal(document.querySelector(modalTarget));
                    modal.show();

                    // Obtener los datos del gráfico correspondiente
                    const category = this.getAttribute('data-category'); // Asegúrate de asignar el atributo "data-category" a los contenedores
                    const chartConfig = graphData[category];

                    // Obtener los datos específicos de la categoría desde chartData
                    const data = chartData[chartConfig.dataKey];
                    const labels = chartData.labels;
                    const varianza = chartData[chartConfig.varianzaKey]; // Obtener la varianza desde chartData

                    // Mostrar el gráfico al abrir el modal
                    setupAndShowChart(data, labels, varianza, chartConfig);
                });
            });

            // Evento shown.bs.modal para actualizar el gráfico si es necesario
            Object.keys(graphData).forEach(category => {
                const modalId = graphData[category].id;
                document.getElementById(modalId).addEventListener('shown.bs.modal', function() {
                    const chartConfig = graphData[category];
                    const data = chartData[chartConfig.dataKey];
                    const labels = chartData.labels;
                    const varianza = chartData[chartConfig.varianzaKey]; // Obtener la varianza desde chartData

                    setupAndShowChart(data, labels, varianza, chartConfig);
                });
            });
        });
    </script>
    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://kit.fontawesome.com/e9f58d382f.js" crossorigin="anonymous"></script>
    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="../js/menu.js"></script>
    <?php
    include '../controlador/enviar_alerta_clima.php';
    ?>
</body>

</html>