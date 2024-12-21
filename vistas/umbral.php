<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION["id_usuario"])) {
    header("location: ../auth/login.php");
    exit();
}

// Incluir el archivo de conexión
require '../modelo/conexion.php';

$id_usuario = isset($_SESSION['id_usuario']) ? intval($_SESSION['id_usuario']) : 0;

// Definir $selected_table si no está definida
$selected_table = isset($_POST['table']) ? $_POST['table'] : '';

// Eliminar registro si se ha solicitado
if (isset($_GET['delete'], $_GET['table'], $_GET['id'])) {
    $table = htmlspecialchars($_GET['table']);
    $id = intval($_GET['id']);

    try {
        $sql_delete = "DELETE FROM $table WHERE id = :id AND id_usuario = :id_usuario";
        $stmt = $pdo->prepare($sql_delete);
        $stmt->execute([':id' => $id, ':id_usuario' => $id_usuario]);
    } catch (PDOException $e) {
        echo "Error al eliminar registro: " . $e->getMessage();
    }
}

// Obtener datos de las tablas
$tables = ['umbral_suelo', 'umbral_ambiente', 'umbral_meteorologicos'];
$results = [];

foreach ($tables as $table) {
    try {
        $sql = "SELECT * FROM $table WHERE id_usuario = :id_usuario";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id_usuario' => $id_usuario]);
        $results[$table] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error al obtener datos de $table: " . $e->getMessage();
    }
}

// Filtrar resultados por fecha y tabla seleccionada
$filtered_results = [];
if (isset($_POST['table'], $_POST['start_date'], $_POST['end_date'])) {
    $selected_table = htmlspecialchars($_POST['table']);
    $start_date = $_POST['start_date'] . ' 00:00:00';
    $end_date = $_POST['end_date'] . ' 23:59:59';

    try {
        $sql_filtered = "SELECT * FROM $selected_table 
                         WHERE id_usuario = :id_usuario 
                         AND fecha BETWEEN :start_date AND :end_date";
        $stmt = $pdo->prepare($sql_filtered);
        $stmt->execute([
            ':id_usuario' => $id_usuario,
            ':start_date' => $start_date,
            ':end_date' => $end_date
        ]);
        $filtered_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error al filtrar resultados: " . $e->getMessage();
    }
}
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
    <link rel="icon" href="../img/logo_proyecto.png" type="image/png">
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


        /* Estilo para el botón de cerrar */
        .notification-close {
            display: inline-block;
            width: 20px;
            height: 20px;
            background-color: red;
            color: white;
            text-align: center;
            line-height: 20px;
            border-radius: 50%;
            font-size: 14px;
            cursor: pointer;
            margin-left: 10px;
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
            width: 110px;
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
            display: inline-block;
            width: 20px;
            height: 20px;
            background-color: red;
            color: white;
            text-align: center;
            line-height: 20px;
            border-radius: 50%;
            font-size: 14px;
            cursor: pointer;
            margin-left: 10px;
            text-decoration: none;
        }

        .btn-delete:hover {
            background-color: darkred;
        }

        .badge {
            font-size: 1.2em;
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
                            <span class="notification-close" data-id="${notification.id}" style="display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; border-radius: 50%; background-color: #dc3545; color: #fff; font-size: 1.2em; cursor: pointer; text-align: center;">
                                &times;
                            </span>
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
                <a href="../index.php" class="nav_logo">
                    <i class="fa-solid fa-desktop nav_logo-icon"></i>
                    <span class="nav_logo-name">SensorWatch</span>
                </a>
                <div class="nav_list">
                    <a href="suelo.php" class="nav_link">
                        <i class="fa-solid fa-seedling nav_logo-icon"></i>
                        <span class="nav_name">Suelo</span>
                    </a>
                    <a href="ambiente.php" class="nav_link">
                        <i class="fa-solid fa-cloud nav_logo-icon"></i>
                        <span class="nav_name">Ambiente</span>
                    </a>
                    <a href="clima.php" class="nav_link">
                        <i class="fa-solid fa-cloud-sun-rain nav_logo-icon"></i>
                        <span class="nav_name">Estación <br>Meteorológica</span>
                    </a>
                    <a href="predicciones.php" class="nav_link">
                        <i class="fa-solid fa-calendar nav_logo-icon"></i>
                        <span class="nav_name">Predicciones</span>
                    </a>
                    <a href="umbral.php" class="nav_link active">
                        <i class="fa-solid fa-triangle-exclamation nav_logo-icon"></i>
                        <span class="nav_name">Alarmas</span>
                    </a>
                </div>
            </div>
            <a href="../controlador/controlador_cerrar_sesion.php" class="nav_link">
                <i class="bx bx-log-out nav_icon"></i>
                <span class="nav_name">Cerrar Sesion</span>
            </a>
        </nav>
    </div>
    <!-- Container Main start -->
    <div class="height-100">
        <div class="container mt-4" style="align-items: center; text-align: center; justify-content: center;">
            <h1>Alarmas</h1>
            <br><br><br>
            <div class="form-inline mb-3">
                <form method="post" action="">
                    <select name="table" class="form-select form-control-lg">
                        <option value="">Seleccione una tabla</option>
                        <?php foreach ($tables as $table) : ?>
                            <option value="<?php echo $table; ?>" <?php echo $selected_table === $table ? 'selected' : ''; ?>>
                                <?php echo ucfirst(str_replace('_', ' ', $table)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <br>
                    <input type="date" name="start_date" class="form-control form-control-lg" value="<?php echo $start_date; ?>">
                    <br>
                    <input type="date" name="end_date" class="form-control form-control-lg" value="<?php echo $end_date; ?>">
                    <br>
                    <button type="submit" class="btn btn-primary btn-lg">Buscar</button>
                </form>
            </div>
            <?php if (!empty($filtered_results)) : ?>
                <div class="row mt-4">
                    <div class="col-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Humedad Mínima</th>
                                    <th>Humedad Máxima</th>
                                    <th>Temperatura Mínima</th>
                                    <th>Temperatura Máxima</th>
                                    <?php if ($selected_table === 'umbral_meteorologicos') : ?>
                                        <th>Presión Mínima</th>
                                        <th>Presión Máxima</th>
                                    <?php endif; ?>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($filtered_results as $row) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['humedad_min']); ?></td>
                                        <td><?php echo htmlspecialchars($row['humedad_max']); ?></td>
                                        <td><?php echo htmlspecialchars($row['temperatura_min']); ?></td>
                                        <td><?php echo htmlspecialchars($row['temperatura_max']); ?></td>
                                        <?php if ($selected_table === 'umbral_meteorologicos') : ?>
                                            <td><?php echo htmlspecialchars($row['presion_min']); ?></td>
                                            <td><?php echo htmlspecialchars($row['presion_max']); ?></td>
                                        <?php endif; ?>
                                        <td>
                                            <button class="btn-delete btn btn-danger" data-table="<?php echo $selected_table; ?>" data-id="<?php echo htmlspecialchars($row['id']); ?>">X</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else : ?>
                <div class="alert alert-warning mt-4" role="alert">
                    No se encontraron resultados para la búsqueda.
                </div>
            <?php endif; ?>
        </div>
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
                            window.location.href = `?delete=1&table=${encodeURIComponent(table)}&id=${encodeURIComponent(id)}`;
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