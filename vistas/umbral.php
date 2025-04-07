<?php
session_start();
if (empty($_SESSION["id_usuario"])) {
    header("location: ../auth/login.php");
    exit();
}

require '../modelo/conexion.php';

$id_usuario = isset($_SESSION['id_usuario']) ? intval($_SESSION['id_usuario']) : 0;

// Tablas disponibles
$tables = ['umbral_suelo', 'umbral_ambiente', 'umbral_meteorologicos'];

// Eliminar registro si se ha solicitado
if (isset($_GET['delete']) && isset($_GET['table']) && isset($_GET['id'])) {
    $table = $_GET['table'];
    $id = intval($_GET['id']);
    $sql_delete = "DELETE FROM $table WHERE id = $id AND id_usuario = $id_usuario";
    $conexion->query($sql_delete);
}

// Obtener tabla seleccionada
$selected_table = isset($_POST['table']) ? $_POST['table'] : '';
$results = null;

// Consultar datos según la tabla seleccionada
if (!empty($selected_table) && in_array($selected_table, $tables)) {
    $sql = "SELECT * FROM $selected_table WHERE id_usuario = $id_usuario";
    $results = $conexion->query($sql);
} elseif (empty($selected_table)) {
    // Opcional: obtener todas las tablas si no hay una seleccionada
    $results = [];
    foreach ($tables as $table) {
        $sql = "SELECT * FROM $table WHERE id_usuario = $id_usuario";
        $results[$table] = $conexion->query($sql);
    }
}


// Cerrar conexión
$conexion->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Alarmas</title>
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

        .form-inline {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .form-inline select,
        .form-inline input,
        .form-inline button {
            margin: 0 10px;
        }

        .btn-delete {
            width: auto;
            height: auto;
            background-color: red;
            color: white;
            text-align: center;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-delete:hover {
            background-color: darkred;
        }

        .badge {
            font-size: 1.2em;
        }
        
        .table-responsive {
    overflow-x: auto; /* Asegura scroll horizontal en dispositivos pequeños */
}

.table th, .table td {
    text-align: center; /* Centra los datos horizontalmente */
    vertical-align: middle; /* Centra los datos verticalmente */
    word-wrap: break-word; /* Ajusta el texto en caso de ser largo */
}

.table th {
    background-color: #f8f9fa; /* Fondo más claro para encabezados */
    font-weight: bold; /* Negrita para encabezados */
}
    </style>
</head>

<body id="body-pd" style="background-image: url(../img/fondo7.jpg); background-repeat: no-repeat; background-size: cover;">
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
                    <a href="predicciones.php" class="nav_link" data-toggle="tooltip" data-placement="right" title="Interfaz de Predicciones">
                        <i class="fa-solid fa-calendar nav_logo-icon"></i>
                        <span class="nav_name">Predicciones</span>
                    </a>
                    <a href="umbral.php" class="nav_link active" data-toggle="tooltip" data-placement="right" title="Interfaz de Alarmas">
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
            <h1>Alarmas</h1>
            <br><br><br>
            <div class="form-inline mb-3"> <!-- Añadido mb-3 para margen inferior -->
                <form method="post" action="">
                    <select name="table" class="form-select form-control-lg"> <!-- Añadido form-control-lg para hacer el select más grande -->
                        <option value="">Seleccione una tabla</option>
                        <?php foreach ($tables as $table) : ?>
                            <option value="<?php echo $table; ?>" <?php echo $selected_table == $table ? 'selected' : ''; ?>>
                                <?php echo ucfirst(str_replace('_', ' ', $table)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <br>
                   
                    <button type="submit" class="btn btn-primary btn-lg">Buscar</button> <!-- Añadido btn-lg para hacer el botón más grande -->
                </form>
            </div>
            <?php if ($results && $results->num_rows > 0) : ?>
    <div class="row mt-4">
        <div class="col-12 table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Humedad Mínima</th>
                        <th>Humedad Máxima</th>
                        <th>Temperatura Mínima</th>
                        <th>Temperatura Máxima</th>
                        <?php if ($selected_table == 'umbral_meteorologicos') : ?>
                            <th>Presión Mínima</th>
                            <th>Presión Máxima</th>
                        <?php endif; ?>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $results->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['humedad_min']; ?></td>
                            <td><?php echo $row['humedad_max']; ?></td>
                            <td><?php echo $row['temperatura_min']; ?></td>
                            <td><?php echo $row['temperatura_max']; ?></td>
                            <?php if ($selected_table == 'umbral_meteorologicos') : ?>
                                <td><?php echo $row['presion_min']; ?></td>
                                <td><?php echo $row['presion_max']; ?></td>
                            <?php endif; ?>
                            <td>
                                <button class="btn-delete" data-table="<?php echo $selected_table; ?>" data-id="<?php echo $row['id']; ?>">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php else : ?>
    <div class="alert alert-warning">
    <p class="text-center mt-4">No se encontraron resultados.</p>
    </div>
<?php endif; ?>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.btn-delete').forEach(button => {
                    button.addEventListener('click', function() {
                        const table = this.getAttribute('data-table');
                        const id = this.getAttribute('data-id');

                        Swal.fire({
                            title: '¿Estás seguro?',
                            text: "Esta acción eliminará el registro permanentemente.",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Redirigir a la misma página con parámetros de eliminación
                                window.location.href = `?delete=1&table=${table}&id=${id}`;
                            }
                        });
                    });
                });
            });
        </script>
        <!-- Bootstrap JavaScript Libraries -->
        <script src="https://kit.fontawesome.com/e9f58d382f.js" crossorigin="anonymous"></script>
        <!-- Bootstrap JavaScript Libraries -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <script src="../js/menu.js"></script>
</body>

</html>